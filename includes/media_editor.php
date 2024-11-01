<?php

namespace MauiMarketing\UpdateAltAtribute\MediaEditor;

function display_pagination( $current_page = 1, $total_pages = 1 ){
    $echo = 'MauiMarketing\UpdateAltAtribute\Core\fnc';
    
    $html = "Page:&nbsp;&nbsp;";
    
    
    if( $current_page > 1 ){
        
        $html .= <<<EOF
            <a class="first-page button" href="{$echo( get_pagenum_link( 1 ) )}">
                <span class="screen-reader-text">First page</span>
                <span aria-hidden="true">«</span>
            </a>
            <a class="prev-page button" href="{$echo( get_pagenum_link( $current_page - 1 ) )}">
                <span class="screen-reader-text">Previous page</span>
                <span aria-hidden="true">‹</span>
            </a>
EOF;

    } else {
    
        $html .= <<<EOF
            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
EOF;
    
    }
    
    
    $html .= <<<EOF
        <span class="paging-input">
            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
            <input class="current-page" id="current-page-selector" type="text" name="paged" value="{$echo( $current_page )}" size="1" aria-describedby="table-paging">
            <span class="tablenav-paging-text"> of <span class="total-pages">{$echo( $total_pages )}</span>
            </span>
        </span>
EOF;
    
    
    if( $current_page < $total_pages ){
    
        $html .= <<<EOF
            <a class="next-page button" href="{$echo( get_pagenum_link( $current_page + 1 ) )}">
                <span class="screen-reader-text">Next page</span>
                <span aria-hidden="true">›</span>
            </a>
            <a class="last-page button" href="{$echo( get_pagenum_link( $total_pages ) )}">
                <span class="screen-reader-text">Last page</span>
                <span aria-hidden="true">»</span>
            </a>
EOF;
        
    } else {
    
        $html .= <<<EOF
            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
EOF;
        
    }
    
    
    echo $html;
}

