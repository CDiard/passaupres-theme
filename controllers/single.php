<?php
$twig = $GLOBALS['twig'];

function format_date($date) {

    $dateObj = DateTime::createFromFormat('d/m/Y', $date);

    if (!$dateObj) {
        return null;
    }

    return $dateObj->format('Y-m-d H:i:s');
}

$post_data = [];

if (have_posts()) {
    while (have_posts()) {
        the_post();

        $originalImage = get_field('original_image');
        $copyImage = get_field('copy_image');
        $address = get_field('address');

        if ($originalImage['width'] > $originalImage['height']) {
            $typeImages = 'landscape';
        } else {
            $typeImages = 'portrait';
        }

        $post_data = [
            'title' => get_the_title(),
            'content' => apply_filters('the_content', get_the_content()),
            'date' => get_the_date(),
            'image_original' => [
                'url' => esc_url($originalImage['url']),
                'alt' => esc_attr($originalImage['alt']),
                'type' => $typeImages
            ],
            'image_copy' => [
                'url' => esc_url($copyImage['url']),
                'alt' => esc_attr($copyImage['alt']),
                'type' => $typeImages
            ],
            'latitude' => get_field('latitude'),
            'longitude' => get_field('longitude'),
            'address' => [
                'numero' => !empty($address['numero_address']) ? $address['numero_address'] : null,
                'street' => $address['street_address'],
                'zip' => $address['zip_address'],
                'city' => $address['city_address'],
                'country' => $address['country_address']
            ],
            'author_original' => get_field('autor_original_image'),
            'author_copy' => get_field('autor_copy_image'),
            'date_original' => format_date(get_field('date_original_image')),
            'date_copy' => format_date(get_field('date_copy_image')),
            'source' => get_field('source')
        ];
    }
}

// Map init
$mapData = [
    'center' => [get_field('latitude'), get_field('longitude')],
    'zoom' => 18,
    'interactive' => false,
    'preview' => true,
    'markers' => [[
        'lat' => get_field('latitude'),
        'lng' => get_field('longitude'),
        'type' => 'primary'
    ]]
];

// Nearest posts
function get_nearest_posts($post_id, $limit = 4)
{
    $lat = get_field('latitude', $post_id);
    $lng = get_field('longitude', $post_id);

    if (!$lat || !$lng) {
        return [];
    }

    global $wpdb;

    $query = $wpdb->prepare("
        SELECT p.ID, 
        (
            6371 * acos(
                cos(radians(%f)) *
                cos(radians(lat.meta_value)) *
                cos(radians(lng.meta_value) - radians(%f)) +
                sin(radians(%f)) *
                sin(radians(lat.meta_value))
            )
        ) AS distance

        FROM {$wpdb->posts} p

        INNER JOIN {$wpdb->postmeta} lat
            ON p.ID = lat.post_id
            AND lat.meta_key = 'latitude'

        INNER JOIN {$wpdb->postmeta} lng
            ON p.ID = lng.post_id
            AND lng.meta_key = 'longitude'

        WHERE
            p.post_status = 'publish'
            AND p.post_type = 'post'
            AND p.ID != %d

        ORDER BY distance ASC
        LIMIT %d

    ", $lat, $lng, $lat, $post_id, $limit);

    $results = $wpdb->get_results($query);

    $posts = [];

    foreach ($results as $row) {

        $posts[] = [
            'id' => $row->ID,
            'title' => get_the_title($row->ID),
            'image' => [
                'url' => esc_url(get_field('original_image', $row->ID)['url']),
                'alt' => esc_attr(get_field('original_image', $row->ID)['alt'])
            ],
            'link' => get_permalink($row->ID),
            'distance' => round($row->distance, 2)
        ];
    }

    return $posts;
}

$near_posts = get_nearest_posts(get_the_ID(), 4);

echo $twig->render('pages/single.twig', [
    'post' => $post_data,
    'map_data' => $mapData,
    'near_posts' => $near_posts
]);