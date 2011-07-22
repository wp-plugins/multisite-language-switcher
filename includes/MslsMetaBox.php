<?php

require_once (dirname (__FILE__) . '/MslsMain.php');
require_once (dirname (__FILE__) . '/MslsOptions.php');

class MslsMetaBox extends MslsMain implements iMslsMain {

	static function init () {
		$obj = new self ();
		add_action ('add_meta_boxes', array ($obj, 'add'));
		add_action ('save_post', array ($obj, 'set'));
		return $obj;
	}

	public function add () {
		add_meta_box (
			 'msls',
			 __ ("Multisite Language Switcher", MSLS_DEF_STRING),
			 array ($this, 'render_post'),
			 'post',
			 'side',
			 'high'
		);
		add_meta_box (
			 'msls',
			 __ ("Multisite Language Switcher", MSLS_DEF_STRING),
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
			$mydata = new MslsPostOptions ($post->ID);
			wp_nonce_field (MSLS_PLUGIN_PATH, MSLS_DEF_STRING . '_noncename');
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
				$options = '';
				$my_query = new WP_Query ($args);
				while ($my_query->have_posts ()) {
					$my_query->the_post ();
					$my_id = get_the_ID ();
					$options .= sprintf (
						'<option value="%s"%s>%s</option>',
						$my_id, ($my_id == $mydata->$language ? ' selected="selected"' : ''), get_the_title ()
					);
				}
				printf (
					'<li><label for="%s[%s]"><img alt="%s" src="%s" /> </label><select style="width:90%%" name="%s[%s]" class="postform"><option value=""></option>%s</select></li>',
					MSLS_DEF_STRING, $language, $language, $this->get_image_url ($language), MSLS_DEF_STRING, $language, $options
				);
				restore_current_blog ();
			}
			printf (
				'</ul><input style="align:right" type="submit" class="button-secondary" value="%s"/>',
				__ ("Update", MSLS_DEF_STRING)
			);
			$post = $temp;
		} else {
			printf (
				'<p>%s</p>',
				__ ("You should define at least another blog in a different language in order to have some benefit from this plugin!", MSLS_DEF_STRING)
			);
		}
	}

	public function render_post () {
		$this->render ('post');
	}

	public function render_page () {
		$this->render ('page');
	}

	public function set ($post_id) {
		if (defined ('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
			return;
		if (!wp_verify_nonce ($_POST[MSLS_DEF_STRING . '_noncename'], MSLS_PLUGIN_PATH))
			return;
		if ('page' == $_POST['post_type']) {
			if (!current_user_can ('edit_page')) return;
		} else {
			if (!current_user_can ('edit_post')) return;
		}
		$this->save ($post_id, 'MslsPostOptions');
	}

}

?>
