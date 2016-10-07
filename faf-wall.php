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
    wp_enqueue_style('fw-css', plugins_url('freewall/css/freewall.min.css', __FILE__), array('bootstrap'), '1.0');
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
        $html   = '<div class="'.$id.'">';

        foreach ($images AS $imgId) {

            $imgLarge   = wp_get_attachment_image_url($imgId, 'large');
            $imgSrc     = wp_get_attachment_image_url($imgId, 'medium');
            $imgSrcset  = wp_get_attachment_image_srcset($imgId, 'medium');
            $imgSizes   = wp_get_attachment_image_sizes($imgId, 'medium');

            $html .= '<div class="item">';
            $html .= '<a rel="lightbox" href="' . esc_url($imgSrc) . '" title="">';
            $html .= '<img src="'.esc_url($imgSrc).'" srcset="'.esc_attr($imgSrcset).'" sizes="'.esc_attr($imgSizes).'" class="logo" alt=""/>';
            $html .= '</a>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '<script>
                    $(function() {
                        var wall = new Freewall("#'.$id.'");
                        wall.fitWidth();
                    });
                </script>';

        return $html;
    }
}
add_shortcode('wall', 'fafWallShort');

