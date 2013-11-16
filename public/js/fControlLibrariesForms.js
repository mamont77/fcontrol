(function ($) {
    var route,
        routesForForms = {};

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.setRouters = {
        attach: function (context, settings) {
            routesForForms = {
                'aircraft': 'admin/libraries/aircrafts',
                'aircraft_type': 'admin/libraries/aircraft_types',
                'air_operator': 'admin/libraries/air_operators',
                'airport': 'admin/libraries/airports',
                'base_of_permit': 'admin/libraries/base_of_permits',
                'city': 'admin/libraries/cities',
                'country': 'admin/libraries/countries',
                'currency': 'admin/libraries/currencies',
                'kontragent': 'admin/libraries/kontragents',
                'region': 'admin/libraries/regions',
                'type_of_ap_service': 'admin/libraries/type_of_ap_services',
                'unit': 'admin/libraries/units',
                'flightHeader': ''
            }
        }
    };

    /**
     *
     * @type {{attach: Function}}
     */
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

    /**
     *
     * @type {{attach: Function}}
     */
    fControl.behaviors.baseOfPermitForm = {
        attach: function (context, settings) {
            var $form = $('form#base_of_permit');

            if ($form.length == 0) return;

            var countriesField = $form.find('#countryId'),
                airportsField = $form.find('#airports'),
                airportIdField = $form.find('#airportId'),
                ajaxPath = '/admin/libraries/base_of_permit/get-airports/';

            if (countriesField.val() > 0) {
                $.getJSON(ajaxPath + countriesField.val(), function (data) {
                    airportsField.prop('disabled', false);
                    $.each(data['airports'], function (key, val) {
                        airportsField.append(
                            $('<option></option>').val(key).html(val)
                        );
                    });
                    if (airportIdField.val() > 0) {
                        airportsField.val('id_' + airportIdField.val());
                    }
                });
            }

            countriesField.change(function () {
                var currentCountryId = $(this).val();
                airportsField.prop('disabled', true).empty();
                airportIdField.val(0);
                $.getJSON(ajaxPath + currentCountryId, function (data) {
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
