<?php

require_once (dirname (__FILE__) . '/MslsMain.php');
require_once (dirname (__FILE__) . '/MslsOptions.php');
require_once (dirname (__FILE__) . '/MslsLink.php');

class MslsOutput extends MslsMain implements iMslsMain {

	static function init () {
		return new self ();
	}

	public function __toString () {
		global $post;
		$output = '';
		$blogs = $this->get_blogs ();
		if ($blogs) {
			$mydata = new MslsPostOptions ($post->ID);
			foreach ($blogs as $language => $blog) {
				switch_to_blog ($blog->userblog_id);
				$temp = new MslsOptions;
				$link = MslsLink::create ($this->options->display);
				$link->txt = (
					isset ($temp->description) ? 
					$temp->description : 
					$language
				);
				$link->src = $this->get_image_url ($language);
				$link->alt = $language;
				$output .= sprintf (
					'%s<a href="%s" title="%s">%s</a>%s',
					$this->options->before_item,
					$mydata->get_permalink ($language),
					$link->txt,
					$link,
					$this->options->after_item 
				);
			}
			$output = sprintf (
				'%s%s%s',
				$this->options->before_list,
				$output,
				$this->options->after_list
			);
			restore_current_blog ();
		}
		return $output;
	}

}

function the_msls () {
	$obj = new MslsOutput ();
	echo $obj;
}

?>
