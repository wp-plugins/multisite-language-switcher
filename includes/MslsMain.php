<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsOptions.php';
require_once dirname( __FILE__ ) . '/MslsOutput.php';

interface IMslsMain {

    static function init();

}

class MslsMain {

    protected $user_id;
    protected $current_blog_id;
    protected $options;

    protected $blogs = null;

    static function activate() {
        if ( function_exists( 'is_multisite' ) && is_multisite() ) 
            return; 
        deactivate_plugins( __FILE__ );
        die(
            "This plugin needs the activation of the multisite-feature for working properly. Please read <a href='http://codex.wordpress.org/Create_A_Network'>this post</a> if you don't know the meaning.\n"
        );
    }

    static function deactivate() { }

    public function __construct() {
        $this->current_blog_id = get_current_blog_id();
        $this->user_id = get_user_id_from_string( get_blog_option( $this->current_blog_id, 'admin_email' ) );
        $this->options = new MslsOptions;
        load_plugin_textdomain( MSLS_DEF_STRING, false, dirname( MSLS_PLUGIN_PATH ) . '/languages/' );
    }

    public function get_blogs() {
        if ( is_null( $this->blogs ) ) {
            $this->blogs = array();
            foreach ( get_blogs_of_user( $this->user_id ) as $blog ) {
                if ( $blog->userblog_id != $this->current_blog_id ) {
                    $temp = get_blog_option( $blog->userblog_id, MSLS_DEF_STRING );
                    if ( $temp && empty( $temp['exclude_current_blog'] ) ) {
                        $language = $this->get_language( $blog->userblog_id );
                        $this->blogs[$language] = $blog;
                    }
                }
            }
            ksort( $this->blogs );
        }
        $this->blogs = apply_filters( 'mls_get_blogs_return', $this->blogs );
        return $this->blogs;
    }

    public function get_language( $blog_id = 0 ) {
        if ( 0 == $blog_id ) $blog_id = $this->current_blog_id;
        $language = get_blog_option( $blog_id, 'WPLANG' );
        return empty($language) ? 'us' : $language;
    }

    public function get_image_url( $language ) {
        $url = $this->options->image_url;
        if ( empty( $url ) ) {
            $url = sprintf(
                '%s/%s/flags',
                WP_PLUGIN_URL, 
                dirname( MSLS_PLUGIN_PATH )
            );
        }
        if ( 5 == strlen( $language ) ) 
            $language = strtolower( substr( $language, -2 ) );
        return sprintf(
            '%s/%s.png',
            $url,
            $language
        );
    }

    public function is_excluded() {
        return (bool) $this->options->exclude_current_blog;
    }

    protected function save( $id, $class ) {
        if ( isset( $_POST[MSLS_DEF_STRING] ) ) {
            $mydata  = $_POST[MSLS_DEF_STRING];
            $options = new $class( $id );
            $options->save( $mydata );
            $language = $this->get_language();
            $mydata[$language] = $id;
            foreach ( $this->get_blogs() as $language => $blog ) {
                if ( !empty( $mydata[$language] ) ) {
                    switch_to_blog( $blog->userblog_id );
                    $temp    = $mydata;
                    $options = new $class( $temp[$language] );
                    unset( $temp[$language] );
                    $options->save( $temp );
                    restore_current_blog();
                }
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
