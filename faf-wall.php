<?php

/*
Plugin Name: FAF Wall
Plugin URI: https://github.com/fafiebig/faf-wall
Description: Show image walls of your WordPress images.
Version: 1.0
Author: F.A. Fiebig
Author URI: http://fafworx.com
License: GNU GENERAL PUBLIC LICENSE
*/

defined('ABSPATH') or die('No direct script access allowed!');

/**
 * load translation domain
 */
function fafWallLoadTextdomain()
{
    load_plugin_textdomain('faf-wall', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'fafWallLoadTextdomain');

/**
 * add scripts and styles to admin
 */
function fafWallEnqueueAdminScriptsStyles()
{
    wp_enqueue_script('faf-wall-editor-js', plugins_url('editor/editor.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'fafWallEnqueueAdminScriptsStyles');

/**
 * add scripts and styles to frontend
 */
function fafWallEnqueueScriptsStyles()
{
    /* load freewall scripts and styles */
    wp_enqueue_style('freewall-css', plugins_url('freewall/css/freewall.min.css', __FILE__), array(), '1.0', 'all');
    wp_enqueue_script('freewall-js', plugins_url('freewall/js/freewall.min.js', __FILE__), array('jquery'), '1.0', true);

    /* load slb scripts and styles */
    wp_enqueue_style('simplelightbox-css', plugins_url('simplelightbox/css/simplelightbox.min.css', __FILE__), array(), '1.0', 'all');
    wp_enqueue_script('simplelightbox-js', plugins_url('simplelightbox/js/simplelightbox.min.js', __FILE__), array('jquery'), '1.0', true);

    /* load faf trigger script */
    wp_enqueue_script('faf-wall-js', plugins_url('faf-wall.js', __FILE__), array('jquery', 'freewall-js', 'simplelightbox-js'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'fafWallEnqueueScriptsStyles');

/**
 * add the media button to editor
 */
function fafWallAddEditorButton()
{
    echo '<a href="#" id="faf-wall-short" class="button">' . __('FAF Image Wall', 'faf-wall') . '</a>';
}
add_action('media_buttons', 'fafWallAddEditorButton', 100);

/**
 * @param $atts
 * @return string
 */
function fafWallGalleryShort($atts) {

    global $post;

    if ( ! empty( $atts['ids'] ) ) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        //if ( empty( $atts['orderby'] ) )
            //$atts['orderby'] = 'post__in';
        $atts['include'] = $atts['ids'];
    }

    extract(shortcode_atts(array(
        'orderby'       => 'menu_order ASC, ID ASC',
        'include'       => '',
        'id'            => $post->ID,
        'itemtag'       => 'dl',
        'icontag'       => 'dt',
        'captiontag'    => 'dd',
        'columns'       => 3,
        'size'          => 'medium',
        'link'          => 'file'
    ), $atts));


    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'orderby' => $orderby
    );

    if ( !empty($include) )
        $args['include'] = $include;
    else {
        $args['post_parent'] = $id;
        $args['numberposts'] = -1;
    }

    $images = get_posts($args);

    $height = ($atts['size'])?$atts['size']:200;
    $id     = uniqid('wall');
    $html   = '<div id="'.$id.'" class="free-wall">';

    shuffle($images);

    foreach ( $images as $image ) {
        //$title          = $image->post_title;
        //$caption        = $image->post_excerpt;
        //$description    = $image->post_content;
        //$image_alt      = get_post_meta($image->ID,'_wp_attachment_image_alt', true);
        $width          = $height + $height * random_int(0,1);

        $imgTitle   = get_the_title($image->ID);
        $imgMeta    = wp_get_attachment_metadata($image->ID, true);
        $imgLarge   = wp_get_attachment_image_url($image->ID, 'large');
        $imgMedium  = wp_get_attachment_image_url($image->ID, 'medium');
        $imgFull    = wp_get_attachment_image_url($image->ID, 'full');
        //$imgSet     = wp_get_attachment_image_srcset($image->ID, 'medium');
        //$imgSizes   = wp_get_attachment_image_sizes($image->ID, 'medium');

        $exif = mapExif(exif_read_data($imgFull));
        //$exif = mapMeta($imgMeta['image_meta']);
        $meta = implode(", ", $exif);

        $html .= '<a class="free-wall-link" rel="lightbox" href="'.esc_url($imgLarge).'" title="'.esc_attr($imgTitle).' | '.$meta.'">';
        $html .= '<div class="cell" style="width:'.$width.'px; height: '.$height.'px; background-image: url('.esc_url($imgMedium).')"></div>';
        $html .= '</a>';


    }
    $html .= '</div>';

    $html .= '<script>
    var wallId = "#'.$id.'";
    var wallHeight = '.$height.';
</script>';

    return $html;
}
remove_shortcode('gallery');
add_shortcode('gallery', 'fafWallGalleryShort');

function mapMeta($meta)
{
    $retval = array();
    foreach ($meta AS $key => $val) {
        switch ($key) {
            case 'aperture':
                $retval[] = 'f'.$val;
                break;
            case 'camera':
                $retval[] = $val;
                break;
            case 'focal_length':
                $retval[] = $val.'mm';
                break;
            case 'iso':
                $retval[] = 'ISO'.$val;
                break;
            case 'shutter_speed':
                $retval[] = round($val,2).'sek';
                break;
        }
    }

    return $retval;
}

function mapExif($exif)
{
    $retval = array();
    foreach ($exif AS $key => $val) {
        switch ($key) {
            case 'Make':
            case 'Model':
            case 'UndefinedTag:0xA434':
                $retval[] = $val;
                break;
            case 'ExposureTime':
                $retval[] = $val.'sec';
                break;
            case 'ISOSpeedRatings':
                $retval[] = 'ISO'.$val;
                break;
            case 'FNumber':
                @list($a, $v) = explode('/', $val);
                $retval[] = 'F'.($a/$v);
                break;
            case 'FocalLength':
                @list($a, $v) = explode('/', $val);
                $retval[] = ($a/$v).'mm';
                break;
            case 'Flash':
                if ($val != 16) {
                    $retval[] = 'Flash';
                }
                break;
        }
    }

    return $retval;
}

