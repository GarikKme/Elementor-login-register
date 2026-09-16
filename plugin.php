<?php
/**
 * Plugin Name:       Elementor Login Register Widgets
 * Description:       Adds login and registration widgets for Elementor.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            CBA
 * Text Domain:       elementor-login-register
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELR_WIDGETS_VERSION', '1.0.0' );
define( 'ELR_WIDGETS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ELR_WIDGETS_URL', plugin_dir_url( __FILE__ ) );

register_activation_hook( __FILE__, 'elr_widgets_activate' );

function elr_widgets_activate() {
	if ( version_compare( PHP_VERSION, '8.1', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			esc_html__( 'Elementor Login Register Widgets requires PHP 8.1 or higher. Please upgrade PHP and reactivate the plugin.', 'elementor-login-register' ),
			esc_html__( 'Plugin activation error', 'elementor-login-register' ),
			array( 'back_link' => true )
		);
	}
}

add_action( 'plugins_loaded', 'elr_widgets_init' );

function elr_widgets_init() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'elr_widgets_missing_elementor_notice' );
		return;
	}

	require_once ELR_WIDGETS_PATH . 'vendor/autoload.php';

	\ElementorLoginRegister\Includes\Plugin::instance();
}

function elr_widgets_missing_elementor_notice() {
	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php
			esc_html_e( 'Elementor Login Register Widgets requires Elementor to be installed and active.', 'elementor-login-register' );
			?>
		</p>
	</div>
	<?php
}
