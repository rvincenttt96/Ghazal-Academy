<?php
defined( 'ABSPATH' ) || exit;

function darkmode_shkrn_button($place='header'){
    if (shkrn_options('show_darkmode_shokrino')) {
    if ($place == 'header'){ ?>
    <a href="#" aria-label="Theme mode changer" class="moon-icon-shkrn shkrn-mode-changer-btn flex-center header-icon-shkrn" onclick="set_lightmode_shkrn()">
        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 30.46 30.46">
            <path id="Path_12" data-name="Path 12" d="M0,0H30.46V30.46H0Z" fill="none"/>
            <path id="Path_13" data-name="Path 13" d="M14.422,3h.5A9.519,9.519,0,0,0,24.973,18.8,11.423,11.423,0,1,1,14.422,2.992Z" transform="translate(0.808 0.805)" fill="none" stroke="var(--shkrn-var-color-light-dark-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
        </svg>
    </a>
    <a href="#" aria-label="Theme mode changer" class="system-icon-shkrn shkrn-mode-changer-btn flex-center header-icon-shkrn" onclick="set_darkmode_shkrn()">
        <svg  xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <circle cx="12" cy="12" r="9"></circle>
            <line x1="12" y1="3" x2="12" y2="21"></line>
            <line x1="12" y1="9" x2="16.65" y2="4.35"></line>
            <line x1="12" y1="14.3" x2="19.37" y2="6.93"></line>
            <line x1="12" y1="19.6" x2="20.85" y2="10.75"></line>
        </svg>
    </a>
    <a href="#" aria-label="Theme mode changer" class="sun-icon-shkrn shkrn-mode-changer-btn flex-center header-icon-shkrn" onclick="set_automode_shkrn()">
        <svg id="sun-icon-shkrn" xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 30 30">
            <path id="Path_14" data-name="Path 14" d="M0,0H30V30H0Z" fill="none"/>
            <circle id="Ellipse_3" data-name="Ellipse 3" cx="5" cy="5" r="5" transform="translate(10.46 9.54)" fill="none" stroke="var(--shkrn-var-color-light-dark-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
            <path id="Path_15" data-name="Path 15" d="M3,14.25H4.25M14.25,3V4.25m10,10H25.5m-11.25,10V25.5m-8-19.25.875.875M22.25,6.25l-.875.875m0,14.25.875.875M7.125,21.375l-.875.875" transform="translate(0.75 0.75)" fill="none" stroke="var(--shkrn-var-color-light-dark-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
        </svg>
    </a>
    <?php } elseif ($place == 'mobile') { ?>
        <a href="#" aria-label="Theme mode changer" class="moon-icon-shkrn shkrn-mode-changer-btn flex-center header-icon-shkrn" onclick="set_lightmode_shkrn()">
            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 30.46 30.46">
                <path id="Path_12" data-name="Path 12" d="M0,0H30.46V30.46H0Z" fill="none"/>
                <path id="Path_13" data-name="Path 13" d="M14.422,3h.5A9.519,9.519,0,0,0,24.973,18.8,11.423,11.423,0,1,1,14.422,2.992Z" transform="translate(0.808 0.805)" fill="none" stroke="var(--shkrn-var-color-light-dark-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
            </svg>
            <span class="shkrn-darkmode-btn-text"></span>
        </a>
        <a href="#" aria-label="Theme mode changer" class="system-icon-shkrn shkrn-mode-changer-btn flex-center header-icon-shkrn" onclick="set_darkmode_shkrn()">
            <svg  xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--shkrn-var-color-light-dark-text)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <circle cx="12" cy="12" r="9"></circle>
                <line x1="12" y1="3" x2="12" y2="21"></line>
                <line x1="12" y1="9" x2="16.65" y2="4.35"></line>
                <line x1="12" y1="14.3" x2="19.37" y2="6.93"></line>
                <line x1="12" y1="19.6" x2="20.85" y2="10.75"></line>
            </svg>
            <span class="shkrn-darkmode-btn-text"></span>
        </a>
        <a href="#" aria-label="Theme mode changer" class="sun-icon-shkrn shkrn-mode-changer-btn flex-center header-icon-shkrn" onclick="set_automode_shkrn()">
            <svg id="sun-icon-shkrn" xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 30 30">
                <path id="Path_14" data-name="Path 14" d="M0,0H30V30H0Z" fill="none"/>
                <circle id="Ellipse_3" data-name="Ellipse 3" cx="5" cy="5" r="5" transform="translate(10.46 9.54)" fill="none" stroke="var(--shkrn-var-color-light-dark-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                <path id="Path_15" data-name="Path 15" d="M3,14.25H4.25M14.25,3V4.25m10,10H25.5m-11.25,10V25.5m-8-19.25.875.875M22.25,6.25l-.875.875m0,14.25.875.875M7.125,21.375l-.875.875" transform="translate(0.75 0.75)" fill="none" stroke="var(--shkrn-var-color-light-dark-text)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
            </svg>
            <span class="shkrn-darkmode-btn-text"></span>
        </a>
    <?php } }
}
function add_dark_mode_class_first($classes){
    if (shkrn_options('show_darkmode_shokrino')) {
        if (isset($_COOKIE['shkrnLightMode'])) {
            $classes[] = 'light-mode-shkrn';
        } elseif (isset($_COOKIE['shkrnDarkMode'])) {
            $classes[] = 'dark-mode-shkrn';
        } elseif (isset($_COOKIE['shkrnSystemMode'])) {
            $classes[] = 'system-mode-shkrn';
        } else {
            $classes[] = 'system-mode-shkrn';
        }
    } else {
        $classes[] = 'light-mode-shkrn';
    }
    return $classes;
}
add_filter('body_class','add_dark_mode_class_first');
