<?php
/*
Plugin Name: Snipplr Snippets
Plugin URI: http://nooshu.com/wordpress-plug-in-snipplr-snippets/
Description: Embed Snipplr snippets into your Wordpress posts. Update of Tyler Halls plug-in from 2006.
Author: Matt Hobbs (Tyler Hall + Jan Stepien)
Version: 1.0.0
Author URI: http://snipplr.com
Licence: The Snipplr WordPress plugin is licensed under a Creative Commons Attribution 2.5  License available at http://creativecommons.org/licenses/by/2.5/
*/

/*
Wordpress Plug-in Snipplr Snippets
Copyright (C) 2010 Matt Hobbs  (matt AT nooshu DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//Define the plug-in version
define('SNIPPLR_SNIPPETS_VERSION', '1.0.0');
define('SNIPS_DIR', dirname(__FILE__));

// We *must* turn off errors because of deprecated pass by refs in xmlrpc.inc.php
ini_set('display_errors', "0");

//Require xmlrpc and geshi
require_once('xmlrpc.inc.php');
require_once("geshi/geshi.php");

//Snipplr Class
if(!class_exists("SnipplrSnippets")){
	class SnipplrSnippets {
		//Initialise admin options on activation
		function init(){
			$this->admin_options();
		}//End init
		
		//Set any default admin options on activation
		function admin_options(){
			update_option("snip_api_key", 'Enter your API Key', '', 'yes');
		}//End admin_options
		
		//Init admin page
		function snipplrsnippets_admin(){
			if(function_exists('add_management_page')){
				add_options_page('Snipplr Snippets', 'Snipplr Snippets', 1, basename(__FILE__), array($this,'snipplrsnippets_admin_page'));
			}
		}//End snipplrsnippets_admin
		
		//Generate the snipplr admin page
		function snipplrsnippets_admin_page(){
			//Save the settings
			if(isset($_POST['snip_key_save'])){
				//Check validity
				$key = $this->check_key($_POST['APIkey']);
				if($key == 0):
					$message = "<div id='message' class='updated fade'><p>Sorry, your Snipplr API key isn't valid. Please double check it.</p></div>";
				else:
					update_option('snip_api_key', $key);
					$message = "<div id='message' class='updated fade'><p>Your API key was updated successfully.</p></div>";
				endif;
			}
				
			if(isset($_POST['snip_settings_save'])){
				//Update the checkbox settings
				if($_POST['csshead'] == 1): update_option('snip_css_header', 1); else: update_option('snip_css_header', 0); endif;
				if($_POST['title'] == 1): update_option('snip_title', 1); else: update_option('snip_title', 0); endif;
				if($_POST['author'] == 1): update_option('snip_author', 1); else: update_option('snip_author', 0); endif;
				if($_POST['comment'] == 1): update_option('snip_comment', 1); else: update_option('snip_comment', 0); endif;
				if($_POST['numbers'] == 1): update_option('snip_numbers', 1); else: update_option('snip_numbers', 0); endif;
				if($_POST['highlight'] == 1): update_option('snip_highlight', 1); else: update_option('snip_highlight', 0); endif;
				$message = "<div id='message' class='updated fade'><p>Your settings were saved successfully.</p></div>";
			}
			
			//Uninstall plug-in settings
			if(isset($_POST['snp_uninstall'])){
				delete_option("snip_api_key");
				delete_option('snip_title');
				delete_option('snip_author');
				delete_option('snip_comment');
				delete_option('snip_numbers');
				delete_option('snip_highlight');
				delete_option("snip_css_header");
				$message = '<div id="message" class="updated fade"><p>The settings have been removed from the database.</p></div>';
			}
			
			//Checkbox status
			$csshead = (get_option('snip_css_header') == 1) ? "checked='checked'" : "";
			$title = (get_option('snip_title') == 1) ? "checked='checked'" : "";
			$author = (get_option('snip_author') == 1) ? "checked='checked'" : "";
			$comment = (get_option('snip_comment') == 1) ? "checked='checked'" : "";
			$numbers = (get_option('snip_numbers') == 1) ? "checked='checked'" : "";
			$highlight = (get_option('snip_highlight') == 1) ? "checked='checked'" : "";
			
			//Get the current set API key
			$key = get_option("snip_api_key");
			
			if($key == "" && $message == ""):
				$message = '<div id="message" class="error fade"><p>You must enter your Snipplr API key before the plugin will work.</p></div>';
			endif;
			
			//Include the template
			include(SNIPS_DIR.'/includes/admin_page.inc.php');
		}//End snipplrsnippets_admin_page
		
		//Add CSS to the admin header
		function admin_register_head(){
			$site_url = get_option('siteurl');
			$css_url = $site_url . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/snipplr-admin-panel.css';
			echo "<link rel='stylesheet' type='text/css' href='$css_url' />\n";
		}//End admin_register_head
		
		//Add the plug-in CSS to the site header
		function site_register_head(){
			$site_url = get_option('siteurl');
			$snipplr_css_url = $site_url . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/snipplr-main.css';
			$includecss = get_option("snip_css_header");
			if(!$includecss):
				echo "<link rel='stylesheet' type='text/css' href='$snipplr_css_url' />\n";
			endif;
		}//End site_register_head
		
		//Call Snipplr
		function snipplr_call($method, $args){
			return XMLRPC_request('snipplr.com', '/xml-rpc.php', $method, $args);
		}//End snipplr_call
		
		//Checks the Snipplr API key for validity
		function check_key($key){
			$key = preg_replace('/[^a-f0-9]/i', '', strtolower($key));
			$result = $this->snipplr_call('user.checkkey', array(XMLRPC_prepare($key)));
			return ($result[1] == 1) ? $key : 0;
		}//End check_key
		
		//Posts the snippet to the blog post via GeSHi
		function parse_post_for_snippets($post){
			$show_title = (get_option('snip_title') == 1) ? true : false;
			$show_author = (get_option('snip_author') == 1) ? true : false;
			$show_comment = (get_option('snip_comment') == 1) ? true : false;
			$show_numbers = (get_option('snip_numbers') == 1) ? true : false;
			$disable_highlight = (get_option('snip_highlight') == 1) ? true : false;
			
			//Look for [snippet=##] in the post
			preg_match_all("/\[snippet id=([0-9]+)\]/", $post, $matches);
			
			//Loop through the post snippets
			for($i = 0; $i < count($matches[0]); $i++){
				$tag = $matches[0][$i];
				$snippet_id = $matches[1][$i];
				$result = $this->snipplr_call("snippet.get", array(XMLRPC_prepare($snippet_id)));
				
				if($result['faultCode'] == 2):
					$replace = "Snippet #$snippet_id does not exist.";
				else:
					$snippet     = $result[1];
					
					$user_id     = $snippet['user_id'];
					$username    = $snippet['username'];
					$title       = $snippet['title'];
					$snipplr_url = $snippet['snipplr_url'];
					$comment     = $snippet['comment'];
					$source      = html_entity_decode($snippet['source']);
					$created     = date("F jS, Y", strtotime($snippet['created']));
					
					$language    = strtolower($snippet['language']);				
					if($language == "other" || $disable_highlight == true):
						$language = "text";
					endif;
					
					
					//GeSHi code formatting
					$geshi = new GeSHi($source, $language);
					$geshi->set_header_type(GESHI_HEADER_NONE);
					
					$liststart = ($show_title != "" || $show_author != "" || $show_comment!= "") ? "<ul class='snippet-meta'>" : "";
					$title = $show_title ? "<li><strong><a href='$snipplr_url'>$title</a></strong></li>" : "";
					$author = $show_author ? "<li><small>Posted by <a href='http://snipplr.com/users/$username'>$username</a> on $created</small></li>" : "";
					$comment = $show_comment ? "<li><small>$comment</small></li>" : "";
					$listend = ($show_title != "" || $show_author != "" || $show_comment!= "") ? "</ul>" : "";
					
					if($show_numbers):
						$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
					endif;
					
					$source = "<div class='sniplrcode'>" . $geshi->parse_code() . "</div>";
					$source = "{$source}{$liststart}{$title}{$author}{$comment}{$listend}";
					
					$post   = str_replace($tag, $source, $post);
				endif;
			}
			return $post;
		}
	}//End class
}

//Snipplr widget class
class Snipplr_Widget extends WP_Widget {
	//Widget constructor
	function Snipplr_Widget(){
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'snipplr', 'description' => 'Display your latest snippets in your sidebar.' );
		
		/* Widget control settings. */
		$control_ops = array('id_base' => 'snipplr-widget');
		
		/* Create the widget. */
		$this->WP_Widget( 'snipplr-widget', 'Snipplr Snippets', $widget_ops, $control_ops );
	}
	
	//Widget layout
	function widget($args, $instance) {
		extract($args);
		
		/* User-selected settings. */
		$title = apply_filters('snipplr-widget', $instance['title']);
		$snippetnumber = apply_filters('snipplr-widget', $instance['snippetnumber']);
		$sortby = apply_filters('snipplr-widget', $instance['sortby']);
		$showprivate = apply_filters('snipplr-widget', $instance['showprivate']);
		
		/* Before widget */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ($title){
			echo $before_title . $title . $after_title;
		}
		
		$limit = 10;//Can't use this to limit results, API returns double the number!
		$tags = "";
		
		$userlimit = $instance['snippetnumber'];
		$result = SnipplrSnippets::snipplr_call("snippet.list", array(XMLRPC_prepare(get_option('snip_api_key')), XMLRPC_prepare($tags), XMLRPC_prepare($sortby), XMLRPC_prepare($limit)));
		if(isset($result['faultCode'])) return;
		
		$out = "<ul class='snippets'>";
		$sCount = 1;
		foreach($result[1] as $snippet):
			//Skip private
			if($snippet['private'] && !$showprivate){continue;}
			//Set numer of snippets to view
			if($sCount > $userlimit){continue;}
			
			$id    = $snippet['id'];
			$title = $snippet['title'];
			$clean_title = strtolower(str_replace(" ", "-", preg_replace("/[^a-zA-Z0-9 ]/", "", $title)));
			$url = "http://snipplr.com/view/$id/$clean_title/";
			$out .= "<li><a href='$url'>$title</a></li>";
			$sCount++;
		endforeach;
		$out .= "</ul>";
		echo $out;
		
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	//Save our user settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['snippetnumber'] = strip_tags($new_instance['snippetnumber']);
		$instance['sortby'] = strip_tags($new_instance['sortby']);
		$instance['showprivate'] = strip_tags($new_instance['showprivate']);
		return $instance;
	}
	
	//Widget admin display
	function form($instance) {
		/* Set up some default widget settings. */
		$defaults = array('title' => 'Recent Snippets', 'snippetnumber' => 5, 'sortby' => 'date', 'showprivate' => 0);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat"  />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'snippetnumber' ); ?>">Number of snippets to show:
				<input type="text" id="<?php echo $this->get_field_id( 'snippetnumber' ); ?>" name="<?php echo $this->get_field_name( 'snippetnumber' ); ?>" value="<?php echo $instance['snippetnumber']; ?>" style="width: 20%; display: block;" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('sortby'); ?>">Sort snippets by:
				<select id="<?php echo $this->get_field_id( 'sortby' ); ?>" name="<?php echo $this->get_field_name( 'sortby' ); ?>" class="widefat" style="width: 40%; display: block;">
					<option <?php if ( $instance['sortby'] == 'date' ) echo 'selected="selected"'; ?> value="date">Date</option>
					<option <?php if ( $instance['sortby'] == 'title' ) echo 'selected="selected"'; ?> value="title">Title</option>
					<option <?php if ( $instance['sortby'] == 'random' ) echo 'selected="selected"'; ?> value="random">Random</option>
				</select>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('showprivate'); ?>">Show private snippets?:
				<select id="<?php echo $this->get_field_id( 'showprivate' ); ?>" name="<?php echo $this->get_field_name( 'showprivate' ); ?>" class="widefat" style="width: 40%; display: block;">
					<option <?php if ( $instance['showprivate'] == 0 ) echo 'selected="selected"'; ?> value="0">No</option>
					<option <?php if ( $instance['showprivate'] == 1 ) echo 'selected="selected"'; ?> value="1">Yes</option>
				</select>
			</label>
		</p>
		
	<?php 
	}
}

function snipplr_load_widget() {
	register_widget('Snipplr_Widget');
}

//Initialise Snipplr Class
if(class_exists("SnipplrSnippets")){
	$SnipplrSnippets = new SnipplrSnippets();
}

if(isset($SnipplrSnippets)){
	//Initialise the plug-in
	register_activation_hook(__FILE__,array(&$SnipplrSnippets, 'init'));
	//Initialise the admin page
	add_action('admin_menu', array(&$SnipplrSnippets, 'snipplrsnippets_admin'), 1);
	//Add css to admin header
	add_action('admin_head', array(&$SnipplrSnippets, 'admin_register_head'), 1);
	//Add css to the site header
	add_action('wp_head', array(&$SnipplrSnippets, 'site_register_head'), 1);
	//Parse each blog post
	add_filter('the_content', array(&$SnipplrSnippets, 'parse_post_for_snippets'), 1);
	//Load Snipplr widget on init
	add_action('widgets_init', 'snipplr_load_widget');
}
?>