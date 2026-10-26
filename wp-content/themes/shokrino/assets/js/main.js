//cookies
function getCookie(e){let t=e+"=",n=document.cookie.split(";");for(let e=0;e<n.length;e++){let o=n[e];for(;" "==o.charAt(0);)o=o.substring(1);if(0==o.indexOf(t))return o.substring(t.length,o.length)}return""}function setCookie(e,t,n){const o=new Date;o.setTime(o.getTime()+24*n*60*60*1e3);let i="expires="+o.toUTCString();document.cookie=e+"="+t+";"+i+";path=/; SameSite=None; Secure"}
//darkmode
function body_shkrn( darkclass ) {
    var element = document.body;
    element.classList.remove("dark-mode-shkrn");
    element.classList.remove("light-mode-shkrn");
    element.classList.remove("system-mode-shkrn");
    element.classList.add(darkclass);
}
function cookie_shkrn( darkcookie ) {
    setCookie("shkrnLightMode","",-1);
    setCookie("shkrnDarkMode","",-1);
    setCookie("shkrnSystemMode","",-1);
    //set this cookie
    setCookie(darkcookie,"true",365);
}
function set_darkmode_shkrn() {
    body_shkrn("dark-mode-shkrn");
    cookie_shkrn("shkrnDarkMode");
}
function set_lightmode_shkrn() {
    body_shkrn("light-mode-shkrn");
    cookie_shkrn("shkrnLightMode");
}
function set_automode_shkrn() {
    body_shkrn("system-mode-shkrn");
    cookie_shkrn("shkrnSystemMode");
}
//mobile menu
function open_mobilemenu_shkrn() {
    const element = document.querySelector('.shkrn-mobile-menu-inside');
    element.style.transform = "translateX(0)";
    const elementBox = document.querySelector('.shkrn-mobile-menu-box');
    elementBox.style.visibility = "visible";
}
function close_mobile_menu_shkrn() {
    const element = document.querySelector('.shkrn-mobile-menu-inside');
    element.style.removeProperty('transform');
    const elementBox = document.querySelector('.shkrn-mobile-menu-box');
    elementBox.style.removeProperty('visibility');
}
//search box
function open_searchbox_shkrn() {
    const element = document.querySelector('.shkrn-header-search-box');
    element.style.visibility = 'visible';
    element.style.opacity = 1;
}
function close_searchbox_shkrn() {
    window.addEventListener('click', function(e){
        if (document.querySelector('.search-icon-shkrn').contains(e.target)) {
        } else {
            if (document.getElementById('shkrn-popup-form-search').contains(e.target)) {
            } else {
                const element = document.querySelector('.shkrn-header-search-box');
                element.style.removeProperty('visibility');
                element.style.removeProperty('opacity');
            }
        }
    });
}
//menu mobile submenu opener
$(document).ready(function () {
    $("#mobile-menu").click(function () {
        $(".shkrn-mobile-menu").slideToggle();
    }),
    $(".shkrn-mobile-menu ul.sub-menu").before(
        '<span class="openner-nav-shkrn"><svg class="menu-arrow fa-angle-down" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" fill="var(--shkrn-var-color-light-dark-text)" height="28px"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M119.5 326.9L3.5 209.1c-4.7-4.7-4.7-12.3 0-17l7.1-7.1c4.7-4.7 12.3-4.7 17 0L128 287.3l100.4-102.2c4.7-4.7 12.3-4.7 17 0l7.1 7.1c4.7 4.7 4.7 12.3 0 17L136.5 327c-4.7 4.6-12.3 4.6-17-.1z"></path></svg></span>'
    ),
    $(".shkrn-mobile-menu .openner-nav-shkrn").click(function () {
        $(this).next("ul.sub-menu").slideToggle();
        if( $(this).css('transform') == 'none' ) {
            $(this).css({'transform': 'rotate(' + 180 + 'deg)'})
        } else {
            $(this).css({'transform': ''})
        }
    });
})
// Read bar
readbarShokrinioElement = document.getElementById("read-bar-shkrn");
if (typeof(readbarShokrinioElement) != 'undefined' && readbarShokrinioElement != null) {
    let processScrollReadBarShkrn = () => {
        let docElem = document.documentElement,
            docBody = document.body,
            scrollTop = docElem['scrollTop'] || docBody['scrollTop'],
            scrollBottom = (docElem['scrollHeight'] || docBody['scrollHeight']) - window.innerHeight,
            scrollPercent = scrollTop / scrollBottom * 100 + '%';
        readbarShokrinioElement.style.setProperty("--scrollAmount", scrollPercent);
    }
    document.addEventListener('scroll', processScrollReadBarShkrn);
}
// Back to top Button (W3schools)

let backtotopShokrinioElement = document.getElementById("btn-back-to-top-shkrn");
if (typeof(backtotopShokrinioElement) != 'undefined' && backtotopShokrinioElement != null) {
    window.onscroll = function () {
        scrollFunctionShkrn();
    };

    function scrollFunctionShkrn() {
        if (
            document.body.scrollTop > 20 ||
            document.documentElement.scrollTop > 20
        ) {
            backtotopShokrinioElement.style.visibility = "visible";
            backtotopShokrinioElement.style.opacity = "1";
        } else {
            backtotopShokrinioElement.style.visibility = "hidden";
            backtotopShokrinioElement.style.opacity = "0";
        }
    }

    backtotopShokrinioElement.addEventListener("click", backToTopShkrn);

    function backToTopShkrn() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
}
//Scroll to page part product course
jQuery(window).scroll(function() {
    var windscroll = jQuery(window).scrollTop();
    if (windscroll >= 100) {
        jQuery('.anchor-list-on-product-shkrn').each(function(i) {
            // The number at the end of the next line is how pany pixels you from the top you want it to activate.
            if (jQuery(this).position().top <= windscroll + 20) {
                jQuery('#nav-wrapper-shkrn a.active').removeClass('active');
                jQuery('#nav-wrapper-shkrn a').eq(i).addClass('active');
            }
        });
    } else {
        jQuery('#nav-wrapper-shkrn a.active').removeClass('active');
        jQuery('#nav-wrapper-shkrn a:first').addClass('active');
    }
}).scroll();
//sliders swiper
var swiper = new Swiper("#home-page-post-slider-shkrn", {
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        600: {
            slidesPerView: 2,
        },
        1000: {
            slidesPerView: 4,
        }
    },
    spaceBetween: 10,
    loop: true,
    lazy: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    autoplay: {
        delay: 4000,
    },
    responsive: { 0: { items: 1 }, 600: { items: 1 }, 1000: { items: 1 } },
});