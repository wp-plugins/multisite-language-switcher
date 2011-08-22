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

	public function __construct () {
		$args = func_get_args ();
		$this->name = MSLS_DEF_STRING . $this->sep . implode ($this->sep, $args);
		$this->exists = $this->set (get_option ($this->name));
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

	public function set ($arr) {
		if (is_array ($arr)) {
			foreach ($arr as $key => $value) {
				$this->__set ($key, $value);
			}
			return true;
		}
		return false;
	}

	public function save ($arr) {
		if ($this->set ($arr)) {
			delete_option ($this->name);
			add_option ($this->name, $this->options, '', $this->autoload);
		}
	}

	public function __isset ($key) {
		return isset ($this->options[$key]);
	}

	public function has_value ($language) {
		return (!empty ($this->options[$language]) ? true : false);
	}

	public function get_postlink ($language) {
		return false;
	}

	public function get_permalink ($language) {
		$postlink = $this->get_postlink ($language);
		return ($postlink ? $postlink : site_url ());
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

	public function get_postlink ($language) {
		return (
			$this->has_value ($language) ? 
			get_tag_link ($this->options[$language]) : 
			false
		);
	}

}

class MslsCategoryOptions extends MslsTermOptions {

	public function get_postlink ($language) {
		return (
			$this->has_value ($language) ? 
			get_category_link ($this->options[$language]) : 
			false
		);
	}

}

?>
