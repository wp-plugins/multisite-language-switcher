<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class MslsRegistry {

    private static $objects = array();
    private static $instance;

    private function __construct() { }

    private function __clone() { }

    private function get( $key ) {
        if ( isset( $this->objects[$key] ) ) {
            return $this->objects[$key];
        }
        return null;
    }

    private function set( $key, $instance ) {
        $this->objects[$key] = $instance;
    }

    public static function singleton() {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get_object( $key ) {
        return self::singleton()->get( $key );
    }

    public static function set_object( $key, $instance ) {
        return self::singleton()->set( $key, $instance );
    }

}

class MslsRegistryInstance {

    /**
     * @return object
     */
    final public static function instance() {
        $registry = MslsRegistry::singleton();
        $cls      = get_class();
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
