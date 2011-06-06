<?php

interface iMslsMain {

	static function init ();

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
				'%s/%s/%s',
				WP_PLUGIN_URL, dirname (MSLS_PLUGIN_DIR), 'flags'
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

?>
