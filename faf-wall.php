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
    load_plugin_textdomain('faf-wall', false, dirname(plugin_basename(__FILE__)) . '/lang/');
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
    wp_enqueue_style('fw-css', plugins_url('freewall/css/freewall.min.css', __FILE__), array(), '1.0');
    wp_enqueue_script('fw-js', plugins_url('freewall/js/freewall.min.js', __FILE__), array('jquery'), '1.0', false);
}
add_action('wp_enqueue_scripts', 'fafWallEnqueueScriptsStyles');

/**
 * add the media button to editor
 */
function fafWallAddEditorButton()
{
    echo '<a href="#" id="faf-wall-short" class="button">' . __('FAF Image Wall') . '</a>';
}
add_action('media_buttons', 'fafWallAddEditorButton', 100);

/**
 * exif short code
 *
 * @param $atts
 * @return string
 */
function fafWallShort($atts)
{
    if (isset($atts['images'])) {
        $images = explode(',', $atts['images']);
        $id     = uniqid('wall');
        $html   = '<div id="'.$id.'" class="free-wall"><div>';
        $pics   = array();

        foreach ($images AS $imgId) {

            $imgLarge   = wp_get_attachment_image_url($imgId, 'large');
            $imgSrc     = wp_get_attachment_image_url($imgId, 'medium');
            $imgSrcset  = wp_get_attachment_image_srcset($imgId, 'medium');
            $imgSizes   = wp_get_attachment_image_sizes($imgId, 'medium');

            $pics[] = esc_url($imgLarge);
        }

        $imgs = '["' . implode('", "', $pics) . '"]';

        $html .= '<script>
    // grid wall
    var temp = "<a class=\"free-wall-link\" rel=\"lightbox\" href=\"{index}\"><div class=\"cell\" style=\"width:{width}px; height: {height}px; background-image: url({index})\"></div></a>";
    var w = 1, html = "";
    var imgs = '.$imgs.';

    imgs.forEach(function(ix){
        w = 250 + 250 * Math.random() << 0;
        html += temp.replace(/\{height\}/g, 250).replace(/\{width\}/g, w).replace(/\{index\}/g, ix);
    });
    $("#'.$id.'").html(html);

    var wall = new Freewall("#'.$id.'");
    wall.reset({
        selector: ".cell",
        animate: false,
        cellW: 250,
        cellH: 250,
        gutterX: 10,
        gutterY: 10,
        onResize: function () {
            wall.fitWidth();
        }
    });
    wall.fitWidth();
    // for scroll bar appear;
    $(window).trigger("resize");
</script>';

        return $html;
    }
}
add_shortcode('wall', 'fafWallShort');

