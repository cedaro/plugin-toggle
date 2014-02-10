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
 * Main plugin class.
 *
 * @since 1.0.0
 */
class PluginToggle {
	/**
	 * The URL of the current request.
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $current_url;

	/**
	 * Load the plugin.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		add_action( 'admin_bar_menu', array( $this, 'setup_toolbar' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'wp_redirect', array( $this, 'redirect' ) );
		add_action( 'load-plugins.php', array( $this, 'flush_plugins_cache' ) );
	}

	/**
	 * Add the plugin list to the toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Admin_Bar $toolbar Toolbar object.
	 */
	public function setup_toolbar( $toolbar ) {
		global $wp;

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$plugins = $this->get_plugins();

		if ( empty( $plugins ) ) {
			return;
		}

		$this->add_top_level_node( $toolbar, count( $plugins ) );
		$this->add_group_to_node( $toolbar );

		$visible_plugins = 0;
		foreach ( $plugins as $plugin_file => $plugin_name ) {
			if ( ! $this->is_network_related_plugin( $plugin_file ) ) {
				$this->add_plugin_to_group( $toolbar, $plugin_file, $plugin_name );
				$visible_plugins++;
			}
		}

		// Remove the top-level node if there aren't any visible plugins.
		if ( ! $visible_plugins ) {
			$toolbar->remove_node( 'plugin-toggle' );
		}
	}

	/**
	 * Add top level toolbar node.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Admin_Bar $toolbar      Toolbar object.
	 * @param integer      $plugin_count Number of plugins installed.
	 */
	protected function add_top_level_node( WP_Admin_Bar $toolbar, $plugin_count ) {
		$node_args = array(
			'id'    => 'plugin-toggle',
			'title' => sprintf( '<span class="ab-icon"></span> <span class="ab-label">%s</span>', __( 'Plugins', 'plugin-toggle' ) ),
			'href'  => self_admin_url( 'plugins.php' ),
			'meta'  => array(
				'class' => ( $plugin_count > 20 ) ? 'has-many' : '',
			),
		);
		$toolbar->add_node( $node_args );
	}

	/**
	 * Add group to toolbar node.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Admin_Bar $toolbar Toolbar object.
	 */
	protected function add_group_to_node( WP_Admin_Bar $toolbar ) {
		$node_args = array(
			'id'     => 'plugin-toggle-group',
			'group'  => true,
			'parent' => 'plugin-toggle',
		);
		$toolbar->add_node( $node_args );
	}

	/**
	 * Add plugin to group.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Admin_Bar $toolbar     Toolbar object.
	 * @param string       $plugin_file Plugin basename.
	 * @param string       $plugin_name Plugin name.
	 */
	protected function add_plugin_to_group( WP_Admin_Bar $toolbar, $plugin_file, $plugin_name ) {
		$node_args = array(
			'id'     => 'plugin-toggle_' . sanitize_title( $plugin_name ),
			'title'  => $plugin_name,
			'href'   => $this->get_plugin_toggle_url( $plugin_file ),
			'parent' => 'plugin-toggle-group',
			'meta'   => array(
				'class' => is_plugin_active( $plugin_file ) ? 'is-active' : '',
			),
		);
		$toolbar->add_node( $node_args );
	}

	/**
	 * Check if the plugin is network activated, or is a network only plugin and this isn't a network admin screen.
	 *
	 * @since 1.1.0
	 *
	 * @param string $plugin_file Plugin basename.
	 *
	 * @return bool True if plugin is network activated, or is a network only plugin and this isn't a network admin screen.
	 */
	protected function is_network_related_plugin( $plugin_file ) {
		$screen  = is_admin() ? get_current_screen() : '';

		return is_multisite() &&
			( ! is_admin() || ! $screen->in_admin( 'network' ) ) &&
			( is_plugin_active_for_network( $plugin_file ) || is_network_only_plugin( $plugin_file ) );
	}

	/**
	 * Get a plugin activation or deactivation URL depending on the plugin's status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_file Plugin basename.
	 * @return string
	 */
	protected function get_plugin_toggle_url( $plugin_file ) {
		$action = 'activate';
		if ( is_plugin_active( $plugin_file ) ) {
			$action = 'deactivate';
		}

		$query_args = array(
			'action'                   => $action,
			'plugin'                   => $plugin_file,
			'plugintoggle_redirect_to' => $this->get_current_url(),
		);

		return wp_nonce_url(
			add_query_arg( $query_args, self_admin_url( 'plugins.php' )	),
			$action . '-plugin_' . $plugin_file
		);
	}

	/**
	 * Retrieve the list of installed plugins.
	 *
	 * The list is cached for a day so it doesn't have to be regenerated on
	 * every request. Visit the plugins page to flush the cache.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_plugins() {
		$plugins = get_transient( 'plugintoggle_plugins' );

		if ( ! $plugins ) {
			$plugins = get_plugins();

			// Only the plugin file and name are needed.
			// plugin_file => plugin_name
			$plugins = array_combine( array_keys( $plugins ), wp_list_pluck( $plugins, 'Name' ) );
			set_transient( 'plugintoggle_plugins', $plugins, DAY_IN_SECONDS );
		}

		return $plugins;
	}

	/**
	 * Get the URL for the current request.
	 *
	 * @since 1.0.0
	 * @link http://stephenharris.info/how-to-get-the-current-url-in-wordpress/
	 *
	 * @return string
	 */
	protected function get_current_url() {
		global $wp;

		if ( empty( $this->current_url ) ) {
			$url = is_admin() ? add_query_arg( array() ) : home_url( add_query_arg( array(), $wp->request ) );
			$url = remove_query_arg( array( '_wpnonce', 'redirect_to' ), $url );
			$this->current_url = $url;
		}

		return $this->current_url;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		// Look ma! No jQuery!
		wp_enqueue_script( 'plugin-toggle', plugin_dir_url( __FILE__ ) . 'plugin-toggle.js', array(), '1.0.0', true );
		wp_enqueue_style( 'plugin-toggle', plugin_dir_url( __FILE__ ) . 'plugin-toggle.css' );
	}

	/**
	 * Filter redirects and return back to the URL that was being viewed when a plugin was toggled.
	 *
	 * @since 1.0.0
	 *
	 * @param string $location Default redirection location.
	 * @return string
	 */
	public function redirect( $location ) {
		if ( false !== strpos( $location, 'plugins.php' ) && ! empty( $_REQUEST['plugintoggle_redirect_to'] ) ) {
			$redirect = wp_sanitize_redirect( $_REQUEST['plugintoggle_redirect_to'] );
			$location = wp_validate_redirect( $redirect, $location );
		}

		return $location;
	}

	/**
	 * Flush the cached list of plugins.
	 *
	 * @since 1.0.0
	 */
	public function flush_plugins_cache() {
		delete_transient( 'plugintoggle_plugins' );
	}
}

global $plugintoggle;
$plugintoggle = new PluginToggle();
add_action( 'plugins_loaded', array( $plugintoggle, 'load_plugin' ) );
