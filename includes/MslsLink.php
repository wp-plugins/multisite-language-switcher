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
        return __( 'Flag and description', 'msls' );
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

    public function __get( $key ) {
        return(
            isset ($this->args[$key]) ?
            $this->args[$key] :
            null
        );
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
        return __( 'Description only', 'msls' );
    }

}

class MslsLinkImageOnly extends MslsLink {

    protected $format_string = '<img src="{src}" alt="{alt}"/>';

    static function get_description() {
        return __( 'Flag only', 'msls' );
    }

}

class MslsLinkTextImage extends MslsLink {

    protected $format_string = '{txt} <img src="{src}" alt="{alt}"/>';

    static function get_description() {
        return __( 'Description and flag', 'msls' );
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

    public function set_href( $id ) {
        $this->href = get_edit_post_link( $id );
    }

    public function __toString() {
        return $this->get_a();
    }

    public function get_img() {
        return sprintf(
            '<img alt="%s" src="%s" />',
            $this->language,
            $this->src
        );
    }

    protected function get_a() {
        if ( !empty( $this->href ) ) {
            $href  = $this->href;
            $title = sprintf(
                __( 'Edit the translation in the %s-blog' ),
                $this->language
            );
        }
        else {
            $href  = $this->get_edit_new();
            $title = sprintf(
                __( 'Create a new translation in the %s-blog' ),
                $this->language
            );
        }
        return sprintf(
            '<a title="%s" href="%s">%s</a>&nbsp;',
            $title,
            $href,
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

    public function set_href( $id ) {
        $this->href = get_edit_term_link( $id, 'category' );
    }

}

class MslsAdminIconTag extends MslsAdminIcon {

    protected $path = 'edit-tags.php?taxonomy=post_tag';

    public function set_href( $id ) {
        $this->href = get_edit_term_link( $id, 'post_tag' );
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
