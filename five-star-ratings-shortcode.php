<?php

/**
 * Plugin Name: Five-Star Ratings Shortcode
 * Version: 1.0.0
 * Author URI: https://github.com/seezee
 * Plugin URI: https://wordpress.org/plugins/five-star-ratings-shortcode/
 * GitHub Plugin URI: seezee/five-star-ratings-shortcode  
 * Description: Simple lightweight shortcode to add 5-star ratings anywhere  
 * Author: Chris J. Zähller / Messenger Web Design
 * Author URI: https://messengerwebdesign.com/
 * Requires at least: 4.0
 * Tested up to: 5.3
 * PHP Version 7.0
 * Text Domain: wp-foft-loader
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
                'id'             => '5125',
                'slug'           => 'five-star-ratings-shortcode',
                'type'           => 'plugin',
                'public_key'     => 'pk_9847875a95be002fa7fedc9eb5bc9',
                'is_premium'     => false,
                'premium_suffix' => 'Five-Star Ratings Shorcode PRO',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 14,
                'is_require_payment' => false,
            ),
                'menu'           => array(
                'slug'   => 'five-star-ratings-shortcode',
                'parent' => array(
                'slug' => 'options-general.php',
            ),
            ),
                'is_live'        => true,
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
const  _FSRS_BASE_ = 'fsrs_' ;
const  _FSRS_VERSION_ = '1.0.0' ;
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
    $instance = five_star_ratings_shortcode::instance( __FILE__, _FSRS_VERSION_ );
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
    
    if ( _VERSION_ !== get_option( _FSRS_BASE_ . 'version' ) || get_option( _FSRS_BASE_ . 'version' ) == FALSE ) {
        // Runs if version mismatch or doesn't exist.
        // $pagenow is a global variable referring to the filename of the
        // current page, such as ‘admin.php’, ‘post-new.php’.
        global  $pagenow ;
        if ( $pagenow != 'options-general.php' || !current_user_can( 'install_plugins' ) ) {
            // Show only on settings pages.
            return;
        }
        
        if ( fsrs_fs()->is_not_paying() ) {
            // Notice for FREE users.
            $html = '<div id="updated" class="notice notice-success is-dismissible">';
            $html .= '<p>';
            $html .= '<span class="dashicons dashicons-yes-alt"></span> ' . __( 'Five-Star Ratings Shortcode updated successfully. For custom colors and sizes or to change the total quantity of stars displayed, please upgrade to', 'fsrs' ) . ' <a href="' . esc_url( '//checkout.freemius.com/mode/dialog/plugin/5125/plan/8260/licenses/1/' ) . '" rel="noopener noreferrer">Five-Star Ratings Shortcode PRO</a>. ' . __( 'Not sure if you need those features? We have a', 'fsrs' ) . ' <a href="' . esc_url( '//checkout.freemius.com/mode/dialog/plugin/5125/plan/8260/?trial=free" rel="noopener noreferrer' ) . '">' . __( 'FREE 14-day trial.', 'fsrs' ) . '</a>';
            $html .= '</p>';
            $html .= '</div>';
            echo  $html ;
        }
        
        update_option( _FSRS_BASE_ . 'version', _VERSION_ );
    }

}

add_action( 'plugins_loaded', 'fsrs_check_version' );
function fsrs_fs_uninstall_cleanup()
{
    foreach ( wp_load_alloptions() as $option => $value ) {
        if ( strpos( $option, _FSRS_BASE_ ) === 0 ) {
            delete_option( $option );
        }
    }
}

// Uninstall hook.
fsrs_fs()->add_action( 'after_uninstall', 'fsrs_fs_uninstall_cleanup' );