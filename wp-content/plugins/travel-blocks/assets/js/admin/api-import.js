/**
 * API Import Admin JavaScript
 * Phase 1: UI interactions, validation, and preparation for AJAX
 */

(function ($) {
    'use strict';

    /**
     * Main API Import Handler
     */
    const ApiImport = {
        // State
        isProcessing: false,
        isStopped: false,
        tourIds: [],
        currentIndex: 0,
        stats: {
            total: 0,
            success: 0,
            errors: 0,
            skipped: 0
        },

        // DOM Elements (cached)
        $form: null,
        $tourIdsInput: null,
        $preview: null,
        $startBtn: null,
        $stopBtn: null,
        $progressContainer: null,
        $progressFill: null,
        $progressText: null,
        $progressPercentage: null,
        $progressCurrent: null,
        $progressTotal: null,
        $logs: null,
        $statsCard: null,
        $updateExisting: null,

        /**
         * Initialize the handler
         */
        init() {
            this.cacheElements();
            this.bindEvents();
            this.log('info', window.travelApiImport.i18n.validating || 'Sistema listo. Ingresa IDs de tours para comenzar.');
        },

        /**
         * Cache DOM elements
         */
        cacheElements() {
            this.$form = $('#travel-api-import-form');
            this.$tourIdsInput = $('#tour_ids');
            this.$preview = $('.tour-ids-preview');
            this.$startBtn = $('#start_import_btn');
            this.$stopBtn = $('#stop_import_btn');
            this.$progressContainer = $('.progress-container');
            this.$progressFill = $('.progress-fill');
            this.$progressText = $('.progress-text');
            this.$progressPercentage = $('.progress-percentage');
            this.$progressCurrent = $('#progress_current');
            this.$progressTotal = $('#progress_total');
            this.$logs = $('#import_logs');
            this.$statsCard = $('.travel-api-stats');
            this.$updateExisting = $('#update_existing');
        },

        /**
         * Bind event handlers
         */
        bindEvents() {
            // Input validation and preview
            this.$tourIdsInput.on('input', () => this.validateAndPreview());
            this.$tourIdsInput.on('paste', () => {
                setTimeout(() => this.validateAndPreview(), 100);
            });

            // Form submission
            this.$form.on('submit', (e) => {
                e.preventDefault();
                this.startImport();
            });

            // Stop button
            this.$stopBtn.on('click', () => this.stopImport());

            // Clear logs
            $('#clear_logs_btn').on('click', () => this.clearLogs());
        },

        /**
         * Validate input and show preview
         */
        validateAndPreview() {
            const input = this.$tourIdsInput.val().trim();

            if (!input) {
                this.$preview.hide();
                return;
            }

            // Parse IDs (comma, space, or newline separated)
            const ids = this.parseIds(input);

            if (ids.length > 0) {
                const validIds = ids.filter(id => id > 0);
                const invalidCount = ids.length - validIds.length;

                let previewText = `${validIds.length} tour(s) válido(s) para importar`;

                if (invalidCount > 0) {
                    previewText += ` (${invalidCount} inválido(s) ignorado(s))`;
                }

                this.$preview.find('.preview-text').text(previewText);
                this.$preview.show();
            } else {
                this.$preview.hide();
            }
        },

        /**
         * Parse tour IDs from input
         */
        parseIds(input) {
            // Split by comma, space, or newline
            const parts = input.split(/[\s,\n]+/);

            // Convert to integers and filter out invalid
            return parts
                .map(part => parseInt(part.trim(), 10))
                .filter(id => !isNaN(id) && id > 0);
        },

        /**
         * Start the import process
         */
        startImport() {
            // Get and validate IDs
            const input = this.$tourIdsInput.val().trim();

            if (!input) {
                alert(window.travelApiImport.i18n.noIds || 'Por favor ingresa al menos un ID de tour');
                return;
            }

            this.tourIds = this.parseIds(input);

            if (this.tourIds.length === 0) {
                alert(window.travelApiImport.i18n.invalidIds || 'No se encontraron IDs válidos');
                return;
            }

            // Reset state
            this.isProcessing = true;
            this.isStopped = false;
            this.currentIndex = 0;
            this.stats = {
                total: this.tourIds.length,
                success: 0,
                errors: 0,
                skipped: 0
            };

            // Update UI
            this.updateUIState('processing');
            this.updateStats();
            this.updateProgress(0);

            // Log start
            this.log('info', `Iniciando importación de ${this.tourIds.length} tour(s): [${this.tourIds.join(', ')}]`);
            this.log('info', `Actualizar existentes: ${this.$updateExisting.is(':checked') ? 'Sí' : 'No'}`);

            // In Phase 1, we just show the UI
            // Actual AJAX processing will be implemented in later phases
            this.log('warning', 'FASE 1: UI preparada. La lógica de importación se implementará en las siguientes fases.');

            // Simulate processing for demo (remove in later phases)
            this.simulateProcessing();
        },

        /**
         * Stop the import process
         */
        stopImport() {
            if (!this.isProcessing) return;

            this.isStopped = true;
            this.log('warning', 'Importación detenida por el usuario');
            this.updateUIState('idle');
        },

        /**
         * Update UI state
         */
        updateUIState(state) {
            if (state === 'processing') {
                this.$startBtn.prop('disabled', true).hide();
                this.$stopBtn.show();
                this.$tourIdsInput.prop('disabled', true);
                this.$updateExisting.prop('disabled', true);
                this.$progressContainer.show();
                this.$statsCard.show();
            } else {
                this.$startBtn.prop('disabled', false).show();
                this.$stopBtn.hide();
                this.$tourIdsInput.prop('disabled', false);
                this.$updateExisting.prop('disabled', false);
                this.isProcessing = false;
            }
        },

        /**
         * Update progress bar
         */
        updateProgress(current) {
            const total = this.stats.total;
            const percentage = total > 0 ? Math.round((current / total) * 100) : 0;

            this.$progressFill.css('width', percentage + '%');
            this.$progressPercentage.text(percentage + '%');
            this.$progressCurrent.text(current);
            this.$progressTotal.text(total);

            if (percentage === 100) {
                this.$progressText.text('Completado');
            } else if (percentage > 0) {
                this.$progressText.text('Procesando...');
            } else {
                this.$progressText.text('Preparando...');
            }
        },

        /**
         * Update statistics
         */
        updateStats() {
            $('#stat_total').text(this.stats.total);
            $('#stat_success').text(this.stats.success);
            $('#stat_errors').text(this.stats.errors);
            $('#stat_skipped').text(this.stats.skipped);
        },

        /**
         * Add log entry
         */
        log(type, message) {
            const now = new Date();
            const time = now.toTimeString().split(' ')[0];

            let icon = 'dashicons-info';
            if (type === 'success') icon = 'dashicons-yes-alt';
            else if (type === 'error') icon = 'dashicons-dismiss';
            else if (type === 'warning') icon = 'dashicons-warning';

            const $logEntry = $('<div>')
                .addClass('log-entry log-' + type)
                .html(
                    `<span class="log-time">${time}</span>` +
                    `<span class="log-icon dashicons ${icon}"></span>` +
                    `<span class="log-message">${this.escapeHtml(message)}</span>`
                );

            this.$logs.append($logEntry);

            // Auto-scroll to bottom
            this.$logs.scrollTop(this.$logs[0].scrollHeight);
        },

        /**
         * Clear logs
         */
        clearLogs() {
            this.$logs.empty();
            this.log('info', 'Logs limpiados');
        },

        /**
         * Escape HTML for safe display
         */
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        },

        /**
         * Simulate processing for Phase 1 demo
         * This will be replaced with real AJAX calls in later phases
         */
        simulateProcessing() {
            let processed = 0;

            const processNext = () => {
                if (this.isStopped || processed >= this.tourIds.length) {
                    if (!this.isStopped) {
                        this.log('success', `✓ Importación completada: ${this.stats.success} exitosos, ${this.stats.errors} errores, ${this.stats.skipped} omitidos`);
                    }
                    this.updateUIState('idle');
                    return;
                }

                const tourId = this.tourIds[processed];
                processed++;

                // Simulate random result
                const rand = Math.random();
                if (rand > 0.8) {
                    this.stats.errors++;
                    this.log('error', `Tour ID ${tourId}: Error simulado (DEMO)`);
                } else if (rand > 0.6) {
                    this.stats.skipped++;
                    this.log('warning', `Tour ID ${tourId}: Omitido (DEMO)`);
                } else {
                    this.stats.success++;
                    this.log('success', `Tour ID ${tourId}: Importado exitosamente (DEMO)`);
                }

                this.updateProgress(processed);
                this.updateStats();

                // Continue with next
                setTimeout(processNext, 500);
            };

            // Start processing
            setTimeout(processNext, 500);
        }
    };

    // Initialize on document ready
    $(document).ready(function () {
        // Only run on the import page
        if ($('#travel-api-import-form').length > 0) {
            ApiImport.init();
        }
    });

})(jQuery);
