<?php
defined( 'ABSPATH' ) || exit;
get_header();
$page_header_show = skrn_metaoptions('page_header_show');
$page_footer_show = skrn_metaoptions('page_footer_show');
$page_breadcrumb_show = skrn_metaoptions('page_breadcrumb_show');
if ($page_header_show == "false") { ?>
    <style>
        header {
            display: none !important;
        }
    </style>
<?php }
if ($page_footer_show == "false") { ?>
    <style>
        footer {
            display: none !important;
        }
    </style>
<?php } ?>
    <div class="container">
        <?php
        if ($page_breadcrumb_show != "false") { ?>
        <nav class="breadcrumb-box-shkrn">
            <?php shkrn_bread_crumb(); ?>
        </nav>
        <?php } else { ?>
            <style>
                .content-single-shkrn {
                    margin-top: 25px;
                }
            </style>
        <?php } ?>
        <div class="single-page-shkrn">
            <div class="single-page-main-part post-page-shkrn">
                <?php if (have_posts()) :
                    while (have_posts()) : the_post(); ?>
                        <article class="content-single-shkrn">
                            <h1 class="page-title-shkrn entry-title"><?php the_title(); ?></h1>
                            <?php the_content(); ?>
                        </article>
                    <?php
                    endwhile;
                endif; ?>
            </div>
        </div>
    </div>
<?php get_footer();

