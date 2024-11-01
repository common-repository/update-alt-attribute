<?php

use MauiMarketing\UpdateAltAtribute\Stats;

$post_type_stats = Stats\get_post_type_stats();

$total_posts = array_sum( $post_type_stats["total"] );

$post_types = "<em>" . implode( "</em>, <em>", array_keys( $post_type_stats["total"] ) ) . "</em>";
$post_types = strrpos( $post_types, ',' ) !== false ? substr_replace( $post_types, ' or', strrpos( $post_types, ',' ), 1 ) : $post_types;


?>
<div id="post_type_stats" class="has_chart">
    
    <div class="the_chart">
        <canvas id="post_type_stats_chart"></canvas>
    </div>
    
    <div class="the_data">
        
        <div>We have identified <?php echo $total_posts . _n( " Post Object", " Post Objects", $total_posts ); ?> that <?php echo _n( " doesn't", " don't", $total_posts ); ?> have empty content.</div>
        
        <?php if( ! empty( $post_type_stats["total"]["Pages"] ) ){ ?>
            
            <p></p>
            
            <?php if( ! empty( $post_type_stats["with_image"]["Pages"] ) ){ ?>
            
                <div class="stat_row">
                    <span>
                        <?php echo $post_type_stats["with_image"]["Pages"] . _n( " page", " pages", $post_type_stats["with_image"]["Pages"] ); ?>
                    </span>
                    <div class="chart_box chart_blue"></div>
                    with a Featured image or an image in the content
                </div>
                
            <?php } ?>
            <?php if( ! empty( $post_type_stats["without_image"]["Pages"] ) ){ ?>
            
                <div class="stat_row">
                    <span>
                        <?php echo $post_type_stats["without_image"]["Pages"] . _n( " page", " pages", $post_type_stats["without_image"]["Pages"] ); ?>
                    </span>
                    <div class="chart_box chart_blue_light"></div>
                    without any images
                </div>
                
            <?php } ?>
        <?php } ?>
        
        <?php if( ! empty( $post_type_stats["total"]["Posts"] ) ){ ?>
            
            <p></p>
            
            <?php if( ! empty( $post_type_stats["with_image"]["Posts"] ) ){ ?>
            
                <div class="stat_row">
                    <span>
                        <?php echo $post_type_stats["with_image"]["Posts"] . _n( " post", " posts", $post_type_stats["with_image"]["Posts"] ); ?>
                    </span>
                    <div class="chart_box chart_yellow"></div>
                    with a Featured image or an image in the content
                </div>
                
            <?php } ?>
            <?php if( ! empty( $post_type_stats["without_image"]["Posts"] ) ){ ?>
            
                <div class="stat_row">
                    <span>
                        <?php echo $post_type_stats["without_image"]["Posts"] . _n( " post", " posts", $post_type_stats["without_image"]["Posts"] ); ?>
                    </span>
                    <div class="chart_box chart_yellow_light"></div>
                    without any images
                </div>
                
            <?php } ?>
        <?php } ?>
        
        <?php if( ! empty( $post_type_stats["total"]["Products"] ) ){ ?>
            
            <p></p>
            
            <?php if( ! empty( $post_type_stats["with_image"]["Products"] ) ){ ?>
            
                <div class="stat_row">
                    <span>
                        <?php echo $post_type_stats["with_image"]["Products"] . _n( " product", " products", $post_type_stats["with_image"]["Products"] ); ?>
                    </span>
                    <div class="chart_box chart_green"></div>
                    with a Featured image or an image in the content
                </div>
                
            <?php } ?>
            <?php if( ! empty( $post_type_stats["without_image"]["Products"] ) ){ ?>
            
                <div class="stat_row">
                    <span>
                        <?php echo $post_type_stats["without_image"]["Products"] . _n( " product", " products", $post_type_stats["without_image"]["Products"] ); ?>
                    </span>
                    <div class="chart_box chart_green_light"></div>
                    without any images
                </div>
                
            <?php } ?>
        <?php } ?>
        
        <?php if( $total_posts == 0 ){ ?>
            
            <div>
                You don't have any <?php echo $post_types; ?>! Why don't you create some?
            </div>
            
        <?php } ?>
        
    </div>
</div>
