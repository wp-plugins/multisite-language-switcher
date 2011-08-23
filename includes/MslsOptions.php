<?php

if (!defined ('MSLS_DEF_STRING'))  define ('MSLS_DEF_STRING', 'msls');

class MslsOptionsFactory {

	static function create () {
		if (is_single () || is_page ()) {
			global $post;
			return new MslsPostOptions ($post->ID);
		} elseif (is_category ()) {
			return new MslsCategoryOptions (get_query_var ('cat'));
		} elseif (is_tag ()) {
			return new MslsTermOptions (get_query_var ('tag_id'));
		}
		return new MslsOptions ();
	}

}

class MslsOptions {

	protected $name;
	protected $options = array ();
	protected $exists = false;
	protected $sep = '';
	protected $autoload = 'yes';
	protected $base;

	public function __construct () {
		$args = func_get_args ();
		$this->name = MSLS_DEF_STRING . $this->sep . implode ($this->sep, $args);
		$this->exists = $this->set (get_option ($this->name));
		$this->set_base ();
	}

	public function __get ($key) {
		return (
			isset ($this->options[$key]) ?
			$this->options[$key] :
			''
		);
	}

	public function __set ($key, $value) {
		if ('' == $value) {
			if (isset ($this->options[$key]))
				unset ($this->options[$key]);
		} else {
			$this->options[$key] = $value;
		}
	}

	public function __isset ($key) {
		return isset ($this->options[$key]);
	}

	protected function correct ($url, $base) {
		if ($this->base != $base) {
			$search = '/' . $this->base . '/';
			$replace = '/' . $base . '/';
			$url = str_replace ($search, $replace, $url);
		}
		return $url;
	}

	public function save ($arr) {
		if ($this->set ($arr)) {
			delete_option ($this->name);
			add_option ($this->name, $this->options, '', $this->autoload);
		}
	}

	public function set ($arr) {
		if (is_array ($arr)) {
			foreach ($arr as $key => $value) {
				$this->__set ($key, $value);
			}
			return true;
		}
		return false;
	}

	protected function set_base () { }

	public function get_permalink ($language) {
		$postlink = $this->get_postlink ($language);
		return ($postlink ? $postlink : site_url ());
	}

	public function get_postlink ($language) {
		return false;
	}

	public function has_value ($language) {
		return (!empty ($this->options[$language]) ? true : false);
	}

}

class MslsPostOptions extends MslsOptions {

	protected $sep = '_';
	protected $autoload = 'no';

	public function get_postlink ($language) {
		return (
			$this->has_value ($language) ? 
			get_permalink ($this->options[$language]) : 
			false
		);
	}

}

class MslsTermOptions extends MslsOptions {

	protected $sep = '_term_';
	protected $autoload = 'no';

	protected function set_base () {
		$this->base = get_option ('tag_base', 'tag');
	}

	public function get_postlink ($language) {
		if ($this->has_value ($language)) {
			$url = get_term_link ((int) $this->options[$language], 'post_tag');
			$base = get_option ('tag_base', 'tag');
			return $this->correct ($url, $base);
		}
		return false;
	}

}

class MslsCategoryOptions extends MslsTermOptions {

	protected function set_base () {
		$this->base = get_option ('category_base', 'category');
	}

	public function get_postlink ($language) {
		if ($this->has_value ($language)) { 
			$url = get_term_link ((int) $this->options[$language], 'category'); 
			$base = get_option ('category_base', 'category');
			return $this->correct ($url, $base);
		}
		return false;
	}

}

?>
