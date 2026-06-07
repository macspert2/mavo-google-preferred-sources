<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GPS_Render {

	/** Set to true once render() is called so the plugin knows to enqueue assets. */
	public static bool $rendered = false;

	/**
	 * Builds and returns the full button + tooltip HTML.
	 *
	 * @param array $args {
	 *   @type string $style   'badge' | 'text'   Default from settings.
	 *   @type string $variant 'full' | 'short'   Default 'full'.
	 *   @type string $context 'post' | 'widget' | 'shortcode'
	 * }
	 */
	public static function render( array $args = array() ): string {
		$settings = GPS_Settings::get();

		$style   = isset( $args['style'] ) && in_array( $args['style'], array( 'badge', 'text' ), true )
			? $args['style']
			: $settings['button_style'];
		$variant = isset( $args['variant'] ) && in_array( $args['variant'], array( 'full', 'short' ), true )
			? $args['variant']
			: 'full';
		$context = isset( $args['context'] ) && in_array( $args['context'], array( 'post', 'widget', 'shortcode' ), true )
			? $args['context']
			: 'post';

		if ( ! $settings['enable_tooltip'] ) {
			// Tooltip is disabled globally; render a plain link.
			return self::render_plain_link( $style, $variant, $context, $settings );
		}

		self::$rendered = true;

		$lang    = GPS_I18n::current_lang();
		$strings = GPS_I18n::get_strings();
		$domain  = self::resolve_domain( $lang, $settings );
		$uid     = wp_unique_id( 'gps-' );

		$deeplink  = esc_url( 'https://google.com/preferences/source?q=' . $domain );
		$label     = $variant === 'short' ? $strings['label_short'] : $strings['label_full'];
		$heading   = $strings['heading'];
		$tooltip   = $strings['tooltip'];

		$inner = self::build_inner( $style, $lang, $label );

		$html  = '<div class="gps-wrap gps-context-' . esc_attr( $context ) . '">';
		$html .= '<a class="gps-btn gps-style-' . esc_attr( $style ) . '"';
		$html .= ' href="' . $deeplink . '"';
		$html .= ' target="_blank" rel="noopener nofollow"';
		$html .= ' aria-describedby="' . esc_attr( $uid ) . '">';
		$html .= $inner;
		$html .= '</a>';
		$html .= '<button type="button" class="gps-info" aria-expanded="false"';
		$html .= ' aria-controls="' . esc_attr( $uid ) . '"';
		$html .= ' aria-label="' . esc_attr( $heading ) . '">&#9432;</button>';
		$html .= '<div id="' . esc_attr( $uid ) . '" class="gps-tip" role="tooltip" hidden>';
		$html .= '<strong>' . esc_html( $heading ) . '</strong>';
		$html .= '<p>' . esc_html( $tooltip ) . '</p>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	private static function render_plain_link( string $style, string $variant, string $context, array $settings ): string {
		self::$rendered = true;

		$lang     = GPS_I18n::current_lang();
		$strings  = GPS_I18n::get_strings();
		$domain   = self::resolve_domain( $lang, $settings );
		$deeplink = esc_url( 'https://google.com/preferences/source?q=' . $domain );
		$label    = $variant === 'short' ? $strings['label_short'] : $strings['label_full'];
		$inner    = self::build_inner( $style, $lang, $label );

		$html  = '<div class="gps-wrap gps-context-' . esc_attr( $context ) . '">';
		$html .= '<a class="gps-btn gps-style-' . esc_attr( $style ) . '"';
		$html .= ' href="' . $deeplink . '"';
		$html .= ' target="_blank" rel="noopener nofollow">';
		$html .= $inner;
		$html .= '</a>';
		$html .= '</div>';

		return $html;
	}

	private static function build_inner( string $style, string $lang, string $label ): string {
		if ( $style === 'badge' ) {
			$badge_url = GPS_I18n::badge_url( $lang );
			return '<img src="' . esc_url( $badge_url ) . '" alt="' . esc_attr( $label ) . '" class="gps-badge-img" />';
		}

		// Text style: pill button with star glyph.
		return '<span class="gps-star" aria-hidden="true">&#9733;</span>' . esc_html( $label );
	}

	/**
	 * Returns the source domain for the deeplink's q= parameter.
	 * Per-language domain overrides are used only when explicitly configured.
	 */
	private static function resolve_domain( string $lang, array $settings ): string {
		if ( ! empty( $settings[ "domain_{$lang}" ] ) ) {
			return sanitize_text_field( $settings[ "domain_{$lang}" ] );
		}
		return sanitize_text_field( $settings['source_domain'] );
	}
}
