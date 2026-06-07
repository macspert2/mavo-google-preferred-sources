<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GPS_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'gps_widget',
			__( 'Google Preferred Sources', 'google-preferred-sources' ),
			array(
				'description' => __( 'Displays a Google Preferred Sources button to encourage readers to follow your site.', 'google-preferred-sources' ),
			)
		);
	}

	/** @param array<string,mixed> $args @param array<string,mixed> $instance */
	public function widget( $args, $instance ): void {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo wp_kses_post( $args['before_widget'] );

		if ( $title ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		wp_enqueue_style( 'gps-style' );
		wp_enqueue_script( 'gps-script' );

		echo GPS_Render::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'context' => 'widget',
				'variant' => 'short',
			)
		);

		echo wp_kses_post( $args['after_widget'] );
	}

	/** @param array<string,mixed> $instance */
	public function form( $instance ): void {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'google-preferred-sources' ); ?>
			</label>
			<input class="widefat" type="text"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $title ); ?>" />
			<small><?php esc_html_e( 'Leave empty to show no title.', 'google-preferred-sources' ); ?></small>
		</p>
		<?php
	}

	/**
	 * @param array<string,mixed> $new_instance
	 * @param array<string,mixed> $old_instance
	 * @return array<string,mixed>
	 */
	public function update( $new_instance, $old_instance ): array {
		return array(
			'title' => sanitize_text_field( $new_instance['title'] ?? '' ),
		);
	}
}
