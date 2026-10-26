<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_video_player extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_video_player';
    }

    public function get_title() {
        return 'پخش کننده ویدیو شکرینو';
    }

    public function get_icon() {
        return 'eicon-youtube shokrino-el-icon';
    }


    public function get_categories() {
        return ['general_category'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'تنظیمات ویدیو', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'video_source',
            array(
                'label'   => __( 'منبع ویدیو', 'shokrino' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'hosted',
                'options' => array(
                    'aparat'       => __( 'آپارات', 'shokrino' ),
                    'hosted'      => __( 'از هاست خودتان', 'shokrino' ),
                ),
            )
        );

        $this->add_control(
            'player_type',
            array(
                'label'   => __( 'نوع پخش کننده ویدیو', 'shokrino' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'html5',
                'options' => array(
                    'html5'       => __( 'پیشفرض html5 (وابسته به مرورگر)', 'shokrino' ),
                    'wordpress'      => __( 'ویدیو پلیر وردپرس', 'shokrino' ),
                ),
                'condition'   => array(
                    'video_source' => 'hosted',
                ),
            )
        );

        $this->add_control(
            'style_image1',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/video-player.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'condition'   => array(
                    'player_type' => 'wordpress',
                ),
            ],
        );

        $this->add_control(
            'style_image2',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/video-player2.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'condition'   => array(
                    'player_type' => 'html5',
                ),
            ],
        );

        $this->add_control(
            'aparat_url',
            array(
                'label'       => __( 'کد کوتاه ویدیو آپارات', 'shokrino' ),
                'description'       => __( 'شناسه ویدئوی آپارات را درج کنید :

به‌عنوان مثال شناسه ویدئوی https://www.aparat.com/v/8OHwC عبارت است از : 8OHwC', 'shokrino' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'dynamic'     => array(
                    'active'     => true,
                ),
                'placeholder' => __( 'برای مثال: 8OHwC', 'shokrino' ),
                'default'     => '8OHwC',
                'label_block' => true,
                'condition'   => array(
                    'video_source' => 'aparat',
                ),
            )
        );

        $this->add_control(
            'self_hosted_media',
            array(
                'label'      => esc_html__( 'انتخاب فایل از هاست خودتان', 'shokrino' ),
                'type'       => \Elementor\Controls_Manager::MEDIA,
                'media_type' => 'video',
                'condition'   => array(
                    'video_source' => 'hosted',
                ),
                'dynamic' => array(
                    'active' => true,
                ),
            )
        );

        $this->add_control(
            'self_hosted_url',
            array(
                'label'      => esc_html__( 'لینک ویدیو از هاست خودتان', 'shokrino' ),
                'type'       => \Elementor\Controls_Manager::TEXT,
                'condition'   => array(
                    'video_source' => 'hosted',
                ),
            )
        );

        $this->add_control(
            'poster',
            array(
                'label' => esc_html__( 'پوستر ویدیو (کاور)', 'shokrino' ),
                'type'  => \Elementor\Controls_Manager::MEDIA,
                'condition' => array(
                    'video_source'  => 'hosted',
                ),
            )
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $video_source = $settings['video_source'];
        $player_type = $settings['player_type'];
        $aparat_url = $settings['aparat_url'];
        if (empty($settings['self_hosted_media']['url'])) {
            $video_url = $settings['self_hosted_url'];
        } else {
            $video_url = $settings['self_hosted_media']['url'];
        }
        $poster = $settings['poster']['url'];
        ?>

            <?php
            if ($video_source == "aparat") {
                echo do_shortcode("[aparat id='$aparat_url']");
                } else {
                if ($player_type == "html5") {
                ?>
                <video class="" src="<?php echo $video_url; ?>" poster="<?php echo $poster; ?>" loop="" controls=""><span>salam</span></video>
                <?php } else {
                    if (!empty($video_url)) {
                        $attr = array(
                            'src' => $video_url,
                            'width' => '800',
                            'poster' => $poster,
                        );
                        echo wp_video_shortcode($attr);
                    }
                } } ?>
        <style>
            .elementor-widget-shokrino_video_player video {
                border-radius: 25px !important;
                object-fit: fill;
            }
        </style>
        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_video_player .elementor-widget-container{min-height: 1px;}
            .elementor-widget-shokrino_video_player .elementor-widget-container {
                margin: 0px !important;
                width: 100%;
                height: 100%;
            }
            .elementor-widget-shokrino_video_player video {
                border-radius: 25px !important;
            }
            </style>';
        }

    }

    protected function content_template() {}

}