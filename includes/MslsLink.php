<?php

require_once (dirname (__FILE__) . '/MslsMain.php');

class MslsLink {

	protected $args = array ();
	protected $format_string = '<img src="{src}" alt="{alt}"/> {txt}';

	static function geTypes () {
		return array ( 
			'0' => 'MslsLinkImageText',
			'1' => 'MslsLinkTextOnly',
			'2' => 'MslsLinkImageOnly',
			'3' => 'MslsLinkTextImage',
		);
	}

	static function getDescription () {
		return __ ("Flags and description", MSLS_DEF_STRING);
	}

	static function getTypesDescription () {
		$temp = array ();
		foreach (self::getTypes () as $key => $class) {
			$temp[$key] = call_user_func (
				array ($class, 'getDescription')
			);
		}
		return $temp;
	}
	
	public function create ($display) {
		$types = self::getTypes ();
		if (!in_array ($display, $types)) $display = 0;
		return new $types[$display];
	}

	public function __set ($key, $value) {
		$this->args[$key] = $value;
	}

	public function __toString () {
		return str_replace (
			array_keys ($this->args), 
			$this->args, 
			$this->format_string
		);
	}

}

class MslsLinkTextOnly extends MslsLinkImageText {

	protected $format_string = '<img src="{src}" alt="{alt}"/>';

	static function getDescription () {
		return __ ("Description only", MSLS_DEF_STRING);
	}

}

class MslsLinkImageOnly extends MslsLinkImageText {

	protected $format_string = '{txt}';

	static function getDescription () {
		return __ ("Description and flags", MSLS_DEF_STRING);
	}

}

class MslsLinkTextImage extends MslsLinkImageText {

	protected $format_string = '{txt} <img src="{src}" alt="{alt}"/>';

	static function getDescription () {
		return __ ("Flags only", MSLS_DEF_STRING);
	}
}

?>
