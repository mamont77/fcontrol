(function ($) {

    fControl.behaviors.setAircraftRegNumber = {
        attach:function (context, settings) {
            var $form = $('form#flight');
            $form.on('change', '#aircraft', function () {
                var $hint = $form.find('#hint-aircraft');
                if ($(this).val() != '') {
                    $hint.text('GegNumber: ' + $(this).val());
                } else {
                    $hint.text('GegNumber');
                }
            });
        }
    };

})(jQuery);
