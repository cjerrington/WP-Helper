<?php
/*
Plugin Name: WordPress Helper Master
Plugin Script: wp-helper-master.php
Plugin URI: https://github.com/cjerrington/WP-Helper/
Description: Removes and speeds up WordPress. 
Version: 1.4.3
Author: Clayton Errington
Author URI: http://claytonerrington.com
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
GitHub Plugin URI: https://github.com/cjerrington/WP-Helper

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Online: http://www.gnu.org/licenses/gpl.txt
*/

// ------------------

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once(dirname(__FILE__).'/class.php');

// ++++++++++++++++ WP CLEANUP +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
 	
if (get_option( 'wphm_cononical_links' )){remove_action ('wp_head', 'rsd_link');}
if (get_option( 'wphm_manifest_file' )){remove_action( 'wp_head', 'wlwmanifest_link');}
if (get_option( 'wphm_short_link' )){remove_action( 'wp_head', 'wp_shortlink_wp_head');}
if (get_option( 'wphm_wp_generator' )){remove_action( 'wp_head', 'wp_generator');}
if (get_option( 'wphm_adjacent_posts' )){
	remove_action( 'wphm_wp_head', 'adjacent_posts_rel_link_wp_head' );
	remove_action( 'wphm_wp_head', 'adjacent_posts_rel_link');
}
if (get_option( 'wphm_index_rel_link' )){remove_action( 'wp_head', 'index_rel_link' );}
if (get_option( 'wphm_post_rel_link' )){
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
}
if (get_option( 'wphm_feed_links' )){
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
}
if (get_option( 'wphm_cononical_links' )){remove_action( 'wp_head', 'rel_canonical');}

function wphm_wphelper_remove_version() {
return '';
}
if (get_option( 'wphm_version' )){add_filter('the_generator', 'wphm_wphelper_remove_version');}


function wphm_disable_feeds() {
wp_die( __('No feed available, please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
}
if (get_option( 'wphm_disable_feed' )){
	add_action('do_feed', 'wphm_isable_feeds', 1);
	add_action('do_feed_rdf', 'wphm_disable_feeds', 1);
	add_action('do_feed_atom', 'wphm_disable_feeds', 1);
	add_action('do_feed_rss', 'wphm_disable_feeds', 1);
	add_action('do_feed_rss2', 'wphm_disable_feeds', 1); 
}

// ++++++++++++++++ PERFORMANCE +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
if(extension_loaded("zlib") && (ini_get("output_handler") != "ob_gzhandler"))
if(get_option( 'wphm_GZip' )){add_action('wp', create_function('', '@ob_end_clean();@ini_set("zlib.output_compression", 1);'));}

function wphm_remove_script_version( $src ){
	$parts = explode( '?', $src );
	return $parts[0];
}
if (get_option ( 'wphm_query_version' )){
	add_filter( 'script_loader_src', 'wphm_remove_script_version', 15, 1 );
	add_filter( 'style_loader_src', 'wphm_remove_script_version', 15, 1 );
}

if (get_option( 'wphm_xmlrpc' )){remove_filter('atom_service_url','atom_service_url_filter');}

if(get_option ( 'wphm_autoformat' )){
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_excerpt', 'wpautop' );
}

// ++++++++++++++++ SECURITY MODIFICATIONS ++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// Hide login form error messages
if(get_option( 'wphm_login' )){add_filter('login_errors',create_function('$a', "return 'Error';"));}

// Remove WordPress version number
function wphm_remove_wp_version() { return ''; }
if(get_option( 'wphm_wp_version_number' )){add_filter('the_generator', 'wphm_remove_wp_version');}

// Disable XMLRPC
if(get_option( 'wphm_disable_XMLRPC' )){add_filter('xmlrpc_enabled', '__return_false'); }

// Set the maximum number of post revisions unless the constant is already set in wp-config.php
if (get_option( 'wphm_post_revisions' )) {
	$post_revisions = get_option( 'wphm_post_revisions' );
	if (!defined('WP_POST_REVISIONS')) define('WP_POST_REVISIONS', $post_revisions);
}

// Disable Auto Linking of URLs in comments
if (get_option( 'wphm_auto_linking' )){remove_filter('comment_text', 'make_clickable', 9);}

// Disable self-ping
function wphm_no_self_ping( &$links ) {
	$home = get_option( 'home' );
	foreach ( $links as $l => $link )
	if ( 0 === strpos( $link, $home ) )
	unset($links[$l]);
}
if(get_option( 'wphm_self_ping' )){add_action( 'pre_ping', 'wphm_no_self_ping' ); }

//remove lost password link
function wphm_remove_lostpassword_text ( $text ) {
     if ($text == 'Lost your password?'){$text = '';}
        return $text;
     }
if(get_option( 'wphm_lost_pass' )){add_filter( 'gettext', 'wphm_remove_lostpassword_text' );}

// ++++++++++++++++ ADMIN MODIFICATIONS +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //

// Change the default WordPress greeting in Admin
function wphm_replace_howdy( $wp_admin_bar ) {
	$my_account=$wp_admin_bar->get_node('my-account');
	if (get_option( 'wphm_greeting' )){
		$title = get_option( 'wphm_greeting' );
	}else{
		$title = 'Welcome, ';
	}
	$newtitle = str_replace( 'Howdy,', $title, $my_account->title );
	$wp_admin_bar->add_node( array(
	'id' => 'my-account',
	'title' => $newtitle,
	) );
}
if (get_option( 'wphm_greeting' )){add_filter( 'admin_bar_menu', 'wphm_replace_howdy', 25 );}

// Add new Admin menu item "All Settings"
function wphm_all_settings_link() {
	add_options_page(__('All Settings'), __('All Settings'), 'administrator', 'options.php');
}
if (get_option( 'wphm_all_settings' )){add_action('admin_menu', 'wphm_all_settings_link');}


// Add custom dashboard setting 
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
	function my_custom_dashboard_widgets() {
	global $wp_meta_boxes; 
	wp_add_dashboard_widget('custom_help_widget', 'WP Helper Support', 'wphm_custom_dashboard_help');
}

function wphm_custom_dashboard_help() {
	echo '<p>Welcome to the Dashboard! Need help? Contact the developer at <a href="mailto:me@claytonerrington.com">me@claytonerrington.com</a>. 
	<br />For more information visit: <a href="http://claytonerrington.com/" target="_blank">His website</a></p>
	<p>If you like this plugin, send him some money to keep it up. Buy him a coffee to show your support!</p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<table>
<tr><td><input type="hidden" name="on0" value="Donations">Donations</td></tr><tr><td><select name="os0">
	<option value="1)">1) $5.00 USD</option>
	<option value="2)">2) $10.00 USD</option>
	<option value="3)">3) $15.00 USD</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
';

}

$settings = new WP_Helper_Master_Settings( __FILE__ );

?>
