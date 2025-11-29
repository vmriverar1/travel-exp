(function($) {
    'use strict';

    if (typeof acf === 'undefined') {
        return;
    }

    /**
     * Filtrar Locations basado en el Destination seleccionado
     *
     * Cuando el usuario selecciona un Destination (location CPT),
     * filtramos el campo Locations para mostrar solo los locations
     * que comparten la misma taxonomía 'destinations'
     */
    acf.addAction('ready_field/name=destination', function($field) {

        var $destinationField = $field.find('select, input[type="hidden"]');

        $destinationField.on('change', function() {
            var destinationId = $(this).val();

            if (!destinationId) {
                // Si no hay destination seleccionado, mostrar todos los locations
                resetLocationsFilter();
                return;
            }

            // Obtener las taxonomías destinations del location seleccionado
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_location_destinations',
                    location_id: destinationId,
                    nonce: packageLocationFilter.nonce
                },
                beforeSend: function() {
                    // Mostrar loading en el campo locations
                    var $locationsField = $('[data-name="locations"]');
                    $locationsField.addClass('is-loading');
                },
                success: function(response) {
                    if (response.success && response.data.destinations) {
                        filterLocationsByDestinations(response.data.destinations);
                    } else {
                        resetLocationsFilter();
                    }
                },
                complete: function() {
                    var $locationsField = $('[data-name="locations"]');
                    $locationsField.removeClass('is-loading');
                }
            });
        });
    });

    /**
     * Filtrar el campo Locations por las taxonomías destinations
     */
    function filterLocationsByDestinations(destinationTerms) {
        var $locationsField = acf.getField('field_package_locations');

        if (!$locationsField) {
            return;
        }

        // Obtener locations que tienen esas taxonomías destinations
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_locations_by_destinations',
                destination_terms: destinationTerms,
                nonce: packageLocationFilter.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar las opciones del campo locations
                    updateLocationsField($locationsField, response.data.locations);
                }
            }
        });
    }

    /**
     * Actualizar las opciones del campo Locations
     */
    function updateLocationsField($field, locations) {
        // Para ACF post_object con Select2
        var $select = $field.$el().find('select');

        if ($select.length) {
            // Limpiar las opciones actuales excepto las ya seleccionadas
            var selectedValues = $select.val() || [];
            $select.find('option').each(function() {
                var val = $(this).val();
                if (val && selectedValues.indexOf(val) === -1) {
                    $(this).remove();
                }
            });

            // Agregar las nuevas opciones filtradas
            if (locations && locations.length > 0) {
                locations.forEach(function(location) {
                    // Solo agregar si no existe ya
                    if ($select.find('option[value="' + location.ID + '"]').length === 0) {
                        $select.append(new Option(location.post_title, location.ID, false, false));
                    }
                });
            }

            // Refrescar Select2
            $select.trigger('change');
        }
    }

    /**
     * Resetear el filtro de Locations (mostrar todos)
     */
    function resetLocationsFilter() {
        var $locationsField = acf.getField('field_package_locations');

        if (!$locationsField) {
            return;
        }

        // Recargar todas las opciones
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_all_locations',
                nonce: packageLocationFilter.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateLocationsField($locationsField, response.data.locations);
                }
            }
        });
    }

})(jQuery);
