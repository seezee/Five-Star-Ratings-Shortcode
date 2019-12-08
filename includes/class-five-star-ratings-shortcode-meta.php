<?php
/**
 * Plugin Meta class file.
 *
 * @package Five-Star Review Shortcode/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add links to plugin meta
 */
class Five_Star_Ratings_Shortcode_Meta {

	/**
	 * The single instance of Five_Star_Ratings_Shortcode_Meta.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $instance = null;

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent = null;

	/**
	 * Constructor function.
	 */
	public function __links() {

		// Filter the plugin meta.
		add_filter( 'plugin_row_meta', array( $this, 'meta_links' ), 10, 2 );
	}

	/**
	 * Custom links.
	 *
	 * @param string $links Custom links.
	 * @param string $file Path to main plugin file.
	 */
	public function meta_links( $links, $file ) {
		$plugin = 'five-star-ratings-shortcode.php';
		// Only for this plugin.
		if ( strpos( $file, $plugin ) !== false ) {

			$supportlink = 'https://wordpress.org/support/plugin/five-star-ratings-shortcode';
			$donatelink  = 'https://paypal.me/messengerwebdesign?locale.x=en_US';
			$reviewlink  = 'https://wordpress.org/support/view/plugin-reviews/five-star-ratings-shortcode?rate=5#postform';
			$twitterlink = 'https://twitter.com/czahller';
			$coffeelink  = 'https://www.buymeacoffee.com/chrisjzahller';
			$iconstyle   = 'style="-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;"';

			return array_merge( $links, array(
				'<a href="' . esc_url( $supportlink ) . '"> <span class="dashicons dashicons-format-chat" ' . $iconstyle . 'title="WP FOFT Loader Support" aria-label="WP FOFT Loader Support"></span></a>',
				'<a href="' . esc_url( $twitterlink ). '"><span class="dashicons dashicons-twitter" ' . $iconstyle . 'title="Chris J. Zähller on Twitter" aria-label="Chris J. Zähller on Twitter"></span></a>',
				'<a href="' . esc_url( $reviewlink ). '"><span class="dashicons dashicons-star-filled"' . $iconstyle . 'title="Give a 5-Star Review" aria-label="Give a 5-Star Review"></span></a>',
				'<a href="' . esc_url( $donatelink ). '"><span class="dashicons dashicons-heart"' . $iconstyle . 'title="Donate" aria-label="Donate"></span></a>',
				'<a href="' . esc_url( $coffeelink ). '"><span class="fas fa-coffee"' . $iconstyle . 'title="Buy the Developer a Coffee" aria-label="Buy the Developer a Coffee"></span></a>', ) );
		}

		return $links;
	}

	/**
	 * Main Five_Star_Ratings_Shortcode_Meta Instance
	 *
	 * Ensures only one instance of Five_Star_Ratings_Shortcode_Meta is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Five_Star_Ratings_Shortcode()
	 * @param object $parent Object instance.
	 * @return Main Five_Star_Ratings_Shortcode_Meta instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $parent );
		}
		return self::$instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Five_Star_Ratings_Shortcode_Meta is forbidden.', 'wp-foft-loader' ), esc_attr( _FSRS_VERSION_ ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Five_Star_Ratings_Shortcode_Meta is forbidden.' ), esc_attr( _FSRS_VERSION_ ) );
	} // End __wakeup()

}

$meta = new Five_Star_Ratings_Shortcode_Meta();
$meta -> __links();
