<?php
/**
 * Head class file.
 *
 * @package Five-Star Ratings Shortcode/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Sorry, you are not allowed to access this page directly.' );
}

	/**
	 * Enqueue scripts & styles.
	 * Get shortcode values and output them in Rich Snippets JSON.
	 */
class Five_Star_Ratings_Shortcode_Head {

	/**
	 * The single instance of Five_Star_Ratings_Shortcode_Head.
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
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Suffix for Javascripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 */
	public function __construct() {
		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min'; // Use minified script.
	}

	/**
	 * Generate CSS and JS to be loaded in <head>.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function css() {
		$plugin_url = plugin_dir_url( __DIR__ );

		$arr = array(); // Use this with wp_kses. Don't allow any HTML.

		// All options prefixed with FSRS_BASE value; see fsrs.php constants.
		if ( get_option( FSRS_BASE . 'starcolor' ) !== null ) {
			$color = get_option( FSRS_BASE . 'starcolor' );
		} else {
			$color = '';
		}

		if ( get_option( FSRS_BASE . 'textcolor' ) !== null ) {
			$textcolor = get_option( FSRS_BASE . 'textcolor' );
		} else {
			$textcolor = '';
		}

		if ( get_option( FSRS_BASE . 'size' ) !== null ) {
			$size = get_option( FSRS_BASE . 'size' );
		} else {
			$size = '';
		}

		$textsize = '';
		switch ( $size ) {
			case 'fa-xs':
				$textsize = '.75';
				break;
			case 'fa-sm':
				$textsize = '.875';

				break;
			case 'fa-lg':
				$textsize = '1.33';

				break;
			case 'fa-2x':
				$textsize = '2';

				break;
			case 'fa-3x':
				$textsize = '3';

				break;
			case 'fa-4x':
				$textsize = '4';

				break;
			case 'fa-5x':
				$textsize = '5';

				break;
			case 'fa-6x':
				$textsize = '6';

				break;
			case 'fa-7x':
				$textsize = '7';

				break;
			case 'fa-8x':
				$textsize = '8';

				break;
			case 'fa-9x':
				$textsize = '9';

				break;
			case 'fa-10x':
				$textsize = '10';

				break;
			default:
				$textsize = '1';
		}
		if ( ( ! is_null( $textsize ) ) && ( '1' !== $textsize ) ) {
			// Script to inline the font-size style for the visible text.
			echo '<script>
	window.addEventListener("load", function() {
		var el = document.querySelectorAll(".fsrs-text__visible");
		for ( var i = 0; i < el.length; i ++ ) {
			var style = window.getComputedStyle(el[i], null).getPropertyValue("font-size");
			var fontSize = parseFloat(style);
			el[i].style.fontSize = (fontSize * ' . wp_kses( $textsize, $arr ) . ') + "px";
		}
	})
	</script>';
		}
		$html = '
<style type="text/css">';
		if ( ! is_null( $color ) ) {
			$html .= '.fsrs-stars{color:' . wp_kses( $color, $arr ) . '}';
		}
		if ( ! is_null( $textcolor ) ) {
			$html .= '.fsrs-text{color:' . wp_kses( $textcolor, $arr ) . '}';
		}
		$html .= '</style>';
		echo $html; // phpcs:ignore
	}

	/**
	 *
	 * Generate JSON to be loaded in <head>.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function json() {

		$arr = array();

		global $post;

		$result = array();
		// get shortcode regex pattern WordPress function.
		$pattern = get_shortcode_regex( array( 'rating' ) );

		if ( isset( $post->post_content ) && $post->post_content ) {
			if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches ) ) {
				$keys   = array();
				$result = array();
				foreach ( $matches[0] as $key => $value ) {
					$out = $matches[3][ $key ];
					// $matches[3] return the shortcode attribute as string.
					// replace double quotation mark + space with '&' for parse_str() function.
					// $get = str_replace(" ", "&" , $matches[3][$key] );
					$get = str_replace( '" ', '"&', $out );
					parse_str( $get, $output );

					// get all shortcode attribute keys.
					$keys     = array_unique( array_merge( $keys, array_keys( $output ) ) );
					$result[] = $output;

				}

				if ( $keys && $result ) {
					// Loop the result array and add the missing shortcode attribute key.
					foreach ( $result as $key => $value ) {
						// Loop the shortcode attribute key.
						foreach ( $keys as $attr_key ) {
							$result[ $key ][ $attr_key ] = isset( $result[ $key ][ $attr_key ] ) ? $result[ $key ][ $attr_key ] : null;
						}
						// sort the array key.
						ksort( $result[ $key ] );
					}
				}

				// print_r ( $result ); // Debugging.

				$rating_val = implode(
					', ',
					array_map(
						function ( $stars ) {
								$stars['stars'] = str_replace( '"', '', $stars['stars'] );
								return $stars['stars'];
						},
						$result
					)
				); // Get the value from the multidimensional array.

				$review_type = implode(
					', ',
					array_map(
						function ( $type ) {
							if ( array_key_exists( 'type', $type ) ) {
									return $type['type'];
							}
						},
						$result
					)
				);

				$name = implode(
					', ',
					array_map(
						function ( $name ) {
							if ( array_key_exists( 'name', $name ) ) {
								if ( '"title"' === $name['name'] ) {
									$title = get_the_title();
									return $title;
								} else {
									$name['name'] = str_replace( '"', '', $name['name'] );
									return $name['name'];
								}
							}
						},
						$result
					)
				);

				$desc = implode(
					', ',
					array_map(
						function ( $desc ) {
							if ( array_key_exists( 'desc', $desc ) ) {
								if ( '"excerpt"' === $desc['desc'] ) {
									$desc = get_the_excerpt();
									return $desc;
								} else {
									$desc['desc'] = str_replace( '"', '', $desc['desc'] );
									return $desc['desc'];
								}
							}
						},
						$result
					)
				);

				$brand = implode(
					', ',
					array_map(
						function ( $brand ) {
							if ( array_key_exists( 'brand', $brand ) ) {
								$brand['brand'] = str_replace( '"', '', $brand['brand'] );
									return $brand['brand'];
							}
						},
						$result
					)
				);

				$mpn = implode(
					', ',
					array_map(
						function ( $mpn ) {
							if ( array_key_exists( 'mpn', $mpn ) ) {
								$mpn['mpn'] = str_replace( '"', '', $mpn['mpn'] );
									return $mpn['mpn'];
							}
						},
						$result
					)
				);

				$price = implode(
					', ',
					array_map(
						function ( $price ) {
							if ( array_key_exists( 'price', $price ) ) {
									$price['price'] = str_replace( '"', '', $price['price'] );
									return $price['price'];
							}
						},
						$result
					)
				);

				$cur = implode(
					', ',
					array_map(
						function ( $cur ) {
							if ( array_key_exists( 'cur', $cur ) ) {
									$cur['cur'] = str_replace( '"', '', $cur['cur'] );
									return $cur['cur'];
							}
						},
						$result
					)
				);

				$addr = implode(
					', ',
					array_map(
						function ( $addr ) {
							if ( array_key_exists( 'addr', $addr ) ) {
									$addr['addr'] = str_replace( '"', '', $addr['addr'] );
									return $addr['addr'];
							}
						},
						$result
					)
				);

				$locale = implode(
					', ',
					array_map(
						function ( $locale ) {
							if ( array_key_exists( 'locale', $locale ) ) {
									$locale['locale'] = str_replace( '"', '', $locale['locale'] );
									return $locale['locale'];
							}
						},
						$result
					)
				);

				$region = implode(
					', ',
					array_map(
						function ( $region ) {
							if ( array_key_exists( 'region', $region ) ) {
									$region['region'] = str_replace( '"', '', $region['region'] );
									return $region['region'];
							}
						},
						$result
					)
				);

				$postal = implode(
					', ',
					array_map(
						function ( $postal ) {
							if ( array_key_exists( 'postal', $postal ) ) {
									$postal['postal'] = str_replace( '"', '', $postal['postal'] );
									return $postal['postal'];
							}
						},
						$result
					)
				);

				$country = implode(
					', ',
					array_map(
						function ( $country ) {
							if ( array_key_exists( 'country', $country ) ) {
									$country['country'] = str_replace( '"', '', $country['country'] );
									return $country['country'];
							}
						},
						$result
					)
				);

				$tel = implode(
					', ',
					array_map(
						function ( $tel ) {
							if ( array_key_exists( 'tel', $tel ) ) {
									$tel['tel'] = str_replace( '"', '', $tel['tel'] );
									return $tel['tel'];
							}
						},
						$result
					)
				);

				$cuisine = implode(
					', ',
					array_map(
						function ( $cuisine ) {
							if ( array_key_exists( 'cuisine', $cuisine ) ) {
									$cuisine['cuisine'] = str_replace( '"', '', $cuisine['cuisine'] );
									return $cuisine['cuisine'];
							}
						},
						$result
					)
				);

				$auth = implode(
					', ',
					array_map(
						function ( $auth ) {
							if ( array_key_exists( 'author', $auth ) ) {
									$auth['author'] = str_replace( '"', '', $auth['author'] );
									return $auth['author'];
							}
						},
						$result
					)
				);

				$keywords = implode(
					', ',
					array_map(
						function ( $keywords ) {
							if ( array_key_exists( 'keywords', $keywords ) ) {
									$keywords['keywords'] = str_replace( '"', '', $keywords['keywords'] );
									return $keywords['keywords'];
							}
						},
						$result
					)
				);

				$prep = implode(
					', ',
					array_map(
						function ( $prep ) {
							if ( array_key_exists( 'prep', $prep ) ) {
									$prep['prep'] = str_replace( '"', '', $prep['prep'] );
									return $prep['prep'];
							}
						},
						$result
					)
				);

				$cook = implode(
					', ',
					array_map(
						function ( $cook ) {
							if ( array_key_exists( 'cook', $cook ) ) {
									$cook['cook'] = str_replace( '"', '', $cook['cook'] );
									return $cook['cook'];
							}
						},
						$result
					)
				);

				$yield = implode(
					', ',
					array_map(
						function ( $yield ) {
							if ( array_key_exists( 'yield', $yield ) ) {
									$yield['yield'] = str_replace( '"', '', $yield['yield'] );
									return $yield['yield'];
							}
						},
						$result
					)
				);

				$cat = implode(
					', ',
					array_map(
						function ( $cat ) {
							if ( array_key_exists( 'cat', $cat ) ) {
									$cat['cat'] = str_replace( '"', '', $cat['cat'] );
									return $cat['cat'];
							}
						},
						$result
					)
				);

				$cal = implode(
					', ',
					array_map(
						function ( $cal ) {
							if ( array_key_exists( 'cal', $cal ) ) {
									$cal['cal'] = str_replace( '"', '', $cal['cal'] );
									return $cal['cal'];
							}
						},
						$result
					)
				);

				$ing = implode(
					', ',
					array_map(
						function ( $ing ) {
							if ( array_key_exists( 'ing', $ing ) ) {
									$ing['ing'] = str_replace( '"', '', $ing['ing'] );
									$ing['ing'] = str_replace( ', ', "\",\r\n      \"", $ing['ing'] );
									return $ing['ing'];
							}
						},
						$result
					)
				);

				$steps = implode(
					', ',
					array_map(
						function ( $steps ) {
							if ( array_key_exists( 'steps', $steps ) ) {
									$steps['steps'] = str_replace( '"', '', $steps['steps'] );
									$steps['steps'] = preg_replace( '/({| {)/', "\"\r\n      },\r\n      {\r\n      \"@type\": \"HowToStep\",\r\n      \"name\": \"", $steps['steps'] ); // Split string on commas, add Rich Snippets required markup, and add line breaks and spaces. Must be enclosed in double-quotation marks or line breaks will be ignored.
									$steps['steps'] = preg_replace( '/} /', "\",\r\n      \"text\": \"", $steps['steps'] ); // Split string on closing curly braces, add Rich Snippets required markup, and add line breaks and spaces. See note above regarding double-quotation marks. This preg_replace() introduces some redundant markup above the first item, which we remove with another preg_replace() below.
									return $steps['steps'];
							}
						},
						$result
					)
				);
			}
		}

		global $_wp_additional_image_sizes;

		$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' ); // Get the featured image.
		if ( $img ) {
			$img_url    = $img[0]; // Get the featured image URL.
			$img_width  = $img[1]; // Get the featured image width.
			$img_height = $img[2]; // Get the featured image height.
		} else {
			$img_url    = '';
			$img_width  = '';
			$img_height = '';
		}

		if ( isset( $post->post_author ) && $post->post_author ) {
			$author_id = $post->post_author; // Get the reviewer's ID.
		} else {
			$author_id = '';
		}

		$author_name = get_the_author_meta( 'display_name', $author_id );
		// Map the reviewer's ID to the reviewer's display name.
		$author_link = get_author_posts_url( get_the_author_meta( 'ID', $author_id ) );

		$date_pub = get_the_date( 'c' ); // Get the publication date.
		$post_url = get_permalink(); // Get the review URL.

		$org_name = get_bloginfo( 'name' ); // Get the blog name.
		$org_desc = get_bloginfo( 'description' );
		// Get the blog description.
		$org_url = get_bloginfo( 'wpurl' ); // Get the blog URL.

		if ( get_option( FSRS_BASE . 'starsMin' ) !== null ) {
			$rating_min = get_option( FSRS_BASE . 'starsMin' );
		} else {
			$rating_min = '0.5'; // Get the minimum rating.
		}

		if ( get_option( FSRS_BASE . 'starsMax' ) !== null ) {
			$rating_max = get_option( FSRS_BASE . 'starsMax' );
		} else {
			$rating_max = '5'; // Get the maximum rating.
		}

		if ( isset( $review_type ) && ( $review_type ) !== '' ) {
			switch ( $review_type ) {
				case '"Product"':
					$html = '
		<script type="application/ld+json">
			{
			"@context": "http://schema.org",
			"@type": "Product",
			"mainEntityOfPage": {
				"@type": "WebPage",
				"@id": "' . esc_url( $post_url ) . '"
			},
			"image": {
				"@type": "ImageObject",
				"url": "' . esc_url( $img_url ) . '",
				"height": "' . wp_kses( $img_height, $arr ) . '",
				"width": "' . wp_kses( $img_width, $arr ) . '"
			},
			"name": "' . wp_kses( $name, $arr ) . '",
			"brand": "' . wp_kses( $brand, $arr ) . '",
			"mpn": "' . wp_kses( $mpn, $arr ) . '",
			"description": "' . wp_kses( $desc, $arr ) . '",
			"offers": {
				"@type": "Offer",
				"availability": "https://schema.org/InStock",
				"price": "' . wp_kses( $price, $arr ) . '",
				"priceCurrency": "' . wp_kses( $cur, $arr ) . '"
			},
			"review": {
				"@type": "Review",
				"author": {
					"@type": "Person",
					"name": "' . wp_kses( $author_name, $arr ) . '"
      	},
				"url":  "' . wp_kses( $author_link, $arr ) . '",
				"datePublished": "' . wp_kses( $date_pub, $arr ) . '",
				"publisher": {
				"@type": "Organization",
				"name": "' . wp_kses( $org_name, $arr ) . '",
				"description": "' . wp_kses( $org_desc, $arr ) . '",
				"url": "' . esc_url( $org_url ) . '"
				},
				"reviewRating": {
				"@type": "Rating",
				"ratingValue": "' . wp_kses( $rating_val, $arr ) . '",
				"bestRating": "' . wp_kses( $rating_max, $arr ) . '",
				"worstRating": "' . wp_kses( $rating_min, $arr ) . '"
				}
			},
			"url": "' . esc_url( $post_url ) . '"
			}
		</script>
		';
					break;
				case '"Restaurant"':
					$html = '
		<script type="application/ld+json">
			{
			"@context": "https://schema.org",
			"@type": "Restaurant",
			"mainEntityOfPage": {
				"@type": "WebPage",
				"@id": "' . esc_url( $post_url ) . '"
			},
			"image": {
				"@type": "ImageObject",
				"url": "' . esc_url( $img_url ) . '",
				"height": "' . wp_kses( $img_height, $arr ) . '",
				"width": "' . wp_kses( $img_width, $arr ) . '"
			},
			"name": "' . wp_kses( $name, $arr ) . '",
			"description": "' . wp_kses( $desc, $arr ) . '",
			"address": {
				"@type": "PostalAddress",
				"streetAddress": "' . wp_kses( $addr, $arr ) . '",
				"addressLocality": "' . wp_kses( $locale, $arr ) . '",
				"addressRegion": "' . wp_kses( $region, $arr ) . '",
				"postalCode": "' . wp_kses( $postal, $arr ) . '",
				"addressCountry": "' . wp_kses( $country, $arr ) . '"
			},
			"review": {
				"@type": "Review",
				"author": {
					"@type": "Person",
					"name": "' . wp_kses( $author_name, $arr ) . '"
      	},
				"url":  "' . wp_kses( $author_link, $arr ) . '",
				"datePublished": "' . wp_kses( $date_pub, $arr ) . '",
				"publisher": {
				"@type": "Organization",
				"name": "' . wp_kses( $org_name, $arr ) . '",
				"description": "' . wp_kses( $org_desc, $arr ) . '",
				"url": "' . esc_url( $org_url ) . '"
				},
				"reviewRating": {
				"@type": "Rating",
				"ratingValue": "' . wp_kses( $rating_val, $arr ) . '",
				"bestRating": "' . wp_kses( $rating_max, $arr ) . '",
				"worstRating": "' . wp_kses( $rating_min, $arr ) . '"
				}
			},
			"telephone": "' . wp_kses( $tel, $arr ) . '",
			"servesCuisine": "' . wp_kses( $cuisine, $arr ) . '",
			"priceRange": "' . wp_kses( $price, $arr ) . '",
			"url": "' . esc_url( $post_url ) . '"
			}
		</script>
		';
					break;
				case '"Recipe"':
					$html = '
	<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "Recipe",
			"mainEntityOfPage": {
				"@type": "WebPage",
				"@id": "' . esc_url( $post_url ) . '"
			},
			"image": {
				"@type": "ImageObject",
				"url": "' . esc_url( $img_url ) . '",
				"height": "' . wp_kses( $img_height, $arr ) . '",
				"width": "' . wp_kses( $img_width, $arr ) . '"
			},
			"name": "' . wp_kses( $name, $arr ) . '",
			"description": "' . wp_kses( $desc, $arr ) . '",
			"author": {
				"@type": "Person",
				"name": "' . wp_kses( $auth, $arr ) . '"
			},
			"keywords": "' . wp_kses( $keywords, $arr ) . '",
			"prepTime": "' . wp_kses( $prep, $arr ) . '",
			"cookTime": "' . wp_kses( $cook, $arr ) . '",
			"recipeYield": "' . wp_kses( $yield, $arr ) . '",
			"recipeCategory": "' . wp_kses( $cat, $arr ) . '",
			"recipeCuisine": "' . wp_kses( $cuisine, $arr ) . '",
			"nutrition": {
				"@type": "NutritionInformation",
				"calories": "' . wp_kses( $cal, $arr ) . '"
			},
			"recipeIngredient": [
				"' . wp_kses( $ing, $arr ) . '"
			],
			"recipeInstructions": [
				' . wp_kses( $steps, $arr ) . "\"\r\n      } // Extra markup needed after preg_replace().
			],
			\"review\": {
				\"@type\": \"Review\",
				\"author\": \"" . wp_kses( $author_name, $arr ) . '",
				"url":  "' . wp_kses( $author_link, $arr ) . '",
				"datePublished": "' . wp_kses( $date_pub, $arr ) . '",
				"publisher": {
					"@type": "Organization",
					"name": "' . wp_kses( $org_name, $arr ) . '",
					"description": "' . wp_kses( $org_desc, $arr ) . '",
					"url": "' . esc_url( $org_url ) . '"
				}
			},
			"reviewRating": {
				"@type": "Rating",
				"ratingValue": "' . wp_kses( $rating_val, $arr ) . '",
				"bestRating": "' . wp_kses( $rating_max, $arr ) . '",
				"worstRating": "' . wp_kses( $rating_min, $arr ) . '"
			},
			"url": "' . esc_url( $post_url ) . '"
		}
	</script>
				';
					$html = preg_replace( '/(\"recipeInstructions\"\: \[\R\s*\"\R\s*},)/m', '"recipeInstructions": [', $html ); // Remove redundant markup introduced by earlier preg_replace().
					break;
				default:
					$html = '';
			}
		}

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'rating' ) && isset( $review_type ) && ( $review_type ) !== '' ) { // Does the shortcode appear in the post?
			echo $html; // phpcs:ignore
		}
	}

	/**
	 * Place the CSS & JS in the head.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function head() {
		$this->css();
		$this->json();
	}

	/**
	 * Main Five_Star_Ratings_Shortcode_Head Instance
	 *
	 * Ensures only one instance of Five_Star_Ratings_Shortcode_Head is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Five_Star_Ratings_Shortcode()
	 * @param object $parent Object instance.
	 * @return Main Five_Star_Ratings_Shortcode_Head instance
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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Class_Five_Star_Ratings_Shortcode_Head is forbidden.', 'fsrs' ), esc_attr( FSRS_VERSION ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Class_Five_Star_Ratings_Shortcode_Head is forbidden.', 'fsrs' ), esc_attr( FSRS_VERSION ) );
	} // End __wakeup()

}

/**
* Place the JSON, script, and styles in the header.
*/

	$head = new Five_Star_Ratings_Shortcode_Head();

	add_action( 'wp_head', array( $head, 'head' ) );
