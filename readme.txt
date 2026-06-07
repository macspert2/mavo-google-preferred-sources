=== Google Preferred Sources Button ===
Contributors: mavo
Tags: google, preferred sources, news, widget, polylang
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Displays a Google Preferred Sources call-to-action button below posts and as a footer widget, with a detailed explanatory tooltip. Fully localized for French, English, and German (Polylang).

== Description ==

**Google Preferred Sources** is a Google Search feature that lets signed-in users star publishers they trust. That publisher's articles then appear more prominently in the user's "Top Stories" and in a dedicated "From your sources" area for relevant queries.

This plugin adds a prominently placed, fully accessible call-to-action button that invites your readers to add your site as a preferred source directly from your pages — no account needed to reach the deeplink.

**Features**

* Button appears automatically below single posts (configurable post types).
* Footer widget for persistent placement.
* `[google_preferred_source]` shortcode for manual placement.
* Detailed explanatory tooltip (hover, keyboard focus, and tap/click — fully accessible).
* Official Google badge images bundled locally (no hotlinking).
* Full Polylang localization: French (default), English, German.
* Admin-overridable copy per language via Polylang Strings Translations.
* Simple settings page (Settings → Preferred Sources).
* CSS/JS loaded only on pages where the button renders.
* Clean uninstall (removes the option on plugin deletion).

**Important note on eligibility**

Google only supports domain-level and subdomain-level preferred sources — subdirectories are **not** eligible. If your Polylang site uses language subdirectories (`/en/`, `/de/`), the plugin correctly uses your bare domain (`example.com`) for all languages. If languages run on separate domains or subdomains, you can configure per-language domain overrides in the settings.

You should verify your site is listed at https://www.google.com/preferences/source before publishing this button.

== Installation ==

1. Upload the `google-preferred-sources` folder to `/wp-content/plugins/`.
2. Activate the plugin via **Plugins → Installed Plugins**.
3. Go to **Settings → Preferred Sources** and confirm your source domain.
4. Verify your domain at https://www.google.com/preferences/source.
5. Optionally add the **Google Preferred Sources** widget to a footer widget area.

== Frequently Asked Questions ==

= What is a "Preferred Source"? =
It's a Google Search feature. A signed-in user can star publishers they trust. That publisher's articles appear more prominently in the user's "Top Stories" and in a dedicated "From your sources" area for relevant news queries. It personalizes *that user's* results — it does not change global rankings.

= Why is the deeplink always the same regardless of language? =
Google's source-preferences tool is domain-level. Whether a reader visits the French, English, or German version of your site, they all add the same domain. The language only changes the button label, tooltip text, and badge image.

= Does this work without Polylang? =
Yes. The plugin falls back gracefully to `get_locale()` and then to French if Polylang is not active.

= Can I customize the button copy? =
Yes. With Polylang active, go to **Languages → Strings translations** and find the "Google Preferred Sources" group. Without Polylang, you can override the strings via standard `.po`/`.mo` translation files.

= The button only shows the badge image — can I use a text button instead? =
Yes. Go to **Settings → Preferred Sources** and change **Button style** to "Text/CSS pill button".

= Does clicking the button add us as a source immediately? =
No. The button links to Google's source-preferences page, where the reader confirms their choice while signed in to their Google account.

== Screenshots ==

1. Button below a post (badge style).
2. Footer widget.
3. Tooltip open (explaining the feature).
4. Admin settings page.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
