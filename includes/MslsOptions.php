<?php

if (!defined ('MSLS_DEF_STRING'))  define ('MSLS_DEF_STRING', 'msls');

class MslsOptions {

	protected $options;

	public function __construct () {
		$this->options = get_option (MSLS_DEF_STRING);
	}

	public function __get ($key) {
		return (
			isset ($this->options[$key]) ?
			$this->options[$key] :
			''
		);
	}

	public function __isset ($key) {
		return isset ($this->options[$key]);
	}

}

class MslsPostOptions extends MslsOptions {

	public function __construct ($post_id) {
		$this->options = get_option (MSLS_DEF_STRING . '_' . $post_id);
	}

	public function get_postlink ($language) {
		return (
			(is_single () || is_page ()) && !empty ($this->options[$language]) ? 
			get_permalink ($this->options[$language]) : 
			false
		);
	}

	public function get_permalink ($language) {
		$postlink = $this->get_postlink ($language);
		return ($postlink ? $postlink : site_url ());
	}

}

?>
