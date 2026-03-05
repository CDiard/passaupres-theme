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
    'post_type' => 'post',
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


// Fetch questions for accordion
$queryFaqs = new WP_Query([
    'post_type' => 'faq',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'order' => 'ASC',
    'orderby' => 'date',
    'tax_query' => [
        [
            'taxonomy' => 'type_faq',
            'field' => 'slug',
            'terms' => 'accueil',
        ]
    ]
]);

$faq = [];

if ($queryFaqs->have_posts()) {
    while ($queryFaqs->have_posts()) {
        $queryFaqs->the_post();

        $faq[] = [
            'title' => get_the_title(),
            'content' => get_field('response'),
        ];
    }
    wp_reset_postdata();
}

echo $twig->render('pages/home.twig', [
    'original_image' => $originalImage,
    'copy_image' => $copyImage,
    'map_data' => $mapData,
    'numbers' => $numbers,
    'faq' => $faq
]);
