<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MslsOptions.php
 *
 * @package Msls
 */

require_once dirname( __FILE__ ) . '/MslsRegistry.php';

/**
 * MslsOptionsFactory
 */
class MslsOptionsFactory {

    static function create() {
        if ( is_single() || is_page() ) {
            global $post;
            return new MslsPostOptions( $post->ID );
        } elseif ( is_category() ) {
            return new MslsCategoryOptions( get_query_var( 'cat' ) );
        } elseif ( is_tag() ) {
            return new MslsTermOptions( get_query_var( 'tag_id' ) );
        }
        return new MslsOptions();
    }

}

/**
 * MslsOptions
 */
class MslsOptions extends MslsRegistryInstance {

    protected $args;
    protected $name;
    protected $options  = array();
    protected $exists   = false;
    protected $sep      = '';
    protected $autoload = 'yes';
    protected $base;

    public function __construct() {
        $this->args   = func_get_args();
        $this->name   = 'msls' . $this->sep . implode( $this->sep, $this->args );
        $this->exists = $this->set( get_option( $this->name ) );
        $this->base   = $this->get_base();
    }

    /**
     * @param mixed $key
     * @return string
     */
    public function __get( $key ) {
        return (
            isset( $this->options[$key] ) ?
            $this->options[$key] :
            ''
        );
    }

    public function __set( $key, $value ) {
        if ( '' == $value ) {
            if (isset( $this->options[$key] ) )
                unset( $this->options[$key] );
        } else {
            $this->options[$key] = $value;
        }
    }

    public function __isset( $key ) {
        return isset( $this->options[$key] );
    }

    public function save( $arr ) {
        if ( $this->set( $arr ) ) {
            delete_option( $this->name );
            add_option( $this->name, $this->options, '', $this->autoload );
        }
    }

    public function set( $arr ) {
        if ( is_array( $arr ) ) {
            foreach ( $arr as $key => $value ) {
                $this->__set( $key, $value );
            }
            return true;
        }
        return false;
    }

    protected function get_base() {
        return null;
    }

    public function get_permalink( $language ) {
        $postlink = $this->get_postlink( $language );
        return(
            $postlink ?
            $postlink :
            site_url()
        );
    }

    public function get_postlink( $language ) {
        return null;
    }

    public function get_current_link() {
        return site_url();
    }

    public function has_value( $value ) {
        return !empty( $this->options[$value] ) ? true : false;
    }

    public function is_excluded() {
        if ( false == $this->exists )
            return true;
        return $this->has_value( 'exclude_current_blog' );
    }

    public function is_content_filter() {
        return $this->has_value( 'content_filter' );
    }

}

/**
 * MslsPostOptions
 */
class MslsPostOptions extends MslsOptions {

    protected $sep      = '_';
    protected $autoload = 'no';

    public function get_postlink( $language ) {
        return(
            $this->has_value( $language ) ? 
            get_permalink( $this->options[$language] ) :
            null
        );
    }

    public function get_current_link() {
        return get_permalink( (int) $this->args[0] );
    }

}

/**
 * MslsTermOptions
 */
class MslsTermOptions extends MslsOptions {

    protected $sep          = '_term_';
    protected $autoload     = 'no';
    protected $base_option  = 'tag_base';
    protected $base_defined = 'tag';
    protected $taxonomy     = 'post_tag';

    protected function get_base() {
        $base = get_option( $this->base_option );
        return(
            !empty ($base) ?
            $base :
            $this->base_defined
        );
    }

    public function get_postlink( $language ) {
        if ( $this->has_value( $language ) ) {
            $url = get_term_link(
                (int) $this->options[$language], 
                $this->taxonomy
            );
            if ( empty( $url ) || !is_string( $url ) )
                return null;
            $base = $this->get_base();
            if ( $this->base != $base ) {
                $search  = '/' . $this->base . '/';
                $replace = '/' . $base . '/';
                $count   = 1;
                $url     = str_replace( $search, $replace, $url, $count );
            }
            return $url;
        }
        return null;
    }

    /**
     * @return string
     */
    public function get_current_link() {
        return get_tag_link( (int) $this->args[0] );
    }

}

/**
 * MslsCategoryOptions
 */
class MslsCategoryOptions extends MslsTermOptions {

    protected $base_option  = 'category_base';
    protected $base_defined = 'category';
    protected $taxonomy     = 'category';

    /**
     * @return string
     */
    public function get_current_link() {
        return get_category_link( (int) $this->args[0] );
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
