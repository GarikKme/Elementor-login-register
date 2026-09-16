<?php

namespace ElementorLoginRegister\Widgets;

use ElementorLoginRegister\Widgets\Traits\Login_Fields_Trait;
use ElementorLoginRegister\Widgets\Traits\Registration_Fields_Trait;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Login_Register_Tabs extends Widget_Base {

	use Login_Fields_Trait;
	use Registration_Fields_Trait;

	private const LOGIN_ID_PREFIX    = 'login_';
	private const REGISTER_ID_PREFIX = 'register_';

	public function get_name(): string {
		return 'login_register_tabs';
	}

	public function get_title(): string {
		return esc_html__( 'Login + Registration (Tabs)', 'elementor-login-register' );
	}

	public function get_icon(): string {
		return 'eicon-tabs';
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
			'section_tabs_settings',
			[
				'label' => esc_html__( 'Tabs Settings', 'elementor-login-register' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'default_active_tab',
			[
				'label'   => esc_html__( 'Default Active Tab', 'elementor-login-register' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'login',
				'options' => [
					'login'    => esc_html__( 'Login', 'elementor-login-register' ),
					'register' => esc_html__( 'Registration', 'elementor-login-register' ),
				],
			]
		);

		$this->add_control(
			'tab_label_login',
			[
				'label'   => esc_html__( 'Login Tab Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Log In', 'elementor-login-register' ),
			]
		);

		$this->add_control(
			'tab_label_register',
			[
				'label'   => esc_html__( 'Registration Tab Label', 'elementor-login-register' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Register', 'elementor-login-register' ),
			]
		);

		$this->end_controls_section();

		$this->register_login_content_controls(
			self::LOGIN_ID_PREFIX,
			esc_html__( 'Login', 'elementor-login-register' ) . ': '
		);

		$this->register_registration_content_controls(
			self::REGISTER_ID_PREFIX,
			esc_html__( 'Register', 'elementor-login-register' ) . ': '
		);

		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => esc_html__( 'Tabs Nav Style', 'elementor-login-register' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tabs_nav_typography',
				'label'    => esc_html__( 'Typography', 'elementor-login-register' ),
				'selector' => '{{WRAPPER}} .llr-tabs__tab',
			]
		);

		$this->add_control(
			'tabs_inactive_color',
			[
				'label'     => esc_html__( 'Inactive Tab Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b6b6b',
				'selectors' => [
					'{{WRAPPER}} .llr-tabs__tab' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tabs_active_color',
			[
				'label'     => esc_html__( 'Active Tab Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e1e1e',
				'selectors' => [
					'{{WRAPPER}} .llr-tabs__tab--active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tabs_active_bg_color',
			[
				'label'     => esc_html__( 'Active Tab Background Color', 'elementor-login-register' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .llr-tabs__tab--active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->register_login_style_controls(
			self::LOGIN_ID_PREFIX,
			esc_html__( 'Login', 'elementor-login-register' ) . ': '
		);

		$this->register_registration_style_controls(
			self::REGISTER_ID_PREFIX,
			esc_html__( 'Register', 'elementor-login-register' ) . ': '
		);
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$default_tab = $settings['default_active_tab'] ?? 'login';

		if ( ! in_array( $default_tab, [ 'login', 'register' ], true ) ) {
			$default_tab = 'login';
		}

		$tab_label_login    = $settings['tab_label_login'] ?? '';
		$tab_label_register = $settings['tab_label_register'] ?? '';

		$tabs_id = 'llr-tabs-' . $this->get_id();
		?>
		<div class="llr-tabs" data-active="<?php echo esc_attr( $default_tab ); ?>">
			<div class="llr-tabs__nav" role="tablist">
				<button
					type="button"
					class="llr-tabs__tab<?php echo 'login' === $default_tab ? ' llr-tabs__tab--active' : ''; ?>"
					data-tab="login"
					id="<?php echo esc_attr( $tabs_id ); ?>-tab-login"
					role="tab"
					aria-controls="<?php echo esc_attr( $tabs_id ); ?>-panel-login"
					aria-selected="<?php echo 'login' === $default_tab ? 'true' : 'false'; ?>"
				>
					<?php echo esc_html( $tab_label_login ); ?>
				</button>
				<button
					type="button"
					class="llr-tabs__tab<?php echo 'register' === $default_tab ? ' llr-tabs__tab--active' : ''; ?>"
					data-tab="register"
					id="<?php echo esc_attr( $tabs_id ); ?>-tab-register"
					role="tab"
					aria-controls="<?php echo esc_attr( $tabs_id ); ?>-panel-register"
					aria-selected="<?php echo 'register' === $default_tab ? 'true' : 'false'; ?>"
				>
					<?php echo esc_html( $tab_label_register ); ?>
				</button>
			</div>

			<div
				class="llr-tabs__panel<?php echo 'login' === $default_tab ? ' llr-tabs__panel--active' : ''; ?>"
				data-tab-panel="login"
				id="<?php echo esc_attr( $tabs_id ); ?>-panel-login"
				role="tabpanel"
				aria-labelledby="<?php echo esc_attr( $tabs_id ); ?>-tab-login"
				<?php echo 'login' === $default_tab ? '' : 'hidden'; ?>
			>
				<?php $this->render_login_form_markup( $settings, self::LOGIN_ID_PREFIX, 'llr-login-tabs' ); ?>
			</div>

			<div
				class="llr-tabs__panel<?php echo 'register' === $default_tab ? ' llr-tabs__panel--active' : ''; ?>"
				data-tab-panel="register"
				id="<?php echo esc_attr( $tabs_id ); ?>-panel-register"
				role="tabpanel"
				aria-labelledby="<?php echo esc_attr( $tabs_id ); ?>-tab-register"
				<?php echo 'register' === $default_tab ? '' : 'hidden'; ?>
			>
				<?php $this->render_registration_form_markup( $settings, self::REGISTER_ID_PREFIX, 'llr-register-tabs' ); ?>
			</div>
		</div>
		<?php
	}
}
