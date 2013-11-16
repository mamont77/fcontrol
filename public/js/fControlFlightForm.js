(function ($) {
    var ajaxPath = '/leg/get-airports/';

    function renderAirportsByCountry($airportField, countryId, airportId) {
        var ajaxPath = '/leg/get-airports/';
        $.getJSON(ajaxPath + countryId, function (data) {
            $.each(data['airports'], function (key, val) {
                $airportField.append('<option value="' + key + '" ' +
                    'data-name="' + val['name'] + '">' + val['code'] + '</option>');
            });
            if (airportId) {
                if ($airportField.selector === 'form#leg #apDepAirports') {
                    $airportField.find('option').each(function () {
                        $(this).prop('disabled', true);
                    });
                }
                $airportField.find('[value="id_' + airportId + '"]').attr('selected', 'selected').prop('disabled', false);
            }
            $airportField.prop('disabled', false);
        });
    }

    function convertApServicePrice2Usd(currentValue, exchangeRateValue) {
        var result = 0;
        currentValue = parseFloat(currentValue);
        exchangeRateValue = parseFloat(exchangeRateValue);
        if (!isNaN(currentValue) && !isNaN(exchangeRateValue)) {
            result = currentValue * exchangeRateValue;
        }

        return result.toFixed(2);
    }

    fControl.behaviors.allForms = {
        attach: function (context, settings) {
            $('.chosen').chosen(
                {
                    allow_single_deselect: true,
                    no_results_text: 'Nothing found!'
                }
            );
            $('.chosen-drop .chosen-search input[type="text"]:first').focus();
        }
    };

    fControl.behaviors.flightHeaderForm = {
        attach: function (context, settings) {
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
        attach: function (context, settings) {
            var $form = $('form#leg'),
                preSelectedApDepCountryId = $form.find('#preSelectedApDepCountryId').val(),
                preSelectedApDepAirportId = $form.find('#preSelectedApDepAirportId').val(),
                $apDepAirportId = $form.find('#apDepAirportId'),
                $apArrAirportId = $form.find('#apArrAirportId'),
                $apDepCountries = $form.find('#apDepCountries'),
                $apDepAirports = $form.find('#apDepAirports'),
                $apArrCountries = $form.find('#apArrCountries'),
                $apArrAirports = $form.find('#apArrAirports'),
                currentCountryId;

            $($form).find('#dateOfFlight').mask('99-99-9999');
            $($form).find('#apDepTime').mask('99:99');
            $($form).find('#apArrTime').mask('99:99');

            // если данные являются продолжением цепочки leg, то выбираем значения в Ap Dep
            // из предыдущего Ap Arr
            if (preSelectedApDepCountryId > 0 && preSelectedApDepAirportId > 0) {
                $apDepCountries.find('option').each(function () {
                    $(this).prop('disabled', true);
                });
                $apDepCountries.find('[value="' + preSelectedApDepCountryId + '"]').attr('selected', 'selected').prop('disabled', false);
                renderAirportsByCountry($apDepAirports, preSelectedApDepCountryId, preSelectedApDepAirportId);
                $apDepAirportId.val(preSelectedApDepAirportId);
            }

            // при редактировании данных, если уже есть $apArrAirportId, то отрисовываем поле IATA (ICAO)
            if ($apArrAirportId.val() > 0) {
                currentCountryId = $apArrCountries.val();
                renderAirportsByCountry($apArrAirports, currentCountryId, $apArrAirportId.val());
            }

            $apDepCountries.change(function () {
                var currentCountryId = $(this).val();
                $apDepAirports.prop('disabled', true).empty();
                $apDepAirports.val(0);
                renderAirportsByCountry($apDepAirports, currentCountryId);
            });

            $apArrCountries.change(function () {
                var currentCountryId = $(this).val();
                $apArrAirports.prop('disabled', true).empty();
                $apArrAirports.val(0);
                renderAirportsByCountry($apArrAirports, currentCountryId);
            });

            $apDepAirports.change(function () {
                var currentAirportId = $(this).val().split('_');
                currentAirportId = currentAirportId[1];
                $apDepAirportId.val(currentAirportId);
            });

            $apArrAirports.change(function () {
                var currentAirportId = $(this).val().split('_');
                currentAirportId = currentAirportId[1];
                $apArrAirportId.val(currentAirportId);
            });
        }
    };

    fControl.behaviors.refuelForm = {
        attach: function (context, settings) {
            var $form = $('form#refuel');
            $($form).find('#date').mask('99-99-9999');
        }
    };

    fControl.behaviors.ApServiceForm = {
        attach: function (context, settings) {
            var $form = $('form#apService'),
                $price = $($form).find('#price'),
                $exchangeRate = $($form).find('#exchangeRate'),
                $priceUsd = $($form).find('#priceUSD');

            $($price).bind("keyup change", function() {
                var currentValue = $(this).val() || 0,
                    exchangeRateValue = $exchangeRate.val() || 1;
                $priceUsd.val(convertApServicePrice2Usd(currentValue, exchangeRateValue));
            });

            $($exchangeRate).bind("keyup change", function() {
                var exchangeRateValue = $(this).val() || 0,
                    currentValue = $price.val() || 1;
                $priceUsd.val(convertApServicePrice2Usd(currentValue, exchangeRateValue));
            });
        }
    };

    fControl.behaviors.flightSearchForm = {
        attach: function (context, settings) {
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
