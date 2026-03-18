<?php
function custom_roles_caps() {
    // Admininistrator
    $administrator = get_role('administrator');

    if (!$administrator) return;

    $caps = [
        'edit_faqs',
        'read_faqs',
        'delete_faqs',
        'edit_faqs',
        'edit_others_faqs',
        'publish_faqs',
        'read_private_faqs',
        'delete_faqs',
        'delete_private_faqs',
        'delete_published_faqs',
        'delete_others_faqs',
        'edit_private_faqs',
        'edit_published_faqs'
    ];

    foreach ($caps as $cap) {
        $administrator->add_cap($cap);
    }

    // Contributor
    $contributor = get_role('contributor');

    if ($contributor) {
        $contributor->add_cap('wf2fa_activate_2fa_self');
    }
}

add_action('init', 'custom_roles_caps');