<?php

$get_post   = get_option('custom_post_update_alt');
$check_cron = get_option('check_run_cron_alt');

if( isset( $_POST['save_change_alt'] ) ){
    
    update_option( 'custom_post_update_alt', $_POST['post_type_alt'] );
    $get_post = get_option('custom_post_update_alt');
    
    update_option( 'check_run_cron_alt', $_POST['cron_update_time'] );
    $check_cron = get_option('check_run_cron_alt');
    
    if( $check_cron != 1 ){
        
        wp_clear_scheduled_hook('mm_update_alt_post');
        
    } else {
        
        wp_clear_scheduled_hook('mm_update_alt_post');
        
        $timeNext = time() + ( 15 * 60 );
        
        if( ! empty( $get_post ) ){
            
            // wp_schedule_event( $timeNext, 'hourly', 'mm_update_alt_post' );
            
        } else {
            
            wp_clear_scheduled_hook('mm_update_alt_post');
            echo "<span style='color:red;'>Error: You must select at least a post type! </span>";
        }
    }
}
?>
<div class="uaa_tab_content">
    <strong>Note: </strong>
    <span>First you need to choose <b>Post type</b> which needs to be updated in the Post type List. Next, in the setting, there is a On/Off button to enable or disable the auto-run update ALT images. This function will automatically update <b>20 records</b> after <b>15 minutes.</b></span>
    <br>
</div>
<form method="post" action="#"> 
    <?php wp_nonce_field( 'submit_update_post_content', 'update_post_content_nonce' ); ?>
    <div class="custom-post">
        <h3>1. Select post type</h3>
        <i><b><?php _e('Select the post types you would like to run the updater on:', 'update-alt-attribute'); ?></b></i>
        <ul class="custom_post_types_checkboxes">
            <?php
            
            $get_post_types = get_post_types( '', 'objects' );
            
            foreach ( $get_post_types as $post_type ) {
                
                if( $post_type->name != 'revision' && $post_type->name != 'nav_menu_item' ){
                    
                    $checked = ( ! empty( $get_post && in_array( $post_type->name, $get_post ) ) ) ? 'checked' : '';
                ?>
                    <li>
                        <input type="checkbox" id="post_type_selected" value="<?php echo $post_type->name ?>" name="post_type_alt[]" <?php echo $checked; ?>>
                        <?php echo $post_type->labels->name ?>
                    </li>
                <?php  } ?>
            <?php  } ?>
        </ul>

    </div>
    <div>
        <h3>2. Settings</h3>
        
        <div class="apple_toggle">
            <input type="checkbox" id="cron_update_time" name="cron_update_time" value="1" <?php echo ( $check_cron == 1 ) ? 'checked' : ''; ?> />
            <div class="toggle-handle"></div>
            <label for="cron_update_time" onclick></label>
        </div>

        <b>Note: </b><i>Select on to use the cron feature. It will update 20 records every 15 mins.</i>
    </div>
    <div>
        <button id="save_change_alt" class="uaa_master_button" name="save_change_alt" value="1">Save Settings</button>
    </div>
    <div id="update_content_dry_run_wrapper">
        <button id="update_content" class="uaa_master_button">Start Update Only Content</button>
        <!-- <div id="update_content_dry_run">Do a test run instead</div>         -->
        <div id="update_content_nothing_to_do" class="center<?php echo $stats['faulty_images'] > 0 ? ' hide' : ''; ?>"></div>
        <div id="update_content_progress">
            <div id="update_content_progress_bar_label">Processed: <span id="content_processed">0</span> / <span id="content_to_process">100</span></div>
            
            <div id="update_content_progress_bar">
                <div id="update_content_progress_bar_filler"></div>
            </div>
        </div>
    </div> 
</form>
<div id="result_of_content_update">
    <h3>Processed media images:</h3>
    <table class="show_list_table">
        <tr>
            <th>ID</th>
            <th>Url Image</th>
            <th>Alt</th>
            <th class="check_column">Update Alt</th>
        </tr>
    </table>
</div>

