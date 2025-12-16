<?php
/**
 * Styles, semantics, presentation, & rich snippets for PRO version.
 *
 * @package Five Star Ratings Shortcode/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 */
class Five_Star_Ratings_Shortcode_Settings {

	/**
	 * The single instance of Five_Star_Ratings_Shortcode_Settings.
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
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   1.0.0
	 *
	 * @param string $parent Parent.
	 */
	public function __construct( $parent ) {
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
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {

		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {
				$page = add_options_page( esc_html__( 'Five Star Ratings Shortcode Settings', 'fsrs' ), esc_html__( 'Five Star Ratings Shortcode Settings', 'fsrs' ), 'manage_options', $this->parent->token, array( $this, 'settings_page' ) );
			} else {
				$page = add_options_page( esc_html__( 'Five Star Ratings Shortcode Documentation', 'fsrs' ), esc_html__( 'Five Star Ratings Shortcode Documentation', 'fsrs' ), 'manage_options', $this->parent->token, array( $this, 'settings_page' ) );
			}
		}

		if ( ( ! fsrs_fs()->is__premium_only() ) || ( ( fsrs_fs()->is__premium_only() ) && ( ! fsrs_fs()->can_use_premium_code() ) ) ) {
			$page = add_options_page( esc_html__( 'Five Star Ratings Shortcode Documentation', 'fsrs' ), esc_html__( 'Five Star Ratings Shortcode Documentation', 'fsrs' ), 'manage_options', $this->parent->token, array( $this, 'settings_page' ) );
		}

		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 *
	 * @return void
	 */
	public function settings_assets() {

		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {
				// We're including the farbtastic script & styles here because they're
				// needed for the colour picker.
				// If you're not including a colour picker field then you can leave
				// these calls out as well as the farbtastic dependency for the
				// wpt-admin-js script below.
				wp_enqueue_style( 'farbtastic' );
				wp_enqueue_script( 'farbtastic' );
			}
		}

		wp_register_script( $this->parent->token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), esc_html( FSRS_VERSION ), true );
		wp_enqueue_script( $this->parent->token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->token . '">' . esc_html__( 'Settings', 'fsrs' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {
		$arr = array(
			'p'      => array(),
			'a'      => array( // The link.
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

		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {

				$rel = 'noopener noreferrer'; // Used in both links.

				$url1 = '//en.wikipedia.org/wiki/ISO_4217#Active_code';
				$url2 = '//en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements';
				$url3 = '//en.wikipedia.org/wiki/ISO_8601#Durations';

				$cur_note = sprintf( // Translation string with variables.
					wp_kses( /* translators: ignore the placeholders in the URL */
						__( '<em>(Required)</em> Product price currency in <a href="%1$s" rel="%2$s"><abbr>ISO</abbr> 4217 format</a>.', 'fsrs' ),
						$arr
					),
					esc_url( $url1 ),
					$rel // The 1st & 2nd variables (strings) in the link. Sanitize the url.
				);

				$cur_desc = sprintf(
					wp_kses( /* translators: ignore the placeholders in the URL */
						__( 'Product price currency in <a href="%1$s" rel="%2$s"><abbr>ISO</abbr> 4217 format</a>, <abbr>e.g.</abbr>, “USD”, “GBP”', 'fsrs' ),
						$arr
					),
					esc_url( $url1 ),
					$rel
				);

				$country_note = sprintf(
					wp_kses( /* translators: ignore the placeholders in the URL */
						__( '<em>(Required)</em> Restaurant country code in <a href="%1$s" rel="%2$s"><abbr>ISO</abbr> 3166-1 alpha-2 (2-letter) format</a>.', 'fsrs' ),
						$arr
					),
					esc_url( $url2 ),
					$rel
				);

				$country_desc = sprintf(
					wp_kses( /* translators: ignore the placeholders in the URL */
						__( 'Restaurant country in <a href="%1$s" rel="%2$s"><abbr>ISO</abbr> 3166-1 alpha-2 (2-letter) format</a>, <abbr>e.g.</abbr>, “US”, “UK”, “CN”', 'fsrs' ),
						$arr
					),
					esc_url( $url2 ),
					$rel
				);

				$prep_note = sprintf(
					wp_kses( /* translators: ignore the placeholders in the URL */
						__( '<em>(Required)</em> Recipe preparation time in <a href="%1$s" rel="%2$s"><abbr><abbr>ISO</abbr> 8601 format</a>.', 'fsrs' ),
						$arr
					),
					esc_url( $url3 ),
					$rel
				);

				$prep_desc = sprintf(
					wp_kses( /* translators: ignore the placeholders in the URL */
						__( 'Recipe preparation time in <a href="%1$s" rel="%2$s"><abbr>ISO</abbr> 8601 format</a>, <abbr>e.g.</abbr>, “PT20M”', 'fsrs' ),
						$arr
					),
					esc_url( $url3 ),
					$rel
				);

				$cook_note = sprintf(
					wp_kses( /* translators: ignore the placeholders in the URL */
						__( '<em>(Required)</em> Recipe cooking time in <a href="%1$s" rel="%2$s"><abbr><abbr>ISO</abbr> 8601 format</a>.', 'fsrs' ),
						$arr
					),
					esc_url( $url3 ),
					$rel
				);

				$cook_desc = sprintf(
					wp_kses( /* translators: ignore the placeholders in the URL */
						__( 'Recipe cooking time in <a href="%1$s" rel="%2$s"><abbr>ISO</abbr> 8601 format</a>, <abbr>e.g.</abbr>, “PT45M”', 'fsrs' ),
						$arr
					),
					esc_url( $url3 ),
					$rel
				);

				$settings['options'] = array(
					'title'       => esc_html__( 'Options', 'fsrs' ),
					'description' => wp_kses( __( 'Syntax and formatting options. All options are global, <abbr>i.e.</abbr>, they affect all star ratings on the site. <em>If employing advanced syntax, use only one shortcode per post or page; otherwise the Rich Snippet will not be placed in the document head.</em>', 'fsrs' ), $arr ) . '
		<details>
		<summary>' . esc_html__( 'Shortcode Basic Usage', 'fsrs' ) . '
		</summary>
		<section>
			<div class="col col-3">
				<div class="col__nobreak">
					<h3>' . esc_html__( 'Syntax:', 'fsrs' ) . '</h3>
					<p>[rating stars="<em>float</em>"]</p>
					<dl>
						<dt>rating</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Initiates the shortcode.', 'fsrs' ), $arr ) . '</dd>
						<dt>stars</dt>
						<dd><em>(float)</em> ' . wp_kses( __( '<em>(Required)</em> The star rating expressed as a numeric float. Must end in a single decimal place, <abbr>e.g.</abbr>, <code>1.0</code>, <code>3.3</code>, <code>7.5</code>, <code>2.7</code>.', 'fsrs' ), $arr ) . '</dd>
					</dl>
				</div>
				<div class="col__nobreak">
					<p>' . esc_html__( 'Assuming the default setting with “Number of Stars” set to 5, the following shortcodes will ouput as shown:', 'fsrs' ) . '</p>
					<ul>
						<li><code>[rating stars="0.5"]</code> (' . esc_html__( 'Displays ½ star out of 5', 'fsrs' ) . ')</li>
						<li><code>[rating stars="3.0"]</code> (' . esc_html__( 'Displays 3 stars out of 5', 'fsrs' ) . ')</li>
						<li><code>[rating stars="4.0"]</code> (' . esc_html__( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
						<li><code>[rating stars="2.5"]</code> (' . esc_html__( 'Displays 2½ stars out of 5', 'fsrs' ) . ')</li>
						<li><code>[rating stars="5.5"]</code> (' . esc_html__( 'Incorrect usage, but will display 5 stars out of 5', 'fsrs' ) . ')</li>
					</ul>
				</div>
				<div>
					<p>' . wp_kses( __( 'The 4<sup>th</sup> example produces the following raw output before processing:', 'fsrs' ), $arr ) . '</p>
					<pre><code>&lt;span class="fsrs"&gt;
  &lt;span class="fsrs-stars"&gt;
    &lt;i class="fsrs-fas fa-fw fa-star "&gt;&lt;/i&gt;
    &lt;i class="fsrs-fas fa-fw fa-star "&gt;&lt;/i&gt;
    &lt;i class="fsrs-fas fa-fw fa-star-half-stroke "&gt;&lt;/i&gt;
    &lt;i class="fsrs-far fa-fw fa-star "&gt;&lt;/i&gt;
    &lt;i class="fsrs-far fa-fw fa-star "&gt;&lt;/i&gt;
  &lt;/span&gt;
  &lt;span class="hide fsrs-text fsrs-text__hidden" aria-hidden="false"&gt;' . esc_html__( '2.5 out of 5', 'fsrs' ) . '&lt;/span&gt;
  &lt;span class="lining fsrs-text fsrs-text__visible" aria-hidden="true"&gt;2.5&lt;/span&gt;
&lt;/span&gt;</code></pre>
					</div>
				</div>
			</section>
		</details>
		<details>
		<summary>' . esc_html__( 'Advanced Usage: Product Reviews', 'fsrs' ) . '
		</summary>
			<div class="col col-3">
				<section>
					<h3>' . esc_html__( 'Syntax:', 'fsrs' ) . '</h3>
					<p><code>[rating stars="<em>float</em>" type="<em>str</em>" name="<em>str</em>" desc="<em>str</em>" brand="<em>str</em>" mpn="<em>str</em>" price="<em>int|float</em>" cur="<em>str</em>"]</code></p>
					<p>' . wp_kses( __( '<strong>IMPORTANT!</strong> Double quotation marks are not allowed in text strings. Use single quotation marks only. All plus sign symbols must be <abbr>URL</abbr> encoded: <abbr>e.g</abbr>, <code>%2B</code>. <strong>Incorrect:</strong> <code>This is a text string with incorrectly "double-quoted text" and a plus sign symbol (+)</code>. <strong>Correct:</strong> <code>This is a text string with  \'single-quoted text\' and an encoded plus sign symbol (%2B).</code>', 'fsrs' ), $arr ) . '</p>
					<dl>
						<dt>rating</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Initiates the shortcode.', 'fsrs' ), $arr ) . '</dd>
						<dt>stars</dt>
						<dd><em>(float)</em> ' . wp_kses( __( '<em>(Required)</em> The star rating expressed as a numeric float. Must end in a single decimal place, <abbr>e.g.</abbr>, <code>1.0</code>, <code>3.3</code>, <code>7.5</code>, <code>2.7</code>.', 'fsrs' ), $arr ) . '</dd>
						<dt>type</dt>
						<dd><em>(string)</em> ' . /* translators: don't translate the string “Product” or the code example “<code>type="Product"</code>” */ wp_kses( __( '<em>(Required)</em> Review type. Must be “Product” (case-sensitive), <abbr>i.e.</abbr>, <code>type="Product"</code>.', 'fsrs' ), $arr ) . '</dd>
						<dt>name</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Product name. If set to “title” (case-sensitive), the snippet will use the post title.', 'fsrs' ), $arr ) . '</dd>
						<dt>desc</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Product description. If set to “excerpt” (case-sensitive), the snippet will use the post excerpt.', 'fsrs' ), $arr ) . '</dd>
						<dt>brand</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Reviewed product brand.', 'fsrs' ), $arr ) . '</dd>
						<dt>mpn</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Manufacturer’s product number.', 'fsrs' ), $arr ) . '</dd>
						<dt>price</dt>
						<dd><em>(integer or float) </em> ' . wp_kses( __( '<em>(Required)</em> Product price with an optional currency symbol.', 'fsrs' ), $arr ) . '</dd>
						<dt>cur</dt>
						<dd><em>(string)</em> ' . $cur_note . '</dd>
					</dl>
				</section>
				<hr/>
				<section>
					<h3>' . ( esc_html__( 'Example', 'fsrs' ) ) . '</h3><p>' . esc_html__( 'Shortcode:', 'fsrs' ) . '</p>
					<pre><code>[rating stars="9.5" type="Product" name="title" desc="excerpt" brand="Gitzo" mpn="GK1555T-82TQD" price="1049.99" cur="USD" ]</code></pre>
					<p>' . esc_html__( 'Rich Snippet output:', 'fsrs' ) . '</p>
					<pre><code>{
	  "@context": "http://schema.org",
	  "@type": "Product",
	  "mainEntityOfPage": {
		"@type": "WebPage",
		"@id": "https://mywebsite.com/path/to/review/post/"
	  },
	  "image": {
		"@type": "ImageObject",
		"url": "https://mywebsite.com/path/to/featured/image.jpg",
		"height": "2448",
		"width": "2448"
	  },
	  "name": "Review: Gitzo Traveler, Series 1, 5-section Tripod Kit",
	  "brand": "Gitzo",
	  "mpn": "GK1555T-82TQD",
	  "description": "Spicy jalapeno bacon ipsum dolor amet aliqua enim turducken pastrami est meatball fugiat pork loin ribeye ham. Tongue meatball ea velit, shoulder boudin shankle eiusmod non flank sirloin venison. Jerky proident short loin bresaola. Sint aliqua pork qui landjaeger.",
	  "offers": {
		"@type": "Offer",
		"availability": "https://schema.org/InStock",
		"price": "1049.99",
		"priceCurrency": "USD"
	  },
	  "review": {
		"@type": "Review",
		"author": "Jane Doe",
		"url":  "https://mywebsite.com/janedoe/",
		"datePublished": "2019-12-15T19:32:44-06:00",
		"publisher": {
		  "@type": "Organization",
		  "name": "My Website",
		  "description": "Just another WordPress site",
		  "url": "https://mywebsite.com"
		},
		"reviewRating": {
		  "@type": "Rating",
		  "ratingValue": "9.5",
		  "bestRating": "10",
		  "worstRating": "0.5"
		}
	  },
	  "url": "https://mywebsite.com/path/to/review/post/"
	}</code></pre>
				</section>
			</div>
		</details>
		<details>
		<summary>' . esc_html__( 'Advanced Usage: Restaurant Reviews', 'fsrs' ) . '
		</summary>
			<div class="col col-3">
				<section>
					<h3>' . esc_html__( 'Syntax:', 'fsrs' ) . '</h3>
					<p><code>[rating stars="<em>float</em>" type="<em>str</em>" name="<em>str</em>" desc="<em>str</em>" price="<em>int|float|str</em>" addr="<em>str</em>" locale="<em>str</em>" region="<em>str</em>" postal="<em>str</em>" country="<em>str</em>" tel="<em>str</em>" cuisine="<em>str</em>"]</code></p>
					<p><strong>' . wp_kses( __( 'IMPORTANT! </strong> Double quotation marks are not allowed in text strings. Use single quotation marks only. All plus sign symbols must be <abbr>URL</abbr> encoded: <abbr>e.g</abbr>, <code>%2B</code>. <strong>Incorrect:</strong> <code>This is a text string with incorrectly "double-quoted text" and a plus sign symbol (+)</code>. <strong>Correct:</strong> <code>This is a text string with  \'single-quoted text\' and an encoded plus sign symbol (%2B).</code>', 'fsrs' ), $arr ) . '</p>
					<dl>
						<dt>rating</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Initiates the shortcode.', 'fsrs' ), $arr ) . '</dd>
						<dt>stars</dt>
						<dd><em>(float)</em> ' . wp_kses( __( '<em>(Required)</em> The star rating expressed as a numeric float. Must end in a single decimal place, <abbr>e.g.</abbr>, <code>1.0</code>, <code>3.3</code>, <code>7.5</code>, <code>2.7</code>.', 'fsrs' ), $arr ) . '</dd>
						<dt>type</dt>
						<dd><em>(string)</em> ' . /* translators: don't translate the string “Restaurant” or the code example “<code>type="Restaurant"</code>” */ wp_kses( __( '<em>(Required)</em> Review type. Must be “Restaurant” (case-sensitive), <abbr>i.e.</abbr>,<code>type="Restaurant"</code>.', 'fsrs' ), $arr ) . '</dd>
						<dt>name</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Restaurant name. If set to “title” (case-sensitive), the snippet will use the post title.', 'fsrs' ), $arr ) . '</dd>
						<dt>desc</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Restaurant description. If set to “excerpt” (case-sensitive), the snippet will use the post excerpt.', 'fsrs' ), $arr ) . '</dd>
						<dt>price</dt>
						<dd><em>(integer|float|string)</em> <em>' . wp_kses( __( '(Required)</em> Restaurant price range. May be a string of one or more currency symbols or a descriptive phrase, <abbr>e.g.</abbr>, “$$$”, “💰💰💰”, “Budget-friendly”.', 'fsrs' ), $arr ) . '</dd>
						<dt>addr</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Restaurant street address.', 'fsrs' ), $arr ) . '</dd>
						<dt>locale</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Restaurant city.', 'fsrs' ), $arr ) . '</dd>
						<dt>region</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Restaurant state or province.', 'fsrs' ), $arr ) . '</dd>
						<dt>postal</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Restaurant postal code.', 'fsrs' ), $arr ) . '</dd>
						<dt>country</dt>
						<dd><em>(string)</em> ' . $country_note . '</dd>
						<dt>tel</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Restaurant telephone number.', 'fsrs' ), $arr ) . '</dd>
						<dt>cuisine</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Restaurant cuisine.', 'fsrs' ), $arr ) . '</dd>
					</dl>
				</section>
				<hr/>
				<section>
					<h3>' . esc_html__( 'Example', 'fsrs' ) . '</h3>
					<p>' . esc_html__( 'Shortcode:', 'fsrs' ) . '</p>
					<pre><code>[rating stars="3.5" type="Restaurant" name="title" desc="excerpt" price="$$" addr="119 N. Grand Traverse St." locale="Flint" region="MI" postal="48503" country="US" tel="%2B1 555 555 5555" cuisine="American"]</code></pre>
					<p>' . esc_html__( 'Note the use of “%2B” for the plus-sign (+) in the telephone number.', 'fsrs' ) . '</p>
					<p>' . esc_html__( 'Rich Snippet output:', 'fsrs' ) . '</p>
					<pre><code>{
	  "@context": "https://schema.org",
	  "@type": "Restaurant",
	  "mainEntityOfPage": {
		"@type": "WebPage",
		"@id": "https://mywebsite.com/path/to/review/post/"
	  },
	  "image": {
		  "@type": "ImageObject",
		  "url": "https://mywebsite.com/path/to/featured/image.jpg",
		  "height": "2448",
		  "width": "2448"
	   },
	  "name": "Review: Joe’s Food Truck",
	  "description": "Spicy jalapeno bacon ipsum dolor amet aliqua enim turducken pastrami est meatball fugiat pork loin ribeye ham. Tongue meatball ea velit, shoulder boudin shankle eiusmod non flank sirloin venison. Jerky proident short loin bresaola. Sint aliqua pork qui landjaeger.",
	  "address": {
		"@type": "PostalAddress",
		"streetAddress": "119 N. Grand Traverse St.",
		"addressLocality": "Flint",
		"addressRegion": "MI",
		"postalCode": "48503",
		"addressCountry": "US"
	  },
	  "review": {
		"@type": "Review",
		"author": "David Jones",
		"url":  "https://mysite.com/davidjones/",
		"datePublished": "2019-12-15T19:32:44-06:00",
		"publisher": {
		  "@type": "Organization",
		  "name": "My Website",
		  "description": "Just another WordPress site",
		  "url": "https://mywebsite.com"
		},
		"reviewRating": {
		  "@type": "Rating",
		  "ratingValue": "3.5",
		  "bestRating": "5",
		  "worstRating": "0.5"
		}
	  },
	  "telephone": "+1 555 555 5555",
	  "servesCuisine": "American",
	  "priceRange": "$$",
	  "url": "https://mywebsite.com/path/to/review/post/"
	}</code></pre>
				</section>
			</div>
		</details>
		<details>
		<summary>' . esc_html__( 'Advanced Usage: Recipe Reviews', 'fsrs' ) . '
		</summary>
			<div class="col col-3">
				<section>
					<h3>' . esc_html__( 'Syntax:', 'fsrs' ) . '</h3>
					<p><code>[rating stars="<em>float</em>" type="<em>str</em>" name="<em>str</em>" desc="<em>str</em>" author="<em>str</em>" cuisine="<em>str</em>" keywords="<em>str</em>" prep="<em>str</em>" cook="<em>str</em>" yield="<em>str</em>" cat="<em>str</em>" cal="<em>str</em>" ing="<em>str</em>" steps="{<em>str</em>} <em>str</em>"]</code></p>
					<p><strong>' . wp_kses( __( 'IMPORTANT! </strong> Double quotation marks are not allowed in text strings. Use single quotation marks only. All plus sign symbols must be <abbr>URL</abbr> encoded: <abbr>e.g</abbr>, <code>%2B</code>. <strong>Incorrect:</strong> <code>This is a text string with incorrectly "double-quoted text" and a plus sign symbol (+)</code>. <strong>Correct:</strong> <code>This is a text string with  \'single-quoted text\' and an encoded plus sign symbol (%2B).</code>', 'fsrs' ), $arr ) . '</p>
					<dl>
						<dt>rating</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Initiates the shortcode.', 'fsrs' ), $arr ) . '</dd>
						<dt>stars</dt>
						<dd><em>(float)</em> ' . wp_kses( __( '<em>(Required)</em> The star rating expressed as a numeric float. Must end in a single decimal place, <abbr>e.g.</abbr>, <code>1.0</code>, <code>3.3</code>, <code>7.5</code>, <code>2.7</code>.', 'fsrs' ), $arr ) . '</dd>
						<dt>type</dt>
						<dd><em>(string)</em> ' . /* translators: don't translate the string “Recipe” or the code example “<code>type="Recipe"</code>” */ wp_kses( __( '<em>(Required)</em> Review type. Must be “Recipe” (case-sensitive), <abbr>i.e.</abbr>, <code>type="Recipe"</code>.', 'fsrs' ), $arr ) . '</dd>
						<dt>name</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe name. If set to “title” (case-sensitive), the snippet will use the post title.', 'fsrs' ), $arr ) . '</dd>
						<dt>desc</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe description. If set to “excerpt” (case-sensitive), the snippet will use the post excerpt.', 'fsrs' ), $arr ) . '</dd>
						<dt>auth</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe author.', 'fsrs' ), $arr ) . '</dd>
						<dt>cuisine</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe cuisine.', 'fsrs' ), $arr ) . '</dd>
						<dt>keywords</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe keywords as a comma-separated list.', 'fsrs' ), $arr ) . '</dd>
						<dt>prep</dt>
						<dd><em>(string)</em> ' . $prep_note . '</dd>
						<dt>cook</dt>
						<dd><em>(string)</em> ' . $cook_note . '</dd>
						<dt>yield</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe yield.', 'fsrs' ), $arr ) . '</dd>
						<dt>cat</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe category.', 'fsrs' ), $arr ) . '</dd>
						<dt>cal</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe calories.', 'fsrs' ), $arr ) . '</dd>
						<dt>ing</dt>
						<dd><em>(string)</em> ' . wp_kses( __( '<em>(Required)</em> Recipe ingredients as a comma-separated list.', 'fsrs' ), $arr ) . '</dd>
						<dt>steps</dt>
						<dd><em>(string)</em> ' . /* translators: please translate the variables $name and $text into your own language. */ wp_kses( __( '<em>(Required)</em> Recipe steps in the form {$name} $text, where $name is a <em>short</em> description of the step and $text is the detailed text of the step. Enter as many steps as necessary, <abbr>e.g.</abbr>, <code>{Purée the fruit} Purée the sliced apples and papayas in a blender until smooth. {Garnish and serve} Pour into a tall glass and garnish with the spearmint sprig.</code>', 'fsrs' ), $arr ) . '</dd>
					</dl>
				</section>
				<hr/>
				<section>
					<h3>' . esc_html__( 'Example', 'fsrs' ) . '</h3>
					<p>' . esc_html__( 'Shortcode:', 'fsrs' ) . '</p>
					<pre><code>[rating stars="3.5" type="Recipe" name="title" desc="excerpt" auth="Joe Schmoe" cuisine="American" keywords="weiner, frankfurter, hot dog, awesome" prep="PT10M" cook="PT20M" yield="6 servings" cat="entrée" ing="6 Nathan’s All Beef Skinless Franks, 6 hot dog buns, 2 TBSP unsalted butter, ¼ cup stone-ground mustard, ¼ sweet pickle relish, ¼ minced white onions, ½ cup chili (see recipe)" cal="400 per serving" steps="{A step} Do this. {Another step} Do that. {A third step} Do the other thing. Do another thing. Do yet something else."]</code></pre>
					<p>' . esc_html__( 'Rich Snippet output:', 'fsrs' ) . '</p>
					<pre><code>  {
    "@context": "https://schema.org",
    "@type": "Recipe",
    "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": "https://mywebsite.com/path/to/review/post/"
    },
    "image": {
      "@type": "ImageObject",
      "url": "https://mywebsite.com/path/to/featured/image.jpg",
      "height": "2448",
      "width": "2448"
    },
    "name": "Review: World’s “Greatest Hot Dog” Recipe",
    "description": "Spicy jalapeno bacon ipsum dolor amet aliqua enim turducken pastrami est meatball fugiat pork loin ribeye ham. Tongue meatball ea velit, shoulder boudin shankle eiusmod non flank sirloin venison. Jerky proident short loin bresaola. Sint aliqua pork qui landjaeger. Our rating:",
    "author": "Joe Schmoe",
    "keywords": "weiner, frankfurter, hot dog, awesome",
    "prepTime": "PT10M",
    "cookTime": "PT20M",
    "recipeYield": "6 servings",
    "recipeCategory": "entrée",
    "recipeCuisine": "American",
    "nutrition": {
      "@type": "NutritionInformation",
      "calories": "400 per serving"
    },
    "recipeIngredient": [
      "6 Nathan’s All Beef Skinless Franks",
      "6 hot dog buns",
      "2 TBSP unsalted butter",
      "¼ cup stone-ground mustard",
      "¼ sweet pickle relish",
      "¼ minced white onions",
      "½ cup chili (see recipe)"
    ],
    "recipeInstructions": [
      {
      "@type": "HowToStep",
      "name": "A step",
      "text": "Do this."
      },
      {
      "@type": "HowToStep",
      "name": "Another step",
      "text": "Do that."
      },
      {
      "@type": "HowToStep",
      "name": "A third step",
      "text": "Do the other thing. Do another thing. Do yet something else."
      },
    ],
    "review": {
      "@type": "",
      "author": "",
      "url":  "",
      "datePublished": "",
      "publisher": {
        "@type": "Organization",
        "name": "",
        "description": "",
        "url": ""
      }
    },
    "reviewRating": {
      "@type": "Rating",
      "ratingValue": "",
      "bestRating": "",
      "worstRating": ""
    },
    "url": ""
  }</code></pre>
				</section>
			</div>
		</details>',
					'fields'      => array(
						array(
							'id'          => 'syntax',
							'label'       => esc_html__( 'Syntax', 'fsrs' ),
							'description' => wp_kses( __( 'Choose <code>&lt;i&gt;</code> output for brevity or <code>&lt;span&gt;</code> output for semantics. The default is <code>&lt;i&gt;</code>', 'fsrs' ), $arr ),
							'type'        => 'radio',
							'options'     => array(
								'i'    => '&lt;i&gt;',
								'span' => '&lt;span&gt;',
							),
							'default'     => 'i',
						),
						array(
							'id'          => 'starsmin',
							'label'       => __( 'Minimum Rating', 'fsrs' ),
							'description' => __( 'Change the minimum rating. The default is ½.', 'fsrs' ),
							'type'        => 'range',
							'step'        => '0.5',
							'min'         => '0.0',
							'max'         => '1',
							'default'     => '0.5',
						),
						array(
							'id'          => 'starsmax',
							'label'       => __( 'Maximum Rating', 'fsrs' ),
							'description' => __( 'Change the maximum rating. The default is 5.', 'fsrs' ),
							'type'        => 'range2',
							'step'        => '1',
							'min'         => '3',
							'max'         => '10',
							'default'     => '5',
						),
						array(
							'id'          => 'starcolor',
							'label'       => __( 'Star Color', 'fsrs' ),
							'description' => __( 'Change the star icon color. The icons inherit their color by default.', 'fsrs' ),
							'type'        => 'color',
							'default'     => '#000',
						),
						array(
							'id'          => 'textcolor',
							'label'       => __( 'Text Color', 'fsrs' ),
							'description' => __( 'Change the numeric text color. The text inherits its color by default.', 'fsrs' ),
							'type'        => 'color',
							'default'     => '#000',
						),
						array(
							'id'          => 'size',
							'label'       => __( 'Star Size', 'fsrs' ),
							'description' => __( 'Change the star icon and text rating size. The icons and text inherit their sizes by default.', 'fsrs' ),
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
							'default'     => '',
						),
						array(
							'id'          => 'numericText',
							'label'       => esc_html__( 'Numeric Text Visibility', 'fsrs' ),
							'description' => esc_html__( 'The plugin shows the numeric text after the star icons by default. Show or hide the text with this setting.', 'fsrs' ),
							'type'        => 'radio',
							'options'     => array(
								'show' => 'Show',
								'hide' => 'Hide',
							),
							'default'     => 'show',
						),
					),
				);

				if ( get_option( FSRS_BASE . 'starsmax' ) !== false ) {
					$stars_max = get_option( FSRS_BASE . 'starsmax' );
					$max_int   = intval( $stars_max );
					// Decrement by one: same as "$max_int = $max_int - 1".
					$max_int --;
				}

				if ( get_option( FSRS_BASE . 'starsmin' ) !== false ) {
					$stars_min = get_option( FSRS_BASE . 'starsmin' );
					$min_int   = floatval( $stars_min );
					if ( 1.0 === $min_int ) {
						$min_int = '1.[0-9]|^([1-';
					} elseif ( 0.0 === $min_int ) {
						$min_int = '0.[0-9]|^([1-';
					} else {
						$min_int = '0.[5-9]|^([1-';
					};
				}

				$settings['generator'] = array(
					'title'       => esc_html__( 'Rich Snippets', 'fsrs' ),
					'description' => '<p>' . esc_html__( 'Generate a shortcode to rate a product, restaurant, or recipe with Google Rich Snippets. All fields are required for their respective review type (Product, Restaurant, or Recipe).', 'fsrs' ) . '</p>',
					'fields'      => array(
						array(
							'id'          => 'reviewType',
							'label'       => esc_html__( 'Review Type', 'fsrs' ),
							'description' => wp_kses( __( '<strong> Required.</strong> Choose the review type.', 'fsrs' ), $arr ),
							'type'        => 'radio',
							'options'     => array(
								'Product'    => esc_html__( 'Product', 'fsrs' ),
								'Restaurant' => esc_html__( 'Restaurant', 'fsrs' ),
								'Recipe'     => esc_html__( 'Recipe', 'fsrs' ),
							),
							'required'    => 'required',
							'default'     => '',
						),
						array(
							'id'          => 'reviewRating',
							'label'       => esc_html__( 'Rating', 'fsrs' ),
							'description' => sprintf(
								wp_kses( /* translators: the placeholders %1$.1f and %2$d.0 are indeterminate numerals. Example output: "Must be a 1-decimal place float ranging from 0.0 to 5.0," etc. */
									__( '<strong>Required.</strong> The star rating expressed as a numeral. Must be a 1-decimal place float ranging from %1$.1f to %2$d.0, <abbr>e.g.</abbr>, “2.5”, “1.0”. ', 'fsrs' ),
									array(
										'strong',
										'em',
									)
								),
								$stars_min,
								$stars_max
							),
							'type'        => 'text',
							'pattern'     => '^((' . wp_kses( $stars_max, $arr ) . ')\.0)$|' . wp_kses( $min_int, $arr ) . wp_kses( $max_int, $arr ) . ']?\.[0-9]){1}$',
							'required'    => 'required',
							'default'     => null,

						),
						array(
							'id'          => 'reviewName',
							'label'       => esc_html_x( 'Name', 'noun', 'fsrs' ),
							'description' => esc_html__( 'Product, restaurant, or recipe name. Leave empty to use the post title.', 'fsrs' ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'reviewDesc',
							'label'       => esc_html__( 'Description', 'fsrs' ),
							'description' => esc_html__( 'Product, restaurant, or recipe description. Leave empty to use the post excerpt.', 'fsrs' ),
							'type'        => 'textarea',
							'default'     => '',

						),
						array(
							'id'          => 'prodBrand',
							'label'       => esc_html_x( 'Brand', 'noun', 'fsrs' ),
							'description' => esc_html__( 'Product brand,', 'fsrs' ) . ' <abbr>e.g.</abbr>, “Coca-Cola”',
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'prodMPN',
							'label'       => esc_html__( 'Product number', 'fsrs' ),
							'description' => wp_kses( __( 'Manufacturer product number, <abbr>e.g.</abbr>, “ABZ-10003n”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'prodPrice',
							'label'       => esc_html_x( 'Price', 'noun', 'fsrs' ),
							'description' => wp_kses( __( 'Product price, with optional currency symbol &amp; optional decimals, <abbr>e.g.</abbr>, “59”, “20.89”, “$5”, “€90.99”. Use Latin abbreviation for right-to-left languages, <abbr>e.g.<abbr>, use “DZD” for the Algerian dinar instead of <span lang="arq">”دج“</span>', 'fsrs' ), $arr ),
							// Numerals with or without currency sybols or decimals, exactly 2 places, commas and currency symbols allowed.
							'pattern'     => '(¤|Ar|฿|B\/\.|Br|₿|Bs\.|GH₵|₡|C\$|D|ден|DA|BD|I\.Q\.D\.|JD|K\.D\.|LD|дин|DT|DH|Dhs|Db|\$|₫|€|ƒ}Ft}FBu|FCFA|CFA|F|Fr|fr|FRw|G|₲|₴|₭|₭n|Kč|kr|kn|Kz|K|ლ|L|Le|лв\.|E|₺|M|₼|KM|MT|MTn|Nfk|Nfa|₦|Nu\.|UM|T\$|MOP\$|圓|元|₱|₱|PHP|P|£|LL|LS|P|Q|R|R\$|QR|SR|៛|RM|PRB|₽|Rf\.|₹|₨|SRe|SR|Rp|₪|Tsh|TSh|Ksh|KSh|Sh\.So\.|USh|S\/|сом|৳|WS\$|₸|₮|VT|₩|¥|zł|௹|૱|ರ|රු|꠸)??([0-9]{1,3}(\.|,)([0-9]{3}(\.|,))*?[0-9]{3}|[0-9]+)(.[0-9][0-9])??$',
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'prodCur',
							'label'       => esc_html__( 'Currency', 'fsrs' ),
							'description' => $cur_desc,
							'pattern'     => '^[A-Z]{3}?',
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'restRange',
							'label'       => esc_html__( 'Price Range', 'fsrs' ),
							'description' => wp_kses( __( 'Restaurant price range, <abbr>e.g.</abbr>, “$$$”, “💰💰💰”, “moderate”, “pricey”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'restAddr',
							'label'       => esc_html__( 'Address', 'fsrs' ),
							'description' => wp_kses( __( 'Restaurant street address, <abbr>e.g.</abbr>, “123 Main St”, “456 Carrer de la Diputació”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'restCity',
							'label'       => esc_html__( 'City', 'fsrs' ),
							'description' => wp_kses( __( 'Restaurant town or city, <abbr>e.g.</abbr>, “Springfield”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'restState',
							'label'       => esc_html__( 'State/Province', 'fsrs' ),
							'description' => wp_kses( __( 'Restaurant state, territory, or province, <abbr>e.g.</abbr>, “OK”, “Alaska”, “ON”, “Puerto Rico”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'restPost',
							'label'       => esc_html__( 'Postal Code', 'fsrs' ),
							'description' => wp_kses( __( 'Restaurant postal code, <abbr>e.g.</abbr>, “10001”, “KOA 1K0”, “90001-1111”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'restCountry',
							'label'       => esc_html__( 'Country', 'fsrs' ),
							'description' => $country_desc,
							'type'        => 'text',
							'pattern'     => '^[A-Z]{2}?',
							'default'     => '',

						),
						array(
							'id'          => 'restTel',
							'label'       => esc_html__( 'Telephone', 'fsrs' ),
							'description' => wp_kses( __( 'Restaurant telephone number, <abbr>e.g.</abbr>, “+1 555 555 5555”', 'fsrs' ), $arr ),
							'type'        => 'tel',
							'minlength'   => '4',
							'maxlength'   => '30',
							'default'     => '',

						),
						array(
							'id'          => 'resrecCuisine',
							'label'       => esc_html__( 'Cuisine', 'fsrs' ),
							'description' => wp_kses( __( 'Restaurant or Recipe cuisine, <abbr>e.g.</abbr>, “Thai”, “Soulfood”, “Cajun”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'recAuthor',
							'label'       => esc_html__( 'Author', 'fsrs' ),
							'description' => wp_kses( __( 'Recipe author, <abbr>e.g.</abbr>, “Madhur Jaffrey”, “James Beard”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'recKeywords',
							'label'       => esc_html__( 'Keywords', 'fsrs' ),
							'description' => wp_kses( __( 'Recipe keywords as a comma-separated list, <abbr>e.g.</abbr>, “easy, low-calorie, keto”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'recPrep',
							'label'       => esc_html__( 'Preparation Time', 'fsrs' ),
							'description' => $prep_desc,
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'recCook',
							'label'       => esc_html__( 'Cooking Time', 'fsrs' ),
							'description' => $cook_desc,
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'recYield',
							'label'       => esc_html__( 'Yield', 'fsrs' ),
							'description' => wp_kses( __( 'Recipe yield, <abbr>e.g.</abbr>, “4 servings”, “3 cups”, “2.5 liters”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'recCat',
							'label'       => esc_html__( 'Category', 'fsrs' ),
							'description' => wp_kses( __( 'Recipe category, <abbr>e.g.</abbr>, “breakfast”, “appetizer”, “cocktail”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'recCal',
							'label'       => esc_html__( 'Calories', 'fsrs' ),
							'description' => wp_kses( __( 'Recipe calories, <abbr>e.g.</abbr>, “400 per serving”', 'fsrs' ), $arr ),
							'type'        => 'text',
							'default'     => '',

						),
						array(
							'id'          => 'recIng',
							'label'       => esc_html__( 'Ingredients', 'fsrs' ),
							'description' => wp_kses( __( 'Recipe ingredients as a comma-separated list, <abbr>e.g.</abbr>, “1 medium onion, 6 garlic cloves, 2 TBSP unsalted butter”', 'fsrs' ), $arr ),
							'type'        => 'textarea',
							'default'     => '',

						),
						array(
							'id'          => 'recSteps',
							'label'       => esc_html_x( 'Steps', 'noun, plural', 'fsrs' ),
							/* translators: please translate the variables $name and $text into your own language. */
							'description' => wp_kses( __( 'Recipe steps in the form {$name} $text, where $name is a <em>short</em> description of the step and $text is the detailed text of the step. Enter as many steps as necessary, <abbr>e.g.</abbr>, <code>{Purée the fruit} Purée the sliced apples and papayas in a blender until smooth. {Garnish and serve} Pour into a tall glass and garnish with the spearmint sprig.</code>', 'fsrs' ), $arr ),
							'type'        => 'textarea',
							'pattern'     => '^({.+}(?! {) .+)$',
							'default'     => '',

						),
					),
				);

				$settings = apply_filters( $this->parent->token . '_settings_fields', $settings );

				return $settings;
			}
		}

		if ( ( ! fsrs_fs()->is__premium_only() ) || ( ( fsrs_fs()->is__premium_only() ) && ( ! fsrs_fs()->can_use_premium_code() ) ) ) {
			$settings['documentation'] = array(
				'title'       => esc_html__( 'Documentation', 'fsrs' ),
				'description' => esc_html__( 'The FREE version of this plugin has no settings. For usage examples, see below.', 'fsrs' ) . '
	<details>
	<summary>' . esc_html__( 'Shortcode Examples', 'fsrs' ) . '
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
				<p>' . esc_html__( 'The following shortcodes will ouput as shown:', 'fsrs' ) .
				'</p>
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
    &lt;i class="fsrs-fas fa-fw fa-star-half-stroke "&gt;&lt;/i&gt;
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
	<summary>' . esc_html__( 'Account Info &amp; Support', 'fsrs' ) . '
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
	<summary>' . esc_html__( 'PRO Only Features', 'fsrs' ) . '
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
			<li>' . esc_html__( 'Show or hide numeric text', 'fsrs' ) . '</li>
			<li>' . esc_html__( 'Locale aware decimal separator', 'fsrs' ) . '</li>
			<li>' . esc_html__( 'Options reset button', 'fsrs' ) . '</li>
		</ul>
	</details>',
			);

			$settings = apply_filters( $this->parent->token . '_settings_fields', $settings );

			return $settings;
		}
	}

	/**
	 * Register plugin settings
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {
			// Check posted/selected tab.
			$current_section = '';
			if ( fsrs_fs()->is__premium_only() ) {
				if ( fsrs_fs()->can_use_premium_code() ) {

					 // phpcs:disable
					if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
						$current_section = sanitize_text_field( wp_unslash( $_POST['tab'] ) );
					} else {
						if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
							$current_section = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
						}
					}
					// phpcs:enable
				}
			}

			if ( ( ! fsrs_fs()->is__premium_only() ) || ( ( fsrs_fs()->is__premium_only() ) && ( ! fsrs_fs()->can_use_premium_code() ) ) ) {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) { // phpcs:ignore
					$current_section = sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( fsrs_fs()->is__premium_only() ) {
					if ( fsrs_fs()->can_use_premium_code() ) {
						if ( $current_section && $current_section !== $section ) {
							continue;
						}
					}
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->token );

				if ( fsrs_fs()->is__premium_only() ) {
					if ( fsrs_fs()->can_use_premium_code() ) {
						foreach ( $data['fields'] as $field ) {

							// Validation callback for field.
							$validation = '';
							if ( isset( $field['callback'] ) ) {
								$validation = $field['callback'];
							}

							// Register field.
							$option_name = FSRS_BASE . $field['id'];
							register_setting( $this->parent->token, $option_name, $validation );

							// Add field to page.
							add_settings_field(
								$field['id'],
								$field['label'],
								array( $this->parent->admin, 'display_field' ),
								$this->parent->token,
								$section,
								array(
									'field'  => $field,
									'prefix' => FSRS_BASE,
								)
							);

						}

						if ( ! $current_section ) {
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * Output the settings
	 *
	 * @param string $section The individual settings sections.
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html; // phpcs:ignore
	}

	/**
	 * Load settings page content
	 *
	 * @return void
	 * @param string $hook_suffix Fires in head section for a specific admin page.
	 */
	public function settings_page( $hook_suffix ) {
		global $pagenow;
		$arr = array(); // For wp_kses.

		if ( get_option( FSRS_BASE . 'starsmax' ) !== false ) {
			$stars_max = get_option( FSRS_BASE . 'starsmax' );
			$max_int   = intval( $stars_max );
			// Same as "$max_int = $max_int - 1".
			$max_int--;
		}

		if ( get_option( FSRS_BASE . 'starsmin' ) !== false ) {
			$stars_min = get_option( FSRS_BASE . 'starsmin' );
			$min_int   = floatval( $stars_min );
			if ( 1.0 === $min_int ) {
				$min_int = '1.[0-9]|^([1-';
			} elseif ( 0.0 === $min_int ) {
				$min_int = '0.[0-9]|^([1-';
			} else {
				$min_int = '0.[5-9]|^([1-';
			};
		}

		// Build page HTML.
		$html = '<div class="wrap" id="' . $this->parent->token . '_settings">' . "\n";
		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {
				$html .= '<h2><i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i> ' . esc_html__( 'Five-Star Ratings Shortcode Settings', 'fsrs' ) . ' <i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i></h2>' . "\n";

				$tab = '';

				if ( ( 'options-general.php' == $pagenow ) && ( isset( $_GET['tab'] ) ) && ( 'generator' == $_GET['tab'] ) ) { // phpcs:ignore
					if ( get_option( FSRS_BASE . 'reviewRating' ) !== false ) {
						$stars = get_option( FSRS_BASE . 'reviewRating' );
					}

					if ( get_option( FSRS_BASE . 'reviewType' ) !== false ) {
						$type = get_option( FSRS_BASE . 'reviewType' );
					}

					if ( get_option( FSRS_BASE . 'reviewName' ) !== false ) {
						$name = get_option( FSRS_BASE . 'reviewName' );
					}

					if ( get_option( FSRS_BASE . 'reviewDesc' ) !== false ) {
						$desc = get_option( FSRS_BASE . 'reviewDesc' );
					}

					if ( get_option( FSRS_BASE . 'prodBrand' ) !== false ) {
						$brand = get_option( FSRS_BASE . 'prodBrand' );
					}

					if ( get_option( FSRS_BASE . 'prodMPN' ) !== false ) {
						$mpn = get_option( FSRS_BASE . 'prodMPN' );
					}

					if ( get_option( FSRS_BASE . 'prodPrice' ) !== false ) {
						$price = get_option( FSRS_BASE . 'prodPrice' );
					}

					if ( get_option( FSRS_BASE . 'prodCur' ) !== false ) {
						$cur = get_option( FSRS_BASE . 'prodCur' );
					}

					if ( get_option( FSRS_BASE . 'restRange' ) !== false ) {
						$range = get_option( FSRS_BASE . 'restRange' );
					}

					if ( get_option( FSRS_BASE . 'restAddr' ) !== false ) {
						$addr = get_option( FSRS_BASE . 'restAddr' );
					}

					if ( get_option( FSRS_BASE . 'restCity' ) !== false ) {
						$locale = get_option( FSRS_BASE . 'restCity' );
					}

					if ( get_option( FSRS_BASE . 'restState' ) !== false ) {
						$region = get_option( FSRS_BASE . 'restState' );
					}

					if ( get_option( FSRS_BASE . 'restPost' ) !== false ) {
						$postal = get_option( FSRS_BASE . 'restPost' );
					}

					if ( get_option( FSRS_BASE . 'restCountry' ) !== false ) {
						$country = get_option( FSRS_BASE . 'restCountry' );
					}

					if ( get_option( FSRS_BASE . 'restTel' ) !== false ) {
						$tel = get_option( FSRS_BASE . 'restTel' );
					}

					if ( get_option( FSRS_BASE . 'resrecCuisine' ) !== false ) {
						$cuisine = get_option( FSRS_BASE . 'resrecCuisine' );
					}

					if ( get_option( FSRS_BASE . 'recAuthor' ) !== false ) {
						$auth = get_option( FSRS_BASE . 'recAuthor' );
					}

					if ( get_option( FSRS_BASE . 'recKeywords' ) !== false ) {
						$keywd = get_option( FSRS_BASE . 'recKeywords' );
					}

					if ( get_option( FSRS_BASE . 'recPrep' ) !== false ) {
						$prep = get_option( FSRS_BASE . 'recPrep' );
					}

					if ( get_option( FSRS_BASE . 'recCook' ) !== false ) {
						$cook = get_option( FSRS_BASE . 'recCook' );
					}

					if ( get_option( FSRS_BASE . 'recYield' ) !== false ) {
						$yield = get_option( FSRS_BASE . 'recYield' );
					}

					if ( get_option( FSRS_BASE . 'recCat' ) !== false ) {
						$category = get_option( FSRS_BASE . 'recCat' );
					}

					if ( get_option( FSRS_BASE . 'recCal' ) !== false ) {
						$calories = get_option( FSRS_BASE . 'recCal' );
					}

					if ( get_option( FSRS_BASE . 'recIng' ) !== false ) {
						$ing = get_option( FSRS_BASE . 'recIng' );
					}

					if ( get_option( FSRS_BASE . 'recSteps' ) !== false ) {
						$steps = get_option( FSRS_BASE . 'recSteps' );
					}

					$array = array(
						$name,
						$desc,
						$brand,
						$mpn,
						$price,
						$range,
						$addr,
						$locale,
						$region,
						$postal,
						$tel,
						$cuisine,
						$auth,
						$keywd,
						$prep,
						$cook,
						$yield,
						$category,
						$calories,
						$ing,
						$steps,
					);

					foreach ( $array as $key => &$value ) {
						if ( strpos( $value, '+' ) !== false ) {
							$value = str_replace( '+', '%2B', $value );
						}
						if ( strpos( $value, '"' ) !== false ) {
							$value = str_replace( '"', '\'', $value );

						}
					}

					$name     = $array[0];
					$desc     = $array[1];
					$brand    = $array[2];
					$mpn      = $array[3];
					$price    = $array[4];
					$range    = $array[5];
					$addr     = $array[6];
					$locale   = $array[7];
					$region   = $array[8];
					$postal   = $array[9];
					$tel      = $array[10];
					$cuisine  = $array[11];
					$auth     = $array[12];
					$keywd    = $array[13];
					$prep     = $array[14];
					$cook     = $array[15];
					$yield    = $array[16];
					$category = $array[17];
					$calories = $array[18];
					$ing      = $array[19];
					$steps    = $array[20];

					if ( '' === $name ) {
						$name = 'title';
					}

					if ( '' === $desc ) {
						$desc = 'excerpt';
					}

					if ( get_option( FSRS_BASE . 'starsmax' ) !== false ) {
						$stars_max = get_option( FSRS_BASE . 'starsmax' );
					}

					if ( get_option( FSRS_BASE . 'starsmin' ) !== false ) {
						$stars_min = get_option( FSRS_BASE . 'starsmin' );
						$min_int   = floatval( $stars_min );
						if ( 0 === $min_int ) {
							$stars_min = 0.0;
						} elseif ( 1 === $min_int ) {
							$stars_min = 1.0;
						} else {
							$stars_min = 0.5;
						}
					}

					$heading = '<h3>' . esc_html__( 'Please copy the following shortcode and paste it into your reviews post or page:', 'fsrs' ) . '</h3>';

					if ( 'Product' === $type ) {
						$shortcode = '<div id="shortcode">' . $heading . '<code id="product-shortcode">[rating stars="' . wp_kses( $stars, $arr ) . '" type="' . wp_kses( $type, $arr ) . '" name="' . wp_kses( $name, $arr ) . '" desc="' . wp_kses( $desc, $arr ) . '" brand="' . wp_kses( $brand, $arr ) . '" mpn="' . wp_kses( $mpn, $arr ) . '" price="' . wp_kses( $price, $arr ) . '" cur="' . wp_kses( $cur, $arr ) . '"]</code></div><button class="copyBtn button button-primary" data-clipboard-target="#product-shortcode">' . esc_html_x( 'Copy Shortcode', 'imperative verb', 'fsrs' ) . '</button>';
					} elseif ( 'Restaurant' === $type ) {
						$shortcode = '<div id="shortcode">' . $heading . '<code id="restaurant-shortcode">[rating stars="' . wp_kses( $stars, $arr ) . '" type="' . wp_kses( $type, $arr ) . '" name="' . wp_kses( $name, $arr ) . '" desc="' . wp_kses( $desc, $arr ) . '" price="' . wp_kses( $range, $arr ) . '" addr="' . wp_kses( $addr, $arr ) . '" locale="' . wp_kses( $locale, $arr ) . '" region="' . wp_kses( $region, $arr ) . '" postal="' . wp_kses( $postal, $arr ) . '" country="' . wp_kses( $country, $arr ) . '" tel="' . wp_kses( $tel, $arr ) . '" cuisine="' . wp_kses( $cuisine, $arr ) . '"]</code></div><button class="copyBtn button button-primary" data-clipboard-target="#restaurant-shortcode">' . esc_html_x( 'Copy Shortcode', 'imperative verb', 'fsrs' ) . '</button>';
					} elseif ( 'Recipe' === $type ) {
						$shortcode = '<div id="shortcode">' . $heading . '<code id="recipe-shortcode">[rating stars="' . wp_kses( $stars, $arr ) . '" type="' . wp_kses( $type, $arr ) . '" name="' . wp_kses( $name, $arr ) . '" desc="' . wp_kses( $desc, $arr ) . '" author="' . wp_kses( $auth, $arr ) . '" cuisine="' . wp_kses( $cuisine, $arr ) . '" keywords="' . wp_kses( $keywd, $arr ) . '" prep="' . wp_kses( $prep, $arr ) . '" cook="' . wp_kses( $cook, $arr ) . '" yield="' . wp_kses( $yield, $arr ) . '" cat="' . wp_kses( $category, $arr ) . '" cal="' . wp_kses( $calories, $arr ) . '" ing="' . wp_kses( $ing, $arr ) . '" steps="' . wp_kses( $steps, $arr ) . '"]</code></div><button class="copyBtn button button-primary" data-clipboard-target="#recipe-shortcode">' . esc_html_x( 'Copy Shortcode', 'imperative verb', 'fsrs' ) . '</button>';
					} else {
						$shortcode = $type;
					}

					$html .= $shortcode;
				}
			}
		}

		if ( ( ! fsrs_fs()->is__premium_only() ) || ( ( fsrs_fs()->is__premium_only() ) && ( ! fsrs_fs()->can_use_premium_code() ) ) ) {
			$html .= '<h2><i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i> ' . esc_html__( 'Five-Star Ratings Shortcode Documentation', 'fsrs' ) . ' <i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i></h2>' . "\n";
		}

		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {
				$tab = '';

				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) { // phpcs:ignore
					$tab .= $_GET['tab']; // phpcs:ignore
				}

				// Show page tabs.
				if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

					$html .= '<h2 class="nav-tab-wrapper">' . "\n";

					$c = 0;
					foreach ( $this->settings as $section => $data ) {

						// Set tab class.
						$class = 'nav-tab';
						if ( ! isset( $_GET['tab'] ) ) {
							if ( 0 === $c ) {
								$class .= ' nav-tab-active';
							}
						} else {
							if ( isset( $_GET['tab'] ) && $section === $_GET['tab'] ) {
								$class .= ' nav-tab-active';
							}
						}

						// Set tab link.
						$tab_link = add_query_arg( array( 'tab' => $section ) );
						if ( isset( $_GET['settings-updated'] ) ) {
							$tab_link = remove_query_arg( 'settings-updated', $tab_link );
						}

						// Output tab.
						$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

						++$c;
					}

					$html .= '</h2>' . "\n";
				}
			}
		}

		$html .= '
		<form method="post" action="options.php" name="fsrs_settings" id="fsrs_settings" enctype="multipart/form-data">' . "\n";

		// Get settings fields.
		ob_start();
		settings_fields( $this->parent->token );
		do_settings_sections( $this->parent->token );
		$html .= ob_get_clean();

		global $pagenow; // Run certain logic ONLY if we are on the correct settings page.

		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {
				$html .= '<p class="submit">' . "\n";
				$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
			}
		}

		echo $html; // phpcs:ignore

		$html2 = '';

		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {
				submit_button( esc_html__( 'Save Settings', 'fsrs' ), 'primary', 'save_fsrs_options', true ) . '<span style="display:inline-block;width:1rem;"></span>';
				$html2 = '</p>' . "\n";
			}
		}

		$html2 .= '</form>' . "\n";

		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {
				if ( ( 'options-general.php' === $pagenow ) && ( isset( $_GET['tab'] ) ) && ( 'generator' === $_GET['tab'] ) ) {
					$html2 .= '
	<form id="fsrs-reset" name="fsrs-reset" method="post" action="options-general.php?page=' . $this->parent->token . '&tab=generator">';
					$html2 .= wp_nonce_field( plugin_basename( __FILE__ ), 'fsrs_reset_nonce', true, false );
					$html2 .= '
		<p class="submit"><input name="reset" class="button button-secondary" type="submit" value="' . esc_html__( 'Reset the Shortcode Generator', 'fsrs' ) . '" >
		<input type="hidden" name="action" value="reset" />
	  </p>
	</form>';

					if ( isset( $_POST['reset'] ) ) {
						if ( ! isset( $_POST['fsrs_reset_nonce'] ) || ! wp_verify_nonce( ( sanitize_key( $_POST['fsrs_reset_nonce'] ) ), plugin_basename( __FILE__ ) ) ) {
							die( esc_html__( 'Invalid nonce. Form submission blocked!', 'fsrs' ) ); // Get out of here, the nonce is rotten!
						} else {
							$array = array(
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
								'fsrs_recSteps',
							);
							foreach ( $array as &$item ) {
								update_option( $item, '' );
							}
							echo "<meta http-equiv='refresh' content='0'>";
						}
					}
				}

				if ( ( 'options-general.php' === $pagenow ) && ( 'five-star-ratings-shortcode' === $_GET['page'] ) ) { // phpcs:ignore
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
				pattern: "' . sprintf(
					wp_kses( /* translators: /* translators: the placeholders %1$.1f and %2$d.0 are indeterminate numerals. Example output: "Rating must be a 1-decimal place float ranging from 0.0 to 5.0," etc. */
						__( 'Rating must be a 1-decimal place float ranging from %1$.1f to to %2$d.0, <abbr>e.g.</abbr>, “3.5”, “1.0”. ', 'fsrs' ),
						array(
							'abbr',
						)
					),
					$stars_min,
					$stars_max
				) //phpcs:ignore
					. '"
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
				pattern: "' . sprintf(
					wp_kses( /* translators: please translate the variables $name and $text into your own language. */
						__( 'Recipe steps must be in the form {$name} $text, repeating as needed, where $name is a <em>short</em> description of the step and $text is the detailed text of the step.', 'fsrs' ),
						array(
							'em',
						)
					),
					$stars_max
				) //phpcs:ignore
					. '"
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
		}

		if ( fsrs_fs()->is__premium_only() ) {
			if ( fsrs_fs()->can_use_premium_code() ) {
				if ( ( 'options-general.php' === $pagenow ) && ( ( isset( $_GET['tab'] ) ) && ( 'options' === $_GET['tab'] ) ) || ( ! isset( $_GET['tab'] ) ) ) {
					$html2 .= '
	<form id="fsrs-reset-options" name="fsrs-reset-options" method="post" action="options-general.php?page=' . $this->parent->token . '&tab=options">';
					$html2 .= wp_nonce_field( plugin_basename( __FILE__ ), 'fsrs_reset_default_nonce', true, false );
					$html2 .= '
		<p class="submit"><input name="reset" class="button button-secondary" type="submit" value="' . esc_html__( 'Reset Defaults', 'fsrs' ) . '" >
		<input type="hidden" name="action" value="reset" />
		</p>
	</form>';

					if ( isset( $_POST['reset'] ) ) {
						if ( ! isset( $_POST['fsrs_reset_default_nonce'] ) || ! wp_verify_nonce( ( sanitize_key( $_POST['fsrs_reset_default_nonce'] ) ), plugin_basename( __FILE__ ) ) ) {
							die( esc_html__( 'Invalid nonce. Form submission blocked!', 'fsrs' ) ); // Get out of here, the nonce is rotten!
						} else {
							update_option( 'fsrs_syntax', 'i' );
							update_option( 'fsrs_starsmin', '0.5' );
							update_option( 'fsrs_starsmax', '5' );
							update_option( 'fsrs_numericText', 'show' );
							$array = array(
								'fsrs_starsize',
								'fsrs_starcolor',
								'fsrs_textcolor',
							);
							foreach ( $array as &$item ) {
								update_option( $item, '' );
							};
							echo "<meta http-equiv='refresh' content='0'>";
						}
					}
				}
			}
		}

		$html2 .= '</div>' . "\n";
		echo $html2; // phpcs:ignore

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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Class_Five_Star_Ratings_Shortcode_Settings is forbidden.' ), $this->parent->_version ); // phpcs:ignore
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Class_Five_Star_Ratings_Shortcode_Settings is forbidden.' ), $this->parent->_version ); // phpcs:ignore
	} // End __wakeup()

}
