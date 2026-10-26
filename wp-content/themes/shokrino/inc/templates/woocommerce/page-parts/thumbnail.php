<?php if (has_post_thumbnail()) { ?>
    <figure>
        <?php the_post_thumbnail('product_single'); ?>
    </figure>
<?php } else { ?>
    <figure>
        <img class="image3" src="<?php echo SHKRN_ASSETS . '/img/0.jpg' ?>">
    </figure>
<?php }