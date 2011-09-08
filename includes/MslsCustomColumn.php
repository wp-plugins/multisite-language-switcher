<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsMain.php';

class MslsCustomColumn extends MslsMain implements IMslsMain {

    static function init() {
        $obj = new self();
        add_filter( 'manage_pages_columns' , array( $obj, 'manage' ) );
        add_filter( 'manage_posts_columns' , array( $obj, 'manage' ) );
        add_action( 'manage_pages_custom_column' , array( $obj, 'post_columns' ), 10, 2 );
        add_action( 'manage_posts_custom_column' , array( $obj, 'post_columns' ), 10, 2 );
        return $obj;
    }

    function manage( $columns ) {
        $blogs = $this->get_blogs();
        if ( $blogs ) {
            foreach ( array_keys( $blogs ) as $language ) {
                $icon = new MslsAdminIcon();
                $icon->set_language( $language );
                $icon->set_src( $this->get_flag_url( $language, true ) );
                $columns[$language] = $icon->get_img();
            }
        }
        return $columns;
    }

    function columns( $column_name, $post_id ) {
        $blogs = $this->get_blogs();
        if ( $blogs ) {
            if ( in_array( $column_name, array_keys( $blogs ) ) ) {
                $mydata = new MslsPostOptions( $post_id );
                echo $mydata->$column_name;
            }
        }
    }

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
