<?php

use MauiMarketing\UpdateAltAtribute\Core;

$done_indexing = get_option( "uaa_done_indexing", "no" );

?>
<div class="mm_plugin_intro" style="text-align: center;">
    <h2>Maui Marketing</h2>
    <a href="https://mauimarketing.com/"><img width="300" height="100" title="Maui Marketing" alt="Maui Marketing" src="<?php echo UAA_PLUGIN_URL; ?>/assets/logo.png"></a>
</div>

<?php if( $done_indexing !== "yes" ){ ?>

    <div id="indexing_wrapper">
    
        <p>In order to use this plugin, we need to index all your Pages, Posts and Products.</p>
        <p>Don't worry, this will only take a moment and it is required only once after the plugin is activated.</p>

        <div id="start_indexing_wrapper" class="center">
            <button id="start_indexing" class="uaa_master_button">Start indexing</button>
        </div>
        
        <div id="indexing_progress">
            
            <div id="indexing_progress_bar_label">Posts indexed: <span id="posts_indexed">0</span> / <span id="posts_to_index">?</span></div>
            
            <div id="indexing_progress_bar">
                <div id="indexing_progress_bar_filler"></div>
            </div>
            
        </div>
        
        <div id="indexing_error"></div>
        
    </div>
    
<?php } else { ?>
    
    <div class="dashboard_stats_row">
    
        <?php Core\load_template_or_tab( 'dashboard_part__', 'media_stats' ); ?>
        
        <?php Core\load_template_or_tab( 'dashboard_part__', 'post_stats' ); ?>
        
    </div>
    
    <div class="dashboard_stats_row">
        
        <?php Core\load_template_or_tab( 'dashboard_part__', 'poor_attributes_stats' ); ?>
        
        <?php Core\load_template_or_tab( 'dashboard_part__', 'used_images_stats' ); ?>
        
    </div>
    
<?php } ?>
