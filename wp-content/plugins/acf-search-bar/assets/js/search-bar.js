(function ($) {
    function init($form) {
        if (!$form || !$form.length) return;

        // INIT SELECT2
        var $select = $form.find('select.asb-select2');
        if ($select.length && $.fn.select2) {
            $select.select2({
                width: 'resolve',
                placeholder: $select.data('placeholder') || 'Where...',
                allowClear: true
            });
        }

        // INIT FLATPICKR
        var $date = $form.find('input.asb-date');
        if ($date.length && window.flatpickr) {
            var yStart = parseInt($form.data('year-start'), 10) || new Date().getFullYear() - 1;
            var yEnd = parseInt($form.data('year-end'), 10) || new Date().getFullYear() + 3;
            flatpickr($date.get(0), {
                mode: 'single',
                altInput: true,
                altFormat: 'd M Y',
                dateFormat: 'Y-m-d',
                allowInput: true,
                minDate: new Date(yStart, 0, 1),
                maxDate: new Date(yEnd, 11, 31)
            });
        }

        // On submit -> preparar datos
        $form.on('submit', function () {
            // fecha elegida (solo 1)
            var date = $date.val() || '';

            // Guardamos en 'date'
            $form.find('input[name="date"]').val(date);

            // NO establecer parámetro s - solo usamos destination y date
            // El parámetro s debe quedar vacío para evitar búsqueda en posts
            $form.find('input[name="s"]').remove();
        });
    }

    $(document).on('ready', function () {
        if (window.ASB_DATA && ASB_DATA.formId) {
            init($('#' + ASB_DATA.formId));
        } else {
            $('.asb-search-bar').each(function () { init($(this)); });
        }
    });
})(jQuery);
