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
			'tooltip'     => __( 'Preferred Sources is a Google Search feature that lets you choose the publishers you want to see more of. When you add us as a preferred source, our articles are more likely to appear for you in the "Top Stories" section — and in a dedicated "From your sources" area — when we publish timely content related to what you\'re searching for. Clicking opens Google\'s source-preferences page, where you confirm your choice while signed in to your Google account. It doesn\'t change rankings for everyone; it personalizes your own results, and you can add or remove sources at any time.', $domain ),
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
				'fr' => '« Sources préférées » est une fonctionnalité de Google Search qui vous permet de choisir les éditeurs que vous souhaitez voir plus souvent. Lorsque vous nous ajoutez comme source préférée, nos articles ont plus de chances d\'apparaître pour vous dans la section « À la une », ainsi que dans un espace dédié « À partir de vos sources », lorsque nous publions un contenu d\'actualité lié à votre recherche. En cliquant, vous ouvrez la page des préférences de sources de Google où vous confirmez votre choix une fois connecté à votre compte Google. Cela ne modifie pas le classement pour tout le monde : cela personnalise vos propres résultats, et vous pouvez ajouter ou retirer des sources à tout moment.',
				'en' => 'Preferred Sources is a Google Search feature that lets you choose the publishers you want to see more of. When you add us as a preferred source, our articles are more likely to appear for you in the "Top Stories" section — and in a dedicated "From your sources" area — when we publish timely content related to what you\'re searching for. Clicking opens Google\'s source-preferences page, where you confirm your choice while signed in to your Google account. It doesn\'t change rankings for everyone; it personalizes your own results, and you can add or remove sources at any time.',
				'de' => '„Bevorzugte Quellen" ist eine Funktion der Google Suche, mit der Sie die Publisher auswählen können, die Sie häufiger sehen möchten. Wenn Sie uns als bevorzugte Quelle hinzufügen, erscheinen unsere Artikel mit höherer Wahrscheinlichkeit im Bereich „Top-Meldungen" sowie in einem eigenen Bereich „Aus Ihren Quellen", wenn wir aktuelle Inhalte zu Ihrem Suchbegriff veröffentlichen. Beim Klick öffnet sich die Google-Seite für Quelleneinstellungen, auf der Sie Ihre Auswahl bei angemeldetem Google-Konto bestätigen. Das ändert nicht das Ranking für alle Nutzer, sondern personalisiert Ihre eigenen Ergebnisse — Sie können Quellen jederzeit hinzufügen oder entfernen.',
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
