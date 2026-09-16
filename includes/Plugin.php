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
		// Widget registration will be hooked into 'elementor/widgets/register' here.
	}
}
