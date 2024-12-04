<?php

/**
  * The plugin bootstrap file
  *
  * @link              https://robertdevore.com
  * @since             1.0.0
  * @package           Table_Block_Enhancer
  *
  * @wordpress-plugin
  *
  * Plugin Name: Table Block Enhancer
  * Description: Adds filtering and sorting functionality to the default WordPress Table block.
  * Plugin URI:  https://github.com/robertdevore/table-block-enhancer/
  * Version:     1.0.0
  * Author:      Robert DeVore
  * Author URI:  https://robertdevore.com/
  * License:     GPL-2.0+
  * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
  * Text Domain: table-block-enhancer
  * Domain Path: /languages
  * Update URI:  https://github.com/robertdevore/table-block-enhancer/
  */

defined( 'ABSPATH' ) || exit;

require 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/robertdevore/table-block-enhancer/',
    __FILE__,
    'table-block-enhancer'
);

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch( 'main' );

// Define the plugin version.
define( 'TABLE_BLOCK_ENHANCER_VERSION', '1.0.0' );

/**
 * Enqueue JavaScript for the block editor.
 *
 * Enqueues the script used in the block editor for enhancing the table block.
 *
 * @since  1.0.0
 * @return void
 */
function table_block_enhancer_enqueue_editor_assets() {
    wp_enqueue_script(
        'table-block-enhancer-editor-script',
        plugin_dir_url( __FILE__ ) . 'assets/js/table-block-enhancer-editor.js',
        [ 'wp-blocks', 'wp-editor', 'wp-element', 'wp-components', 'wp-i18n' ],
        TABLE_BLOCK_ENHANCER_VERSION,
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'table_block_enhancer_enqueue_editor_assets' );

/**
 * Enqueue JavaScript and CSS for the frontend.
 *
 * Enqueues necessary scripts and styles for DataTables functionality on the frontend.
 *
 * @since  1.0.0
 * @return void
 */
function table_block_enhancer_enqueue_frontend_assets() {
    // Enqueue DataTables CSS.
    wp_enqueue_style(
        'datatables-style',
        plugin_dir_url( __FILE__ ) . 'assets/css/dataTables.min.css',
        [],
        TABLE_BLOCK_ENHANCER_VERSION
    );

    // Enqueue DataTables JavaScript.
    wp_enqueue_script(
        'datatables-script',
        plugin_dir_url( __FILE__ ) . 'assets/js/dataTables.min.js',
        [ 'jquery' ],
        TABLE_BLOCK_ENHANCER_VERSION,
        true
    );

    // Enqueue the plugin's frontend script.
    wp_enqueue_script(
        'table-block-enhancer-frontend-script',
        plugin_dir_url( __FILE__ ) . 'assets/js/table-block-enhancer.js',
        [ 'jquery', 'datatables-script' ],
        TABLE_BLOCK_ENHANCER_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'table_block_enhancer_enqueue_frontend_assets' );
