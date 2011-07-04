<?php

require_once (dirname (__FILE__) . '/MslsMain.php');

class MslsLink {

	protected $args = array ();
	protected $format_string = '<img src="{src}" alt="{alt}"/> {txt}';

	static function getTypes () {
		return array ( 
			'0' => 'MslsLink',
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
		if (!in_array ($display, array_keys ($types))) $display = 0;
		return new $types[$display];
	}

	public function __set ($key, $value) {
		$this->args[$key] = $value;
	}

	public function __toString () {
		$temp = array ();
		foreach (array_keys ($this->args) as $key) {
			$temp[] = '{' . $key . '}';
		}
		return str_replace (
			$temp,
			$this->args,
			$this->format_string
		);
	}

}

class MslsLinkTextOnly extends MslsLink {

	protected $format_string = '{txt}';

	static function getDescription () {
		return __ ("Description only", MSLS_DEF_STRING);
	}

}

class MslsLinkImageOnly extends MslsLink {

	protected $format_string = '<img src="{src}" alt="{alt}"/>';

	static function getDescription () {
		return __ ("Description and flags", MSLS_DEF_STRING);
	}

}

class MslsLinkTextImage extends MslsLink {

	protected $format_string = '{txt} <img src="{src}" alt="{alt}"/>';

	static function getDescription () {
		return __ ("Flags only", MSLS_DEF_STRING);
	}
}

?>
