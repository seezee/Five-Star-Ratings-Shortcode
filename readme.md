=== Five-Star Ratings Shortcode ===  
Contributors: seezee  
Donate link: https://messengerwebdesign.com/donate  
Author URI: https://github.com/seezee  
Plugin URI: https://wordpress.org/plugins/five-star-ratings-shortcode/  
Tags:  wordpress, plugin, ratings, stars, icon, shortcode, accessible 
Requires at least: 4.6.0  
Tested up to: 5.6  
Requires PHP: 7.0  
Stable tag: 1.2.9  
License: GNUv3 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html  
GitHub Plugin URI: seezee/Five-Star-Ratings-Plugin  

== Description ==

Add accessible, attractive 5-star ratings anywhere on your site with a simple shortcode. The plugin uses Font Awesome icons via their SVG + JavaScript method.

[![WP compatibility](https://plugintests.com/plugins/wporg/five-star-ratings-shortcode/wp-badge.svg)](https://plugintests.com/plugins/wporg/five-star-ratings-shortcode/latest)
[![PHP compatibility](https://plugintests.com/plugins/wporg/five-star-ratings-shortcode/php-badge.svg)](https://plugintests.com/plugins/wporg/five-star-ratings-shortcode/latest)

== Acknowledgement ==

This plugin is based on [Hugh Lashbrooke’s Starter Plugin](https://github.com/hlashbrooke/WordPress-Plugin-Template), a robust and GPL-licensed code template for creating a standards-compliant WordPress plugin.

== PRO only features ==

* Google Rich Snippets for Products, Restaurants, & Recipes
* Custom icon sizes
* Custom icon and text colors
* Choice of HTML `<i>` or `<span>` elements in HTML output
* Change the number of stars (from 3 – 10)
* NEW in v1.1.2: Shortcode generator

== Installation ==

### USING THE WORDPRESS DASHBOARD
1. Navigate to “Add New” in the plugins dashboard
2. Search for “Five-Star Ratings Shortcode”
3. Click “Install Now”
4. Activate the plugin on the Plugin dashboard
5. The FREE plugin has no settings. PRO users: Go to Settings -> Five-Star Ratings Shortcode if you want to customize the shortcode output.

### UPLOADING IN WORDPRESS DASHBOARD
1. Click the download button on this and save “five-star-ratings-plugin.zip” to your computer
2. Navigate to “Add New” in the plugins dashboard
3. Navigate to the “Upload” area
4. Select “five-star-ratings-plugin.zip” from your computer
5. Click “Install Now”
6. Activate the plugin in the Plugin dashboard
7. The FREE plugin has no settings. PRO users: Go to Settings -> Five-Star Ratings Shortcode if you want to customize the shortcode output.

### USING FTP
1. Download the Five-Star Ratings Shortcode ZIP file
2. Extract the Five-Star Ratings Shortcode ZIP file to your computer
3. Upload the “five-star-ratings-plugin” directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard
5. The FREE plugin has no settings. PRO users: Go to Settings -> Five-Star Ratings Shortcode if you want to customize the shortcode output.

### UPGRADING TO FIVE-STAR RATINGS SHORTCODE PRO
1. Go to Settings -> Five-Star Ratings Shortcode -> Upgrade
2. Fill out the payment form and submit
3. Your license key will automatically be entered

### DOWNLOAD FROM GITHUB
1. Download the plugin via [https://github.com/seezee/Five-Star-Ratings-Shortcode](https://github.com/seezee/Five-Star-Ratings-Shortcode)
2. Follow the directions for using FTP

== Usage Examples ==

[rating stars="0.5"] (Displays ½ star out of 5)  
[rating stars="3.0"] (Displays 3 stars out of 5)  
[rating stars="2.5"] (Displays 2½ stars out of 5)  
[rating stars="4.0"] (Displays 4 stars out of 5)  
[rating stars="5.5"] (Incorrect usage but will display 5 stars out of 5)  

In the 2nd example, the raw output will be like this before processing:  
```html
<span class="fsrs">
  <span class="fsrs-stars">
    <i class="fsrs-fas fa-fw fa-star"></i>
    <i class="fsrs-fas fa-fw fa-star"></i>
    <i class="fsrs-fas fa-fw fa-star-half-alt"></i>
    <i class="fsrs-far fa-fw fa-star"></i>
    <i class="fsrs-far fa-fw fa-star"></i>
  </span>
  <span class="hide fsrs-text fsrs-text__hidden" aria-hidden="false">2.5 out of 5</span> 
  <span class="lining fsrs-text fsrs-text__visible" aria-hidden="true">2.5</span>
</span>
```  
PRO users can change the minimum and maximum ratings on the settings page.

== Frequently Asked Questions ==

### What is the plugin for?

This plugin adds accessible, attractive 5-star ratings anywhere on your site with a simple shortcode. The plugin uses Font Awesome icons via their SVG + JavaScript method.

### How may I help improve this plugin?

I’d love to hear your feedback. In particular, tell me about your experience configuring the plugin. Are the instructions clear? Do I need to reword them? Did I leave out something crucial? You get the drift.

### I’d like to do more

I’m looking for collaborators to improve the code. If you are an experienced Wordpress programmer, hit me up!

### I’d like to do even more

Feel free to send a donation to my [Paypal account](https://paypal.me/messengerwebdesign?locale.x=en_US). Or buy me a beer if you’re in town.

== Translations ==

* English: Default language, always included

Would you like to help translate Five-Star Ratings Shortcode into your own language? [You can do that here!](https://translate.wordpress.org/projects/wp-plugins/five-star-ratings-shortcode)

== Dependencies ==

This plugin includes these third-party libraries in its package.

* [Font Awesome 5](https://github.com/FortAwesome/Font-Awesome): v5.11.2

== Changelog ==

= 1.2.9 =
* 2021-01-18
* BUGFIX: Replace incorrect variable $link with $url in function checklink()

= 1.2.8 =
* 2021-01-07
* BUGFIX: Revert strict comparison operators to loose-typing

= 1.2.7 =
2021-01-07
Tested up to WordPress 5.6
BUGFIX: fix undefined variable

= 1.2.6 =
* 2021-01-07
* Add local fallback for external scripts
* Some code formatting cleanup to meet WordPress coding standard, but more is needed
* Load Farbtastic script in footer
* SECURITY: Sanitize $textsize & $textcolor on output
* SECURITY: Add nonce to form reset for to prevent CSFR attacks

= 1.2.5 =
* 2020-10-08
* Tested up to 5.5.3
* Update FREEMIUS SDK to v.2.4.1
* Use Dashicons coffee glyph instead of FontAwesome coffee glyph in plugin meta

= 1.2.4 =
* 2020-09-29
* BUGFIX fix missing borders on &lt;details&gt; element

= 1.2.3 =
* 2020-09-29
* SECURITY FIX: escape or sanitize all translatable strings

= 1.2.2 =
* 2020-09-22
* Improved microcopy
* Fix some i18n errors

= 1.2.1 =
* 2020-09-21
* (PRO only) Improved label and validation message for ratings field in shortcode generator

= 1.2.0 =
* 2020-09-20
* (PRO only) BREAKING CHANGES: Recipe Rich Snippets require new syntax
* (PRO only) BUGFIX: fix missing curly brace in Recipe Rich Snippets output
* (PRO only) Improve Recipe Rich Snippets syntax
* (PRO only) Now supports guided recipes

= 1.1.4 =
* 2020-09-17
* Fix incorrect translator notes
* Improve ARIA text in output
* (PRO only) Better currency regex (allow period (.) as 1000s separater & comma (,) as decimal separater)

= 1.1.3 =
* 2020-09-14
* Replace $pagenow global with $hook check wherever appropriate
* Complete overhaul of i18n internationalization
* Sanitize links in internationalized strings
* Update .pot file
* Update README

= 1.1.2 =
* 2020-09-13
* BUGFIX: 1.1.2 reintroduced error of scripts loading outside plugin settings page, conflicting with other plugins; this update fixes that while ensuring scripts still load when needed

= 1.1.1 =
* 2020-09-13
* BUGFIX: fix version check
* BUGFIX: fix missing admin scripts
* BUGFIX: fix missing padding on details:summary
* New PRO feature: shortcode generator
* Updated UX & CSS
* Updated usage examples

= 1.1.0 =
* There is no v1.1.0 :-(

= 1.0.22 =
* 2020-09-04
* Tested up to WordPress 5.5.1

= 1.0.21 =
* 2020-08-01
* BUGFIX: Load admin scripts and styles correctly to fix critical conflict with other plugins

= 1.0.20 =
* 2020-07-23
* Integrate auto-deactivation of FREE version when upgrading to PRO

= 1.0.19 =
* 2020-07-20
* BUGFIX: Fix fatal error on upgrade: cannot redeclare fsrs_fs_uninstall_cleanup()

= 1.0.18 =
* 2020-06-25
* BUGFIX: Fix coffee cup icon not rendering in plugin meta

= 1.0.17 =
* 2020-06-16
* Tested up to WordPress 5.4.2
* BUGFIX: Fix premium code rendering in free plugin

= 1.0.16 =
* 2020-04-30
* Tested up to WordPress 5.4.1

= 1.0.15 =
* 2020-04-01
* Tested up to WordPress 5.4
* BUGFIX: Fix use of "this" keyword outside object context
* BUGFIX (PRO ONLY): Replace borked color picker with native HTML color picker
* Remove surrounding underscores from constant names per WordPress coding standards

= 1.0.14 =
* 2019-12-17
* Use get_bloginfo( 'wpurl' ) instead of get_bloginfo( 'url' )

= 1.0.13 =
* 2019-12-16
* New PRO feature: Google Rich Snippets for products, restaurants, & recipes
* Add debugging on PHP contants conflict

= 1.0.12 =
* 2019-12-10
* Correct Plugin URI in README
* Correct link to Github repo in README

= 1.0.11 =
* 2019-12-10
* BUGFIX: Fix stars showing zero if rating is 0.5
* Improve usage examples

= 1.0.10 =
* 2019-12-10
* Update usage examples in readme

= 1.0.9 =
* 2019-12-09
* BUGFIX: Fix incorrect text output if user enters x.5 where x is the maximum number of stars

= 1.0.8 =
* 2019-12-09
* Change SCRIPT_DEBUG CORS policy check
* Fix missing translation string
* Change PRO version slug

= 1.0.7 =
* 2019-12-09
* BUGFIX: Fix CORS policy error

= 1.0.6 =
* 2019-12-09
* Update .POT file.

= 1.0.5 =
* 2019-12-09
* Fix error in documentation.

= 1.0.4 =
* 2019-12-09
* Fix error in documentation.

= 1.0.3 =
* 2019-12-09
* BUGFIX: Fix options not displaying for PRO plugin
* BUGFIX: Fix incorrect class in ratings text output
* Refactor shortcode: remove "half" attribute & use float instead
* Remove unused admin CSS rules

= 1.0.2 =
* 2019-12-08
* Correct plugin tags
* Correct badge links in readme.md

= 1.0.1 =
* 2019-12-08
* BUGFIX: Fix incorrect _VERSION_ constant; should be FSRS_VERSION

= 1.0.0 =
* 2019-12-06
* Initial release

== Upgrade Notice ==

= 1.2.9 =
* 2021-01-18
* BUGFIX: Replace incorrect variable $link with $url in function checklink()

[//]: # (*********************************************************************            ***Do not copy/paste to readme.txt! You'll mess up the formatting!***            *********************************************************************)
[//]: # (REMEMBER to update the Stable tag and copy all changes to readme.txt!)
[//]: # (REMEMBER to update the Version Number in all files that contain it!)
