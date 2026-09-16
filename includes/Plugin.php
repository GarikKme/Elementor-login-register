<?php

namespace ElementorLoginRegister\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {

	private static ?Plugin $instance = null;

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_categories' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );

		new \ElementorLoginRegister\Ajax\Login_Handler();
		new \ElementorLoginRegister\Ajax\Registration_Handler();
	}

	public function register_categories( \Elementor\Elements_Manager $elements_manager ): void {
		$elements_manager->add_category(
			'login-register',
			[
				'title' => esc_html__( 'Login & Registration', 'elementor-login-register' ),
				'icon'  => 'eicon-lock-user',
			]
		);
	}

	public function register_widgets( \Elementor\Widgets_Manager $widgets_manager ): void {
		$widgets_manager->register( new \ElementorLoginRegister\Widgets\Login_Form() );
		$widgets_manager->register( new \ElementorLoginRegister\Widgets\Registration_Form() );
	}

	public function register_assets(): void {
		wp_register_style(
			'llr-login-register',
			ELR_WIDGETS_URL . 'assets/css/login-register.css',
			[],
			ELR_WIDGETS_VERSION
		);

		wp_register_script(
			'llr-login-register',
			ELR_WIDGETS_URL . 'assets/js/login-register.js',
			[ 'jquery' ],
			ELR_WIDGETS_VERSION,
			true
		);

		wp_localize_script(
			'llr-login-register',
			'llrLoginRegister',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'homeUrl' => home_url( '/' ),
			]
		);
	}
}
