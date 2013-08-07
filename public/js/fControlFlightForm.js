(function ($) {

    fControl.behaviors.flightHeaderForm = {
        attach:function (context, settings) {
            var $form = $('form#flightHeader');
            $($form).find('#dateOrder').mask('9999-99-99');
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

    fControl.behaviors.flightLegForm = {
        attach:function (context, settings) {
            var $form = $('form#leg');
            $($form).find('#dateOfFlight').mask('99-99-9999');
            $($form).find('#apDepTime').mask('99:99');
            $($form).find('#apArrTime').mask('99:99');
        }
    };

    fControl.behaviors.refuelForm = {
        attach:function (context, settings) {
            var $form = $('form#refuel');
            $($form).find('#date').mask('99-99-9999');
        }
    };

})(jQuery);
