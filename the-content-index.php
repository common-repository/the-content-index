<?php
/**
 * The Content Index plugin
 *
 * @link              http://www.easantos.net/wordpress/the-content-index/
 * @since             1.0.0
 * @package           The_Content_Index
 *
 * @wordpress-plugin
 * Plugin Name:       The Content Index
 * Plugin URI:        http://www.easantos.net/wordpress/the-content-index/
 * Description:       The Content Index wordpress plugin will generate a content index for your posts and pages.
 * Version:           1.0.7
 * Author:            Easantos
 * Author URI:        http://www.easantos.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       the-content-index
 * Domain Path:       /languages
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use TheContentIndex\ContentIndex;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

add_action('init', 'tciInit');
add_action('the_content', 'tciContent', -1);
add_action('admin_menu', 'tciAddMenuItems');
add_action('admin_enqueue_scripts', 'tciAddColorPicker');
register_activation_hook(__FILE__, 'tciSetDefaultSettings');
add_filter('plugin_action_links', 'tciPluginActionLinks', 10, 2);
add_shortcode('content-index', 'tciShortcode');

require __DIR__ . '/vendor/autoload.php';


/** @var ContentIndex $contentIndex */
global $contentIndex;

$contentIndex = new ContentIndex();

function tciInit()
{
    wp_register_script(
        'tciMain',
        plugins_url('public/js/main.js', __FILE__),
        array('jquery'),
        '1.1',
        true
    );

    wp_enqueue_script('tciMain');
}

function tciAddColorPicker($hook)
{
    if (is_admin()) {
        // Add the color picker css file
        wp_enqueue_style('wp-color-picker');

        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script('custom-script-handle', plugins_url('public/js/admin.js', __FILE__), array('wp-color-picker'),
            false, true);
    }
}

function tciPluginActionLinks($links, $file)
{
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=tciSettings">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

function tciAddMenuItems()
{
    /** @var ContentIndex $theContentIndex */
    global $contentIndex;

    $contentIndex->addMenuItems();
}

function tciMain()
{
    /** @var ContentIndex $theContentIndex */
    global $contentIndex;

    $contentIndex->main();
}

function tciSettings()
{
    /** @var ContentIndex $theContentIndex */
    global $contentIndex;

    $contentIndex->settings();
}

function tciContent($content)
{
    /** @var ContentIndex $theContentIndex */
    global $contentIndex;

    return $contentIndex->content($content);
}

function tciShortcode($content)
{
    return tciContent($content);
}

function tciSetDefaultSettings()
{
    /** @var ContentIndex $theContentIndex */
    global $contentIndex;

    $contentIndex->setDefaultSettings();
}
