<?php
/**
 * License section template.
 *
 * @since 3.0
 *
 * @param array {
 *     Section arguments.
 *
 *     @type string $id    Page section identifier.
 *     @type string $title Page section title.
 * }
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );
?>

<div class="wpr-sectionHeader">
	<h2 id="<?php echo esc_attr( $data['id'] ); ?>" class="wpr-title1 wpr-icon-important"><?php echo esc_html( $data['title'] ); ?></h2>
	<div class="wpr-sectionHeader-title wpr-title3">
		<?php _e( 'WP Rocket was not able to automatically validate your license.', 'rocket' ); ?>
	</div>
	<div class="wpr-sectionHeader-description">
        <a href="<?php echo admin_url( 'admin.php?page=zhk_guard_register_rocket' ); ?>" class="wpr-button">Activate</a>
		<?php
		// translators: %1$s = tutorial URL, %2$s = support URL.
		printf( __( 'Follow this <a href="%1$s" target="_blank">link</a> to get the license key.', 'rocket' ),
            'https://zhaket.com/dashboard/downloads/');
		?>
	</div>
</div><br>
