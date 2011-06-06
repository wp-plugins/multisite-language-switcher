<?php

class MslsOutput extends MslsMain implements iMslsMain {

	static function init () {
		return new self ();
	}

	public function output () {
		global $post;
		$output = '';
		$blogs = $this->get_blogs ();
		if ($blogs) {
			$mydata = get_option (self::DEF_STRING . '_' . $post->ID);
			$options = $this->get_options ();
			foreach ($blogs as $language => $blog) {
				switch_to_blog ($blog->userblog_id);
				$temp = get_option (self::DEF_STRING);
				$title = (isset ($temp['description']) ? $temp['description'] : $language);
				if (empty ($option['display'])) {
					$link = sprintf (
						'<img src="%s" alt="%s"/> %s',
						$this->get_image_url ($language), $language, $title 
					);
				} elseif ($option['display'] == 1) {
					$link = sprintf (
						'<img src="%s" alt="%s"/>',
						$this->image_url ($language), $title
					); 
				} else {
					$link = $title;
				}
				$output .= sprintf (
					'%s<a href="%s" title="%s">%s</a>%s',
					(isset ($options['before_item']) ? $options['before_item'] : ''),
					((is_single () || is_page ()) && !empty ($mydata[$language]) ? get_permalink ($mydata[$language]) : site_url ()),
					$title,
					$link,
					(isset ($options['after_item']) ? $options['after_item'] : '') 
				);
			}
			$output = sprintf (
				'%s%s%s',
				(isset ($options['before_list']) ? $options['before_list'] : ''),
				$output,
				(isset ($options['after_list']) ? $options['after_list'] : '') 
			);
			restore_current_blog ();
		}
		return $output;
	}

}

?>
