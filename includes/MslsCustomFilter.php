<?php
/**
 * MslsCustomFilter
 * @author Maciej Czerpiński <contact@speccode.com>
 * @since 0.9.9
 */

/**
 * Adding custom filter to posts/pages table.
 * @package Msls
 */
class MslsCustomFilter extends MslsMain {

	/**
	 * Init
	 * @return MslsCustomFilter
	 */
	static function init() {
		$obj     = new self();
		$options = MslsOptions::instance();
		if ( ! $options->is_excluded() ) {
			$post_type = MslsPostType::instance()->get_request();
			if ( ! empty( $post_type ) ) {
				add_action( 'restrict_manage_posts', array( $obj, 'add_filter' ) );
				add_filter( 'parse_query',           array( $obj, 'execute_filter' ) );
			}
		}
		return $obj;
	}

	/**
	 * Echo's select tag with list of blogs
	 */
	public function add_filter() {
		$id    = ( isset( $_GET['msls_filter'] ) ? (int) $_GET['msls_filter'] : '' );
		$blogs = MslsBlogCollection::instance()->get();
		if ( $blogs ) {
			echo '<select name="msls_filter" id="msls_filter">';
			echo '<option value="">' . __( 'Show all blogs', 'msls' ) . '</option>';
			foreach ( $blogs as $blog ) {
				printf(
					'<option value="%d" %s>%s</option>',
					$blog->userblog_id,
					selected( $id, $blog->userblog_id, false ),
					sprintf( __( 'Not translated in the %s-blog', 'msls' ), $blog->get_description() )
				);
			}
			echo '</select>';
		}
	}

	/**
	 * Execute filter. Exclude translated posts from WP_Query
	 * @uses $wpdb
	 * @return false or WP_Query object
	 */
	public function execute_filter( $query ) {
		$blogs = MslsBlogCollection::instance()->get();

		//some "validation"
		if ( ! isset( $_GET['msls_filter'] ) ) {
			return false;
		}
		$id = (int) $_GET['msls_filter'];
		if ( isset( $blogs[$id] ) ) {
			global $wpdb;

			//load post we need to exclude (already have translation) from search query
			$sql   = $wpdb->prepare(
				"SELECT option_id, option_name FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value LIKE %s",
				'msls_%',
				'%"' . $blogs[$id]->get_language() . '"%'
			);
			$posts = $wpdb->get_results( $sql );

			$exclude_ids = array();
			foreach ( $posts as $post ) {
				$exclude_ids[] = substr( $post->option_name, 5 );
			}
			$query->query_vars['post__not_in'] = $exclude_ids;
			return $query;
		}
		return false;
	}

}
