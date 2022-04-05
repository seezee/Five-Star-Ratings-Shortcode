<?php

/**
 * Plugin Name: Five-Star Ratings Shortcode
 * Version: 1.2.41
 * Author URI: https://github.com/seezee
 * Plugin URI: https://wordpress.org/plugins/five-star-ratings-shortcode/
 * GitHub Plugin URI: seezee/five-star-ratings-shortcode
 * Description: Simple lightweight shortcode to add 5-star ratings anywhere
 * Author: Chris J. Zähller / Messenger Web Design
 * Author URI: https://messengerwebdesign.com/
 * Requires at least: 4.6.0
 * Tested up to: 5.9.3
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

if ( function_exists( 'fsrs_fs' ) ) {
    fsrs_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS `IF`: IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'fsrs_fs' ) ) {
        /**
         * Create a helper function for easy SDK access.
         *
         * @since  1.0.0
         */
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
    
    $arr = array(
        'abbr' => array(),
    );
    // Plugin constants.
    $error_open = '<div id="updated" class="notice notice-error is-dismissible"><span class="dashicons dashicons-no"></span> ';
    $error_close = '</div>';
    
    if ( !defined( 'FSRS_BASE' ) ) {
        define( 'FSRS_BASE', 'fsrs_' );
    } else {
        $message = __( 'Five-Star Ratings Shortcode ERROR! The <abbr>PHP</abbr> constant “FSRS_BASE” has already been defined. This could be due to a conflict with another plugin or theme. Please check your logs to debug.', 'fsrs' );
        echo  $error_open . wp_kses( $message, $arr ) . $error_close ;
        // phpcs:ignore
    }
    
    
    if ( !defined( 'FSRS_VERSION' ) ) {
        define( 'FSRS_VERSION', '1.2.41' );
    } else {
        $message = __( 'Five-Star Ratings Shortcode ERROR! The <abbr>PHP</abbr> constant “FSRS_VERSION” has already been defined. This could be due to a conflict with another plugin or theme. Please check your logs to debug.', 'fsrs' );
        echo  $error_open . wp_kses( $message, $arr ) . $error_close ;
        // phpcs:ignore
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
        global  $pagenow ;
        
        if ( FSRS_VERSION !== get_option( FSRS_BASE . 'version' ) || get_option( FSRS_BASE . 'version' ) === false ) {
            // Runs if version mismatch or doesn't exist.
            if ( 'options-general.php' !== $pagenow || !current_user_can( 'install_plugins' ) ) {
                // Show only on settings pages.
                return;
            }
            // Notice for FREE users.
            $html = '<div id="updated" class="notice notice-success is-dismissible">';
            $html .= '<p>';
            $html .= '<span class="dashicons dashicons-yes-alt"></span> ';
            $rel = 'noopener noreferrer';
            // Used in both links.
            $url = '//checkout.freemius.com/mode/dialog/plugin/5125/plan/8260/licenses/1/';
            $html .= sprintf(
                // Translation string with variables.
                wp_kses(
                    /* translators: ignore the placeholders in the URL */
                    __( 'Five-Star Ratings Shortcode updated successfully. For Google Rich Snippets support, customized colors and sizes, variable minimum and maximum ratings, and additional customized output please upgrade to <a href="%1$s" rel="%2$s">Five-Star Ratings Shortcode PRO</a>.', 'fsrs' ),
                    array(
                        'a' => array(
                        'href' => array(),
                        'rel'  => array(),
                    ),
                    )
                ),
                esc_url( $url ),
                $rel
            );
            $url = '//checkout.freemius.com/mode/dialog/plugin/5125/plan/8260/?
            trial=free';
            $html .= ' ' . sprintf( wp_kses(
                /* translators: ignore the placeholders in the URL */
                __( 'Not sure if you need those features? We have a <a href="%1$s" rel="%2$s">FREE 14-day trial</a>.', 'fsrs' ),
                array(
                    'a' => array(
                    'href' => array(),
                    'rel'  => array(),
                ),
                )
            ), esc_url( $url ), $rel );
            $html .= '</p>';
            $html .= '</div>';
            echo  $html ;
            // phpcs:ignore
            update_option( FSRS_BASE . 'version', FSRS_VERSION );
        }
    
    }
    
    add_action( 'plugins_loaded', 'fsrs_check_version' );
    /**
     * Deletes plugin options from the WordPress database. Free users only.
     *
     * Since 1.0.0
     */
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
}
