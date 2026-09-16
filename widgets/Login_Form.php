<?php

namespace ElementorLoginRegister\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Login_Form extends Widget_Base {

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
		$this->start_controls_section(
			'section_form_settings',
			[
				'label' => esc_html__( 'Form Settings', 'elementor-login-register' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'login_by',
			[
				'label'   => esc_html__( 'Login By', 'elementor-login-register' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'email'    => esc_html__( 'Email', 'elementor-login-register' ),
					'username' => esc_html__( 'Username', 'elementor-login-register' ),
					'both'     => esc_html__( 'Username or Email', 'elementor-login-register' ),
				],
			]
		);

		$this->add_control(
			'show_remember_me',
			[
				'label'        => esc_html__( 'Show "Remember Me"', 'elementor-login-register' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'elementor-login-register' ),
				'label_off'    => esc_html__( 'Hide', 'elementor-login-register' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_forgot_password',
			[
				'label'        => esc_html__( 'Show "Forgot Password" Link', 'elementor-login-register' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'elementor-login-register' ),
				'label_off'    => esc_html__( 'Hide', 'elementor-login-register' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'redirect_url',
			[
				'label'       => esc_html__( 'Redirect URL After Login', 'elementor-login-register' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'https://your-site.com/dashboard', 'elementor-login-register' ),
				'default'     => '',
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
			'label_username_email',
			[
				'label'   => esc_html__( 'Username/Email Field Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Username or Email', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_password',
			[
				'label'   => esc_html__( 'Password Field Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Password', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_submit',
			[
				'label'   => esc_html__( 'Submit Button Text', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Log In', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_remember_me',
			[
				'label'   => esc_html__( 'Remember Me Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Remember Me', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'label_forgot_password',
			[
				'label'   => esc_html__( 'Forgot Password Link Text', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Forgot your password?', 'elementor-login-register' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$login_by              = $settings['login_by'] ?? 'both';
		$show_remember_me      = 'yes' === ( $settings['show_remember_me'] ?? '' );
		$show_forgot_password  = 'yes' === ( $settings['show_forgot_password'] ?? '' );
		$redirect_url          = $settings['redirect_url'] ?? '';

		$label_username_email  = $settings['label_username_email'] ?? '';
		$label_password        = $settings['label_password'] ?? '';
		$label_submit          = $settings['label_submit'] ?? '';
		$label_remember_me     = $settings['label_remember_me'] ?? '';
		$label_forgot_password = $settings['label_forgot_password'] ?? '';

		switch ( $login_by ) {
			case 'email':
				$field_type = 'email';
				$field_name = 'llr_email';
				break;
			case 'username':
				$field_type = 'text';
				$field_name = 'llr_username';
				break;
			default:
				$field_type = 'text';
				$field_name = 'llr_login';
				break;
		}

		$widget_id = 'llr-login-' . $this->get_id();
		?>
		<div class="llr-login-form">
			<form class="llr-login-form__form" method="post" novalidate>
				<?php wp_nonce_field( 'llr_login_action', 'nonce' ); ?>
				<input type="hidden" name="action" value="llr_login">
				<?php if ( '' !== $redirect_url ) : ?>
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>">
				<?php endif; ?>

				<p class="llr-login-form__field">
					<label for="<?php echo esc_attr( $widget_id ); ?>-login">
						<?php echo esc_html( $label_username_email ); ?>
					</label>
					<input
						type="<?php echo esc_attr( $field_type ); ?>"
						id="<?php echo esc_attr( $widget_id ); ?>-login"
						name="<?php echo esc_attr( $field_name ); ?>"
						required
					>
				</p>

				<p class="llr-login-form__field">
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

				<?php if ( $show_remember_me ) : ?>
					<p class="llr-login-form__field llr-login-form__field--checkbox">
						<label for="<?php echo esc_attr( $widget_id ); ?>-remember">
							<input
								type="checkbox"
								id="<?php echo esc_attr( $widget_id ); ?>-remember"
								name="llr_remember"
								value="forever"
							>
							<?php echo esc_html( $label_remember_me ); ?>
						</label>
					</p>
				<?php endif; ?>

				<p class="llr-login-form__field llr-login-form__field--submit">
					<button type="submit" class="llr-login-form__submit">
						<?php echo esc_html( $label_submit ); ?>
					</button>
				</p>

				<?php if ( $show_forgot_password ) : ?>
					<p class="llr-login-form__field llr-login-form__field--forgot">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
							<?php echo esc_html( $label_forgot_password ); ?>
						</a>
					</p>
				<?php endif; ?>
			</form>

			<div class="llr-form-message" role="alert"></div>
		</div>
		<?php
	}
}
