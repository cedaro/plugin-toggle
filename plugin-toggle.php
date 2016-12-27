<?php
/**
 * Plugin Toggle
 *
 * @package PluginToggle
 * @author Brady Vercher
 * @copyright Copyright (c) 2016, Cedaro, LLC
 * @license GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Plugin Toggle
 * Plugin URI:  https://wordpress.org/plugins/plugin-toggle/?utm_source=wordpress-plugin&utm_medium=link&utm_content=plugin-toggle-plugin-uri&utm_campaign=plugins
 * Description: Quickly toggle plugin activation status from the toolbar.
 * Version:     1.3.1
 * Author:      Cedaro
 * Author URI:  https://www.cedaro.com/?utm_source=wordpress-plugin&utm_medium=link&utm_content=plugin-toggle-author-uri&utm_campaign=plugins
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: plugin-toggle
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

$plugintoggle = new PluginToggle();

add_action( 'init', array( $plugintoggle, 'load_plugin' ) );
