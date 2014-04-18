<?php
/**
 * MslsWidget
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * The standard widget of the Multisite Language Switcher
 * @package Msls
 */
class MslsWidget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			false,
			$name = __( 'Multisite Language Switcher', 'msls' )
		);
	}

	/**
	 * Output of the widget in the frontend
	 * @param array $args
	 * @param array $instance
	 * @user MslsOutput
	 */
	public function widget( $args, $instance ) {
		$title   = $instance['title'];
		$content = MslsOutput::init()->__toString();

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		if ( $title ) {
			$title = $args['before_title'] . esc_attr( $title ) . $args['after_title'];
		}

		if ( '' == $content ) {
			$content = __( 'No available translations found', 'msls' );
		}

		echo $args['before_widget'], $title, $content, $args['after_widget'];  // xss ok
	}

	/**
	 * Update widget in the backend
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Display an input-form in the backend
	 * @param array $instance
	 */
	public function form( $instance ) {
		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'title' ),
			__( 'Title', 'msls' ),
			$this->get_field_name( 'title' ),
			( isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '' )
		);
	}

}
