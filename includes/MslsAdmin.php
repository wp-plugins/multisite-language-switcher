<?php

require_once (dirname (__FILE__) . '/MslsMain.php');
require_once (dirname (__FILE__) . '/MslsOptions.php');
require_once (dirname (__FILE__) . '/MslsLink.php');

class MslsAdmin extends MslsMain implements iMslsMain {

	static function init () {
		$obj = new self ();
		add_options_page (
			__ ("Multisite Language Switcher", MSLS_DEF_STRING), 
			__ ("Multisite Language Switcher", MSLS_DEF_STRING), 
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
			__ ("Multisite Language Switcher Options", MSLS_DEF_STRING),
			__ ("To achieve maximum flexibility, you have to configure each blog separately.", MSLS_DEF_STRING)
		);
		settings_fields (MSLS_DEF_STRING);
		do_settings_sections (__CLASS__);
		printf (
			'<p class="submit"><input name="Submit" type="submit" class="button-primary" value="%s" /></p></form></div>',
			__ ("Update", MSLS_DEF_STRING)
		);
	}

	public function register () {
		register_setting (MSLS_DEF_STRING, MSLS_DEF_STRING, array ($this, 'validate'));
		add_settings_section ('section', __ ("Main Settings", MSLS_DEF_STRING), array ($this, 'section'), __CLASS__);
		add_settings_field ('display', __ ("Display", MSLS_DEF_STRING), array ($this, 'display'), __CLASS__, 'section');
		add_settings_field ('description', __ ("Description", MSLS_DEF_STRING), array ($this, 'description'), __CLASS__, 'section');
		add_settings_field ('before_output', __ ("Text/HTML before the list", MSLS_DEF_STRING), array ($this, 'before_output'), __CLASS__, 'section');
		add_settings_field ('after_output', __ ("Text/HTML after the list", MSLS_DEF_STRING), array ($this, 'after_output'), __CLASS__, 'section');
		add_settings_field ('before_item', __ ("Text/HTML before each item", MSLS_DEF_STRING), array ($this, 'before_item'), __CLASS__, 'section');
		add_settings_field ('after_item', __ ("Text/HTML after each item", MSLS_DEF_STRING), array ($this, 'after_item'), __CLASS__, 'section');
	}

	public function section () { }

	public function display () {
		$items = '';
		foreach (MslsLink::getTypesDescription () as $key => $value) {
			$items .= sprintf (
				'<option value="%s"%s>%s</option>',
				$key, 
				($this->options->display == $key ? ' selected="selected"' : ''), 
				$value
			);
		}
		printf (
			'<select id="display" name="%s[display]">%s</select>',
			MSLS_DEF_STRING, $items
		);
	}

	public function description () {
		echo $this->render_input ('description', '40');
	}

	public function before_output () {
		echo $this->render_input ('before_output');
	}

	public function after_output () {
		echo $this->render_input ('after_output');
	}

	public function before_item () {
		echo $this->render_input ('before_item');
	}

	public function after_item () {
		echo $this->render_input ('after_item');
	}

	public function render_input ($key, $size = '30') {
		return sprintf (
			'<input id="%s" name="%s[%s]" value="%s" size="%s"/>',
			$key, MSLS_DEF_STRING, $key, $this->options->$key
		);

	}

	public function validate ($input) {
		if (!is_numeric ($input['display'])) $input['display'] = 0; 
		return $input;
	}

}

?>
