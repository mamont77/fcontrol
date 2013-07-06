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

    fControl.behaviors.flightDataForm = {
        attach:function (context, settings) {
            var $form = $('form#flightData');
            $($form).find('#dateOfFlight').mask('99-99-9999');

            //Flight Number
            $form.on('change', '#flightNumberIdIcao', function () {
                var master = $(this).val(),
                    slave,
                    firstElement = $form.find('#flightNumberIdIata option:first');
                if (master == '' || master === undefined) {
                    firstElement.prop('selected', true);
                } else {
                    slave = $form.find("#flightNumberIdIata [value='" + $(this).val() + "']");
                    if (slave.val() === undefined) {
                        firstElement.prop('selected', true);
                    } else {
                        slave.prop('selected', true);
                    }
                }
            });

            $form.on('change', '#flightNumberIdIata', function () {
                var master = $(this).val(),
                    slave,
                    firstElement = $form.find('#flightNumberIdIcao option:first');
                if (master == '' || master === undefined) {
                    firstElement.prop('selected', true);
                } else {
                    slave = $form.find("#flightNumberIdIcao [value='" + $(this).val() + "']");
                    if (slave.val() === undefined) {
                        firstElement.prop('selected', true);
                    } else {
                        slave.prop('selected', true);
                    }
                }
            });

            //App Dep
            $($form).find('#apDepTime').mask('99:99');
            $form.on('change', '#apDepIdIcao', function () {
                var master = $(this).val(),
                    slave,
                    firstElement = $form.find('#apDepIdIata option:first');
                if (master == '' || master === undefined) {
                    firstElement.prop('selected', true);
                } else {
                    slave = $form.find("#apDepIdIata [value='" + $(this).val() + "']");
                    if (slave.val() === undefined) {
                        firstElement.prop('selected', true);
                    } else {
                        slave.prop('selected', true);
                    }
                }
            });

            $form.on('change', '#apDepIdIata', function () {
                var master = $(this).val(),
                    slave,
                    firstElement = $form.find('#apDepIdIcao option:first');
                if (master == '' || master === undefined) {
                    firstElement.prop('selected', true);
                } else {
                    slave = $form.find("#apDepIdIcao [value='" + $(this).val() + "']");
                    if (slave.val() === undefined) {
                        firstElement.prop('selected', true);
                    } else {
                        slave.prop('selected', true);
                    }
                }
            });

            //App Arr
            $($form).find('#apArrTime').mask('99:99');
            $form.on('change', '#apArrIdIcao', function () {
                var master = $(this).val(),
                    slave,
                    firstElement = $form.find('#apArrIdIata option:first');
                if (master == '' || master === undefined) {
                    firstElement.prop('selected', true);
                } else {
                    slave = $form.find("#apArrIdIata [value='" + $(this).val() + "']");
                    if (slave.val() === undefined) {
                        firstElement.prop('selected', true);
                    } else {
                        slave.prop('selected', true);
                    }
                }
            });

            $form.on('change', '#apArrIdIata', function () {
                var master = $(this).val(),
                    slave,
                    firstElement = $form.find('#apArrIdIcao option:first');
                if (master == '' || master === undefined) {
                    firstElement.prop('selected', true);
                } else {
                    slave = $form.find("#apArrIdIcao [value='" + $(this).val() + "']");
                    if (slave.val() === undefined) {
                        firstElement.prop('selected', true);
                    } else {
                        slave.prop('selected', true);
                    }
                }
            });

        }
    };

})(jQuery);
