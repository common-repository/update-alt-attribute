<?php

use MauiMarketing\UpdateAltAtribute\Stats;

$used_images_stats = Stats\get_used_images_stats();

$used = array_sum( $used_images_stats ) - $used_images_stats["total"];
$unused = $used_images_stats["total"] - $used;

?>
<div id="used_images_stats" class="has_chart">
    
    <div class="the_chart">
        <canvas id="used_images_stats_chart"></canvas>
    </div>
    
    <div class="the_data">
        
        <div><?php echo $used . _n( " image", " images", $used ); ?> out of <?php echo $used_images_stats["total"]; ?> are being used in the Post Objects.</div>
        
        <p></p>
        
        <?php if( ! empty( $unused ) ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&used_as=not_used&columns=info"); ?>">
                    <?php echo $unused . _n( " image", " images", $unused ); ?>
                </a>
            </span>
            <div class="chart_box chart_red"></div>
            <?php echo _n( " is", " are", $unused ); ?> not being used at all
        </div>
        <?php } ?>
        
        <?php if( ! empty( $used_images_stats["featured"] ) ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&used_as=featured_image&columns=info"); ?>">
                    <?php echo $used_images_stats["featured"] . _n( " image", " images", $used_images_stats["featured"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_yellow"></div>
            <?php echo _n( " is", " are", $used_images_stats["featured"] ); ?> being used as a Featured image
        </div>
        <?php } ?>
        
        <?php if( ! empty( $used_images_stats["both"] ) ){ ?>
        <div class="stat_row">
            <span>
                <?php echo $used_images_stats["both"] . _n( " image", " images", $used_images_stats["both"] ); ?>
            </span>
            <div class="chart_box chart_green"></div>
            <?php echo _n( " is", " are", $used_images_stats["both"] ); ?> being used as a Featured image and in the content
        </div>
        <?php } ?>
        
        <?php if( ! empty( $used_images_stats["in_content"] ) ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&used_as=in_content&columns=info"); ?>">
                    <?php echo $used_images_stats["in_content"] . _n( " image", " images", $used_images_stats["in_content"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_blue"></div>
            <?php echo _n( " is", " are", $used_images_stats["in_content"] ); ?> being used in the content
        </div>
        <?php } ?>
        
        <?php if( $used == 0 ){ ?>
            
            <div>
                None of your images are being used!
            </div>
            
        <?php } ?>
        
    </div>
</div>
