(function ($) {

    fControl.behaviors.setFocus = {
        attach:function (context, settings) {
            $('form:first *:input[type!=hidden]:first').focus();
        }
    };

})(jQuery);
