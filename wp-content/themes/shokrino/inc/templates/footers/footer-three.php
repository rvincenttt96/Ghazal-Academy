<?php
defined( 'ABSPATH' ) || exit;
$show_social = shkrn_options('show_social');
$show_footer_menu = shkrn_options('show_footer_menu');
$footer_copyright = shkrn_options('footer_copyright');
?>
<footer class="footer-shkrn footer-fullwidth-background footer-three-bottom-padding-shkrn">
    <div class="header-footer-container-shkrn footer-three-shkrn">
        <?php if ( is_active_sidebar( 'shokrino_footer_one' ) or is_active_sidebar( 'shokrino_footer_two' ) or is_active_sidebar( 'shokrino_footer_three' ) ) { ?>
        <div class="footer-wrapper">
            <div class="footer-widgets-container">
                <div class="footer-widget-item">
                    <?php if ( is_active_sidebar( 'shokrino_footer_one' ) ) {
                        dynamic_sidebar('shokrino_footer_one');
                    }
                    ?>
                </div>
                <div class="footer-widget-item">
                    <?php if ( is_active_sidebar( 'shokrino_footer_two' ) ) {
                        dynamic_sidebar('shokrino_footer_two');
                    }
                    ?>
                </div>
                <div class="footer-widget-item">
                    <div class="namadha">
                        <?php if ( is_active_sidebar( 'shokrino_footer_three' ) ) {
                            dynamic_sidebar('shokrino_footer_three');
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="copyright-footer-three-shkrn flex-center">
            <div class="copyright-text">
                <?php if (!empty($footer_copyright)) { ?>
                    <p class="flex-center"><svg xmlns="http://www.w3.org/2000/svg" height="17px" fill="var(--shkrn-var-color-light-dark-text)" style="margin-bottom: 1px;" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm117.13 346.75c-1.59 1.87-39.77 45.73-109.85 45.73-84.69 0-144.48-63.26-144.48-145.56 0-81.31 62-143.4 143.76-143.4 67 0 102 37.31 103.42 38.9a12 12 0 0 1 1.24 14.58l-22.38 34.7a12 12 0 0 1-16.59 3.57 11.79 11.79 0 0 1-1.64-1.27c-.24-.21-26.53-23.88-61.88-23.88-46.12 0-73.92 33.58-73.92 76.08 0 39.61 25.52 79.7 74.28 79.7 38.7 0 65.28-28.34 65.54-28.63a12 12 0 0 1 16.95-.73h.05a12.2 12.2 0 0 1 1.55 1.74l24.55 33.58a12 12 0 0 1-.6 14.85z" class="fa-secondary"/><path d="M373.13 354.75c-1.59 1.87-39.77 45.73-109.85 45.73-84.69 0-144.48-63.26-144.48-145.56 0-81.31 62-143.4 143.76-143.4 67 0 102 37.31 103.42 38.9a12 12 0 0 1 1.24 14.58l-22.38 34.7a12 12 0 0 1-16.59 3.57 11.79 11.79 0 0 1-1.64-1.27c-.24-.21-26.53-23.88-61.88-23.88-46.12 0-73.92 33.58-73.92 76.08 0 39.61 25.52 79.7 74.28 79.7 38.7 0 65.28-28.34 65.54-28.63a12 12 0 0 1 16.95-.73h.05a12.2 12.2 0 0 1 1.55 1.74l24.55 33.58a12 12 0 0 1-.6 14.85z" class="fa-primary"/></svg> <?php echo $footer_copyright ?></p>
                <?php } else { ?>
                    <p class="flex-center"><svg xmlns="http://www.w3.org/2000/svg" height="17px" fill="var(--shkrn-var-color-light-dark-text)" style="margin-bottom: 1px;" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm117.13 346.75c-1.59 1.87-39.77 45.73-109.85 45.73-84.69 0-144.48-63.26-144.48-145.56 0-81.31 62-143.4 143.76-143.4 67 0 102 37.31 103.42 38.9a12 12 0 0 1 1.24 14.58l-22.38 34.7a12 12 0 0 1-16.59 3.57 11.79 11.79 0 0 1-1.64-1.27c-.24-.21-26.53-23.88-61.88-23.88-46.12 0-73.92 33.58-73.92 76.08 0 39.61 25.52 79.7 74.28 79.7 38.7 0 65.28-28.34 65.54-28.63a12 12 0 0 1 16.95-.73h.05a12.2 12.2 0 0 1 1.55 1.74l24.55 33.58a12 12 0 0 1-.6 14.85z" class="fa-secondary"/><path d="M373.13 354.75c-1.59 1.87-39.77 45.73-109.85 45.73-84.69 0-144.48-63.26-144.48-145.56 0-81.31 62-143.4 143.76-143.4 67 0 102 37.31 103.42 38.9a12 12 0 0 1 1.24 14.58l-22.38 34.7a12 12 0 0 1-16.59 3.57 11.79 11.79 0 0 1-1.64-1.27c-.24-.21-26.53-23.88-61.88-23.88-46.12 0-73.92 33.58-73.92 76.08 0 39.61 25.52 79.7 74.28 79.7 38.7 0 65.28-28.34 65.54-28.63a12 12 0 0 1 16.95-.73h.05a12.2 12.2 0 0 1 1.55 1.74l24.55 33.58a12 12 0 0 1-.6 14.85z" class="fa-primary"/></svg> <?php esc_html_e('2022 | All rights reserved', 'shokrino' ); ?></p>
                <?php } ?>
            </div>
            <?php if ($show_footer_menu) { ?>
                <div class="footer-menu">
                    <?php wp_nav_menu( array( 'theme_location' => 'footer-menu' , 'container' => '' , 'depth' => 1 ) ); ?>
                </div>
            <?php }
            if ($show_social) { ?>
                <div class="social-three">
                    <?php
                    require_once SHKRN_TMPL . '/page-parts/social-icons.php';
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
</footer>
<?php
