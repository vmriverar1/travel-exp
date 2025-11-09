/**
 * FASE 9A: Aurora Mock Data Wizard - Modal Controller
 *
 * Interactive wizard with real-time progress, checkpoints, and batch processing
 *
 * @package Travel_Package_Wizard
 * @since FASE 9A
 */

(function($) {
    'use strict';

    /**
     * Main Wizard Class
     */
    class MockDataWizard {
        constructor() {
            this.currentStep = 1;
            this.totalSteps = 6;
            this.currentBatch = 0;
            this.totalBatches = 0;
            this.isPaused = false;
            this.isRunning = false;
            this.checkpointData = {};
            this.retryCount = 0;
            this.maxRetries = 3;
            this.errorLog = [];
            this.statistics = {
                startTime: null,
                itemsProcessed: 0,
                errorsCount: 0,
                retriesCount: 0
            };

            // Bind methods
            this.launch = this.launch.bind(this);
            this.processCurrentStep = this.processCurrentStep.bind(this);
            this.processBatch = this.processBatch.bind(this);
            this.togglePause = this.togglePause.bind(this);
            this.cancel = this.cancel.bind(this);
        }

        /**
         * Initialize wizard
         */
        init() {
            const self = this;

            // Launch button click handler
            $(document).on('click', '#launch-wizard-btn', function(e) {
                e.preventDefault();
                self.launch();
            });

            // Check for existing checkpoint on page load
            this.checkForExistingCheckpoint();
        }

        /**
         * Launch the wizard
         */
        launch() {
            console.log('🚀 Launching Mock Data Wizard...');

            // Show confirmation modal first
            this.showConfirmationModal();
        }

        /**
         * Show confirmation modal with cleanup option
         */
        showConfirmationModal() {
            const modalHTML = `
                <div class="aurora-wizard-modal" id="aurora-wizard-confirmation-modal">
                    <div class="aurora-wizard-content" style="max-width: 600px;">
                        <div class="aurora-wizard-header">
                            <h2>🚀 Mock Data Generation Wizard</h2>
                        </div>

                        <div class="aurora-wizard-body" style="padding: 30px;">
                            <div style="text-align: center; margin-bottom: 30px;">
                                <div style="font-size: 64px; margin-bottom: 20px;">⚙️</div>
                                <h3 style="margin: 0 0 15px 0; color: #333;">Ready to Generate Mock Data?</h3>
                                <p style="color: #666; line-height: 1.6;">
                                    The wizard will create packages, images, locations, guides, and more.
                                </p>
                            </div>

                            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                                <div style="display: flex; align-items: flex-start; gap: 15px;">
                                    <div style="font-size: 32px;">⚠️</div>
                                    <div>
                                        <h4 style="margin: 0 0 10px 0; color: #856404;">Existing Data Detected</h4>
                                        <p style="margin: 0 0 15px 0; color: #856404; font-size: 14px;">
                                            You currently have <strong id="existing-packages-count">...</strong> packages in your database.
                                        </p>
                                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; color: #856404;">
                                            <input type="checkbox" id="cleanup-before-start" style="width: 18px; height: 18px;">
                                            <span>🗑️ Delete all existing mock data before starting</span>
                                        </label>
                                        <p style="margin: 10px 0 0 28px; font-size: 12px; color: #856404; font-style: italic;">
                                            Recommended to avoid duplicates
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="aurora-wizard-actions" style="display: flex; gap: 15px; justify-content: center;">
                                <button class="aurora-wizard-btn-secondary" id="wizard-confirm-cancel-btn" style="min-width: 120px;">
                                    Cancel
                                </button>
                                <button class="aurora-wizard-btn-primary" id="wizard-confirm-start-btn" style="min-width: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                    Start Wizard →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modalHTML);

            // Load current package count
            this.loadPackageCount();

            // Attach event handlers
            $('#wizard-confirm-cancel-btn').on('click', () => {
                $('#aurora-wizard-confirmation-modal').remove();
            });

            $('#wizard-confirm-start-btn').on('click', () => {
                this.startWizardProcess();
            });
        }

        /**
         * Load package count for confirmation modal
         */
        async loadPackageCount() {
            try {
                const response = await $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'aurora_wizard_get_stats',
                        nonce: auroraWizardData.nonce
                    }
                });

                if (response.success && response.data.statistics) {
                    const packageCount = response.data.statistics.packages || 0;
                    $('#existing-packages-count').text(packageCount + ' packages');

                    if (packageCount === 0) {
                        $('#cleanup-before-start').prop('checked', false).prop('disabled', true);
                        $('#cleanup-before-start').parent().css('opacity', '0.5');
                    }
                }
            } catch (error) {
                console.error('Error loading package count:', error);
                $('#existing-packages-count').text('some packages');
            }
        }

        /**
         * Start the wizard process (after confirmation)
         */
        async startWizardProcess() {
            const shouldCleanup = $('#cleanup-before-start').is(':checked');

            // Remove confirmation modal
            $('#aurora-wizard-confirmation-modal').remove();

            // Show processing modal
            this.statistics.startTime = Date.now();
            this.showModal();

            // If cleanup is requested, do it first
            if (shouldCleanup) {
                await this.performCleanup();
            }

            // Start normal processing
            this.startProcessing();
        }

        /**
         * Show modal
         */
        showModal() {
            const modalHTML = `
                <div class="aurora-wizard-modal" id="aurora-wizard-modal">
                    <div class="aurora-wizard-content">
                        <div class="aurora-wizard-header">
                            <h2>🚀 Mock Data Generation Wizard</h2>
                            <button class="aurora-wizard-close" id="wizard-close-btn" style="display:none;">×</button>
                        </div>

                        <div class="aurora-wizard-body">
                            <div class="aurora-wizard-step-indicator" id="wizard-step-indicator">
                                Step 1 of 6: Initializing...
                            </div>

                            <div class="aurora-wizard-progress-bar">
                                <div class="aurora-wizard-progress-fill" id="wizard-progress-fill" style="width: 0%;">
                                    0%
                                </div>
                            </div>

                            <p class="aurora-wizard-progress-text" id="wizard-progress-text">
                                Preparing to start...
                            </p>

                            <div class="aurora-wizard-activity-log" id="wizard-activity-log">
                                <div class="aurora-wizard-log-entry">
                                    🔄 Initializing wizard...
                                </div>
                            </div>
                        </div>

                        <div class="aurora-wizard-footer">
                            <div class="aurora-wizard-time-estimate" id="wizard-time-estimate">
                                ⏱️ Calculating time...
                            </div>

                            <div class="aurora-wizard-actions">
                                <button class="aurora-wizard-btn-secondary" id="wizard-pause-btn">Pause</button>
                                <button class="aurora-wizard-btn-secondary" id="wizard-cancel-btn">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modalHTML);

            // Attach event handlers
            $('#wizard-pause-btn').on('click', () => this.togglePause());
            $('#wizard-cancel-btn').on('click', () => this.cancel());
            $('#wizard-close-btn').on('click', () => this.closeModal());
        }

        /**
         * Start processing
         */
        async startProcessing() {
            this.isRunning = true;
            this.addLog('✅ Wizard started', 'success');

            // Update initial progress text
            $('#wizard-progress-text').text('Starting wizard...');

            // Process all steps sequentially
            for (let step = this.currentStep; step <= this.totalSteps; step++) {
                if (!this.isRunning) {
                    this.addLog('⏸️ Wizard cancelled by user', 'error');
                    break;
                }

                this.currentStep = step;
                this.currentBatch = 0;

                console.log(`🔄 Processing step ${step}...`);
                await this.processCurrentStep();

                // Save checkpoint after each step
                this.saveCheckpoint();
            }

            if (this.isRunning) {
                this.showCompletionScreen();
            }
        }

        /**
         * Process current step (all batches)
         */
        async processCurrentStep() {
            // Get step info
            const stepInfo = await this.getStepInfo(this.currentStep);
            this.updateStepIndicator(stepInfo);

            this.addLog(`${stepInfo.icon} Starting: ${stepInfo.title}`, 'info');

            let stepFinished = false;

            while (!stepFinished && this.isRunning) {
                // Wait if paused
                while (this.isPaused && this.isRunning) {
                    await this.sleep(500);
                }

                if (!this.isRunning) break;

                // Process batch with retry logic
                console.log(`📦 Processing batch ${this.currentBatch} of step ${this.currentStep}`);
                const result = await this.processBatchWithRetry();

                console.log('📊 Batch result:', result);

                if (result.success) {
                    this.updateProgress(result);
                    this.retryCount = 0; // Reset retry count on success

                    if (result.step_finished) {
                        stepFinished = true;
                        this.addLog(`✅ Completed: ${stepInfo.title}`, 'success');
                    }

                    this.currentBatch = result.next_batch || this.currentBatch + 1;
                } else {
                    // Handle error after all retries exhausted
                    this.addLog(`❌ Failed after ${this.maxRetries} attempts: ${result.error}`, 'error');
                    this.statistics.errorsCount++;

                    // Ask user what to do
                    const userChoice = await this.showErrorDialog(result.error);

                    if (userChoice === 'skip') {
                        // Skip this batch and continue
                        this.currentBatch++;
                        this.retryCount = 0;
                    } else if (userChoice === 'retry') {
                        // Try again
                        this.retryCount = 0;
                    } else {
                        // Stop wizard
                        this.isRunning = false;
                        stepFinished = true;
                    }
                }
            }
        }

        /**
         * Process single batch via AJAX with retry logic
         */
        async processBatchWithRetry() {
            for (let attempt = 0; attempt < this.maxRetries; attempt++) {
                try {
                    // Add log for retry attempts
                    if (attempt > 0) {
                        this.addLog(`🔄 Retry attempt ${attempt + 1} of ${this.maxRetries}...`, 'info');
                        this.statistics.retriesCount++;
                    }

                    const response = await this.processBatch();

                    if (response.success) {
                        return response;
                    } else {
                        // Server returned error
                        throw new Error(response.error || 'Server returned error');
                    }

                } catch (error) {
                    console.error(`Attempt ${attempt + 1} failed:`, error);

                    // Log detailed error
                    this.logError({
                        timestamp: Date.now(),
                        step: this.currentStep,
                        batch: this.currentBatch,
                        attempt: attempt + 1,
                        error: error.message || error.statusText || 'Unknown error',
                        errorObject: error
                    });

                    // If not last attempt, wait with exponential backoff
                    if (attempt < this.maxRetries - 1) {
                        const backoffTime = Math.min(1000 * Math.pow(2, attempt), 8000); // Max 8 seconds
                        this.addLog(`⏳ Waiting ${backoffTime / 1000}s before retry...`, 'info');
                        await this.sleep(backoffTime);
                    } else {
                        // All retries exhausted
                        return {
                            success: false,
                            error: error.message || error.statusText || 'Unknown error occurred',
                            step_finished: false
                        };
                    }
                }
            }

            // Should never reach here, but just in case
            return {
                success: false,
                error: 'All retry attempts failed',
                step_finished: false
            };
        }

        /**
         * Process single batch via AJAX
         */
        async processBatch() {
            try {
                const response = await $.ajax({
                    url: auroraWizardData.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'aurora_wizard_process_batch',
                        nonce: auroraWizardData.nonce,
                        step: this.currentStep,
                        batch: this.currentBatch,
                        checkpoint_data: this.checkpointData
                    },
                    timeout: 60000 // 60 second timeout
                });

                // wp_send_json_success wraps response in {success: true, data: {...}}
                if (response.success && response.data) {
                    return { success: true, ...response.data };
                }

                return response;

            } catch (error) {
                console.error('AJAX Error:', error);

                let errorMessage = 'Unknown error occurred';
                if (error.statusText === 'timeout') {
                    errorMessage = 'Request timed out. Server may be overloaded.';
                } else if (error.status === 500) {
                    errorMessage = 'Server error (500). Check PHP error logs.';
                } else if (error.status === 403) {
                    errorMessage = 'Permission denied. Check nonce validity.';
                } else if (error.statusText) {
                    errorMessage = error.statusText;
                }

                return {
                    success: false,
                    error: errorMessage,
                    step_finished: false
                };
            }
        }

        /**
         * Update progress UI
         */
        updateProgress(result) {
            // Update progress bar
            if (result.progress) {
                // Calculate percentage if not provided
                let percentage = result.progress.percentage;
                if (!percentage && result.progress.current && result.progress.total) {
                    percentage = Math.floor((result.progress.current / result.progress.total) * 100);
                }
                percentage = percentage || 0;

                $('#wizard-progress-fill')
                    .css('width', percentage + '%')
                    .text(percentage + '%');

                // Update progress text
                const text = `Progress: ${result.progress.current || 0} of ${result.progress.total || 0} items`;
                $('#wizard-progress-text').text(text);
            }

            // Add log entries for created/updated items
            if (result.created_items && result.created_items.length > 0) {
                result.created_items.forEach(item => {
                    this.addLog(`✓ Created: "${item.title}"`, 'success');
                    this.statistics.itemsProcessed++;
                });
            }

            if (result.updated_items && result.updated_items.length > 0) {
                result.updated_items.forEach(item => {
                    const imagesText = item.images_added ? ` (${item.images_added} images)` : '';
                    this.addLog(`✓ Updated: "${item.title}"${imagesText}`, 'success');
                    this.statistics.itemsProcessed++;
                });
            }

            // Add error logs
            if (result.errors && result.errors.length > 0) {
                result.errors.forEach(error => {
                    this.addLog(`✗ Failed: "${error.title}" - ${error.error}`, 'error');
                });
            }

            // Update checkpoint data
            if (result.checkpoint_data) {
                this.checkpointData = { ...this.checkpointData, ...result.checkpoint_data };
            }

            // Update time estimate
            this.updateTimeEstimate();
        }

        /**
         * Update step indicator
         */
        updateStepIndicator(stepInfo) {
            const text = `Step ${this.currentStep} of ${this.totalSteps}: ${stepInfo.title}`;
            $('#wizard-step-indicator').text(text);
        }

        /**
         * Add log entry with animations (FASE 9F enhanced)
         */
        addLog(message, type = 'info') {
            // Remove any existing "processing" entry pulse
            $('.aurora-wizard-log-entry.processing').removeClass('aurora-wizard-pulse');

            const logEntry = $('<div>')
                .addClass('aurora-wizard-log-entry')
                .addClass(type)
                .addClass('aurora-wizard-slide-in-left')  // FASE 9F: Add slide animation
                .text(message);

            // Add pulse animation to processing entries
            if (type === 'processing') {
                logEntry.addClass('aurora-wizard-pulse');
            }

            // Add shake animation to error entries
            if (type === 'error') {
                logEntry.addClass('aurora-wizard-shake');
            }

            const logContainer = $('#wizard-activity-log');
            logContainer.append(logEntry);

            // Auto-scroll to bottom with smooth behavior
            if (logContainer[0]) {
                logContainer.animate({
                    scrollTop: logContainer[0].scrollHeight
                }, 300);
            }

            // Keep only last 50 entries
            const entries = logContainer.find('.aurora-wizard-log-entry');
            if (entries.length > 50) {
                entries.first().fadeOut(200, function() {
                    $(this).remove();
                });
            }
        }


        /**
         * Toggle pause
         */
        togglePause() {
            this.isPaused = !this.isPaused;

            const $pauseBtn = $('#wizard-pause-btn');

            if (this.isPaused) {
                $pauseBtn.text('Resume');
                this.addLog('⏸️ Paused by user', 'info');
            } else {
                $pauseBtn.text('Pause');
                this.addLog('▶️ Resumed', 'success');
            }
        }

        /**
         * Cancel wizard
         */
        cancel() {
            if (confirm('Are you sure you want to cancel? Progress will be saved and you can resume later.')) {
                this.isRunning = false;
                this.addLog('🛑 Cancelled by user', 'error');
                this.saveCheckpoint();

                setTimeout(() => {
                    this.closeModal();
                }, 2000);
            }
        }

        /**
         * Show completion screen
         */
        async showCompletionScreen() {
            // Get final statistics
            const stats = await this.getFinalStatistics();

            const duration = Date.now() - this.statistics.startTime;
            const minutes = Math.floor(duration / 60000);
            const seconds = Math.floor((duration % 60000) / 1000);
            const timeText = minutes > 0 ? `${minutes}m ${seconds}s` : `${seconds}s`;

            const completionHTML = `
                <div class="aurora-wizard-completion-screen">
                    <div class="aurora-wizard-completion-icon aurora-wizard-bounce">✅</div>

                    <h2 class="aurora-wizard-scale-in">Mock Data Generated Successfully!</h2>
                    <p class="aurora-wizard-fade-in">All data has been created and configured.</p>

                    <div class="aurora-wizard-stats-grid">
                        <div class="aurora-wizard-stat-card">
                            <div class="aurora-wizard-stat-number">${stats.packages || 0}</div>
                            <div class="aurora-wizard-stat-label">Packages</div>
                        </div>
                        <div class="aurora-wizard-stat-card">
                            <div class="aurora-wizard-stat-number">${this.statistics.itemsProcessed}</div>
                            <div class="aurora-wizard-stat-label">Total Items</div>
                        </div>
                        <div class="aurora-wizard-stat-card">
                            <div class="aurora-wizard-stat-number">${stats.images || 0}</div>
                            <div class="aurora-wizard-stat-label">Images</div>
                        </div>
                        <div class="aurora-wizard-stat-card">
                            <div class="aurora-wizard-stat-number">${timeText}</div>
                            <div class="aurora-wizard-stat-label">Total Time</div>
                        </div>
                        <div class="aurora-wizard-stat-card ${this.statistics.errorsCount > 0 ? 'error-stat' : ''}">
                            <div class="aurora-wizard-stat-number" style="${this.statistics.errorsCount > 0 ? 'color: #f44336;' : ''}">${this.statistics.errorsCount}</div>
                            <div class="aurora-wizard-stat-label">Errors</div>
                        </div>
                        <div class="aurora-wizard-stat-card">
                            <div class="aurora-wizard-stat-number">${this.statistics.retriesCount}</div>
                            <div class="aurora-wizard-stat-label">Retries</div>
                        </div>
                    </div>

                    ${this.errorLog.length > 0 ? `
                        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 6px;">
                            <p style="margin: 0; font-size: 14px; color: #856404;">
                                ⚠️ ${this.errorLog.length} error(s) occurred during processing.
                                <button class="aurora-wizard-btn-secondary" id="export-error-log-btn" style="font-size: 13px; padding: 5px 15px; margin-left: 10px;">
                                    Download Error Log
                                </button>
                            </p>
                        </div>
                    ` : ''}

                    <div class="aurora-wizard-actions" style="margin-top: 30px; justify-content: center;">
                        <button class="aurora-wizard-btn-primary" id="wizard-complete-close-btn">
                            Close
                        </button>
                    </div>
                </div>
            `;

            $('.aurora-wizard-body').html(completionHTML);
            $('.aurora-wizard-footer').hide();
            $('#wizard-close-btn').show();

            // Clear checkpoint
            this.clearCheckpoint();

            // Close button handler
            $('#wizard-complete-close-btn, #wizard-close-btn').on('click', () => {
                this.closeModal();
            });

            // Export error log button handler
            $('#export-error-log-btn').on('click', () => {
                this.exportErrorLog();
            });

            // Trigger confetti animation
            this.triggerConfetti();
        }

        /**
         * Close modal
         */
        closeModal() {
            $('#aurora-wizard-modal').fadeOut(300, function() {
                $(this).remove();
            });
        }

        /**
         * Save checkpoint to localStorage with validation
         */
        saveCheckpoint() {
            const checkpoint = {
                version: '1.0', // Schema version for future compatibility
                step: this.currentStep,
                batch: this.currentBatch,
                timestamp: Date.now(),
                expiresAt: Date.now() + (24 * 60 * 60 * 1000), // 24 hours from now
                checkpointData: this.checkpointData,
                statistics: this.statistics,
                wizardState: {
                    isPaused: this.isPaused,
                    totalSteps: this.totalSteps,
                    errorCount: this.statistics.errorsCount,
                    retriesCount: this.statistics.retriesCount
                },
                checksum: this.generateChecksum({
                    step: this.currentStep,
                    batch: this.currentBatch,
                    timestamp: Date.now()
                })
            };

            try {
                localStorage.setItem('aurora_wizard_checkpoint', JSON.stringify(checkpoint));
                console.log('✅ Checkpoint saved:', {
                    step: checkpoint.step,
                    batch: checkpoint.batch,
                    age: 0
                });
            } catch (e) {
                console.error('❌ Failed to save checkpoint:', e);
                // If localStorage is full, try to clear old data
                this.cleanupOldCheckpoints();
            }
        }

        /**
         * Load checkpoint from localStorage with validation
         */
        loadCheckpoint() {
            const saved = localStorage.getItem('aurora_wizard_checkpoint');

            if (!saved) {
                return null;
            }

            try {
                const checkpoint = JSON.parse(saved);

                // Validate checkpoint structure
                if (!this.validateCheckpoint(checkpoint)) {
                    console.warn('⚠️ Invalid checkpoint structure, clearing...');
                    this.clearCheckpoint();
                    return null;
                }

                // Check expiration
                if (Date.now() > checkpoint.expiresAt) {
                    console.warn('⚠️ Checkpoint expired, clearing...');
                    this.clearCheckpoint();
                    return null;
                }

                // Validate checksum (integrity check)
                const expectedChecksum = this.generateChecksum({
                    step: checkpoint.step,
                    batch: checkpoint.batch,
                    timestamp: checkpoint.timestamp
                });

                if (checkpoint.checksum !== expectedChecksum) {
                    console.warn('⚠️ Checkpoint integrity check failed, clearing...');
                    this.clearCheckpoint();
                    return null;
                }

                // Calculate age
                const ageMinutes = Math.floor((Date.now() - checkpoint.timestamp) / 60000);
                console.log('✅ Valid checkpoint loaded:', {
                    step: checkpoint.step,
                    batch: checkpoint.batch,
                    ageMinutes: ageMinutes
                });

                return checkpoint;

            } catch (e) {
                console.error('❌ Failed to parse checkpoint:', e);
                this.clearCheckpoint();
                return null;
            }
        }

        /**
         * Clear checkpoint
         */
        clearCheckpoint() {
            localStorage.removeItem('aurora_wizard_checkpoint');
            console.log('🗑️ Checkpoint cleared');
        }

        /**
         * Validate checkpoint structure
         */
        validateCheckpoint(checkpoint) {
            if (!checkpoint || typeof checkpoint !== 'object') {
                return false;
            }

            // Required fields
            const requiredFields = ['version', 'step', 'batch', 'timestamp', 'expiresAt', 'checksum'];
            for (const field of requiredFields) {
                if (!(field in checkpoint)) {
                    console.error(`Missing required field: ${field}`);
                    return false;
                }
            }

            // Validate data types
            if (typeof checkpoint.step !== 'number' || checkpoint.step < 1 || checkpoint.step > 6) {
                console.error('Invalid step number:', checkpoint.step);
                return false;
            }

            if (typeof checkpoint.batch !== 'number' || checkpoint.batch < 0) {
                console.error('Invalid batch number:', checkpoint.batch);
                return false;
            }

            if (typeof checkpoint.timestamp !== 'number' || checkpoint.timestamp <= 0) {
                console.error('Invalid timestamp:', checkpoint.timestamp);
                return false;
            }

            // Validate timestamp is not in the future
            if (checkpoint.timestamp > Date.now()) {
                console.error('Checkpoint timestamp is in the future');
                return false;
            }

            return true;
        }

        /**
         * Generate checksum for integrity validation
         */
        generateChecksum(data) {
            // Simple checksum using string hash
            const str = JSON.stringify(data);
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                const char = str.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash; // Convert to 32bit integer
            }
            return hash.toString(36);
        }

        /**
         * Cleanup old checkpoints and error logs
         */
        cleanupOldCheckpoints() {
            try {
                // Clear wizard checkpoint
                this.clearCheckpoint();

                // Clear old error logs (keep only recent ones)
                const errorLog = localStorage.getItem('aurora_wizard_error_log');
                if (errorLog) {
                    try {
                        const errors = JSON.parse(errorLog);
                        // Keep only errors from last 7 days
                        const sevenDaysAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);
                        const recentErrors = errors.filter(err => err.timestamp > sevenDaysAgo);

                        if (recentErrors.length < errors.length) {
                            localStorage.setItem('aurora_wizard_error_log', JSON.stringify(recentErrors));
                            console.log(`🧹 Cleaned up ${errors.length - recentErrors.length} old error logs`);
                        }
                    } catch (e) {
                        // If parsing fails, just clear it
                        localStorage.removeItem('aurora_wizard_error_log');
                    }
                }

                console.log('🧹 Checkpoint cleanup completed');
            } catch (e) {
                console.error('Failed to cleanup checkpoints:', e);
            }
        }

        /**
         * Check for existing checkpoint on page load
         */
        checkForExistingCheckpoint() {
            // First, cleanup old error logs
            this.cleanupOldCheckpoints();

            // Load and validate checkpoint
            const checkpoint = this.loadCheckpoint();

            if (checkpoint) {
                // Checkpoint is valid (already validated in loadCheckpoint)
                this.showResumeDialog(checkpoint);
            }
        }

        /**
         * Show resume dialog
         */
        showResumeDialog(checkpoint) {
            console.log('Checkpoint found:', checkpoint);

            // Calculate time elapsed since checkpoint
            const elapsed = Date.now() - checkpoint.timestamp;
            const minutes = Math.floor(elapsed / 60000);
            const hours = Math.floor(elapsed / 3600000);

            let timeText, ageColor, ageIcon;

            if (minutes < 60) {
                timeText = `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
                ageColor = '#4CAF50'; // Green - very recent
                ageIcon = '🟢';
            } else if (hours < 6) {
                timeText = `${hours} hour${hours !== 1 ? 's' : ''} ago`;
                ageColor = '#8BC34A'; // Light green - recent
                ageIcon = '🟢';
            } else if (hours < 12) {
                timeText = `${hours} hours ago`;
                ageColor = '#FF9800'; // Orange - moderate
                ageIcon = '🟠';
            } else {
                timeText = `${hours} hours ago`;
                ageColor = '#f44336'; // Red - old
                ageIcon = '🔴';
            }

            // Calculate time until expiration
            const timeToExpire = checkpoint.expiresAt - Date.now();
            const hoursToExpire = Math.floor(timeToExpire / 3600000);
            const minutesToExpire = Math.floor((timeToExpire % 3600000) / 60000);
            const expiresText = hoursToExpire > 0
                ? `Expires in ${hoursToExpire}h ${minutesToExpire}m`
                : `Expires in ${minutesToExpire} minutes`;

            // Build progress summary
            const stepNames = [
                'Creating Packages',
                'Adding Package Images',
                'Creating Other Content',
                'Adding Content Images',
                'Setting Up Taxonomies',
                'Finalizing'
            ];

            let progressHTML = '<ul style="text-align: left; margin: 20px 0;">';
            for (let i = 1; i <= 6; i++) {
                let icon = '⏳';
                let status = 'Pending';
                let style = 'color: #999;';

                if (i < checkpoint.step) {
                    icon = '✅';
                    status = 'Completed';
                    style = 'color: #4CAF50; font-weight: 600;';
                } else if (i === checkpoint.step) {
                    icon = '⏸️';
                    status = 'In Progress';
                    style = 'color: #FF9800; font-weight: 600;';
                }

                progressHTML += `<li style="${style}">${icon} Step ${i}: ${stepNames[i-1]} - ${status}</li>`;
            }
            progressHTML += '</ul>';

            // Create resume dialog HTML
            const dialogHTML = `
                <div class="aurora-wizard-modal" id="aurora-wizard-resume-modal" style="z-index: 1000000;">
                    <div class="aurora-wizard-content" style="max-width: 600px;">
                        <div class="aurora-wizard-header">
                            <h2>⏸️ Previous Session Found</h2>
                        </div>

                        <div class="aurora-wizard-body" style="max-height: none;">
                            <div style="background: ${ageColor}15; border-left: 4px solid ${ageColor}; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 20px;">${ageIcon}</span>
                                    <div>
                                        <p style="margin: 0; font-size: 15px; font-weight: 600; color: #2c3e50;">
                                            Session paused <strong style="color: ${ageColor};">${timeText}</strong>
                                        </p>
                                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">
                                            ${expiresText}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div style="background: #f5f7fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                                <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #2c3e50;">
                                    📊 Progress Summary
                                </h3>
                                ${progressHTML}
                                ${checkpoint.wizardState && checkpoint.wizardState.errorCount > 0 ? `
                                    <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border-radius: 6px; border: 1px solid #ffeeba;">
                                        <p style="margin: 0; font-size: 13px; color: #856404;">
                                            ⚠️ ${checkpoint.wizardState.errorCount} error(s) occurred,
                                            ${checkpoint.wizardState.retriesCount} retry attempts made
                                        </p>
                                    </div>
                                ` : ''}
                            </div>

                            <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                                You can resume from where you left off, or start fresh if you prefer.
                            </p>
                        </div>

                        <div class="aurora-wizard-footer">
                            <div style="flex: 1;"></div>
                            <div class="aurora-wizard-actions">
                                <button class="aurora-wizard-btn-secondary" id="resume-restart-btn">
                                    Start Fresh
                                </button>
                                <button class="aurora-wizard-btn-primary" id="resume-continue-btn">
                                    Resume from Checkpoint
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Append to body
            $('body').append(dialogHTML);

            // Attach event handlers
            $('#resume-continue-btn').on('click', () => {
                // Restore checkpoint state
                this.currentStep = checkpoint.step;
                this.currentBatch = checkpoint.batch || 0;
                this.checkpointData = checkpoint.checkpointData || {};
                this.statistics = checkpoint.statistics || {
                    startTime: Date.now(),
                    itemsProcessed: 0,
                    errorsCount: 0
                };

                // Close resume dialog
                $('#aurora-wizard-resume-modal').fadeOut(300, function() {
                    $(this).remove();
                });

                // Launch wizard
                this.launch();
            });

            $('#resume-restart-btn').on('click', () => {
                // Clear checkpoint
                this.clearCheckpoint();

                // Close resume dialog
                $('#aurora-wizard-resume-modal').fadeOut(300, function() {
                    $(this).remove();
                });

                // User will need to click launch button again
                console.log('Checkpoint cleared. Ready for fresh start.');
            });
        }

        /**
         * Get step info via AJAX
         */
        async getStepInfo(step) {
            // For now, return hardcoded info (can be fetched from server later)
            const stepInfo = {
                1: { title: 'Creating Packages', icon: '📦' },
                2: { title: 'Adding Package Images', icon: '🖼️' },
                3: { title: 'Creating Other Content', icon: '📝' },
                4: { title: 'Adding Content Images', icon: '🎨' },
                5: { title: 'Setting Up Taxonomies', icon: '🏷️' },
                6: { title: 'Finalizing', icon: '✅' }
            };

            return stepInfo[step] || { title: 'Processing', icon: '⚙️' };
        }

        /**
         * Get final statistics via AJAX
         */
        async getFinalStatistics() {
            try {
                const response = await $.ajax({
                    url: auroraWizardData.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'aurora_wizard_get_stats',
                        nonce: auroraWizardData.nonce
                    }
                });

                return response.statistics || {};
            } catch (error) {
                console.error('Failed to get statistics:', error);
                return {};
            }
        }

        /**
         * Log detailed error information
         */
        logError(errorInfo) {
            this.errorLog.push(errorInfo);

            // Also log to console for debugging
            console.group('🔴 Wizard Error Details');
            console.log('Timestamp:', new Date(errorInfo.timestamp).toLocaleString());
            console.log('Step:', errorInfo.step);
            console.log('Batch:', errorInfo.batch);
            console.log('Attempt:', errorInfo.attempt);
            console.log('Error:', errorInfo.error);
            console.log('Full Error Object:', errorInfo.errorObject);
            console.groupEnd();

            // Save to localStorage for debugging
            try {
                localStorage.setItem('aurora_wizard_error_log', JSON.stringify(this.errorLog));
            } catch (e) {
                console.error('Failed to save error log to localStorage:', e);
            }
        }

        /**
         * Export error log as downloadable file
         */
        exportErrorLog() {
            if (this.errorLog.length === 0) {
                alert('No errors to export');
                return;
            }

            const logContent = this.errorLog.map(err => {
                return `[${new Date(err.timestamp).toISOString()}] Step ${err.step}, Batch ${err.batch}, Attempt ${err.attempt}\nError: ${err.error}\n---`;
            }).join('\n\n');

            const blob = new Blob([logContent], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `wizard-error-log-${Date.now()}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        /**
         * Show error dialog and get user choice
         */
        async showErrorDialog(errorMessage) {
            return new Promise((resolve) => {
                const dialogHTML = `
                    <div class="aurora-wizard-modal" id="aurora-wizard-error-modal" style="z-index: 1000001;">
                        <div class="aurora-wizard-content" style="max-width: 500px;">
                            <div class="aurora-wizard-header" style="background: linear-gradient(135deg, #f44336 0%, #e91e63 100%);">
                                <h2>⚠️ Error Occurred</h2>
                            </div>

                            <div class="aurora-wizard-body" style="max-height: none;">
                                <div style="background: #fff3cd; border: 1px solid #ffeeba; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                                    <strong style="color: #856404;">Error Details:</strong>
                                    <p style="margin: 10px 0 0 0; color: #856404; font-family: monospace; font-size: 13px;">
                                        ${errorMessage}
                                    </p>
                                </div>

                                <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                                    The wizard encountered an error while processing this batch. You can:
                                </p>

                                <ul style="font-size: 14px; color: #666; margin: 0 0 20px 20px; line-height: 1.8;">
                                    <li><strong>Skip:</strong> Skip this batch and continue with the next one</li>
                                    <li><strong>Retry:</strong> Try processing this batch again</li>
                                    <li><strong>Stop:</strong> Stop the wizard and investigate the issue</li>
                                </ul>
                            </div>

                            <div class="aurora-wizard-footer">
                                <div style="flex: 1;"></div>
                                <div class="aurora-wizard-actions">
                                    <button class="aurora-wizard-btn-secondary" id="error-view-log-btn" style="margin-right: auto;">
                                        View Error Log
                                    </button>
                                    <button class="aurora-wizard-btn-secondary" id="error-stop-btn" style="background: #f44336; color: #fff;">
                                        Stop Wizard
                                    </button>
                                    <button class="aurora-wizard-btn-secondary" id="error-skip-btn">
                                        Skip Batch
                                    </button>
                                    <button class="aurora-wizard-btn-primary" id="error-retry-btn">
                                        Retry
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Append to body
                $('body').append(dialogHTML);

                // Attach event handlers
                $('#error-view-log-btn').on('click', () => {
                    this.exportErrorLog();
                });

                $('#error-skip-btn').on('click', () => {
                    $('#aurora-wizard-error-modal').remove();
                    resolve('skip');
                });

                $('#error-retry-btn').on('click', () => {
                    $('#aurora-wizard-error-modal').remove();
                    resolve('retry');
                });

                $('#error-stop-btn').on('click', () => {
                    $('#aurora-wizard-error-modal').remove();
                    resolve('stop');
                });
            });
        }

        /**
         * FASE 9F: Trigger confetti animation on completion
         */
        triggerConfetti() {
            const colors = ['#667eea', '#764ba2', '#4CAF50', '#FF9800', '#f44336', '#2196F3', '#E91E63'];
            const confettiCount = 50;

            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = $('<div>')
                        .addClass('aurora-wizard-confetti')
                        .css({
                            left: Math.random() * 100 + '%',
                            top: '-20px',
                            backgroundColor: colors[Math.floor(Math.random() * colors.length)],
                            width: (Math.random() * 10 + 5) + 'px',
                            height: (Math.random() * 10 + 5) + 'px',
                            animationDuration: (Math.random() * 2 + 2) + 's',
                            animationDelay: '0s'
                        });

                    $('body').append(confetti);

                    // Remove after animation
                    setTimeout(() => {
                        confetti.remove();
                    }, 4000);
                }, i * 50); // Stagger the confetti
            }
        }

        /**
         * FASE 9F: Improved time estimation algorithm
         */
        updateTimeEstimate() {
            if (!this.statistics.startTime) return;

            const elapsed = Date.now() - this.statistics.startTime;
            const elapsedSeconds = Math.floor(elapsed / 1000);

            // Calculate progress based on steps and batches
            // Weight steps by their typical duration
            const stepWeights = {
                1: 1.0,  // Package content
                2: 1.5,  // Package images (más pesado)
                3: 1.2,  // Other CPTs
                4: 1.3,  // CPT images
                5: 0.8,  // Taxonomies (más rápido)
                6: 0.2   // Finalization (muy rápido)
            };

            const totalWeight = Object.values(stepWeights).reduce((a, b) => a + b, 0);
            let completedWeight = 0;

            // Add weight of completed steps
            for (let i = 1; i < this.currentStep; i++) {
                completedWeight += stepWeights[i] || 1;
            }

            // Add partial weight of current step
            // Assume each step has ~10 batches on average
            const currentStepProgress = Math.min(this.currentBatch / 10, 1);
            completedWeight += (stepWeights[this.currentStep] || 1) * currentStepProgress;

            const progressFraction = completedWeight / totalWeight;

            // Estimate total time based on current progress
            const estimatedTotal = progressFraction > 0.05 ? elapsed / progressFraction : elapsed * 20;
            const remaining = Math.max(0, estimatedTotal - elapsed);
            const remainingSeconds = Math.floor(remaining / 1000);

            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;

            let timeText = '⏱️ ';
            if (remainingSeconds > 0) {
                if (minutes > 0) {
                    timeText += `~${minutes}m ${seconds}s remaining`;
                } else {
                    timeText += `~${seconds}s remaining`;
                }
            } else {
                timeText += 'Almost done...';
            }

            $('#wizard-time-estimate').text(timeText);
        }

        /**
         * Perform cleanup as part of wizard process
         */
        async performCleanup() {
            this.addLog('🗑️ Starting cleanup process...', 'processing');

            try {
                // Step 1: Delete packages
                this.addLog('📦 Deleting packages...', 'processing');
                await this.sleep(300);

                const response = await $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'aurora_wizard_cleanup',
                        nonce: auroraWizardData.nonce
                    }
                });

                if (response.success) {
                    const results = response.data.results;

                    // Show detailed results
                    if (results.packages > 0) {
                        this.addLog(`  ✓ Deleted ${results.packages} packages`, 'success');
                    }

                    if (results.deals > 0) {
                        this.addLog(`💰 Deleting deals...`, 'processing');
                        await this.sleep(100);
                        this.addLog(`  ✓ Deleted ${results.deals} deals`, 'success');
                    }

                    if (results.locations > 0) {
                        this.addLog(`📍 Deleting locations...`, 'processing');
                        await this.sleep(100);
                        this.addLog(`  ✓ Deleted ${results.locations} locations`, 'success');
                    }

                    if (results.guides > 0) {
                        this.addLog(`👨‍🏫 Deleting guides...`, 'processing');
                        await this.sleep(100);
                        this.addLog(`  ✓ Deleted ${results.guides} guides`, 'success');
                    }

                    if (results.reviews > 0) {
                        this.addLog(`⭐ Deleting reviews...`, 'processing');
                        await this.sleep(100);
                        this.addLog(`  ✓ Deleted ${results.reviews} reviews`, 'success');
                    }

                    if (results.collaborators > 0) {
                        this.addLog(`👥 Deleting collaborators...`, 'processing');
                        await this.sleep(100);
                        this.addLog(`  ✓ Deleted ${results.collaborators} collaborators`, 'success');
                    }

                    if (results.attachments > 0) {
                        this.addLog(`🖼️ Deleting images...`, 'processing');
                        await this.sleep(200);
                        this.addLog(`  ✓ Deleted ${results.attachments} images`, 'success');
                    }

                    if (results.terms > 0) {
                        this.addLog(`🏷️ Deleting taxonomy terms...`, 'processing');
                        await this.sleep(100);
                        this.addLog(`  ✓ Deleted ${results.terms} terms`, 'success');
                    }

                    const total = results.packages + results.deals + results.locations +
                                results.guides + results.reviews + results.collaborators +
                                results.attachments + results.terms;

                    this.addLog(`✅ Cleanup complete: ${total} total items deleted`, 'success');

                    // Clear any checkpoints
                    this.clearCheckpoint();
                } else {
                    this.addLog('❌ Cleanup failed: ' + (response.data.error || 'Unknown error'), 'error');
                    throw new Error('Cleanup failed');
                }
            } catch (error) {
                console.error('Cleanup error:', error);
                this.addLog('❌ Cleanup error: ' + error.message, 'error');
                throw error;
            }
        }

        /**
         * Utility: Sleep function
         */
        sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    }

    // Initialize on document ready
    $(document).ready(function() {
        window.auroraWizard = new MockDataWizard();
        window.auroraWizard.init();

        console.log('✅ Aurora Mock Data Wizard initialized');
        console.log('🔑 Nonce available:', typeof auroraWizardData !== 'undefined' && auroraWizardData.nonce ? 'Yes' : 'No');
    });

})(jQuery);
