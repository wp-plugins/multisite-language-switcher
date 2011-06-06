<?php

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
		$display = $this->get_options ('display');
		$items = '';
		foreach ($arr as $key => $value) {
			$items .= sprintf (
				'<option value="%s"%s>%s</option>',
				$key, ($display == $key ? ' selected="selected"' : ''), $value
			);
		}
		printf (
			'<select id="display" name="%s[display]">%s</select>',
			self::DEF_STRING, $items
		);
	}

	public function description () {
		printf (
			'<input id="description" name="%s[description]" value="%s" size="40">',
			self::DEF_STRING, $this->get_options ('description')
		);
	}

	public function before_output () {
		printf (
			'<input id="before_output" name="%s[before_output]" value="%s" size="30"/>',
			self::DEF_STRING, $this->get_options ('before_output')
		);
	}

	public function after_output () {
		printf (
			'<input id="after_output" name="%s[after_output]" value="%s" size="30"/>',
			self::DEF_STRING, $this->get_options ('after_output')
		);
	}

	public function before_item () {
		printf (
			'<input id="before_item" name="%s[before_item]" value="%s" size="30"/>',
			self::DEF_STRING, $this->get_options ('before_item')
		);
	}

	public function after_item () {
		printf (
			'<input id="after_item" name="%s[after_item]" value="%s" size="30"/>',
			self::DEF_STRING, $this->get_options ('after_item')
		);
	}

	public function validate ($input) {
		if (!is_numeric ($input['display'])) $input['display'] = 0; 
		return $input;
	}

}

?>
