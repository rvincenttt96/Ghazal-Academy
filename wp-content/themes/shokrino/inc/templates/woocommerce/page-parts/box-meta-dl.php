<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="features-downloady-box-shkrn">
<?php
if (!empty($build_date)) { ?>
    <div class="dwnldy-features">
        <span><?php esc_html_e('Release date:', 'shokrino' ); ?> </span><span><?php echo $build_date; ?></span>
    </div>
<?php } ?>
            <?php if (!empty($update_date)) { ?>
    <div class="dwnldy-features">
        <span><?php esc_html_e('Last Update:', 'shokrino' ); ?> </span><span><?php echo $update_date; ?></span>
    </div>
<?php } ?>
            <?php if (!empty($last_version)) { ?>
    <div class="dwnldy-features">
        <span><?php esc_html_e('Version:', 'shokrino' ); ?> </span><span><?php echo $last_version; ?></span>
    </div>
<?php } ?>
            <?php if (!empty($support_product_dwnld)) { ?>
    <div class="dwnldy-features">
        <span><?php esc_html_e('Support:', 'shokrino' ); ?> </span><span><?php echo $support_product_dwnld; ?></span>
    </div>
<?php } ?>
            <?php if (!empty($dl_c1) && !empty($dl_c1_a)) { ?>
    <div class="dwnldy-features">
        <span><?php echo $dl_c1; ?></span><span><?php echo $dl_c1_a; ?></span>
    </div>
<?php } ?>
            <?php if (!empty($dl_c2) && !empty($dl_c2_a)) { ?>
    <div class="dwnldy-features">
        <span><?php echo $dl_c2; ?></span><span><?php echo $dl_c2_a; ?></span>
    </div>
<?php } ?>
            <?php if (!empty($dl_c3) && !empty($dl_c3_a)) { ?>
    <div class="dwnldy-features">
        <span><?php echo $dl_c3; ?></span><span><?php echo $dl_c3_a; ?></span>
    </div>
<?php } ?>
    </div>
<?php
