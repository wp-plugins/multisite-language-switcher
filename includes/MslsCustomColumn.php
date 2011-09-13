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
            add_action( 'manage_category_custom_column' , array( $obj, 'taxonomy_columns' ), 10, 2 );
            add_action( 'manage_post_tag_custom_column' , array( $obj, 'taxonomy_columns' ), 10, 2 );
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
            $columns['msls'] = implode( '&nbsp;', $arr );
        }
        return $columns;
    }

    public function pages_columns( $column_name, $post_id ) {
        $this->columns( 'page', $column_name, $post_id );
    }

    public function posts_columns( $column_name, $post_id ) {
        $this->columns( 'post', $column_name, $post_id );
    }

    public function taxonomy_columns( $column_name, $post_id ) {
        echo '1&nbsp;';
    }

    protected function columns( $type, $column_name, $post_id ) {
        $blogs = $this->blogs->get();
        if ( $blogs && 'msls' == $column_name ) {
            $arr    = array();
            $mydata = new MslsPostOptions( $post_id );
            foreach ( $blogs as $language => $blog ) {
                switch_to_blog( $blog->userblog_id );
                $edit_link = MslsAdminIcon::create( $type );
                $edit_link->set_language( $language );
                if ( $mydata->has_value( $language ) ) {
                    $edit_link->set_src( $this->get_url( 'images' ) . '/link_edit.png' );
                    $edit_link->set_href( get_edit_post_link( $mydata->$language ) );
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
