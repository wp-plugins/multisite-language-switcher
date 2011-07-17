<?php

require_once (dirname (__FILE__) . '/MslsMain.php');
require_once (dirname (__FILE__) . '/MslsOptions.php');

class MslsPostTag extends MslsMain implements iMslsMain {

	public $taxonomy;

	static function init () {
		$obj = new self ();
		if (isset ($_REQUEST['taxonomy'])) {
			$obj->taxonomy = $_REQUEST['taxonomy'];
			if (in_array ($obj->taxonomy, array ('category', 'post_tag'))) {
				add_action ("{$obj->taxonomy}_edit_form_fields", array ($obj, 'add'));
				add_action ("edited_{$obj->taxonomy}", array ($obj, 'set'));
			}
		}
		return $obj;
	}

	public function add ($tag) {
		$blogs = $this->get_blogs ();
		if ($blogs) {
			printf (
				'<tr><th colspan="2"><strong>%s</strong></th></tr>',
				 __ ("Multisite Language Switcher", MSLS_DEF_STRING)
			);
			$mydata = new MslsTermOptions ($tag->term_id);
			foreach ($blogs as $language => $blog) {
				switch_to_blog ($blog->userblog_id);
				$options = '';
				$terms = get_terms ($this->taxonomy);
				if (!empty ($terms)) {
					foreach ($terms as $term) {
						$options .= sprintf (
							'<option value="%s"%s>%s</option>',
							$term->term_id, ($term->term_id == $mydata->$language ? ' selected="selected"' : ''), $term->name
						);
					}
				}
				printf (
					'<tr class="form-field"><th scope="row" valign="top"><label for="%s[%s]"><img alt="%s" src="%s" /> </label></th><td><select style="width:25em;" name="%s[%s]"><option value=""></option>%s</select></td>',
					MSLS_DEF_STRING, $language, $language, $this->get_image_url ($language), MSLS_DEF_STRING, $language, $options
				);
			}
			restore_current_blog ();
		}
	}

	public function set ($term_id) {
		if (!current_user_can ('manage_categories')) return;
		$this->save ($term_id, 'MslsTermOptions');
	}

}

?>
