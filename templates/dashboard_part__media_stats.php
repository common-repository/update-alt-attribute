<?php

use MauiMarketing\UpdateAltAtribute\Stats;

$media_stats  = Stats\get_media_stats();

?>
<div id="media_stats" class="has_chart">
    
    <div class="the_chart">
        <canvas id="media_stats_chart"></canvas>
    </div>
    
    <div class="the_data">
        
        <div>There <?php echo _n( "is a ", "are ", $media_stats["total"] ) . $media_stats["total"] . _n( " image", " images", $media_stats["total"] ); ?> in the Media Folder of this site.</div>
        
        <p></p>
        
        <?php if( $media_stats["no_alt"] ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&empty_attributes=empty_alt"); ?>">
                    <?php echo $media_stats["no_alt"] . _n( " image", " images", $media_stats["no_alt"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_red"></div>
            <?php echo _n( " has", " have", $media_stats["no_alt"] ); ?> the Alt attribute empty
        </div>
        <?php } ?>
        
        <?php if( $media_stats["no_title"] ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&empty_attributes=empty_title"); ?>">
                    <?php echo $media_stats["no_title"] . _n( " image", " images", $media_stats["no_title"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_blue"></div>
            <?php echo _n( " has", " have", $media_stats["no_title"] ); ?> the Title attribute empty
        </div>
        <?php } ?>
        
        <?php if( $media_stats["no_desc"] ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&empty_attributes=empty_description"); ?>">
                    <?php echo $media_stats["no_desc"] . _n( " image", " images", $media_stats["no_desc"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_yellow"></div>
            <?php echo _n( " has", " have", $media_stats["no_desc"] ); ?> the Description attribute empty
        </div>
        <?php } ?>
        
        <?php if( $media_stats["no_caption"] ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&empty_attributes=empty_caption"); ?>">
                    <?php echo $media_stats["no_caption"] . _n( " image", " images", $media_stats["no_caption"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_green"></div>
            <?php echo _n( " has", " have", $media_stats["no_caption"] ); ?> the Caption attribute empty
        </div>
        <?php } ?>
        
        <?php if( ( $media_stats["no_alt"] + $media_stats["no_title"] + $media_stats["no_desc"] + $media_stats["no_caption"] ) > $media_stats["faulty_images"] ){ ?>
            
            <p></p>
            
            <div>
                Because some images have more than 1 empty attribute there <?php echo _n( " is", " are", $media_stats["faulty_images"] ); ?>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&empty_attributes=empty_any"); ?>">
                    <?php echo $media_stats["faulty_images"] . _n( "&nbsp;image", "&nbsp;images", $media_stats["faulty_images"] ); ?>
                </a>
                that <?php echo _n( "needs", "need", $media_stats["faulty_images"] ); ?> to be updated.
            </div>
            
        <?php } ?>
        
        <?php if( $media_stats["faulty_images"] > 0 ){ ?>
            
            <p></p>
            
            <div>
                You can edit them with our <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor"); ?>">Media&nbsp;Editor</a>,
                or you can try the <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_automatic_updaters&tab=update_media_images"); ?>">Automatic&nbsp;Updater&nbsp;for&nbsp;the&nbsp;Media&nbsp;images</a>.
            </div>
            
        <?php } else { ?>
            
            <p></p>
            
            <div>
                All your images have all their attributes populated. Well done!
            </div>
            
        <?php } ?>
        
    </div>
    
</div>
