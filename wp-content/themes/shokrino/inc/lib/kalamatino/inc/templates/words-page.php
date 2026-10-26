<?php
do_action( 'add_meta_boxes', 'klmtn_words_meta_box', 0 );
?>
<script>
    var $ = jQuery;
    $(document).ready(function(){$(".submit-klmtn").click(function(){$(".lite-box-spinner-saving").css({display:"flex"})})})
</script>
<div class="lite-box-spinner-saving">
    <svg aria-hidden="true" focusable="false" class="loader loading" viewBox="0 0 38 38" width="100px" height="100px">
        <g transform="translate(2 2)" stroke="currentColor" stroke-width="4" fill-rule="evenodd" style="fill: #0096ff;stroke: #e6e6e6;">
            <circle class="st0" stroke-opacity=".5" cx="17" cy="17" r="16.5"></circle>
            <path class="st1" d="M33.5 17C33.5 7.9 26.1.5 17 .5" transform="rotate(249.767 17 17)">
                <animateTransform accumulate="none" additive="replace" attributeName="transform" calcMode="linear" dur="1s" from="0 17 17" repeatCount="indefinite" restart="always" to="360 17 17" type="rotate"></animateTransform>
            </path>
        </g>
    </svg>
    <svg id="Layer_1" class="kalamatino-icon-loader" width="50" height="50" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1917.44 1917.44" style="enable-background:new 0 0 1917.44 1917.44;" xml:space="preserve">
        <g>
            <path class="st0" d="M929.21,1751c-52.2,0-103.12-10.13-151.35-30.1c-48.23-19.98-91.4-48.82-128.31-85.73l-116.54-116.54   c-92.86-92.86-92.86-243.41,0-336.27l0,0l299.11,299.11c3.97,3.97,8.5,7.32,13.45,9.97c69.91,37.49,145.94,24.81,195.16-24.41   l3.88-3.88c61.5-61.5,61.5-161.56,0-223.05l-90.18-90.18l168.13-168.13l90.18,90.18c37.72,37.72,66.95,81.76,86.87,130.89   c19.22,47.4,28.97,97.45,28.97,148.77c0,51.31-9.75,101.36-28.97,148.77c-19.93,49.14-49.15,93.18-86.87,130.89l-11.44,11.44   c-11.71,11.71-24.3,22.52-37.81,32.1C1092.48,1725.23,1012.47,1751,929.21,1751z"/>
        </g>
        <g>
            <path class="st0" d="M735.86,936.27l-90.18-90.18c-37.72-37.72-66.94-81.76-86.87-130.89c-19.22-47.4-28.97-97.45-28.97-148.77   s9.75-101.36,28.97-148.76c19.93-49.14,49.15-93.18,86.87-130.89l3.88-3.88c36.91-36.91,80.08-65.76,128.31-85.73   c48.23-19.98,99.15-30.11,151.35-30.11c52.2,0,103.12,10.13,151.35,30.11c48.23,19.98,91.4,48.82,128.31,85.73l109.81,109.81   c92.86,92.86,92.86,243.41,0,336.27l0,0l-277.94-277.94c-29.79-29.79-69.4-46.2-111.52-46.19c-42.13,0-81.74,16.41-111.52,46.2   l-3.88,3.88c-61.5,61.49-61.5,161.55,0,223.05l90.18,90.18L735.86,936.27z"/>
        </g>
        <path class="st0" d="M1407.66,876.74L1011.7,480.79c-46.43-46.43-121.7-46.43-168.13,0v0c-46.43,46.43-46.43,121.7,0,168.13  l113.34,113.34L731.55,987.62L619.32,875.39c-46.43-46.43-121.7-46.43-168.13,0v0c-46.43,46.43-46.43,121.7,0,168.13l395.95,395.95  c46.43,46.43,121.7,46.43,168.13,0l0,0c46.43-46.43,46.43-121.7,0-168.13l-115.58-115.58l225.36-225.36l114.48,114.48  c46.43,46.43,121.7,46.43,168.13,0l0,0C1454.09,998.44,1454.09,923.17,1407.66,876.74z"/>
    </svg>
</div>
<div class="wrap">
    <h2><?php echo __('Words List','shokrino'); ?></h2>
    <div class="fx-settings-meta-box-wrap">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-1 kalamatino">
                <div id="postbox-container-2" class="postbox-container">
                    <div class="box-top-klmtn">
                        <div class="form-klmtn-add-words">
                            <h3><?php _e('text changer','shokrino'); ?></h3>
                            <form action="" method="POST" id="klmtn-save-words">
                                <label for="from-text">
                                    <p><?php echo __('from','shokrino'); ?></p>
                                    <input type="text" name="from-text">
                                </label>
                                <label for="to-text">
                                    <p><?php echo __('to','shokrino'); ?></p>
                                    <input type="text" name="to-text">
                                </label>
                                <button type="submit" class="submit-klmtn" name="savewords"><?php echo __('add to list','shokrino'); ?></button>
                            </form>
                            <p><?php echo __('simply add the text (from) you want to change (to) . this feature works 99%','shokrino'); ?></p>
                        </div>
                    </div>
                    <div class="table-klmtn">
                        <div class="table-head-klmtn">
                            <div class="box-table-head-klmtn"><?php echo __('From','shokrino'); ?></div>
                            |
                            <div class="box-table-head-klmtn"><?php echo __('Replaced with','shokrino'); ?></div>
                            |
                            <div class="box-table-head-klmtn"><?php echo __('Actions','shokrino'); ?></div>
                        </div>
                        <div class="table-body-klmtn">
                            <?php
                            $from_text_table = get_option('klmtn_from_text','');
                            $to_text_table = get_option('klmtn_to_text','');
                            if (is_array($from_text_table)) {
                                $length = count($from_text_table);
                            }
                            for ($i = 0; $i < $length; $i++) { ?>
                                <div class="row-table-klmtn deleted-row-<?php echo esc_attr($i); ?>">
                                    <div class="table-from-klmtn"><?php print esc_attr($from_text_table[$i]); ?></div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                        <line x1="5" y1="12" x2="11" y2="18" />
                                        <line x1="5" y1="12" x2="11" y2="6" />
                                    </svg>
                                    <div class="table-to-klmtn"><?php print esc_attr($to_text_table[$i]); ?></div>
                                    <div class="table-actions-klmtn">
                                        <form method="POST" action="" id="">
                                            <button name="delete-word<?php echo esc_attr($i); ?>" type="submit" class="button button-primary submit-klmtn">
                                                <?php echo __('delete this row','shokrino'); ?>
                                            </button>
                                        </form>
                                        <?php
                                        if (isset($_POST['delete-word'.$i])) {
                                            $arr = $from_text_table;
                                            $val = $from_text_table[$i];
                                            $key = array_search($val, $arr, true);
                                            if ($key !== false) {
                                                array_splice($arr, $key, 1);
                                            }
                                            $newfrom_text_option = $arr;
                                            $empty_array_words = count($newfrom_text_option);
                                            if ($empty_array_words == 0) {
                                                delete_option('klmtn_from_text');
                                            } else {
                                                update_option('klmtn_from_text', $newfrom_text_option);
                                            }
                                            $arr = $to_text_table;
                                            $val = $to_text_table[$i];
                                            $key = array_search($val, $arr, true);
                                            if ($key !== false) {
                                                array_splice($arr, $key, 1);
                                            }
                                            $newto_text_option = $arr;
                                            $empty_array_words = count($newto_text_option);
                                            if ($empty_array_words == 0) {
                                                delete_option('klmtn_to_text');
                                            } else {
                                                update_option('klmtn_to_text', $newto_text_option);
                                            }
                                            echo "<meta http-equiv='refresh' content='0'>";
                                            echo "<style>.deleted-row-{$i} { display: none !important;}.lite-box-spinner-saving {display: flex !important;}";
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$from_text_for_array = sanitize_text_field($_POST['from-text']);
$to_text_for_array = sanitize_text_field($_POST['to-text']);
if (isset($_POST['savewords']) && !empty($_POST['from-text']) && !empty($_POST['to-text'])) {
    $from_text_option = get_option('klmtn_from_text','');
    if (empty($from_text_option)) {
        add_option('klmtn_from_text', array(sanitize_text_field($_POST['from-text'])));
    } else {
        $from_text_option = array_merge($from_text_option, array(sanitize_text_field($_POST['from-text'])));
        update_option('klmtn_from_text', $from_text_option);
    }
    $to_text_option = get_option('klmtn_to_text','');
    if (empty($to_text_option)) {
        add_option('klmtn_to_text', array(sanitize_text_field($_POST['to-text'])));
    } else {
        $to_text_option = array_merge($to_text_option, array(sanitize_text_field($_POST['to-text'])));
        update_option('klmtn_to_text', $to_text_option);
    }
}
if (isset($_POST['savewords']) && !empty($_POST['from-text']) && !empty($_POST['to-text'])) {
    ?>
    <style>
        .lite-box-spinner-saving {
            display: flex !important;
        }
    </style>
    <?php
    echo "<meta http-equiv='refresh' content='0'>";
}
?>
