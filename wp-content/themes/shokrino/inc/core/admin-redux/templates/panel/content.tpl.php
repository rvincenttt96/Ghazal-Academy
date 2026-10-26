<?php
/**
 * The template for the main content of the panel.
 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
 *
 * @author      Redux Framework
 * @package     ReduxFramework/Templates
 * @version:    4.0.0
 */

?>
<!-- Header Block -->
<?php $this->get_template( 'header.tpl.php' ); ?>

<!-- Intro Text -->
<?php if ( isset( $this->parent->args['intro_text'] ) ) { ?>
	<div id="redux-intro-text"><?php echo wp_kses_post( $this->parent->args['intro_text'] ); ?></div>
<?php } ?>

<?php if (shkrn_protected_boolean(shkrn_get_zgboolean())) { ?>
	<?php $this->get_template( 'menu-container.tpl.php' ); ?>

	<div class="redux-main">
		<!-- Stickybar -->
		<?php $this->get_template( 'header-stickybar.tpl.php' ); ?>
		<div id="redux_ajax_overlay">&nbsp;</div>
		<?php foreach ( $this->parent->sections as $k => $section ) { ?>
			<?php if ( isset( $section['customizer_only'] ) && true === $section['customizer_only'] ) { ?>
				<?php continue; ?>
			<?php } // phpcs:ignore Squiz.PHP.NonExecutableCode.Unreachable ?>

			<?php $section['class'] = isset( $section['class'] ) ? ' ' . $section['class'] : ''; ?>

			<?php $disabled = ''; ?>
			<?php if ( isset( $section['disabled'] ) && $section['disabled'] ) { ?>
				<?php $disabled = 'disabled '; ?>
			<?php } ?>

			<div
				id="<?php echo esc_attr( $k ); ?>_section_group"
				class="redux-group-tab <?php echo esc_attr( $disabled ); ?><?php echo esc_attr( $section['class'] ); ?>"
				data-rel="<?php echo esc_attr( $k ); ?>">

				<?php $display = true; ?>

				<?php if ( isset( $_GET['page'] ) && $this->parent->args['page_slug'] === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification ?>
					<?php if ( isset( $section['panel'] ) && false === $section['panel'] ) { ?>
						<?php $display = false; ?>
					<?php } ?>
				<?php } ?>

				<?php
				if ( $display ) {
					/**
					 * Action 'redux/page/{opt_name}/section/before'
					 *
					 * @param object $this ReduxFramework
					 */

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( "redux/page/{$this->parent->args['opt_name']}/section/before", $section );

					$this->output_section( $k );

					/**
					 * Action 'redux/page/{opt_name}/section/after'
					 *
					 * @param object $this ReduxFramework
					 */

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( "redux/page/{$this->parent->args['opt_name']}/section/after", $section );
				}
				?>
			</div> <!-- section group -->
		<?php } ?>

		<?php
		/**
		 * Action 'redux/page/{opt_name}/sections/after'
		 *
		 * @param object $this ReduxFramework
		 */

		// phpcs:ignore WordPress.NamingConventions.ValidHookName
		do_action( "redux/page/{$this->parent->args['opt_name']}/sections/after", $this );
		?>
		<div class="clear"></div>
		<!-- Footer Block -->
		<?php $this->get_template( 'footer.tpl.php' ); ?>
		<div id="redux-sticky-padder" style="display: none;">&nbsp;</div>
	</div> <!-- redux main -->
<?php } else { ?>
	<div class="diabled-menu-redux-main">
		<p><?php esc_html_e('Please activate your license (otherwise you will not be able to access this section)', 'shokrino' ); ?></p>
		<a href="<?php echo get_site_url().'/wp-admin/admin.php?page=add_shokrino_zg' ?>" ><?php esc_html_e('register license', 'shokrino' ); ?></a>
	</div>
	<style>
        .diabled-menu-redux-main {
            background: #fff !important;
            border: 4px solid #0096ff !important;
            border-radius: 25px !important;
            margin: 10px auto 35px;
            padding: 20px 50px;
            width: 90%;
        }
        .diabled-menu-redux-main > p {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            background: white;
            padding: 17px;
            border-radius: 20px;
            text-align: center;
        }
        .diabled-menu-redux-main > a {
            padding: 12px 24px 14px !important;
            margin: 10px auto !important;
            display: flex;
            width: fit-content;
            background-image: linear-gradient(90deg,#6bd43b,#75ea4a) !important;
            box-shadow: 0 4px 18px #8bff004f !important;
            text-decoration: none;
            color: white;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
        }
        .diabled-menu-redux-main > a:hover {
            background-color: white !important;
            background-image: none !important;
            box-shadow: 0 4px 18px #8bff004f !important;
            color: #29ca4a !important;
            border: none !important;
        }
	</style>
<?php } ?>


<div class="clear"></div>
