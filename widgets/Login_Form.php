<?php

namespace ElementorLoginRegister\Widgets;

use ElementorLoginRegister\Widgets\Traits\Login_Fields_Trait;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Login_Form extends Widget_Base {

	use Login_Fields_Trait;

	public function get_name(): string {
		return 'login_form';
	}

	public function get_title(): string {
		return esc_html__( 'Login Form', 'elementor-login-register' );
	}

	public function get_icon(): string {
		return 'eicon-lock-user';
	}

	public function get_categories(): array {
		return [ 'login-register' ];
	}

	public function get_style_depends(): array {
		return [ 'llr-login-register' ];
	}

	public function get_script_depends(): array {
		return [ 'llr-login-register' ];
	}

	protected function register_controls(): void {
		$this->register_login_content_controls();
	}

	protected function render(): void {
		$this->render_login_form_markup( $this->get_settings_for_display() );
	}
}
