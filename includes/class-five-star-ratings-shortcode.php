<?php

/**
 * Main plugin class file.
 *
 * @package Five Star Ratings Shortcode/Includes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Main plugin class.
 */
class Five_Star_Ratings_Shortcode
{
    /**
     * The single instance of Five_Star_Ratings_Shortcode.
     *
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static  $instance = null ;
    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $token ;
    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $file ;
    /**
     * The main plugin directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $dir ;
    /**
     * The plugin assets directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $assets_dir ;
    /**
     * The plugin assets URL.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $assets_url ;
    /**
     * Suffix for Javascripts.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $script_suffix ;
    /**
     * Settings class object
     *
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public  $settings = null ;
    /**
     * Constructor function.
     *
     * @param string $file File constructor.
     */
    public function __construct( $file = '' )
    {
        $this->token = 'five-star-ratings-shortcode';
        // Load plugin environment variables.
        $this->file = $file;
        $this->dir = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
        $this->script_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        // Use minified script.
        register_activation_hook( $this->file, array( $this, 'install' ) );
        // Load admin JS & CSS.
        add_action(
            'admin_enqueue_scripts',
            array( $this, 'admin_enqueue_styles' ),
            10,
            1
        );
        add_action(
            'admin_enqueue_scripts',
            array( $this, 'admin_enqueue_scripts' ),
            10,
            1
        );
        add_action(
            'admin_enqueue_scripts',
            array( $this, 'enqueue_jquery' ),
            10,
            1
        );
        add_action(
            'wp_enqueue_scripts',
            array( $this, 'enqueue_fa_scripts' ),
            10,
            1
        );
        add_action(
            'wp_enqueue_scripts',
            array( $this, 'enqueue_fsrs_styles' ),
            10,
            1
        );
        add_filter(
            'script_loader_tag',
            array( $this, 'hash_js' ),
            9,
            2
        );
        // Handle localisation.
        $this->load_plugin_textdomain();
        add_action( 'init', array( $this, 'load_localisation' ), 0 );
        if ( !fsrs_fs()->can_use_premium_code() ) {
            // Display the admin notification.
            add_action( 'admin_notices', array( $this, 'free_activation' ) );
        }
        add_shortcode( 'rating', array( $this, 'rating_func' ) );
    }
    
    // End __construct ()
    /**
     * Displays an activation notice.
     */
    public function free_activation()
    {
        
        if ( !fsrs_fs()->can_use_premium_code() ) {
            // $pagenow is a global variable referring to the filename of the
            // current page, such as ‘admin.php’, ‘post-new.php’.
            global  $pagenow ;
            if ( 'plugins.php' !== $pagenow || !current_user_can( 'install_plugins' ) ) {
                return;
            }
            $html = '<div id="activated" class="notice notice-info is-dismissible">';
            $html .= '<p>';
            $html .= '<span class="dashicons dashicons-info"></span>';
            $rel = 'noopener noreferrer';
            // Used in both links.
            $url = '//checkout.freemius.com/mode/dialog/plugin/5125/plan/8260/licenses/1/';
            $html .= sprintf(
                // Translation string with variables.
                wp_kses(
                    /* translators: ignore the placeholders in the URL */
                    __( 'Thank you for installing Five-Star Ratings Shortcode. For custom icon and text color and size, Google Rich Snippets, and other features, please upgrade to <a href="%1$s" rel="%2$s">Five-Star Ratings Shortcode PRO</a>.', 'fsrs' ),
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
                // translators: placeholders do not need translation.
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
            return $html;
        }
    
    }
    
    // End plugin_activation.
    /**
     * Admin enqueue style.
     *
     * @param string $hook Hook parameter.
     *
     * @return void
     */
    public function admin_enqueue_styles( $hook )
    {
        global  $pagenow ;
        if ( 'settings_page_five-star-ratings-shortcode' !== $hook && 'plugins.php' !== $pagenow || !current_user_can( 'install_plugins' ) ) {
            return;
        }
        wp_register_style(
            $this->token . '-admin',
            esc_url( $this->assets_url ) . 'css/admin' . $this->script_suffix . '.css',
            array(),
            esc_html( FSRS_VERSION )
        );
        wp_enqueue_style( $this->token . '-admin' );
    }
    
    // End admin_enqueue_styles ()
    /**
     * Load admin meta Javascript.
     *
     * @access public
     *
     * @param  string $hook Hook parameter.
     *
     * @return void
     * @since  1.0.0
     */
    public function admin_enqueue_scripts( $hook )
    {
        global  $pagenow ;
        if ( 'settings_page_five-star-ratings-shortcode' !== $hook && 'plugins.php' !== $pagenow || !current_user_can( 'install_plugins' ) ) {
            return;
        }
        $protocol = 'https:';
        $url = '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/js/fontawesome';
        $fallback = esc_url( $this->assets_url ) . 'js/fontawesome';
        $suffix = $this->script_suffix . '.js';
        $link = $protocol . $url . $suffix;
        /**
         * Check whether external files are available.
         *
         * @access public
         *
         * @param string $link Link parameter.
         *
         * @since   1.0.0
         */
        function checklink( $link )
        {
            return (bool) @fopen( $link, 'r' );
            // phpcs:ignore
        }
        
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-form' );
        // If boolean is TRUE.
        
        if ( checklink( $link ) ) {
            wp_register_script(
                $this->token . '-fa-main',
                $url . $this->script_suffix . '.js',
                array(),
                esc_html( FSRS_VERSION ),
                true
            );
            // Otherwise use local copy.
        } else {
            wp_register_script(
                $this->token . '-fa-main',
                $fallback . $this->script_suffix . '.js',
                array(),
                esc_html( FSRS_VERSION ),
                true
            );
        }
        
        wp_enqueue_script( $this->token . '-fa-main' );
        // We're using a specially optimized version of fa-solid.js to
        // load only the necessary Fontawesome glyphs, i.e. fa-star & fa-star-half-alt. In the event we ever need to add more glyphs, both
        // scripts, i.e., fa-solid.js & fa-solid.min.js, will need to be
        // updated.
        wp_register_script(
            $this->token . '-fa-solid',
            esc_url( $this->assets_url ) . 'js/fsrs-fa-solid' . $this->script_suffix . '.js',
            array(),
            esc_html( FSRS_VERSION ),
            true
        );
        wp_enqueue_script( $this->token . '-fa-solid' );
    }
    
    // End admin_enqueue_scripts ().
    /**
     * Load plugin Javascript.
     *
     * @access public
     *
     * @since  1.0.0
     */
    public function enqueue_fa_scripts()
    {
        $protocol = 'https:';
        $url = '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/js/fontawesome';
        $fallback = esc_url( $this->assets_url ) . 'js/fontawesome';
        $suffix = $this->script_suffix . '.js';
        $link = $protocol . $url . $suffix;
        /**
         * Check whether external files are available.
         *
         * @access public
         *
         * @param string $link Link parameter.
         *
         * @since   1.0.0
         */
        function checklink( $link )
        {
            return (bool) @fopen( $link, 'r' );
            // phpcs:ignore
        }
        
        
        if ( !is_admin() ) {
            // If boolean is TRUE.
            
            if ( checklink( $link ) ) {
                wp_register_script(
                    $this->token . '-fa-main',
                    $url . $this->script_suffix . '.js',
                    array(),
                    esc_html( FSRS_VERSION ),
                    true
                );
                // Otherwise use local copy.
            } else {
                wp_register_script(
                    $this->token . '-fa-main',
                    $fallback . $this->script_suffix . '.js',
                    array(),
                    esc_html( FSRS_VERSION ),
                    true
                );
            }
            
            // We're using specially optimized versions of fa-solid.js and
            // fa-regular.js to load only the necessary Fontawesome glyphs,
            // i.e. fa-star (regular and solid) & fa-half-star. In the event
            // we ever need to add more glyphs, both scripts, i.e., fsrs-
            // solid.js, fsrs-fa-regular, fsrs-fa-solid.min.js, & fsrs-fa-
            // regular.min.js will need to be updated.
            wp_register_script(
                $this->token . '-fa-solid',
                esc_url( $this->assets_url ) . 'js/fsrs-fa-solid' . $this->script_suffix . '.js',
                array(),
                esc_html( FSRS_VERSION ),
                true
            );
            wp_register_script(
                $this->token . '-fa-reg',
                esc_url( $this->assets_url ) . 'js/fsrs-fa-regular' . $this->script_suffix . '.js',
                array(),
                esc_html( FSRS_VERSION ),
                true
            );
            wp_enqueue_script( $this->token . '-fa-main' );
            wp_enqueue_script( $this->token . '-fa-solid' );
            wp_enqueue_script( $this->token . '-fa-reg' );
        }
    
    }
    
    // End enqueue_fa_scripts ().
    /**
     * Load jQuery validatioon.
     *
     * @access public
     *
     * @since  1.0.0
     */
    public function enqueue_jquery()
    {
        $protocol = 'https:';
        $url = array( '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods', '//cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard' );
        $fallback = array( esc_url( $this->assets_url ) . 'js/jquery-validate/jquery.validate', esc_url( $this->assets_url ) . 'js/jquery-validate/additional-methods', esc_url( $this->assets_url ) . 'js/clipboard' );
        $suffix = $this->script_suffix . '.js';
        $links = array( $protocol . $url[0] . $suffix, $protocol . $url[1] . $suffix, $protocol . $url[2] . $suffix );
        /**
         * Check whether external files are available.
         *
         * @access public
         *
         * @param string $links Link parameter.
         *
         * @since   1.0.0
         */
        function checklinks0( $links )
        {
            return (bool) @fopen( $links[0], 'r' );
            // phpcs:ignore
        }
        
        /**
         * Check whether external files are available.
         *
         * @access public
         *
         * @param string $links Link parameter.
         *
         * @since   1.0.0
         */
        function checklinks1( $links )
        {
            return (bool) @fopen( $links[1], 'r' );
            // phpcs:ignore
        }
        
        /**
         * Check whether external files are available.
         *
         * @access public
         *
         * @param string $links Link parameter.
         *
         * @since   1.0.0
         */
        function checklinks2( $links )
        {
            return (bool) @fopen( $links[2], 'r' );
            // phpcs:ignore
        }
        
        
        if ( is_admin() ) {
            
            if ( checklinks0( $links ) ) {
                wp_register_script(
                    $this->token . '-validate',
                    $url[0] . $this->script_suffix . '.js',
                    array( 'jquery' ),
                    '1.19.2',
                    true
                );
            } else {
                wp_register_script(
                    $this->token . '-validate',
                    $fallback[0] . $this->script_suffix . '.js',
                    array( 'jquery' ),
                    '1.19.2',
                    true
                );
            }
            
            
            if ( checklinks1( $links ) ) {
                wp_register_script(
                    $this->token . '-methods',
                    $url[1] . $this->script_suffix . '.js',
                    array( 'jquery' ),
                    '1.19.2',
                    true
                );
            } else {
                wp_register_script(
                    $this->token . '-methods',
                    $fallback[1] . $this->script_suffix . '.js',
                    array( 'jquery' ),
                    '1.19.2',
                    true
                );
            }
            
            
            if ( checklinks2( $links ) ) {
                wp_register_script(
                    $this->token . '-clipboard',
                    $url[2] . $this->script_suffix . '.js',
                    array( 'jquery' ),
                    '1.19.2',
                    true
                );
            } else {
                wp_register_script(
                    $this->token . '-clipboard',
                    $fallback[2] . $this->script_suffix . '.js',
                    array( 'jquery' ),
                    '1.19.2',
                    true
                );
            }
            
            wp_enqueue_script( $this->token . '-validate' );
            wp_enqueue_script( $this->token . '-methods' );
            wp_enqueue_script( $this->token . '-clipboard' );
        }
    
    }
    
    // End enqueue_jquery().
    /**
     * Hash external javascripts
     *
     * @param string $tag Script HTML tag.
     * @param string $handle WordPress script handle.
     */
    public function hash_js( $tag, $handle )
    {
        // add script handles to the array below.
        if ( $this->token . '-fa-main' === $handle ) {
            
            if ( SCRIPT_DEBUG ) {
                return str_replace( ' src', ' integrity="sha512-5dPAL40pRRefHhHtDbDGbi6O0EfZof2fwSdjNvpyA1g5Ww3yI/GNVpK8Itc8+5cdKL/78nx6eHKnKmXbmd8ZMQ==" crossorigin="anonymous" src', $tag );
            } else {
                return str_replace( ' src', ' integrity="sha512-pafh0hrrT9ZPZl/jx0cwyp7N2+ozgQf+YK94jSupHHLD2lcEYTLxEju4mW/2sbn4qFEfxJGZyIX/yJiQvgglpw==" crossorigin="anonymous" src', $tag );
            }
        
        }
        if ( $this->token . '-validate' === $handle ) {
            
            if ( SCRIPT_DEBUG ) {
                return str_replace( ' src', ' integrity="sha512-C+jfaS6VNfZywmL6JY4LteCNiaxJpcM54oGjjnAFxqBMXcnMbXYQR/r+zHyB5jTmzyQ4tg3tZq1qVhk28AiIhQ==" crossorigin="anonymous" src', $tag );
            } else {
                return str_replace( ' src', ' integrity="sha512-UdIMMlVx0HEynClOIFSyOrPggomfhBKJE28LKl8yR3ghkgugPnG6iLfRfHwushZl1MOPSY6TsuBDGPK2X4zYKg==" crossorigin="anonymous" src', $tag );
            }
        
        }
        if ( $this->token . '-methods' === $handle ) {
            
            if ( SCRIPT_DEBUG ) {
                return str_replace( ' src', ' integrity="sha512-z1lquNEhC4s9l8MLVGCFb7HyHAw7MhgOXb05rugwZde3lyx7G0sQKE2FnLfmoBJZE8HxbKHBx8jq31UXS+zjRA==" crossorigin="anonymous" src', $tag );
            } else {
                return str_replace( ' src', ' integrity="sha512-6Uv+497AWTmj/6V14BsQioPrm3kgwmK9HYIyWP+vClykX52b0zrDGP7lajZoIY1nNlX4oQuh7zsGjmF7D0VZYA==" crossorigin="anonymous" src', $tag );
            }
        
        }
        if ( $this->token . '-clipboard' === $handle ) {
            
            if ( SCRIPT_DEBUG ) {
                return str_replace( ' src', ' integrity="sha512-tjW2dLIvxBrQWtbL7npJzlMVxznKMrkEJtRX5ztkEP6RC5oJdVkmAfFNHTSNrqv7++hAza+dvV4Bijf8rHeC0Q==" crossorigin="anonymous" src', $tag );
            } else {
                return str_replace( ' src', ' integrity="sha512-hDWGyh+Iy4Mr9AHOzUP2+Y0iVPn/BwxxaoSleEjH/i1o4EVTF/sh0/A1Syii8PWOae+uPr+T/KHwynoebSuAhw==" crossorigin="anonymous" src', $tag );
            }
        
        }
        return $tag;
    }
    
    /**
     * Load plugin styles.
     *
     * @access  public
     *
     * @return  void
     * @since   1.0.0
     */
    public function enqueue_fsrs_styles()
    {
        
        if ( !is_admin() ) {
            wp_register_style(
                $this->token . '-fsrs-style',
                esc_url( $this->assets_url ) . 'css/style' . $this->script_suffix . '.css',
                array(),
                esc_html( FSRS_VERSION ),
                'all'
            );
            wp_enqueue_style( $this->token . '-fsrs-style' );
        }
    
    }
    
    // End enqueue_fsrs_styles().
    /**
     * Load plugin localisation
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation()
    {
        load_plugin_textdomain( 'fsrs', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    }
    
    // End load_localisation ()
    /**
     * Load plugin textdomain
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain()
    {
        $domain = 'fsrs';
        $fallbacke = apply_filters( 'plugin_locale', get_locale(), $domain );
        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $fallbacke . '.mo' );
        load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    }
    
    // End load_plugin_textdomain ()
    /**
     * Shortcode
     *
     * @access  public
     *
     * @param string $atts Shortcode attributes.
     * @since   1.0.0
     */
    public static function rating_func( $atts )
    {
        $atts = shortcode_atts( array(
            'stars' => '',
        ), $atts );
        $arr = array();
        
        if ( get_option( FSRS_BASE . 'syntax' ) != null ) {
            // Don't use strict comparison.
            $syntax = get_option( FSRS_BASE . 'syntax' );
        } else {
            $syntax = 'i';
        }
        
        // Default syntax.
        
        if ( get_option( FSRS_BASE . 'starsmax' ) != null ) {
            // Don't use strict comparison.
            $starsmax = get_option( FSRS_BASE . 'starsmax' );
        } else {
            $starsmax = '5';
        }
        
        // Default value; also the only value for the FREE plugin.
        
        if ( get_option( FSRS_BASE . 'size' ) != null ) {
            // Don't use strict comparison.
            $size = get_option( FSRS_BASE . 'size' );
        } else {
            $size = '';
        }
        
        // Get the value and if it's a float, trim it.
        $star = esc_attr( $atts['stars'] );
        $parts = explode( '.', $star );
        array_pop( $parts );
        $startrim = implode( '.', $parts );
        // Recast string to integer.
        $startrim = (double) $startrim;
        // How many whole stars?
        $stars = str_repeat( '<' . $syntax . ' class="fsrs-fas fa-fw fa-star ' . $size . '"></' . $syntax . '>', $startrim );
        // How many leftover stars if there is no half star?
        $dif = wp_kses( $starsmax, $arr ) - $startrim;
        // Output for the half star.
        $halfstar = '<' . $syntax . ' class="fsrs-fas fa-fw fa-star-half-alt ' . $size . '"></' . $syntax . '>';
        // Empty stars if there is no half star.
        $empty = str_repeat( '<' . $syntax . ' class="fsrs-far fa-fw fa-star ' . $size . '"></' . $syntax . '>', $dif );
        // How many leftover stars if there is a half star?
        
        if ( $dif >= 1 ) {
            $dif2 = $dif - 1;
        } else {
            $dif2 = 0;
        }
        
        // Empty stars if there is a half star.
        $emptyhalf = str_repeat( '<' . $syntax . ' class="fsrs-far fa-fw fa-star ' . $size . '"></' . $syntax . '>', $dif2 );
        
        if ( $startrim == $star ) {
            // There is no half star. Don't use strict type checking because we're dealing with floats and integers.
            $rating = sprintf(
                wp_kses( __(
                /* translators: translate only the phrase "%3$.1F out of %4$.1F stars", where "%3$.1F" and "%4$.1F" are placeholders for numerical floats. */
                '<span class="fsrs">
							<span class="fsrs-stars">%1$s%2$s</span>
							<span class="hide fsrs-text fsrs-text__hidden" aria-hidden="false">%3$.1F out of %4$.1F stars</span>
							<span class="lining fsrs-text fsrs-text__visible" aria-hidden="true">%3$.1F</span>
						</span>',
                'fsrs'
            ), array(
                'span' => array(
                'class'       => array(),
                'aria-hidden' => array(),
            ),
            ) ),
                $stars,
                $empty,
                $star,
                wp_kses( $starsmax, $arr )
            );
            return $rating;
        } elseif ( $star < wp_kses( $starsmax, $arr ) ) {
            // There is a half star.
            $rating = sprintf(
                wp_kses( __(
                /* translators: translate only the phrase "%4$.1F out of %5$.1F stars", where "%4$.1F" and "%5$.1F" are placeholders for numerical floats. */
                '<span class="fsrs">
							<span class="fsrs-stars">%1$s%2$s%3$s</span>
							<span class="hide fsrs-text fsrs-text__hidden" aria-hidden="false">%4$.1F out of %5$.1F stars</span>
							<span class="lining fsrs-text fsrs-text__visible" aria-hidden="true">%4$.1F</span>
						</span>',
                'fsrs'
            ), array(
                'span' => array(
                'class'       => array(),
                'aria-hidden' => array(),
            ),
            ) ),
                $stars,
                $halfstar,
                $emptyhalf,
                $star,
                wp_kses( $starsmax, $arr )
            );
            return $rating;
        } else {
            // There is a half star but the number of stars exceeds the maximum. Don't ouput a half star.
            $rating = sprintf(
                wp_kses( __(
                /* translators: translate only the phrase "%3$.1F out of %4$.1F stars", where "%3$.1F" and "%4$.1F" are placeholders for numerical floats. */
                '<span class="fsrs">
							<span class="fsrs-stars">%1$s%2$s</span>
							<span class="hide fsrs-text fsrs-text__hidden" aria-hidden="false">%3$.1F out of %4$.1F stars</span>
							<span class="lining fsrs-text fsrs-text__visible" aria-hidden="true">%3$.1F</span>
						</span>',
                'fsrs'
            ), array(
                'span' => array(
                'class'       => array(),
                'aria-hidden' => array(),
            ),
            ) ),
                $stars,
                $empty,
                $startrim,
                wp_kses( $starsmax, $arr )
            );
            return $rating;
        }
    
    }
    
    /**
     * Main Five_Star_Ratings_Shortcode Instance
     *
     * Ensures only one instance of Five_Star_Ratings_Shortcode is loaded or can
     * be loaded.
     *
     * @param string $file File instance.
     * @param string FSRS_VERSION Version parameter.
     *
     * @return Object Five_Star_Ratings_Shortcode instance
     * @since 1.0.0
     * @static
     */
    public static function instance( $file = '' )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $file, FSRS_VERSION );
        }
        return self::$instance;
    }
    
    // End instance ()
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Class_Five_Star_Ratings_Shortcode is forbidden.', 'fsrs' ), esc_html( FSRS_VERSION ) );
    }
    
    // End __clone ()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Class_Five_Star_Ratings_Shortcode is forbidden.', 'fsrs' ), esc_html( FSRS_VERSION ) );
    }
    
    // End __wakeup ()
    /**
     * Installation. Runs on activation.
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install()
    {
        $this->logversion_number();
    }
    
    // End install ()
    /**
     * Log the plugin version number.
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function logversion_number()
    {
        update_option( $this->token . 'version', esc_html( FSRS_VERSION ) );
    }

}