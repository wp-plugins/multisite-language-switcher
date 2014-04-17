<?php
/**
 * MslsTaxonomy
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Content types: Taxonomies (Tags, Categories, ...)
 * @package Msls
 */
class MslsTaxonomy extends MslsContentTypes implements IMslsRegistryInstance {

	/**
	 * Post type
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->types = array_merge(
			array( 'category', 'post_tag' ), // no 'post_link' here
			get_taxonomies(
				array( 'public' => true, '_builtin' => false ),
				'names',
				'and'
			)
		);
		if ( ! empty( $_REQUEST['taxonomy'] ) ) {
			$this->request = esc_attr( $_REQUEST['taxonomy'] );
		}
		else {
			$this->request = get_query_var( 'taxonomy' );
		}
		if ( ! empty( $_REQUEST['post_type'] ) ) {
			$this->post_type = esc_attr( $_REQUEST['post_type'] );
		}
	}

	/**
	 * Get the requested post_type of the taxonomy
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Check for taxonomy
	 * @return bool
	 */
	public function is_taxonomy() {
		return true;
	}

	/**
	 * Get or create a instance of MslsTaxonomy
	 * @return MslsBlogCollection
	 */
	static function instance() {
		$registry = MslsRegistry::singleton();
		$cls      = __CLASS__;
		$obj      = $registry->get_object( $cls );
		if ( is_null( $obj ) ) {
			$obj = new $cls;
			$registry->set_object( $cls, $obj );
		}
		return $obj;
	}

}
