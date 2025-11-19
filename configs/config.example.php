<?php

if (!defined('ABSPATH')) exit;

const TEMPLATE_WP_SIDEBAR_ID = 'sidebar-1';

/**
 * Returns the Page ID → Controller mapping
 */
function get_page_controllers()
{
    return [
        29 => 'example.php'
    ];
}
