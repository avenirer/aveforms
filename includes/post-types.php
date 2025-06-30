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
        // show only in admin area, not in the front-end
        'public'            => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
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

function aveform_post_type_help()
{
    global $post, $pagenow;

    // Here we check if we are on the post-new.php or post.php pages
    // This is where we want to add our help notice
    if ( ! in_array(
         $pagenow
        ,array(
             'post-new.php'
            ,'post.php'
         )
    ) ) {
        return;
	}

    // We only want to show this notice for our custom post type
    // If we are not on the correct post type, we return
    if ( ! in_array(
         $post->post_type
        ,array(
             'aveform'
         )
    ) )
        return;

    // You can use the global $post here
    echo '<div class="notice notice-info is-dismissible">
    <h2>This is an example of using the form:</h2>
    <p>To create a form, you can use the following shortcode:</p>
    <pre>
[aveform form_id="theform"]
  [aveform_input type="text" name="first_name" label="First Name" placeholder="First Name" rules="required{The field is required}|string{The value must be a string}|max:255{Maximum number of characters reached}|min:3{Minimum number of characters not met}"]
  [aveform_input type="text" name="last_name" label="Last Name" placeholder="Last Name" rules="required|string|max:255|min:3"]
  [aveform_input type="email" name="email" label="Email" placeholder="email@email.com" rules="required|email"]
  [aveform_textarea name="message" cols="5" rows="10" label="Message" placeholder="Type your message here" rules="required|min:10|max:500"]
  [aveform_submit text="Send" class="button button-primary"]
[/aveform]
</pre>
<p>As you can see, besides having the validation rules, you can put custom messages between accolades.</p></div>';
}
add_action( 'admin_notices', 'aveform_post_type_help' );


// Add a custom column to the Aveform post type in the admin area
// This will allow us to display the shortcode for each form in the list of forms
add_filter( 'manage_aveform_posts_columns', 'set_custom_edit_aveform_columns' );
function set_custom_edit_aveform_columns($columns) {
    $columns['shortcode'] = 'Shortcode';
    return $columns;
}

// Add the content for the custom column
// This will display the shortcode for each form in the list of forms
add_action( 'manage_aveform_posts_custom_column' , 'custom_aveform_column', 10, 2 );
function custom_aveform_column( $column, $post_id ) {
    switch ( $column ) {

        case 'shortcode' :
            echo '[aveformshow id="' . $post_id . '"]';
            echo '<br><small>Copy this shortcode to use the form.</small>';
            break;

    }
}