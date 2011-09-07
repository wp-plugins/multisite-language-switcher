<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsMain.php';
require_once dirname( __FILE__ ) . '/MslsOptions.php';
require_once dirname( __FILE__ ) . '/MslsLink.php';

class MslsOutput extends MslsMain implements IMslsMain {

    static function init() {
        return new self();
    }

    public function is_content_filter() {
        return(
            $this->options->content_filter == 1 ?
            true :
            false
        );
    }

    public function get( $display, $exists = false ) {
        $arr = array();
        if ( !$this->is_excluded() ) {
            $blogs = $this->get_blogs();
            if ( $blogs ) {
                $mydata = MslsOptionsFactory::create();
                $link   = MslsLink::create( $display );
                if ( $this->options->output_current_blog && false == $exists ) {
                    $language       = $this->get_language();
                    $link->txt      = (
                        isset( $this->options->description ) ?
                        $this->options->description :
                        $language
                    );
                    $link->src      = $this->get_flag_url( $language );
                    $link->alt      = $language;
                    $sort_key       = (
                        $this->options->sort_by_description ?
                        $link->get_txt() :
                        $language
                    );
                    $arr[$sort_key] = sprintf(
                        '<a href="%s" title="%s">%s</a>',
                        $mydata->get_current_link(),
                        $link->get_txt(),
                        $link
                    );
                }
                foreach ( $blogs as $language => $blog ) {
                    if ( true == $exists && !$mydata->has_value( $language ) )
                        continue;
                    switch_to_blog( $blog->userblog_id );
                    $temp           = new MslsOptions;
                    $link->txt      = (
                        isset( $temp->description ) ?
                        $temp->description :
                        $language
                    );
                    $link->src      = $this->get_flag_url( $language );
                    $link->alt      = $language;
                    $sort_key       = (
                        $this->options->sort_by_description ?
                        $link->get_txt() :
                        $language
                    );
                    $arr[$sort_key] = sprintf(
                        '<a href="%s" title="%s">%s</a>',
                        $mydata->get_permalink( $language ),
                        $link->get_txt(),
                        $link
                    );
                    restore_current_blog();
                }
                ksort( $arr );
            }
        }
        return $arr;
    }

    public function __toString() {
        $arr = $this->get(
            (int) $this->options->display,
            (bool) $this->options->only_with_translation
        );
        $str = '';
        if ( !empty( $arr ) ) {
            $str = $this->options->before_output .
                $this->options->before_item .
                implode(
                    $this->options->after_item . $this->options->before_item,
                    $arr
                ) .
                $this->options->after_item .
                $this->options->after_output;
        }
        return $str;
    }

}

class MslsWidget extends WP_Widget {

    public function __construct() {
        parent::__construct( false, $name = __( 'Multisite Language Switcher', MSLS_DEF_STRING ) );
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;
        $obj = new MslsOutput();
        echo $obj;
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance          = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

    function form( $instance ) {
        $title = esc_attr( $instance['title'] );
        printf(
            '<p><label for="%s">%s:</label> <input class="widefat" id="%s" name="%s" type="text" value="%s" /></p>',
            $this->get_field_id( 'title' ),
            __( 'Title', MSLS_DEF_STRING ),
            $this->get_field_id( 'title' ),
            $this->get_field_name( 'title' ),
            $title
        );
    }

}

/**
 * Registers Widget
 */
function msls_widgets_init() {
    if ( get_option( MSLS_DEF_STRING ) ) {
        $obj = new MslsOutput();
        if ( !$obj->is_excluded() )
            register_widget( 'MslsWidget' );
    }
}
add_action( 'widgets_init', 'msls_widgets_init' );

function msls_content_filter( $content ) {
    $obj = new MslsOutput();
    if ( $obj->is_content_filter() ) {
        $links = $obj->get( 1, true );
        if ( !empty( $links ) ) {
            if ( count( $links ) > 1 ) {
                $last  = array_pop( $links );
                $links = sprintf(
                    __( '%s and %s', MSLS_DEF_STRING ),
                    implode( ', ', $links ),
                    $last
                );
            } else {
                $links = $links[0];
            }
            $content .= '<p id="msls">' .
                sprintf(
                    __( 'This post is also available in %s.', MSLS_DEF_STRING ),
                    $links
                ) .
                '</p>';
        }
    }
    return $content;
}
add_filter( 'the_content', 'msls_content_filter' );

/**
 * Get the output for using the links to the translations in your code
 * 
 * @return string
 */
function get_the_msls() {
    $obj = new MslsOutput();
    return( sprintf( '%s', $obj ) );
}

/**
 * Output the links to the translations in your template
 * 
 * @uses get_the_msls
 */
function the_msls() {
    echo get_the_msls();
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
