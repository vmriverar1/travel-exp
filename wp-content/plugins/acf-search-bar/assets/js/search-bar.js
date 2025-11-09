(function($){
    function init($form){
        if(!$form || !$form.length) return;

        // INIT SELECT2
        var $select = $form.find('select.asb-select2');
        if ($select.length && $.fn.select2){
            $select.select2({
                width: 'resolve',
                placeholder: $select.data('placeholder') || 'Where...',
                allowClear: true
            });
        }

        // INIT FLATPICKR
        var $date = $form.find('input.asb-date');
        if ($date.length && window.flatpickr){
            var yStart = parseInt($form.data('year-start'), 10) || new Date().getFullYear()-1;
            var yEnd   = parseInt($form.data('year-end'), 10) || new Date().getFullYear()+3;
            flatpickr($date.get(0), {
                altInput: true,
                altFormat: 'd M Y',
                dateFormat: 'Y-m-d',
                allowInput: true,
                minDate: new Date(yStart,0,1),
                maxDate: new Date(yEnd,11,31)
            });
        }

        // On submit -> set ?s=
        $form.on('submit', function(ev){
            var destText = '';
            var destVal  = $select.val() || '';
            if (destVal){
                // get selected text (pretty)
                var selected = $select.find('option:selected').text();
                destText = selected ? selected.trim() : destVal;
            }
            var dateVal = $date.val() || '';
            // Build "s": destino + fecha (si hay)
            var s = (destText + ' ' + dateVal).trim();
            if (!s) {
                // prevenir enviar vacío; pero deja ir a ?s= para template de búsqueda limpia
                s = ' ';
            }
            $form.find('input[name=\"s\"]').val(s);
        });
    }

    $(document).on('ready', function(){
        if (window.ASB_DATA && ASB_DATA.formId){
            init($('#' + ASB_DATA.formId));
        } else {
            $('.asb-search-bar').each(function(){ init($(this)); });
        }
    });
})(jQuery);
