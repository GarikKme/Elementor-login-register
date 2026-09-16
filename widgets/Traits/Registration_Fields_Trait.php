<?php

namespace ElementorLoginRegister\Widgets\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared Content controls + render markup for the registration form fields.
 * Used by Registration_Form (standalone) and Login_Register_Tabs (composed).
 */
trait Registration_Fields_Trait {

	protected function register_registration_content_controls( string $id_prefix = '', string $label_prefix = '' ): void {
		$this->start_controls_section(
			$id_prefix . 'section_form_settings',
			[
				'label' => $label_prefix . esc_html__( 'Form Settings', 'elementor-login-register' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			$id_prefix . 'redirect_url',
			[
				'label'       => esc_html__( 'Redirect URL After Registration', 'elementor-login-register' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'https://your-site.com/welcome', 'elementor-login-register' ),
				'default'     => '',
			]
		);

		$this->add_control(
			$id_prefix . 'require_name_fields',
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
			$id_prefix . 'section_text_labels',
			[
				'label' => $label_prefix . esc_html__( 'Text Labels', 'elementor-login-register' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			$id_prefix . 'label_first_name',
			[
				'label'   => esc_html__( 'First Name Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'First Name', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			$id_prefix . 'label_last_name',
			[
				'label'   => esc_html__( 'Last Name Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Last Name', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			$id_prefix . 'label_email',
			[
				'label'   => esc_html__( 'Email Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Email', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			$id_prefix . 'label_username',
			[
				'label'   => esc_html__( 'Username Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Username', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			$id_prefix . 'label_password',
			[
				'label'   => esc_html__( 'Password Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Password', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			$id_prefix . 'label_password_confirm',
			[
				'label'   => esc_html__( 'Confirm Password Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Confirm Password', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			$id_prefix . 'label_submit',
			[
				'label'   => esc_html__( 'Submit Button Text', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Register', 'elementor-login-register' ),
			]
		);

		$this->end_controls_section();
	}

	protected function register_registration_style_controls( string $id_prefix = '', string $label_prefix = '' ): void {
		$input_selector = '{{WRAPPER}} .llr-register-form__form input[type="text"], {{WRAPPER}} .llr-register-form__form input[type="email"], {{WRAPPER}} .llr-register-form__form input[type="password"]';
		$submit_selector = '{{WRAPPER}} .llr-register-form__submit';

		$this->start_controls_section(
			$id_prefix . 'section_register_style',
			[
				'label' => $label_prefix . esc_html__( 'Registration Form Style', 'elementor-login-register' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			$id_prefix . 'register_heading_style_input',
			[
				'label' => esc_html__( 'Input Fields', 'elementor-login-register' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $id_prefix . 'register_input_typography',
				'label'    => esc_html__( 'Typography', 'elementor-login-register' ),
				'selector' => $input_selector,
			]
		);

		$this->add_control(
			$id_prefix . 'register_input_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e1e1e',
				'selectors' => [
					$input_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$id_prefix . 'register_input_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					$input_selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$id_prefix . 'register_input_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cccccc',
				'selectors' => [
					$input_selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$id_prefix . 'register_heading_style_submit',
			[
				'label'     => esc_html__( 'Submit Button', 'elementor-login-register' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $id_prefix . 'register_submit_typography',
				'label'    => esc_html__( 'Typography', 'elementor-login-register' ),
				'selector' => $submit_selector,
			]
		);

		$this->add_control(
			$id_prefix . 'register_submit_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					$submit_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$id_prefix . 'register_submit_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2271b1',
				'selectors' => [
					$submit_selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$id_prefix . 'register_submit_bg_hover_color',
			[
				'label'     => esc_html__( 'Hover Background Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#135e96',
				'selectors' => [
					$submit_selector . ':hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$id_prefix . 'register_heading_style_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'elementor-login-register' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			$id_prefix . 'register_fields_gap',
			[
				'label'      => esc_html__( 'Gap Between Fields', 'elementor-login-register' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .llr-register-form__field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render_registration_form_markup( array $settings, string $id_prefix = '', string $element_id_prefix = 'llr-register' ): void {
		$redirect_url        = $settings[ $id_prefix . 'redirect_url' ] ?? '';
		$require_name_fields = 'yes' === ( $settings[ $id_prefix . 'require_name_fields' ] ?? '' );

		$label_first_name       = $settings[ $id_prefix . 'label_first_name' ] ?? '';
		$label_last_name        = $settings[ $id_prefix . 'label_last_name' ] ?? '';
		$label_email             = $settings[ $id_prefix . 'label_email' ] ?? '';
		$label_username          = $settings[ $id_prefix . 'label_username' ] ?? '';
		$label_password          = $settings[ $id_prefix . 'label_password' ] ?? '';
		$label_password_confirm = $settings[ $id_prefix . 'label_password_confirm' ] ?? '';
		$label_submit            = $settings[ $id_prefix . 'label_submit' ] ?? '';

		$widget_id            = $element_id_prefix . '-' . $this->get_id();
		$name_field_required  = $require_name_fields ? 'required' : '';
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
