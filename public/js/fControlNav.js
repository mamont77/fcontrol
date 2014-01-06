(function ($) {

    fControl.behaviors.browserDetector = {
        attach: function (context, settings) {
            var browserName = $.browser.name,
                browserVersion = $.browser.version;
//                txt = ''
//                + 'jQuery.browser.ua  = ' + $.browser.ua + '<br>'
//                + 'jQuery.browser.name  = ' + $.browser.name + '<br>'
//                + 'jQuery.browser.fullVersion  = ' + $.browser.fullVersion + '<br>'
//                + 'jQuery.browser.version = ' + $.browser.version + '<br><br><br>'
//                + 'jQuery.browser.msie = ' + $.browser.msie + '<br>'
//                + 'jQuery.browser.mozilla = ' + $.browser.mozilla + '<br>'
//                + 'jQuery.browser.opera = ' + $.browser.opera + '<br>'
//                + 'jQuery.browser.webkit = ' + $.browser.webkit + '<br>';
//                console.log(txt);
            if (browserName != 'Chrome') {
                $('.breadcrumb').after('<div class="alert"><strong>Warning!</strong> Your browser is not supported. ' +
                    'Please use the <a href="http://www.google.com/chrome" target="_blank">Chrome</a>!</div>');
            }
        }
    };

    fControl.behaviors.userNav = {
        attach: function (context, settings) {
            var userNav = $('.user_nav .pull-right');
            $('.navbar .container .nav-collapse').append(userNav);
        }
    };

    fControl.behaviors.DisabledLinks = {
        attach: function (context, settings) {
            $("a.disabled").click(function () {
                return false;
            });
        }
    };

    fControl.behaviors.tableFixedHeader = {
        attach: function (context, settings) {
            $('.table-fixed-header').fixedHeader();
        }
    }

})(jQuery);
