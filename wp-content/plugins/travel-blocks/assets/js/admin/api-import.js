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
        startTime: 0,
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
        $importImages: null,

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
            this.$importImages = $('#import_images');
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

            // Parse IDs with detailed validation
            const parseResult = this.parseIds(input);
            const { validIds, duplicates, invalid } = parseResult;

            if (validIds.length > 0 || duplicates.length > 0 || invalid.length > 0) {
                let previewHtml = '';

                // Valid IDs
                if (validIds.length > 0) {
                    previewHtml += `<span class="preview-valid">✓ ${validIds.length} tour(s) válido(s)</span>`;
                }

                // Duplicates removed
                if (duplicates.length > 0) {
                    previewHtml += ` <span class="preview-warning">⚠ ${duplicates.length} duplicado(s) removido(s): [${duplicates.join(', ')}]</span>`;
                }

                // Invalid IDs
                if (invalid.length > 0) {
                    const invalidPreview = invalid.slice(0, 5).join(', ');
                    const moreCount = invalid.length > 5 ? ` (+${invalid.length - 5} más)` : '';
                    previewHtml += ` <span class="preview-error">✗ ${invalid.length} inválido(s): [${invalidPreview}${moreCount}]</span>`;
                }

                this.$preview.find('.preview-text').html(previewHtml);
                this.$preview.show();
            } else {
                this.$preview.hide();
            }
        },

        /**
         * Parse tour IDs from input with detailed validation
         * Returns: { validIds: [], duplicates: [], invalid: [] }
         */
        parseIds(input) {
            // Split by comma, space, or newline
            const parts = input.split(/[\s,\n]+/).filter(p => p.trim() !== '');

            const validIds = [];
            const duplicates = [];
            const invalid = [];
            const seen = new Set();

            parts.forEach(part => {
                const trimmed = part.trim();

                // Try to parse as integer
                const parsed = parseInt(trimmed, 10);

                // Check if it's a valid positive integer
                if (isNaN(parsed) || parsed <= 0 || trimmed !== String(parsed)) {
                    // Invalid: not a number, negative, or has extra characters
                    invalid.push(trimmed);
                } else if (seen.has(parsed)) {
                    // Duplicate
                    duplicates.push(parsed);
                } else {
                    // Valid and unique
                    validIds.push(parsed);
                    seen.add(parsed);
                }
            });

            return { validIds, duplicates, invalid };
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

            const parseResult = this.parseIds(input);
            const { validIds, duplicates, invalid } = parseResult;

            // Log warnings about duplicates and invalid IDs
            if (duplicates.length > 0) {
                this.log('warning', `Se removieron ${duplicates.length} ID(s) duplicado(s): [${duplicates.join(', ')}]`);
            }

            if (invalid.length > 0) {
                const invalidPreview = invalid.slice(0, 10).join(', ');
                const moreCount = invalid.length > 10 ? ` (+${invalid.length - 10} más)` : '';
                this.log('error', `Se ignoraron ${invalid.length} ID(s) inválido(s): [${invalidPreview}${moreCount}]`);
            }

            if (validIds.length === 0) {
                alert(window.travelApiImport.i18n.invalidIds || 'No se encontraron IDs válidos para importar');
                return;
            }

            this.tourIds = validIds;

            // Reset state
            this.isProcessing = true;
            this.isStopped = false;
            this.currentIndex = 0;
            this.startTime = Date.now();
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
            this.log('info', `Iniciando importación de ${this.tourIds.length} tour(s) válido(s): [${this.tourIds.join(', ')}]`);
            this.log('info', `Actualizar existentes: ${this.$updateExisting.is(':checked') ? 'Sí' : 'No'}`);
            this.log('info', `Importar imágenes: ${this.$importImages.is(':checked') ? 'Sí' : 'No'}`);

            // Start AJAX processing in chunks
            this.processNextChunk();
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
                this.$importImages.prop('disabled', true);
                this.$progressContainer.show();
                this.$statsCard.show();
            } else {
                this.$startBtn.prop('disabled', false).show();
                this.$stopBtn.hide();
                this.$tourIdsInput.prop('disabled', false);
                this.$updateExisting.prop('disabled', false);
                this.$importImages.prop('disabled', false);
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
         * Process next chunk of tour IDs via AJAX
         */
        processNextChunk() {
            if (this.isStopped) {
                this.log('warning', 'Importación detenida por el usuario');
                this.finishImport();
                return;
            }

            // Check if we're done
            if (this.currentIndex >= this.tourIds.length) {
                this.finishImport();
                return;
            }

            // Get next chunk (process 3 at a time to avoid timeouts)
            const chunkSize = 3;
            const chunk = this.tourIds.slice(this.currentIndex, this.currentIndex + chunkSize);

            // Make AJAX request
            $.ajax({
                url: window.travelApiImport.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'travel_api_import_process',
                    nonce: window.travelApiImport.nonce,
                    tour_ids: chunk,
                    update_existing: this.$updateExisting.is(':checked'),
                    import_images: this.$importImages.is(':checked')
                },
                timeout: 120000, // 2 minutes timeout
                success: (response) => {
                    if (response.success && response.data.results) {
                        this.handleResults(response.data.results);
                    } else {
                        this.log('error', `Error en respuesta del servidor: ${response.data?.message || 'Sin mensaje'}`);
                        this.stats.errors += chunk.length;
                    }

                    this.currentIndex += chunk.length;
                    this.updateProgress(this.currentIndex);
                    this.updateStats();

                    // Process next chunk after a small delay
                    setTimeout(() => this.processNextChunk(), 500);
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    this.log('error', `Error AJAX: ${textStatus} - ${errorThrown}`);

                    // Mark all tours in this chunk as errors
                    chunk.forEach(tourId => {
                        this.log('error', `Tour ID ${tourId}: Error de conexión`);
                    });

                    this.stats.errors += chunk.length;
                    this.currentIndex += chunk.length;
                    this.updateProgress(this.currentIndex);
                    this.updateStats();

                    // Continue with next chunk
                    setTimeout(() => this.processNextChunk(), 1000);
                }
            });
        },

        /**
         * Handle results from AJAX response
         */
        handleResults(results) {
            results.forEach(result => {
                const tourId = result.tour_id;
                const status = result.status;
                const message = result.message;

                if (status === 'success') {
                    this.stats.success++;
                    const action = result.action === 'create' ? 'Creado' : 'Actualizado';
                    let logMessage = `✓ Tour ID ${tourId}: ${action} - "${result.title}" (ID: ${result.post_id})`;

                    // Add images count if imported
                    if (result.images_count && result.images_count > 0) {
                        logMessage += ` - ${result.images_count} imagen(es) importada(s)`;
                    }

                    this.log('success', logMessage);
                } else if (status === 'error') {
                    this.stats.errors++;
                    this.log('error', `✗ Tour ID ${tourId}: ${message}`);
                } else if (status === 'skipped') {
                    this.stats.skipped++;
                    this.log('warning', `⊘ Tour ID ${tourId}: ${message}`);
                }
            });
        },

        /**
         * Finish import process
         */
        finishImport() {
            const totalTime = ((Date.now() - this.startTime) / 1000).toFixed(2);

            if (!this.isStopped) {
                this.log('success', `✓ Importación completada en ${totalTime}s: ${this.stats.success} exitosos, ${this.stats.errors} errores, ${this.stats.skipped} omitidos`);
            }

            this.updateUIState('idle');
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
