<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsMain.php';

class MslsCustomColumn extends MslsMain implements IMslsMain {

    static function init() {
        $obj = new self();
        if ( !$obj->is_excluded() ) {
            add_filter( 'manage_pages_columns' , array( $obj, 'manage' ) );
            add_filter( 'manage_posts_columns' , array( $obj, 'manage' ) );
            add_action( 'manage_pages_custom_column' , array( $obj, 'pages_columns' ), 10, 2 );
            add_action( 'manage_posts_custom_column' , array( $obj, 'posts_columns' ), 10, 2 );
        }
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

    public function pages_columns( $column_name, $post_id ) {
        $this->columns( 'page', $column_name, $post_id );
    }

    public function posts_columns( $column_name, $post_id ) {
        $this->columns( 'post', $column_name, $post_id );
    }

    protected function columns( $type, $column_name, $post_id ) {
        $blogs = $this->get_blogs();
        if ( $blogs && in_array( $column_name, array_keys( $blogs ) ) ) {
            $mydata = new MslsPostOptions( $post_id );
            switch_to_blog( $blogs[$column_name]->userblog_id );
            $edit_link = MslsAdminIcon::create( $type );
            $edit_link->set_language( $column_name );
            if ( $mydata->has_value( $column_name ) ) {
                $edit_link->set_src( $this->get_url( 'images' ) . '/pencil.png' );
                $edit_link->set_href( get_edit_post_link( $mydata->$column_name ) );
            }
            else {
                $edit_link->set_src( $this->get_url( 'images' ) . '/add.png' );
            }
            echo $edit_link;
            restore_current_blog();
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
