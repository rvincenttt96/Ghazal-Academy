<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>
<aside class="sidebar-single-shkrn">
    <div class="sidebar-box-sticky-shkrn">
        <?php
        $course_students = shkrn_options('course_students');
        $course_students_text = shkrn_options('course_students_text');
        if ($course_students == 1) { ?>
            <span class="customer-number-side-shkrn">
            <?php
            $students_count_default = get_post_meta(get_the_ID(), 'total_sales', true);
            $students_added_number = skrn_metaoptions('add-to-students-number');
            $students_total_number = $students_count_default + $students_added_number;
            echo $students_total_number;
            ?>
            </span>
            <span class="customer-text-count-shkrn">
                <?php if (!empty($course_students_text)) {
                    echo $course_students_text;
                } else { ?>
                    <?php _e('People , have acquired skills with this course','shokrino'); ?>
                <?php } ?>
            </span>
        <?php } ?>
        <div class="box-features-course-side-shkrn shkrn-only-desktop">
            <?php
            require SHKRN_WOO . '/page-parts/box-meta-dore.php';
            ?>
        </div>
    </div>
    <style>
        .single-page-seperator-shkrn .content-shkrn {
            width: 70%;
        }
    </style>
</aside>