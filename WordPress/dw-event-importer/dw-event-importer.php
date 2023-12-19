<?php 
/**
* Plugin Name: DW Event Importer 
* Plugin URI: https://dreamwarrior.com/
* Description: This plugin is used to import events from DEA master calendar 
* Version: 1.0.0
* Author: Anees Awan
* Author URI: https://dreamwarrior.com/
* License: GPL2
*/

// Sanity check.
if (!defined('ABSPATH')) die('Direct access is not allowed.');

// Helper functions.
include('includes/helper-functions.php');

// Constants.
include('includes/constants.php');

// User list table class.
include('includes/user-list.class.php');

/**
 * Install function
 * Create necessary tables and setup default variables.
 */
function dwei_install() {

// Create Tables here     
  
// Vars.
add_option(DWEI_SHORTNAME.'dwei_variable', '0');

}

/**
 * Main init function.
 */
function dwei_init() {
  
  // Start the session if it hasn't been started yet.
  if (!session_id()) {
    session_start();
  }

  // Add necessary scripts.
  wp_enqueue_script('dwei-ajax-request', WP_PLUGIN_URL . '/dw-event-importer/assets/js/front.js', array('jquery'));
  wp_localize_script('dwei-ajax-request', 'dwei', array(
    'URL' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('dwei-post-nonce'),
  ));

  
}

/**
 * Administrator init function.
 */
function dwei_admin_init(){
  
  // Add admin CSS and JS.
  wp_enqueue_style('bootstrap_css', '//stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css');
  wp_enqueue_style('dwei-style-admin', WP_PLUGIN_URL . '/dw-event-importer/assets/css/admin.css');
  wp_enqueue_style('thickbox');
  wp_enqueue_script('dwei-script-admin', WP_PLUGIN_URL . '/dw-event-importer/assets/js/admin.js', array('jquery'));
  //wp_enqueue_script('media-upload');
  
}

/**
 * Admin menu links.
 */
//Admin Dashboard 
include('admin-pages/dw_event_dashboard.php');
include('admin-pages/todays_events.php');
include('admin-pages/upcoming_events.php');
include('admin-pages/archived_events.php');
include('admin-pages/import_events.php');
include('admin-pages/add_update_events.php');
include('admin-pages/events_shortcode.php');


function dwei_admin_menu(){
  
  // Main settings.
    add_menu_page(
        'DW Event Manager', 
        'DW Event Manager',
        DWEI_PERMISSIONS,
        'dw_event_dashboard', 
        'dw_event_dashboard', 
        'dashicons-schedule',
        9
    );
  
  //Submeneu.
  add_submenu_page('dw_event_dashboard', 'Today`s Events', 'Today`s Events', DWEI_PERMISSIONS, 'todays_events', 'todays_events');
  add_submenu_page('dw_event_dashboard', 'Upcoming Events', 'Upcoming Events', DWEI_PERMISSIONS, 'upcoming_events', 'upcoming_events');
  add_submenu_page('dw_event_dashboard', 'Archived Events', 'Archived Events', DWEI_PERMISSIONS, 'archived_events', 'archived_events');
  add_submenu_page('dw_event_dashboard', 'Import Events', 'Import Events', DWEI_PERMISSIONS, 'import_events', 'import_events');
  add_submenu_page('import_events', 'Add/Update Events', 'Add/Update Events', DWEI_PERMISSIONS, 'add_update_events', 'add_update_events');
  add_submenu_page('import_events', 'Edit Events', 'Edit Events', DWEI_PERMISSIONS, 'edit_event', 'edit_event');
  add_submenu_page('import_events', 'Update Events', 'Update Events', DWEI_PERMISSIONS, 'update_event', 'update_event');
  add_submenu_page('dw_event_dashboard', 'Shortcode Manager', 'Shortcode Manager', DWEI_PERMISSIONS, 'events_shortcodes', 'events_shortcodes');


  
}

function dwei_uninstall(){

}

/**
 * Actions.
 */
add_action('init', 'dwei_init');
add_action('admin_init', 'dwei_admin_init');
add_action('admin_menu', 'dwei_admin_menu');


/**
 * Registers.
 */
register_activation_hook(__FILE__, 'dwei_install');
register_deactivation_hook(__FILE__, 'dwei_uninstall');