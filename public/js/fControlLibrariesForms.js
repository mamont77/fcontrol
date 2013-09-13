(function ($) {
    var route;
    var routesForForms = {};

    fControl.behaviors.setRouters = {
        attach: function (context, settings) {
            routesForForms = {
                'region': 'admin/libraries/regions',
                'country': 'admin/libraries/countries',
                'city': 'admin/libraries/cities',
                'airport': 'admin/libraries/airports',
                'aircraft_type': 'admin/libraries/aircraft_types',
                'aircraft': 'admin/libraries/aircrafts',
                'air_operator': 'admin/libraries/air_operators',
                'kontragent': 'admin/libraries/kontragents',
                'unit': 'admin/libraries/units',
                'currency': 'admin/libraries/currencies',
                'flightHeader': ''
            }
        }
    };

    fControl.behaviors.cancelButton = {
        attach: function (context, settings) {
            for (route in routesForForms) {
                var formId = 'form#' + route;
                if ($(formId).length > 0) {
                    $(formId + ' button.cancel').click(function () {
                        window.location.href = '/' + routesForForms[route];
                    });
                    return true;
                }
            }
        }
    };

    fControl.behaviors.baseOfPermitForm = {
        attach: function (context, settings) {
            var $form = $('form#base_of_permit'),
                countriesField = $form.find('#countryId'),
                airportsField = $form.find('#airports'),
                airportIdField = $form.find('#airportId');

            countriesField.change(function () {
                var currentCountryId = $(this).val();
                airportsField.prop('disabled', true).empty();
                airportIdField.val(0);
                $.getJSON('/admin/libraries/base_of_permit/get-airports/' + currentCountryId, function (data) {
                    airportsField.prop('disabled', false);
                    $.each(data['airports'], function (key, val) {
                        airportsField.append(
                            $('<option></option>').val(key).html(val)
                        );
                    });
                });
            });

            airportsField.change(function () {
                var currentAirportId = $(this).val().split('_');
                currentAirportId = currentAirportId[1];
                airportIdField.val(currentAirportId);
            });
        }
    };

})(jQuery);
