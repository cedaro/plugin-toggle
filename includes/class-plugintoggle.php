<?php
/**
 * Plugin Toggle
 *
 * @package PluginToggle
 * @author Brady Vercher
 * @copyright Copyright (c) 2014, Blazer Six, Inc.
 * @license GPL-2.0+
 * @since 1.1.0
 */

/**
 * Main plugin class.
 *
 * @package PluginToggle
 * @since 1.0.0
 */
class PluginToggle {
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
		add_filter( 'wp_redirect', array( $this, 'redirect' ), 1 );
		add_action( 'load-plugins.php', array( $this, 'flush_plugins_cache' ) );

		// Refresh the plugins cache when the plugins directory is changed
		// or active plugins are modified.
		add_filter( 'plugintoggle_force_plugins_refresh', array( $this, 'plugins_changed' ) );
		add_filter( 'plugintoggle_force_plugins_refresh', array( $this, 'active_plugins_changed' ) );
	}

	/**
	 * Add the plugin list to the toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Admin_Bar $toolbar Toolbar object.
	 */
	public function setup_toolbar( $toolbar ) {
		$plugins = $this->get_plugins();

		if ( empty( $plugins ) ) {
			return;
		}

		new PluginToggle_Toolbar( $toolbar, $plugins );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		// Look ma! No jQuery!
		wp_enqueue_script( 'plugin-toggle', PLUGINTOGGLE_URL . 'assets/js/plugin-toggle.js', array(), '1.0.0', true );
		wp_enqueue_style( 'plugin-toggle', PLUGINTOGGLE_URL . 'assets/css/plugin-toggle.css' );
	}

	/**
	 * Filter redirects and return back to the URL that was being viewed when a
	 * plugin was toggled.
	 *
	 * @since 1.0.0
	 *
	 * @param string $location Default redirection location.
	 * @return string
	 */
	public function redirect( $location ) {
		if (
			false !== strpos( $location, 'plugins.php' ) &&
			! empty( $_REQUEST['plugintoggle_redirect_to'] ) &&
			false === strpos( $location, 'error=true' )
		) {
			$redirect = rawurldecode( $_REQUEST['plugintoggle_redirect_to'] );
			$redirect = wp_sanitize_redirect( $redirect );
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

		if (
			! $plugins ||
			! ( reset( $plugins ) instanceof PluginToggle_Plugin ) ||
			apply_filters( 'plugintoggle_force_plugins_refresh', false )
		) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$plugins = array();
			foreach ( get_plugins() as $plugin_file => $plugin_data ) {
				$plugin = new PluginToggle_Plugin( $plugin_file, $plugin_data );
				$plugins[ $plugin_file ] = $plugin;
			}

			set_transient( 'plugintoggle_plugins', $plugins, DAY_IN_SECONDS );
		}

		return $plugins;
	}

	/**
	 * Whether any plugins have been added or removed since last checked.
	 *
	 * This is just a basic scan of the top level plugin directory and doesn't
	 * actually check for valid plugin files.
	 *
	 * @since 1.1.4
	 *
	 * @param bool $refresh Whether to refresh the plugin cache.
	 * @return bool
	 */
	public function plugins_changed( $refresh ) {
		$data = scandir( WP_PLUGIN_DIR );
		return $this->has_changed( 'plugins', $data ) ? true : $refresh;
	}

	/**
	 * Whether the active plugins have changed.
	 *
	 * @since 1.1.4
	 *
	 * @param bool $refresh Whether to refresh the plugin cache.
	 * @return bool
	 */
	public function active_plugins_changed( $refresh ) {
		$data = (array) get_option( 'active_plugins', array() );
		return $this->has_changed( 'active_plugins', $data ) ? true : $refresh;
	}

	/**
	 * Determine whether data has changed since last checked.
	 *
	 * @since 1.1.4
	 *
	 * @param string $key An identifier used to store the data hash.
	 * @param mixed $data Data to compare.
	 * @return bool
	 */
	protected function has_changed( $key, $data ) {
		$option_name = sprintf( 'plugintoggle_hash-%s', sanitize_key( $key ) );
		$hash = md5( maybe_serialize( $data ) );
		$hash_changed = ( get_option( $option_name, '' ) !== $hash );

		if ( $hash_changed ) {
			update_option( $option_name, $hash );
		}

		return $hash_changed;
	}
}
