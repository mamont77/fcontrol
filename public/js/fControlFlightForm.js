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
                preSelectedApDepCountryId = $form.find('#preSelectedApDepCountryId').val(),
                preSelectedApDepAirportId = $form.find('#preSelectedApDepAirportId').val(),
                $apDepAirportId = $form.find('#apDepAirportId'),
                $apArrAirportId = $form.find('#apArrAirportId'),
                $apDepCountries = $form.find('#apDepCountries'),
                $apDepAirports = $form.find('#apDepAirports'),
                $apArrCountries = $form.find('#apArrCountries'),
                $apArrAirports = $form.find('#apArrAirports');

            $($form).find('#dateOfFlight').mask('99-99-9999');
            $($form).find('#apDepTime').mask('99:99');
            $($form).find('#apArrTime').mask('99:99');

            if (preSelectedApDepCountryId > 0) {
                $apDepCountries.find('option').each(function(){
                    $(this).prop('disabled', true);
                });
                $apDepCountries.find('[value="' + preSelectedApDepCountryId + '"]').attr("selected", "selected").prop('disabled', false);
            }
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
//    fControl.behaviors.flightLegFormRenderAirports = {
//        attach: function (context, settings) {
//            var $form = $('form#leg'),
//                $ApDepCountriesField = $form.find('#apDepCountries'),
//                $ApArrCountriesField = $form.find('#apArrCountries'),
//                $apDepAirportId = $form.find('#apDepAirportId'),
//                $apArrAirportId = $form.find('#apArrAirportId'),
//                $apDepAirports = $form.find('#apDepAirports'),
////                $helpApDepAirports = $form.find("#help-apDep[apDepAirports]"),
//                $apArrAirports = $form.find('#apArrAirports'),
////                $helpApArrAirports = $form.find("#help-apArr[apArrAirports]"),
//                ajaxPath = '/leg/get-airports/';
//
//            if ($ApDepCountriesField.val() > 0) {
//                $.getJSON(ajaxPath + $ApDepCountriesField.val(), function (data) {
//                    $apDepAirports.prop('disabled', false);
//                    $.each(data['airports'], function (key, val) {
//                        $apDepAirports.append('<option value="' + key + '" ' +
//                            'data-name="' + val['name'] + '">' + val['code'] + '</option>');
//                    });
//                    if ($apDepAirports.val() > 0) {
//                        $apDepAirports.val('id_' + $apDepAirports.val());
//                    }
//                });
//            }
//
//            $ApDepCountriesField.change(function () {
//                var currentCountryId = $(this).val();
//                $apDepAirports.prop('disabled', true).empty();
//                $apDepAirports.val(0);
//                $.getJSON(ajaxPath + currentCountryId, function (data) {
//                    $apDepAirports.prop('disabled', false);
//                    $.each(data['airports'], function (key, val) {
//                        $apDepAirports.append('<option value="' + key + '" ' +
//                            'data-name="' + val['name'] + '">' + val['code'] + '</option>');
//                    });
//                });
//            });
//
//            $apDepAirports.change(function () {
//                var currentAirportId = $(this).val().split('_');
////                    currentAirportName;
//                currentAirportId = currentAirportId[1];
//                $apDepAirportId.val(currentAirportId);
////                currentAirportName = $apDepAirports.find('option:selected').attr('data-name');
//            });
//
//            if ($ApArrCountriesField.val() > 0) {
//                $.getJSON(ajaxPath + $ApArrCountriesField.val(), function (data) {
//                    $apArrAirports.prop('disabled', false);
//                    $.each(data['airports'], function (key, val) {
//                        $apArrAirports.append('<option value="' + key + '" ' +
//                            'data-name="' + val['name'] + '">' + val['code'] + '</option>');
//                    });
//                    if ($apArrAirports.val() > 0) {
//                        $apArrAirports.val('id_' + $apArrAirports.val());
//                    }
//                });
//            }
//
//            $ApArrCountriesField.change(function () {
//                var currentCountryId = $(this).val();
//                $apArrAirports.prop('disabled', true).empty();
//                $apArrAirports.val(0);
//                $.getJSON(ajaxPath + currentCountryId, function (data) {
//                    $apArrAirports.prop('disabled', false);
//                    $.each(data['airports'], function (key, val) {
//                        $apArrAirports.append('<option value="' + key + '" ' +
//                            'data-name="' + val['name'] + '">' + val['code'] + '</option>');
//                    });
//                });
//            });
//
//            $apArrAirports.change(function () {
//                var currentAirportId = $(this).val().split('_');
//                currentAirportId = currentAirportId[1];
//                $apArrAirportId.val(currentAirportId);
//            });
//        }
//    };

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
