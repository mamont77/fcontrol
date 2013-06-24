(function ($) {
    var route;
    var routesForForms = {};

    fControl.behaviors.setRouters = {
        attach:function (context, settings) {
            routesForForms = {
                'region':'admin/libraries/regions',
                'country':'admin/libraries/countries',
                'city':'admin/libraries/cities',
                'airport':'admin/libraries/airports',
                'aircraft_type':'admin/libraries/aircraft_types',
                'aircraft':'admin/libraries/aircrafts',
                'air_operator':'admin/libraries/air_operators',
                'kontragent':'admin/libraries/kontragents',
                'unit':'admin/libraries/units',
                'currency':'admin/libraries/currencies',
                'flightHeader':''
            }
        }
    };

    fControl.behaviors.cancelButton = {
        attach:function (context, settings) {
            for (route in routesForForms) {
                var formId = 'form#' + route;
                if ($(formId).length > 0) {
                    $(formId + ' button.cancel').click(function(){
                        window.location.href = '/' + routesForForms[route];
                    });
                    return true;
                }
            }
        }
    };

})(jQuery);
