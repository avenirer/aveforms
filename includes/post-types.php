<?php

// The custom function to register a custom article post type
function aveforms_form_post_type() {
    // Set the labels. This variable is used in the $args array
    $labels = array(
        'name'               => __( 'Aveforms' ),
        'singular_name'      => __( 'Aveform' ),
        'add_new'            => __( 'Add New Form' ),
        'add_new_item'       => __( 'Add New Form' ),
        'edit_item'          => __( 'Edit Form' ),
        'new_item'           => __( 'New Form' ),
        'all_items'          => __( 'All Forms' ),
        'view_item'          => __( 'View Form' ),
        'search_items'       => __( 'Search Form' ),
    );
    // Set the arguments for the custom post type
    // This variable is used in the register_post_type function
    $args = array(
        'labels'            => $labels,
        'description'       => 'Aveforms custom post type for managing contact forms',
        'public'            => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position'     => 30,
        'supports'          => array(
            'title',
            'editor',
            'revisions',
            'custom-fields',
            'author',
            'page-attributes',
        ),
        'has_archive'       => false,
        'show_in_admin_bar' => true,
        'query_var'         => true,
        'menu_icon'         => 'dashicons-feedback',
    );
    register_post_type('aveform', $args);
}

add_action( 'init', 'aveforms_form_post_type' );