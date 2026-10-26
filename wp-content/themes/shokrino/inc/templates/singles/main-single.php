<?php
defined( 'ABSPATH' ) || exit;
get_header();?>
<div class="container">
    <nav class="breadcrumb-box-shkrn">
        <?php shkrn_bread_crumb(); ?>
    </nav>
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
