<?php
defined( 'ABSPATH' ) || exit;
?>
<section class="posts-section-shkrn flex-center">
    <div class="posts-section-header flex-center">
        <div class="posts-title-shkrn flex-center">
            <h2><?php esc_html_e('Last Posts', 'shokrino' ); ?></h2>
            <h5><?php esc_html_e('Important tips you should know!', 'shokrino' ); ?></h5>
        </div>
        <a class="more-button-section-shkrn flex-center" href="<?php echo get_post_type_archive_link('post') ?>"><?php esc_html_e('View All ', 'shokrino' ); ?>
            <svg xmlns="http://www.w3.org/2000/svg" height="18px" style="margin-bottom: 3px;" fill="#fff"  viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M224 239l135.61-136a23.78 23.78 0 0 1 33.8 0L416 125.64a23.94 23.94 0 0 1 0 33.89l-96.16 96.37L416 352.27a23.94 23.94 0 0 1 0 33.89L393.53 409a23.78 23.78 0 0 1-33.8 0L224.12 273a23.94 23.94 0 0 1-.1-34z" class="fa-secondary"/><path d="M32.11 239l135.55-136a23.77 23.77 0 0 1 33.79 0L224 125.74a23.94 23.94 0 0 1 0 33.89L127.89 256l96 96.47a23.94 23.94 0 0 1 0 33.89L201.35 409a23.77 23.77 0 0 1-33.79 0L32 273a24 24 0 0 1 .11-34z" class="fa-primary"/></svg>
        </a>
    </div>
    <div id="home-page-post-slider-shkrn" class="swiper posts-wrapper-slider-shkrn swiper-initialized swiper-horizontal swiper-backface-hidden">
        <div class="swiper-wrapper post-slider-shokrino" aria-live="polite">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post(); ?>
                    <div class="box-article swiper-slide">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) {
                                the_post_thumbnail(optimize_img_shkrn('posts'));
                            } else { ?>
                                <img src="<?php echo SHKRN_ASSETS . '/img/0.jpg'; ?>">
                            <?php } ?>
                            <h2><?php the_title(); ?></h2>
                            <p><?php the_excerpt(); ?></p>
                            <div class="btn-more"><?php esc_html_e('Read more', 'shokrino' ); ?></div>
                        </a>
                    </div>
                <?php
                endwhile;
            endif;
            ?>
        </div>
        <div class="swiper-button-prev swiper-btn-style-shkrn"></div>
        <div class="swiper-button-next swiper-btn-style-shkrn"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>
<?php
