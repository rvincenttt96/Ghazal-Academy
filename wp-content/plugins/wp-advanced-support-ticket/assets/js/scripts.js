/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD (Register as an anonymous module)
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var pluses = /\+/g;

    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }

    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            // If we can't parse the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
            return config.json ? JSON.parse(s) : s;
        } catch (e) {
        }
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }

    var config = $.cookie = function (key, value, options) {

        // Write

        if (arguments.length > 1 && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
            }

            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }

        // Read

        var result = key ? undefined : {},
            // To prevent the for loop in the first place assign an empty array
            // in case there are no cookies at all. Also prevents odd result when
            // calling $.cookie().
            cookies = document.cookie ? document.cookie.split('; ') : [],
            i = 0,
            l = cookies.length;

        for (; i < l; i++) {
            var parts = cookies[i].split('='),
                name = decode(parts.shift()),
                cookie = parts.join('=');

            if (key === name) {
                // If second argument (value) is a function it's a converter...
                result = read(cookie, value);
                break;
            }

            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        // Must not alter options, thus extending a fresh object...
        $.cookie(key, '', $.extend({}, options, {expires: -1}));
        return !$.cookie(key);
    };

}));

// Sticky Sidebar
(function ($) {
    $.fn.StickySidebar = function (options) {
        var defaults = {
            'containerSelector': '',
            'additionalMarginTop': 0,
            'additionalMarginBottom': 0,
            'updateSidebarHeight': true,
            'minWidth': 0,
            'disableOnResponsiveLayouts': true,
            'sidebarBehavior': 'modern'
        };
        options = $.extend(defaults, options);

        // Validate options
        options.additionalMarginTop = parseInt(options.additionalMarginTop) || 0;
        options.additionalMarginBottom = parseInt(options.additionalMarginBottom) || 0;

        tryInitOrHookIntoEvents(options, this);

        // Try doing init, otherwise hook into window.resize and document.scroll and try again then.
        function tryInitOrHookIntoEvents(options, $that) {
            var success = tryInit(options, $that);

            if (!success) {
                console.log('TST: Body width smaller than options.minWidth. Init is delayed.');

                $(document).scroll(function (options, $that) {
                    return function (evt) {
                        var success = tryInit(options, $that);

                        if (success) {
                            $(this).unbind(evt);
                        }
                    };
                }(options, $that));
                $(window).resize(function (options, $that) {
                    return function (evt) {
                        var success = tryInit(options, $that);

                        if (success) {
                            $(this).unbind(evt);
                        }
                    };
                }(options, $that))
            }
        }

        // Try doing init if proper conditions are met.
        function tryInit(options, $that) {
            if (options.initialized === true) {
                return true;
            }

            if ($('body').width() < options.minWidth) {
                return false;
            }

            init(options, $that);

            return true;
        }

        // Init the sticky sidebar(s).
        function init(options, $that) {
            options.initialized = true;

            // Add CSS
            $('head').append($('<style>.StickySidebar:after {content: ""; display: table; clear: both;}</style>'));

            $that.each(function () {
                var o = {};

                o.sidebar = $(this);

                // Save options
                o.options = options || {};

                // Get container
                o.container = $(o.options.containerSelector);
                if (o.container.length == 0) {
                    o.container = o.sidebar.parent();
                }

                // Create sticky sidebar
                o.sidebar.parents().css('-webkit-transform', 'none'); // Fix for WebKit bug - https://code.google.com/p/chromium/issues/detail?id=20574
                o.sidebar.css({
                    'position': 'relative',
                    'overflow': 'visible',
                    // The "box-sizing" must be set to "content-box" because we set a fixed height to this element when the sticky sidebar has a fixed position.
                    '-webkit-box-sizing': 'border-box',
                    '-moz-box-sizing': 'border-box',
                    'box-sizing': 'border-box'
                });

                // Get the sticky sidebar element. If none has been found, then create one.
                o.stickySidebar = o.sidebar.find('.StickySidebar');
                if (o.stickySidebar.length == 0) {
                    o.sidebar.find('script').remove(); // Remove <script> tags, otherwise they will be run again on the next line.
                    o.stickySidebar = $('<div>').addClass('StickySidebar').append(o.sidebar.children());
                    o.sidebar.append(o.stickySidebar);
                }

                // Get existing top and bottom margins and paddings
                o.marginTop = parseInt(o.sidebar.css('margin-top'));
                o.marginBottom = parseInt(o.sidebar.css('margin-bottom'));
                o.paddingTop = parseInt(o.sidebar.css('padding-top'));
                o.paddingBottom = parseInt(o.sidebar.css('padding-bottom'));

                // Add a temporary padding rule to check for collapsable margins.
                var collapsedTopHeight = o.stickySidebar.offset().top;
                var collapsedBottomHeight = o.stickySidebar.outerHeight();
                o.stickySidebar.css('padding-top', 1);
                o.stickySidebar.css('padding-bottom', 1);
                collapsedTopHeight -= o.stickySidebar.offset().top;
                collapsedBottomHeight = o.stickySidebar.outerHeight() - collapsedBottomHeight - collapsedTopHeight;
                if (collapsedTopHeight == 0) {
                    o.stickySidebar.css('padding-top', 0);
                    o.stickySidebarPaddingTop = 0;
                } else {
                    o.stickySidebarPaddingTop = 1;
                }

                if (collapsedBottomHeight == 0) {
                    o.stickySidebar.css('padding-bottom', 0);
                    o.stickySidebarPaddingBottom = 0;
                } else {
                    o.stickySidebarPaddingBottom = 1;
                }

                // We use this to know whether the user is scrolling up or down.
                o.previousScrollTop = null;

                // Scroll top (value) when the sidebar has fixed position.
                o.fixedScrollTop = 0;

                // Set sidebar to default values.
                resetSidebar();

                o.onScroll = function (o) {
                    // Stop if the sidebar isn't visible.
                    if (!o.stickySidebar.is(":visible")) {
                        return;
                    }

                    // Stop if the window is too small.
                    if ($('body').width() < o.options.minWidth) {
                        resetSidebar();
                        return;
                    }

                    // Stop if the sidebar width is larger than the container width (e.g. the theme is responsive and the sidebar is now below the content)
                    if (o.options.disableOnResponsiveLayouts) {
                        var sidebarWidth = o.sidebar.outerWidth(o.sidebar.css('float') == 'none');

                        if (sidebarWidth + 50 > o.container.width()) {
                            resetSidebar();
                            return;
                        }
                    }

                    var scrollTop = $(document).scrollTop();
                    var position = 'static';

                    // If the user has scrolled down enough for the sidebar to be clipped at the top, then we can consider changing its position.
                    if (scrollTop >= o.container.offset().top + (o.paddingTop + o.marginTop - o.options.additionalMarginTop)) {
                        // The top and bottom offsets, used in various calculations.
                        var offsetTop = o.paddingTop + o.marginTop + options.additionalMarginTop;
                        var offsetBottom = o.paddingBottom + o.marginBottom + options.additionalMarginBottom;

                        // All top and bottom positions are relative to the window, not to the parent elemnts.
                        var containerTop = o.container.offset().top;
                        var containerBottom = o.container.offset().top + getClearedHeight(o.container);

                        // The top and bottom offsets relative to the window screen top (zero) and bottom (window height).
                        var windowOffsetTop = 0 + options.additionalMarginTop;
                        var windowOffsetBottom;

                        var sidebarSmallerThanWindow = (o.stickySidebar.outerHeight() + offsetTop + offsetBottom) < $(window).height();
                        if (sidebarSmallerThanWindow) {
                            windowOffsetBottom = windowOffsetTop + o.stickySidebar.outerHeight();
                        } else {
                            windowOffsetBottom = $(window).height() - o.marginBottom - o.paddingBottom - options.additionalMarginBottom;
                        }

                        var staticLimitTop = containerTop - scrollTop + o.paddingTop + o.marginTop;
                        var staticLimitBottom = containerBottom - scrollTop - o.paddingBottom - o.marginBottom;

                        var top = o.stickySidebar.offset().top - scrollTop;
                        var scrollTopDiff = o.previousScrollTop - scrollTop;

                        // If the sidebar position is fixed, then it won't move up or down by itself. So, we manually adjust the top coordinate.
                        if (o.stickySidebar.css('position') == 'fixed') {
                            if (o.options.sidebarBehavior == 'modern') {
                                top += scrollTopDiff;
                            }
                        }

                        if (o.options.sidebarBehavior == 'stick-to-top') {
                            top = options.additionalMarginTop;
                        }

                        if (o.options.sidebarBehavior == 'stick-to-bottom') {
                            top = windowOffsetBottom - o.stickySidebar.outerHeight();
                        }

                        if (scrollTopDiff > 0) { // If the user is scrolling up.
                            top = Math.min(top, windowOffsetTop);
                        } else { // If the user is scrolling down.
                            top = Math.max(top, windowOffsetBottom - o.stickySidebar.outerHeight());
                        }

                        top = Math.max(top, staticLimitTop);

                        top = Math.min(top, staticLimitBottom - o.stickySidebar.outerHeight());

                        // If the sidebar is the same height as the container, we won't use fixed positioning.
                        var sidebarSameHeightAsContainer = o.container.height() == o.stickySidebar.outerHeight();

                        if (!sidebarSameHeightAsContainer && top == windowOffsetTop) {
                            position = 'fixed';
                        } else if (!sidebarSameHeightAsContainer && top == windowOffsetBottom - o.stickySidebar.outerHeight()) {
                            position = 'fixed';
                        } else if (scrollTop + top - o.sidebar.offset().top - o.paddingTop <= options.additionalMarginTop) {
                            // Stuck to the top of the page. No special behavior.
                            position = 'static';
                        } else {
                            // Stuck to the bottom of the page.
                            position = 'absolute';
                        }
                    }

                    /*
                     * Performance notice: It's OK to set these CSS values at each resize/scroll, even if they don't change.
                     * It's way slower to first check if the values have changed.
                     */
                    if (position == 'fixed') {
                        o.stickySidebar.css({
                            'position': 'fixed',
                            'width': o.sidebar.width(),
                            'top': top,
                            'left': o.sidebar.offset().left + parseInt(o.sidebar.css('padding-left'))
                        });
                    } else if (position == 'absolute') {
                        var css = {};

                        if (o.stickySidebar.css('position') != 'absolute') {
                            css.position = 'absolute';
                            css.top = scrollTop + top - o.sidebar.offset().top - o.stickySidebarPaddingTop - o.stickySidebarPaddingBottom;
                        }

                        css.width = o.sidebar.width();
                        css.left = '';

                        o.stickySidebar.css(css);
                    } else if (position == 'static') {
                        resetSidebar();
                    }

                    if (position != 'static') {
                        if (o.options.updateSidebarHeight == true) {
                            o.sidebar.css({
                                'min-height': o.stickySidebar.outerHeight() + o.stickySidebar.offset().top - o.sidebar.offset().top + o.paddingBottom
                            });
                        }
                    }

                    o.previousScrollTop = scrollTop;
                };

                // Initialize the sidebar's position.
                o.onScroll(o);

                // Recalculate the sidebar's position on every scroll and resize.
                $(document).scroll(function (o) {
                    return function () {
                        o.onScroll(o);
                    };
                }(o));
                $(window).resize(function (o) {
                    return function () {
                        o.stickySidebar.css({'position': 'static'});
                        o.onScroll(o);
                    };
                }(o));

                // Reset the sidebar to its default state
                function resetSidebar() {
                    o.fixedScrollTop = 0;
                    o.sidebar.css({
                        'min-height': '1px'
                    });
                    o.stickySidebar.css({
                        'position': 'static',
                        'width': ''
                    });
                }

                // Get the height of a div as if its floated children were cleared. Note that this function fails if the floats are more than one level deep.
                function getClearedHeight(e) {
                    var height = e.height();

                    e.children().each(function () {
                        height = Math.max(height, $(this).height());
                    });

                    return height;
                }
            });
        }
    }
})(jQuery);

/*
	jTippy
	https://github.com/HTMLGuyLLC/jTippy
	Made with love by HTMLGuy, LLC
	https://htmlguy.com
	MIT Licensed
*/
(function(a){a.fn.jTippy=function(b){if(1<this.length)return this.each(function(){a(this).jTippy(b)}),this;if("undefined"==typeof this||1!==this.length)return!1;const c=a(this);b=a.extend({},a.jTippy.defaults,b,c.data());let d=c.attr("title");"undefined"!=typeof d&&d.length&&(b.title=d),b.class+=" jt-"+b.theme+"-theme",b.class+=" jt-"+b.size,"click"!==b.trigger&&(b.backdrop=!1),b.trigger=b.trigger.toLowerCase().trim();let f={dom:this,dom_wrapped:c,position_debug:b.position_debug,trigger:b.trigger,title:b.title,content:b.title,theme:b.theme,class:b.class,backdrop:b.backdrop,position:b.position,close_on_outside_click:b.close_on_outside_click,singleton:b.singleton,dataAttr:"jTippy",createTooltipHTML:function(){return`<div class='jtippy ${f.class}' role='tooltip'><div class='jt-arrow'></div><div class='jt-title'>${f.content}</div></div>`},createBackdropHTML:function(){return!!f.backdrop&&`<div class='jt-backdrop jt-${f.backdrop}-backdrop'></div>`},destroy:function(){const b=f.dom_wrapped.data(f.dataAttr);"undefined"!=typeof b&&null!==b&&("click"===b.trigger?(b.dom_wrapped.off("touchstart mousedown",b.toggleTooltipHandler),b.dom_wrapped.off("click",b.preventDefaultHandler)):"focus"===b.trigger?(b.dom_wrapped.off("touchstart focus",b.show),b.dom_wrapped.off("touchend blur",b.hide)):"hover"===b.trigger?(b.dom_wrapped.off("touchstart mouseenter",b.show),b.dom_wrapped.off("touchend mouseleave",b.hide)):"hoverfocus"===b.trigger&&(b.dom_wrapped.off("focus",b.hoverfocusFocusShow),b.dom_wrapped.off("blur",b.hoverfocusBlur),b.dom_wrapped.off("touchstart mouseenter",b.show),b.dom_wrapped.off("touchend mouseleave",b.hoverfocusHide)),a(window).off("resize",b.onResize),b.isVisible()&&b.hide(),b.dom_wrapped.data(b.dataAttr,null))},initialize:function(){return"click"===f.trigger?(f.dom_wrapped.on("touchstart mousedown",f.toggleTooltipHandler),f.dom_wrapped.on("click",f.preventDefaultHandler)):"focus"===f.trigger?(f.dom_wrapped.on("touchstart focus",f.show),f.dom_wrapped.on("touchend blur",f.hide)):"hover"===f.trigger?(f.dom_wrapped.on("touchstart mouseenter",f.show),f.dom_wrapped.on("touchend mouseleave",f.hide)):"hoverfocus"===f.trigger&&(f.dom_wrapped.on("focus",f.hoverfocusFocusShow),f.dom_wrapped.on("blur",f.hoverfocusBlur),f.dom_wrapped.on("touchstart mouseenter",f.show),f.dom_wrapped.on("touchend mouseleave",f.hoverfocusHide)),a.jTippy.body_click_initialized||(a(document).on("touchstart mousedown",f.onClickOutside),a.jTippy.bodyClickInitialized=!0),f.dom_wrapped.data(f.dataAttr,f),f.dom},hoverfocusFocusShow:function(){f.dom_wrapped.addClass("jt-focused"),f.show()},hoverfocusBlur:function(){f.dom_wrapped&&f.dom_wrapped.length&&f.dom_wrapped.removeClass("jt-focused"),f.hide()},hoverfocusHide:function(){return!f.dom_wrapped.hasClass("jt-focused")&&void f.hide()},preventDefaultHandler:function(a){return a.preventDefault(),!1},toggleTooltipHandler:function(a){return a.preventDefault(),f.isVisible()&&f.hide()||f.show(),!1},show:function(b){if(f.isVisible())return!1;f.singleton&&f.hideAllVisible();const c=a("body");"blurred"===f.backdrop?c.addClass("jt-blurred-body"):f.backdrop&&c.append(f.createBackdropHTML()),("undefined"==typeof b||b)&&"function"==typeof f.title&&(f.content=f.title(f.dom_wrapped,f)),c.append(f.createTooltipHTML()),f.tooltip=a(".jtippy:last"),f.positionTooltip(),a(window).on("resize",f.onResize);const d="jTippy"+Date.now();f.tooltip.attr("id",d),f.dom.attr("aria-describedby",d),a.jTippy.visible.push(f),("undefined"==typeof b||b)&&f.dom.trigger("jt-show",[f.tooltip,f.hide]),a("body").on("DOMSubtreeModified",f.positionTooltip)},isVisible:function(){return-1<a.inArray(f,a.jTippy.visible)},hideAllVisible:function(){return a.each(a.jTippy.visible,function(a,b){b.dom_wrapped.hasClass("jt-focused")||b.hide()}),this},hide:function(b){a("body").off("DOMSubtreeModified",f.positionTooltip),a(window).off("resize",f.onResize),f.dom.attr("aria-describedby",null),f.tooltip&&f.tooltip.length&&f.tooltip.remove(),"blurred"===f.backdrop?a("body").removeClass("jt-blurred-body"):f.backdrop&&a(".jt-backdrop").remove(),("undefined"==typeof b||b)&&f.dom.trigger("jt-hide"),"click"!==f.trigger&&f.dom_wrapped.off("touchstart mousedown",f.hide);var c=a.inArray(f,a.jTippy.visible);return a.jTippy.visible.splice(c,1),f.dom},onResize:function(){f.hide(!1),f.show(!1)},onClickOutside:function(b){const c=a(b.target);c.hasClass("jtippy")||c.parents(".jtippy:first").length||a.each(a.jTippy.visible,function(a,b){"undefined"!=typeof b&&b.close_on_outside_click&&(c!==b.dom_wrapped||"focus"!==b.trigger&&"hoverfocus"!==b.trigger)&&b.hide()})},positionTooltip:function(){f.positionDebug("-- Start positioning --"),f.dom_wrapped.length&&f.dom_wrapped.is(":visible")||(f.positionDebug("Elem no longer exists. Removing tooltip"),f.hide(!0));let a=f.tooltip.find(".jt-arrow"),[b,c,d,e,g,h]=f.calculateSafePosition(f.position);return("undefined"==typeof g&&"auto"!==f.position&&(f.positionDebug("Couldn't fit preferred position"),[b,c,d,e,g,h]=f.calculateSafePosition("auto")),"undefined"==typeof g)?(f.positionDebug("Doesn't appear to fit. Displaying centered"),f.tooltip.addClass("jt-centered").css({top:"50%",left:"50%","margin-left":-(d/2),"margin-top":-(e/2)}),a&&a.length&&a.remove(),void f.positionDebug("-- Done positioning --")):(f.positionDebug({"Setting Position":{Left:g,Top:h}}),f.tooltip.css("left",g),f.tooltip.css("top",h),60>c&&(f.positionDebug("Element is less than "+c+"px. Setting arrow to hug the side tighter"),b+=" jt-arrow-super-hug"),a.addClass("jt-arrow-"+b),f.positionDebug("-- Done positioning --"),f)},calculateSafePosition:function(a){let b=f.tooltip.find(".jt-arrow"),c=f.dom_wrapped.offset(),d=f.dom_wrapped.outerHeight(),e=f.dom_wrapped.outerWidth(),g=f.tooltip.outerWidth(),h=f.tooltip.outerHeight(),i=document.querySelector("body").offsetWidth,j=document.querySelector("body").offsetHeight,k=b.is(":visible")?b.outerHeight():0,l=b.is(":visible")?b.outerWidth():0,m={};m.below=5<j-(h+d+c.top),m.above=5<c.top-h,m.vertical_half=5<c.top+e/2-h/2,m.right=5<i-(g+e+c.left),m.right_half=5<i-c.left-e/2-g/2,m.right_full=5<i-c.left-g,m.left=5<c.left-g,m.left_half=5<c.left+e/2-g/2,m.left_full=5<c.left-g,f.positionDebug({"Clicked Element":{Left:c.left,Top:c.top}}),f.positionDebug({"Element Dimensions":{Height:d,Width:e},"Tooltip Dimensions":{Height:h,Width:g},"Window Dimensions":{Height:j,Width:i},"Arrow Dimensions":{Height:k,Width:l}}),f.positionDebug(m);let n,o,p;return("auto"===a||"bottom"===a)&&m.below&&m.left_half&&m.right_half?(f.positionDebug("Displaying below, centered"),n="top",o=c.left-g/2+e/2,p=c.top+d+k/2):("auto"===a||"top"===a)&&m.above&&m.left_half&&m.right_half?(f.positionDebug("Displaying above, centered"),n="bottom",o=c.left-g/2+e/2,p=c.top-h-k/2):("auto"===a||"left"===a)&&m.left&&m.vertical_half?(f.positionDebug("Displaying left, centered"),n="right",o=c.left-g-l/2,p=c.top+d/2-h/2):("auto"===a||"right"===a)&&m.right&&m.vertical_half?(f.positionDebug("Displaying right, centered"),n="left",o=c.left+e+l/2,p=c.top+d/2-h/2):("auto"===a||"bottom"===a)&&m.below&&m.right_full?(f.positionDebug("Displaying below, to the right"),n="top jt-arrow-hug-left",o=c.left,p=c.top+d+k/2):("auto"===a||"bottom"===a)&&m.below&&m.left_full?(f.positionDebug("Displaying below, to the left"),n="top jt-arrow-hug-right",o=c.left+e-g,p=c.top+d+k/2):("auto"===a||"top"===a)&&m.above&&m.right_full?(f.positionDebug("Displaying above, to the right"),n="bottom jt-arrow-hug-left",o=c.left,p=c.top-h-k/2):("auto"===a||"top"===a)&&m.above&&m.left_full&&(f.positionDebug("Displaying above, to the left"),n="bottom jt-arrow-hug-right",o=c.left+e-g,p=c.top-h-k/2),[n,e,g,h,o,p]},positionDebug:function(a){return!!f.position_debug&&("object"==typeof a?console.table(a):console.log(`Position: ${a}`))}};return f.destroy(),f.initialize()},a.jTippy={},a.jTippy.visible=[],a.jTippy.body_click_initialized=!1,a.jTippy.defaults={title:"",trigger:"hoverfocus",position:"auto",class:"",theme:"black",size:"small",backdrop:!1,singleton:!0,close_on_outside_click:!0}})(jQuery);

jQuery(document).ready(function ($) {
    let jTippy = $('[data-toggle="wpast-jTippy"]');

    if (jTippy.length) {
        jTippy.each(function () {
            let title = $(this).attr('title');
            $(this).jTippy({
                size: 'medium',
            }).on('jt-show', function (e, tooltip, hide) {
                $(this).attr('title', '');
            }).on('jt-hide', function (e) {
                $(this).attr('title', title);
            });
        });
    }

    $('.wpast-wrap .wpast-fags h5').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.next('p').slideToggle();
        $this.parentsUntil('.wpast-fags').siblings('li').find('p').slideUp();
        $this.parentsUntil('.wpast-fags').siblings('li').removeClass('wpast-collapse');
        $this.parentsUntil('.wpast-fags').toggleClass('wpast-collapse');
    });

    $('.wpast-wrap .wpast-not-found-answer').click(function (e) {
        e.preventDefault();
        $('.wpast-wrap .wpast-step-1').slideDown();
    });

    $('.wpast-wrap #wpast-parent-department').change(function () {
        var $this = $(this);
        var $value = $this.val();
        if ($value === '') {
            $('.wpast-wrap .wpast-child-department').hide();
            $('.wpast-wrap .wpast-child-department-0').show();
            if ($('#wpast-submit-ticket').hasClass('multi-step')) {
                $('.wpast-wrap .wpast-step-2').slideUp();
            }
            return false;
        }
        $('.wpast-wrap .wpast-child-department').hide();
        $('.wpast-wrap .wpast-child-department-' + $value).show().prop("selectedIndex", 0);
        $('.wpast-wrap .wpast-description-wrapper').slideUp();
        if ($('#wpast-submit-ticket').hasClass('multi-step')) {
            $('.wpast-wrap .wpast-step-2').slideUp();
        }
    });

    $('.wpast-wrap #wpast-child-department').change(function () {
        var $this = $(this);
        var $value = $this.val();
        if ($('#wpast-submit-ticket').hasClass('multi-step')) {
            if ($value === '') {
                $('.wpast-wrap .wpast-step-2').slideUp();
                $('.wpast-wrap .wpast-description-wrapper').slideUp();
                return false;
            }
        }
        var $selected = $this.children("option:selected");
        var $support = $selected.data('support');
        var $vendorSubmit = $selected.data('vendor-submit');
        var $block = $selected.data('block');
        var $description = $selected.data('description');
        var $fields = $selected.data('fields');
        if ($description !== '' && $value !== '') {
            $('.wpast-wrap .wpast-description-wrapper').slideDown();
            $('.wpast-wrap .wpast-description').text($description);
        } else {
            $('.wpast-wrap .wpast-description-wrapper').slideUp();
        }
        if (!$support) {
            $('.wpast-wrap .wpast-product-wrapper').hide();
            $('.wpast-wrap .wpast-product-wrapper input[name="product"]').val('');
            $('.wpast-wrap .wpast-order-wrapper').hide();
            $('.wpast-wrap .wpast-order-wrapper input[name="order_id"]').val('');
        } else {
            $('.wpast-wrap .wpast-product-wrapper').show();
            $('.wpast-wrap .wpast-order-wrapper').show();
        }
        if (!$vendorSubmit) {
            $('.wpast-wrap .wpast-user-wrapper').hide();
            $('.wpast-wrap .wpast-user-wrapper input[name="user"]').val('');
        } else {
            $('.wpast-wrap .wpast-user-wrapper').show();
        }
        if (!$block) {
            $('.wpast-wrap .wpast-step-2').slideDown();
        } else {
            $('.wpast-wrap .wpast-step-2').slideUp();
        }

        $fields = $fields.toString();
        if ($fields) {
            if (!$fields.includes(",")) {
                $fields = [$fields];
            } else {
                $fields = $fields.split(",");
            }
            for (var i = 0; i < $fields.length; i++) {
                $('.wpast-field-wrapper:not(.wpast-field-' + $fields[i] + ')').hide();
            }
            for (var i = 0; i < $fields.length; i++) {
                $('.wpast-field-wrapper.wpast-field-' + $fields[i]).show();
            }
            $('.wpast-fields-wrapper').slideDown();
        } else {
            $('.wpast-fields-wrapper').slideUp();
        }
    });

    var nonce = $('meta[name="wpast-nonce"]').attr('content');
    var ajaxResults = $('.wpast-product-wrapper .wpast-ajax-results');
    var loader = $('.wpast-product-wrapper .wpast-loader');
    var $continue = true;
    $('.wpast-wrap #wpast-product').on('input', function () {
        var $this = $(this);
        var searchTerm = $this.val();
        if (searchTerm.length < 2) {
            $continue = false;
            ajaxResults.fadeOut();
            loader.fadeOut();
            return false;
        } else {
            $continue = true;
        }
        loader.fadeIn();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast__search_product',
                searchTerm: searchTerm,
                nonce: nonce
            },
            success: function (response) {
                if (response.__success === true && $continue) {
                    ajaxResults.html(response.results).fadeIn();
                } else {
                    ajaxResults.empty().hide();
                }
                loader.fadeOut();
            },
            error: function () {
                ajaxResults.fadeOut();
                loader.fadeOut();
            }
        });
    });
    $(window).click(function () {
        ajaxResults.empty().hide();
        loader.fadeOut();
    });
    $(document).on('click', '.wpast-product-wrapper .wpast-ajax-results li', function (e) {
        e.preventDefault();
        var $this = $(this);
        var productID = parseInt($this.data('id'));
        if (!productID) {
            return false;
        }
        $('.wpast-product-wrapper #wpast-product').val($this.text());
        $('.wpast-product-wrapper input[name="product"]').val(productID);
        $('.wpast-product-wrapper .wpast-ajax-results').fadeOut();
    });
    $('body').click(function () {
        setTimeout(function () {
            ajaxResults.empty().hide();
            loader.fadeOut();
        }, 500);
    });

    var userAjaxResults = $('.wpast-user-wrapper .wpast-ajax-results');
    var userLoader = $('.wpast-user-wrapper .wpast-loader');
    var userContinue = true;
    $('.wpast-wrap #wpast-user').on('input', function () {
        var $this = $(this);
        var searchTerm = $this.val();
        if (searchTerm.length < 2) {
            userContinue = false;
            userAjaxResults.fadeOut();
            userLoader.fadeOut();
            return false;
        } else {
            userContinue = true;
        }
        userLoader.fadeIn();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast__search_user',
                searchTerm: searchTerm,
                nonce: nonce
            },
            success: function (response) {
                if (response.__success === true && userContinue) {
                    userAjaxResults.html(response.results).fadeIn();
                } else {
                    userAjaxResults.empty().hide();
                }
                userLoader.fadeOut();
            },
            error: function () {
                userAjaxResults.fadeOut();
                userLoader.fadeOut();
            }
        });
    });
    $(window).click(function () {
        userAjaxResults.empty().hide();
        userLoader.fadeOut();
    });
    $(document).on('click', '.wpast-user-wrapper .wpast-ajax-results li', function (e) {
        e.preventDefault();
        var $this = $(this);
        var userID = parseInt($this.data('id'));
        if (!userID) {
            return false;
        }
        $('.wpast-user-wrapper #wpast-user').val($this.text());
        $('.wpast-user-wrapper input[name="user"]').val(userID);
        $('.wpast-user-wrapper .wpast-ajax-results').fadeOut();
    });
    $('body').click(function () {
        setTimeout(function () {
            userAjaxResults.empty().hide();
            userLoader.fadeOut();
        }, 500);
    });

    $('.wpast-wrap #wpast-file').change(function () {
        var $this = $(this);
        var file = $this[0].files[0];
        $this.next('.wpast-file-name').text(file['name']);
    });

    $('.wpast-wrap #wpast-submit-ticket').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var $submit = $this.find('.wpast-submit');
        var $loader = $this.find('.wpast-loader');
        $submit.prop('disabled', true);
        $loader.show();
        if (WPAST_DATA.advanced_editor) {
            tinyMCE.triggerSave();
        }

        var formData = new FormData();
        formData.append('action', 'wpast_submit_ticket');
        formData.append('nonce', nonce);
        $this.find('input[name], select[name]:visible, textarea[name]').each(function () {
            var $value = $(this).val();
            var $name = $(this).attr('name');
            if ($value !== undefined) {
                if ($(this).attr('type') === 'checkbox' || $(this).attr('type') === 'radio') {
                    if ($(this).is(':checked')) {
                        formData.append($name, $value);
                    }
                } else {
                    formData.append($name, $value);
                }
            }
        });
        if ($('#wpast-file').length) {
            var fileData = $this.find('#wpast-file').prop('files')[0];
            formData.append('file', fileData);
        }

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            contentType: false,
            processData: false,
            timeout: 0,
            data: formData,
            success: function (response) {
                if (response.__success === true) {
                    $this.trigger("reset");
                    $('.wpast-timer #hour, .wpast-timer #minute, .wpast-timer #seconds').text('00');
                    $('#wpast-recordings-list .wpast-row').html('');
                    if ($('#g-recaptcha-response').length) grecaptcha.reset();
                    let uppyFile = $('.wpast-wrap .wpast-ath-file');
                    if (uppyFile.length) {
                        uppyFile.remove();
                        uppy.reset();
                    }
                    //$('.wpast-wrap #wpast-file').val('');
                    //$('.wpast-upload .wpast-file-name').text('');
                    if ($('#wpast-submit-ticket').hasClass('multi-step')) {
                        $('.wpast-wrap .wpast-step-2').slideUp();
                    }

                    if (WPAST_DATA.auto_redirect) {
                        window.location.href = response.results;
                    } else {
                        var $options = {
                            icon: 'success',
                            title: WPAST_DATA.ticket_submitted,
                            footer: '<a href="' + response.results + '">' + WPAST_DATA.view_ticket + '</a>',
                            confirmButtonText: WPAST_DATA.ok,
                        };
                        if (!WPAST_DATA.user_logged) {
                            $options.html = WPAST_DATA.token + ": <span id='selectable' onclick=\"selectText('selectable')\">" + response.token + '</span>'
                        }
                        Swal.fire($options);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: WPAST_DATA.error_occurred,
                        html: response.results.toString().replace(/,/g, "<br>"),
                        confirmButtonText: WPAST_DATA.ok,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.error_occurred,
                    confirmButtonText: WPAST_DATA.ok,
                });
            },
            complete: function (data) {
                $submit.prop('disabled', false);
                $loader.hide();
            }
        });
    });

    $('.wpast-token').click(function (e) {
        $(this).select();
    });

    $('.wpast-reply-toggle a:nth-child(1)').click(function (e) {
        e.preventDefault();
        $('.wpast-wrap #wpast-submit-ticket-reply').slideToggle();
    });

    $('.wpast-reply-toggle a:nth-child(2)').click(function (e) {
        e.preventDefault();

        if(!confirm(WPAST_DATA.you_sure_cancel)) {
            return false;
        }

        var $this = $(this);
        var ticketID = parseInt($this.data('id'));

        if(!ticketID) {
            return false;
        }

        var $loader = $this.find('.wpast-loader');
        $loader.show();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_cancel_ticket',
                ticketID,
                nonce
            },
            success: function (response) {
                if (response.__success === true) {
                    $('.wpast-replies').html(response.replies_html);
                    $('.wpast-widget-status .wpast-status').remove();
                    $('.wpast-widget-status').prepend(response.__status);

                    Swal.fire({
                        icon: 'success',
                        title: WPAST_DATA.ticket_canceled,
                        confirmButtonText: WPAST_DATA.ok,
                    });

                    $this.remove();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: WPAST_DATA.error_occurred,
                        confirmButtonText: WPAST_DATA.ok,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.error_occurred,
                    confirmButtonText: WPAST_DATA.ok,
                });
            },
            complete: function (data) {
                $loader.hide();
            }
        });
    });

    $('#wpast-sidebar-toggle').click(function (e) {
        e.preventDefault();
        var wrapper = $('.wpast-wrap.wpast-view-ticket');
        wrapper.toggleClass('wpast-show-sibebar');
        if (!wrapper.hasClass('wpast-show-sibebar')) {
            $.cookie('wpast-hide-sidebar', 1, {expires: 30, path: '/'});
        } else {
            $.removeCookie('wpast-hide-sidebar', {path: '/'});
        }
    });

    $('.wpast-replies-title').click(function (e) {
        e.preventDefault();
        var replies = $('.wpast-replies');
        replies.children().each(function (i, li) {
            replies.prepend(li)
        })
    });

    $('.wpast-wrap #wpast-submit-ticket-reply').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var $submit = $this.find('.wpast-submit');
        var $loader = $this.find('.wpast-loader');
        $submit.prop('disabled', true);
        $loader.show();
        if (WPAST_DATA.advanced_editor) {
            tinyMCE.triggerSave();
        }

        var formData = new FormData();
        formData.append('action', 'wpast_submit_reply');
        formData.append('nonce', nonce);
        $this.find('input[name], select[name]:visible, textarea[name]').each(function () {
            var $value = $(this).val();
            var $name = $(this).attr('name');
            if ($value !== undefined) {
                if ($(this).attr('type') === 'checkbox') {
                    if ($(this).is(':checked')) {
                        formData.append($name, $value);
                    }
                } else {
                    formData.append($name, $value);
                }
            }
        });
        if ($('#wpast-file').length) {
            var fileData = $this.find('#wpast-file').prop('files')[0];
            formData.append('file', fileData);
        }

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            contentType: false,
            processData: false,
            timeout: 0,
            data: formData,
            success: function (response) {
                if (response.__success === true) {
                    $this.trigger("reset");
                    $('.wpast-timer #hour, .wpast-timer #minute, .wpast-timer #seconds').text('00');
                    $('#wpast-recordings-list .wpast-row').html('');
                    let uppyFile = $('.wpast-wrap .wpast-ath-file');
                    if (uppyFile.length) {
                        uppyFile.remove();
                        uppy.reset();
                    }
                    //$('.wpast-wrap #wpast-file').val('');
                    //$('.wpast-upload .wpast-file-name').text('');
                    $('.wpast-replies').html(response.replies_html);
                    $('.wpast-widget-status .wpast-status').remove();
                    $('.wpast-widget-status').prepend(response.__status);
                    Swal.fire({
                        icon: 'success',
                        title: WPAST_DATA.ticket_submitted,
                        confirmButtonText: WPAST_DATA.ok,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: WPAST_DATA.error_occurred,
                        html: response.results.toString().replace(/,/g, "<br>"),
                        confirmButtonText: WPAST_DATA.ok,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.error_occurred,
                    confirmButtonText: WPAST_DATA.ok,
                });
            },
            complete: function (data) {
                $submit.prop('disabled', false);
                $loader.hide();
            }
        });
    });

    /*if ($('.wpast-support-icon').length) {
        tippy('.wpast-support-icon', {
            animation: 'scale',
            duration: 250,
            arrow: false,
            delay: [100, 150]
        });
    }*/

    $('.wpast-wrap #wpast-submit-rating').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var $submit = $this.find('.wpast-submit');
        var $loader = $this.find('.wpast-loader');
        $submit.prop('disabled', true);
        $loader.show();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_submit_rating',
                formData: $this.serialize(),
                nonce: nonce
            },
            success: function (response) {
                if (response.__success === true) {
                    $('.wpast-rating-widget').remove();
                    $('.wpast-widget-status').before(response.results);
                    Swal.fire({
                        icon: 'success',
                        title: WPAST_DATA.rating_registered,
                        confirmButtonText: WPAST_DATA.ok,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: WPAST_DATA.error_occurred,
                        confirmButtonText: WPAST_DATA.ok,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.error_occurred,
                    confirmButtonText: WPAST_DATA.ok,
                });
            },
            complete: function (data) {
                $submit.prop('disabled', false);
                $loader.hide();
            }
        });
    });

    $(document).on('click', '.wpast-extension', function (e) {
        e.preventDefault();
        var $this = $(this);
        var $loader = $this.find('.wpast-loader');
        var productID = parseInt($this.data('pid'));
        if (!productID) {
            return false;
        }
        $this.addClass('disabled');
        $loader.show();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_support_extension',
                productID: productID,
                nonce: nonce
            },
            success: function (response) {
                if (response.__success === true) {
                    Swal.fire({
                        icon: 'success',
                        showCloseButton: true,
                        showConfirmButton: false,
                        title: WPAST_DATA.extension_created,
                        footer: '<a href="' + response.cart_url + '">' + WPAST_DATA.go_cart + '</a>',
                        confirmButtonText: WPAST_DATA.ok,
                    });
                    setTimeout(function () {
                        window.location.href = response.cart_url;
                    }, 3000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: WPAST_DATA.error_occurred,
                        confirmButtonText: WPAST_DATA.ok,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.error_occurred,
                    confirmButtonText: WPAST_DATA.ok,
                });
            },
            complete: function (data) {
                $this.removeClass('disabled');
                $loader.hide();
            }
        });
    });

    $('.wpast-toggle-login').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var $toggle = $this.data('toggle');
        $this.parentsUntil('.wpast-col-12').hide();
        $('.' + $toggle).show();
        $('.' + $toggle).find('.wpast-login-footer').show();
    });

    $('.wpast-login-form').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var btn = $('.wpast-login');
        var loader = btn.find('.wpast-loader');

        if ($('#wpast-username').val() === '' || $('#wpast-password').val() === '') {
            Swal.fire({
                icon: 'error',
                title: WPAST_DATA.fill_all_fields,
                confirmButtonText: WPAST_DATA.ok,
            });
            return false;
        }

        btn.prop("disabled", true);
        loader.show();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_user_login',
                formData: $this.serialize(),
                nonce: nonce
            },
            success: function (response) {
                if (response._success === true) {
                    location.reload();
                } else {
                    if (response.msg) {
                        Swal.fire({
                            icon: 'error',
                            title: response.msg,
                            confirmButtonText: WPAST_DATA.ok,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: WPAST_DATA.error_occurred,
                            confirmButtonText: WPAST_DATA.ok,
                        });
                    }
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.error_occurred,
                    confirmButtonText: WPAST_DATA.ok,
                });
            },
            complete: function (data) {
                btn.prop("disabled", false);
                loader.hide();
            }
        });
    });

    $('.wpast-register-form').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var btn = $('.wpast-register');
        var loader = btn.find('.wpast-loader');

        if ($('#wpast-username2').val() === '' || ($('#wpast-email').length && $('#wpast-email').val() === '') || ($('#wpast-mobile').length && $('#wpast-mobile').val() === '') || $('#wpast-password2').val() === '') {
            Swal.fire({
                icon: 'error',
                title: WPAST_DATA.fill_all_fields,
                confirmButtonText: WPAST_DATA.ok,
            });
            return false;
        }

        if ($('#wpast-password2').val() !== $('#wpast-confirm-password').val()) {
            Swal.fire({
                icon: 'error',
                title: WPAST_DATA.password_not_match,
                confirmButtonText: WPAST_DATA.ok,
            });
            return false;
        }

        if ($('#wpast-email').length) {
            if (validateEmail($('#wpast-email').val()) === false) {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.incorrect_email,
                    confirmButtonText: WPAST_DATA.ok,
                });
                return false;
            }
        }

        if ($('#wpast-mobile').length) {
            if (validateMobile($('#wpast-mobile').val()) === false) {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.enter_valid_mobile,
                    confirmButtonText: WPAST_DATA.ok,
                });
                return false;
            }
        }

        btn.prop("disabled", true);
        loader.show();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_user_register',
                formData: $this.serialize(),
                nonce: nonce
            },
            success: function (response) {
                if (response._success === true) {
                    location.reload();
                } else {
                    if (response.msg) {
                        Swal.fire({
                            icon: 'error',
                            title: response.msg,
                            confirmButtonText: WPAST_DATA.ok,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: WPAST_DATA.error_occurred,
                            confirmButtonText: WPAST_DATA.ok,
                        });
                    }
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.error_occurred,
                    confirmButtonText: WPAST_DATA.ok,
                });
            },
            complete: function (data) {
                btn.prop("disabled", false);
                loader.hide();
            }
        });
    });

    $('#wpast-load-more-products').click(function (e) {
        e.preventDefault();
        let $this = $(this);
        let page = parseInt($this.attr('data-page'));
        let loader = $this.find('.wpast-loader');
        $this.prop("disabled", true);
        loader.show();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_load_more_products',
                page,
                nonce
            },
            success: function (response) {
                if (response._success === true) {
                    page = page + 1;
                    $this.attr('data-page', page);
                    $this.prev('ul').append(response.results);
                    if(response.results === '') $this.hide();
                }
            },
            error: function () {
            },
            complete: function (data) {
                $this.prop("disabled", false);
                loader.hide();
            }
        });
    });

    function validateEmail(mail) {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
            return true;
        }

        return false;
    }

    function validateMobile(mobile) {
        if (/^\d{11}$/.test(mobile)) {
            return true;
        }

        return false
    }

    let uppyArea = $('#wpast-uppy-area');
    if (uppyArea.length) {
        var uploaderWidth = WPAST_DATA.uploader_width;
        if ($(window).width() < 768 || !uploaderWidth) {
            uploaderWidth = 100;
        }
        var uploaderHeight = WPAST_DATA.uploader_height;
        uploaderHeight = uploaderHeight ? uploaderHeight : 350;
        var numberFiles = WPAST_DATA.number_files;
        numberFiles = numberFiles == 0 ? 99999 : numberFiles;

        var uppyOptions = {
            autoProceed: false,
            restrictions: {
                maxNumberOfFiles: numberFiles,
            }
        };
        if (WPAST_DATA.locale === 'fa_IR') uppyOptions.locale = Uppy.locales.fa_IR;
        if (WPAST_DATA.upload_auto_proceed) uppyOptions.autoProceed = true;
        if (WPAST_DATA.upload_max) uppyOptions.restrictions.maxFileSize = parseInt(WPAST_DATA.upload_max) * 1000000;
        if (WPAST_DATA._extensions) uppyOptions.restrictions.allowedFileTypes = JSON.parse(WPAST_DATA._extensions);
        var uppy = Uppy.Core(uppyOptions);
        uppy.use(Uppy.Dashboard,
            {
                target: '#wpast-uppy-area',
                inline: true,
                proudlyDisplayPoweredByUppy: true,
                width: uploaderWidth + '%',
                height: parseInt(uploaderHeight),
            }
        );
        uppy.use(Uppy.XHRUpload, {endpoint: WPAST_DATA.ajax_url + '?action=uppy_upload&nonce=' + nonce});

        uppy.on('complete', (result) => {
            let $result = result.successful;
            for (let i = 0; i < $result.length; i++) {
                uppyArea.after('<input type="hidden" class="wpast-ath-file" name="ath_file[]" value="' + $result[i]['uploadURL'] + '">');
            }
        });
    }

    $('.wpast-wrap #wpast-tracking-form').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var $submit = $this.find('.wpast-submit');
        var $loader = $this.find('.wpast-loader');
        var $token = $this.find('#wpast-token');
        if ($token.val() === '') {
            return false;
        }
        $submit.prop('disabled', true);
        $loader.show();

        $.ajax({
            url: WPAST_DATA.ajax_url,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_ticket_tracking',
                token: $token.val(),
                nonce: nonce
            },
            success: function (response) {
                if (response.__success === true) {
                    window.location.href = response.results;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: WPAST_DATA.error_occurred,
                        html: response.results.toString().replace(/,/g, "<br>"),
                        confirmButtonText: WPAST_DATA.ok,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: WPAST_DATA.error_occurred,
                    confirmButtonText: WPAST_DATA.ok,
                });
            },
            complete: function (data) {
                $submit.prop('disabled', false);
                $loader.hide();
            }
        });
    });

    $(function () {
        $('.wpast-sidabar').StickySidebar({
            additionalMarginTop: 40
        });
    });
});

function selectText(containerID) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerID));
        range.select();
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerID));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    }
}
