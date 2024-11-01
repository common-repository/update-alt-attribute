<?php

namespace MauiMarketing\UpdateAltAtribute\Core;

class Settings{
    
    private static $instance = null;
    
    private $input_defaults         = [];
    private $checkbox_defaults      = [];
    private $checkboxes_defaults_no = [];
    
    private $defaults               = [];
    private $negative_defaults      = [];
    
    private $settings               = [];
    
    private function __construct(){
        
        $this->input_defaults = [
            "process_step"          => "2",
            "exclude_words"         => "",
        ];
        
        $this->checkbox_defaults = [
            "replace_hyphen"        => "yes",
            "replace_underscore"    => "yes",
            'capitalize_words'      => "no",
            
            "remove_numbers"        => "yes",
            'remove_comma'          => "no",
            'remove_period'         => "no",
            
            "update_title"          => "yes",
            "update_description"    => "yes",
            "update_caption"        => "yes",
        ];
        
        $this->checkbox_defaults      = apply_filters( "uaa_checbox_defaults", $this->checkbox_defaults );
        $this->checkboxes_defaults_no = array_fill_keys( array_keys( $this->checkbox_defaults ), "no" );
        
        $this->defaults          = array_merge( $this->input_defaults, $this->checkbox_defaults );
        $this->negative_defaults = array_merge( $this->input_defaults, $this->checkboxes_defaults_no );
        
        $this->settings = get_option('uaa_settings', $this->defaults );
        $this->settings = wp_parse_args( $this->settings, $this->defaults );
        
        $this->clean_settings();
        
    }
    
    public static function get_instance(){
        
        if( is_null( self::$instance ) ){
            
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone(){}
    private function __wakeup(){}
    
    
    function clean_settings(){
        
        foreach( $this->settings as $key => $value ){
            
            if( ! in_array( $key, array_keys( $this->defaults ) ) ){
                
                unset( $this->settings[ $key ]);
            }
        }
        
    }
    
    
    function load( $setting_id = false ){
        
        if( $setting_id === false ){
            
            return $this->settings;
            
        } else {
            
            return ! empty( $this->settings[ $setting_id ] ) ? $this->settings[ $setting_id ] : false;
        }
    }
    
    function save( $some_settings ){
        
        $this->settings = wp_parse_args( $some_settings, $this->negative_defaults );
        
        $this->clean_settings();
        
        update_option( 'uaa_settings', $this->settings );
        
        return $this->settings;
    }
    
    
    function get_checkbox_html( $args ){
        
        /* $args = [
            'setting_id',
            'Description',
            'Help text',// optional
        ]; */
        
        if( empty( $args[0] ) || empty( $this->settings[ $args[0] ] ) ){
            // return "";
        }
        
        $row_class = "";
        $help_text = ! empty( $args[2] ) ? '<span class="settings_description">' . $args[2] . '</span>' : "";
        $checked   = ! empty( $this->settings[ $args[0] ] ) && $this->settings[ $args[0] ] === "yes" ? ' checked' : '';
        
        if( ! in_array( $args[0], array_keys( $this->settings ) ) ){
            
            $help_text  = '<span class="settings_description">';
            $help_text .=       '<span class="dashicons dashicons-arrow-right"></span> ';
            $help_text .=       '<a href="?page=uaa_automatic_updaters&tab=go_pro">Available in the Pro version</a>';
            $help_text .= '</span>';
            
            $row_class  = " needs_pro";
            $checked    = " checked";
        }
        
        $html    = '
        
        <div class="settings_row' . $row_class . '">
            
            <span class="settings_label">' . $args[1] . '</span>
            
            <div class="apple_toggle">
                <input type="checkbox" id="uaa_' . $args[0] . '" name="uaa_settings[' . $args[0] . ']" value="yes"' . $checked . '/>
                <div class="toggle-handle"></div>
                <label for="uaa_' . $args[0] . '" onclick></label>
            </div>
            ' . $help_text . '
        </div>
        
        ';
        
        return $html;
    }
    
    function echo_checkbox_html( $args ){
        
        echo $this->get_checkbox_html( $args );
    }
    
    
    function get_input_number_html( $args ){
        
        /* $args = [
            'setting_id',
            'Description',
            'Help text',// optional
        ]; */
        
        if( empty( $args[0] ) || empty( $this->settings[ $args[0] ] ) ){
            return "";
        }
        
        $checked = $this->settings[ $args[0] ] === "yes" ? ' checked' : '';
        
        $help = ! empty( $args[2] ) ? '<span class="settings_description">' . $args[2] . '</span>' : "";
        
        $html = '
        
        <div class="settings_row">
            
            <span class="settings_label">' . $args[1] . '</span>
            
            <div class="">
                <input type="number" id="uaa_' . $args[0] . '" name="uaa_settings[' . $args[0] . ']" value="' . esc_attr( $this->settings[ $args[0] ] ) . '"/>
            </div>
            ' . $help . '
        </div>
        
        ';
        
        return $html;
    }
    
    function echo_input_number_html( $args ){
        
        echo $this->get_input_number_html( $args );
    }

    function get_input_text_html( $args ){
        
        /* $args = [
            'setting_id',
            'Description',
            'Help text',// optional
        ]; */
        
        if( empty( $args[0] ) ){
            return "";
        }
        
        $checked = $this->settings[ $args[0] ] === "yes" ? ' checked' : '';
        
        $help_text = ! empty( $args[2] ) ? '<span class="settings_description">' . $args[2] . '</span>' : "";
        
        $html = '
        
        <div class="settings_row">
            
            <span class="settings_label">' . $args[1] . '</span>
            
            <div class="">
                <textarea id="uaa_' . $args[0] . '" name="uaa_settings[' . $args[0] . ']" rows="4" cols="50">' . esc_attr( $this->settings[ $args[0] ] ) . '</textarea>
            </div>
            ' . $help_text . '
        </div>
        
        ';
        
        return $html;
    }

    function echo_input_text_html( $args ){
        
        echo $this->get_input_text_html( $args );
    }
    
}

function settings(){
    
    return Settings::get_instance();
}
