<?php
$twig = $GLOBALS['twig'];

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
            'terms' => 'contributeur',
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

// Fetch contributor users
$queryUsers = new WP_User_Query([
    'role__in' => ['contributor'],
    'fields' => ['ID', 'display_name'],
    'number' => -1,
    'order' => 'ASC',
    'orderby' => 'display_name'
]);

$contributorUsers = $queryUsers->get_results();

echo $twig->render('pages/contributor.twig', [
    'faq' => $faq,
    'contributors' => $contributorUsers,
    'form' => '[forminator_form id="'.TEMPLATE_WP_ID_FORM.'"]'
]);
