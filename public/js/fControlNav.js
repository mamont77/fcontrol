(function ($) {

    fControl.behaviors.userNav = {
        attach:function (context, settings) {
            var userNav = $('.user_nav .pull-right');
            $('.navbar .container .nav-collapse').append(userNav);
        }
    };

})(jQuery);
