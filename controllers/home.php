<?php
$twig = $GLOBALS['twig'];

// Fetch copy and original images
$copyImageField = get_field('copy_image');
$originalImageField = get_field('original_image');

if ($copyImageField) {
    $copyImage = [
        'url' => $copyImageField['url'],
        'alt' => $copyImageField['alt']
    ];
}

if ($originalImageField) {
    $originalImage = [
        'url' => $originalImageField['url'],
        'alt' => $originalImageField['alt']
    ];
}


// Fetch markers map
$mapData = [
    'center' => [48.8566, 2.3522],
    'zoom' => 6,
    'markers' => []
];

// Fetch all posts
$queryPosts = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'post_status' => 'publish',
]);

// Process query all posts
$idPosts = [];
$cities = [];

if ($queryPosts->have_posts()) {
    while ($queryPosts->have_posts()) {
        $queryPosts->the_post();

        // Get markers data
        $mapData['markers'][] = [
            'lat' => get_field('latitude'),
            'lng' => get_field('longitude'),
            'type' => 'primary'
        ];

        // Get number of posts
        $idPosts[] = get_the_ID();

        // Get cities array
        $address = get_field('address');

        if (!empty($address['city_address'])) {

            $city = strtolower(trim($address['city_address']));

            if (!in_array($city, $cities)) {
                $cities[] = $city;
            }
        }
    }
    wp_reset_postdata();
}

// Fetch number of posts
$totalPosts = count($idPosts);

// Fetch number of cities
$totalCities = count($cities);

// Fetch the oldest post date
$queryDate = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 1,
    'meta_key' => 'date_original_image',
    'orderby' => 'meta_value',
    'order' => 'ASC',
]);

$oldestArchive = null;

if ($queryDate->have_posts()) {
    $queryDate->the_post();

    $date = get_field('date_original_image');
    $year = null;

    if ($date) {
        $dt = DateTime::createFromFormat('d/m/Y', $date);
        $year = $dt ? $dt->format('Y') : null;
    }

    $oldestArchive = $year;

    wp_reset_postdata();
}

// Fetch number of users
$queryUsers = new WP_User_Query([
    'role__in' => ['contributor', 'author', 'editor', 'administrator'],
    'fields' => 'ID'
]);

$countUsers = $queryUsers->get_total();

$numbers = [
    [
        'value' => $totalPosts ?? 0,
        'label' => 'Comparaison' . ($totalPosts > 1 ? 's' : '') . ' réalisée' . ($totalPosts > 1 ? 's' : '')
    ],
    [
        'value' => $oldestArchive ?? 'N.C. (Non Connu)',
        'label' => 'Première archive'
    ],
    [
        'value' => $countUsers ?? 0,
        'label' => 'Membre' . ($countUsers > 1 ? 's' : '') . ' contributeur' . ($countUsers > 1 ? 's' : '')
    ],
    [
        'value' => $totalCities ?? 0,
        'label' => 'Ville' . ($totalCities > 1 ? 's' : '') . ' explorée' . ($totalCities > 1 ? 's' : '')
    ]
];


// Set questions for accordion
$faq = [
    [
        'title' => 'Comment comparer une photo ancienne avec une photo récente sur PassAuPrés ?',
        'content' => 'Chaque lieu dispose d’une page dédiée où la photo ancienne et la photo récente sont présentées côte à côte. Un système de comparaison visuelle permet d’observer facilement les différences de cadrage, de paysage et d’environnement entre le passé et le présent.'
    ],
    [
        'title' => 'Les photos présentes sur le site sont-elles vérifiées ou contextualisées historiquement ?',
        'content' => 'Oui. Les contributions sont vérifiées avant publication afin d’assurer leur cohérence avec les images originales. Lorsque des informations historiques sont disponibles, elles sont ajoutées pour contextualiser le lieu, l’époque ou les événements associés.'
    ],
    [
        'title' => 'Puis-je explorer les photos par ville ou directement depuis la carte ?',
        'content' => 'Oui. PassAuPrés propose une navigation par ville ainsi qu’une carte interactive regroupant l’ensemble des lieux photographiés, afin de faciliter l’exploration géographique des comparaisons.'
    ],
    [
        'title' => 'Quelle est l’origine des photos anciennes utilisées sur le site ?',
        'content' => 'Les photos anciennes proviennent de collections publiques, d’archives, de fonds libres de droits ou de collections personnelles partagées par la communauté, lorsque leur usage est autorisé.'
    ],
    [
        'title' => 'Puis-je devenir contributeur même si je ne suis pas photographe professionnel ?',
        'content' => 'Oui. PassAuPrés est ouvert à tous. Un smartphone ou un appareil photo classique suffit, l’essentiel étant de reproduire le point de vue de la photo ancienne de la manière la plus fidèle possible.'
    ]
];

echo $twig->render('pages/home.twig', [
    'original_image' => $originalImage,
    'copy_image' => $copyImage,
    'map_data' => $mapData,
    'numbers' => $numbers,
    'faq' => $faq
]);
