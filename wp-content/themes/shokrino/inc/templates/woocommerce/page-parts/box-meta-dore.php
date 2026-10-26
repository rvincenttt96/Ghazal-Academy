<?php
defined( 'ABSPATH' ) || exit;
?>
    <?php
    $dore_language = skrn_metaoptions('course_language');
    if (!empty($dore_language)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M256 0C397.4 0 512 114.6 512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0zM256 480C272.7 480 296.4 465.6 317.9 422.7C327.8 402.9 336.1 378.1 341.1 352H170C175.9 378.1 184.2 402.9 194.1 422.7C215.6 465.6 239.3 480 256 480V480zM164.3 320H347.7C350.5 299.8 352 278.3 352 256C352 233.7 350.5 212.2 347.7 192H164.3C161.5 212.2 160 233.7 160 256C160 278.3 161.5 299.8 164.3 320V320zM341.1 160C336.1 133 327.8 109.1 317.9 89.29C296.4 46.37 272.7 32 256 32C239.3 32 215.6 46.37 194.1 89.29C184.2 109.1 175.9 133 170 160H341.1zM379.1 192C382.6 212.5 384 233.9 384 256C384 278.1 382.6 299.5 379.1 320H470.7C476.8 299.7 480 278.2 480 256C480 233.8 476.8 212.3 470.7 192H379.1zM327.5 43.66C348.5 71.99 365.1 112.4 374.7 160H458.4C432.6 105.5 385.3 63.12 327.5 43.66V43.66zM184.5 43.66C126.7 63.12 79.44 105.5 53.56 160H137.3C146.9 112.4 163.5 71.99 184.5 43.66V43.66zM32 256C32 278.2 35.24 299.7 41.28 320H132C129.4 299.5 128 278.1 128 256C128 233.9 129.4 212.5 132 192H41.28C35.24 212.3 32 233.8 32 256V256zM458.4 352H374.7C365.1 399.6 348.5 440 327.5 468.3C385.3 448.9 432.6 406.5 458.4 352zM137.3 352H53.56C79.44 406.5 126.7 448.9 184.5 468.3C163.5 440 146.9 399.6 137.3 352V352z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_language); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_duration = skrn_metaoptions('course_duration');
    if (!empty($dore_duration)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M240 112C240 103.2 247.2 96 256 96C264.8 96 272 103.2 272 112V247.4L360.9 306.7C368.2 311.6 370.2 321.5 365.3 328.9C360.4 336.2 350.5 338.2 343.1 333.3L247.1 269.3C242.7 266.3 239.1 261.3 239.1 256L240 112zM256 0C397.4 0 512 114.6 512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0zM32 256C32 379.7 132.3 480 256 480C379.7 480 480 379.7 480 256C480 132.3 379.7 32 256 32C132.3 32 32 132.3 32 256z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_duration); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_type = skrn_metaoptions('course_type');
    if (!empty($dore_type)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M408 120C408 174.6 334.9 271.9 302.8 311.1C295.1 321.6 280.9 321.6 273.2 311.1C241.1 271.9 168 174.6 168 120C168 53.73 221.7 0 288 0C354.3 0 408 53.73 408 120zM288 32C239.4 32 200 71.4 200 120C200 127.4 202.7 139 209.4 154.1C215.9 170.4 225.1 187.4 235.8 204.7C252.9 232.5 272.7 259.4 288 279.1C303.3 259.4 323.1 232.5 340.2 204.7C350.9 187.4 360.1 170.4 366.6 154.1C373.3 139 376 127.4 376 120C376 71.4 336.6 32 288 32V32zM425.2 180.7L543.1 133.5C558.9 127.2 576 138.8 576 155.8V426.6C576 436.4 570 445.2 560.9 448.9L405.9 510.9C402.6 512.2 399 512.4 395.6 511.4L176.9 448.9L32.91 506.5C17.15 512.8 0 501.2 0 484.2V213.4C0 203.6 5.975 194.8 15.09 191.1L138.3 141.9C140.4 152.3 143.6 162.7 147.5 172.6L32 218.8V472.4L160 421.2V304C160 295.2 167.2 288 176 288C184.8 288 192 295.2 192 304V419.9L384 474.8V303.1C384 295.2 391.2 287.1 400 287.1C408.8 287.1 416 295.2 416 303.1V472.4L544 421.2V167.6L405.9 222.9C405.1 223.2 404.3 223.4 403.4 223.6C411.6 209.5 419.1 194.9 425.2 180.7L425.2 180.7z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_type); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_prerequisite = skrn_metaoptions('course_prerequisite');
    if (!empty($dore_prerequisite)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M104 392C104 361.1 129.1 336 160 336C190.9 336 216 361.1 216 392C216 422.9 190.9 448 160 448C129.1 448 104 422.9 104 392zM160 416C173.3 416 184 405.3 184 392C184 378.7 173.3 368 160 368C146.7 368 136 378.7 136 392C136 405.3 146.7 416 160 416zM216 256C216 286.9 190.9 312 160 312C129.1 312 104 286.9 104 256C104 225.1 129.1 200 160 200C190.9 200 216 225.1 216 256zM160 232C146.7 232 136 242.7 136 256C136 269.3 146.7 280 160 280C173.3 280 184 269.3 184 256C184 242.7 173.3 232 160 232zM104 120C104 89.07 129.1 64 160 64C190.9 64 216 89.07 216 120C216 150.9 190.9 176 160 176C129.1 176 104 150.9 104 120zM160 144C173.3 144 184 133.3 184 120C184 106.7 173.3 96 160 96C146.7 96 136 106.7 136 120C136 133.3 146.7 144 160 144zM0 64C0 28.65 28.65 0 64 0H256C291.3 0 320 28.65 320 64V352C320 440.4 248.4 512 160 512C71.63 512 0 440.4 0 352V64zM32 64V352C32 422.7 89.31 480 160 480C230.7 480 288 422.7 288 352V64C288 46.33 273.7 32 256 32H64C46.33 32 32 46.33 32 64z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_prerequisite); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_start_date = skrn_metaoptions('course_start_date');
    if (!empty($dore_start_date)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M64 264C64 241.9 81.91 224 104 224H184C206.1 224 224 241.9 224 264V344C224 366.1 206.1 384 184 384H104C81.91 384 64 366.1 64 344V264zM96 264V344C96 348.4 99.58 352 104 352H184C188.4 352 192 348.4 192 344V264C192 259.6 188.4 256 184 256H104C99.58 256 96 259.6 96 264zM128 64H320V16C320 7.164 327.2 0 336 0C344.8 0 352 7.164 352 16V64H384C419.3 64 448 92.65 448 128V448C448 483.3 419.3 512 384 512H64C28.65 512 0 483.3 0 448V128C0 92.65 28.65 64 64 64H96V16C96 7.164 103.2 0 112 0C120.8 0 128 7.164 128 16V64zM32 448C32 465.7 46.33 480 64 480H384C401.7 480 416 465.7 416 448V192H32V448zM32 128V160H416V128C416 110.3 401.7 96 384 96H64C46.33 96 32 110.3 32 128z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_start_date); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_update_date = skrn_metaoptions('course_update_date');
    if (!empty($dore_update_date)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M250.4 235.3C269.1 216.6 299.5 216.6 318.2 235.3L324.7 241.8C343.4 260.5 343.4 290.9 324.7 309.6L201.6 432.7C197.4 436.9 192 439.9 186.2 441.2L136 452.7C118.8 456.7 103.3 441.2 107.3 423.1L118.8 373.8C120.1 367.1 123.1 362.6 127.3 358.4L250.4 235.3zM295.6 257.9C289.4 251.7 279.2 251.7 272.1 257.9L260.8 270.2L289.8 299.2L302.1 287C308.3 280.8 308.3 270.6 302.1 264.4L295.6 257.9zM267.2 321.8L238.2 292.8L149.1 380.1L141.3 418.7L179 410L267.2 321.8zM112 0C120.8 0 128 7.164 128 16V64H320V16C320 7.164 327.2 0 336 0C344.8 0 352 7.164 352 16V64H384C419.3 64 448 92.65 448 128V448C448 483.3 419.3 512 384 512H64C28.65 512 0 483.3 0 448V128C0 92.65 28.65 64 64 64H96V16C96 7.164 103.2 0 112 0zM416 192H32V448C32 465.7 46.33 480 64 480H384C401.7 480 416 465.7 416 448V192zM384 96H64C46.33 96 32 110.3 32 128V160H416V128C416 110.3 401.7 96 384 96z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_update_date); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_file_size = skrn_metaoptions('course_file_size');
    if (!empty($dore_file_size)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M448 32H64C28.65 32 0 60.65 0 96v64c0 35.35 28.65 64 64 64h384c35.35 0 64-28.65 64-64V96C512 60.65 483.3 32 448 32zM480 160c0 17.64-14.36 32-32 32H64C46.36 192 32 177.6 32 160V96c0-17.64 14.36-32 32-32h384c17.64 0 32 14.36 32 32V160zM416 104c-13.25 0-24 10.74-24 24c0 13.25 10.75 24 24 24c13.26 0 24-10.75 24-24C440 114.7 429.3 104 416 104zM320 104c-13.25 0-24 10.74-24 24c0 13.25 10.75 24 24 24c13.26 0 24-10.75 24-24C344 114.7 333.3 104 320 104zM448 288H64c-35.35 0-64 28.65-64 64v64c0 35.35 28.65 64 64 64h384c35.35 0 64-28.65 64-64v-64C512 316.7 483.3 288 448 288zM480 416c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32v-64c0-17.64 14.36-32 32-32h384c17.64 0 32 14.36 32 32V416zM416 360c-13.25 0-24 10.74-24 24c0 13.25 10.75 24 24 24c13.26 0 24-10.75 24-24C440 370.7 429.3 360 416 360zM320 360c-13.25 0-24 10.74-24 24c0 13.25 10.75 24 24 24c13.26 0 24-10.75 24-24C344 370.7 333.3 360 320 360z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_file_size); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_lesseons = skrn_metaoptions('course_lesseons');
    if (!empty($dore_lesseons)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M16 72C16 58.75 26.75 48 40 48H88C101.3 48 112 58.75 112 72V120C112 133.3 101.3 144 88 144H40C26.75 144 16 133.3 16 120V72zM80 112V80H48V112H80zM496 80C504.8 80 512 87.16 512 96C512 104.8 504.8 112 496 112H176C167.2 112 160 104.8 160 96C160 87.16 167.2 80 176 80H496zM496 240C504.8 240 512 247.2 512 256C512 264.8 504.8 272 496 272H176C167.2 272 160 264.8 160 256C160 247.2 167.2 240 176 240H496zM496 400C504.8 400 512 407.2 512 416C512 424.8 504.8 432 496 432H176C167.2 432 160 424.8 160 416C160 407.2 167.2 400 176 400H496zM88 208C101.3 208 112 218.7 112 232V280C112 293.3 101.3 304 88 304H40C26.75 304 16 293.3 16 280V232C16 218.7 26.75 208 40 208H88zM48 240V272H80V240H48zM16 392C16 378.7 26.75 368 40 368H88C101.3 368 112 378.7 112 392V440C112 453.3 101.3 464 88 464H40C26.75 464 16 453.3 16 440V392zM80 432V400H48V432H80z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_lesseons); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_support = skrn_metaoptions('course_support');
    if (!empty($dore_support)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M223.7 96h.6387c37.75 0 71.97 22.1 87.43 56.63c15.38 34.63 8.982 75.33-16.39 103.3c0 .125 0-.125 0 0L304 256c16.5 0 31.9-8.6 40.52-22.6C349 220.4 352 206.6 352 192c0-70.75-57.25-128-128-128s-128 57.25-128 128c0 40.38 19.12 75.99 48.37 99.49C144.2 290.2 144 289.3 144 288C144.1 276.8 147.1 265.6 152.9 256c-25.5-28-32.13-68.5-16.75-103.3C151.6 118.2 185.8 96 223.7 96zM48 224C56.88 224 64 216.9 64 208V192c0-88.25 71.75-160 160-160s160 71.75 160 160v16C384 252.1 348.1 288 304 288h-32c0-17.62-14.38-32-32-32h-32c-17.62 0-32 14.38-32 32s14.38 32 32 32h96c61.88-.125 111.9-50.13 112-112V192c0-105.9-86.13-192-192-192S32 86.13 32 192v16C32 216.9 39.13 224 48 224zM314.7 352H133.3C59.7 352 0 411.7 0 485.3C0 500.1 11.94 512 26.66 512H421.3C436.1 512 448 500.1 448 485.3C448 411.7 388.3 352 314.7 352zM32.14 480c2.779-53.41 47.11-96 101.2-96h181.3c54.09 0 98.42 42.59 101.2 96H32.14z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_support); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_receive_type = skrn_metaoptions('course_receive_type');
    if (!empty($dore_receive_type)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M315.3 363.3l-144 144C168.2 510.4 164.1 512 160 512s-8.188-1.562-11.31-4.688l-144-144c-6.25-6.25-6.25-16.38 0-22.62s16.38-6.25 22.62 0L144 457.4V16C144 7.156 151.2 0 160 0s16 7.156 16 16v441.4l116.7-116.7c6.25-6.25 16.38-6.25 22.62 0S321.6 357.1 315.3 363.3z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_receive_type); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_level = skrn_metaoptions('course_level');
    if (!empty($dore_level)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M576 48C576 56.84 568.8 64 560 64H448v112C448 184.8 440.8 192 432 192h-128v144c0 8.844-7.156 16-16 16H160v112C160 472.8 152.8 480 144 480h-128C7.156 480 0 472.8 0 464S7.156 448 16 448H128v-112C128 327.2 135.2 320 144 320h128V176C272 167.2 279.2 160 288 160h128V48C416 39.16 423.2 32 432 32h128C568.8 32 576 39.16 576 48z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_level); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $dore_certificate = skrn_metaoptions('course_certificate');
    if (!empty($dore_certificate)) : ?>
        <div class="shkrn-item-feature-dore flex-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M243.1 339.2c10.35-10.58 14.41-26.13 10.6-40.52C251 285.2 250.1 290.8 254.6 277.3c3.844-14.56-.2813-30.14-10.72-40.58C234 226.8 234.1 228.4 231.4 214.9C227.7 200.4 216.5 188.1 202.1 185.1C188.7 181.4 190.5 182.6 180.7 172.4c-10.53-10.69-25.85-14.89-40.35-11.03c-13.36 3.654-11.29 3.646-24.63 0C101.2 157.5 85.87 161.8 75.33 172.4C65.46 182.6 67.26 181.4 53.86 185.1c-14.35 3.891-25.6 15.33-29.29 29.86C21.01 228.4 21.97 226.8 12.14 236.7c-10.44 10.44-14.57 26.02-10.72 40.58C5.037 290.8 4.982 285.2 1.447 298.7c-3.814 14.39 .25 29.94 10.6 40.52c10.04 10.09 8.904 8.18 12.47 22.03c3.875 14.42 15.16 25.8 29.44 29.67c5.164 1.424 7.836 2.188 10.03 3.115V496c0 5.547 2.877 10.69 7.596 13.61c4.689 2.875 10.6 3.156 15.57 .7031L128 489.9l40.85 20.42C171.1 511.4 173.6 512 176 512c2.938 0 5.844-.7969 8.406-2.391C189.1 506.7 192 501.5 192 496v-101.1c2.197-.9297 4.869-1.691 10.03-3.115c14.28-3.877 25.57-15.25 29.44-29.67C235.1 347.4 233.9 349.3 243.1 339.2zM160 470.1l-24.85-12.42c-4.502-2.25-9.814-2.25-14.31 0l-24.85 12.42v-55.32c6.422 1.434 13.16 1.549 19.69-.2305c13.69-3.682 11.03-3.656 24.63 0c6.537 1.779 13.27 1.664 19.69 .2305V470.1zM222.4 302.1c2.328 8.818 3.051 10.34-3.938 17.41c-11.5 11.61-12.94 14.19-17.88 33.46c-1.557 5.934-6.199 6.881-10.31 8.016c-15.82 4.344-18.35 5.844-30.07 17.7c-5.387 5.457-6.715 6.299-14.69 4.094c-15.88-4.35-18.98-4.371-34.94 0c-7.975 2.205-9.301 1.363-14.69-4.094c-11.72-11.86-14.25-13.36-30.07-17.7c-4.117-1.135-8.758-2.082-10.31-8.016c-4.939-19.27-6.377-21.85-17.88-33.46C30.59 312.5 31.31 310.9 33.64 302.1C37.08 289.3 37.08 286.4 33.61 273.8C31.38 265.5 30.52 263.6 37.17 256.9c11.75-11.91 13.22-14.45 17.44-30.42c2.139-8.055 2.697-9.232 11.13-11.53c15.91-4.344 18.44-5.844 30.1-17.75c5.346-5.414 6.725-6.238 14.72-4.047c16.07 4.393 18.81 4.393 34.88 0c8-2.191 9.377-1.367 14.72 4.047c11.66 11.91 14.19 13.41 30.1 17.75C198.7 217.3 199.3 218.5 201.4 226.5C205.6 242.5 207.1 245 218.8 256.9c6.654 6.697 5.787 8.58 3.562 16.81C218.9 286.4 218.9 289.3 222.4 302.1zM493.3 125.3l-106.5-106.5C374.7 6.742 358.5 0 341.5 0L191.1-.0001c-35.35 0-64 28.65-64 64L128 112C128 120.8 135.2 128 144 128C152.8 128 160 120.8 160 112V64c0-17.67 14.33-32 32-32h128v112C320 170.5 341.5 192 368 192H480v256c0 17.67-14.33 32-32 32H240C231.2 480 224 487.2 224 496C224 504.8 231.2 512 240 512l207.1-.0001c35.35 0 64-28.65 64-64v-277.5C512 153.5 505.3 137.3 493.3 125.3zM368 160C359.2 160 352 152.8 352 144V34.08c4.477 1.566 8.666 3.846 12.12 7.299l106.5 106.5C474.1 151.3 476.4 155.5 477.9 160H368z"/></svg>
            <div class="feature-text-shkrn">
                <?php _e($dore_certificate); ?>
            </div>
        </div>
    <?php endif;
    if (empty($dore_certificate) && empty($dore_level) && empty($dore_receive_type) && empty($dore_support) && empty($dore_lesseons) && empty($dore_certificate) && empty($dore_update_date) && empty($dore_start_date) && empty($dore_prerequisite) && empty($dore_type) && empty($dore_duration) && empty($dore_language)) {
        ?>
        <style>
            .box-features-course-side-shkrn {
                padding: 0 !important;
            }
        </style>
    <?php } ?>