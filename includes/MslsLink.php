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

class MslsAdminIcon {

    protected $language;
    protected $src;
    protected $href;
    protected $blog_id;
    protected $path = 'post-new.php';

    static function create( $type ) {
        if ( 'page' == $type ) {
            return new MslsAdminIconPage;
        }
        elseif ( 'category' == $type ) {
            return new MslsAdminIconCategory;
        }
        elseif ( 'post_tag' == $type ) {
            return new MslsAdminIconTag;
        }
        return new MslsAdminIcon;
    }

    public function __construct() {
        $this->blog_id = get_current_blog_id();
    }

    public function set_language( $language ) {
        $this->language = $language;
    }

    public function set_src( $src ) {
        $this->src = $src;
    }

    public function set_href( $href ) {
        $this->href = $href;
    }

    public function __toString() {
        return $this->get_a();
    }

    protected function get_img() {
        return sprintf(
            '<img alt="%s" src="%s" />',
            $this->language,
            $this->src
        );
    }

    protected function get_path() {
        return sprintf(
            '<a href="%s">%s</a>',
            ( !empty( $this->href ) ? $this->href : $this->get_edit_new() ),
            $this->get_img()
        );
    }

    protected function get_edit_new() {
        return get_admin_url( $this->blog_id, $this->path );
    }

}

class MslsAdminIconPage extends MslsAdminIcon {

    protected $path = 'post-new.php?post_type=page';

}

class MslsAdminIconCategory extends MslsAdminIcon {

    protected $path = 'edit-tags.php?taxonomy=category';

}

class MslsAdminIconTag extends MslsAdminIcon {

    protected $path = 'edit-tags.php?taxonomy=post_tag';

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
