<?php
defined( 'ABSPATH' ) || exit;

$video_pro_array = skrn_metaoptions('shokrino_video_Woo');

if (!empty($video_pro_array) && is_array($video_pro_array)) {
    $video_pro = $video_pro_array["url"];
    $poster_array = skrn_metaoptions('shokrino_video_Woo_poster');
    $poster = $poster_array["url"];
}

if (!empty($video_pro)) {
    $attr = array(
        'src' => $video_pro,
        'width' => '804',
        'height' => 'auto',
        'poster' => $poster,
    ); ?>

    <div class="video-product">
        <?php echo wp_video_shortcode($attr); ?>
    </div>

<?php }