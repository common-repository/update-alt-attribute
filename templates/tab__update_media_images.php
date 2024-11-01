<?php

use MauiMarketing\UpdateAltAtribute\Core;
use MauiMarketing\UpdateAltAtribute\Stats;
use MauiMarketing\UpdateAltAtribute\Media;

$stats = Stats\get_media_stats();

$will_process = [ "Alt" ];

if( Core\settings()->load("update_title")       === "yes" ){ $will_process[] = "Title";       }
if( Core\settings()->load("update_description") === "yes" ){ $will_process[] = "Description"; }
if( Core\settings()->load("update_caption")     === "yes" ){ $will_process[] = "Caption";     }

$will_process_text = "<em>" . implode( "</em>, <em>", $will_process ) . "</em>";
$will_process_text = strrpos( $will_process_text, ',' ) !== false ? substr_replace( $will_process_text, ' and', strrpos( $will_process_text, ',' ), 1 ) : $will_process_text;


?>
<div class="uaa_tab_content">

    <div class="tab_dashboard">
    
        <div class="tab_info">
            <div class="tab_info_content">
                <?php echo Media\get_media_stats_html( $stats ); ?>
            </div>
        </div>
        
        <div class="tab_description">

            <p>By pressing the <strong>Update Media</strong> button the plugin will generate <strong>a descriptive text</strong> which will be used to populate empty <?php echo $will_process_text; ?> attributes for every processed image.</p>
            <p>The descriptive text will be generated from the title of the attached Post/Page where applicable or generated from the image file name.</p>
            <p>
                According to <a href="?page=uaa_automatic_updaters&tab=settings">Settings</a>, if the file name is to be used:
                <?php if( Core\settings()->load("replace_hyphen")     === "yes" ){ ?><br/>- Hyphens will be converted to spaces<?php } ?>
                <?php if( Core\settings()->load("replace_underscore") === "yes" ){ ?><br/>- Underscores will be converted to spaces<?php } ?>
                <?php if( Core\settings()->load("remove_numbers")     === "yes" ){ ?><br/>- Numerical digits will be removed<?php } ?>
                <?php if( Core\settings()->load("capitalize_words")   === "yes" ){ ?><br/>- Words will be capitalized<?php } ?>
                <?php if( Core\settings()->load("remove_comma")       === "yes" ){ ?><br/>- Commas will be removed<?php } ?>
                <?php if( Core\settings()->load("remove_period")      === "yes" ){ ?><br/>- Periods will be removed<?php } ?>
                <?php if( Core\settings()->load("exclude_words")      ==! "" ){ ?><br/>- The words "<?php echo Core\settings()->load("exclude_words") ; ?>" will be deleted<?php } ?>
            </p>
            <p>If the image is not attached to any Post/Page and processing it's file name returns an empty string then the full file name will be used (without the extension).</p>
            
        </div>
        
    </div>
    
    <div id="processing_wrapper">
    
        <?php if( $stats['faulty_images'] > 0 ){ ?>
            
            <div id="update_media_button_wrapper" class="center">
                <button id="update_media" class="uaa_master_button">Update Media</button>
                <div id="update_media_dry_run_wrapper"><div id="update_media_dry_run">Do a test run instead</div></div>
            </div>
            
        <?php } ?>
        
        <div id="update_media_nothing_to_do" class="center<?php echo $stats['faulty_images'] > 0 ? ' hide' : ''; ?>">
            Nothing to see here, carry on. May we suggest that you also <a href="?page=uaa_automatic_updaters&tab=update_post_content">check your Post Content</a> for the images that need updating?
        </div>
        
        <div id="update_media_progress">
            
            <div id="update_media_progress_bar_label">Processed: <span id="images_processed">0</span> / <span id="images_to_process">100</span></div>
            
            <div id="update_media_progress_bar">
                <div id="update_media_progress_bar_filler"></div>
            </div>
            
        </div>
        
    </div>
    
</div>

<label class="notify"></label>

<div id="result_of_media_update">
    <h3>Processed media images:</h3>
    <table class="show_list_table">
        <tr>
            <th>ID</th>
            <th>Base file name or Page name</th>
            <th>Text used</th>
            <th class="check_column">Alt</th>
            <th class="check_column">Title</th>
            <th class="check_column">Description</th>
            <th class="check_column">Caption</th>
        </tr>
    </table>
</div>
