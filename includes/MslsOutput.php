<?php

require_once (dirname (__FILE__) . '/MslsMain.php');
require_once (dirname (__FILE__) . '/MslsOptions.php');
require_once (dirname (__FILE__) . '/MslsLink.php');

class MslsOutput extends MslsMain implements iMslsMain {

	protected $content_filter;

	static function init () {
		return new self ();
	}

	public function get ($display, $exists = false) {
		$arr = array ();
		$blogs = $this->get_blogs ();
		if ($blogs) {
			global $post;
			$mydata = new MslsPostOptions ($post->ID);
			foreach ($blogs as $language => $blog) {
				switch_to_blog ($blog->userblog_id);
				$temp = new MslsOptions;
				if (true == $exists && !$mydata->get_postlink ($language))
					continue;
				$link = MslsLink::create ($display);
				$link->txt = (
					isset ($temp->description) ? 
					$temp->description :
					$language
				);
				$link->src = $this->get_image_url ($language);
				$link->alt = $language;
				$arr[] = sprintf (
					'<a href="%s" title="%s">%s</a>',
					$mydata->get_permalink ($language),
					$link->txt,
					$link
				);
			}
			restore_current_blog ();
		}
		return $arr;
	}

	public function __toString () {
		return (
			$this->options->before_output . 
			$this->options->before_item .
			implode (
				$this->options->after_item . 
				$this->options->before_item,
				$this->get ($this->options->display)
			) .
			$this->options->after_item .
			$this->options->after_output
		);
	}

}

class MslsWidget extends WP_Widget {

	public function __construct () {
		parent::__construct (false, $name = __ ("Multisite Language Switcher", MSLS_DEF_STRING));
	}

	function widget ($args, $instance) {
		extract ($args);
		$title = apply_filters ('widget_title', $instance['title']);
		echo $before_widget;
		if ($title)
			echo $before_title . $title . $after_title;
		$obj = new MslsOutput ();
		echo $obj;
		echo $after_widget;
	}

	function update ($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags ($new_instance['title']);
		return $instance;
	}

    function form($instance) {
        $title = esc_attr ($instance['title']);
		printf (
			'<p><label for="%s">%s:</label> <input class="widefat" id="%s" name="%s" type="text" value="%s" /></p>',
			$this->get_field_id ('title'),
			__ ('Title', MSLS_DEF_STRING),
			$this->get_field_id ('title'),
			$this->get_field_name ('title'),
			$title
		);
	}

}
add_action ('widgets_init', create_function ('', 'return register_widget("MslsWidget");'));

function msls_content_filter ($content) {
	$obj = new MslsOutput ();
	$links = $obj->get (1, true);
	if (!empty ($links)) {
		if (count ($links) > 2) {
			$last = array_pop ($links);
			$links = sprintf (
				__ ("%s and %s", MSLS_DEF_STRING),
				implode (', ', $links),
				$last
			);
		} else {
			$links = $links[0];
		}
		$content .= 
			'<p id="msls">' .
			sprintf (
				__ ("This post is also available in %s.", MSLS_DEF_STRING),
				$links
			) .
			'</p>';
	}
	return $content;
}

function the_msls () {
	$obj = new MslsOutput ();
	echo $obj;
}

?>
