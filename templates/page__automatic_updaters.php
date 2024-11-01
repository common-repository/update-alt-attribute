<?php

use MauiMarketing\UpdateAltAtribute\Core;

$tabs = array(
    'update_media_images' => 'Update Media Images',
    'update_post_content' => 'Update Post Content',
    'settings'            => 'Settings',
    'go_pro'              => 'Go Pro!',
);

$tabs        = apply_filters( "uaa_automatic_updaters_tabs", $tabs );

$current_tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : array_keys( $tabs )[0];

$version     = apply_filters( "uaa_get_display_version", "Version " . UAA_VERSION );

?>
<div id="automatic_updaters_header">

    <div id="automatic_updaters_header_info">

        <div id="maui_logo">
            <a href="https://mauimarketing.com/"><img height="100" width="300" src="<?php echo UAA_PLUGIN_URL; ?>/assets/logo.png" alt="Maui Marketing" title="Maui Marketing"></a>
        </div>
        
        <h2 id="uaa_title">Update ALT Attribute</h2>
        <i id="uaa_version"><?php echo $version; ?></i>
    </div>
    
    <h2 class="nav-tab-wrapper">
    <?php
        foreach( $tabs as $tab => $name ){
            
            $class = ( $tab == $current_tab ) ? ' nav-tab-active' : '';
            
            echo '<a id="tab__' . $tab . '" class="nav-tab' . $class . '" href="?page=uaa_automatic_updaters&tab=' . $tab . '">' . $name . '</a>';
        }
    ?>
    </h2>
    
</div>

<?php Core\load_template_or_tab( "tab__", $current_tab ); ?>
