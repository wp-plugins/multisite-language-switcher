<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname( __FILE__ ) . '/MslsMain.php';
require_once dirname( __FILE__ ) . '/MslsOptions.php';
require_once dirname( __FILE__ ) . '/MslsLink.php';

class MslsAdmin extends MslsMain implements IMslsMain {

    static function init() {
        $obj = new self();
        add_options_page(
            __( 'Multisite Language Switcher', MSLS_DEF_STRING ),
            __( 'Multisite Language Switcher', MSLS_DEF_STRING ),
            'manage_options',
            __CLASS__,
            array( $obj, 'render' )
        );
        add_action( 'admin_init', array( $obj, 'register' ) );
        return $obj;
    }

    public function render() {
        printf(
            '<div class="wrap"><div class="icon32" id="icon-options-general"><br></div><h2>%s</h2><p>%s</p><form action="options.php" method="post">',
            __( 'Multisite Language Switcher Options', MSLS_DEF_STRING ),
            __( 'To achieve maximum flexibility, you have to configure each blog separately.', MSLS_DEF_STRING )
        );
        settings_fields( MSLS_DEF_STRING );
        do_settings_sections( __CLASS__ );
        printf(
            '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="%s" /></p></form></div>',
            __( 'Update', MSLS_DEF_STRING )
        );
    }

    public function register() {
        register_setting( MSLS_DEF_STRING, MSLS_DEF_STRING, array( $this, 'validate' ) );
        add_settings_section(
            'section',
            __( 'Main Settings', MSLS_DEF_STRING ),
            array( $this, 'section' ),
            __CLASS__
        );
        add_settings_field( 'display', __( 'Display', MSLS_DEF_STRING ), array( $this, 'display' ), __CLASS__, 'section' );
        add_settings_field( 'sort_by_description', __( 'Sort output by description', MSLS_DEF_STRING ), array( $this, 'sort_by_description' ), __CLASS__, 'section' );
        add_settings_field( 'exclude_current_blog', __( 'Exclude this blog from output', MSLS_DEF_STRING ), array( $this, 'exclude_current_blog' ), __CLASS__, 'section' );
        add_settings_field( 'output_current_blog', __( 'Display link to the current language', MSLS_DEF_STRING ), array( $this, 'output_current_blog' ), __CLASS__, 'section' );
        add_settings_field( 'description', __( 'Description', MSLS_DEF_STRING ), array( $this, 'description' ), __CLASS__, 'section' );
        add_settings_field( 'before_output', __( 'Text/HTML before the list', MSLS_DEF_STRING ), array( $this, 'before_output' ), __CLASS__, 'section' );
        add_settings_field( 'after_output', __( 'Text/HTML after the list', MSLS_DEF_STRING ), array( $this, 'after_output' ), __CLASS__, 'section' );
        add_settings_field( 'before_item', __( 'Text/HTML before each item', MSLS_DEF_STRING ), array( $this, 'before_item' ), __CLASS__, 'section' );
        add_settings_field( 'after_item', __( 'Text/HTML after each item', MSLS_DEF_STRING ), array( $this, 'after_item' ), __CLASS__, 'section' );
        add_settings_field( 'content_filter', __( 'Add hint for available translations', MSLS_DEF_STRING ), array( $this, 'content_filter' ), __CLASS__, 'section' );
        add_settings_field( 'content_priority', __( 'Hint priority', MSLS_DEF_STRING ), array( $this, 'content_priority' ), __CLASS__, 'section' );
    }

    public function section() {}

    public function display() {
        $items = '';
        foreach ( MslsLink::get_types_description() as $key => $value ) {
            $items .= sprintf(
                '<option value="%s"%s>%s</option>',
                $key, 
                ( $this->options->display == $key ? ' selected="selected"' : '' ), 
                $value
            );
        }
        printf(
            '<select id="display" name="%s[display]">%s</select>',
            MSLS_DEF_STRING, $items
        );
    }

    public function sort_by_description() {
        echo $this->render_checkbox( 'sort_by_description' );
    }

    public function exclude_current_blog() {
        echo $this->render_checkbox( 'exclude_current_blog' );
    }

    public function output_current_blog() {
        echo $this->render_checkbox( 'output_current_blog' );
    }

    public function description() {
        echo $this->render_input( 'description', '40' );
    }

    public function before_output() {
        echo $this->render_input( 'before_output' );
    }

    public function after_output() {
        echo $this->render_input( 'after_output' );
    }

    public function before_item() {
        echo $this->render_input( 'before_item' );
    }

    public function after_item() {
        echo $this->render_input( 'after_item' );
    }

    public function content_filter() {
        echo $this->render_checkbox( 'content_filter' );
    }

    public function content_priority() {
        $priority = (
            !empty ($this->options->content_priority) ? 
            $this->options->content_priority :
            10
        );
        $items    = '';
        foreach ( range( 1, 10 ) as $key ) {
            $items .= sprintf(
                '<option%s>%s</option>',
                ( $priority == $key ? ' selected="selected"' : '' ), 
                $key
            );
        }
        printf(
            '<select id="content_priority" name="%s[content_priority]">%s</select>',
            MSLS_DEF_STRING, $items
        );
    }

    public function render_checkbox( $key ) {
        return sprintf(
            '<input type="checkbox" id="%s" name="%s[%s]" value="1"%s/>',
            $key,
            MSLS_DEF_STRING,
            $key,
            ( $this->options->$key == 1 ? ' checked="checked"' : '' )
        );

    }

    public function render_input( $key, $size = '30' ) {
        return sprintf(
            '<input id="%s" name="%s[%s]" value="%s" size="%s"/>',
            $key,
            MSLS_DEF_STRING,
            $key,
            esc_attr( $this->options->$key ),
            $size
        );
    }

    public function validate( $input ) {
        if ( !is_numeric( $input['display'] ) ) $input['display'] = 0; 
        return $input;
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
