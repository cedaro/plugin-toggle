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
 * Plugin class.
 *
 * @package PluginToggle
 * @since 1.1.0
 */
class PluginToggle_Plugin {
	/**
	 * Plugin name.
	 *
	 * @since 1.1.0
	 * @type string
	 */
	protected $name;

	/**
	 * Plugin file.
	 *
	 * Relative path from the plugins directory to the main plugin file.
	 *
	 * @since 1.1.0
	 * @type string
	 */
	protected $file;

	/**
	 * Plugin activation status.
	 *
	 * @since 1.1.0
	 * @type string active|inactive
	 */
	protected $status;

	/**
	 * Plugin network status.
	 *
	 * @since 1.1.0
	 * @type string network-activated|network-only
	 */
	protected $network_status;

	/**
	 * Constructor method.
	 *
	 * Initializes the plugin and sets up object properties.
	 *
	 * @since 1.1.0
	 *
	 * @param string $file Plugin file.
	 * @param array  $data Optional. Plugin data.
	 */
	public function __construct( $file, $data = array() ) {
		$this->file = $file;

		if ( empty( $data ) ) {
			$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $file, false, false );
		}

		$this->name = $data['Name'];
		$this->set_status();
		$this->set_network_status();
	}

	/**
	 * Whether the plugin is active.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_active() {
		return 'active' == $this->status;
	}

	/**
	 * Retrieve the plugin name.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Retrieve the plugin file.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_file() {
		return $this->file;
	}

	/**
	 * Whether the plugin is network only or activated for the network.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_network_related() {
		return is_multisite() &&
			( ! is_admin() || ! get_current_screen()->in_admin( 'network' ) ) &&
			! empty( $this->network_status );
	}

	/**
	 * Get a plugin activation or deactivation URL depending on the plugin's current status.
	 *
	 * @since 1.1.0
	 *
	 * @param  string $redirect Optional. Where to redirect after toggling the plugin.
	 * @return string
	 */
	public function get_toggle_url( $redirect = '' ) {
		$action = $this->is_active() ? 'deactivate' : 'activate';

		$query_args = array(
			'action' => $action,
			'plugin' => $this->file,
		);

		if ( ! empty( $redirect ) ) {
			$query_args['plugintoggle_redirect_to'] = rawurlencode( $redirect );
		}

		return wp_nonce_url(
			add_query_arg(
				$query_args,
				self_admin_url( 'plugins.php' )
			),
			$action . '-plugin_' . $this->file
		);
	}

	/**
	 * Store the plugin's activation status.
	 *
	 * @since 1.1.0
	 */
	protected function set_status() {
		$this->status = is_plugin_active( $this->file ) ? 'active' : 'inactive';
	}

	/**
	 * Store the plugin's network activation status.
	 *
	 * @since 1.1.0
	 */
	protected function set_network_status() {
		if ( ! is_multisite() ) {
			return;
		}

		if ( is_plugin_active_for_network( $this->file ) ) {
			$this->network_status = 'network-activated';
		} elseif ( is_network_only_plugin( $this->file ) ) {
			$this->network_status = 'network-only';
		}
	}
}
