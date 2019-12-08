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
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static  $_instance = null ;
    /**
     * The main plugin object.
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public  $parent = null ;
    /**
     * Available settings for plugin.
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public  $settings = array() ;
    public function __construct( $parent )
    {
        $this->parent = $parent;
        // Initialise settings
        add_action( 'init', array( $this, 'init_settings' ), 11 );
        // Register plugin settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        // Add settings page to menu
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
        // Add settings link to plugins page
        add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ), array( $this, 'add_settings_link' ) );
    }
    
    /**
     * Initialise settings
     * @return void
     */
    public function init_settings()
    {
        $this->settings = $this->settings_fields();
    }
    
    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item()
    {
        
        if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
            $page = add_options_page(
                __( 'Five Star Ratings Shortcode Settings', 'fsrs' ),
                __( 'Five Star Ratings Shortcode Settings', 'fsrs' ),
                'manage_options',
                $this->parent->token,
                array( $this, 'settings_page' )
            );
        } else {
            $page = add_options_page(
                __( 'Five Star Ratings Shortcode Documentation', 'fsrs' ),
                __( 'Five Star Ratings Shortcode Documentation', 'fsrs' ),
                'manage_options',
                $this->parent->token,
                array( $this, 'settings_page' )
            );
        }
        
        add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
    }
    
    /**
     * Load settings JS & CSS
     * @return void
     */
    public function settings_assets()
    {
        
        if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
            // We're including the farbtastic script & styles here because they're needed for the colour picker
            // If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
            wp_enqueue_style( 'farbtastic' );
            wp_enqueue_script( 'farbtastic' );
        }
        
        wp_register_script(
            $this->parent->token . '-settings-js',
            $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js',
            array( 'farbtastic', 'jquery' ),
            '1.0.0'
        );
        wp_enqueue_script( $this->parent->token . '-settings-js' );
    }
    
    /**
     * Add settings link to plugin list table
     * @param  array $links Existing links
     * @return array        Modified links
     */
    public function add_settings_link( $links )
    {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->token . '">' . __( 'Settings', 'fsrs' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }
    
    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    private function settings_fields()
    {
        
        if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
            $settings['options'] = array(
                'title'       => __( 'Options', 'fsrs' ),
                'description' => __( 'Syntax and formatting options. All options are global,', 'fsrs' ) . ' <abbr>i.e.</abbr>, ' . __( 'they affect all star ratings on the site.', 'fsrs' ) . '
	<details>
	  <summary class="wp-admin-lite-blue">' . __( 'Shortcode Examples', 'fsrs' ) . '
	  </summary>
	  <div class="col col-2">
		<div class="col__nobreak">
		  <p>' . __( 'Shortcode syntax:', 'fsrs' ) . ' [rating stars=<em>int</em> half=<em>string|int|bool</em>]</p>
		  <dl>
			<dt>rating</dt>
			<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Initiates the shortcode.', 'fsrs' ) . '</dd>
			<dt>stars</dt>
			<dd><em>(' . __( 'integer', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'The quantity of whole stars to display.', 'fsrs' ) . '</dd>
			<dt>half</dt>
			<dd><em>(' . __( 'string|integer|boolean', 'fsrs' ) . ')</em> <em>(' . __( 'Optional', 'fsrs' ) . ')</em> ' . __( 'Whether to append a half-star.', 'fsrs' ) . ' <code>true</code>,  <code>yes</code>, ' . __( 'and', 'fsrs' ) . ' <code>1</code> ' . __( 'all resolve to', 'fsrs' ) . ' TRUE.  <code>false</code>,  <code>no</code>, ' . __( 'and', 'fsrs' ) . ' <code>0</code> ' . __( 'all resolve to', 'fsrs' ) . ' FALSE. ' . __( 'Defaults to', 'fsrs' ) . ' FALSE.</dd>
		  </dl>
		</div>
		<div class="col__nobreak">
			<p>' . __( 'Assuming the default setting with', 'fsrs' ) . ' &ldquo;' . __( 'Number of Stars', 'fsrs' ) . '&rdquo; ' . __( 'set to 5, the following shortcodes will ouput as shown:', 'fsrs' ) . '<ul>
				<li><code>[rating stars=3]</code> (' . __( 'Displays 3 stars out of 5', 'fsrs' ) . ')</li>
				<li><code>[rating stars=4 half=false]</code> (' . __( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
				<li><code>[rating stars=4 half=no]</code> (' . __( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
				<li><code>[rating stars=4 half=0]</code> (' . __( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
				<li><code>[rating stars=2 half=true]</code> (' . __( 'Displays 2', 'fsrs' ) . '&frac12 ' . __( 'stars out of 5', 'fsrs' ) . ')</li>
				<li><code>[rating stars=2 half=yes]</code> (' . __( 'Displays 2', 'fsrs' ) . '&frac12 ' . __( 'stars out of 5', 'fsrs' ) . ')</li>
				<li><code>[rating stars=2 half=1]</code> (' . __( 'Displays 2', 'fsrs' ) . '&frac12 ' . __( 'stars out of 5', 'fsrs' ) . ')</li>
				<li><code>[rating stars=5 half=true]</code> (' . __( 'Incorrect usage but will display 5 stars out of 5', 'fsrs' ) . ')</li>
			</ul>
		</div>
	  </div>
	</details>',
                'fields'      => array(
                array(
                'id'          => 'syntax',
                'label'       => __( 'Syntax', 'fsrs' ),
                'description' => __( 'Choose <code>&lt;i&gt;</code> output for brevity or', 'fsrs' ) . ' <code>&lt;span&gt</code> ' . __( 'output for semantics. Default is', 'fsrs' ) . ' <code>&lt;i&gt;</code>',
                'type'        => 'radio',
                'options'     => array(
                'i'    => '&lt;i&gt;',
                'span' => '&lt;span&gt;',
            ),
                'default'     => '&lt;i&gt;',
            ),
                array(
                'id'          => 'starsnum',
                'label'       => __( 'Number of Stars', 'fsrs' ),
                'description' => __( 'Change the maximum number of stars. Default is 5.', 'fsrs' ),
                'type'        => 'range',
                'min'         => '3',
                'max'         => '10',
                'default'     => '5',
            ),
                array(
                'id'          => 'starcolor',
                'label'       => __( 'Star Color', 'fsrs' ),
                'description' => __( 'Change the star icon color. By default, the icons inherit their color.', 'fsrs' ),
                'type'        => 'color',
                'default'     => '',
            ),
                array(
                'id'          => 'textcolor',
                'label'       => __( 'Text Color', 'fsrs' ),
                'description' => __( 'Change the numeric text color.   By default, the text inherits its color.', 'fsrs' ),
                'type'        => 'color',
                'default'     => '',
            ),
                array(
                'id'          => 'size',
                'label'       => __( 'Star Size', 'fsrs' ),
                'description' => __( 'Change the star icon and text rating size. By default, the icons and text inherit their color.', 'fsrs' ),
                'type'        => 'select',
                'options'     => array(
                ''       => __( 'Default', 'fsrs' ),
                'fa-xs'  => __( 'Extra Small', 'fsrs' ),
                'fa-sm'  => __( 'Small', 'fsrs' ),
                'fa-lg'  => __( 'Large', 'fsrs' ),
                'fa-2x'  => '2&times;',
                'fa-3x'  => '3&times;',
                'fa-4x'  => '4&times;',
                'fa-5x'  => '5&times;',
                'fa-6x'  => '6&times;',
                'fa-7x'  => '7&times;',
                'fa-8x'  => '8&times;',
                'fa-9x'  => '9&times;',
                'fa-10x' => '10&times;',
            ),
                ''            => 'Default',
            )
            ),
            );
            $settings = apply_filters( $this->parent->token . '_settings_fields', $settings );
            return $settings;
        } else {
            $settings['documentation'] = array(
                'title'       => __( 'Documentation', 'fsrs' ),
                'description' => __( 'The FREE version of this plugin has no settings. For usage examples, see below.', 'fsrs' ) . '
	<details>
	<summary class="wp-admin-lite-blue">' . __( 'Shortcode Examples', 'fsrs' ) . '
	</summary>
		<div class="col col-2">
			<div class="col__nobreak">
				<p>' . __( 'Shortcode syntax:', 'fsrs' ) . ' [rating stars=<em>int</em> half=<em>string|int|bool</em>]</p>
				<dl>
					<dt>rating</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Initiates the shortcode.', 'fsrs' ) . '</dd>
					<dt>stars</dt>
					<dd><em>(' . __( 'integer', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'The quantity of whole stars to display.', 'fsrs' ) . '</dd>
					<dt>half</dt>
					<dd><em>(' . __( 'string|integer|boolean', 'fsrs' ) . ')</em> <em>(' . __( 'Optional', 'fsrs' ) . ')</em> ' . __( 'Whether to append a half-star.', 'fsrs' ) . ' <code>true</code>,  <code>yes</code>, ' . __( 'and', 'fsrs' ) . ' <code>1</code> ' . __( 'all resolve to', 'fsrs' ) . ' TRUE.  <code>false</code>,  <code>no</code>, ' . __( 'and', 'fsrs' ) . ' <code>0</code> ' . __( 'all resolve to', 'fsrs' ) . ' FALSE. ' . __( 'Defaults to', 'fsrs' ) . ' FALSE.</dd>
				</dl>
			</div>
			<div class="col__nobreak">
				<p>' . __( 'The following shortcodes will ouput as shown:', 'fsrs' ) . '<ul>
					<li><code>[rating stars=3]</code> (' . __( 'Displays 3 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars=4 half=false]</code> (' . __( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars=4 half=no]</code> (' . __( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars=4 half=0]</code> (' . __( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars=2 half=true]</code> (' . __( 'Displays 2', 'fsrs' ) . '&frac12 ' . __( 'stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars=2 half=yes]</code> (' . __( 'Displays 2', 'fsrs' ) . '&frac12 ' . __( 'stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars=2 half=1]</code> (' . __( 'Displays 2', 'fsrs' ) . '&frac12 ' . __( 'stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars=5 half=true]</code> (' . __( 'Incorrect usage but will display 5 stars out of 5', 'fsrs' ) . ')</li>
				</ul>
			</div>
		</div>
	</details>
	<details>
	<summary class="wp-admin-lite-blue">' . __( 'PRO Only Features', 'fsrs' ) . '
	</summary>
		<ul>
			<li>' . __( 'Custom icon colors', 'fsrs' ) . '</li>
			<li>' . __( 'Custom text colors', 'fsrs' ) . '</li>
			<li>' . __( 'Custom icon and text sizes', 'fsrs' ) . '</li>
			<li>' . __( 'Change star display quantity from', 'fsrs' ) . ' 3 &ndash; 10</li>
			<li>' . __( 'Custom syntax', 'fsrs' ) . ' (<code>&lt;i&gt;</code> ' . __( 'or', 'fsrs' ) . ' <code>&lt;span&gt;</code>)</li>
		</ul>
	</details>',
            );
            $settings = apply_filters( $this->parent->token . '_settings_fields', $settings );
            return $settings;
        }
    
    }
    
    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings()
    {
        
        if ( is_array( $this->settings ) ) {
            
            if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
                // Check posted/selected tab
                $current_section = '';
                
                if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
                    $current_section = $_POST['tab'];
                } else {
                    if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                        $current_section = $_GET['tab'];
                    }
                }
            
            }
            
            foreach ( $this->settings as $section => $data ) {
                if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
                    if ( $current_section && $current_section != $section ) {
                        continue;
                    }
                }
                // Add section to page
                add_settings_section(
                    $section,
                    $data['title'],
                    array( $this, 'settings_section' ),
                    $this->parent->token . '_settings'
                );
                if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
                    foreach ( $data['fields'] as $field ) {
                        // Validation callback for field
                        $validation = '';
                        if ( isset( $field['callback'] ) ) {
                            $validation = $field['callback'];
                        }
                        // Register field
                        $option_name = _FSRS_BASE_ . $field['id'];
                        register_setting( $this->parent->token . '_settings', $option_name, $validation );
                        if ( !fsrs_fs()->can_use_premium_code() ) {
                            // Add field to page
                            add_settings_field(
                                $field['id'],
                                $field['label'],
                                array( $this->parent->admin, 'display_field' ),
                                $this->parent->token . '_settings',
                                $section,
                                array(
                                'field'  => $field,
                                'prefix' => _FSRS_BASE_,
                            )
                            );
                        }
                    }
                }
                if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
                    if ( !$current_section ) {
                        break;
                    }
                }
            }
        }
    
    }
    
    public function settings_section( $section )
    {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
        echo  $html ;
    }
    
    /**
     * Load settings page content
     * @return void
     */
    public function settings_page()
    {
        // Build page HTML
        $html = '<div class="wrap" id="' . $this->parent->token . '_settings">' . "\n";
        
        if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
            $html .= '<h2>' . __( 'Five-Star Ratings Shortcode Settings', 'fsrs' ) . '</h2>' . "\n";
        } else {
            $html .= '<h2>' . __( 'Five-Star Ratings Shortcode Documentation', 'fsrs' ) . '</h2>' . "\n";
        }
        
        
        if ( fsrs_fs()->is__premium_only() && fsrs_fs()->can_use_premium_code() ) {
            $tab = '';
            if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                $tab .= $_GET['tab'];
            }
            // Show page tabs
            
            if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {
                $html .= '<h2 class="nav-tab-wrapper">' . "\n";
                $c = 0;
                foreach ( $this->settings as $section => $data ) {
                    // Set tab class
                    $class = 'nav-tab';
                    
                    if ( !isset( $_GET['tab'] ) ) {
                        if ( 0 == $c ) {
                            $class .= ' nav-tab-active';
                        }
                    } else {
                        if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
                            $class .= ' nav-tab-active';
                        }
                    }
                    
                    // Set tab link
                    $tab_link = add_query_arg( array(
                        'tab' => $section,
                    ) );
                    if ( isset( $_GET['settings-updated'] ) ) {
                        $tab_link = remove_query_arg( 'settings-updated', $tab_link );
                    }
                    // Output tab
                    $html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";
                    ++$c;
                }
                $html .= '</h2>' . "\n";
            }
        
        }
        
        $html .= '<form method="post" action="options.php" name="fsrsSettings" id="fsrsSettings" enctype="multipart/form-data">' . "\n";
        // Get settings fields
        ob_start();
        settings_fields( $this->parent->token . '_settings' );
        do_settings_sections( $this->parent->token . '_settings' );
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
                __( 'Save Settings', 'fsrs' ),
                'primary',
                'save_fsrs_options',
                false
            );
            $html2 = '</p>' . "\n";
        } else {
            $html2 = '';
        }
        
        $html2 .= '</form>' . "\n";
        $success1 = __( 'Yeehaw!', 'fsrs' );
        $success2 = __( 'Good Job!', 'fsrs' );
        $success3 = __( 'Hooray!', 'fsrs' );
        $success4 = __( 'Yay!', 'fsrs' );
        $success5 = __( 'Huzzah!', 'fsrs' );
        $success6 = __( 'Bada bing bada boom!', 'fsrs' );
        $message1 = array(
            $success1,
            $success2,
            $success3,
            $success4,
            $success5,
            $success6
        );
        $message1 = $message1[array_rand( $message1 )];
        $error1 = __( 'Dangit!', 'fsrs' );
        $error2 = __( 'Aw heck!', 'fsrs' );
        $error3 = __( 'Egads!', 'fsrs' );
        $error4 = __( 'D&rsquo;oh!', 'fsrs' );
        $error5 = __( 'Drat!', 'fsrs' );
        $error6 = __( 'Dagnabit!', 'fsrs' );
        $message2 = array(
            $error1,
            $error2,
            $error3,
            $error4,
            $error5,
            $error6
        );
        $message2 = $message2[array_rand( $message2 )];
        if ( 'plugins.php' === $pagenow && 'five-star-ratings-shortcode' === $_GET['page'] ) {
            // Ajaxify the form. Timeout should be >= 5000 or you'll get errors.
            $html2 .= '<div id="saveResult"></div>
	<script type="text/javascript">
	jQuery(document).ready(function() {
	   jQuery("#fsrsSettings").submit(function() { 
		  jQuery(this).ajaxSubmit({
			 success: function(){
				jQuery("#saveResult").html(`<div id="saveMessage" class="notice notice-success is-dismissible"></div>`);
				jQuery("#saveMessage").append(`<p><span class="dashicons dashicons-yes-alt"></span> ' . $message1 . ' ' . __( 'Your settings were saved!', 'fsrs' ) . '</p>`).show();
			 },
			 error: function(){
				jQuery("#saveResult").html(`<div id="saveMessage" class="notice notice-error is-dismissible"></div>`);
				jQuery("#saveMessage").append(`<p><span class="dashicons dashicons-no"></span> ' . $message2 . ' ' . __( 'There was an error saving your settings. Please open a support ticket if the problem persists!', 'fsrs' ) . '</p>`).show();
			 },
			 timeout: 10000
		  }); 
		  setTimeout(`jQuery("#saveMessage").hide("slow");`, 7500);
		  return false; 
	   });
	});
	</script>';
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
     * @since 1.0.0
     * @static
     * @see Five_Star_Ratings_Shortcode()
     * @return Main Five_Star_Ratings_Shortcode_Settings instance
     */
    public static function instance( $parent )
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $parent );
        }
        return self::$_instance;
    }
    
    // End instance()
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
    }
    
    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
    }

}