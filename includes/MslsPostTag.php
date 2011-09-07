<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsMain.php';
require_once dirname( __FILE__ ) . '/MslsOptions.php';
require_once dirname( __FILE__ ) . '/MslsLink.php';

class MslsPostTag extends MslsMain implements IMslsMain {

    public $taxonomy;

    static function init() {
        $obj = new self();
        if ( !$obj->is_excluded() && isset( $_REQUEST['taxonomy'] ) ) {
            $obj->taxonomy = $_REQUEST['taxonomy'];
            if ( in_array( $obj->taxonomy, array( 'category', 'post_tag' ) ) ) {
                add_action( "{$obj->taxonomy}_edit_form_fields", array( $obj, 'add' ) );
                add_action( "{$obj->taxonomy}_add_form_fields", array( $obj, 'add' ) );
                add_action( "edited_{$obj->taxonomy}", array( $obj, 'set' ) );
            }
        }
        return $obj;
    }

    public function add( $tag ) {
        $term_id = ( is_object( $tag ) ? $tag->term_id : 0 );
        $blogs = $this->get_blogs();
        if ( $blogs ) {
            printf(
                '<tr><th colspan="2"><strong>%s</strong></th></tr>',
                __( 'Multisite Language Switcher', MSLS_DEF_STRING )
            );
            $mydata = new MslsTermOptions( $term_id );
            foreach ( $blogs as $language => $blog ) {
                switch_to_blog( $blog->userblog_id );
                $options   = '';
                $terms     = get_terms( $this->taxonomy );
                $edit_link = MslsAdminIcon::create( $this->taxonomy );
                $edit_link->set_language( $language );
                $edit_link->set_src( $this->get_flag_url( $language ) );
                if ( !empty( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $selected = '';
                        if ( $term->term_id == $mydata->$language ) {
                            $selected = 'selected="selected"';
                            $edit_link->set_href( get_edit_term_link( $term->term_id, $this->taxonomy ) );
                        }
                        $options .= sprintf(
                            '<option value="%s"%s>%s</option>',
                            $term->term_id,
                            $selected,
                            $term->name
                        );
                    }
                }
                printf(
                    '<tr class="form-field"><th scope="row" valign="top"><label for="%s[%s]">%s </label></th><td><select style="width:25em;" name="%s[%s]"><option value=""></option>%s</select></td>',
                    MSLS_DEF_STRING,
                    $language,
                    $edit_link,
                    MSLS_DEF_STRING,
                    $language,
                    $options
                );
                restore_current_blog();
            }
        }
    }

    public function set( $term_id ) {
        if ( !current_user_can( 'manage_categories' ) ) return;
        $this->save( $term_id, 'MslsTermOptions' );
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
