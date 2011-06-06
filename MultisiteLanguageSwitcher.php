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

	interface iMslsMain {
		static function init ();
	}

    require_once (dirname (__FILE__) . '/include/MslsOutput.php');

	register_activation_hook (__FILE__, 'MslsMain::activate');
	register_deactivation_hook (__FILE__, 'MslsMain::deactivate');

	if (is_admin()) {
		require_once (dirname (__FILE__) . '/include/MslsMetaBox.php');
		add_action ('load-post.php', 'MslsMetaBox::init');

		require_once (dirname (__FILE__) . '/include/MslsAdmin.php');
		add_action ('admin_menu', 'MslsAdmin::init');
	}

	function the_msls () {
		$obj = new MslsOutput ();
		echo $obj->output ();
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

		public function get_options ($key = null) {
			if (is_null ($this->options)) {
				$this->options = get_option (self::DEF_STRING);
			}
			return (!is_null ($key) && isset ($this->options[$key]) ? $this->options[$key] : $this->options);
		}

	}

}

?>
