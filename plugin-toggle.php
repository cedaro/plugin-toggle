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
 * Version: 1.1.4
 * Author: Blazer Six
 * Author URI: http://www.blazersix.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
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
