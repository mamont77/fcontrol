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

        return result.toFixed(4);
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

        return result.toFixed(2);
    }

    /**
     *
     * @param quantityLtr
     * @param unit
     * @returns {string}
     */
    function convertRefuelQuantityLtr2OtherUnits(quantityLtr, unit) {
        var result = 0,
            round = 2;
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
                    round = 3;

                    break;
            }
        }

        return result.toFixed(round);
    }

    /**
     *
     * @param quantityOtherUnits
     * @param unit
     * @returns {string}
     */
    function convertRefuelQuantityOtherUnits2Ltr(quantityOtherUnits, unit) {
        var result = 0,
            round = 2;
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
                    round = 3;

                    break;
            }
        }

        return result.toFixed(round);
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
                $airportId = $($form).find('#airportId'),
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

            $airportId.change(function () {
                var legIdValue = $(this).val().split('-');

                legIdValue = legIdValue[0];
                $date.val(legDataMapped[legIdValue]);
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

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.refuelIncomeInvoiceStep1 = {
        attach: function (context, settings) {
            var $form = $('form#refuelIncomeInvoiceStep1');

            if ($form.length == 0) return;

            $($form).find('#dateOrderFrom, #dateOrderTo').mask('99-99-9999');

            var $form2 = $('form#refuelIncomeInvoiceStep2'),
                $rowsCheckbox = $form2.find('.refuelsSelected');

            if ($form2.length == 0) return;

            // блокируем кнопку Apply до тех пор, пока не будут выбраны один или несколько одинаковых Fuel supplier
            $form2.find('.btn').prop('disabled', true);
            $rowsCheckbox.change(function () {
                var selectedAgents = [];

                $rowsCheckbox.each(function () {
                    var $this = $(this);
                    if ($this.prop('checked')) {
                        selectedAgents.push($this.parent().parent().find('.refuelAgentShortName').text());
                    }
                });

                selectedAgents.sort();
                var i = selectedAgents.length, result = [];
                while (i--) {
                    if (result.join('').search(selectedAgents[i]) == '-1') {
                        result.push(selectedAgents[i]);
                    }
                }
                if (result.length == 1) {
                    $form2.find('.btn').prop('disabled', false);
                } else {
                    $form2.find('.btn').prop('disabled', true);
                }
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.refuelIncomeInvoiceStep3 = {
        attach: function (context, settings) {
            var $form = $('form#refuelIncomeInvoiceStep3');

            if ($form.length == 0) return;

            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceCurrency = $form.find('#invoiceCurrency'),
                $invoiceExchangeRate = $form.find('#invoiceExchangeRate'),
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text() || 'USD',
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            // Блокирум поля при инициилизации
            $($form).find('#invoiceData input, #invoiceData select').each(function () {
                $(this).prop('disabled', true).trigger('chosen:updated');
            });

            //Если Currency == USD, то значение поля Exchange Rate == 1
            $invoiceCurrency.change(function () {
                var value = $(this).val();
                if (value == 'USD') {
                    $invoiceExchangeRate.val(1);
                }
                $('.refuelCurrency').text(value);
            });

            // После Apply курса валют - разблокируем поля
            $('#rateApply').click(function () {
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text();
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val());
                $($form).find('#invoiceCurrency, #invoiceExchangeRate').each(function () {
                    $(this).prop('readonly', true).trigger('chosen:updated');
                });
                $('#rateApply').prop('disabled', true);
                $invoiceCurrency.parent().html('<input type="hidden" name="invoiceCurrency" value="' + invoiceCurrencyText + '"/>'
                    + invoiceCurrencyText);

                $($form).find('#invoiceData input, #invoiceData select').each(function () {
                    $(this).prop('disabled', false).trigger('chosen:updated');
                });
                return false;
            });


            $('.refuelQuantityLtr, .refuelQuantityOtherUnits, .refuelUnitId, .refuelItemPrice, ' +
                '.refuelTax, .refuelMot, .refuelVat, .refuelDeliver').bind("keyup change", function () {
                    var $this = $(this),
                        $row = $(this).parent().parent(),
                    //fields values
                        $refuelQuantityLtr = $row.find('.refuelQuantityLtr'),
                        refuelQuantityLtrVal = parseFloat($refuelQuantityLtr.val()) || 0,
                        $refuelQuantityOtherUnits = $row.find('.refuelQuantityOtherUnits'),
                        refuelQuantityOtherUnitsVal = parseFloat($refuelQuantityOtherUnits.val()) || 0,
                        $refuelUnitId = $row.find('.refuelUnitId'),
                        refuelUnitIdVal = parseFloat($refuelUnitId.val()) || 0,
                        refuelUnitIdText = $refuelUnitId.find(':selected').text() || '',
                        $refuelItemPrice = $row.find('.refuelItemPrice'),
                        refuelItemPriceVal = parseFloat($refuelItemPrice.val()) || 0,
                        $refuelTax = $row.find('.refuelTax'),
                        refuelTaxVal = parseFloat($refuelTax.val()) || 0,
                        $refuelMot = $row.find('.refuelMot'),
                        refuelMotVal = parseFloat($refuelMot.val()) || 0,
                        $refuelVat = $row.find('.refuelVat'),
                        refuelVatVal = parseFloat($refuelVat.val()) || 0,
                        $refuelDeliver = $row.find('.refuelDeliver'),
                        refuelDeliverVal = parseFloat($refuelDeliver.val()) || 0,
                        $refuelPrice = $row.find('.refuelPrice'),
                        refuelPriceVal = parseFloat($refuelPrice.val()) || 0,
                        $refuelPriceTotal = $row.find('.refuelPriceTotal'),
                        refuelPriceTotalVal = parseFloat($refuelPriceTotal.val()) || 0,
                        $refuelExchangeToUsdPriceTotal = $row.find('.refuelExchangeToUsdPriceTotal'),
                        refuelExchangeToUsdPriceTotalVal = parseFloat($refuelExchangeToUsdPriceTotal.val()) || 0,
                    //subTotals
                        $refuelPriceSubTotal = $form.find('.refuelPriceSubTotal'),
                        refuelPriceSubTotalVal = 0,
                        $refuelCurrencyForSubTotal = $form.find('.refuelCurrency'),
                        refuelCurrencyName = '',
                        $refuelExchangeToUsdPriceSubTotal = $form.find('.refuelExchangeToUsdPriceSubTotal'),
                        refuelExchangeToUsdPriceSubTotalVal = 0;

                    // пересчитываем литры в юниты и юниты в литры
                    if (($this.hasClass('refuelQuantityLtr') || $this.hasClass('refuelUnitId')) && refuelUnitIdText != '') {
                        $refuelQuantityOtherUnits.val(convertRefuelQuantityLtr2OtherUnits(refuelQuantityLtrVal,
                            refuelUnitIdText));
                    }

                    // считаем тоталы
                    refuelPriceVal = ((refuelItemPriceVal + refuelTaxVal + refuelMotVal) * ((refuelVatVal + 100 ) / 100)).toFixed(4);
                    $refuelPrice.val(refuelPriceVal);

                    refuelPriceTotalVal = (((refuelQuantityOtherUnitsVal * refuelPriceVal)) + refuelDeliverVal).toFixed(2);
                    $refuelPriceTotal.val(refuelPriceTotalVal);

                    refuelExchangeToUsdPriceTotalVal = (refuelPriceTotalVal * invoiceExchangeRateVal).toFixed(2);
                    $refuelExchangeToUsdPriceTotal.val(refuelExchangeToUsdPriceTotalVal);

                    // отрисоваем сумму под таблицей
                    $form.find('.refuelPriceTotal').each(function () {
                        var val = $(this).val();
                        if (!isNaN(val) && val != '') {
                            refuelPriceSubTotalVal = parseFloat(refuelPriceSubTotalVal) + parseFloat(val);
                        }
                    });
                    $refuelPriceSubTotal.text(refuelPriceSubTotalVal.toFixed(2));

                    $form.find('.refuelExchangeToUsdPriceTotal').each(function () {
                        var val = $(this).val();
                        if (!isNaN(val) && val != '') {
                            refuelExchangeToUsdPriceSubTotalVal = parseFloat(refuelExchangeToUsdPriceSubTotalVal)
                                + parseFloat(val);
                        }
                    });
                    $refuelExchangeToUsdPriceSubTotal.text(refuelExchangeToUsdPriceSubTotalVal.toFixed(2));
                });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.refuelIncomeInvoiceStep4 = {
        attach: function (context, settings) {
            var $form = $('form#refuelIncomeInvoiceStep4');

            if ($form.length == 0) return;

            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceExchangeRate = $form.find('#invoiceExchangeRate'),
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            $('.refuelQuantityLtr, .refuelQuantityOtherUnits, .refuelUnitId, .refuelItemPrice, ' +
                '.refuelTax, .refuelMot, .refuelVat, .refuelDeliver').bind("keyup change", function () {
                    var $this = $(this),
                        $row = $(this).parent().parent(),
                    //fields values
                        $refuelQuantityLtr = $row.find('.refuelQuantityLtr'),
                        refuelQuantityLtrVal = parseFloat($refuelQuantityLtr.val()) || 0,
                        $refuelQuantityOtherUnits = $row.find('.refuelQuantityOtherUnits'),
                        refuelQuantityOtherUnitsVal = parseFloat($refuelQuantityOtherUnits.val()) || 0,
                        $refuelUnitId = $row.find('.refuelUnitId'),
                        refuelUnitIdVal = parseFloat($refuelUnitId.val()) || 0,
                        refuelUnitIdText = $refuelUnitId.find(':selected').text() || '',
                        $refuelItemPrice = $row.find('.refuelItemPrice'),
                        refuelItemPriceVal = parseFloat($refuelItemPrice.val()) || 0,
                        $refuelTax = $row.find('.refuelTax'),
                        refuelTaxVal = parseFloat($refuelTax.val()) || 0,
                        $refuelMot = $row.find('.refuelMot'),
                        refuelMotVal = parseFloat($refuelMot.val()) || 0,
                        $refuelVat = $row.find('.refuelVat'),
                        refuelVatVal = parseFloat($refuelVat.val()) || 0,
                        $refuelDeliver = $row.find('.refuelDeliver'),
                        refuelDeliverVal = parseFloat($refuelDeliver.val()) || 0,
                        $refuelPrice = $row.find('.refuelPrice'),
                        refuelPriceVal = parseFloat($refuelPrice.val()) || 0,
                        $refuelPriceTotal = $row.find('.refuelPriceTotal'),
                        refuelPriceTotalVal = parseFloat($refuelPriceTotal.val()) || 0,
                        $refuelExchangeToUsdPriceTotal = $row.find('.refuelExchangeToUsdPriceTotal'),
                        refuelExchangeToUsdPriceTotalVal = parseFloat($refuelExchangeToUsdPriceTotal.val()) || 0,
                    //subTotals
                        $refuelPriceSubTotal = $form.find('.refuelPriceSubTotal'),
                        refuelPriceSubTotalVal = 0,
                        $refuelCurrencyForSubTotal = $form.find('.refuelCurrency'),
                        refuelCurrencyName = '',
                        $refuelExchangeToUsdPriceSubTotal = $form.find('.refuelExchangeToUsdPriceSubTotal'),
                        refuelExchangeToUsdPriceSubTotalVal = 0;

                    // пересчитываем литры в юниты и юниты в литры
                    if (($this.hasClass('refuelQuantityLtr') || $this.hasClass('refuelUnitId')) && refuelUnitIdText != '') {
                        $refuelQuantityOtherUnits.val(convertRefuelQuantityLtr2OtherUnits(refuelQuantityLtrVal, refuelUnitIdText));
                    }

                    // считаем тоталы
                    refuelPriceVal = ((refuelItemPriceVal + refuelTaxVal + refuelMotVal) * ((refuelVatVal + 100 ) / 100)).toFixed(4);
                    $refuelPrice.val(refuelPriceVal);

                    refuelPriceTotalVal = (((refuelQuantityOtherUnitsVal * refuelPriceVal)) + refuelDeliverVal).toFixed(2);
                    $refuelPriceTotal.val(refuelPriceTotalVal);

                    refuelExchangeToUsdPriceTotalVal = (refuelPriceTotalVal * invoiceExchangeRateVal).toFixed(2);
                    $refuelExchangeToUsdPriceTotal.val(refuelExchangeToUsdPriceTotalVal);

                    // отрисоваем сумму под таблицей
                    $form.find('.refuelPriceTotal').each(function () {
                        var val = $(this).val();
                        if (!isNaN(val) && val != '') {
                            refuelPriceSubTotalVal = parseFloat(refuelPriceSubTotalVal) + parseFloat(val);
                        }
                    });
                    $refuelPriceSubTotal.text(refuelPriceSubTotalVal.toFixed(2));

                    $form.find('.refuelExchangeToUsdPriceTotal').each(function () {
                        var val = $(this).val();
                        if (!isNaN(val) && val != '') {
                            refuelExchangeToUsdPriceSubTotalVal = parseFloat(refuelExchangeToUsdPriceSubTotalVal) + parseFloat(val);
                        }
                    });
                    $refuelExchangeToUsdPriceSubTotal.text(refuelExchangeToUsdPriceSubTotalVal.toFixed(2));
                });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.refuelOutcomeInvoiceStep1 = {
        attach: function (context, settings) {
            var $form = $('form#refuelOutcomeInvoiceStep1');

            if ($form.length == 0) return;

            $($form).find('#dateFrom, #dateTo').mask('99-99-9999');

            var $form2 = $('form#refuelOutcomeInvoiceStep2'),
                $rowsCheckbox = $form2.find('.refuelsSelected');

            if ($form2.length == 0) return;

            // блокируем кнопку Apply до тех пор, пока не будут выбраны один или несколько одинаковых Customer
            $form2.find('.btn').prop('disabled', true);
            $rowsCheckbox.change(function () {
                var selectedCustomers = [];

                $rowsCheckbox.each(function () {
                    var $this = $(this);
                    if ($this.prop('checked')) {
                        selectedCustomers.push($this.parent().parent().find('.incomeInvoiceCustomer').text());
                    }
                });

                selectedCustomers.sort();
                var i = selectedCustomers.length, result = [];
                while (i--) {
                    if (result.join('').search(selectedCustomers[i]) == '-1') {
                        result.push(selectedCustomers[i]);
                    }
                }
                if (result.length == 1) {
                    $form2.find('.btn').prop('disabled', false);
                } else {
                    $form2.find('.btn').prop('disabled', true);
                }
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.refuelOutcomeInvoiceStep3 = {
        attach: function (context, settings) {
            var $form = $('form#refuelOutcomeInvoiceStep3');

            if ($form.length == 0) return;

            $($form).find('#invoiceNumber').mask('9999-99999999');
            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceCurrency = $form.find('#invoiceCurrency'),
                $invoiceExchangeRate = $form.find('#invoiceExchangeRate'),
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text() || 'USD',
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            // Блокирум поля при инициилизации
            $($form).find('#invoiceData input, #invoiceData select').each(function () {
                $(this).prop('disabled', true).trigger('chosen:updated');
            });

            //Если Currency == USD, то значение поля Exchange Rate == 1
            $invoiceCurrency.change(function () {
                var value = $(this).val();
                if (value == 'USD') {
                    $invoiceExchangeRate.val(1);
                }
                $('.refuelCurrency').text(value);
            });

            // После Apply курса валют - разблокируем поля
            $('#rateApply').click(function () {
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text();
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val());
                $($form).find('#invoiceCurrency, #invoiceExchangeRate').each(function () {
                    $(this).prop('readonly', true).trigger('chosen:updated');
                });
                $('#rateApply').prop('disabled', true);
                $invoiceCurrency.parent().html('<input type="hidden" name="invoiceCurrency" value="' + invoiceCurrencyText + '"/>'
                    + invoiceCurrencyText);

                $($form).find('#invoiceData input, #invoiceData select').each(function () {
                    $(this).prop('disabled', false).trigger('chosen:updated');
                });
                return false;
            });


            $('.quantityLtr, .quantityOtherUnits, .unitId, .itemPrice, .tax, .mot, .vat, .deliver').bind("keyup change", function () {
                var $this = $(this),
                    $row = $(this).parent().parent(),
                //fields values
                    $quantityLtr = $row.find('.quantityLtr'),
                    quantityLtrVal = parseFloat($quantityLtr.val()) || 0,
                    $quantityOtherUnits = $row.find('.quantityOtherUnits'),
                    quantityOtherUnitsVal = parseFloat($quantityOtherUnits.val()) || 0,
                    $unitId = $row.find('.unitId'),
                    unitIdText = $unitId.find(':selected').text() || '',
                    $itemPrice = $row.find('.itemPrice'),
                    itemPriceVal = parseFloat($itemPrice.val()) || 0,
                    $tax = $row.find('.tax'),
                    taxVal = parseFloat($tax.val()) || 0,
                    $mot = $row.find('.mot'),
                    motVal = parseFloat($mot.val()) || 0,
                    $vat = $row.find('.vat'),
                    vatVal = parseFloat($vat.val()) || 0,
                    $deliver = $row.find('.deliver'),
                    deliverVal = parseFloat($deliver.val()) || 0,
                    $price = $row.find('.price'),
                    priceVal = 0,
                    $priceTotal = $row.find('.priceTotal'),
                    priceTotalVal = 0,
                    $priceTotalExchangedToUsd = $row.find('.priceTotalExchangedToUsd'),
                    priceTotalExchangedToUsdVal = 0,
                //subTotals
                    $priceSubTotal = $form.find('.priceSubTotal'),
                    priceSubTotalVal = 0,
                    $exchangeToUsdPriceSubTotal = $form.find('.exchangeToUsdPriceSubTotal'),
                    exchangeToUsdPriceSubTotalVal = 0;

                // пересчитываем литры в юниты и юниты в литры
                if (($this.hasClass('quantityLtr') || $this.hasClass('unitId')) && unitIdText != '') {
                    $quantityOtherUnits.val(convertRefuelQuantityLtr2OtherUnits(quantityLtrVal, unitIdText));
                }

                // считаем тоталы
                priceVal = ((itemPriceVal + taxVal + motVal) * ((vatVal + 100 ) / 100)).toFixed(4);
                $price.val(priceVal);

                priceTotalVal = (((quantityOtherUnitsVal * priceVal)) + deliverVal).toFixed(2);
                $priceTotal.val(priceTotalVal);

                priceTotalExchangedToUsdVal = (priceTotalVal * invoiceExchangeRateVal).toFixed(2);
                $priceTotalExchangedToUsd.val(priceTotalExchangedToUsdVal);

                // отрисоваем сумму под таблицей
                $form.find('.priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalVal = parseFloat(priceSubTotalVal) + parseFloat(val);
                    }
                });
                $priceSubTotal.text(priceSubTotalVal.toFixed(2));

                $form.find('.priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        exchangeToUsdPriceSubTotalVal = parseFloat(exchangeToUsdPriceSubTotalVal) + parseFloat(val);
                    }
                });
                $exchangeToUsdPriceSubTotal.text(exchangeToUsdPriceSubTotalVal.toFixed(2));
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.refuelOutcomeInvoiceStep4 = {
        attach: function (context, settings) {
            var $form = $('form#refuelOutcomeInvoiceStep4');

            if ($form.length == 0) return;

            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceExchangeRate = $form.find('#invoiceExchangeRate'),
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            $('.quantityLtr, .quantityOtherUnits, .unitId, .itemPrice, .tax, .mot, .vat, .deliver').bind("keyup change", function () {
                var $this = $(this),
                    $row = $(this).parent().parent(),
                //fields values
                    $quantityLtr = $row.find('.quantityLtr'),
                    quantityLtrVal = parseFloat($quantityLtr.val()) || 0,
                    $quantityOtherUnits = $row.find('.quantityOtherUnits'),
                    quantityOtherUnitsVal = parseFloat($quantityOtherUnits.val()) || 0,
                    $unitId = $row.find('.unitId'),
                    unitIdText = $unitId.find(':selected').text() || '',
                    $itemPrice = $row.find('.itemPrice'),
                    itemPriceVal = parseFloat($itemPrice.val()) || 0,
                    $tax = $row.find('.tax'),
                    taxVal = parseFloat($tax.val()) || 0,
                    $mot = $row.find('.mot'),
                    motVal = parseFloat($mot.val()) || 0,
                    $vat = $row.find('.vat'),
                    vatVal = parseFloat($vat.val()) || 0,
                    $deliver = $row.find('.deliver'),
                    deliverVal = parseFloat($deliver.val()) || 0,
                    $price = $row.find('.price'),
                    priceVal = 0,
                    $priceTotal = $row.find('.priceTotal'),
                    priceTotalVal = 0,
                    $priceTotalExchangedToUsd = $row.find('.priceTotalExchangedToUsd'),
                    priceTotalExchangedToUsdVal = 0,
                //subTotals
                    $priceSubTotal = $form.find('.priceSubTotal'),
                    priceSubTotalVal = 0,
                    $exchangeToUsdPriceSubTotal = $form.find('.exchangeToUsdPriceSubTotal'),
                    exchangeToUsdPriceSubTotalVal = 0;

                // пересчитываем литры в юниты и юниты в литры
                if (($this.hasClass('quantityLtr') || $this.hasClass('unitId')) && unitIdText != '') {
                    $quantityOtherUnits.val(convertRefuelQuantityLtr2OtherUnits(quantityLtrVal, unitIdText));
                }

                // считаем тоталы
                priceVal = ((itemPriceVal + taxVal + motVal) * ((vatVal + 100 ) / 100)).toFixed(4);
                $price.val(priceVal);

                priceTotalVal = (((quantityOtherUnitsVal * priceVal)) + deliverVal).toFixed(2);
                $priceTotal.val(priceTotalVal);

                priceTotalExchangedToUsdVal = (priceTotalVal * invoiceExchangeRateVal).toFixed(2);
                $priceTotalExchangedToUsd.val(priceTotalExchangedToUsdVal);

                // отрисоваем сумму под таблицей
                $form.find('.priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalVal = parseFloat(priceSubTotalVal) + parseFloat(val);
                    }
                });
                $priceSubTotal.text(priceSubTotalVal.toFixed(2));

                $form.find('.priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        exchangeToUsdPriceSubTotalVal = parseFloat(exchangeToUsdPriceSubTotalVal) + parseFloat(val);
                    }
                });
                $exchangeToUsdPriceSubTotal.text(exchangeToUsdPriceSubTotalVal.toFixed(2));
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.apServiceIncomeInvoiceStep1 = {
        attach: function (context, settings) {
            var $form = $('form#apServiceIncomeInvoiceStep1');

            if ($form.length == 0) return;

            $($form).find('#dateFrom, #dateTo').mask('99-99-9999');

            var $form2 = $('form#apServiceIncomeInvoiceStep2'),
                $rowsCheckbox = $form2.find('.rowsSelected');

            if ($form2.length == 0) return;

            // блокируем кнопку Apply до тех пор, пока не будет выбран только один чекбокс
            $form2.find('.btn').prop('disabled', true);
            $rowsCheckbox.change(function () {
                var selectedRows = [];

                $rowsCheckbox.each(function () {
                    var $this = $(this);
                    if ($this.prop('checked')) {
                        selectedRows.push($this.val());
                    }
                });

                if (selectedRows.length == 1) {
                    $form2.find('.btn').prop('disabled', false);
                } else {
                    $form2.find('.btn').prop('disabled', true);
                }
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.apServiceIncomeInvoiceStep3 = {
        attach: function (context, settings) {
            var $form = $('form#apServiceIncomeInvoiceStep3');

            if ($form.length == 0) return;

            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceCurrency = $form.find('#currency'),
                $invoiceExchangeRate = $form.find('#exchangeRate'),
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text() || 'USD',
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            // Добавляем строки.
            $('.addRow').click(function () {
                var $hiddenRows = $('#invoiceData').find('.dataRow.hidden');
                if ($hiddenRows.length) {
                    $hiddenRows.first().removeClass('hidden').find('.chosen-container').css({'width': '210px'}).trigger('chosen:updated');
                } else {
                    $(this).prop('disabled', true)
                }
                return false;
            });

            // Удаляем строки
            $('.removeRow').click(function () {
                $(this).parent().parent().remove();
                return false;
            });

            // Блокирум поля при инициилизации
            $($form).find('#invoiceData input, #invoiceData select').each(function () {
                $(this).prop('disabled', true).trigger('chosen:updated');
            });

            //Если Currency == USD, то значение поля Exchange Rate == 1
            $invoiceCurrency.change(function () {
                var value = $(this).val();
                if (value == 'USD') {
                    $invoiceExchangeRate.val(1);
                }
                $('.currency').text(value);
            });

            // После Apply курса валют - разблокируем поля
            $('#rateApply').click(function () {
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text();
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val());
                $($form).find('#currency, #exchangeRate').each(function () {
                    $(this).prop('readonly', true).trigger('chosen:updated');
                });
                $('#rateApply').prop('disabled', true);
                $invoiceCurrency.parent().html('<input type="hidden" name="currency" value="' + invoiceCurrencyText + '"/>'
                    + invoiceCurrencyText);

                $($form).find('#invoiceData input, #invoiceData select').each(function () {
                    $(this).prop('disabled', false).trigger('chosen:updated');
                });
                return false;
            });

            $('.itemPrice, .quantity').bind("keyup change", function () {
                var $this = $(this),
                    $row = $(this).parent().parent(),
                //fields values
                    $itemPrice = $row.find('.itemPrice'),
                    itemPriceVal = $itemPrice.val() || 0,
                    $quantity = $row.find('.quantity'),
                    quantityVal = $quantity.val() || 0,
                    $priceTotal = $row.find('.priceTotal'),
                    priceTotalVal = $priceTotal.val() || 0,
                    $priceTotalExchangedToUsd = $row.find('.priceTotalExchangedToUsd'),
                    priceTotalExchangedToUsdVal = $priceTotalExchangedToUsd.val() || 0,
                //subTotals
                    $priceSubTotal = $form.find('.priceSubTotal'),
                    priceSubTotalVal = 0,
                    $priceSubTotalExchangedToUsd = $form.find('.priceSubTotalExchangedToUsd'),
                    priceSubTotalExchangedToUsdVal = 0;

                // считаем тоталы
                priceTotalVal = (itemPriceVal * quantityVal).toFixed(4);
                $priceTotal.val(priceTotalVal);

                priceTotalExchangedToUsdVal = (priceTotalVal * invoiceExchangeRateVal).toFixed(2);
                $priceTotalExchangedToUsd.val(priceTotalExchangedToUsdVal);

                // отрисоваем сумму под таблицей
                $form.find('.priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalVal = parseFloat(priceSubTotalVal) + parseFloat(val);
                    }
                });
                $priceSubTotal.text(priceSubTotalVal.toFixed(4));

                $form.find('.priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalExchangedToUsdVal = parseFloat(priceSubTotalExchangedToUsdVal)
                            + parseFloat(val);
                    }
                });
                $priceSubTotalExchangedToUsd.text(priceSubTotalExchangedToUsdVal.toFixed(2));
            });

            // По клику на сабмит прибиваем все скрытые строки
            $('.btn-primary').click(function () {
                $('#invoiceData').find('.dataRow.hidden').remove();
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.apServiceIncomeInvoiceStep4 = {
        attach: function (context, settings) {
            var $form = $('form#apServiceIncomeInvoiceStep4');

            if ($form.length == 0) return;

            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceCurrency = $form.find('#currency'),
                $invoiceExchangeRate = $form.find('#exchangeRate'),
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            $('.itemPrice, .quantity').bind("keyup change", function () {
                var $this = $(this),
                    $row = $(this).parent().parent(),
                //fields values
                    $itemPrice = $row.find('.itemPrice'),
                    itemPriceVal = $itemPrice.val() || 0,
                    $quantity = $row.find('.quantity'),
                    quantityVal = $quantity.val() || 0,
                    $priceTotal = $row.find('.priceTotal'),
                    priceTotalVal = $priceTotal.val() || 0,
                    $priceTotalExchangedToUsd = $row.find('.priceTotalExchangedToUsd'),
                    priceTotalExchangedToUsdVal = $priceTotalExchangedToUsd.val() || 0,
                //subTotals
                    $priceSubTotal = $form.find('.priceSubTotal'),
                    priceSubTotalVal = 0,
                    $priceSubTotalExchangedToUsd = $form.find('.priceSubTotalExchangedToUsd'),
                    priceSubTotalExchangedToUsdVal = 0;

                // считаем тоталы
                priceTotalVal = (itemPriceVal * quantityVal).toFixed(4);
                $priceTotal.val(priceTotalVal);

                priceTotalExchangedToUsdVal = (priceTotalVal * invoiceExchangeRateVal).toFixed(2);
                $priceTotalExchangedToUsd.val(priceTotalExchangedToUsdVal);

                // отрисоваем сумму под таблицей
                $form.find('.priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalVal = parseFloat(priceSubTotalVal) + parseFloat(val);
                    }
                });
                $priceSubTotal.text(priceSubTotalVal.toFixed(4));

                $form.find('.priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalExchangedToUsdVal = parseFloat(priceSubTotalExchangedToUsdVal)
                            + parseFloat(val);
                    }
                });
                $priceSubTotalExchangedToUsd.text(priceSubTotalExchangedToUsdVal.toFixed(2));
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.apServiceOutcomeInvoiceStep1 = {
        attach: function (context, settings) {
            var $form = $('form#apServiceOutcomeInvoiceStep1');

            if ($form.length == 0) return;

            $($form).find('#dateFrom, #dateTo').mask('99-99-9999');

            var $form2 = $('form#apServiceOutcomeInvoiceStep2'),
                $rowsCheckbox = $form2.find('.rowsSelected');

            if ($form2.length == 0) return;

            // блокируем кнопку Apply до тех пор, пока не будет выбран только один чекбокс
            $form2.find('.btn').prop('disabled', true);
            $rowsCheckbox.change(function () {
                var selectedRows = [];

                $rowsCheckbox.each(function () {
                    var $this = $(this);
                    if ($this.prop('checked')) {
                        selectedRows.push($this.val());
                    }
                });

                if (selectedRows.length == 1) {
                    $form2.find('.btn').prop('disabled', false);
                } else {
                    $form2.find('.btn').prop('disabled', true);
                }
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.apServiceOutcomeInvoiceStep3 = {
        attach: function (context, settings) {
            var $form = $('form#apServiceOutcomeInvoiceStep3');

            if ($form.length == 0) return;

            $($form).find('#number').mask('9999-99999999');
            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceCurrency = $form.find('#currency'),
                $invoiceExchangeRate = $form.find('#exchangeRate'),
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text() || 'USD',
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            // Добавляем строки.
            $('.addRow').click(function () {
                var $hiddenRows = $('#invoiceData').find('.dataRow.hidden');
                if ($hiddenRows.length) {
                    $hiddenRows.first().removeClass('hidden').find('.chosen-container').css({'width': '210px'}).trigger('chosen:updated');
                } else {
                    $(this).prop('disabled', true)
                }
                return false;
            });
            $('.addSubRow').click(function () {
                var $hiddenSubRows = $('#invoiceData').find('.subDataRow.hidden');
                if ($hiddenSubRows.length) {
                    $hiddenSubRows.first().removeClass('hidden').find('.chosen-container').css({'width': '210px'}).trigger('chosen:updated');
                } else {
                    $(this).prop('disabled', true)
                }
                return false;
            });

            // Удаляем строки
            $('.removeRow').click(function () {
                $(this).parent().parent().remove();
                return false;
            });

            // Блокирум поля при инициилизации
            $($form).find('#invoiceData input, #invoiceData select').each(function () {
                $(this).prop('disabled', true).trigger('chosen:updated');
            });

            //Если Currency == USD, то значение поля Exchange Rate == 1
            $invoiceCurrency.change(function () {
                var value = $(this).val();
                if (value == 'USD') {
                    $invoiceExchangeRate.val(1);
                }
                $('.currency').text(value);
            });

            // После Apply курса валют - разблокируем поля
            $('#rateApply').click(function () {
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text();
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val());
                $($form).find('#currency, #exchangeRate').each(function () {
                    $(this).prop('readonly', true).trigger('chosen:updated');
                });
                $('#rateApply').prop('disabled', true);
                $invoiceCurrency.parent().html('<input type="hidden" name="currency" value="' + invoiceCurrencyText + '"/>'
                    + invoiceCurrencyText);

                $($form).find('#invoiceData input, #invoiceData select').each(function () {
                    $(this).prop('disabled', false).trigger('chosen:updated');
                });
                return false;
            });

            $('.itemPrice, .quantity, .disbursement').bind("keyup change", function () {
                var $this = $(this),
                    $row = $(this).parent().parent(),
                //fields values
                    $itemPrice = $row.find('.itemPrice'),
                    itemPriceVal = $itemPrice.val() || 0,
                    $quantity = $row.find('.quantity'),
                    quantityVal = $quantity.val() || 0,
                    $priceTotal = $row.find('.priceTotal'),
                    priceTotalVal = $priceTotal.val() || 0,
                    $priceTotalExchangedToUsd = $row.find('.priceTotalExchangedToUsd'),
                    priceTotalExchangedToUsdVal = $priceTotalExchangedToUsd.val() || 0,
                //disbursementTotals
                    $disbursement = $form.find('.disbursement'),
                    disbursementVal = $disbursement.val() || 0,
                    $disbursementTotal = $form.find('.disbursementTotal'),
                    disbursementTotalVal = $disbursementTotal.val() || 0,
                    $disbursementTotalExchangedToUsd = $form.find('.disbursementTotalExchangedToUsd'),
                    disbursementTotalExchangedToUsdVal = $disbursementTotalExchangedToUsd.val() || 0,
                //subTotals
                    $priceSubTotal = $form.find('.priceSubTotal'),
                    priceSubTotalVal = 0,
                    $priceSubTotalExchangedToUsd = $form.find('.priceSubTotalExchangedToUsd'),
                    priceSubTotalExchangedToUsdVal = 0,
                // окончательные subSubTotals
                    $subPriceSubTotal = $form.find('.subPriceSubTotal'),
                    subPriceSubTotalVal = $subPriceSubTotal.val() || 0,
                    $subPriceSubTotalExchangedToUsd = $form.find('.subPriceSubTotalExchangedToUsd'),
                    subPriceSubTotalExchangedToUsdVal = $subPriceSubTotalExchangedToUsd.val() || 0;

                // считаем тоталы
                priceTotalVal = (parseFloat(itemPriceVal) * parseFloat(quantityVal)).toFixed(4);
                $priceTotal.val(priceTotalVal);

                priceTotalExchangedToUsdVal = (parseFloat(priceTotalVal) * parseFloat(invoiceExchangeRateVal)).toFixed(2);
                $priceTotalExchangedToUsd.val(priceTotalExchangedToUsdVal);

                // считаем сумму под таблицей
                $form.find('.dataRow .priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalVal = parseFloat(priceSubTotalVal) + parseFloat(val);
                    }
                });
                $priceSubTotal.text(priceSubTotalVal.toFixed(4));

                $form.find('.dataRow .priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalExchangedToUsdVal = parseFloat(priceSubTotalExchangedToUsdVal)
                            + parseFloat(val);
                    }
                });
                $priceSubTotalExchangedToUsd.text(priceSubTotalExchangedToUsdVal.toFixed(2));

                // считаем disbursement total
                disbursementTotalVal = (parseFloat(priceSubTotalVal) * parseFloat(disbursementVal) / 100);
                $disbursementTotal.val(disbursementTotalVal.toFixed(4));
                disbursementTotalExchangedToUsdVal = (parseFloat(priceSubTotalExchangedToUsdVal) * parseFloat(disbursementVal) / 100);
                $disbursementTotalExchangedToUsd.val(disbursementTotalExchangedToUsdVal.toFixed(2));

                // считаем окончательные тоталы (subTotals & disbursement total)
                $form.find('.subDataRow .priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        subPriceSubTotalVal = parseFloat(subPriceSubTotalVal) + parseFloat(val);
                    }
                });
                $subPriceSubTotal.text((subPriceSubTotalVal + disbursementTotalVal + priceSubTotalVal).toFixed(4));

                $form.find('.subDataRow .priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        subPriceSubTotalExchangedToUsdVal = parseFloat(subPriceSubTotalExchangedToUsdVal) + parseFloat(val);
                    }
                });
                $subPriceSubTotalExchangedToUsd.text((subPriceSubTotalExchangedToUsdVal
                    + disbursementTotalExchangedToUsdVal + priceSubTotalExchangedToUsdVal).toFixed(2));
            });

            // По клику на сабмит прибиваем все скрытые строки
            $('.btn-primary').click(function () {
                $('#invoiceData').find('.dataRow.hidden, .subDataRow.hidden').remove();
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.apServiceOutcomeInvoiceStep4 = {
        attach: function (context, settings) {
            var $form = $('form#apServiceOutcomeInvoiceStep4');

            if ($form.length == 0) return;

            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceCurrency = $form.find('#currency'),
                $invoiceExchangeRate = $form.find('#exchangeRate'),
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text() || 'USD',
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            $('.itemPrice, .quantity, .disbursement').bind("keyup change", function () {
                var $this = $(this),
                    $row = $(this).parent().parent(),
                //fields values
                    $itemPrice = $row.find('.itemPrice'),
                    itemPriceVal = $itemPrice.val() || 0,
                    $quantity = $row.find('.quantity'),
                    quantityVal = $quantity.val() || 0,
                    $priceTotal = $row.find('.priceTotal'),
                    priceTotalVal = $priceTotal.val() || 0,
                    $priceTotalExchangedToUsd = $row.find('.priceTotalExchangedToUsd'),
                    priceTotalExchangedToUsdVal = $priceTotalExchangedToUsd.val() || 0,
                //disbursementTotals
                    $disbursement = $form.find('.disbursement'),
                    disbursementVal = $disbursement.val() || 0,
                    $disbursementTotal = $form.find('.disbursementTotal'),
                    disbursementTotalVal = $disbursementTotal.val() || 0,
                    $disbursementTotalExchangedToUsd = $form.find('.disbursementTotalExchangedToUsd'),
                    disbursementTotalExchangedToUsdVal = $disbursementTotalExchangedToUsd.val() || 0,
                //subTotals
                    $priceSubTotal = $form.find('.priceSubTotal'),
                    priceSubTotalVal = 0,
                    $priceSubTotalExchangedToUsd = $form.find('.priceSubTotalExchangedToUsd'),
                    priceSubTotalExchangedToUsdVal = 0,
                // окончательные subSubTotals
                    $subPriceSubTotal = $form.find('.subPriceSubTotal'),
                    subPriceSubTotalVal = $subPriceSubTotal.val() || 0,
                    $subPriceSubTotalExchangedToUsd = $form.find('.subPriceSubTotalExchangedToUsd'),
                    subPriceSubTotalExchangedToUsdVal = $subPriceSubTotalExchangedToUsd.val() || 0;

                // считаем тоталы
                priceTotalVal = (parseFloat(itemPriceVal) * parseFloat(quantityVal)).toFixed(4);
                $priceTotal.val(priceTotalVal);

                priceTotalExchangedToUsdVal = (parseFloat(priceTotalVal) * parseFloat(invoiceExchangeRateVal)).toFixed(2);
                $priceTotalExchangedToUsd.val(priceTotalExchangedToUsdVal);

                // считаем сумму под таблицей
                $form.find('.dataRow .priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalVal = parseFloat(priceSubTotalVal) + parseFloat(val);
                    }
                });
                $priceSubTotal.text(priceSubTotalVal.toFixed(4));

                $form.find('.dataRow .priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalExchangedToUsdVal = parseFloat(priceSubTotalExchangedToUsdVal)
                            + parseFloat(val);
                    }
                });
                $priceSubTotalExchangedToUsd.text(priceSubTotalExchangedToUsdVal.toFixed(2));

                // считаем disbursement total
                disbursementTotalVal = (parseFloat(priceSubTotalVal) * parseFloat(disbursementVal) / 100);
                $disbursementTotal.val(disbursementTotalVal.toFixed(4));
                disbursementTotalExchangedToUsdVal = (parseFloat(priceSubTotalExchangedToUsdVal) * parseFloat(disbursementVal) / 100);
                $disbursementTotalExchangedToUsd.val(disbursementTotalExchangedToUsdVal.toFixed(2));

                // считаем окончательные тоталы (subTotals & disbursement total)
                $form.find('.subDataRow .priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        subPriceSubTotalVal = parseFloat(subPriceSubTotalVal) + parseFloat(val);
                    }
                });
                $subPriceSubTotal.text((subPriceSubTotalVal + disbursementTotalVal + priceSubTotalVal).toFixed(4));

                $form.find('.subDataRow .priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        subPriceSubTotalExchangedToUsdVal = parseFloat(subPriceSubTotalExchangedToUsdVal) + parseFloat(val);
                    }
                });
                $subPriceSubTotalExchangedToUsd.text((subPriceSubTotalExchangedToUsdVal
                    + disbursementTotalExchangedToUsdVal + priceSubTotalExchangedToUsdVal).toFixed(2));
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.permissionIncomeInvoiceStep1 = {
        attach: function (context, settings) {
            var $form = $('form#permissionIncomeInvoiceStep1');

            if ($form.length == 0) return;

            $($form).find('#dateFrom, #dateTo').mask('99-99-9999');

            var $form2 = $('form#permissionIncomeInvoiceStep2'),
                $rowsCheckbox = $form2.find('.rowsSelected');

            if ($form2.length == 0) return;

            // блокируем кнопку Apply до тех пор, пока не будут выбраны один или несколько одинаковых Agent
            $form2.find('.btn').prop('disabled', true);
            $rowsCheckbox.change(function () {
                var selectedAgents = [];

                $rowsCheckbox.each(function () {
                    var $this = $(this);
                    if ($this.prop('checked')) {
                        selectedAgents.push($this.parent().parent().find('.preInvoiceAgentShortName').text());
                    }
                });

                selectedAgents.sort();
                var i = selectedAgents.length, result = [];
                while (i--) {
                    if (result.join('').search(selectedAgents[i]) == '-1') {
                        result.push(selectedAgents[i]);
                    }
                }
                if (result.length == 1) {
                    $form2.find('.btn').prop('disabled', false);
                } else {
                    $form2.find('.btn').prop('disabled', true);
                }
            });
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.permissionIncomeInvoiceStep3 = {
        attach: function (context, settings) {
            var $form = $('form#permissionIncomeInvoiceStep3');

            if ($form.length == 0) return;

            $($form).find('.date').mask('99-99-9999');

            // invoice (header) values
            var $invoiceCurrency = $form.find('#currency'),
                $invoiceExchangeRate = $form.find('#exchangeRate'),
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text() || 'USD',
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val()) || 1;

            // Блокирум поля при инициилизации
            $($form).find('#invoiceData input, #invoiceData select').each(function () {
                $(this).prop('disabled', true).trigger('chosen:updated');
            });

            //Если Currency == USD, то значение поля Exchange Rate == 1
            $invoiceCurrency.change(function () {
                var value = $(this).val();
                if (value == 'USD') {
                    $invoiceExchangeRate.val(1);
                }
                $('.currency').text(value);
            });

            // После Apply курса валют - разблокируем поля
            $('#rateApply').click(function () {
                invoiceCurrencyText = $invoiceCurrency.find(':selected').text();
                invoiceExchangeRateVal = parseFloat($invoiceExchangeRate.val());
                $($form).find('#currency, #exchangeRate').each(function () {
                    $(this).prop('readonly', true).trigger('chosen:updated');
                });
                $('#rateApply').prop('disabled', true);
                $invoiceCurrency.parent().html('<input type="hidden" name="currency" value="' + invoiceCurrencyText + '"/>'
                    + invoiceCurrencyText);

                $($form).find('#invoiceData input, #invoiceData select').each(function () {
                    $(this).prop('disabled', false).trigger('chosen:updated');
                });
                return false;
            });


            $('.itemPrice, .quantity').bind("keyup change", function () {
                var $this = $(this),
                    $row = $(this).parent().parent(),
                //fields values
                    $itemPrice = $row.find('.itemPrice'),
                    itemPriceVal = parseFloat($itemPrice.val()) || 0,
                    $quantity = $row.find('.quantity'),
                    quantityVal = parseFloat($quantity.val()) || 0,
                    $unitId = $row.find('.unitId'),
                    unitIdVal = parseFloat($unitId.val()) || 0,
                    unitIdText = $unitId.find(':selected').text() || '',
                    $priceTotal = $row.find('.priceTotal'),
                    priceTotalVal = parseFloat($priceTotal.val()) || 0,
                    $priceTotalExchangedToUsd = $row.find('.priceTotalExchangedToUsd'),
                    priceTotalExchangedToUsdVal = parseFloat($priceTotalExchangedToUsd.val()) || 0,
                //subTotals
                    $priceSubTotal = $form.find('.priceSubTotal'),
                    priceSubTotalVal = 0,
                    $priceSubTotalExchangedToUsd = $form.find('.priceSubTotalExchangedToUsd'),
                    priceSubTotalExchangedToUsdVal = 0;

                // считаем тоталы
                priceTotalVal = (quantityVal * itemPriceVal).toFixed(2);
                $priceTotal.val(priceTotalVal);

                priceTotalExchangedToUsdVal = (priceTotalVal * invoiceExchangeRateVal).toFixed(2);
                $priceTotalExchangedToUsd.val(priceTotalExchangedToUsdVal);

                // отрисоваем сумму под таблицей
                $form.find('.priceTotal').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalVal = parseFloat(priceSubTotalVal) + parseFloat(val);
                    }
                });
                $priceSubTotal.text(priceSubTotalVal.toFixed(2));

                $form.find('.priceTotalExchangedToUsd').each(function () {
                    var val = $(this).val();
                    if (!isNaN(val) && val != '') {
                        priceSubTotalExchangedToUsdVal = parseFloat(priceSubTotalExchangedToUsdVal)
                            + parseFloat(val);
                    }
                });
                $priceSubTotalExchangedToUsd.text(priceSubTotalExchangedToUsdVal.toFixed(2));
            });
        }
    };
})(jQuery);
