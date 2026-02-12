<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package PassAuPres_Theme
 */

$page_id = get_the_ID();

$controllers = get_page_controllers();

if (array_key_exists($page_id, $controllers)) {
    include_once 'controllers/' . $controllers[$page_id];
    return;
}

include_once 'controllers/page.php';
