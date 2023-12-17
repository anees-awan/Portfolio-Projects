<?php 
/**
 * Plugin Name: DW IP Whitelisting 
 * Plugin URI: https://10labz.com/dw-ip-whitelisting 
 * Description: This plugin is used to whitelist IP's for login to the admin panel.  
 * Version: 1.0.0
 * Author: Anees
 * Author URI: https://10labz.com/
 * License: GPL2
 */

// Sanity check.
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

// Helper functions.
require_once(plugin_dir_path(__FILE__) . 'includes/helper-functions.php');

// Constants.
require_once(plugin_dir_path(__FILE__) . 'includes/constants.php');

/**
 * Install function.
 * Create necessary tables and setup default variables.
 */
function dwipwl_install() {
    // Create Tables here.
    create_ip_whitelisting_table();
}
register_activation_hook(__FILE__, 'dwipwl_install');

// Hook the function to check login credentials.
// Function to check login credentials and perform additional actions
function custom_login_check() {
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check if the IP exists in the allowed list in the custom table
    if (is_ip_allowed($ip_address)) {
        return true;
    } else {
        // Redirect user to email input form 
        wp_redirect(home_url('dw-email-verification'));
        exit;
    }
}

add_filter('authenticate', 'custom_login_check', 10, 3);




// Handle email verification form submission.
function handle_email_form_submission() {
    // Check if the email address is submitted.
    if (isset($_POST['login_id'])) {

        $login_id = isset($_POST['login_id']) ? sanitize_text_field($_POST['login_id']) : '';

        // Check if the email address exists in the users table.
        $user = get_user_by('email', $login_id);
        $_SESSION['login_id'] =  $_POST['login_id'];

        //if user
        if(!$user){

            $user = get_user_by('login', $login_id);
        }

        if ($user) {
            // Generate verification token.
            $verification_token = rand(1000, 9999);
            $user_name = $user->user_login;
            $user_email = $user->user_email;
            
            // Save verification token to user meta.
            update_user_meta($user->ID, 'verification_token', $verification_token);

            // Send verification email.
            $subject = 'Admin Authorization code for Xbtv.com';
            $message = "Dear Admin,\n\n"
            . "The verification code below has been provided to whitelist your current IP address to allow entry into the Admin Dashboard of Xbtv.com:\n\n"
            . "$verification_token\n\n"
            . "If you did not request a verification code, please ignore this message. Otherwise, copy the code above and paste it in the verification text field.\n\n"
            . "As always, you can contact us with your questions and concerns via the email contact@xbtv.com";
    
            wp_mail($user_email, $subject, $message);

            // Save IP and email status to the custom table.
            $_SESSION['email'] = $user_email;
            $_SESSION['user_name'] = $user_name;
            $ip_address = $_SERVER['REMOTE_ADDR'];

            save_ip_email_status($ip_address, $user_email, $user_name, $verification_token);
            // Redirect the user to a verification code page.
            wp_redirect(home_url('dw-code-verification'));
            exit;
        } else {
            // Handle the case when the email address doesn't exist in the users table.
            // Redirect the user back to the email verification form with an error message.
            echo $_SESSION['ip_whitelisting_error'] = 'This enail or user name dose not exist in our records.'; 
            wp_redirect(home_url('dw-email-verification'));
            exit;
        }
    }
}
add_action('admin_post_dw_email_form_submission', 'handle_email_form_submission', 1);
add_action('admin_post_nopriv_dw_email_form_submission', 'handle_email_form_submission', 1);

/**
 * Create required frontend pages.
 */
function create_required_frontend_pages() {
    create_email_verification_form();
    create_code_verification_form();
}
register_activation_hook(__FILE__, 'create_required_frontend_pages');


// Register shortcode to display the form
add_shortcode('dw_email_verification_form', 'dw_email_form_shortcode');

// Register shortcode to display the form

add_shortcode('dw_code_verification_form', 'dw_code_verification_form_shortcode');


// Handle code verification form submission.
function handle_dw_code_verification_form_submission() {

    // Check if the email address and verification code are submitted.
    if (isset($_POST['email'], $_POST['verification_code'])) {
        $email = sanitize_email($_POST['email']);
        $verification_code = $_POST['verification_code'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'ip_whitelisting';

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE email = %s AND status = 0",
                $email
            )
        );

        $current_time = current_time('mysql');
        $verification_status = false;
        unset($_SESSION['ip_whitelisting_error']);
        if ($results) {
            foreach ($results as $result) {

                $expiration_time = date('Y-m-d H:i:s', strtotime('-3 minutes', strtotime($current_time)));
                
                if ($result->created_at < $expiration_time) {
                    $_SESSION['ip_whitelisting_error'] = 'Code is expired!';
                    break;
                } else {
                    // Access the fields of each record.
                    $id = $result->id;
                    $v_code = $result->verification_code;
                    //print_r($v_code);
                    //print_r($verification_code);
                    if (trim($v_code) == trim($verification_code)) {
                        
                        $verification_status = true;
                        // Update IP status.
                        update_ip_whitelist_status($id);
                        // Redirect to login page.
                        wp_redirect(wp_login_url() . '?email=' . urlencode($_SESSION['login_id']));
                        exit;
                    }else{
                        $_SESSION['ip_whitelisting_error'] = 'Invalid code, please try again!';
                        wp_redirect(home_url('dw-code-verification'));
                        exit;
                    }
                }
            }
        }else{
            $_SESSION['ip_whitelisting_error'] = 'Unable to verify the code, try again later or contact the site administrator.';
        }
        if (!$verification_status) {
            // Handle the case when the verification code is incorrect.
            // Redirect the user back to the email verification form with an error message.
            
            wp_redirect(home_url('dw-email-verification'));
            exit;
        }
    }
}
add_action('admin_post_dw_code_verification_form', 'handle_dw_code_verification_form_submission', 1);
add_action('admin_post_nopriv_dw_code_verification_form', 'handle_dw_code_verification_form_submission', 1);

/**
 * Main init function.
 */
function dwipwl_init() {
    // Start the session if it hasn't been started yet.
    if (!session_id()) {
        session_start();
    }

    // Add necessary scripts.
    wp_enqueue_script('dwipwl-ajax-request', WP_PLUGIN_URL . '/dw-ip-whitelisting/assets/js/front.js', array('jquery'));
    wp_localize_script('dwipwl-ajax-request', 'dwipwl', array(
        'URL' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dwipwl-post-nonce'),
    ));
}
add_action('init', 'dwipwl_init');

/**
 * Administrator init function.
 */
function dwipwl_admin_init(){
    // Add admin CSS and JS.
    //wp_enqueue_style('bootstrap_css', '//stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css');
    wp_enqueue_style('dwipwl-style-admin', WP_PLUGIN_URL . '/dw-ip-whitelisting/assets/css/admin.css');
    wp_enqueue_style('thickbox');
    wp_enqueue_script('dwipwl-script-admin', WP_PLUGIN_URL . '/dw-ip-whitelisting/assets/js/admin.js', array('jquery'));
}
add_action('admin_init', 'dwipwl_admin_init');

function dwipwl_front_init() {
    // Add frontend CSS
    //wp_enqueue_style('bootstrap_css', '//stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css');
    wp_enqueue_style('dwipwl-style-front', plugin_dir_url(__FILE__) . 'assets/css/front.css');
}
add_action('wp_enqueue_scripts', 'dwipwl_front_init');


// Enqueue the JavaScript file on the login page
function dw_enqueue_login_scripts() {
    // Replace 'your-script.js' with the actual file name of your JavaScript file
    wp_enqueue_script('dw_custom-login-script', WP_PLUGIN_URL . '/dw-ip-whitelisting/assets/js/dw_login.js', array('jquery'), '1.0', true);
}
add_action('login_enqueue_scripts', 'dw_enqueue_login_scripts');


/**
 * Admin menu links.
 */
// Admin Dashboard.
require_once(plugin_dir_path(__FILE__) . 'admin-pages/dw_ip_dashboard.php');

function dwipwl_admin_menu() {
    add_menu_page(
        'DW IP Whitelisting', 
        'DW IP Whitelisting',
        DWIPWL_PERMISSIONS,
        'dw_ip_dashboard', 
        'dw_ip_dashboard', 
        'dashicons-admin-network',
        9
    );

    // Submenu.
    // add_submenu_page('dw_ip_dashboard', 'Whitelisted IP\'s ', 'Whitelisted IP\'s', DWIPWL_PERMISSIONS, 'whitelisted_ips', 'whitelisted_ips');
}
add_action('admin_menu', 'dwipwl_admin_menu');

/**
 * Uninstall function.
 */

register_uninstall_hook(__FILE__, 'dwipwl_uninstall');
// Register the deactivation hook
register_deactivation_hook( __FILE__, 'delete_plugin_pages' );

