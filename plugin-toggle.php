<?php
/**
 * Plugin Toggle
 *
 * @package PluginToggle
 * @author Brady Vercher
 * @copyright Copyright (c) 2014, Blazer Six, Inc.
 * @license GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Plugin Toggle
 * Plugin URI: http://www.blazersix.com/
 * Description: Quickly toggle plugin activation status from the toolbar.
 * Version: 1.1.6
 * Author: Blazer Six
 * Author URI: http://www.blazersix.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: plugin-toggle
 * Domain Path: /languages
 */

/**
 * Plugin directory URL.
 */
if ( ! defined( 'PLUGINTOGGLE_URL' ) ) {
	define( 'PLUGINTOGGLE_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Plugin directory path.
 */
if ( ! defined( 'PLUGINTOGGLE_DIR' ) ) {
	define( 'PLUGINTOGGLE_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Include classes.
 */
require( PLUGINTOGGLE_DIR . 'includes/class-plugintoggle.php' );
require( PLUGINTOGGLE_DIR . 'includes/class-plugintoggle-plugin.php' );
require( PLUGINTOGGLE_DIR . 'includes/class-plugintoggle-toolbar.php' );

/**
 * Localize the plugin.
 *
 * @since 1.2.0
 */
function plugintoggle_load_textdomain() {
	$plugin_rel_path = dirname( plugin_basename( __FILE__ ) ) . '/languages';
	load_plugin_textdomain( 'plugin-toggle', false, $plugin_rel_path );
}
add_action( 'plugins_loaded', 'plugintoggle_load_textdomain' );

$plugintoggle = new PluginToggle();

add_action( 'init', array( $plugintoggle, 'load_plugin' ) );
