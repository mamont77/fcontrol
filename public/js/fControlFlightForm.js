(function ($) {
    /**
     *
     * @type {string}
     */
    var ajaxPath = '/leg/get-airports/';

    /**
     *
     * @param $airportField
     * @param countryId
     * @param airportId
     */
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

    /**
     *
     * @param currentValue
     * @param exchangeRateValue
     * @returns {string}
     */
    function convertApServicePrice2Usd(currentValue, exchangeRateValue) {
        var result = 0;
        currentValue = parseFloat(currentValue);
        exchangeRateValue = parseFloat(exchangeRateValue);
        if (!isNaN(currentValue) && !isNaN(exchangeRateValue)) {
            result = currentValue * exchangeRateValue;
            //доллары = количество валюты * (1/курс_валюты)
            //result = currentValue * (1 / exchangeRateValue);
        }

        return result.toFixed(2);
    }

    /**
     *
     * @param quantity
     * @param priceUsd
     * @returns {string}
     */
    function calculateRefuelTotalUsd(quantity, priceUsd) {
        var result = 0;
        quantity = parseFloat(quantity);
        priceUsd = parseFloat(priceUsd);

        if (!isNaN(quantity) && !isNaN(priceUsd)) {
            result = quantity * priceUsd;
        }

        return result.toFixed(4);
    }

    /**
     *
     * @param quantityLtr
     * @param unit
     * @returns {string}
     */
    function convertRefuelQuantityLtr2OtherUnits(quantityLtr, unit) {
        var result = 0;
        quantityLtr = parseFloat(quantityLtr);

        if (!isNaN(quantityLtr) && unit != '') {
            switch (unit) {
                case 'LTR':
                    result = quantityLtr;

                    break;

                case 'MT':
                    result = (quantityLtr * 0.8) / 1000;

                    break;

                case 'IG':
                    result = quantityLtr / 4.54609188;

                    break;

                case 'USG':
                    result = quantityLtr / 3.7854;

                    break;
            }
        }

        return result.toFixed(2);
    }

    /**
     *
     * @param quantityOtherUnits
     * @param unit
     * @returns {string}
     */
    function convertRefuelQuantityOtherUnits2Ltr(quantityOtherUnits, unit) {
        var result = 0;
        quantityOtherUnits = parseFloat(quantityOtherUnits);

        if (!isNaN(quantityOtherUnits) && unit != '') {
            switch (unit) {
                case 'LTR':
                    result = quantityOtherUnits;

                    break;

                case 'MT':
                    result = 1000 * (quantityOtherUnits / 0.8);

                    break;

                case 'IG':
                    result = quantityOtherUnits * 4.54609188;

                    break;

                case 'USG':
                    result = quantityOtherUnits * 3.7854;

                    break;
            }
        }

        return result.toFixed(2);
    }

    /**
     *
     * @type {{attach: Function}}
     */
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

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.flightHeaderForm = {
        attach: function (context, settings) {
            var $form = $('form#flightHeader');

            if ($form.length == 0) return;

            $($form).find('#dateOrder').mask('99-99-9999');
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.flightLegForm = {
        attach: function (context, settings) {
            var $form = $('form#leg');

            if ($form.length == 0) return;

            var preSelectedFlightNumberAirportId = $form.find('#preSelectedFlightNumberAirportId').val(),
                preSelectedApDepCountryId = $form.find('#preSelectedApDepCountryId').val(),
                preSelectedApDepAirportId = $form.find('#preSelectedApDepAirportId').val(),
                $flightNumberAirportId = $form.find('#flightNumberAirportId'),
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

            if (preSelectedFlightNumberAirportId > 0) {
                $flightNumberAirportId.val(preSelectedFlightNumberAirportId);
                $flightNumberAirportId.find('option').each(function () {
                    $(this).prop('disabled', true);
                });
                $flightNumberAirportId.find('[value="' + preSelectedFlightNumberAirportId + '"]')
                    .attr('selected', 'selected').prop('disabled', false);
            }

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

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.refuelForm = {
        attach: function (context, settings) {
            var $form = $('form#refuel');

            if ($form.length == 0) return;

            var $legData = $('#leg table tbody tr'),
                legDataMapped = {},
                $legId = $($form).find('#legId'),
                $quantityLtr = $($form).find('#quantityLtr'),
                $quantityOtherUnits = $($form).find('#quantityOtherUnits'),
                $unitId = $($form).find('#unitId'),
                $priceUsd = $($form).find('#priceUsd'),
                $totalPriceUsd = $($form).find('#totalPriceUsd'),
                $date = $($form).find('#date');

            $date.mask('99-99-9999');

            $legData.each(function () {
                var $row = $(this);

                legDataMapped[$row.attr('data-legId')] = $row.find('.date').text();
            });

            $legId.change(function () {
                $date.val(legDataMapped[$(this).val()]);
            });

            $($quantityLtr).bind("keyup change", function () {
                var quantityLtrValue = $(this).val() || 0,
                    unitSelected = $unitId.find(':selected').text(),
                    quantityOtherUnitsValue = convertRefuelQuantityLtr2OtherUnits(quantityLtrValue, unitSelected),
                    priceUsdValue = $priceUsd.val();

                $quantityOtherUnits.val(quantityOtherUnitsValue);
                $totalPriceUsd.val(calculateRefuelTotalUsd(quantityOtherUnitsValue, priceUsdValue));
            });

            $($quantityOtherUnits).bind("keyup change", function () {
                var quantityOtherUnitsValue = $(this).val() || 0,
                    unitSelected = $unitId.find(':selected').text(),
                    quantityLtrValue = convertRefuelQuantityOtherUnits2Ltr(quantityOtherUnitsValue, unitSelected),
                    priceUsdValue = $priceUsd.val();

                $quantityLtr.val(quantityLtrValue);
                $totalPriceUsd.val(calculateRefuelTotalUsd(quantityOtherUnitsValue, priceUsdValue));
            });

            $unitId.change(function () {
                var unitSelected = $(this).find(':selected').text(),
                    quantityLtrValue = $quantityLtr.val(),
                    quantityOtherUnitsValue = $quantityOtherUnits.val(),
                    priceUsdValue = $priceUsd.val();

                if (quantityLtrValue != '') {
                    quantityOtherUnitsValue = convertRefuelQuantityLtr2OtherUnits(quantityLtrValue, unitSelected);
                    $quantityOtherUnits.val(quantityOtherUnitsValue);
                } else if (quantityOtherUnitsValue != '') {
                    quantityLtrValue = convertRefuelQuantityOtherUnits2Ltr(quantityOtherUnitsValue, unitSelected);
                    $quantityLtr.val(quantityLtrValue);
                }
                $totalPriceUsd.val(calculateRefuelTotalUsd(quantityOtherUnitsValue, priceUsdValue));
            });

            $($priceUsd).bind("keyup change", function () {
                var quantityOtherUnitsValue = $quantityOtherUnits.val(),
                    priceUsdValue = $(this).val() || 0;
                $totalPriceUsd.val(calculateRefuelTotalUsd(quantityOtherUnitsValue, priceUsdValue));
            });

        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.ApServiceForm = {
        attach: function (context, settings) {
            var $form = $('form#apService');

            if ($form.length == 0) return;

            var $price = $($form).find('#price'),
                $currency = $($form).find('#currency'),
                $exchangeRate = $($form).find('#exchangeRate'),
                $priceUsd = $($form).find('#priceUSD');

            $($price).bind("keyup change", function () {
                var currentValue = $(this).val() || 0,
                    exchangeRateValue = $exchangeRate.val() || 1;
                $priceUsd.val(convertApServicePrice2Usd(currentValue, exchangeRateValue));
            });

            $($exchangeRate).bind("keyup change", function () {
                var exchangeRateValue = $(this).val() || 0,
                    currentValue = $price.val() || 1;
                $priceUsd.val(convertApServicePrice2Usd(currentValue, exchangeRateValue));
            });

            $currency.change(function () {
                var value = $(this).val();

                if (value == 'USD') {
                    $exchangeRate.val(1);
                }
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.flightSearchForm = {
        attach: function (context, settings) {
            var $form = $('form#flightSearch'),
                $statusLabels;

            if ($form.length == 0) return;

            $($form).find('#dateOrderFrom, #dateOrderTo').mask('99-99-9999');

            $statusLabels = $($form).find('#controls-status label');
            $statusLabels.eq(0).addClass('labelAny');
            $statusLabels.eq(1).addClass('labelInProcess');
            $statusLabels.eq(2).addClass('labelDone');
        }
    };

})(jQuery);
