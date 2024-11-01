<?php

use MauiMarketing\UpdateAltAtribute\Stats;

$poor_attributes_stats = Stats\get_poor_attributes_stats();

?>
<div id="poor_attributes_stats" class="has_chart">
    
    <div class="the_chart">
        <canvas id="poor_attributes_stats_chart"></canvas>
    </div>
    
    <div class="the_data">
        
        <div>There <?php
            echo _n( "is a ", "are ", $poor_attributes_stats["total_with_attribute"] )
               . $poor_attributes_stats["total_with_attribute"]
               . _n( " image", " images", $poor_attributes_stats["total_with_attribute"] );
        ?> with at least 1 attribute populated.</div>
        
        <p></p>
        
        <?php if( $poor_attributes_stats["without_letters"] ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&faulty_names=without_letters"); ?>">
                    <?php echo $poor_attributes_stats["without_letters"] . _n( " image", " images", $poor_attributes_stats["without_letters"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_red"></div>
            <?php echo _n( " has", " have", $poor_attributes_stats["without_letters"] ); ?> at least one attribute without any letters
        </div>
        <?php } ?>
        
        <?php if( $poor_attributes_stats["repeating"] ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&faulty_names=repeating"); ?>">
                    <?php echo $poor_attributes_stats["repeating"] . _n( " image", " images", $poor_attributes_stats["repeating"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_blue"></div>
            <?php echo _n( " has", " have", $poor_attributes_stats["repeating"] ); ?> at least one attribute with too many consecutive letters
        </div>
        <?php } ?>
        
        <?php if( $poor_attributes_stats["with_symbols"] ){ ?>
        <div class="stat_row">
            <span>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&faulty_names=with_symbols"); ?>">
                    <?php echo $poor_attributes_stats["with_symbols"] . _n( " image", " images", $poor_attributes_stats["with_symbols"] ); ?>
                </a>
            </span>
            <div class="chart_box chart_yellow"></div>
            <?php echo _n( " has", " have", $poor_attributes_stats["with_symbols"] ); ?> at least one attribute with symbols
        </div>
        <?php } ?>
        
        <?php if( ( $poor_attributes_stats["without_letters"] + $poor_attributes_stats["repeating"] + $poor_attributes_stats["with_symbols"] ) > $poor_attributes_stats["faulty_images"] ){ ?>
            
            <p></p>
            
            <div>
                Because some images have more than 1 poorly populated attribute there <?php echo _n( " is", " are", $poor_attributes_stats["faulty_images"] ); ?>
                <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor&faulty_names=all_poor"); ?>">
                    <?php echo $poor_attributes_stats["faulty_images"] . _n( "&nbsp;image", "&nbsp;images", $poor_attributes_stats["faulty_images"] ); ?>
                </a>
                that <?php echo _n( "needs", "need", $poor_attributes_stats["faulty_images"] ); ?> to be updated.
            </div>
            
        <?php } ?>
        
        <?php if( $poor_attributes_stats["faulty_images"] > 0 ){ ?>
            
            <p></p>
            
            <div>
                You can edit them with our <a class="light_link" href="<?php echo admin_url("admin.php?page=uaa_media_editor"); ?>">Media&nbsp;Editor</a>.
            </div>
            
        <?php } elseif( $poor_attributes_stats["total_with_attribute"] > 0 ) { ?>
            
            <p></p>
            
            <div>
                All your images have all their attributes correctly populated. Well done!
            </div>
            
        <?php } ?>
        
    </div>
</div>
