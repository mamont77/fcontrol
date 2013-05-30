(function ($) {

    fControl.behaviors.setAircraftRegNumber = {
        attach:function (context, settings) {
            var $form = $('form#flight');
            $($form).find('#dateOrder').mask('99/99/99');
            $($form).find('#hint-aircraft').text('GegNumber: ' + ($form).find('#aircraft').val());
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
