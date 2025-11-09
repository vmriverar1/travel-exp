/**
 * PDF Download Modal Handler
 */

(function() {
    'use strict';

    // Modal HTML Template
    const modalHTML = `
        <div class="pdf-modal-overlay" id="pdfModalOverlay">
            <div class="pdf-modal">
                <div class="pdf-modal__header">
                    <h2 class="pdf-modal__title">Download Package Itinerary</h2>
                    <p class="pdf-modal__subtitle">Get the complete itinerary sent to your email</p>
                    <button class="pdf-modal__close" id="pdfModalClose" aria-label="Close modal">&times;</button>
                </div>

                <div class="pdf-modal__body">
                    <form class="pdf-modal__form" id="pdfDownloadForm">
                        <div class="pdf-modal__field">
                            <label class="pdf-modal__label pdf-modal__label--required" for="pdfUserName">Your Name</label>
                            <input
                                type="text"
                                id="pdfUserName"
                                name="user_name"
                                class="pdf-modal__input"
                                placeholder="John Doe"
                                required
                            >
                            <span class="pdf-modal__error" id="nameError">Please enter your name</span>
                        </div>

                        <div class="pdf-modal__field">
                            <label class="pdf-modal__label pdf-modal__label--required" for="pdfUserEmail">Your Email</label>
                            <input
                                type="email"
                                id="pdfUserEmail"
                                name="user_email"
                                class="pdf-modal__input"
                                placeholder="john@example.com"
                                required
                            >
                            <span class="pdf-modal__error" id="emailError">Please enter a valid email</span>
                        </div>

                        <div class="pdf-modal__checkbox-wrapper">
                            <input
                                type="checkbox"
                                id="pdfConsent"
                                name="consent"
                                class="pdf-modal__checkbox"
                                required
                            >
                            <label for="pdfConsent" class="pdf-modal__checkbox-label">
                                I agree to receive travel information and updates from Valencia Travel.
                                You can unsubscribe at any time.
                            </label>
                        </div>
                        <span class="pdf-modal__error" id="consentError">Please accept the terms to continue</span>

                        <div class="pdf-modal__success" id="pdfSuccess">
                            ✓ PDF downloaded successfully! Check your downloads folder.
                        </div>

                        <span class="pdf-modal__error" id="generalError"></span>
                    </form>
                </div>

                <div class="pdf-modal__footer">
                    <button type="button" class="pdf-modal__button pdf-modal__button--secondary" id="pdfModalCancel">
                        Cancel
                    </button>
                    <button type="submit" form="pdfDownloadForm" class="pdf-modal__button pdf-modal__button--primary" id="pdfDownloadButton">
                        Download PDF
                    </button>
                </div>
            </div>
        </div>
    `;

    class PDFDownloadModal {
        constructor() {
            this.modal = null;
            this.overlay = null;
            this.form = null;
            this.packageId = null;
            this.isLoading = false;

            this.init();
        }

        init() {
            // Inject modal HTML immediately or on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.injectModal();
                });
            } else {
                // DOM is already ready
                this.injectModal();
            }
        }

        injectModal() {
            console.log('PDF Modal: Injecting modal HTML...');
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            this.cacheElements();
            this.attachEvents();
            console.log('PDF Modal: Initialized successfully');
        }

        cacheElements() {
            this.overlay = document.getElementById('pdfModalOverlay');
            this.modal = this.overlay?.querySelector('.pdf-modal');
            this.form = document.getElementById('pdfDownloadForm');
            this.closeBtn = document.getElementById('pdfModalClose');
            this.cancelBtn = document.getElementById('pdfModalCancel');
            this.downloadBtn = document.getElementById('pdfDownloadButton');
            this.successMsg = document.getElementById('pdfSuccess');
            this.generalError = document.getElementById('generalError');
        }

        attachEvents() {
            // Open modal when clicking on PDF-enabled promo cards
            document.addEventListener('click', (e) => {
                const card = e.target.closest('.promo-card--pdf-enabled');
                if (card) {
                    console.log('PDF Modal: Card clicked', card);
                    e.preventDefault();
                    const packageId = card.dataset.packageId;
                    console.log('PDF Modal: Package ID', packageId);
                    if (packageId) {
                        this.open(packageId);
                    }
                }
            });

            // Close modal events
            this.closeBtn?.addEventListener('click', () => this.close());
            this.cancelBtn?.addEventListener('click', () => this.close());

            this.overlay?.addEventListener('click', (e) => {
                if (e.target === this.overlay) {
                    this.close();
                }
            });

            // ESC key to close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.overlay?.classList.contains('is-active')) {
                    this.close();
                }
            });

            // Form submission
            this.form?.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit();
            });

            // Real-time validation
            const nameInput = document.getElementById('pdfUserName');
            const emailInput = document.getElementById('pdfUserEmail');
            const consentCheckbox = document.getElementById('pdfConsent');

            nameInput?.addEventListener('blur', () => this.validateName());
            emailInput?.addEventListener('blur', () => this.validateEmail());
            consentCheckbox?.addEventListener('change', () => this.validateConsent());
        }

        open(packageId) {
            console.log('PDF Modal: Opening modal for package', packageId);
            this.packageId = packageId;
            this.overlay?.classList.add('is-active');
            document.body.style.overflow = 'hidden';

            // Focus on first input
            setTimeout(() => {
                document.getElementById('pdfUserName')?.focus();
            }, 300);
        }

        close() {
            this.overlay?.classList.remove('is-active');
            document.body.style.overflow = '';

            // Reset form after animation
            setTimeout(() => {
                this.resetForm();
            }, 300);
        }

        validateName() {
            const input = document.getElementById('pdfUserName');
            const error = document.getElementById('nameError');
            const value = input?.value.trim();

            if (!value || value.length < 2) {
                input?.classList.add('is-error');
                error?.classList.add('is-visible');
                return false;
            }

            input?.classList.remove('is-error');
            error?.classList.remove('is-visible');
            return true;
        }

        validateEmail() {
            const input = document.getElementById('pdfUserEmail');
            const error = document.getElementById('emailError');
            const value = input?.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!value || !emailRegex.test(value)) {
                input?.classList.add('is-error');
                error?.classList.add('is-visible');
                return false;
            }

            input?.classList.remove('is-error');
            error?.classList.remove('is-visible');
            return true;
        }

        validateConsent() {
            const checkbox = document.getElementById('pdfConsent');
            const error = document.getElementById('consentError');

            if (!checkbox?.checked) {
                error?.classList.add('is-visible');
                return false;
            }

            error?.classList.remove('is-visible');
            return true;
        }

        async handleSubmit() {
            // Validate all fields
            const isNameValid = this.validateName();
            const isEmailValid = this.validateEmail();
            const isConsentValid = this.validateConsent();

            if (!isNameValid || !isEmailValid || !isConsentValid) {
                return;
            }

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            this.downloadBtn?.classList.add('pdf-modal__button--loading');
            this.downloadBtn?.setAttribute('disabled', 'disabled');
            this.generalError?.classList.remove('is-visible');

            try {
                const formData = new FormData(this.form);
                const data = {
                    package_id: parseInt(this.packageId),
                    user_name: formData.get('user_name'),
                    user_email: formData.get('user_email'),
                };

                const response = await fetch('/wp-json/travel/v1/generate-package-pdf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to generate PDF');
                }

                // Download PDF
                this.downloadPDF(result.pdf, result.filename);

                // Show success message
                this.successMsg?.classList.add('is-visible');

                // Close modal after 2 seconds
                setTimeout(() => {
                    this.close();
                }, 2000);

            } catch (error) {
                console.error('PDF generation error:', error);
                this.generalError.textContent = error.message || 'An error occurred. Please try again.';
                this.generalError?.classList.add('is-visible');
            } finally {
                this.isLoading = false;
                this.downloadBtn?.classList.remove('pdf-modal__button--loading');
                this.downloadBtn?.removeAttribute('disabled');
            }
        }

        downloadPDF(base64Data, filename) {
            try {
                // Convert base64 to blob
                const byteCharacters = atob(base64Data);
                const byteNumbers = new Array(byteCharacters.length);

                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }

                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], { type: 'application/pdf' });

                // Create download link
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename || 'package-itinerary.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('PDF download error:', error);
                throw new Error('Failed to download PDF');
            }
        }

        resetForm() {
            this.form?.reset();

            // Remove error states
            document.querySelectorAll('.pdf-modal__input').forEach(input => {
                input.classList.remove('is-error');
            });

            document.querySelectorAll('.pdf-modal__error').forEach(error => {
                error.classList.remove('is-visible');
            });

            this.successMsg?.classList.remove('is-visible');
            this.packageId = null;
        }
    }

    // Initialize
    new PDFDownloadModal();

})();
