<?php
$twig = $GLOBALS['twig'];

// Get query params
$query = [
    'search' => get_search_query() ?: null,
    'startYear' => $_GET['start_year'] ?? null,
    'endYear' => $_GET['end_year'] ?? null,
    'city' => $_GET['city'] ?? null,
    'country' => $_GET['country'] ?? null,
    'author' => $_GET['author'] ?? null,
];

// Config map
$mapData = [
    'center' => [48.8566, 2.3522],
    'zoom' => 6,
    'interactive' => true,
    'markers' => []
];

// Query SQL

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$posts_per_page = get_option('posts_per_page');
$offset = ($paged - 1) * $posts_per_page;

global $wpdb;

$where = "p.post_type = 'post' AND p.post_status = 'publish'";

$score = "0"; // default score if no search

// Where query search
if (!empty($query['search'])) {
    $search_esc = esc_sql($wpdb->esc_like($query['search']));

    $where .= $wpdb->prepare(" AND (
        p.post_title LIKE %s
        OR p.post_excerpt LIKE %s
        OR pm1.meta_value LIKE %s
        OR pm2.meta_value LIKE %s
    )", "%{$search_esc}%", "%{$search_esc}%", "%{$search_esc}%", "%{$search_esc}%");

    // Simple scoring: title = 4 points, excerpt = 2 points, meta = 1 point each
    $score = $wpdb->prepare("
        (CASE WHEN p.post_title LIKE %s THEN 4 ELSE 0 END) +
        (CASE WHEN p.post_excerpt LIKE %s THEN 2 ELSE 0 END) +
        (CASE WHEN pm1.meta_value LIKE %s THEN 1 ELSE 0 END) +
        (CASE WHEN pm2.meta_value LIKE %s THEN 1 ELSE 0 END)
    ", "%{$search_esc}%", "%{$search_esc}%", "%{$search_esc}%", "%{$search_esc}%");
}

// Where query year range
if (!empty($query['startYear']) && !empty($query['endYear'])) {
    $startYear = !empty($query['startYear']) ? intval($query['startYear']) : 0;
    $endYear = !empty($query['endYear']) ? intval($query['endYear']) : 9999;

    $where .= $wpdb->prepare(" AND ( 
        ( YEAR(STR_TO_DATE(pm_start_year.meta_value, '%%Y%%m%%d')) BETWEEN %d AND %d ) 
        AND ( YEAR(STR_TO_DATE(pm_end_year.meta_value, '%%Y%%m%%d')) BETWEEN %d AND %d ) 
    )", $startYear, $endYear, $startYear, $endYear);
}

// Where query city
if (!empty($query['city'])) {
    $city_like = '%' . $wpdb->esc_like($query['city']) . '%';

    $where .= $wpdb->prepare(" AND pm_city.meta_value LIKE %s", $city_like);
}

// Where query country
if (!empty($query['country'])) {
    $country_like = '%' . $wpdb->esc_like($query['country']) . '%';

    $where .= $wpdb->prepare(" AND pm_country.meta_value LIKE %s", $country_like);
}

// Where query author
if (!empty($query['author'])) {
    $author_like = '%' . $wpdb->esc_like($query['author']) . '%';

    $where .= $wpdb->prepare(" AND ( 
        ( pm_author1.meta_value LIKE %s ) OR ( pm_author2.meta_value LIKE %s ) 
    )", $author_like, $author_like);
}

// SQL query with scoring and sorting by relevance
$sql = "
    SELECT DISTINCT p.ID, $score AS relevance
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm1 ON (p.ID = pm1.post_id AND pm1.meta_key = 'autor_original_image')
    LEFT JOIN {$wpdb->postmeta} pm2 ON (p.ID = pm2.post_id AND pm2.meta_key = 'autor_copy_image')
    LEFT JOIN {$wpdb->postmeta} pm_start_year ON (p.ID = pm_start_year.post_id AND pm_start_year.meta_key = 'date_original_image')
    LEFT JOIN {$wpdb->postmeta} pm_end_year ON (p.ID = pm_end_year.post_id AND pm_end_year.meta_key = 'date_copy_image')
    LEFT JOIN {$wpdb->postmeta} pm_city ON (p.ID = pm_city.post_id AND pm_city.meta_key = 'address_city_address')
    LEFT JOIN {$wpdb->postmeta} pm_country ON (p.ID = pm_country.post_id AND pm_country.meta_key = 'address_country_address')
    LEFT JOIN {$wpdb->postmeta} pm_author1 ON (p.ID = pm_author1.post_id AND pm_author1.meta_key = 'autor_original_image')
    LEFT JOIN {$wpdb->postmeta} pm_author2 ON (p.ID = pm_author2.post_id AND pm_author2.meta_key = 'autor_copy_image')
    WHERE $where
    ORDER BY relevance DESC, p.post_date DESC
";

if (!empty(array_filter($query))) {
    $sql .= " LIMIT %d OFFSET %d";
    $prepared_sql = $wpdb->prepare($sql, $posts_per_page, $offset);
} else {
    $prepared_sql = $sql;
}

$post_ids = $wpdb->get_col($prepared_sql);

if ($post_ids) {
    foreach ($post_ids as $post_id) {
        $originalImage = get_field('original_image', $post_id);
        $copyImage = get_field('copy_image', $post_id);
        $dateOriginalImage = get_field('date_original_image', $post_id);
        $dateCopyImage = get_field('date_copy_image', $post_id);

        $yearOriginalImage = null;
        if ($dateOriginalImage) {
            $dt = DateTime::createFromFormat('d/m/Y', $dateOriginalImage);
            $yearOriginalImage = $dt ? $dt->format('Y') : null;
        }

        $yearCopyImage = null;
        if ($dateCopyImage) {
            $dt = DateTime::createFromFormat('d/m/Y', $dateCopyImage);
            $yearCopyImage = $dt ? $dt->format('Y') : null;
        }

        $context = [
            'title' => get_the_title($post_id),
            'excerpt' => get_the_excerpt($post_id),
            'originalUrl' => esc_url($originalImage['url']),
            'originalAlt' => esc_attr($originalImage['alt']),
            'copyUrl' => esc_url($copyImage['url']),
            'copyAlt' => esc_attr($copyImage['alt']),
            'date' => $yearOriginalImage . ' - ' . $yearCopyImage,
            'author' => get_field('autor_copy_image', $post_id),
            'link' => get_permalink($post_id)
        ];

        $contentPopup = $twig->render('partials/popup.twig', $context);

        // Get markers data
        $mapData['markers'][] = [
            'lat' => get_field('latitude', $post_id),
            'lng' => get_field('longitude', $post_id),
            'type' => 'primary',
            'popup' => $contentPopup,
        ];
    }
}

// Filters

// Fetch all posts
$queryPosts = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => -1,
    'post_status' => 'publish',
]);

// Process query all posts
$cities = [];
$countries = [];
$authors = [];

if ($queryPosts->have_posts()) {
    while ($queryPosts->have_posts()) {
        $queryPosts->the_post();

        $address = get_field('address');

        // Get cities array
        if (!empty($address['city_address'])) {
            $city = strtolower(trim($address['city_address']));

            if (!in_array($city, $cities)) {
                $cities[] = $city;
            }
        }

        // Get countries array
        if (!empty($address['country_address'])) {
            $country = strtolower(trim($address['country_address']));

            if (!in_array($country, $countries)) {
                $countries[] = $country;
            }
        }

        // Get author array
        if (!empty(get_field('autor_original_image'))) {
            $author = trim(get_field('autor_original_image'));

            if (!in_array($author, $authors)) {
                $authors[] = $author;
            }
        }

        if (!empty(get_field('autor_copy_image'))) {
            $author = trim(get_field('autor_copy_image'));

            if (!in_array($author, $authors)) {
                $authors[] = $author;
            }
        }
    }
    wp_reset_postdata();
}

$filters = [
    'cities' => $cities,
    'countries' => $countries,
    'authors' => $authors,
];

echo $twig->render('pages/list.twig', [
    'is_home' => is_home(),
    'is_front_page' => is_front_page(),
    'map_data' => $mapData,
    'query' => $query,
    'filters' => $filters
]);