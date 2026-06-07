<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GPS_Plugin {

	private static ?GPS_Plugin $instance = null;

	public static function get_instance(): GPS_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'pll_init', array( 'GPS_I18n', 'register_pll_strings' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_filter( 'the_content', array( $this, 'append_to_content' ), 20 );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain(
			'google-preferred-sources',
			false,
			dirname( plugin_basename( GPS_PLUGIN_FILE ) ) . '/languages'
		);
	}

	public function init(): void {
		GPS_Shortcode::register();
	}

	public function register_widget(): void {
		register_widget( 'GPS_Widget' );
	}

	public function register_assets(): void {
		wp_register_style(
			'gps-style',
			GPS_PLUGIN_URL . 'assets/css/gps.css',
			array(),
			GPS_VERSION
		);
		wp_register_script(
			'gps-script',
			GPS_PLUGIN_URL . 'assets/js/gps.js',
			array(),
			GPS_VERSION,
			true
		);
	}

	public function append_to_content( string $content ): string {
		$settings = GPS_Settings::get();

		if ( ! $settings['show_below_posts'] ) {
			return $content;
		}

		$post_types = $settings['post_types'];
		if ( ! is_array( $post_types ) || empty( $post_types ) ) {
			return $content;
		}

		if ( ! is_singular( $post_types ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$position = $settings['position'] ?? 'after';
		$button   = GPS_Render::render( array( 'context' => 'post', 'variant' => 'full' ) );

		// Enqueue assets now that we know we'll render.
		wp_enqueue_style( 'gps-style' );
		wp_enqueue_script( 'gps-script' );

		if ( $position === 'before' ) {
			return $button . $content;
		}

		return $content . $button;
	}
}
