<?php
defined( 'ABSPATH' ) || exit;
get_header();
do_action('before_404_load_shkrn'); ?>
<div class="container">
    <div class="bg-404 flex-center">
        <section class="error-404">
            <div class="row-404">
                <h2><?php esc_html_e('We did not find what you are looking for!', 'shokrino' ); ?></h2>
                <h5><?php esc_html_e('The page you are looking for has either been deleted or does not exist at all or there is a problem!', 'shokrino' ); ?></h5>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="more-button-section-shkrn flex-center"><?php esc_html_e('Back to home', 'shokrino' ); ?></a>
            </div>
            <div class="error404-text">
                <h2><?php esc_html_e('404', 'shokrino' ); ?></h2>
            </div>
        </section>
    </div>
</div>
<?php do_action('after_404_load_shkrn');
get_footer();

