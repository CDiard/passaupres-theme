<?php
$twig = $GLOBALS['twig'];

$copy_image_id = get_post_meta(get_the_ID(), 'id_copy_image', true);
$original_image_id = get_post_meta(get_the_ID(), 'id_original_image', true);

if ($copy_image_id) {
    $copy_image_url = wp_get_attachment_url($copy_image_id);
    $copy_image_alt = get_post_meta($copy_image_id, '_wp_attachment_image_alt', true);
}

if ($original_image_id) {
    $original_image_url = wp_get_attachment_url($original_image_id);
    $original_image_alt = get_post_meta($original_image_id, '_wp_attachment_image_alt', true);
}

$map_data = [
    'center' => [48.8566, 2.3522],
    'zoom' => 6,
    'markers' => [
        [
            'lat' => 48.8566,
            'lng' => 2.3522,
            'type' => 'primary',
            'popup' => 'Paris',
        ],
        [
            'lat' => 48.87701464436167,
            'lng' => 2.3833233378022967,
            'type' => 'primary',
            'popup' => 'Paris 19e',
        ],
        [
            'lat' => 49.28995209465912,
            'lng' => 4.156594738558486,
            'type' => 'secondary',
            'popup' => 'Reims',
        ],
        [
            'lat' => 48.14525638181272,
            'lng' => -1.5871050850243384,
            'type' => 'secondary',
            'popup' => 'Rennes',
        ],
        [
            'lat' => 47.278342418679586,
            'lng' => -1.2860554215214992,
            'type' => 'secondary',
            'popup' => 'Nantes',
        ],
        [
            'lat' => 48.39978236776683,
            'lng' => -4.418408739722805,
            'type' => 'secondary',
            'popup' => 'Brest',
        ],
        [
            'lat' => 50.950503433892344,
            'lng' => 1.982947461008075,
            'type' => 'secondary',
            'popup' => 'Calais',
        ],
        [
            'lat' => 48.58343917141125,
            'lng' => 7.479166074226487,
            'type' => 'secondary',
            'popup' => 'Strasbourg',
        ]
    ]
];

$numbers = [
    [
        'value' => 53,
        'label' => 'Comparaisons réalisées'
    ],
    [
        'value' => 1850,
        'label' => 'Première archive'
    ],
    [
        'value' => 5,
        'label' => 'Membres contributeurs'
    ],
    [
        'value' => 24,
        'label' => 'Villes explorées'
    ]
];

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
    'original_image' => [
        'url' => $original_image_url,
        'label' => $original_image_alt
    ],
    'copy_image' => [
        'url' => $copy_image_url,
        'label' => $copy_image_alt
    ],
    'map_data' => $map_data,
    'numbers' => $numbers,
    'faq' => $faq
]);
