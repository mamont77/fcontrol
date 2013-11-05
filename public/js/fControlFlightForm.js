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

    fControl.behaviors.permissionForm = {
        attach: function (context, settings) {
            var $form = $('form#permission'),
                headerId = $form.find('#headerId').val(),
                $agentId = $form.find('#agentId'),
                $legId = $form.find('#legId'),
                $countryId = $form.find('#countryId'),
                $typeOfPermission = $form.find('#typeOfPermission'),
                $agentsList = $form.find('#agentsList'),
                $legsList = $form.find('#legsList'),
                $countriesList = $form.find('#countriesList'),
                $typeOfPermissionsList = $form.find('#typeOfPermissionsList');

            $agentsList.typeahead({
                name: 'agents',
                remote: '/permission/get-agents',
                template: [
                    '<p class="repo-address">{{address}}</p>',
                    '<p class="repo-name">{{name}}</p>',
                    '<p class="repo-mail">{{mail}}</p>'
                ].join(''),
                engine: Hogan
            });

            $agentsList.on("typeahead:selected typeahead:autocompleted", function (e, datum) {
                $agentId.val(datum.id);
            });

            $legsList.typeahead({
                name: 'legs_' + headerId,
                remote: '/permission/get-legs/' + headerId,
                template: [
                    '<p class="repo-address">{{address}}</p>',
                    '<p class="repo-name">{{name}}</p>',
                    '<p class="repo-mail">{{mail}}</p>'
                ].join(''),
                engine: Hogan
            });

            $legsList.on("typeahead:selected typeahead:autocompleted", function (e, datum) {
                $legId.val(datum.id);
            });

            $countriesList.typeahead({
                name: 'countries',
                prefetch: '/permission/get-countries',
                template: [
                    '<p class="repo-code">{{code}}</p>',
                    '<p class="repo-name">{{name}}</p>'
                ].join(''),
                engine: Hogan
            });

            $countriesList.on("typeahead:selected typeahead:autocompleted", function (e, datum) {
                $countryId.val(datum.id);
            });

            $typeOfPermissionsList.typeahead({
                name: 'typeOfPermissions',
                local: [
                    'OFL',
                    'LND',
                    'DG',
                    'DIP'
                ]
            });

            $typeOfPermissionsList.on("typeahead:selected typeahead:autocompleted", function (e, datum) {
                $typeOfPermission.val(datum.value);
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
