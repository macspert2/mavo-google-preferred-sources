<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GPS_Shortcode {

	public static function register(): void {
		add_shortcode( 'google_preferred_source', array( __CLASS__, 'render' ) );
	}

	public static function render( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'style'   => GPS_Settings::get()['button_style'],
				'variant' => 'full',
			),
			$atts,
			'google_preferred_source'
		);

		wp_enqueue_style( 'gps-style' );
		wp_enqueue_script( 'gps-script' );

		return GPS_Render::render(
			array(
				'style'   => $atts['style'],
				'variant' => $atts['variant'],
				'context' => 'shortcode',
			)
		);
	}
}
