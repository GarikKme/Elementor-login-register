<?php

namespace ElementorLoginRegister\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Registration_Handler {

	public function __construct() {
		add_action( 'wp_ajax_llr_register', [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_llr_register', [ $this, 'handle' ] );
	}

	public function handle(): void {
		if ( ! check_ajax_referer( 'llr_register_action', 'nonce', false ) ) {
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

		$first_name = isset( $_POST['llr_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['llr_first_name'] ) ) : '';
		$last_name  = isset( $_POST['llr_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['llr_last_name'] ) ) : '';
		$username   = isset( $_POST['llr_username'] ) ? sanitize_text_field( wp_unslash( $_POST['llr_username'] ) ) : '';
		$email      = isset( $_POST['llr_email'] ) ? sanitize_email( wp_unslash( $_POST['llr_email'] ) ) : '';

		$password         = isset( $_POST['llr_password'] ) ? (string) wp_unslash( $_POST['llr_password'] ) : '';
		$password_confirm = isset( $_POST['llr_password_confirm'] ) ? (string) wp_unslash( $_POST['llr_password_confirm'] ) : '';

		if ( '' === $email || '' === $username || '' === $password || '' === $password_confirm ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'Please fill in all required fields.', 'elementor-login-register' ) ],
				400
			);
		}

		if ( ! is_email( $email ) ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'Please enter a valid email address.', 'elementor-login-register' ) ],
				400
			);
		}

		if ( $password !== $password_confirm ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'Passwords do not match.', 'elementor-login-register' ) ],
				400
			);
		}

		if ( strlen( $password ) < 8 ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'Password must be at least 8 characters long.', 'elementor-login-register' ) ],
				400
			);
		}

		if ( email_exists( $email ) ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'This email is already registered.', 'elementor-login-register' ) ],
				409
			);
		}

		if ( username_exists( $username ) ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'This username is already taken.', 'elementor-login-register' ) ],
				409
			);
		}

		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error(
				[ 'message' => esc_html__( 'Could not create your account. Please try again.', 'elementor-login-register' ) ],
				500
			);
		}

		wp_update_user(
			[
				'ID'         => $user_id,
				'first_name' => $first_name,
				'last_name'  => $last_name,
			]
		);

		$user = get_user_by( 'id', $user_id );

		if ( $user instanceof \WP_User ) {
			$user->set_role( 'subscriber' );
		}

		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		$redirect_url = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';

		wp_send_json_success(
			[
				'message'      => esc_html__( 'Registration successful. Redirecting…', 'elementor-login-register' ),
				'redirect_url' => $redirect_url,
			]
		);
	}
}
