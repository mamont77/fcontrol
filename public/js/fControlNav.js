(function ($) {

    fControl.behaviors.userNav = {
        attach:function (context, settings) {
            var userNav = $('.user_nav .pull-right');
            $('.navbar .container .nav-collapse').append(userNav);
        }
    };

    fControl.behaviors.DisabledLinks = {
        attach:function (context, settings) {
            $( "a.disabled" ).click(function() {
                return false;
            });
        }
    };

    fControl.behaviors.tableFixedHeader = {
        attach:function (context, settings) {
            $('.table-fixed-header').fixedHeader();
        }
    }

})(jQuery);
