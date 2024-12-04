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
 * Activate the plugin.
 *
 * Sets default options upon plugin activation.
 *
 * @since  1.0.0
 * @return void
 */
function table_block_enhancer_activate() {
    // Define default options.
    $default_options = [
        'paging'    => 1,
        'searching' => 1,
        'ordering'  => 1,
    ];

    // If options do not exist, set them to defaults.
    if ( ! get_option( 'table_block_enhancer_options' ) ) {
        update_option( 'table_block_enhancer_options', $default_options );
    }
}
register_activation_hook( __FILE__, 'table_block_enhancer_activate' );

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
        [ 'wp-blocks', 'wp-editor', 'wp-element' ],
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

    // Get options and pass them to JavaScript.
    $options = get_option( 'table_block_enhancer_options', [] );
    $options = wp_parse_args( $options, [
        'paging'    => 1,
        'searching' => 1,
        'ordering'  => 1,
    ] );

    // Prepare data for localization.
    $localize_data = [
        'paging'    => (bool) $options['paging'],
        'searching' => (bool) $options['searching'],
        'ordering'  => (bool) $options['ordering'],
    ];

    // Localize script with options.
    wp_localize_script( 'table-block-enhancer-frontend-script', 'tableBlockEnhancer', $localize_data );
}
add_action( 'wp_enqueue_scripts', 'table_block_enhancer_enqueue_frontend_assets' );

/**
 * Enqueue admin styles for the settings page.
 *
 * Enqueues custom styles for the plugin's settings page in the admin dashboard.
 *
 * @param string $hook_suffix The current admin page.
 * 
 * @since  1.0.0
 * @return void
 */
function table_block_enhancer_enqueue_admin_styles( $hook_suffix ) {
    // Only enqueue styles on the plugin's settings page.
    if ( 'settings_page_table-block-enhancer' !== $hook_suffix ) {
        return;
    }

    wp_enqueue_style(
        'table-block-enhancer-admin-style',
        plugin_dir_url( __FILE__ ) . 'assets/css/table-block-enhancer-admin.css',
        [],
        TABLE_BLOCK_ENHANCER_VERSION
    );
}
add_action( 'admin_enqueue_scripts', 'table_block_enhancer_enqueue_admin_styles' );

/**
 * Register plugin settings.
 *
 * Registers the settings for the plugin's options page.
 *
 * @since  1.0.0
 * @return void
 */
function table_block_enhancer_register_settings() {
    register_setting(
        'table_block_enhancer_options_group',
        'table_block_enhancer_options',
        [ 'table_block_enhancer_sanitize_options' ]
    );
}
add_action( 'admin_init', 'table_block_enhancer_register_settings' );

/**
 * Add settings page to the WordPress admin menu.
 *
 * Adds the plugin's settings page under the Settings menu in the admin dashboard.
 *
 * @since  1.0.0
 * @return void
 */
function table_block_enhancer_add_settings_page() {
    add_options_page(
        esc_html__( 'Table Block Enhancer Settings', 'table-block-enhancer' ),
        esc_html__( 'Table Block Enhancer', 'table-block-enhancer' ),
        'manage_options',
        'table-block-enhancer',
        'table_block_enhancer_render_settings_page'
    );
}
add_action( 'admin_menu', 'table_block_enhancer_add_settings_page' );

/**
 * Render the plugin's settings page.
 *
 * Outputs the settings page HTML.
 *
 * @since  1.0.0
 * @return void
 */
function table_block_enhancer_render_settings_page() {
    // Check if the current user has permission to manage options.
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Get the plugin options.
    $options = get_option( 'table_block_enhancer_options', [] );
    $options = wp_parse_args( $options, [
        'paging'    => 1,
        'searching' => 1,
        'ordering'  => 1,
    ] );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Table Block Enhancer Settings', 'table-block-enhancer' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            // Output security fields for the registered setting "table_block_enhancer_options_group".
            settings_fields( 'table_block_enhancer_options_group' );

            // Get the options again, in case they have been updated.
            $options = get_option( 'table_block_enhancer_options', [] );
            $options = wp_parse_args( $options, [
                'paging'    => 1,
                'searching' => 1,
                'ordering'  => 1,
            ] );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Enable Paging', 'table-block-enhancer' ); ?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="table_block_enhancer_options[paging]" value="1" <?php checked( $options['paging'], 1 ); ?> />
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Enable Searching', 'table-block-enhancer' ); ?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="table_block_enhancer_options[searching]" value="1" <?php checked( $options['searching'], 1 ); ?> />
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Enable Ordering', 'table-block-enhancer' ); ?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="table_block_enhancer_options[ordering]" value="1" <?php checked( $options['ordering'], 1 ); ?> />
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Sanitize plugin options.
 *
 * Sanitizes the options input from the settings page before saving.
 *
 * @param array $input The input options.
 *
 * @since  1.0.0
 * @return array The sanitized options.
 */
function table_block_enhancer_sanitize_options( $input ) {
    $output = [];

    // Sanitize the paging option.
    $output['paging'] = isset( $input['paging'] ) ? 1 : 0;
    // Sanitize the searching option.
    $output['searching'] = isset( $input['searching'] ) ? 1 : 0;
    // Sanitize the ordering option.
    $output['ordering'] = isset( $input['ordering'] ) ? 1 : 0;

    return $output;
}
