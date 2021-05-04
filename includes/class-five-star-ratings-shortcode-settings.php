<?php

/**
 * Color picker & semantics for PRO version.
 *
 * @package Five Star Ratings Shortcode/Includes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Settings class.
 */
class Five_Star_Ratings_Shortcode_Settings
{
    /**
     * The single instance of Five_Star_Ratings_Shortcode_Settings.
     *
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static  $instance = null ;
    /**
     * The main plugin object.
     *
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public  $parent = null ;
    /**
     * Available settings for plugin.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public  $settings = array() ;
    /**
     * Constructor function.
     *
     * @access  public
     * @since   1.0.0
     *
     * @param string $parent Parent.
     */
    public function __construct( $parent )
    {
        $this->parent = $parent;
        // Initialise settings.
        add_action( 'init', array( $this, 'init_settings' ), 11 );
        // Register plugin settings.
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        // Add settings page to menu.
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
        // Add settings link to plugins page.
        add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ), array( $this, 'add_settings_link' ) );
    }
    
    /**
     * Initialise settings
     *
     * @return void
     */
    public function init_settings()
    {
        $this->settings = $this->settings_fields();
    }
    
    /**
     * Add settings page to admin menu
     *
     * @return void
     */
    public function add_menu_item()
    {
        $page = add_options_page(
            esc_html__( 'Five Star Ratings Shortcode Documentation', 'fsrs' ),
            esc_html__( 'Five Star Ratings Shortcode Documentation', 'fsrs' ),
            'manage_options',
            $this->parent->token,
            array( $this, 'settings_page' )
        );
        add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
    }
    
    /**
     * Load settings JS & CSS
     *
     * @return void
     */
    public function settings_assets()
    {
        wp_register_script(
            $this->parent->token . '-settings-js',
            $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js',
            array( 'farbtastic', 'jquery' ),
            '1.0.0',
            true
        );
        wp_enqueue_script( $this->parent->token . '-settings-js' );
    }
    
    /**
     * Add settings link to plugin list table
     *
     * @param  array $links Existing links.
     * @return array Modified links
     */
    public function add_settings_link( $links )
    {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->token . '">' . esc_html__( 'Settings', 'fsrs' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }
    
    /**
     * Build settings fields
     *
     * @return array Fields to be displayed on settings page
     */
    private function settings_fields()
    {
        $arr = array(
            'p'      => array(),
            'a'      => array(
            'href'   => array(),
            'rel'    => array(),
            'target' => array(),
        ),
            'em'     => array(),
            'strong' => array(),
            'abbr'   => array(
            'title' => array(),
        ),
            'code'   => array(),
            'pre'    => array(),
            'sup'    => array(),
        );
        $settings['documentation'] = array(
            'title'       => esc_html__( 'Documentation', 'fsrs' ),
            'description' => esc_html__( 'The FREE version of this plugin has no settings. For usage examples, see below.', 'fsrs' ) . '
	<details>
	<summary class="wp-admin-lite-blue">' . esc_html__( 'Shortcode Examples', 'fsrs' ) . '
	</summary>
		<div class="col col-2">
			<div class="col__nobreak">
				<p>' . esc_html__( 'Shortcode syntax:', 'fsrs' ) . ' [rating stars="<em>float</em>"]</p>
				<dl>
					<dt>rating</dt>
					<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Initiates the shortcode.', 'fsrs' ), $arr ) . '</dd>
					<dt>stars</dt>
					<dd><em>(float)</em> ' . wp_kses( __( '<em>(Required)</em> The star rating expressed as a numeric float. Must end in a single decimal place, <abbr>e.g.</abbr>, <code>1.0</code>, <code>3.3</code>, <code>4.5</code>, <code>2.7</code>.', 'fsrs' ), $arr ) . '</dd>
				</dl>
			</div>
			<div class="col__nobreak">
				<p>' . esc_html__( 'The following shortcodes will ouput as shown:', 'fsrs' ) . '</p>
				<ul>
					<li><code>[rating stars="0.5"]</code> (' . esc_html__( 'Displays ½ star out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="3.0"]</code> (' . esc_html__( 'Displays 3 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="4.0"]</code> (' . esc_html__( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="2.5"]</code> (' . esc_html__( 'Displays 2½ stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="5.5"]</code> (' . esc_html__( 'Incorrect usage, but will display 5 stars out of 5', 'fsrs' ) . ')</li>
				</ul>
			</div>
		<div>
			<p>' . wp_kses( __( 'In the 4<sup>th</sup> example, the raw output will be like this before processing:', 'fsrs' ), $arr ) . '</p>
			<pre><code>&lt;span class="fsrs"&gt;
  &lt;span class="fsrs-stars"&gt;
    &lt;i class="fsrs-fas fa-fw fa-star "&gt;&lt;/i&gt;
    &lt;i class="fsrs-fas fa-fw fa-star "&gt;&lt;/i&gt;
    &lt;i class="fsrs-fas fa-fw fa-star-half-alt "&gt;&lt;/i&gt;
    &lt;i class="fsrs-far fa-fw fa-star "&gt;&lt;/i&gt;
    &lt;i class="fsrs-far fa-fw fa-star "&gt;&lt;/i&gt;
  &lt;/span&gt;
  &lt;span class="hide fsrs-text fsrs-text__hidden" aria-hidden="false"&gt;2.5 out of 5&lt;/span&gt; 
  &lt;span class="lining fsrs-text fsrs-text__visible" aria-hidden="true"&gt;2.5&lt;/span&gt;
&lt;/span&gt;</code></pre>
		</div>
	</div>
	</details>
	<details>
	<summary class="wp-admin-lite-blue">' . esc_html__( 'Account Info &amp; Support', 'fsrs' ) . '
	</summary>
		<p>' . esc_html__( 'You can access account details, contact us, get support, or learn about our affiliate program through these links.' ) . '</p>
		<ul>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-affiliation">' . esc_html__( 'Affiliate Program', 'fsrs' ) . '</a></li>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-account">' . esc_html_x( 'Account', 'noun', 'fsrs' ) . '</a></li>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-contact">' . esc_html__( 'Contact Us', 'fsrs' ) . '</a></li>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-wp-support-forum">' . esc_html_x( 'Support Forum', 'adjectival noun', 'fsrs' ) . '</a></li>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-pricing">' . esc_html_x( 'Upgrade', 'imperative verb', 'fsrs' ) . '</a></li>
		</ul>
	</details>
	<details>
	<summary class="wp-admin-lite-blue">' . esc_html__( 'PRO Only Features', 'fsrs' ) . '
	</summary>
		<ul>
			<li>' . esc_html__( 'Google Rich Snippets for Products, Restaurants, and Recipes', 'fsrs' ) . '</li>
			<li>' . esc_html__( 'Shortcode generator', 'fsrs' ) . '</li>
			<li>' . esc_html__( 'Custom icon colors', 'fsrs' ) . '</li>
			<li>' . esc_html__( 'Custom text colors', 'fsrs' ) . '</li>
			<li>' . esc_html__( 'Custom icon and text sizes', 'fsrs' ) . '</li>
			<li>' . esc_html__( 'Change minimum rating (0.0, 0.5, or 1)', 'fsrs' ) . '</li>
			<li>' . esc_html__( 'Change maximum rating (3 &ndash; 10)', 'fsrs' ) . '</li>
			<li>' . wp_kses( __( 'Custom syntax (<code>&lt;i&gt;</code> or <code>&lt;span&gt;</code>)', 'fsrs' ), $arr ) . '</li>
		</ul>
	</details>',
        );
        $settings = apply_filters( $this->parent->token . '_settings_fields', $settings );
        return $settings;
    }
    
    /**
     * Register plugin settings
     *
     * @return void
     */
    public function register_settings()
    {
        if ( is_array( $this->settings ) ) {
            foreach ( $this->settings as $section => $data ) {
                // Add section to page.
                add_settings_section(
                    $section,
                    $data['title'],
                    array( $this, 'settings_section' ),
                    $this->parent->token
                );
            }
        }
    }
    
    /**
     * Output the settings
     *
     * @param string $section The individual settings sections.
     */
    public function settings_section( $section )
    {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
        echo  $html ;
        // phpcs:ignore
    }
    
    /**
     * Load settings page content
     *
     * @return void
     * @param string $hook_suffix Fires in head section for a specific admin page.
     */
    public function settings_page( $hook_suffix )
    {
        global  $pagenow ;
        $arr = array();
        // For wp_kses.
        
        if ( get_option( FSRS_BASE . 'starsmax' ) !== false ) {
            $stars_max = get_option( FSRS_BASE . 'starsmax' );
            $max_int = intval( $stars_max );
            // Same as "$max_int = $max_int - 1".
            $max_int--;
        }
        
        
        if ( get_option( FSRS_BASE . 'starsmin' ) !== false ) {
            $stars_min = get_option( FSRS_BASE . 'starsmin' );
            $min_int = floatval( $stars_min );
            
            if ( 1.0 === $min_int ) {
                $min_int = '1.[0-9]|^([1-';
            } elseif ( 0.0 === $min_int ) {
                $min_int = '0.[0-9]|^([1-';
            } else {
                $min_int = '0.[5-9]|^([1-';
            }
        
        }
        
        // Build page HTML.
        $html = '<div class="wrap" id="' . $this->parent->token . '_settings">' . "\n";
        $html .= '<h2><i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i> ' . esc_html__( 'Five-Star Ratings Shortcode Documentation', 'fsrs' ) . ' <i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i></h2>' . "\n";
        $html .= '
		<form method="post" action="options.php" name="fsrs_settings" id="fsrs_settings" enctype="multipart/form-data">' . "\n";
        // Get settings fields.
        ob_start();
        settings_fields( $this->parent->token );
        do_settings_sections( $this->parent->token );
        $html .= ob_get_clean();
        global  $pagenow ;
        // Run certain logic ONLY if we are on the correct settings page.
        
        if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
            $html .= '<p class="submit">' . "\n";
            $html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
        }
        
        echo  $html ;
        // phpcs:ignore
        
        if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
            submit_button(
                esc_html__( 'Save Settings', 'fsrs' ),
                'primary',
                'save_fsrs_options',
                true
            ) . '<span style="display:inline-block;width:1rem;"></span>';
            $html2 = '</p>' . "\n";
        }
        
        $html2 = '';
        $html2 .= '</form>' . "\n";
        
        if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
            
            if ( 'options-general.php' === $pagenow && isset( $_GET['tab'] ) && 'generator' === $_GET['tab'] ) {
                $html2 .= '
	<form id="fsrs-reset" name="fsrs-reset" method="post" action="options-general.php?page=' . $this->parent->token . '&tab=generator">';
                $html2 .= wp_nonce_field(
                    plugin_basename( __FILE__ ),
                    'fsrs_reset_nonce',
                    true,
                    false
                );
                $html2 .= '
		<p class="submit"><input name="reset" class="button button-secondary" type="submit" value="' . esc_html__( 'Reset the Shortcode Generator', 'fsrs' ) . '" >
		<input type="hidden" name="action" value="reset" />
	  </p>
	</form>';
                if ( isset( $_POST['reset'] ) ) {
                    
                    if ( !isset( $_POST['fsrs_reset_nonce'] ) || !wp_verify_nonce( sanitize_key( $_POST['fsrs_reset_nonce'] ), plugin_basename( __FILE__ ) ) ) {
                        die( esc_html__( 'Invalid nonce. Form submission blocked!', 'fsrs' ) );
                        // Get out of here, the nonce is rotten!
                    } else {
                        $array = [
                            'fsrs_reviewType',
                            'fsrs_reviewRating',
                            'fsrs_reviewName',
                            'fsrs_reviewDesc',
                            'fsrs_prodBrand',
                            'fsrs_prodMPN',
                            'fsrs_prodPrice',
                            'fsrs_prodCur',
                            'fsrs_restRange',
                            'fsrs_restAddr',
                            'fsrs_restCity',
                            'fsrs_restState',
                            'fsrs_restPost',
                            'fsrs_restCountry',
                            'fsrs_restTel',
                            'fsrs_resrecCuisine',
                            'fsrs_recAuthor',
                            'fsrs_recKeywords',
                            'fsrs_recPrep',
                            'fsrs_recCook',
                            'fsrs_recYield',
                            'fsrs_recCat',
                            'fsrs_recCal',
                            'fsrs_recIng',
                            'fsrs_recSteps'
                        ];
                        foreach ( $array as &$item ) {
                            update_option( $item, '' );
                        }
                        echo  "<meta http-equiv='refresh' content='0'>" ;
                    }
                
                }
            }
            
            if ( 'options-general.php' === $pagenow && 'five-star-ratings-shortcode' === $_GET['page'] ) {
                // phpcs:ignore
                $html2 .= '<script>
	jQuery(document).ready(function($) {
      "use strict"; // Prevent accidental global variables.

	  $("#fsrs_settings").validate({
		rules: {
		  reviewType: {
			required: true
		  },
		  reviewRating: {
			required: true
		  },
		},
		messages: {
		  fsrs_reviewType: "' . esc_html__( 'Please select a review type', 'fsrs' ) . '",
		  fsrs_reviewRating: {
				required: "' . esc_html__( 'Please enter a star rating', 'fsrs' ) . '",
				pattern: "' . sprintf( wp_kses(
                    /* translators: /* translators: the placeholders %1$.1f and %2$d.0 are indeterminate numerals. Example output: "Rating must be a 1-decimal place float ranging from 0.0 to 5.0," etc. */
                    __( 'Rating must be a 1-decimal place float ranging from %1$.1f to to %2$d.0, <abbr>e.g.</abbr>, “3.5”, “1.0”. ', 'fsrs' ),
                    array( 'abbr' )
                ), $stars_min, $stars_max ) . '"
			},
		  fsrs_prodBrand: "' . esc_html__( 'Please enter the brand', 'fsrs' ) . '",
		  fsrs_prodMPN: "' . esc_html__( 'Please enter the product number', 'fsrs' ) . '",
		  fsrs_prodPrice: {
				required: "' . esc_html__( 'Please enter the price', 'fsrs' ) . '",
				pattern: "' . esc_html__( 'Price should contain currency symbols, numerals, commas, and periods only; price must end with either zero or 2 decimal places', 'fsrs' ) . '"
			},
		  fsrs_prodCUr: "' . esc_html__( 'Please enter the currency', 'fsrs' ) . '",
		  fsrs_restRange: "' . esc_html__( 'Please enter the price range', 'fsrs' ) . '",
		  fsrs_restAddr: "' . esc_html__( 'Please enter the street address', 'fsrs' ) . '",
		  fsrs_restCity: "' . esc_html__( 'Please enter the hamlet, village, town, borough, prefecture, city, or metropolis', 'fsrs' ) . '",
		  fsrs_restState: "' . esc_html__( 'Please enter the state or province', 'fsrs' ) . '",
		  fsrs_restPost: "' . esc_html__( 'Please enter the postal code', 'fsrs' ) . '",
		  fsrs_restCountry: {
				required: "' . esc_html__( 'Please enter the country', 'fsrs' ) . '",
				pattern: "' . esc_html__( 'Restaurant country must conform to ISO 3166-1 alpha-2 (2-letter) format, e.g., “US”, “UK”, “CN”', 'fsrs' ) . '"
			},
		  fsrs_restTel: "' . esc_html__( 'Please enter the telephone number', 'fsrs' ) . '",
		  fsrs_resrecCuisine: "' . esc_html__( 'Please enter the cuisine', 'fsrs' ) . '",
		  fsrs_recAuthor: "' . esc_html__( 'Please enter the author name', 'fsrs' ) . '",
		  fsrs_recKeywords: "' . esc_html__( 'Please enter at least 1 keyword', 'fsrs' ) . '",
		  fsrs_recPrep: "' . esc_html__( 'Please enter the preparation time', 'fsrs' ) . '",
		  fsrs_recCook: "' . esc_html__( 'Please enter the cooking time', 'fsrs' ) . '",
		  fsrs_recYield: "' . esc_html__( 'Please enter the recipe yield', 'fsrs' ) . '",
		  fsrs_recCat: "' . esc_html__( 'Please enter at least 1 category', 'fsrs' ) . '",
		  fsrs_recCal: "' . esc_html__( 'Please enter the calories', 'fsrs' ) . '",
		  fsrs_recIng: "' . esc_html__( 'Please enter at least 1 ingredient', 'fsrs' ) . '",
		  fsrs_recSteps: {
				required: "' . esc_html__( 'Please enter the recipe steps', 'fsrs' ) . '",
				pattern: "' . sprintf( wp_kses(
                    /* translators: please translate the variables $name and $text into your own language. */
                    __( 'Recipe steps must be in the form {$name} $text, repeating as needed, where $name is a <em>short</em> description of the step and $text is the detailed text of the step.', 'fsrs' ),
                    array( 'em' )
                ), $stars_max ) . '"
			},
		},
		errorElement: "div",
		errorPlacement: function(label, element) {
			label.addClass("validationError");
			label.insertAfter(element);
		},
		wrapper: "span",
		 submitHandler: function(form) {
		   form.submit();
		 }
	  });

	});

	</script>';
            }
        }
        
        $html2 .= '</div>' . "\n";
        echo  $html2 ;
        // phpcs:ignore
    }
    
    /**
     * Main Five_Star_Ratings_Shortcode_Settings Instance
     *
     * Ensures only one instance of Five_Star_Ratings_Shortcode_Settings is loaded or can be loaded.
     *
     * @param string $parent Parent to this file.
     *
     * @since 1.0.0
     * @static
     * @see Five_Star_Ratings_Shortcode()
     * @return Main Five_Star_Ratings_Shortcode_Settings instance
     */
    public static function instance( $parent )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $parent );
        }
        return self::$instance;
    }
    
    // End instance()
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Class_Five_Star_Ratings_Shortcode_Settings is forbidden.' ), $this->parent->_version );
        // phpcs:ignore
    }
    
    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Class_Five_Star_Ratings_Shortcode_Settings is forbidden.' ), $this->parent->_version );
        // phpcs:ignore
    }

}