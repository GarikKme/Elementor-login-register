<?php

namespace ElementorLoginRegister\Widgets;

use ElementorLoginRegister\Widgets\Traits\Registration_Fields_Trait;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Registration_Form extends Widget_Base {

	use Registration_Fields_Trait;

	public function get_name(): string {
		return 'registration_form';
	}

	public function get_title(): string {
		return esc_html__( 'Registration Form', 'elementor-login-register' );
	}

	public function get_icon(): string {
		return 'eicon-person';
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
		$this->register_registration_content_controls();
	}

	protected function render(): void {
		$this->render_registration_form_markup( $this->get_settings_for_display() );
	}
}
