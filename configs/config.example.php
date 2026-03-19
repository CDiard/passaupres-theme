<?php

if (!defined('ABSPATH')) exit;

const TEMPLATE_WP_SIDEBAR_ID = 'sidebar-1';
const TEMPLATE_WP_LENGTH_LISTS = 16;

const TEMPLATE_WP_ID_HOME = 16;
const TEMPLATE_WP_ID_CONTRIBUTOR = 20;
const TEMPLATE_WP_ID_SEARCH = 18;
const TEMPLATE_WP_ID_FORM = 172;

/**
 * Returns the Page ID → Controller mapping
 */
function get_page_controllers()
{
    return [
        29 => 'example.php'
    ];
}
