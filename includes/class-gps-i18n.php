<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GPS_I18n {

	/**
	 * Returns the two-letter language slug for the current request.
	 * Priority: Polylang > WP locale > site default 'fr'.
	 */
	public static function current_lang(): string {
		if ( function_exists( 'pll_current_language' ) ) {
			$lang = pll_current_language( 'slug' );
			if ( $lang ) {
				return $lang;
			}
		}

		$loc = get_locale();
		if ( str_starts_with( $loc, 'fr' ) ) {
			return 'fr';
		}
		if ( str_starts_with( $loc, 'de' ) ) {
			return 'de';
		}
		if ( str_starts_with( $loc, 'en' ) ) {
			return 'en';
		}

		return 'fr';
	}

	/**
	 * Returns the URL of the badge image for the given language slug.
	 * Falls back to 'en' if an unknown slug is given.
	 */
	public static function badge_url( string $lang ): string {
		$allowed = array( 'fr', 'en', 'de' );
		if ( ! in_array( $lang, $allowed, true ) ) {
			$lang = 'en';
		}

		$file = GPS_PLUGIN_DIR . "assets/img/badge-{$lang}.svg";
		if ( ! file_exists( $file ) ) {
			// Try PNG fallback.
			$file_png = GPS_PLUGIN_DIR . "assets/img/badge-{$lang}.png";
			if ( file_exists( $file_png ) ) {
				return GPS_PLUGIN_URL . "assets/img/badge-{$lang}.png";
			}
			// Last resort: English.
			$fallback = GPS_PLUGIN_DIR . 'assets/img/badge-en.svg';
			if ( file_exists( $fallback ) ) {
				return GPS_PLUGIN_URL . 'assets/img/badge-en.svg';
			}
			return GPS_PLUGIN_URL . 'assets/img/badge-en.png';
		}

		return GPS_PLUGIN_URL . "assets/img/badge-{$lang}.svg";
	}

	/**
	 * Registers Polylang strings and returns the full string map for the current language.
	 * Falls back to WP i18n defaults when Polylang is absent.
	 */
	public static function get_strings(): array {
		$domain = 'google-preferred-sources';

		$defaults = array(
			'label_full'  => __( 'Add us as a preferred source on Google', $domain ),
			'label_short' => __( 'Prefer us on Google', $domain ),
			'tooltip'     => __( 'You read this blog — which means you value quality content grounded in real experiences. With Google Preferred Sources, tell Google which publishers you trust. Add us and other sources you love, and Google will surface them more in your future search results.', $domain ),
			'heading'     => __( 'What is this?', $domain ),
		);

		if ( ! function_exists( 'pll__' ) ) {
			return $defaults;
		}

		return array(
			'label_full'  => pll__( $defaults['label_full'] ) ?: $defaults['label_full'],
			'label_short' => pll__( $defaults['label_short'] ) ?: $defaults['label_short'],
			'tooltip'     => pll__( $defaults['tooltip'] ) ?: $defaults['tooltip'],
			'heading'     => pll__( $defaults['heading'] ) ?: $defaults['heading'],
		);
	}

	/**
	 * Registers default strings with Polylang on 'pll_init'.
	 * Must be called before pll__() is first used.
	 */
	public static function register_pll_strings(): void {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		$domain  = 'google-preferred-sources';
		$group   = 'Google Preferred Sources';

		$strings = array(
			// fr defaults — Polylang will show these in the Strings Translations screen.
			'label_full'  => array(
				'fr' => 'Ajoutez-nous comme source préférée sur Google',
				'en' => 'Add us as a preferred source on Google',
				'de' => 'Als bevorzugte Quelle bei Google hinzufügen',
			),
			'label_short' => array(
				'fr' => 'Suivez-nous en priorité sur Google',
				'en' => 'Prefer us on Google',
				'de' => 'Bei Google bevorzugen',
			),
			'heading'     => array(
				'fr' => 'Qu\'est-ce que c\'est ?',
				'en' => 'What is this?',
				'de' => 'Was ist das?',
			),
			'tooltip'     => array(
				'fr' => 'Vous lisez ce blog, preuve que vous aimez les contenus de qualité fondés sur de vraies expériences. Avec Sources préférées, indiquez à Google les éditeurs en qui vous avez confiance. Ajoutez-nous ainsi que d\'autres sources que vous appréciez : Google les mettra davantage en avant dans vos futurs résultats de recherche.',
				'en' => 'You read this blog — which means you value quality content grounded in real experiences. With Google Preferred Sources, tell Google which publishers you trust. Add us and other sources you love, and Google will surface them more in your future search results.',
				'de' => 'Sie lesen diesen Blog — das zeigt Ihren Sinn für hochwertige Inhalte aus echten Erfahrungen. Mit „Bevorzugte Quellen" teilen Sie Google mit, welchen Quellen Sie vertrauen. Fügen Sie uns und andere Qualitätsquellen hinzu, und Google zeigt sie Ihnen künftig prominenter in den Suchergebnissen.',
			),
		);

		// Register with the site default (fr) value; Polylang lets translators fill others.
		$lang = self::current_lang();
		foreach ( $strings as $key => $translations ) {
			$default_value = $translations[ $lang ] ?? ( $translations['fr'] ?? reset( $translations ) );
			pll_register_string( $key, $default_value, $group, $key === 'tooltip' );
		}
	}
}
