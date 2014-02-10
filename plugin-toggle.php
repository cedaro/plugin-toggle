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
 * Version: 1.0.0
 * Author: Blazer Six
 * Author URI: http://www.blazersix.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Include classes.
 */
require( dirname( __FILE__ ) . '/includes/class-plugintoggle.php' );
require( dirname( __FILE__ ) . '/includes/class-plugintoggle-plugin.php' );
require( dirname( __FILE__ ) . '/includes/class-plugintoggle-toolbar.php' );

$plugintoggle = new PluginToggle();

add_action( 'plugins_loaded', array( $plugintoggle, 'load_plugin' ) );
