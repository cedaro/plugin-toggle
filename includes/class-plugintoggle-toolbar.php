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
 * Toolbar class.
 *
 * @package PluginToggle
 * @since 1.1.0
 */
class PluginToggle_Toolbar {
	/**
	 * The WP_Admin_Bar instance.
	 *
	 * @since 1.1.0
	 * @type WP_Admin_Bar
	 */
	protected $toolbar;

	/**
	 * The URL of the current request.
	 *
	 * @since 1.1.0
	 * @type string
	 */
	protected $current_url;

	/**
	 * Constructor method.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Admin_Bar $toolbar Toolbar object.
	 * @param array        $plugins List of plugins.
	 */
	public function __construct( WP_Admin_Bar $toolbar, $plugins ) {
		$this->toolbar = $toolbar;

		$this->add_top_level_node( count( $plugins ) );
		$this->add_group_to_node();

		$visible_plugins = 0;
		foreach ( $plugins as $plugin_file => $plugin ) {
			if ( ! $plugin->is_network_related() ) {
				$this->add_plugin_to_group( $plugin );
				$visible_plugins++;
			}
		}

		// Remove the top-level node if there aren't any visible plugins.
		if ( ! $visible_plugins ) {
			$this->toolbar->remove_node( 'plugin-toggle' );
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
	protected function add_top_level_node( $plugin_count ) {
		$node_args = array(
			'id'    => 'plugin-toggle',
			'title' => sprintf( '<span class="ab-icon"></span> <span class="ab-label">%s</span>', __( 'Plugins', 'plugin-toggle' ) ),
			'href'  => self_admin_url( 'plugins.php' ),
			'meta'  => array(
				'class' => ( $plugin_count > 20 ) ? 'has-many' : '',
			),
		);
		$this->toolbar->add_node( $node_args );
	}

	/**
	 * Add group to toolbar node.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Admin_Bar $toolbar Toolbar object.
	 */
	protected function add_group_to_node() {
		$node_args = array(
			'id'     => 'plugin-toggle-group',
			'group'  => true,
			'parent' => 'plugin-toggle',
		);
		$this->toolbar->add_node( $node_args );
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
	protected function add_plugin_to_group( PluginToggle_Plugin $plugin ) {
		$node_args = array(
			'id'     => 'plugin-toggle_' . sanitize_title( $plugin->get_name() ),
			'title'  => $plugin->get_name(),
			'href'   => $plugin->get_toggle_url( $this->get_current_url() ),
			'parent' => 'plugin-toggle-group',
			'meta'   => array(
				'class' => $plugin->is_active() ? 'is-active' : '',
			),
		);
		$this->toolbar->add_node( $node_args );
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
}
