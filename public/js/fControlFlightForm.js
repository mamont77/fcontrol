(function ($) {

    fControl.behaviors.flightHeaderForm = {
        attach:function (context, settings) {
            var $form = $('form#flightHeader');
            $($form).find('#dateOrder').mask('99-99-9999');
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
            var $form = $('form#leg'),
                apDepIcaoAndIata,
                preSelectedValue;
            $($form).find('#dateOfFlight').mask('99-99-9999');
            $($form).find('#apDepTime').mask('99:99');
            $($form).find('#apArrTime').mask('99:99');
            apDepIcaoAndIata = $form.find('#apDepIcaoAndIata');
            preSelectedValue = apDepIcaoAndIata.val();
            if (preSelectedValue > 0) {
                apDepIcaoAndIata.find('option').each(function(){
                    $(this).prop('disabled', true);
                });
                apDepIcaoAndIata.find('option:selected').prop('disabled', false)
            }
        }
    };

    fControl.behaviors.refuelForm = {
        attach:function (context, settings) {
            var $form = $('form#refuel');
            $($form).find('#date').mask('99-99-9999');
        }
    };

    fControl.behaviors.flightSearchForm = {
        attach:function (context, settings) {
            var $form = $('form#flightSearch'),
                $statusLabels;
            $($form).find('#dateOrderFrom, #dateOrderTo').mask('99-99-9999');

            $statusLabels = $($form).find('#controls-status label');
            $statusLabels.eq(0).addClass('labelAny');
            $statusLabels.eq(1).addClass('labelInProcess');
            $statusLabels.eq(2).addClass('labelDone');
        }
    };

})(jQuery);
