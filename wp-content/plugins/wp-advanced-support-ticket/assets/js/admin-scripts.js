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
                        o.stickySidebar.css({ 'position': 'static' });
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

jQuery(document).ready(function ($) {
    $(function () {
        $('.wpast-edit-ticket .postbox-container, .wpast-submit-ticket .postbox-container').StickySidebar({
            additionalMarginTop: 40
        });
    });

    $('.wpast-modal-dialog .wpast-modal-close, .wpast-modal-dialog .wpast-modal-backdrop').click(function () {
        $(this).closest('.wpast-modal-dialog').removeClass('show');
    });

    var menuItem = $('.wpast-menu-item a');
    menuItem.click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var $toggle = $this.data('toggle');
        $('.wpast-menu-item a').removeClass('wpast-active');
        $this.addClass('wpast-active');
        $('.wpast-settings-area').removeClass('wpast-active');
        $('.wpast-settings-' + $toggle).addClass('wpast-active');
    });

    $(".wpast-confirm").click(function () {
        var $this = $(this);
        var $toggle = $this.data('toggle');
        if (this.checked) {
            $('#wpast-' + $toggle).slideDown();
        } else {
            $('#wpast-' + $toggle).slideUp();
        }
    });
    $(".wpast-confirm").each(function () {
        var $this = $(this);
        if ($this.is(':checked')) {
            var $toggle = $this.data('toggle');
            $('#wpast-' + $toggle).show();
        }
    });

    var nonce = $('meta[name="wpast-nonce"]').attr('content');

    $('#wpast-settings-form').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        if (typeof tinyMCE !== 'undefined') tinyMCE.triggerSave();
        var button = $this.find('.wpast-save');
        var loader = button.find('.loader');
        button.prop('disabled', true);
        loader.show();

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_update_settings',
                nonce: nonce,
                formData: $this.serialize()
            },
            success: function (response) {
                if (response.result === true) {
                    UIkit.notification({
                        message: WPAST_DATA.settings_saved,
                        status: 'success',
                        pos: 'bottom-center',
                        timeout: 5000
                    });
                }
            },
            error: function () {
                UIkit.notification({
                    message: WPAST_DATA.error_occurred,
                    status: 'danger',
                    pos: 'bottom-center',
                    timeout: 5000
                });
            },
            complete: function (data) {
                button.prop('disabled', false);
                loader.hide();
            }
        });
    });

    $('.wpast-new-fag').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.prev('div').before('<div class="uk-margin-small"><textarea class="uk-textarea" name="fags[]" rows="5"></textarea></div>');
    });

    $('.wpast-new-help-link').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.prev('div').before('<div class="uk-margin-small"><input type="text" class="uk-input" name="help-links[]" /></div>');
    });

    $('.wpast-clipboard').click(function () {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).text()).select();
        document.execCommand("copy");
        $temp.remove();
    });

    $('.wpast-upload-file').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var image = wp.media({
            multiple: false,
        }).open().on('select', function (e) {
            var uploadedImage = image.state().get('selection').first();
            var imageID = uploadedImage.toJSON().id;
            var imageURL = uploadedImage.toJSON().url;
            $this.val(imageURL);
            $this.prev('br').prev('input[type="hidden"]').val(imageID);
            $this.prev('br').prev('input[type="hidden"]').prev('img.wpast-image').attr('src', imageURL);
        });
    });

    $('.wpast-upload-file2').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var image = wp.media({
            multiple: true,
        }).open().on('select', function (e) {
            var imageURL = [];
            var selection = image.state().get('selection');
            selection.map(function (attachment) {
                imageURL.push(attachment.toJSON().url);
            });
            console.log(imageURL);
            $this.val(imageURL.join(','));
        });
    });

    $('.wpast-remove').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var $default = $this.data('default');
        var $image = $this.prev('button').prev('br').prev('input[type="hidden"]').prev('img.wpast-image');
        $this.prev('button').prev('br').prev('input[type="hidden"]').val('');
        $image.attr('src', $default);
    });

    $("select.wpast-confirm").change(function () {
        var $this = $(this);
        var $name = $this.attr('name');
        if ($this.val()) {
            $('#wpast-' + $name).slideDown();
        } else {
            $('#wpast-' + $name).slideUp();
        }
    });
    $("select.wpast-confirm").each(function () {
        var $this = $(this);
        if ($this.val()) {
            var $name = $this.attr('name');
            $('#wpast-' + $name).show();
        }
    });

    $('.wpast-new-status').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.prev('div').append('<div class="uk-margin-small"><input type="text" class="uk-input" name="statuses[]" placeholder="' + WPAST_DATA.slug + ':' + WPAST_DATA.name + ':' + WPAST_DATA.hex_code + '"></div>');
    });

    $('.wpast-code-table code').click(function () {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).text()).select();
        document.execCommand("copy");
        $temp.remove();
    });

    $('.wpast-tickets #bulk-action-selector-top').change(function () {
        var $this = $(this);
        var doAction = $('.wpast-tickets #doaction');
        if ($this.val() === 'bulk-delete') {
            doAction.attr("onclick", "return confirm('" + WPAST_DATA.sure_to_delete + "')");
        } else {
            doAction.attr("onclick", "");
        }
    });

    if ($('.wpast-datepicker').length) {
        if (WPAST_DATA.lang !== 'fa') {
            $(".wpast-datepicker").datepicker();
        } else {
            $('.wpast-datepicker').persianDatepicker({ formatDate: "YYYY-MM-DD" });
        }
    }

    var creatorID = $('#wpast-creator-id');
    if (creatorID.length) {
        creatorID.select2({
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                type: 'post',
                timeout: 20000,
                data: function (params) {
                    return {
                        term: params.term,
                        nonce: nonce,
                        action: 'wpast_search_creator'
                    };
                },
                processResults: function (data) {
                    var options = [];
                    if (data) {
                        $.each(data, function (index, text) {
                            options.push({ id: text[0], text: text[1] });
                        });
                    }
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: WPAST_DATA.creator,
            allowClear: true
        });
    }

    var userID = $('#wpast-user-id');
    if (userID.length) {
        userID.select2({
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                type: 'post',
                timeout: 20000,
                data: function (params) {
                    return {
                        term: params.term,
                        nonce: nonce,
                        action: 'wpast_search_creator'
                    };
                },
                processResults: function (data) {
                    var options = [];
                    if (data) {
                        $.each(data, function (index, text) {
                            options.push({ id: text[0], text: text[1] });
                        });
                    }
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: WPAST_DATA.user,
            allowClear: true
        });
    }

    var vendorID = $('#wpast-vendor-id');
    if (vendorID.length) {
        vendorID.select2({
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                type: 'post',
                timeout: 20000,
                data: function (params) {
                    return {
                        term: params.term,
                        nonce: nonce,
                        action: 'wpast_search_creator'
                    };
                },
                processResults: function (data) {
                    var options = [];
                    if (data) {
                        $.each(data, function (index, text) {
                            options.push({ id: text[0], text: text[1] });
                        });
                    }
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: WPAST_DATA.vendor,
            allowClear: true
        });
    }

    if (WPAST_DATA.woo_active) {
        var product = $('#wpast-product');
        if (product.length) {
            product.select2({
                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    delay: 250,
                    type: 'post',
                    timeout: 20000,
                    data: function (params) {
                        return {
                            term: params.term,
                            nonce: nonce,
                            action: 'wpast_search_product'
                        };
                    },
                    processResults: function (data) {
                        var options = [];
                        if (data) {
                            $.each(data, function (index, text) {
                                options.push({ id: text[0], text: text[1] });
                            });
                        }
                        return {
                            results: options
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: WPAST_DATA.product,
                allowClear: true
            });
        }
    }

    $('.wpast-show-more').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.toggleClass('expand');
        if ($this.hasClass('expand')) {
            $this.text(WPAST_DATA.show_less);
        } else {
            $this.text(WPAST_DATA.show_more);
        }
        $('.wpast-pre-answer .wpast-inner').toggleClass('expand');
    });

    $('.wpast-pre-answer-item.wpast-start-ticket').click(function (e) {
        var $this = $(this);
        var $content = $this.find('.wpast-pre-answer-content').html();
        if (tinyMCE && tinyMCE.activeEditor) {
            tinyMCE.get('wpast-content').selection.setContent($content);
        }
        $('#wpast-content').val($content);
    });

    $('.wpast-pre-answer-item.wpast-reply').click(function (e) {
        var $this = $(this);
        var $content = $this.find('.wpast-pre-answer-content').html();
        if (tinyMCE && tinyMCE.activeEditor) {
            tinyMCE.get('wpast-reply-content').selection.setContent($content);
        }
        $('#wpast-reply-content').val($content);
    });

    $('.wpast-start-ticket .wpast-toggle-edit').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $('#post-body-content .wpast-content-inner').slideToggle();
    });

    $('.wpast-reply .wpast-toggle-edit').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.parentsUntil('#wpast-replies').next('.wpast-editor').slideToggle();
    });

    $('#wpast-add-annotation').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var ticketID = parseInt($this.data('ticket-id'));
        var annotation = $('#wpast-annotation');
        if (!ticketID || !annotation.val()) {
            return false;
        }
        $this.addClass('disabled');

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_add_annotation',
                nonce: nonce,
                ticketID: ticketID,
                annotation: annotation.val()
            },
            success: function (response) {
                if (response.result === true) {
                    annotation.val('');
                    $('.wpast-annotations').remove();
                    $this.after(response._html);
                }
            },
            error: function () {
                alert(WPAST_DATA.error_occurred);
            },
            complete: function (data) {
                $this.removeClass('disabled');
            }
        });
    });

    $('.wpast-delete-annotation').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var ticketID = parseInt($this.data('ticket-id'));
        var date = parseInt($this.data('date'));
        if (!ticketID || !date) {
            return false;
        }

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_delete_annotation',
                nonce: nonce,
                ticketID: ticketID,
                date: date
            },
            success: function (response) {
                if (response.result === true) {
                    $this.parentsUntil('.wpast-annotations').fadeOut();
                }
            },
            error: function () {
                alert(WPAST_DATA.error_occurred);
            },
            complete: function (data) {

            }
        });
    });

    $('#wpast-info-div .wpast-info-tabs a').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var $item = $this.parentsUntil('.wpast-info-tabs');
        var $class = $item.attr('class');
        var panel = $class.replace('wpast-tab-', '');
        $item.addClass('active');
        $item.siblings('li').removeClass('active');
        $('#wpast-info-div .wpast-tab-panel').hide();
        $('#wpast-info-div #wpast-panel-' + panel).show();
    });

    $('.wpast-edit-date').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.next('input').toggle();
    });

    $('.wpast-delete-process').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var ticketID = parseInt($this.data('ticket-id'));
        var $index = parseInt($this.data('index'));
        if (!ticketID || !$index) {
            return false;
        }

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_delete_process',
                nonce: nonce,
                ticketID: ticketID,
                index: $index
            },
            success: function (response) {
                if (response.result === true) {
                    $this.parentsUntil('.wpast-processes').fadeOut();
                }
            },
            error: function () {
                alert(WPAST_DATA.error_occurred);
            },
            complete: function (data) {

            }
        });
    });

    try {
        $('.wpast-edit-ticket #post-body-content, .wpast-edit-ticket #postbox-container-1, .wpast-submit-ticket #post-body-content, .wpast-submit-ticket #postbox-container-1').theiaStickySidebar({
            additionalMarginTop: 40
        });
    } catch (e) {

    }

    var answerableID = $('#wpast-answerable');
    if (answerableID.length) {
        answerableID.select2({
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                type: 'post',
                timeout: 20000,
                data: function (params) {
                    return {
                        term: params.term,
                        nonce: nonce,
                        action: 'wpast_search_creator'
                    };
                },
                processResults: function (data) {
                    var options = [];
                    if (data) {
                        $.each(data, function (index, text) {
                            options.push({ id: text[0], text: text[1] });
                        });
                    }
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: WPAST_DATA.users,
        });
    }

    $('a.wpast-default-email').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var editor_id = $this.data('toggle');
        var $content = $('#wpast-default-email').html();
        if (tinyMCE && tinyMCE.activeEditor) {
            tinyMCE.get(editor_id).selection.setContent($content);
        }
        $(editor_id).val($content);
    });

    $('#wpast-send-test-sms').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var ticketID = $('#wpast-ticket-id');
        var loader = $this.find('.loader');
        var alert = $this.prev();
        if (ticketID.val() === '') {
            return false;
        }
        $this.prop('disabled', true);
        loader.show();
        alert.hide();

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_send_test_sms',
                nonce: nonce,
                ticketID: ticketID.val(),
            },
            success: function (response) {
                alert.html(JSON.stringify(response.result)).show();
            },
            error: function () {
                UIkit.notification({
                    message: WPAST_DATA.error_occurred,
                    status: 'danger',
                    pos: 'bottom-center',
                    timeout: 5000
                });
            },
            complete: function (data) {
                $this.prop('disabled', false);
                loader.hide();
            }
        });
    });

    $("body[class*='wpast-tickets'] fieldset.metabox-prefs .hide-column-tog").change(function () {
        var $this = $(this);
        var $val = $this.val();
        if (!this.checked) {
            document.cookie = "wpast_hide_" + $val + "=1";
            return false;
        }
        document.cookie = "wpast_hide_" + $val + "=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    });

    $('#wpast-sms-service').on('change', function () {
        showPatternWrapper();
    });
    showPatternWrapper();

    function showPatternWrapper() {
        //var patternWrapper = $('.wpast-pattern-wrapper');
        //var messageWrapper = $('.wpast-message-wrapper');
        var codeTable = $('.wpast-code-table');
        var kavenegarTable = $('.wpast-kavenegar');
        var $selected = $('#wpast-sms-service').find(":selected");
        if ($selected.data('pattern') == 1) {
            //patternWrapper.show();
            //messageWrapper.hide();
            if ($selected.val() == 'kavenegar') {
                codeTable.hide();
                kavenegarTable.show();
            } else {
                codeTable.show();
                kavenegarTable.hide();
            }
        } else {
            //patternWrapper.hide();
            //messageWrapper.show();
            //codeTable.hide();
            //kavenegarTable.hide();
        }
    }

    $('.wpast-departments #wpast-parent').on('change', function () {
        var $this = $(this);
        var fields = $('.wpast-form-field-fields');
        if ($this.val() === '0') {
            fields.hide();
        } else {
            fields.show();
        }
    });

    if ($('.wpast-color-field').length) {
        $('.wpast-color-field').wpColorPicker();
    }

    var ddw = $('.default-department-wrapper');
    $('input[name="submit-type"]').change(function () {
        if ($(this).is(':checked') && $(this).val() == 'child-department') {
            ddw.slideDown();
        } else {
            ddw.slideUp();
            $('.default-department').prop('checked', false);
        }
    });

    var orderDefaults = $('.wpast-order-defaults');
    $('input[name="support-type"]').change(function () {
        if ($(this).is(':checked') && $(this).val() == 'order_id') {
            orderDefaults.show();
        } else {
            orderDefaults.hide();
        }
    });

    if ($('input[name="submit-type"]:checked').val() == 'child-department') {
        ddw.show();
    } else {
        ddw.hide();
        $('.default-department').prop('checked', false);
    }

    $('.wpast-hide-tuts-alert').click(function (e) {
        e.preventDefault();
        let $this = $(this);

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_hide_tuts_alert',
                nonce: nonce,
            },
            success: function (response) {
                if (response.result === true) {
                    $this.parentsUntil('.uk-margin').slideUp(200);
                }
            },
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
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_admin_load_more_products',
                page,
                nonce
            },
            success: function (response) {
                if (response._success === true) {
                    page = page + 1;
                    $this.attr('data-page', page);
                    $this.prev('ul').append(response.results);
                    if (response.results === '') $this.hide();
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

    $('.wpast-toggle-preview').click(function (e) {
        e.preventDefault();
        let id = parseInt($(this).data('id'));
        if (!id) {
            return false;
        }

        let modal = $('#wpast-ticket-preview');
        let modalTitle = modal.find('.wpast-modal-title');
        let modalContent = modal.find('.wpast-modal-content');
        
        modal.addClass('show');
        modalTitle.html('');
        modalContent.html(`<div>${WPAST_DATA.is_loading}</div>`);

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 20000,
            data: {
                action: 'wpast_preview_ticket',
                nonce,
                id
            },
            success: function (response) {
                if(response.success === true) {
                    modalTitle.html(response.data.title);
                    modalContent.html(response.data.content_html);
                }
            },
        });
    });
});

function wpast_validate() {
    return confirm(WPAST_DATA.sure_to_delete);
}