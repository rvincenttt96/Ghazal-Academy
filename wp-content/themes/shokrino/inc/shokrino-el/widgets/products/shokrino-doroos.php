<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_lessons extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_lessons';
    }
    public function get_title() {
        return __('محتوای دوره شکرینو','shokrino');
    }
    public function get_icon() {
        return 'eicon-product-tabs shokrino-el-icon';
    }
    public function get_categories() {
        return ['shkrn_products_el'];
    }
    protected function register_controls()
    {
        $this->start_controls_section(
            'dars_section',
            [
                'label' => __( 'تنظیمات اصلی', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'style_image',
            [
                'raw' => '<img class="admin_select_img_style" src="'.get_template_directory_uri().'/img/lessons-widget.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );
        $this->add_control(
            'image',
            [
                'label' => __( 'آیکون این درس (اختیاری)', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $this->add_control(
            'title_dars_season',
            [
                'label' => __( 'عنوان این فصل از دوره', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,

            ]
        );
        $this->add_control(
            'desc_dars_season',
            [
                'label' => __( 'متن زیر عنوان را وارد کنید', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,

            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'doroos_section',
            [
                'label' => __( 'محتوای درس های دوره', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();


        $repeater->add_control(
            'private_dars',
            [
                'label' => __( 'محتوا خصوصی باشد؟', 'shokrino' ),
                'description' => __('اگر خصوصی باشد این درس فقط به خریداران نشان داده می شود'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __( 'بله', 'shokrino' ),
                    'no' => __( 'خیر', 'shokrino' ),
                ],
                'default' => 'no',
            ]
        );


        $repeater->add_control(
            'dars_title_content',
            [
                'label' => __( 'عنوان درس', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,

            ]
        );

        $repeater->add_control(
            'dars_desc_content',
            [
                'label' => __( 'زیرعنوان درس', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::TEXT,

            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => __( 'آیکون درس', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'description' => __('برای استفاده از آیکون های پیشفرض از گزینه کتابخانه آیکون استفاده کنید و در غیر این صورت میتوانید فایل با فرمت SVG آپلود کنید . ( در صورت خالی گذاشتن نمایش داده نخواهد شد )')
            ]
        );

        $repeater->add_control(
            'download_link_dars',
            [
                'label' => __( 'لینک فایل این بخش برای دانلود را وارد کنید', 'shokrino' ),
                'description' => __('این لینک در کلید روبروی عنوان درس نمایش داده میشود ( درصورت خالی گذاشتن مخفی میشود )'),
                'type' => \Elementor\Controls_Manager::URL

            ]
        );

        $repeater->add_control(
            'hr1',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $repeater->add_control(
            'dars_video_content',
            [
                'label' => __( 'لینک ویدیوی این درس را وارد کنید', 'shokrino' ),
                'description' => __('این ویدیو در قسمت محتوای دوره پخش میشود ( درصورت خالی گذاشتن مخفی میشود )'),
                'type' => \Elementor\Controls_Manager::URL
            ]
        );

        $repeater->add_control(
            'dars_video_poster',
            [
                'label' => __( 'پوستر ویدیو (کاور)', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'dars_content_video_dl_enable',
            [
                'label' => __( 'ویدیو قابل دانلود باشد؟', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __( 'بله', 'shokrino' ),
                    'no' => __( 'خیر', 'shokrino' ),
                ],
                'default' => 'yes',
            ]
        );

        $repeater->add_control(
            'hr2',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $repeater->add_control(
            'dars_content_audio',
            [
                'label' => __( 'لینک صوتی این درس را وارد کنید', 'shokrino' ),
                'description' => __('این فایل صوتی در قسمت محتوای دوره پخش میشود ( درصورت خالی گذاشتن مخفی میشود )'),
                'type' => \Elementor\Controls_Manager::URL
            ]
        );

        $repeater->add_control(
            'dars_content_audio_dl_enable',
            [
                'label' => __( 'فایل صوتی قابل دانلود باشد؟', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __( 'بله', 'shokrino' ),
                    'no' => __( 'خیر', 'shokrino' ),
                ],
                'default' => 'yes',
            ]
        );

        $repeater->add_control(
            'hr3',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $repeater->add_control(
            'dars_content',
            [
                'label' => __( 'محتوا های دیگر این درس را در این بخش وارد کنید', 'shokrino' ),
                'description' => __( 'این بخش میتواند شامل عکس ، متن یا و هر محتوای دیگری باشد', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::WYSIWYG
            ]
        );


        $this->add_control(
            'doroos_list',
            [
                'label' => __( 'لیست محتوا ها', 'shokrino' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{dars_title_content}}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $product;
        global $shokrino_options;
        $settings = $this->get_settings_for_display();
        $widget_id = rand(0, 100000);
        $image_dore = $settings['image']['url'];
        $title_dars_season = (!empty($settings['title_dars_season'])) ? $settings['title_dars_season'] : $settings['titlelesson'];
        $desc_dars_season = (!empty($settings['desc_dars_season'])) ? $settings['desc_dars_season'] : $settings['subtitlelesson'];
        $doroos_list = (!empty($settings['doroos_list'])) ? $settings['doroos_list'] : $settings['lessons_list'];
        ?>

        <div class="sarfasl-box-shokrino">
            <input type="checkbox" id="chevron-doroos-<?php echo $widget_id; ?>" name="chevron-doroos-<?php echo $widget_id; ?>" style="display: none">
            <label for="chevron-doroos-<?php echo $widget_id; ?>" class="sarfasl-top-section">
                <div class="image-sarfasl-top">
                    <?php echo '<img src="'.$image_dore.'">'; ?>
                </div>
                <div class="sarfasl-box-subtitle-title">
                    <h4><?php echo $title_dars_season; ?></h4>
                    <p><?php echo $desc_dars_season; ?></p>
                </div>
                <div class="sarfasl-box-chevron-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M6 9l6 6l6 -6" />
                    </svg>
                </div>
            </label>
            <div class="sarfasl-content-box-doroos">
                <?php foreach ($doroos_list as $index=>$current_dars) {
                    $loop_id = $index;
                    $old_private_dars = !empty($current_dars['private_lesson']) ? $current_dars['private_lesson'] : '';
                    $private_dars = (!empty($current_dars['private_dars'])) ? $current_dars['private_dars'] : $old_private_dars;
                    $old_dars_title_content = !empty($current_dars['subtitlelesson']) ? $current_dars['subtitlelesson'] : '';
                    $dars_title_content = (!empty($current_dars['dars_title_content'])) ? $current_dars['dars_title_content'] : $old_dars_title_content;
                    $old_dars_desc_content = !empty($current_dars['subtitlelesson_sub']) ? $current_dars['subtitlelesson_sub'] : '';
                    $dars_desc_content = (!empty($current_dars['dars_desc_content'])) ? $current_dars['dars_desc_content'] : $old_dars_desc_content;
                    $old_download_link_dars = !empty($current_dars['download_lesson']) ? $current_dars['download_lesson'] : '';
                    $download_link_dars = (!empty($current_dars['download_link_dars'])) ? $current_dars['download_link_dars'] : $old_download_link_dars;
                    $old_dars_video_content = !empty($current_dars['lesson_content_video']) ? $current_dars['lesson_content_video'] : '';
                    $dars_video_content = (!empty($current_dars['dars_video_content'])) ? $current_dars['dars_video_content'] : $old_dars_video_content;
                    $old_dars_video_poster = !empty($current_dars['lesson_content_video_poster']) ? $current_dars['lesson_content_video_poster'] : '';
                    $dars_video_poster = (!empty($current_dars['dars_video_poster'])) ? $current_dars['dars_video_poster'] : $old_dars_video_poster;
                    $old_dars_content_video_dl_enable = !empty($current_dars['lesson_content_video_dl']) ? $current_dars['lesson_content_video_dl'] : '';
                    $dars_content_video_dl_enable = (!empty($current_dars['dars_content_video_dl_enable'])) ? $current_dars['dars_content_video_dl_enable'] : $old_dars_content_video_dl_enable;
                    $old_dars_content_audio = !empty($current_dars['lesson_content_audio']) ? $current_dars['lesson_content_audio'] : '';
                    $dars_content_audio = (!empty($current_dars['dars_content_audio'])) ? $current_dars['dars_content_audio'] : $old_dars_content_audio;
                    $old_dars_content_audio_dl_enable = !empty($current_dars['lesson_content_audio_dl']) ? $current_dars['lesson_content_audio_dl'] : '';
                    $dars_content_audio_dl_enable = (!empty($current_dars['dars_content_audio_dl_enable'])) ? $current_dars['dars_content_audio_dl_enable'] : $old_dars_content_audio_dl_enable;
                    $old_dars_content = !empty($current_dars['lesson_content']) ? $current_dars['lesson_content'] : '';
                    $dars_content = (!empty($current_dars['dars_content'])) ? $current_dars['dars_content'] : $old_dars_content;
                    $video_pro = is_array($dars_video_content) ? $dars_video_content["url"] : '';
                    $poster = is_array($dars_video_poster) ? $dars_video_poster["url"] : '';
                    $video_dl_enable = $dars_content_video_dl_enable;
                    $audio_pro = is_array($dars_content_audio) ? $dars_content_audio["url"] : '';
                    $audio_dl_enable = $dars_content_audio_dl_enable; ?>
                    <div class="item-doroos-dore-contents">
                        <div id="open-dars-content-shkrn-<?php echo $widget_id.$loop_id; ?>" class="item-dars-title-top">
                            <div class="icon-item-dars">
                                <?php echo '<i class="' . $current_dars['icon']['value'] . '"></i>'; ?>
                            </div>
                            <div class="titles-item-dars">
                                <span><?php echo $dars_title_content; ?></span>
                                <p><?php echo $dars_desc_content; ?></p>
                            </div>
                            <div class="buttons-item-dars">
                                <?php
                                if ($private_dars == 'yes') {
                                    $check_private = true;
                                } else {
                                    $check_private = false;
                                }

                                if ($check_private == true) {

                                    $id = get_the_ID();
                                    if (is_user_logged_in()) {
                                        $current_user = wp_get_current_user();
                                    }
                                    if (wc_customer_bought_product($current_user->user_email, $current_user->ID, $id)) {
                                        if (!empty($download_link_dars['url'])) { ?>
                                            <a class="download-dars-icon" href="<?php echo $download_link_dars['url']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e2e2e2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                    <polyline points="7 11 12 16 17 11" />
                                                    <line x1="12" y1="4" x2="12" y2="16" />
                                                </svg>
                                            </a>
                                        <?php } ?>
                                        <div class="box-private-public-shkrn bought-dats-item">
                                            <i class="fa fa-lock-open"></i>
                                            <span><?php echo __('دردسترس','shokrino'); ?></span>
                                        </div>
                                    <?php } else { ?>
                                        <div class="box-private-public-shkrn not-bought-dats-item">
                                            <i class="fa fa-lock"></i>
                                            <span><?php echo __('خصوصی','shokrino'); ?></span>
                                        </div>
                                    <?php } ?>

                                <?php } else {
                                    if (!empty($download_link_dars['url'])) { ?>
                                        <a class="download-dars-icon" href="<?php echo $download_link_dars['url']; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e2e2e2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                <polyline points="7 11 12 16 17 11" />
                                                <line x1="12" y1="4" x2="12" y2="16" />
                                            </svg>
                                        </a>
                                    <?php } ?>
                                    <div class="box-private-public-shkrn pubic-dats-item">
                                        <?php echo __('رایگان','shokrino'); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="item-dars-content-bottom">
                            <?php
                            if ($check_private == true) {

                                $id = get_the_ID();
                                if (is_user_logged_in()) {
                                    $current_user = wp_get_current_user();
                                }
                                if (wc_customer_bought_product($current_user->user_email, $current_user->ID, $id)) {

                                    if (!empty($video_pro)) {
                                        $attr = array(
                                            'src' => $video_pro,
                                            'width' => 640,
                                            'poster' => $poster,
                                        );

                                        ?>
                                        <div class="video-product">
                                            <?php echo wp_video_shortcode($attr);
                                            if ($video_dl_enable == "yes") {?>
                                                <a href="<?php echo $video_pro; ?>" target="_blank" class="download-button-course"><i class="fas fa-download"></i><?php echo __('دانلود ویدیو','shokrino'); ?></a>
                                            <?php } ?>
                                        </div>
                                    <?php }

                                    if (!empty($audio_pro)) {
                                        $attr = array(
                                            'src'      => $audio_pro,
                                        );
                                        ?>
                                        <div class="audio-product">
                                            <?php echo wp_audio_shortcode( $attr );
                                            if ($audio_dl_enable == "yes") {?>
                                                <a href="<?php echo $audio_pro; ?>" target="_blank" class="download-button-course"><i class="fas fa-download"></i><?php esc_html_e('دانلود فایل صوتی', 'shokrino' ); ?></a>
                                            <?php } ?>
                                        </div>
                                    <?php }

                                    echo $dars_content;

                                } else {
                                    echo  __('محتوای این درس فقط به خریداران نمایش داده میشود . برای دسترسی کامل به محتوای دوره آن را خریداری کنید.','shokrino');
                                } ?>

                            <?php } else {

                                if (!empty($video_pro)) {
                                    $attr = array(
                                        'src' => $video_pro,
                                        'width' => 640,
                                        'poster' => $poster,
                                    );

                                    ?>
                                    <div class="video-product">
                                        <?php echo wp_video_shortcode($attr);
                                        if ($video_dl_enable == "yes") {?>
                                            <a href="<?php echo $video_pro; ?>" target="_blank" class="download-button-course"><i class="fas fa-download"></i><?php echo __('دانلود ویدیو','shokrino'); ?></a>
                                        <?php } ?>
                                    </div>
                                <?php }

                                if (!empty($audio_pro)) {
                                    $attr = array(
                                        'src'      => $audio_pro,
                                    );
                                    ?>
                                    <div class="audio-product">
                                        <?php echo wp_audio_shortcode( $attr );
                                        if ($audio_dl_enable == "yes") {?>
                                            <a href="<?php echo $audio_pro; ?>" target="_blank" class="download-button-course"><i class="fas fa-download"></i><?php esc_html_e('دانلود فایل صوتی', 'shokrino' ); ?></a>
                                        <?php } ?>
                                    </div>
                                <?php }

                                echo $dars_content;
                            } ?>
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var button = document.getElementById('open-dars-content-shkrn-<?php echo $widget_id.$loop_id; ?>');
                            button.addEventListener("click", function() {
                                this.classList.toggle("active");
                                var contentBoughtSHKRN = this.nextElementSibling;
                                if (contentBoughtSHKRN.style.display === "flex") {
                                    contentBoughtSHKRN.style.display = "none";
                                    contentBoughtSHKRN.style.visibility = "hidden";
                                } else {
                                    contentBoughtSHKRN.style.display = "flex";
                                    contentBoughtSHKRN.style.visibility = "visible";
                                }
                            });
                        });
                    </script>
                <?php } ?>
            </div>
        </div>
        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_doroos .elementor-widget-container{min-height: 1px;}
                        
            </style>
			';
        }

    }

    protected function content_template() {}

}