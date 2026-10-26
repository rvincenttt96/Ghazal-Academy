<?php defined( 'ABSPATH' ) || exit;
if (!empty(shkrn_options('elementor-selector-header'))) {
    echo do_shortcode('[elementor-template id="' . shkrn_options('elementor-selector-header') . '"]');
} else {
    _e('قالب المنتوری را انتخاب نکرده اید','shokrino');
}