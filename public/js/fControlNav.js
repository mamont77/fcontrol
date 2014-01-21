(function ($) {

    fControl.behaviors.browserDetector = {
        attach: function (context, settings) {
            var browserName = $.browser.name,
                browserVersion = $.browser.version;
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
