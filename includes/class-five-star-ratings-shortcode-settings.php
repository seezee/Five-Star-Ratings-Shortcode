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
                'description' => __( 'Syntax and formatting options. All options are global,', 'fsrs' ) . ' <abbr>i.e.</abbr>, ' . __( 'they affect all star ratings on the site.', 'fsrs' ) . ' <em>' . __( 'If employing advanced syntax, use only one shortcode per post or page; otherwise the Rich Snippet will not be placed in the document head.', 'fsrs' ) . '</em>
	<details>
	<summary class="wp-admin-lite-blue">' . __( 'Shortcode Basic Usage', 'fsrs' ) . '
	</summary>
	<section>
		<div class="col col-3">
			<div class="col__nobreak">
				<h3>' . __( 'Syntax:', 'fsrs' ) . '</h3>
				<p>' . __( '[rating stars=<em>int</em>]', 'fsrs' ) . '</p>
				<dl>
					<dt>rating</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Initiates the shortcode.', 'fsrs' ) . '</dd>
					<dt>stars</dt>
					<dd><em>(' . __( 'integer', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'The quantity of whole stars to display. Must end in a single decimal place (.0 or .5).', 'fsrs' ) . '</dd>
				</dl>
			</div>
			<div class="col__nobreak">
				<p>' . __( 'Assuming the default setting with', 'fsrs' ) . ' &ldquo;' . __( 'Number of Stars', 'fsrs' ) . '&rdquo; ' . __( 'set to 5, the following shortcodes will ouput as shown:', 'fsrs' ) . '</p>
				<ul>
					<li><code>[rating stars="0.5"]</code> (' . __( 'Displays', 'fsrs' ) . ' &frac12 ' . __( 'star out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="3.0"]</code> (' . __( 'Displays 3 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="4.0"]</code> (' . __( 'Displays 4 stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="2.5"]</code> (' . __( 'Displays 2', 'fsrs' ) . '&frac12 ' . __( 'stars out of 5', 'fsrs' ) . ')</li>
					<li><code>[rating stars="5.5"]</code> (' . __( 'Incorrect usage but will display 5 stars out of 5', 'fsrs' ) . ')</li>
				</ul>
			</div>
			<div>
				<p>' . __( 'The 4<sup>th</sup> example produces the following raw output before processing:', 'fsrs' ) . '</p>
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
		</section>
	</details>
	<details>
	<summary class="wp-admin-lite-blue">' . __( 'Advanced Usage: Product Reviews', 'fsrs' ) . '
	</summary>
		<div class="col col-3">
			<section>
				<h3>' . __( 'Syntax:', 'fsrs' ) . '</h3>
				<p><code>[rating stars=<em>int</em> type=<em>str</em> name=<em>str</em> desc=<em>str</em> brand=<em>str</em> mpn=<em>str</em> price=<em>int</em> cur=<em>str</em>]</code></p>
				<dl>
					<dt>rating</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Initiates the shortcode.', 'fsrs' ) . '</dd>
					<dt>stars</dt>
					<dd><em>(' . __( 'integer', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'The quantity of whole stars to display. Must end in a single decimal place (.0 or .5).', 'fsrs' ) . '</dd>
					<dt>type</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Review type. Must be &ldquo;Product&rdquo; (case-sensitive),', 'fsrs' ) . ' <abbr>i.e.</abbr>, <code>type="Product"</code>.</dd>
					<dt>name</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Product name. If set to &ldquo;title&rdquo; (case-sensitive), the snippet will use the post title.', 'fsrs' ) . '</dd>
					<dt>desc</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Product description. If set to &ldquo;excerpt&rdquo; (case-sensitive), the snippet will use the post excerpt.', 'fsrs' ) . '</dd>
					<dt>brand</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Brand of reviewed product.', 'fsrs' ) . '</dd>
					<dt>mpn</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Manufacturer&rsquo;s part number.', 'fsrs' ) . '</dd>
					<dt>price</dt>
					<dd><em>(' . __( 'integer', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Product price.', 'fsrs' ) . '</dd>
					<dt>cur</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Product price currency in', 'fsrs' ) . ' <a href="https://www.iso.org/iso-4217-currency-codes.html"><abbr>ISO</abbr> 4217</a> ' . __( 'currency format', 'fsrs' ) . '</dd>
				</dl>
			</section>
			<hr/>
			<section>
				<h3>Example</h3>
				<p>Shortcode:</p>
				<pre><code>[rating stars="9.5" type="Product" name="title" desc="excerpt" brand="Gitzo" mpn="GK1555T-82TQD" price="1049.99" cur="USD" ]</code></pre>
				<p>Rich Snippet output:</p>
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
	<summary class="wp-admin-lite-blue">' . __( 'Advanced Usage: Restaurant Reviews', 'fsrs' ) . '
	</summary>
		<div class="col col-3">
			<section>
				<h3>' . __( 'Syntax:', 'fsrs' ) . '</h3>
				<p><code>[rating stars=<em>int</em> type=<em>str</em> name=<em>str</em> desc=<em>str</em> price=<em>int|str</em> addr=<em>str</em> locale=<em>str</em> region=<em>str</em> postal=<em>str</em> country=<em>str</em> tel=<em>str</em> cuisine=<em>str</em>]</code></p>
				<dl>
					<dt>rating</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Initiates the shortcode.', 'fsrs' ) . '</dd>
					<dt>stars</dt>
					<dd><em>(' . __( 'integer', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'The quantity of whole stars to display. Must end in a single decimal place (.0 or .5).', 'fsrs' ) . '</dd>
					<dt>type</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Review type. Must be &ldquo;Restaurant&rdquo; (case-sensitive),', 'fsrs' ) . ' <abbr>i.e.</abbr>, <code>type="Restaurant"</code>.</dd>
					<dt>name</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant name. If set to &ldquo;title&rdquo; (case-sensitive), the snippet will use the post title.', 'fsrs' ) . '</dd>
					<dt>desc</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant description. If set to &ldquo;excerpt&rdquo; (case-sensitive), the snippet will use the post excerpt.', 'fsrs' ) . '</dd>
					<dt>price</dt>
					<dd><em>(' . __( 'integer|string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant price range. May be an integer or a string of one or more currency symbols.', 'fsrs' ) . '</dd>
					<dt>addr</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant street address.', 'fsrs' ) . '</dd>
					<dt>locale</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant city.', 'fsrs' ) . '</dd>
					<dt>region</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant state or province.', 'fsrs' ) . '</dd>
					<dt>postal</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant postal code.', 'fsrs' ) . '</dd>
					<dt>country</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant country code in', 'fsrs' ) . ' <a href="https://www.iso.org/iso-3166-country-codes.html"><abbr>ISO</abbr> 3166-1 alpha-2</a> ' . __( '(2-letter) format', 'fsrs' ) . '</dd>
					<dt>tel</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant telephone number.', 'fsrs' ) . '</dd>
					<dt>cuisine</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Restaurant cuisine.', 'fsrs' ) . '</dd>
				</dl>
			</section>
			<hr/>
			<section>
				<h3>Example</h3>
				<p>Shortcode:</p>
				<pre><code>[rating stars="3.5" type="Restaurant" name="title" desc="excerpt" price="$$" addr="119 N. Grand Traverse St." locale="Flint" region="MI" postal="48503" country="US" tel="%2B1 555 555 5555" cuisine="American"]</code></pre>
				<p>' . __( 'Note the use of', 'fsrs' ) . ' &ldquo;%2B&rdquo; ' . __( 'for the plus-sign', 'fsra' ) . ' (+) ' . __( 'in the telephone number.', 'fsrs' ) . '</p>
				<p>Rich Snippet output:</p>
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
	<summary class="wp-admin-lite-blue">' . __( 'Advanced Usage: Recipe Reviews', 'fsrs' ) . '
	</summary>
		<div class="col col-3">
			<section>
				<h3>' . __( 'Syntax:', 'fsrs' ) . '</h3>
				<p><code>[rating stars=<em>int</em> type=<em>str</em> name=<em>str</em> desc=<em>str</em> author=<em>str</em> cuisine=<em>str</em> keywords=<em>str</em> prep=<em>str</em> cook=<em>str</em> yield=<em>str</em> cat=<em>str</em> cal=<em>str</em> ing=<em>str</em> steps=<em>str</em>]</code></p>
				<dl>
					<dt>rating</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Initiates the shortcode.', 'fsrs' ) . '</dd>
					<dt>stars</dt>
					<dd><em>(' . __( 'integer', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'The quantity of whole stars to display. Must end in a single decimal place (.0 or .5).', 'fsrs' ) . '</dd>
					<dt>type</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Review type. Must be &ldquo;Recipe&rdquo; (case-sensitive),', 'fsrs' ) . ' <abbr>i.e.</abbr>, <code>type="Recipe"</code>.</dd>
					<dt>name</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe name. If set to &ldquo;title&rdquo; (case-sensitive), the snippet will use the post title.', 'fsrs' ) . '</dd>
					<dt>desc</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe description. If set to &ldquo;excerpt&rdquo; (case-sensitive), the snippet will use the post excerpt.', 'fsrs' ) . '</dd>
					<dt>auth</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe author.', 'fsrs' ) . '</dd>
					<dt>cuisine</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe cuisine.', 'fsrs' ) . '</dd>
					<dt>keywords</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe keywords as comma-separated list.', 'fsrs' ) . '</dd>
					<dt>prep</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe preparation time in', 'fsrs' ) . ' <a href="https://en.wikipedia.org/wiki/ISO_8601#Durations"><abbr>ISO</abbr> 8601 ' . __( 'format.', 'fsrs' ) . '</a></dd>
					<dt>cook</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe cooking time in', 'fsrs' ) . ' <a href="https://en.wikipedia.org/wiki/ISO_8601#Durations"><abbr>ISO</abbr> 8601 ' . __( 'format.', 'fsrs' ) . '</a></dd>
					<dt>yield</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe yield.', 'fsrs' ) . '</dd>
					<dt>cat</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe category.', 'fsrs' ) . '</dd>
					<dt>cal</dt>
					<dd><em>(' . __( 'int', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe calories.', 'fsrs' ) . '</dd>
					<dt>ing</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe ingredients as comma-separated list.', 'fsrs' ) . '</dd>
					<dt>steps</dt>
					<dd><em>(' . __( 'string', 'fsrs' ) . ')</em> <em>(' . __( 'Required', 'fsrs' ) . ')</em> ' . __( 'Recipe steps.', 'fsrs' ) . '</dd>
				</dl>
			</section>
			<hr/>
			<section>
				<h3>Example</h3>
				<p>Shortcode:</p>
				<pre><code>[rating stars="3.5" type="Recipe" name="title" desc="excerpt" auth="Joe Schmoe" cuisine="American" keywords="weiner, frankfurter, hot dog, awesome" prep="PT10M" cook="PT20M" yield="6 servings" cat="entrée" ing="6 Nathan’s All Beef Skinless Franks, 6 hot dog buns, 2 TBSP unsalted butter, ¼ cup stone-ground mustard, ¼ sweet pickle relish, ¼ minced white onions, ½ cup chili (see recipe)" cal="400 per serving" steps="Do this. Do that. Do the other thing. Do another thing. Do yet something else."]</code></pre>
				<pre><code>{
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
  "name": "Review: Joe Schmoe’s “World’s Greatest Hot Dog” Recipe",
  "description": "Spicy jalapeno bacon ipsum dolor amet aliqua enim turducken pastrami est meatball fugiat pork loin ribeye ham. Tongue meatball ea velit, shoulder boudin shankle eiusmod non flank sirloin venison. Jerky proident short loin bresaola. Sint aliqua pork qui landjaeger.",
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
    "6 Nathan’s All Beef Skinless Franks, 6 hot dog buns, 2 TBSP unsalted butter, ¼ cup stone-ground mustard, ¼ sweet pickle relish, ¼ minced white onions, ½ cup chili (see recipe)"
    ],
  "recipeInstructions": [
    "Do this. Do that. Do the other thing. Do another thing. Do yet something else."
    ],
  "review": {
    "@type": "Review",
    "author": "John Doe",
    "url":  "https://mywebsite.com/johndoe/",
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
  "url": "https://mywebsite.com/path/to/review/post/"
}</code></pre>
			</section>
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
                'id'          => 'starsmin',
                'label'       => __( 'Minimum Rating', 'fsrs' ),
                'description' => __( 'Change the minimum rating. The default is', 'fsrs' ) . ' &frac12;.',
                'type'        => 'range',
                'step'        => '0.5',
                'min'         => '0.0',
                'max'         => '1',
                'default'     => '0.5',
            ),
                array(
                'id'          => 'starsmax',
                'label'       => __( 'Maximum Rating', 'fsrs' ),
                'description' => __( 'Change the minimum rating. The default is', 'fsrs' ) . ' 5.',
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
                'default'     => '',
            ),
                array(
                'id'          => 'textcolor',
                'label'       => __( 'Text Color', 'fsrs' ),
                'description' => __( 'Change the numeric text color.   The text inherits its color by default.', 'fsrs' ),
                'type'        => 'color',
                'default'     => '',
            ),
                array(
                'id'          => 'size',
                'label'       => __( 'Star Size', 'fsrs' ),
                'description' => __( 'Change the star icon and text rating size. The icons and text inherit their color by default.', 'fsrs' ),
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
            $html .= '<h2><i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i> ' . __( 'Five-Star Ratings Shortcode Settings', 'fsrs' ) . ' <i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i></h2>' . "\n";
        } else {
            $html .= '<h2><i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i> ' . __( 'Five-Star Ratings Shortcode Documentation', 'fsrs' ) . ' <i class="fsrs-fas fa-fw fa-star wp-admin-lite-blue"></i></h2>' . "\n";
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