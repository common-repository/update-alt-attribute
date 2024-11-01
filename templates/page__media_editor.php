<?php

use MauiMarketing\UpdateAltAtribute\Attachments;
use MauiMarketing\UpdateAltAtribute\MediaEditor;

global $wpdb;

$posts_per_page = isset( $_GET['per_page']  ) ? $_GET['per_page']   : 20;
$paged          = isset( $_GET['paged']     ) ? $_GET['paged']      : 1;
$orderby        = isset( $_GET['orderby']   ) ? $_GET['orderby']    : 'ID';
$order          = isset( $_GET['order']     ) ? $_GET['order']      : 'DESC';

$search_attributes  = isset( $_GET['search_attributes'] ) ? $_GET['search_attributes'] : '';
$search_titles      = isset( $_GET['search_titles']     ) ? $_GET['search_titles']     : '';
$search_categories  = isset( $_GET['search_categories'] ) ? $_GET['search_categories'] : '';
$search_tags        = isset( $_GET['search_tags']       ) ? $_GET['search_tags']       : '';

$info_columns_active        = ! empty( $_GET['columns'] ) && $_GET['columns'] == 'info' ? " active" : "";
$attribute_columns_active   = $info_columns_active !== " active"                        ? " active" : "";

$args = [
    'post_type'          => 'attachment',
    'post_mime_type'     => 'image',
    'post_status'        => 'inherit',
    'posts_per_page'     => $posts_per_page,
    'paged'              => $paged,
    'orderby'            => $orderby,
    'order'              => $order,
    // 'ignore_custom_sort' => true,
];

$args = apply_filters( 'uaa_media_editor_args', $args );

$attachments = new WP_Query( $args );

$total_pages = $attachments->max_num_pages;
?>
<div class="wrap">

    <div id="media_editor_header">
        <h3>Media Editor</h3>
        <div id="media_editor_control">
            Columns:
            <div class="control_column_switch<?php echo $attribute_columns_active; ?>" data-columns="attributes">Attributes</div>
            <div class="control_column_switch<?php echo $info_columns_active;      ?>" data-columns="info">Info</div>
        </div>
        <div id="">
        </div>
    </div>
    
    <div id="first_row" class="header_row">
    
        <div id="image_filters" class="wrapper_box">
            <?php do_action( "uaa_media_editor_filters" ); ?>
        </div>
    
        <div id="search_box">
        
            <input type="text" id="search_attributes" value="<?php echo $search_attributes; ?>" placeholder="Search Attributes">
            <input type="text" id="search_titles"     value="<?php echo $search_titles;     ?>" placeholder="Search Post Titles">
            <input type="text" id="search_categories" value="<?php echo $search_categories; ?>" placeholder="Search Categories">
            <input type="text" id="search_tags"       value="<?php echo $search_tags;       ?>" placeholder="Search Tags">
            
        </div>
    
    </div>
    
    <div id="second_row" class="header_row">
    
        <div id="save_box">
            <p class="lb_save" style="color:red;"></p>
            <img src="<?php echo admin_url('images/wpspin_light-2x.gif'); ?>" class="load_ajax">
            <button type="button" class="btn btn-success btn_save_all">Save All</button>
        </div>
        
        <div id="pagination_wrapper_box" class="wrapper_box">
            
            <div id="total_info">
                
                <?php echo $attachments->found_posts . " " . _n( 'image', 'images', $attachments->found_posts ) . " total"; ?>
                
            </div>
            
            <div id="pagination_box">
                
                <?php MediaEditor\display_pagination( $paged, $total_pages ); ?>
                
            </div>
            
            <div id="per_page_box">
            
                Per page:&nbsp;&nbsp;
                
                <select id="select_per_page" name="select_per_page">
                    <option value="20"  <?php echo ( $posts_per_page ==  20 && ! isset( $_GET['custom'] ) ) ? 'selected' : '' ?>>20&nbsp;&nbsp;</option>
                    <option value="40"  <?php echo ( $posts_per_page ==  40 && ! isset( $_GET['custom'] ) ) ? 'selected' : '' ?>>40&nbsp;&nbsp;</option>
                    <option value="100" <?php echo ( $posts_per_page == 100 && ! isset( $_GET['custom'] ) ) ? 'selected' : '' ?>>100&nbsp;&nbsp;</option>
                    <option value="200" <?php echo ( $posts_per_page == 200 && ! isset( $_GET['custom'] ) ) ? 'selected' : '' ?>>200&nbsp;&nbsp;</option>
                    <option value="-1"  <?php echo ( $posts_per_page ==  -1 && ! isset( $_GET['custom'] ) ) ? 'selected' : '' ?>>All&nbsp;&nbsp;</option>
                    <option value="custom" <?php echo ( isset( $_GET['custom'] ) ) ? 'selected' : '' ?>>Custom&nbsp;&nbsp;</option>
                </select>
                
                <input type="text" value="<?php echo ( isset( $_GET['custom'] ) ) ? $_GET['per_page'] : ''; ?>" id="per_page_custom"
                       placeholder="###" size="5" style="<?php echo ( ! isset( $_GET['custom'] ) ) ? 'display:none;' : ''; ?>">
                
            </div>
            
        </div>
    
    </div>
    
    <table id="media_editor" class="wp-list-table widefat fixed striped media" align="center">
    
        <thead>
            <tr>
                <td class="th_checkbox column-cb check-column" id="cb" data-col_type="attributes">
                    <input type="checkbox" name="check_all" id="check_all">
                </td>
                <th class="column-attachment_image"                                  id="attachment_image" ><span>Image</span></th>
                <th class="column-file_name         desc"                            id="file_name"        ><span>File name</span></th>
                
                <th class="column-title             desc" data-col_type="attributes" id="title"            ><span>Title</span></th>
                <th class="column-alt               desc" data-col_type="attributes" id="alt"              ><span>Alt</span></th>
                <th class="column-description       desc" data-col_type="attributes" id="description"      ><span>Description</span></th>
                <th class="column-caption           desc" data-col_type="attributes" id="caption"          ><span>Caption</span></th>
                
                <th class="column-save"                   data-col_type="attributes" id="save"></th>
                
                <th class="column-used              desc" data-col_type="info"       id="used"             ><span>Total</span></th>
                <th class="column-product           desc" data-col_type="info"       id="product"          ><span>In Products</span></th>
                <th class="column-page              desc" data-col_type="info"       id="page"             ><span>In Pages</span></th>
                <th class="column-post              desc" data-col_type="info"       id="post"             ><span>In Posts</span></th>
                <th class="column-categories        desc" data-col_type="info"       id="categories"       ><span>In Categories</span></th>
                <th class="column-tags              desc" data-col_type="info"       id="tags"             ><span>In Tags</span></th>
            </tr>
        </thead>
        
        <tbody id="the-list">

            <?php
            while( $attachments->have_posts() ){
                $attachments->the_post();
                
                $post_id     = get_the_ID();
                $attachment  = get_post( $post_id );
                
                $media_url   = admin_url('upload.php?item=' . $post_id );
                
                $thumbnail   = wp_get_attachment_image( $post_id );
                $file        = get_attached_file( $post_id );
                $file_name   = basename( $file );
                
                $image_url   = wp_get_attachment_image_src( $post_id, "large" );
                $img_data    = '';
                $img_data   .= ' data-file="' . esc_attr( $image_url[0] ) . '"';
                $img_data   .= ' data-size="' . esc_attr( $image_url[1] ) . 'x' . esc_attr( $image_url[2] ) . 'px"';
                
                $alt         = $attachment->_wp_attachment_image_alt;
                $title       = $attachment->post_title;
                $description = $attachment->post_content;
                $caption     = $attachment->post_excerpt;
                
                
                
                //$used_images = get_posts_using_attachment( get_the_ID(), 'column-used' );
                ?>
                <tr id="edit-<?php echo get_the_ID(); ?>" data-id="<?php echo get_the_ID(); ?>">
                
                    <th class="check-column" data-col_type="attributes" scope="row"><input class="check_item" type="checkbox" name="check_item" data-id="<?php echo get_the_ID(); ?>"></th>
                    
                    <td class="attachment_image column-attachment_image has-row-actions column-primary" data-colname="Image">
                    
                        <a href="<?php echo get_site_url(); ?>/wp-admin/post.php?post=<?php echo get_the_ID(); ?>&action=edit" target="_blank"<?php echo $img_data; ?>>
                            <span class="media-icon image-icon"><?php echo $thumbnail; ?> </span>
                        </a>
                        
                    </td>
                    
                    <td class="file_name column-file_name has-row-actions column-primary" data-colname="File Name">
                        <a class="filename" href="<?php echo $media_url; ?>"><?php echo $file_name; ?></a>
                    </td>
                    
                    <td class="column-title"        data-col_type="attributes" data-colname="Title"        ><input type="text" name="title"        value="<?php echo $title;       ?>"></td>
                    <td class="column-alt"          data-col_type="attributes" data-colname="Alt"          ><input type="text" name="alt"          value="<?php echo $alt;         ?>"></td>
                    <td class="column-description"  data-col_type="attributes" data-colname="Description"  ><input type="text" name="description"  value="<?php echo $description; ?>"></td>
                    <td class="column-caption"      data-col_type="attributes" data-colname="Caption"      ><input type="text" name="caption"      value="<?php echo $caption;     ?>"></td>
                    
                    <td class="save column-save td_save" data-col_type="attributes" data-colname="Save">
                        <button type="button" class="btn btn-success btn_save" data-id="<?php echo get_the_ID(); ?>">Save</button>
                    </td>
                    
                    <td class="column-used"         data-col_type="info" data-colname="Used"       ><?php echo Attachments\get_image_being_used_html( $post_id, ["product","page","post"]              ) ?></td>
                    <td class="column-product"      data-col_type="info" data-colname="Product"    ><?php echo Attachments\get_image_being_used_html( $post_id, ["product"] ) ?></td>
                    <td class="column-page"         data-col_type="info" data-colname="Page"       ><?php echo Attachments\get_image_being_used_html( $post_id, ["page"]    ) ?></td>
                    <td class="column-post"         data-col_type="info" data-colname="Post"       ><?php echo Attachments\get_image_being_used_html( $post_id, ["post"]    ) ?></td>
                    <td class="column-categories"   data-col_type="info" data-colname="Categories" ><div>...</div></td>
                    <td class="column-tags"         data-col_type="info" data-colname="Tags"       ><div>...</div></td>
                </tr>

            <?php } ?>

        </tbody>
        
    </table>
    
    <div>
        <p class="lb_save" style="color:red;"></p>
        <img src="<?php echo admin_url('images/wpspin_light-2x.gif'); ?>" class="load_ajax">
        <button type="button" class="btn btn-success btn_save_all">Save All</button>
    </div>
    
</div>
