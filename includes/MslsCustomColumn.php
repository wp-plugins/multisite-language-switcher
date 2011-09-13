<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsMain.php';
require_once dirname( __FILE__ ) . '/MslsOptions.php';
require_once dirname( __FILE__ ) . '/MslsLink.php';

class MslsCustomColumn extends MslsMain implements IMslsMain {

    static function init() {
        $options = MslsOptions::instance();
        if ( !$options->is_excluded() ) {
            $obj = new self();
            add_filter( 'manage_pages_columns' , array( $obj, 'manage' ) );
            add_filter( 'manage_posts_columns' , array( $obj, 'manage' ) );
            add_filter( 'manage_edit-category_columns' , array( $obj, 'manage' ) );
            add_filter( 'manage_edit-post_tag_columns' , array( $obj, 'manage' ) );
            add_action( 'manage_pages_custom_column' , array( $obj, 'pages_columns' ), 10, 2 );
            add_action( 'manage_posts_custom_column' , array( $obj, 'posts_columns' ), 10, 2 );
            add_action( 'manage_category_custom_column' , array( $obj, 'category_columns' ), 10, 3 );
            add_action( 'manage_post_tag_custom_column' , array( $obj, 'post_tag_columns' ), 10, 3s );
        }
    }

    function manage( $columns ) {
        $blogs = $this->blogs->get();
        if ( $blogs ) {
            $arr = array();
            foreach ( $blogs as $blog ) {
                $language = $blog->get_language();
                $icon     = new MslsAdminIcon();
                $icon->set_language( $language );
                $icon->set_src( $this->get_flag_url( $language, true ) );
                $arr[] = $icon->get_img();
            }
            $columns['mslscol'] = implode( '&nbsp;', $arr );
        }
        return $columns;
    }

    public function pages_columns( $column_name, $post_id ) {
        $this->columns( 'page', $column_name, $post_id );
    }

    public function posts_columns( $column_name, $post_id ) {
        $this->columns( 'post', $column_name, $post_id );
    }

    public function category_columns( $column_name, $post_id ) {
        $this->columns( 'category', $column_name, $post_id );
    }

    public function post_tag_columns( $column_name, $post_id ) {
        $this->columns( 'post_tag', $column_name, $post_id );
    }

    protected function columns( $type, $column_name, $post_id ) {
        $blogs = $this->blogs->get();
        if ( $blogs && 'mslscol' == $column_name ) {
            $arr    = array();
            $mydata = MslsOptionsFactory::create( $type, $post_id );
            foreach ( $blogs as $blog ) {
                switch_to_blog( $blog->userblog_id );
                $language = $blog->get_language();
                $edit_link = MslsAdminIcon::create( $type );
                $edit_link->set_language( $language );
                if ( $mydata->has_value( $language ) ) {
                    $edit_link->set_src( $this->get_url( 'images' ) . '/link_edit.png' );
                    $edit_link->set_href( $mydata->$language );
                }
                else {
                    $edit_link->set_src( $this->get_url( 'images' ) . '/link_add.png' );
                }
                $arr[] = sprintf( '%s', $edit_link );
                restore_current_blog();
            }
            echo implode( '&nbsp;', $arr );
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
