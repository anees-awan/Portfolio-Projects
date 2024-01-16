<?php
/*
Plugin Name: Custom Quote Listing
Description: Adds a custom post type with taxonomies and additional fields.
Version: 1.0
Author: Anees - 10labz.com
*/

// Enqueue the plugin stylesheet
function enqueue_custom_quote_listing_styles() {
    // Get the path to the plugin's CSS file
    $css_file_path = plugin_dir_url(__FILE__) . 'css/custom-quote-listing-style.css';

    // Enqueue the stylesheet with a unique handle
    wp_enqueue_style('custom-quote-listing-style', $css_file_path, array(), '1.0', 'all');
}

// Hook the function to the 'wp_enqueue_scripts' action
add_action('wp_enqueue_scripts', 'enqueue_custom_quote_listing_styles');


// Register Custom Post Type
function custom_quote_post_type() {
    $labels = array(
        'name'               => 'Quote Listings',
        'singular_name'      => 'Quote Listing',
        'menu_name'          => 'Quote Listings',
        'all_items'          => 'All Quote Listings',
        'add_new_item'       => 'Add New Quote',
        'edit_item'          => 'Edit Quote',
        'new_item'           => 'New Quote',
        'view_item'          => 'View Quote',
        'search_items'       => 'Search Quote',
        'not_found'          => 'No quote listings found',
        'not_found_in_trash' => 'No quote listings found in Trash',
    );

    $args = array(
        'label'               => 'Quote Listing',
        'description'         => 'Custom post type for quote listings',
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'password'),
        'taxonomies'          => array('category'),
        'public'              => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-format-chat',
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'quote_listing'),
    );

    register_post_type('quote_listing', $args);
}
add_action('init', 'custom_quote_post_type');

// Add Custom Fields
function custom_quote_fields() {
    add_meta_box('custom_quote_fields', 'Additional Fields', 'render_custom_quote_fields', 'quote_listing', 'normal', 'high');
}
add_action('add_meta_boxes', 'custom_quote_fields');

function render_custom_quote_fields($post) {
    $video_code = get_post_meta($post->ID, 'video_code', true);
    $access_code = get_post_meta($post->ID, 'access_code', true);
    $selected_template = get_post_meta($post->ID, 'selected_template', true);

    echo '<div class="form-wrap">';
    
    // Quote Code field
    echo '<div class="form-field">';
    echo '<label for="video_code">Video Code:</label>';
    echo '<input type="text" name="video_code" id="video_code" value="' . esc_attr($video_code) . '" /><br>';
    echo '</div>';

    // Access Code field
    echo '<div class="form-field">';
    echo '<label for="access_code">Access Code:</label>';
    echo '<input type="text" name="access_code" id="access_code" value="' . esc_attr($access_code) . '" /><br>';
    echo '</div>';

    // Select Template dropdown
    echo '<div class="form-field">';
    echo '<label for="selected_template">Select Template:</label>';
    echo '<select name="selected_template" id="selected_template" style="width:50%;">';
    echo '<option value="quote_detail_single" ' . selected($selected_template, 'quote_detail_single') . '>Quote Detail Single</option>';
    echo '<option value="quote_detail_full" ' . selected($selected_template, 'quote_detail_full') . '>Quote Detail Full</option>';
    echo '<option value="quote_detail_sidebar" ' . selected($selected_template, 'quote_detail_sidebar') . '>Quote Detail with Sidebar</option>';
    echo '</select>';
    echo '</div>';
    
    echo '</div>';
}


// Add template selection dropdown in post editor
function add_template_dropdown() {

}
add_action('post_edit_form_tag', 'add_template_dropdown');

function save_custom_quote_fields($post_id) {
    if (isset($_POST['video_code'])) {
        update_post_meta($post_id, 'video_code', sanitize_text_field($_POST['video_code']));
    }

    if (isset($_POST['access_code'])) {
        update_post_meta($post_id, 'access_code', sanitize_text_field($_POST['access_code']));
    }

    if (isset($_POST['template'])) {
        update_post_meta($post_id, 'template', sanitize_text_field($_POST['template']));
    }

    if (array_key_exists('selected_template', $_POST)) {
        update_post_meta($post_id, 'selected_template', $_POST['selected_template']);
    }

    // Check if it's the right post type
    if ('video_listing' === get_post_type($post_ID)) {
        // Set the default category ID
        $default_category_id = 1; // Change this to your desired default category ID

        // Get the existing categories for the post
        $post_categories = wp_get_post_categories($post_ID);

        // Check if the post has no categories
        if (empty($post_categories)) {
            // Assign the default category
            wp_set_post_terms($post_ID, array($default_category_id), 'category');
        }
    }

}
add_action('save_post', 'save_custom_quote_fields');

// Customize the template based on the selected value
function custom_quote_listing_template($template) {
    global $post;

    $access_code = get_post_meta($post->ID, 'access_code', true);
    if (is_singular('quote_listing')) {
        if (!isset($_COOKIE['wp-postpass_' . COOKIEHASH])) {

            $custom_template = plugin_dir_path(__FILE__) . 'templates/quote_detail_auth.php';
            return $custom_template;
            exit;
        } else {
        
            $selected_template = get_post_meta($post->ID, 'selected_template', true);

            if (!empty($selected_template)) {
                // Path to your custom template file based on the selected value
                $custom_template = plugin_dir_path(__FILE__) . 'templates/' . $selected_template . '.php';
                //wp_clear_auth_cookie();
                clear_non_admin_auth_cookie();
                return $custom_template;
            }
        }
    }
}
add_filter('single_template', 'custom_quote_listing_template');


function clear_non_admin_auth_cookie() {
    if (!current_user_can('activate_plugins')) {
        // Only clear cookies for non-admin users
        wp_clear_auth_cookie();
    }
}
