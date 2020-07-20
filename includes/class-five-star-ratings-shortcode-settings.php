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
        $page = add_options_page(
            __( 'Five Star Ratings Shortcode Documentation', 'fsrs' ),
            __( 'Five Star Ratings Shortcode Documentation', 'fsrs' ),
            'manage_options',
            $this->parent->token,
            array( $this, 'settings_page' )
        );
        add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
    }
    
    /**
     * Load settings JS & CSS
     * @return void
     */
    public function settings_assets()
    {
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
					<dd><em>(' . __( 'integer', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'The quantity of whole stars to display. Must end in a single decimal place (.0 or .5).', 'fsrs' ) . '</dd>
				</dl>
			</div>
			<div class="col__nobreak">
				<p>' . __( 'The following shortcodes will ouput as shown:', 'fsrs' ) . '</p>
				<ul>
					<li><code>[rating stars="0.5"]</code> (' . __( 'Displays', 'fsrs' ) . ' &frac12 ' . __( 'star out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="3.0"]</code> (' . __( 'Displays 3 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="4.0"]</code> (' . __( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="2.5"]</code> (' . __( 'Displays 2', 'fsrs' ) . '&frac12 ' . __( 'stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="5.5"]</code> (' . __( 'Incorrect usage but will display 5 stars out of 5', 'fsrs' ) . ')</li>
				</ul>
			</div>
		<div>
			<p>' . __( 'In the 3<sup>rd</sup> example, the raw output will be like this before processing:', 'fsrs' ) . '</p>
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
	<summary class="wp-admin-lite-blue">' . __( 'Account Info &amp; Support', 'fsrs' ) . '
	</summary>
		<p>' . __( 'You can access account details, contact us, get support, or learn about our affiliate program through these links.' ) . '</p>
		<ul>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-affiliation">' . __( 'Affiliation', 'fsrs' ) . '</a></li>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-account">' . __( 'Account', 'fsrs' ) . '</a></li>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-contact">' . __( 'Contact Us', 'fsrs' ) . '</a></li>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-wp-support-forum">' . __( 'Support Forum', 'fsrs' ) . '</a></li>
			<li><a href="/wp-admin/options-general.php?page=five-star-ratings-shortcode-pricing">Upgrade</a></li>
		</ul>
	</details>
	<details>
	<summary class="wp-admin-lite-blue">' . __( 'PRO Only Features', 'fsrs' ) . '
	</summary>
		<ul>
			<li>' . __( 'Google Rich Snippets for Products, Restaurants, and Recipes', 'fsrs' ) . '</li>
			<li>' . __( 'Custom icon colors', 'fsrs' ) . '</li>
			<li>' . __( 'Custom text colors', 'fsrs' ) . '</li>
			<li>' . __( 'Custom icon and text sizes', 'fsrs' ) . '</li>
			<li>' . __( 'Change minimum rating (0.0, 0.5, or 1)', 'fsrs' ) . '</li>
			<li>' . __( 'Change maximum rating', 'fsrs' ) . ' (3 &ndash; 10)</li>
			<li>' . __( 'Custom syntax', 'fsrs' ) . ' (<code>&lt;i&gt;</code> ' . __( 'or', 'fsrs' ) . ' <code>&lt;span&gt;</code>)</li>
		</ul>
	</details>',
        );
        $settings = apply_filters( $this->parent->token . '_settings_fields', $settings );
        return $settings;
    }
    
    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings()
    {
        if ( is_array( $this->settings ) ) {
            foreach ( $this->settings as $section => $data ) {
                // Add section to page
                add_settings_section(
                    $section,
                    $data['title'],
                    array( $this, 'settings_section' ),
                    $this->parent->token . '_settings'
                );
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
        $html .= '<h2><i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i> ' . __( 'Five-Star Ratings Shortcode Documentation', 'fsrs' ) . ' <i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i></h2>' . "\n";
        $html .= '<form method="post" action="options.php" name="fsrsSettings" id="fsrsSettings" enctype="multipart/form-data">' . "\n";
        // Get settings fields
        ob_start();
        settings_fields( $this->parent->token . '_settings' );
        do_settings_sections( $this->parent->token . '_settings' );
        $html .= ob_get_clean();
        global  $pagenow ;
        echo  $html ;
        $html2 = '';
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