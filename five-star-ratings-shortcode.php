<?php

/**
 * Plugin Name: Five-Star Ratings Shortcode
 * Version: 1.0.18
 * Author URI: https://github.com/seezee
 * Plugin URI: https://wordpress.org/plugins/five-star-ratings-shortcode/
 * GitHub Plugin URI: seezee/five-star-ratings-shortcode  
 * Description: Simple lightweight shortcode to add 5-star ratings anywhere  
 * Author: Chris J. Zähller / Messenger Web Design
 * Author URI: https://messengerwebdesign.com/
 * Requires at least: 4.0
 * Tested up to: 5.3
 * PHP Version 7.0
 * Text Domain: fsrs
 * Domain Path: /lang/
 *
 *
 * @package WordPress
 * @author  Chris J. Zähller <chris@messengerwebdesign.com>
 * @since   1.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !function_exists( 'fsrs_fs' ) ) {
    // Create a helper function for easy SDK access.
    function fsrs_fs()
    {
        global  $fsrs_fs ;
        
        if ( !isset( $fsrs_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $fsrs_fs = fs_dynamic_init( array(
                'id'              => '5125',
                'slug'            => 'five-star-ratings-shortcode',
                'premium_slug'    => 'five-star-ratings-shortcode-pro',
                'type'            => 'plugin',
                'public_key'      => 'pk_9847875a95be002fa7fedc9eb5bc9',
                'is_premium'      => false,
                'premium_suffix'  => 'PRO',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'trial'           => array(
                'days'               => 14,
                'is_require_payment' => false,
            ),
                'has_affiliation' => 'all',
                'menu'            => array(
                'slug'        => 'five-star-ratings-shortcode',
                'contact'     => false,
                'support'     => false,
                'affiliation' => false,
                'parent'      => array(
                'slug' => 'options-general.php',
            ),
            ),
                'is_live'         => true,
            ) );
        }
        
        return $fsrs_fs;
    }
    
    // Init Freemius.
    fsrs_fs();
    // Signal that SDK was initiated.
    do_action( 'fsrs_fs_loaded' );
}

// Plugin constants.

if ( !defined( 'FSRS_BASE' ) ) {
    define( 'FSRS_BASE', 'fsrs_' );
} else {
    echo  '<div id="updated" class="notice notice-error is-dismissible"><span class="dashicons dashicons-no"></span> ' . __( 'Five-Star Ratings Shortcode ERROR! The <abbr>PHP</abbr> constant', 'fsrs' ) . ' &ldquo;FSRS_BASE&rdquo; ' . __( 'has already been defined. This could be due to a conflict with another plugin or theme. Please check your logs to debug.', 'fsrs' ) . '</div>' ;
}


if ( !defined( 'FSRS_VERSION' ) ) {
    define( 'FSRS_VERSION', '1.0.18' );
} else {
    echo  '<div id="updated" class="notice notice-error is-dismissible"><span class="dashicons dashicons-no"></span> ' . __( 'Five-Star Ratings Shortcode ERROR! The <abbr>PHP</abbr> constant', 'fsrs' ) . ' &ldquo;FSRS_VERSION&rdquo; ' . __( 'has already been defined. This could be due to a conflict with another plugin or theme. Please check your logs to debug.', 'fsrs' ) . '</div>' ;
}

// Load plugin class files.
require_once 'includes/class-five-star-ratings-shortcode.php';
require_once 'includes/class-five-star-ratings-shortcode-meta.php';
require_once 'includes/class-five-star-ratings-shortcode-settings.php';
/**
 * Returns the main instance of five_star_ratings_shortcode Settings to prevent the need to use
 * globals.
 *
 * @since  1.0.0
 * @return object five_star_ratings_shortcode
 */
function five_star_ratings_shortcode()
{
    $instance = five_star_ratings_shortcode::instance( __FILE__, FSRS_VERSION );
    if ( is_null( $instance->settings ) ) {
        $instance->settings = Five_Star_Ratings_Shortcode_Settings::instance( $instance );
    }
    return $instance;
}

five_star_ratings_shortcode();
/**
 * Checks the version number in the DB. If they don't match we just upgraded, * so show a notice and update the DB.
 *
 * @since  1.0.0
 */
function fsrs_check_version()
{
    
    if ( FSRS_VERSION !== get_option( FSRS_BASE . 'version' ) || get_option( FSRS_BASE . 'version' ) == FALSE ) {
        // Runs if version mismatch or doesn't exist.
        // $pagenow is a global variable referring to the filename of the
        // current page, such as ‘admin.php’, ‘post-new.php’.
        global  $pagenow ;
        if ( $pagenow != 'options-general.php' || !current_user_can( 'install_plugins' ) ) {
            // Show only on settings pages.
            return;
        }
        // Notice for FREE users.
        $html = '<div id="updated" class="notice notice-success is-dismissible">';
        $html .= '<p>';
        $html .= '<span class="dashicons dashicons-yes-alt"></span> ' . __( 'Five-Star Ratings Shortcode updated successfully. For Google Rich Snippets support, custom colors and sizes, or to change the minimum and maximum ratings, please upgrade to', 'fsrs' ) . ' <a href="' . esc_url( '//checkout.freemius.com/mode/dialog/plugin/5125/plan/8260/licenses/1/' ) . '" rel="noopener noreferrer">Five-Star Ratings Shortcode PRO</a>. ' . __( 'Not sure if you need those features? We have a', 'fsrs' ) . ' <a href="' . esc_url( '//checkout.freemius.com/mode/dialog/plugin/5125/plan/8260/?trial=free" rel="noopener noreferrer' ) . '">' . __( 'FREE 14-day trial.', 'fsrs' ) . '</a>';
        $html .= '</p>';
        $html .= '</div>';
        echo  $html ;
        update_option( FSRS_BASE . 'version', FSRS_VERSION );
    }

}

add_action( 'plugins_loaded', 'fsrs_check_version' );
function fsrs_fs_uninstall_cleanup()
{
    foreach ( wp_load_alloptions() as $option => $value ) {
        if ( strpos( $option, FSRS_BASE ) === 0 ) {
            delete_option( $option );
        }
    }
}

// Uninstall hook.
fsrs_fs()->add_action( 'after_uninstall', 'fsrs_fs_uninstall_cleanup' );