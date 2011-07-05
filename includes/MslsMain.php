<?php

require_once (dirname (__FILE__) . '/MslsOptions.php');
require_once (dirname (__FILE__) . '/MslsOutput.php');

interface iMslsMain {

	static function init ();

}

class MslsMain {

	protected $user_id;
	protected $current_blog_id;
	protected $options;

	protected $blogs = null;
	protected $image_url = null;

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
		$this->options = new MslsOptions;
		load_plugin_textdomain (MSLS_DEF_STRING, false, dirname (MSLS_PLUGIN_PATH) . '/languages/');
		if ($this->options->content_filter == 1)
			add_filter ('the_content', 'msls_content_filter');
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
				'%s/%s/flags',
				WP_PLUGIN_URL, 
				dirname (MSLS_PLUGIN_PATH)
			);
		}
		if (strlen ($language) == 5) 
			$language = strtolower (substr ($language, -2));
		return sprintf (
			'%s/%s.png',
			$this->image_url, $language
		);
	}

}

?>
