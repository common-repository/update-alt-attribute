<?php

use MauiMarketing\UpdateAltAtribute\Core;

if( ! empty( $_POST['save_settings'] ) ){

    if ( wp_verify_nonce( $_POST['update_info_image_nonce'], 'submit_update_info_image' ) ) {

        $new_settings = ! empty( $_POST['uaa_settings'] ) ? $_POST['uaa_settings'] : [];

        Core\settings()->save( $new_settings );
    }
}

?>

<form id="mm_update_info_image" method="post" action="#">
    <?php wp_nonce_field( 'submit_update_info_image', 'update_info_image_nonce' ); ?> 
    <div>
    
        <div class="settings_section">Replacing</div>
        
        <?php
            Core\settings()->echo_checkbox_html([ 'replace_hyphen',     'Replace hyphens <pre>-</pre> with a single space'      ]);
            Core\settings()->echo_checkbox_html([ 'replace_underscore', 'Replace underscores <pre>_</pre> with a single space'  ]);
            Core\settings()->echo_checkbox_html([ 'capitalize_words',   'Capitalize First Letter Of All The Words'  ]);
        ?>
        
    </div>
    
    <div>
    
        <div class="settings_section">Removing</div>
        
        <?php
            Core\settings()->echo_checkbox_html([ 'remove_numbers',       'Remove numerical digits <pre>0123456789</pre>'         ]);
            Core\settings()->echo_checkbox_html([ 'remove_comma',         'Remove commas <pre>,</pre>'                ]);
            Core\settings()->echo_checkbox_html([ 'remove_period',        'Remove periods <pre>.</pre>'               ]);
            Core\settings()->echo_input_text_html([ 'exclude_words',      'Exclude words <pre>,</pre>' ]);
        ?>
        
    </div>
    
    <div>
    
        <div class="settings_section">Update Media Images</div>
        
        <?php
            Core\settings()->echo_input_number_html([ 'process_step',   'How many images will be processed per single Ajax request', 'Recommended value between 20 and 100' ]);
            
            Core\settings()->echo_checkbox_html([ 'update_title',       'Also update the Title of the image'        ]);
            Core\settings()->echo_checkbox_html([ 'update_description', 'Also update the Description of the image'  ]);
            Core\settings()->echo_checkbox_html([ 'update_caption',     'Also update the Caption of the image'      ]);
        ?>
        
    </div>
    
    
    <button id="save_settings" class="uaa_master_button" name="save_settings" value="1">Save Changes</button>
    
</form>
