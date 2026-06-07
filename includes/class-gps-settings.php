<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GPS_Settings {

	private const OPTION_KEY = 'gps_settings';

	/** Returns merged settings (stored + defaults). */
	public static function get(): array {
		$stored   = get_option( self::OPTION_KEY, array() );
		$defaults = self::defaults();
		return wp_parse_args( $stored, $defaults );
	}

	public static function defaults(): array {
		$host   = wp_parse_url( home_url(), PHP_URL_HOST ) ?? '';
		$domain = preg_replace( '/^www\./i', '', $host );

		return array(
			'source_domain'    => $domain,
			'domain_fr'        => '',
			'domain_en'        => '',
			'domain_de'        => '',
			'show_below_posts' => true,
			'post_types'       => array( 'post' ),
			'position'         => 'after',
			'button_style'     => 'badge',
			'enable_tooltip'   => true,
		);
	}

	/** Called on plugin activation to seed the option with defaults. */
	public static function activate(): void {
		if ( ! get_option( self::OPTION_KEY ) ) {
			update_option( self::OPTION_KEY, self::defaults() );
		}
	}

	public static function register_hooks(): void {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	public static function add_menu(): void {
		add_options_page(
			__( 'Preferred Sources', 'google-preferred-sources' ),
			__( 'Preferred Sources', 'google-preferred-sources' ),
			'manage_options',
			'gps-settings',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function register_settings(): void {
		register_setting(
			'gps_settings_group',
			self::OPTION_KEY,
			array(
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
			)
		);

		// --- Section: Source domain ---
		add_settings_section(
			'gps_section_domain',
			__( 'Source Domain', 'google-preferred-sources' ),
			array( __CLASS__, 'render_section_domain' ),
			'gps-settings'
		);

		add_settings_field(
			'source_domain',
			__( 'Primary source domain', 'google-preferred-sources' ),
			array( __CLASS__, 'render_field_source_domain' ),
			'gps-settings',
			'gps_section_domain'
		);

		foreach ( array( 'fr', 'en', 'de' ) as $lang ) {
			add_settings_field(
				"domain_{$lang}",
				/* translators: %s: language code */
				sprintf( __( 'Override domain for %s', 'google-preferred-sources' ), strtoupper( $lang ) ),
				array( __CLASS__, 'render_field_lang_domain' ),
				'gps-settings',
				'gps_section_domain',
				array( 'lang' => $lang )
			);
		}

		// --- Section: Display ---
		add_settings_section(
			'gps_section_display',
			__( 'Display', 'google-preferred-sources' ),
			'__return_false',
			'gps-settings'
		);

		add_settings_field(
			'show_below_posts',
			__( 'Show below posts', 'google-preferred-sources' ),
			array( __CLASS__, 'render_field_show_below_posts' ),
			'gps-settings',
			'gps_section_display'
		);

		add_settings_field(
			'post_types',
			__( 'Post types', 'google-preferred-sources' ),
			array( __CLASS__, 'render_field_post_types' ),
			'gps-settings',
			'gps_section_display'
		);

		add_settings_field(
			'position',
			__( 'Position', 'google-preferred-sources' ),
			array( __CLASS__, 'render_field_position' ),
			'gps-settings',
			'gps_section_display'
		);

		add_settings_field(
			'button_style',
			__( 'Button style', 'google-preferred-sources' ),
			array( __CLASS__, 'render_field_button_style' ),
			'gps-settings',
			'gps_section_display'
		);

		add_settings_field(
			'enable_tooltip',
			__( 'Enable tooltip', 'google-preferred-sources' ),
			array( __CLASS__, 'render_field_enable_tooltip' ),
			'gps-settings',
			'gps_section_display'
		);
	}

	public static function sanitize( mixed $input ): array {
		$defaults = self::defaults();

		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		$clean = array();

		$clean['source_domain'] = sanitize_text_field( $input['source_domain'] ?? $defaults['source_domain'] );

		foreach ( array( 'fr', 'en', 'de' ) as $lang ) {
			$clean[ "domain_{$lang}" ] = sanitize_text_field( $input[ "domain_{$lang}" ] ?? '' );
		}

		$clean['show_below_posts'] = ! empty( $input['show_below_posts'] );
		$clean['enable_tooltip']   = ! empty( $input['enable_tooltip'] );

		$allowed_types = get_post_types( array( 'public' => true ) );
		$raw_types     = isset( $input['post_types'] ) && is_array( $input['post_types'] )
			? $input['post_types']
			: array( 'post' );
		$clean['post_types'] = array_values( array_filter( $raw_types, fn( $t ) => array_key_exists( $t, $allowed_types ) ) );
		if ( empty( $clean['post_types'] ) ) {
			$clean['post_types'] = array( 'post' );
		}

		$clean['position'] = in_array( $input['position'] ?? '', array( 'before', 'after' ), true )
			? $input['position']
			: 'after';

		$clean['button_style'] = in_array( $input['button_style'] ?? '', array( 'badge', 'text' ), true )
			? $input['button_style']
			: 'badge';

		return $clean;
	}

	// ── Field renderers ──────────────────────────────────────────────────────

	public static function render_section_domain(): void {
		echo '<p>' . wp_kses_post(
			sprintf(
				/* translators: URL */
				__( 'Google only supports domain-level sources — <strong>subdirectories are not eligible</strong>. Use the bare registered domain (e.g. <code>example.com</code>). <a href="https://www.google.com/preferences/source" target="_blank" rel="noopener">Verify your domain is listed on Google ↗</a>', 'google-preferred-sources' )
			)
		) . '</p>';
	}

	public static function render_field_source_domain(): void {
		$settings = self::get();
		?>
		<input type="text" name="gps_settings[source_domain]"
			value="<?php echo esc_attr( $settings['source_domain'] ); ?>"
			class="regular-text" />
		<p class="description"><?php esc_html_e( 'Used for all languages unless a per-language override is set below.', 'google-preferred-sources' ); ?></p>
		<?php
	}

	/** @param array{lang:string} $args */
	public static function render_field_lang_domain( array $args ): void {
		$lang     = $args['lang'];
		$settings = self::get();
		?>
		<input type="text" name="gps_settings[domain_<?php echo esc_attr( $lang ); ?>]"
			value="<?php echo esc_attr( $settings[ "domain_{$lang}" ] ?? '' ); ?>"
			class="regular-text" placeholder="<?php esc_attr_e( 'Leave empty to use primary domain', 'google-preferred-sources' ); ?>" />
		<p class="description"><?php esc_html_e( 'Fill only if this language runs on its own domain or subdomain.', 'google-preferred-sources' ); ?></p>
		<?php
	}

	public static function render_field_show_below_posts(): void {
		$settings = self::get();
		?>
		<label>
			<input type="checkbox" name="gps_settings[show_below_posts]" value="1"
				<?php checked( $settings['show_below_posts'] ); ?> />
			<?php esc_html_e( 'Automatically append button below single posts', 'google-preferred-sources' ); ?>
		</label>
		<?php
	}

	public static function render_field_post_types(): void {
		$settings     = self::get();
		$public_types = get_post_types( array( 'public' => true ), 'objects' );
		$selected     = $settings['post_types'];

		foreach ( $public_types as $pt ) {
			?>
			<label style="display:block;margin-bottom:4px">
				<input type="checkbox" name="gps_settings[post_types][]"
					value="<?php echo esc_attr( $pt->name ); ?>"
					<?php checked( in_array( $pt->name, $selected, true ) ); ?> />
				<?php echo esc_html( $pt->labels->singular_name ); ?> (<code><?php echo esc_html( $pt->name ); ?></code>)
			</label>
			<?php
		}
	}

	public static function render_field_position(): void {
		$settings = self::get();
		?>
		<select name="gps_settings[position]">
			<option value="after" <?php selected( $settings['position'], 'after' ); ?>><?php esc_html_e( 'After content', 'google-preferred-sources' ); ?></option>
			<option value="before" <?php selected( $settings['position'], 'before' ); ?>><?php esc_html_e( 'Before content', 'google-preferred-sources' ); ?></option>
		</select>
		<?php
	}

	public static function render_field_button_style(): void {
		$settings = self::get();
		?>
		<select name="gps_settings[button_style]">
			<option value="badge" <?php selected( $settings['button_style'], 'badge' ); ?>><?php esc_html_e( 'Official badge image', 'google-preferred-sources' ); ?></option>
			<option value="text" <?php selected( $settings['button_style'], 'text' ); ?>><?php esc_html_e( 'Text/CSS pill button', 'google-preferred-sources' ); ?></option>
		</select>
		<?php
	}

	public static function render_field_enable_tooltip(): void {
		$settings = self::get();
		?>
		<label>
			<input type="checkbox" name="gps_settings[enable_tooltip]" value="1"
				<?php checked( $settings['enable_tooltip'] ); ?> />
			<?php esc_html_e( 'Show the explanatory tooltip (ⓘ button)', 'google-preferred-sources' ); ?>
		</label>
		<?php
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Google Preferred Sources — Settings', 'google-preferred-sources' ); ?></h1>

			<div class="notice notice-info" style="padding:12px 16px">
				<p>
					<?php
					echo wp_kses_post(
						__( '<strong>Important:</strong> Your site must be listed in Google\'s source-preferences tool before this button has any effect. <a href="https://www.google.com/preferences/source" target="_blank" rel="noopener">Check if your domain appears there ↗</a>', 'google-preferred-sources' )
					);
					?>
				</p>
			</div>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'gps_settings_group' );
				do_settings_sections( 'gps-settings' );
				submit_button();
				?>
			</form>

			<hr />
			<h2><?php esc_html_e( 'Shortcode', 'google-preferred-sources' ); ?></h2>
			<p><?php esc_html_e( 'Use the shortcode below to place the button manually anywhere in your content:', 'google-preferred-sources' ); ?></p>
			<code>[google_preferred_source]</code> &mdash;
			<code>[google_preferred_source style="text" variant="short"]</code>
		</div>
		<?php
	}
}

// Hook the admin page registration.
add_action( 'admin_menu', array( 'GPS_Settings', 'add_menu' ) );
add_action( 'admin_init', array( 'GPS_Settings', 'register_settings' ) );
