<?php
defined( 'ABSPATH' ) || exit;

class Elementor_shokrino_audio_player extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shokrino_audio_player';
    }

    public function get_title() {
        return 'پخش کننده فایل صوتی شکرینو';
    }

    public function get_icon() {
        return 'eicon-play shokrino-el-icon';
    }


    public function get_categories() {
        return ['general_category'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'تنظیمات فایل صوتی', 'shokrino' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'player_type',
            array(
                'label'   => __( 'نوع پخش کننده فایل صوتی', 'shokrino' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'html5',
                'options' => array(
                    'html5'       => __( 'پیشفرض html5 (وابسته به مرورگر)', 'shokrino' ),
                    'wordpress'      => __( 'آدیو پلیر وردپرس', 'shokrino' ),
                ),
            )
        );

        $this->add_control(
            'style_image1',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/audio-player.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'condition'   => array(
                    'player_type' => 'wordpress',
                ),
            ],
        );

        $this->add_control(
            'style_image2',
            [
                'raw' => '<img class="admin_select_img_style" src="'.SHKRN_ASSETS.'/img/audio-player2.jpg">',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'condition'   => array(
                    'player_type' => 'html5',
                ),
            ],
        );
        

        $this->add_control(
            'self_hosted_media',
            array(
                'label'      => esc_html__( 'انتخاب فایل از هاست خودتان', 'shokrino' ),
                'type'       => \Elementor\Controls_Manager::MEDIA,
                'media_type' => 'audio',
                'dynamic' => array(
                    'active' => true,
                ),
            )
        );

        $this->add_control(
            'self_hosted_url',
            array(
                'label'      => esc_html__( 'لینک فایل صوتی از هاست خودتان', 'shokrino' ),
                'type'       => \Elementor\Controls_Manager::TEXT,
            )
        );
        
        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $player_type = $settings['player_type'];
        if (empty($settings['self_hosted_media']['url'])) {
            $audio_url = $settings['self_hosted_url'];
        } else {
            $audio_url = $settings['self_hosted_media']['url'];
        }
        ?>

            <?php
                if ($player_type == "html5") {
                ?>
                <audio class="" src="<?php echo $audio_url; ?>" loop="" controls=""></audio>
                <?php } else {
                    if (!empty($audio_url)) {
                        $attr = array(
                            'src'      => $audio_url,
                        );
                        echo wp_audio_shortcode($attr);
                    }
                } ?>
        <style>
            .elementor-widget-shokrino_audio_player audio {
                border-radius: 25px !important;
            }
            .elementor-widget-shokrino_audio_player audio {
                border-radius: 25px !important;
                width: 100% !important;
            }
            .elementor-widget-shokrino_audio_player .wp-audio-shortcode {
                border-radius: 25px !important;
                overflow: hidden !important;
            }
        </style>
        <?php

        if (is_admin()) {
            echo '<style>.elementor-widget-shokrino_audio_player .elementor-widget-container{min-height: 1px;}
            .elementor-widget-shokrino_audio_player .elementor-widget-container {
                margin: 0px !important;
                width: 100%;
                height: 100%;
            }
            .elementor-widget-shokrino_audio_player audio {
                border-radius: 25px !important;
                width: 100% !important;
            }
            .elementor-widget-shokrino_audio_player .wp-audio-shortcode {
                border-radius: 25px !important;
                overflow: hidden !important;
            }
            </style>';
        }

    }

    protected function content_template() {}

}