<?php

/*
Plugin Name: Multisite Language Switcher
Plugin URI: http://lloc.de/msls
Description: A simple plugin that will help to you with your multisite-multilingual-installation
Version: 0.2
Author: Dennis Ploetner	
Author URI: http://lloc.de/
*/

/*
Copyright 2010  Dennis Ploetner  (email : re@lloc.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('MslsMain')) {

	register_activation_hook (__FILE__, 'MslsMain::activate');
	register_deactivation_hook (__FILE__, 'MslsMain::deactivate');

	if (is_admin()) {
		add_action ('load-post.php', 'MslsMetaBox::init');
		add_action ('admin_menu', 'MslsAdmin::init');
	}

	class MslsMain {

		protected $user_id;
		protected $current_blog_id;

		protected $blogs = null;
		protected $image_url = null;
		protected $options = null;

		const DEF_STRING = 'msls';

		static function activate () {
			if (function_exists ('is_multisite') && is_multisite ()) 
				return; 
			deactivate_plugins (__FILE__);
			die (
				"This plugin needs the activation of the multisite-feature for working properly. Please read <a href='http://codex.wordpress.org/Create_A_Network'>this post</a> if you don't know the meaning.\n"
			);
		}

		static function deactivate () { }

		public function __construct () {
			$this->current_blog_id = get_current_blog_id ();
			$this->user_id = get_user_id_from_string (get_blog_option ($this->current_blog_id, 'admin_email'));
			load_plugin_textdomain (self::DEF_STRING, false, dirname (plugin_basename ( __FILE__ )) . '/languages/');
		}

		public function get_blogs () {
			if (is_null ($this->blogs)) {
				$this->blogs = array ();
				foreach (get_blogs_of_user ($this->user_id) as $blog) {
					if ($blog->userblog_id != $this->current_blog_id) {
						$language = get_blog_option ($blog->userblog_id, 'WPLANG');
						$this->blogs[$language] = $blog;
					}
				}
				ksort ($this->blogs);
			}
			return $this->blogs;
		}

		public function get_image_url ($language) {
			if (is_null ($this->image_url)) {
				$this->image_url = sprintf (
					'%s/%s%s',
					WP_PLUGIN_URL, str_replace (basename (__FILE__), "", plugin_basename (__FILE__)), 'flags'
				);
			}
			if (strlen ($language) == 5) 
				$language = strtolower (substr ($language, -2));
			return sprintf (
				'%s/%s.png',
				$this->image_url, $language
			);
		}

		public function get_options () {
			if (is_null ($this->options))
				$this->options = get_option (self::DEF_STRING);
			return $this->options;
		}

	}

	interface iMslsMain {

		static function init ();

	}

	class MslsMetaBox extends MslsMain implements iMslsMain {

		static function init () {
			$obj = new self ();
			add_action ('add_meta_boxes', array ($obj, 'add'));
			add_action ('save_post', array ($obj, 'save'));
			return $obj;
		}

		public function add () {
			add_meta_box (
				 'msls',
				 __ ("Multisite Language Switcher", self::DEF_STRING),
				 array ($this, 'render_post'),
				 'post',
				 'side',
				 'high'
			);
			add_meta_box (
				 'msls',
				 __ ("Multisite Language Switcher", self::DEF_STRING),
				 array ($this, 'render_page'),
				 'page',
				 'side',
				 'high'
			);
		}

		protected function render ($type) {
			global $post;
			$blogs = $this->get_blogs ();
			if ($blogs) {
				$temp = $post;
				$mydata = get_option (self::DEF_STRING . '_' . $post->ID);
				wp_nonce_field (plugin_basename (__FILE__), self::DEF_STRING . '_noncename');
				echo '<ul>';
				foreach ($blogs as $language => $blog) {
					switch_to_blog ($blog->userblog_id);
					$args = array (
						'post_type' => $type, 
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'orderby' => 'title',
						'order' => 'ASC',
					); 
					$my_query = new WP_Query ($args);
					$options = '';
					while ($my_query->have_posts ()) {
						$my_query->the_post ();
						$my_id = get_the_ID ();
						$options .= sprintf (
							'<option value="%s"%s>%s</option>',
							$my_id, (isset ($mydata[$language]) && $my_id == $mydata[$language] ? ' selected="selected"' : ''), get_the_title ()
						);
					}
					printf (
						'<li><label for="%s[%s]"><img alt="%s" src="%s" /> </label><select style="width:90%%" name="%s[%s]" class="postform"><option value=""></option>%s</select></li>',
						self::DEF_STRING, $language, $language, $this->get_image_url ($language), self::DEF_STRING, $language, $options
					);
				}
				printf (
					'</ul><input style="align:right" type="submit" class="button-secondary" value="%s"/>',
					__ ("Update", self::DEF_STRING)
				);
				$post = $temp;
				restore_current_blog ();
			} else {
				printf (
					'<p>%s</p>',
					__ ("You should define at least another blog in a different language in order to have some benefit from this plugin!", self::DEF_STRING)
				);
			}
		}

		public function render_post () {
			$this->render ('post');
		}

		public function render_page () {
			$this->render ('page');
		}

		public function save ($post_id) {
			if (defined ('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
				return;
			if (!wp_verify_nonce ($_POST[self::DEF_STRING . '_noncename'], plugin_basename( __FILE__ )))
				return;
			if ('page' == $_POST['post_type']) {
				if (!current_user_can ('edit_page', $post_id))
					return;
			} else {
				if (!current_user_can ('edit_post', $post_id))
					return;
			}
			$mydata = $_POST[self::DEF_STRING];
			$language = get_blog_option ($blog->current_blog_id, 'WPLANG');
			$mydata[$language] = $post_id;

			$this->set ($mydata, $language);
			foreach ($this->get_blogs () as $language => $blog) {
				switch_to_blog ($blog->userblog_id);
				$this->set ($mydata, $language);
			}
			restore_current_blog ();
			return $mydata;
		}

		protected function set ($mydata, $language) {
			$myname = self::DEF_STRING . '_' . $mydata[$language];
			delete_option ($myname);
			if (!empty ($mydata[$language])) {
				$mydata = array_filter ($mydata);
				unset ($mydata[$language]); 
				add_option ($myname, $mydata, '', 'no');
			}
		}

	}

	class MslsAdmin extends MslsMain implements iMslsMain {

		static function init () {
			$obj = new self ();
			add_options_page (
				__ ("Multisite Language Switcher", self::DEF_STRING), 
				__ ("Multisite Language Switcher", self::DEF_STRING), 
				'manage_options', 
				__CLASS__,
				array ($obj, 'render')
			);
			add_action ('admin_init', array ($obj, 'register'));
			return $obj;
		}

		public function render () {
			printf (
				'<div class="wrap"><div class="icon32" id="icon-options-general"><br></div><h2>%s</h2><p>%s</p><form action="options.php" method="post">',
				__ ("Multisite Language Switcher Options", self::DEF_STRING),
				__ ("To achieve maximum flexibility, you have to configure each blog separately.", self::DEF_STRING)
			);
			settings_fields (self::DEF_STRING);
			do_settings_sections (__CLASS__);
			printf (
				'<p class="submit"><input name="Submit" type="submit" class="button-primary" value="%s" /></p></form></div>',
				__ ("Update", self::DEF_STRING)
			);
		}

		public function register () {
			register_setting (self::DEF_STRING, self::DEF_STRING, array ($this, 'validate'));
			add_settings_section ('section', __ ("Main Settings", self::DEF_STRING), array ($this, 'section'), __CLASS__);
			add_settings_field ('display', __ ("Display", self::DEF_STRING), array ($this, 'display'), __CLASS__, 'section');
			add_settings_field ('description', __ ("Description", self::DEF_STRING), array ($this, 'description'), __CLASS__, 'section');
			add_settings_field ('before_output', __ ("Text/HTML before the list", self::DEF_STRING), array ($this, 'before_output'), __CLASS__, 'section');
			add_settings_field ('after_output', __ ("Text/HTML after the list", self::DEF_STRING), array ($this, 'after_output'), __CLASS__, 'section');
			add_settings_field ('before_item', __ ("Text/HTML before each item", self::DEF_STRING), array ($this, 'before_item'), __CLASS__, 'section');
			add_settings_field ('after_item', __ ("Text/HTML after each item", self::DEF_STRING), array ($this, 'after_item'), __CLASS__, 'section');
		}

		public function section () { }

		public function display () {
			$arr = array (
				0 => __ ("Flags and description", self::DEF_STRING),
				1 => __ ("Flags only", self::DEF_STRING),
				2 => __ ("Description only", self::DEF_STRING),
			);
			$options = $this->get_options ();
			$items = '';
			foreach ($arr as $key => $value) {
				$items .= sprintf (
					'<option value="%s"%s>%s</option>',
					$key, ($options['display'] == $key ? ' selected="selected"' : ''), $value
				);
			}
			printf (
				'<select id="display" name="%s[display]">%s</select>',
				self::DEF_STRING, $items
			);
		}

		public function description () {
			$options = $this->get_options ();
			printf (
				'<input id="description" name="%s[description]" value="%s" size="40">',
				self::DEF_STRING, $options['description']
			);
		}

		public function before_output () {
			$options = $this->get_options ();
			printf (
				'<input id="before_output" name="%s[before_output]" value="%s" size="30"/>',
				self::DEF_STRING, $options['before_output']
			);
		}

		public function after_output () {
			$options = $this->get_options ();
			printf (
				'<input id="after_output" name="%s[after_output]" value="%s" size="30"/>',
				self::DEF_STRING, $options['after_output']
			);
		}

		public function before_item () {
			$options = $this->get_options ();
			printf (
				'<input id="before_item" name="%s[before_item]" value="%s" size="30"/>',
				self::DEF_STRING, $options['before_item']
			);
		}

		public function after_item () {
			$options = $this->get_options ();
			printf (
				'<input id="after_item" name="%s[after_item]" value="%s" size="30"/>',
				self::DEF_STRING, $options['after_item']
			);
		}

		public function validate ($input) {
			if (!is_numeric ($input['display'])) $input['display'] = 0; 
			return $input;
		}

	}

	class MslsOutput extends MslsMain implements iMslsMain {

		static function init () {
			return new self ();
		}

		public function output () {
			global $post;
			$output = '';
			$blogs = $this->get_blogs ();
			if ($blogs) {
				$mydata = get_option (self::DEF_STRING . '_' . $post->ID);
				$options = $this->get_options ();
				foreach ($blogs as $language => $blog) {
					switch_to_blog ($blog->userblog_id);
					$temp = get_option (self::DEF_STRING);
					$title = (isset ($temp['description']) ? $temp['description'] : $language);
					$link = $title;
					if (empty ($option['display'])) {
						$link = sprintf (
							'<img src="%s" alt="%s"/> %s',
							$this->get_image_url ($language), $language, $title 
						);
					} elseif ($option['display'] == 1) {
						$link = sprintf (
							'<img src="%s" alt="%s"/>',
							$this->image_url ($language), $title
						); 
					}
					$output .= sprintf (
						'%s<a href="%s" title="%s">%s</a>%s',
						(isset ($options['before_item']) ? $options['before_item'] : ''),
						((is_single () || is_page ()) && !empty ($mydata[$language]) ? get_permalink ($mydata[$language]) : site_url ()),
						$title,
						$link,
						(isset ($options['after_item']) ? $options['after_item'] : '') 
					);
				}
				$output = sprintf (
					'%s%s%s',
					(isset ($options['before_list']) ? $options['before_list'] : ''),
					$output,
					(isset ($options['after_list']) ? $options['after_list'] : '') 
				);
				restore_current_blog ();
			}
			return $output;
		}

	}

	function the_msls () {
		$obj = new MslsOutput ();
		echo $obj->output ();
	}

}

?>
