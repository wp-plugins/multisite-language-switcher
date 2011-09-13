<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsRegistry.php';

class MslsBlog {

    private $obj;
    private $description;
    private $language;

    public function __construct( $obj, $description ) {
        $this->obj         = $obj;
        $this->description = (string) $description;
        $this->language    = (string) get_blog_option( $this->obj->userblog_id, 'WPLANG' );
    }

    final public function __get( $key ) {
        return(
            isset( $this->obj->$key ) ?
            $this->obj->$key :
            null
        );
    } 

    public function get_description() {
        return(
            !empty( $this->description ) ?
            $this->description :
            $this->get_language()
        );
    }

    public function get_language() {
        return(
            !empty( $this->language ) ?
            $this->language :
            'us'
        );
    }

    public static function _cmp( $a, $b ) {
        if ( $a == $b ) {
            return 0;
        }
        return( $a < $b ? (-1) : 1 );
    }

    public static function language( $a, $b ) {
        return( self::_cmp( $a->get_language(), $b->get_language() ) );
    }

    public static function description( $a, $b ) {
        return( self::_cmp( $a->get_description(), $b->get_description() ) );
    }

}

class MslsBlogCollection implements IMslsRegistryInstance {

    private $current_blog_id;
    private $current_blog_output;
    private $objects = array();
    private $objects_order = 'language';

    public function __construct() {
        $options                   = MslsOptions::instance();
        $this->current_blog_id     = get_current_blog_id();
        $this->current_blog_output = (bool) $options->output_current_blog;
        if ( true == (bool) $options->sort_by_description )
            $this->objects_order = 'description';
        if ( !$options->is_excluded() ) {
            $user_id = get_user_id_from_string(
                get_blog_option( $this->current_blog_id, 'admin_email' )
            );
            foreach ( get_blogs_of_user( $user_id ) as $blog ) {
                if ( $blog->userblog_id != $this->current_blog_id ) {
                    $temp = get_blog_option( $blog->userblog_id, 'msls' );
                    if ( is_array( $temp ) && empty( $temp['exclude_current_blog'] ) ) {
                        $this->objects[$blog->userblog_id] = new MslsBlog(
                            $blog,
                            $temp['description']
                        );
                    }
                }
                else {
                    $this->objects[$this->current_blog_id] = new MslsBlog(
                        $blog,
                        $options->description
                    );
                }
            }
        }
    }

    public function get_current_blog_id() {
        return $this->current_blog_id;
    }

    public function has_current_blog() {
        return( isset( $this->objects[$this->current_blog_id] ) ? true : false );
    }

    public function get( $frontend = false ) {
        $objects = $this->objects;
        if ( (!$frontend || !$this->current_blog_output) && $this->has_current_blog() )
            unset( $objects[$this->current_blog_id] );
        $objects = apply_filters( 'msls_blog_collection_get', $objects );
        usort( $objects, array( 'MslsBlog', $this->objects_order ) );
        return $objects;
    }

    public static function instance() {
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

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
