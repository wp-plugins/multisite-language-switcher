<?php

require_once (dirname (__FILE__) . '/MslsMain.php');

class MslsMetaBox extends MslsMain implements iMslsMain {

	static function init () {
		$obj = new self ();
		add_action ('add_meta_boxes', array ($obj, 'add'));
		add_action ('save_post', array ($obj, 'save'));
		return $obj;
	}

	public function add () {
		add_meta_box (
			 'msls',
			 __ ("Multisite Language Switcher", self::DEF_STRING),
			 array ($this, 'render_post'),
			 'post',
			 'side',
			 'high'
		);
		add_meta_box (
			 'msls',
			 __ ("Multisite Language Switcher", self::DEF_STRING),
			 array ($this, 'render_page'),
			 'page',
			 'side',
			 'high'
		);
	}

	protected function render ($type) {
		global $post;
		$blogs = $this->get_blogs ();
		if ($blogs) {
			$temp = $post;
			$mydata = get_option (self::DEF_STRING . '_' . $post->ID);
			wp_nonce_field (MSLS_PLUGIN_DIR, self::DEF_STRING . '_noncename');
			echo '<ul>';
			foreach ($blogs as $language => $blog) {
				switch_to_blog ($blog->userblog_id);
				$args = array (
					'post_type' => $type, 
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
				); 
				$my_query = new WP_Query ($args);
				$options = '';
				while ($my_query->have_posts ()) {
					$my_query->the_post ();
					$my_id = get_the_ID ();
					$options .= sprintf (
						'<option value="%s"%s>%s</option>',
						$my_id, (isset ($mydata[$language]) && $my_id == $mydata[$language] ? ' selected="selected"' : ''), get_the_title ()
					);
				}
				printf (
					'<li><label for="%s[%s]"><img alt="%s" src="%s" /> </label><select style="width:90%%" name="%s[%s]" class="postform"><option value=""></option>%s</select></li>',
					self::DEF_STRING, $language, $language, $this->get_image_url ($language), self::DEF_STRING, $language, $options
				);
			}
			printf (
				'</ul><input style="align:right" type="submit" class="button-secondary" value="%s"/>',
				__ ("Update", self::DEF_STRING)
			);
			$post = $temp;
			restore_current_blog ();
		} else {
			printf (
				'<p>%s</p>',
				__ ("You should define at least another blog in a different language in order to have some benefit from this plugin!", self::DEF_STRING)
			);
		}
	}

	public function render_post () {
		$this->render ('post');
	}

	public function render_page () {
		$this->render ('page');
	}

	public function save ($post_id) {
		if (defined ('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
			return;
		if (!wp_verify_nonce ($_POST[self::DEF_STRING . '_noncename'], MSLS_PLUGIN_DIR))
			return;
		if ('page' == $_POST['post_type']) {
			if (!current_user_can ('edit_page', $post_id))
				return;
		} else {
			if (!current_user_can ('edit_post', $post_id))
				return;
		}
		$mydata = $_POST[self::DEF_STRING];
		$language = get_blog_option ($blog->current_blog_id, 'WPLANG');
		$mydata[$language] = $post_id;

		$this->set ($mydata, $language);
		foreach ($this->get_blogs () as $language => $blog) {
			switch_to_blog ($blog->userblog_id);
			$this->set ($mydata, $language);
		}
		restore_current_blog ();
		return $mydata;
	}

	protected function set ($mydata, $language) {
		$myname = self::DEF_STRING . '_' . $mydata[$language];
		delete_option ($myname);
		if (!empty ($mydata[$language])) {
			$mydata = array_filter ($mydata);
			unset ($mydata[$language]); 
			add_option ($myname, $mydata, '', 'no');
		}
	}

}

?>
