<?php

namespace MauiMarketing\UpdateAltAtribute\Attachments;

// 'attachment_fields_to_edit' is a default WordPress hook, it will add content to the Attachment Details edit page
function attachment_details_additional_info( $form_fields, $post ){
    
    $using_attachment            = get_post_meta( $post->ID, 'using_attachment', true );
    $total_post_using_attachment = get_post_meta( $post->ID, 'total_post_using_attachment', true );

    $form_fields['using_attachment'] = [
        "input" => 'html',
        "label" => 'Using attachment:',
        "html"  => ''
                // .  '<input hidden type="text" name="attachments[' . $post->ID . '][using_attachment]" id="attachments[' . $post->ID . '][using_attachment]" />'
                .  '<div>' . $using_attachment . '</div>',
    ];

    $form_fields['total_post_using_attachment'] = [
        "input" => 'html',
        "label" => 'Total post using attachment:',
        "html"  => ''
                // .  '<input hidden type="text" name="attachments[' . $post->ID . '][total_post_using_attachment]" id="attachments[' . $post->ID . '][total_post_using_attachment]" />'
                .  '<div style="line-height: 30px;">' . $total_post_using_attachment . '</div>',
    ];

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', __NAMESPACE__ . '\\' . 'attachment_details_additional_info', 10, 2 );
