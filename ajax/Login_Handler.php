<?php

namespace ElementorLoginRegister\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Login_Handler {

	public function __construct() {
		add_action( 'wp_ajax_llr_login', [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_llr_login', [ $this, 'handle' ] );
	}

	public function handle(): void {
		if ( ! check_ajax_referer( 'llr_login_action', 'nonce', false ) ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'Security check failed. Please refresh the page and try again.', 'elementor-login-register' ) ],
				403
			);
		}

		if ( '' !== ELR_RECAPTCHA_SECRET_KEY ) {
			$recaptcha_token = isset( $_POST['g-recaptcha-response'] )
				? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) )
				: '';

			if ( '' === $recaptcha_token ) {
				wp_send_json_error(
					[ 'message' => esc_html__( 'Please complete the reCAPTCHA verification.', 'elementor-login-register' ) ],
					400
				);
			}

			$verify_response = wp_remote_post(
				'https://www.google.com/recaptcha/api/siteverify',
				[
					'body' => [
						'secret'   => ELR_RECAPTCHA_SECRET_KEY,
						'response' => $recaptcha_token,
						'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
					],
				]
			);

			if ( is_wp_error( $verify_response ) ) {
				wp_send_json_error(
					[ 'message' => esc_html__( 'Please complete the reCAPTCHA verification.', 'elementor-login-register' ) ],
					400
				);
			}

			$verify_body = json_decode( wp_remote_retrieve_body( $verify_response ), true );

			if ( empty( $verify_body['success'] ) ) {
				wp_send_json_error(
					[ 'message' => esc_html__( 'Please complete the reCAPTCHA verification.', 'elementor-login-register' ) ],
					400
				);
			}
		}

		$login_input = '';

		foreach ( [ 'llr_login', 'llr_email', 'llr_username' ] as $field ) {
			if ( ! empty( $_POST[ $field ] ) ) {
				$login_input = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
				break;
			}
		}

		if ( is_email( $login_input ) ) {
			$login_input = sanitize_email( $login_input );
		}

		$password = isset( $_POST['llr_password'] ) ? (string) wp_unslash( $_POST['llr_password'] ) : '';

		if ( '' === $login_input || '' === $password ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'Invalid credentials. Please try again.', 'elementor-login-register' ) ],
				400
			);
		}

		$remember = ! empty( $_POST['llr_remember'] );

		$user_login = $login_input;

		if ( is_email( $login_input ) ) {
			$user = get_user_by( 'email', $login_input );

			if ( $user instanceof \WP_User ) {
				$user_login = $user->user_login;
			}
		}

		$credentials = [
			'user_login'    => $user_login,
			'user_password' => $password,
			'remember'      => $remember,
		];

		$result = wp_signon( $credentials, is_ssl() );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'Invalid credentials. Please try again.', 'elementor-login-register' ) ],
				401
			);
		}

		$redirect_url = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';

		wp_send_json_success(
			[
				'message'      => esc_html__( 'Login successful. Redirecting…', 'elementor-login-register' ),
				'redirect_url' => $redirect_url,
			]
		);
	}
}
