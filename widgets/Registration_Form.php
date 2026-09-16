<?php

namespace ElementorLoginRegister\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Registration_Form extends Widget_Base {

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
		$this->start_controls_section(
			'section_form_settings',
			[
				'label' => esc_html__( 'Form Settings', 'elementor-login-register' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'redirect_url',
			[
				'label'       => esc_html__( 'Redirect URL After Registration', 'elementor-login-register' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'https://your-site.com/welcome', 'elementor-login-register' ),
				'default'     => '',
			]
		);

		$this->add_control(
			'require_name_fields',
			[
				'label'        => esc_html__( 'Require First/Last Name', 'elementor-login-register' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Required', 'elementor-login-register' ),
				'label_off'    => esc_html__( 'Optional', 'elementor-login-register' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_labels',
			[
				'label' => esc_html__( 'Text Labels', 'elementor-login-register' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'label_first_name',
			[
				'label'   => esc_html__( 'First Name Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'First Name', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_last_name',
			[
				'label'   => esc_html__( 'Last Name Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Last Name', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_email',
			[
				'label'   => esc_html__( 'Email Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Email', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_username',
			[
				'label'   => esc_html__( 'Username Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Username', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_password',
			[
				'label'   => esc_html__( 'Password Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Password', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_password_confirm',
			[
				'label'   => esc_html__( 'Confirm Password Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Confirm Password', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_submit',
			[
				'label'   => esc_html__( 'Submit Button Text', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Register', 'elementor-login-register' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$redirect_url         = $settings['redirect_url'] ?? '';
		$require_name_fields  = 'yes' === ( $settings['require_name_fields'] ?? '' );

		$label_first_name       = $settings['label_first_name'] ?? '';
		$label_last_name        = $settings['label_last_name'] ?? '';
		$label_email             = $settings['label_email'] ?? '';
		$label_username          = $settings['label_username'] ?? '';
		$label_password          = $settings['label_password'] ?? '';
		$label_password_confirm = $settings['label_password_confirm'] ?? '';
		$label_submit            = $settings['label_submit'] ?? '';

		$widget_id  = 'llr-register-' . $this->get_id();
		$name_field_required = $require_name_fields ? 'required' : '';
		?>
		<div class="llr-register-form">
			<form class="llr-register-form__form" method="post" novalidate>
				<?php wp_nonce_field( 'llr_register_action', 'nonce' ); ?>
				<input type="hidden" name="action" value="llr_register">
				<?php if ( '' !== $redirect_url ) : ?>
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>">
				<?php endif; ?>

				<p class="llr-register-form__field">
					<label for="<?php echo esc_attr( $widget_id ); ?>-first-name">
						<?php echo esc_html( $label_first_name ); ?>
					</label>
					<input
						type="text"
						id="<?php echo esc_attr( $widget_id ); ?>-first-name"
						name="llr_first_name"
						<?php echo esc_attr( $name_field_required ); ?>
					>
				</p>

				<p class="llr-register-form__field">
					<label for="<?php echo esc_attr( $widget_id ); ?>-last-name">
						<?php echo esc_html( $label_last_name ); ?>
					</label>
					<input
						type="text"
						id="<?php echo esc_attr( $widget_id ); ?>-last-name"
						name="llr_last_name"
						<?php echo esc_attr( $name_field_required ); ?>
					>
				</p>

				<p class="llr-register-form__field">
					<label for="<?php echo esc_attr( $widget_id ); ?>-email">
						<?php echo esc_html( $label_email ); ?>
					</label>
					<input
						type="email"
						id="<?php echo esc_attr( $widget_id ); ?>-email"
						name="llr_email"
						required
					>
				</p>

				<p class="llr-register-form__field">
					<label for="<?php echo esc_attr( $widget_id ); ?>-username">
						<?php echo esc_html( $label_username ); ?>
					</label>
					<input
						type="text"
						id="<?php echo esc_attr( $widget_id ); ?>-username"
						name="llr_username"
						required
					>
				</p>

				<p class="llr-register-form__field">
					<label for="<?php echo esc_attr( $widget_id ); ?>-password">
						<?php echo esc_html( $label_password ); ?>
					</label>
					<input
						type="password"
						id="<?php echo esc_attr( $widget_id ); ?>-password"
						name="llr_password"
						required
					>
				</p>

				<p class="llr-register-form__field">
					<label for="<?php echo esc_attr( $widget_id ); ?>-password-confirm">
						<?php echo esc_html( $label_password_confirm ); ?>
					</label>
					<input
						type="password"
						id="<?php echo esc_attr( $widget_id ); ?>-password-confirm"
						name="llr_password_confirm"
						required
					>
				</p>

				<p class="llr-register-form__field llr-register-form__field--submit">
					<button type="submit" class="llr-register-form__submit">
						<?php echo esc_html( $label_submit ); ?>
					</button>
				</p>
			</form>

			<div class="llr-form-message" role="alert"></div>
		</div>
		<?php
	}
}
