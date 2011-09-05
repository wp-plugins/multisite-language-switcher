<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsMain.php';

class MslsLink {

    protected $args = array();
    protected $format_string = '<img src="{src}" alt="{alt}"/> {txt}';

    static function get_types() {
        return array( 
            '0' => 'MslsLink',
            '1' => 'MslsLinkTextOnly',
            '2' => 'MslsLinkImageOnly',
            '3' => 'MslsLinkTextImage',
        );
    }

    static function get_description() {
        return __( 'Flag and description', MSLS_DEF_STRING );
    }

    static function get_types_description() {
        $temp = array();
        foreach ( self::get_types() as $key => $class ) {
            $temp[$key] = call_user_func(
                array( $class, 'get_description' )
            );
        }
        return $temp;
    }
    
    public function create( $display ) {
        $types = self::get_types();
        if ( !in_array( $display, array_keys( $types ), true ) ) $display = 0;
        return new $types[$display];
    }

    public function __set( $key, $value ) {
        $this->args[$key] = $value;
    }

    public function get_txt() {
        return isset ($this->args['txt']) ? $this->args['txt'] : '';
    }

    public function __toString() {
        $temp = array();
        foreach ( array_keys( $this->args ) as $key ) {
            $temp[] = '{' . $key . '}';
        }
        return str_replace(
            $temp,
            $this->args,
            $this->format_string
        );
    }

}

class MslsLinkTextOnly extends MslsLink {

    protected $format_string = '{txt}';

    static function get_description() {
        return __( 'Description only', MSLS_DEF_STRING );
    }

}

class MslsLinkImageOnly extends MslsLink {

    protected $format_string = '<img src="{src}" alt="{alt}"/>';

    static function get_description() {
        return __( 'Flag only', MSLS_DEF_STRING );
    }

}

class MslsLinkTextImage extends MslsLink {

    protected $format_string = '{txt} <img src="{src}" alt="{alt}"/>';

    static function get_description() {
        return __( 'Description and flag', MSLS_DEF_STRING );
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
