<?php
// Add custom post type for a fossbilling client

function add_client_post_type() {
    $labels = array(
        'name' => 'Clients',
        'singular_name' => 'Client',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Client',
        'edit_item' => 'Edit Client',
        'new_item' => 'New Client',
        'all_items' => 'All Clients',
        'view_item' => 'View Client',
        'search_items' => 'Search Clients',
        'not_found' =>  'No Clients found',
        'not_found_in_trash' => 'No Clients found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Clients'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'client' ),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );

    register_post_type( 'client', $args );
}

add_action( 'init', 'add_client_post_type' );

function handle_form_submit_new_client() {
    if (!isset($_POST['submit_client_form'])) {
        return;
    }
    $client_first_name = sanitize_text_field($_POST['client_first_name']);
    $client_last_name = sanitize_text_field($_POST['client_last_name']);
    $client_company = sanitize_text_field($_POST['client_company']);
    $client_address = sanitize_text_field($_POST['client_address']);
    $client_city = sanitize_text_field($_POST['client_city']);
    $client_state = sanitize_text_field($_POST['client_state']);
    $client_country = sanitize_text_field($_POST['client_country']);
    $client_postal_code = sanitize_text_field($_POST['client_postal_code']);
    $client_phone = sanitize_text_field($_POST['client_phone']);
    $client_currency = sanitize_text_field($_POST['client_currency']);
    $client_email = sanitize_text_field($_POST['client_email']);
    $client_password = sanitize_text_field($_POST['client_password']);

    // Insert a new post of type 'fossbilling_client'
    $client_id = wp_insert_post(array(
        'post_title' => $client_email,
        'post_type' => 'client',
        'post_status' => 'publish',
        'meta_input' => array(
            'client_first_name' => $client_first_name,
            'client_last_name' => $client_last_name,
            'client_company' => $client_company,
            'client_address' => $client_address,
            'client_city' => $client_city,
            'client_state' => $client_state,
            'client_country' => $client_country,
            'client_postal_code' => $client_postal_code,
            'client_phone' => $client_phone,
            'client_currency' => $client_currency,
            'client_email' => $client_email,
            'client_password' => $client_password
        ),
    ));

    if ($client_id) {
        echo '<p>Client added successfully!</p>';
    } else {
        echo '<p>Failed to add client.</p>';
    }
}

add_action('wp', 'handle_form_submit_new_client');
?>
