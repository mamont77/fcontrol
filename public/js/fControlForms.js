(function ($) {

    fControl.behaviors.setFocus = {
        attach:function (context, settings) {
            $('form:first *:input[type!=hidden]:first').focus();
        }
    };

    fControl.behaviors.logsSearchForm = {
        attach:function (context, settings) {
            var $form = $('form#logsSearch');
            $($form).find('#dateFrom, #dateTo').mask('99-99-9999');
        }
    };
})(jQuery);
