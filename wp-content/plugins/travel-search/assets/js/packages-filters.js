(function ($) {
    'use strict';

    /**
     * Travel Search Filters - SPA Mode
     * Handles package filtering with AJAX and URL state management
     */
    class TravelSearchFilters {
        constructor(wrapper) {
            this.$wrapper = $(wrapper);
            this.$filtersBar = this.$wrapper.find('.ts-filters-bar');
            this.$results = this.$wrapper.find('.ts-packages-results');
            this.restUrl = this.$wrapper.data('rest-url');
            this.pageUrl = this.$wrapper.data('page-url');
            this.flatpickrInstance = null;

            if (!this.$filtersBar.length || !this.restUrl) {
                return;
            }

            this.init();
        }

        init() {
            // Wait for flatpickr to be loaded
            if (window.flatpickr) {
                this.initFlatpickr();
                this.restoreFiltersFromURL();
            } else {
                // Retry after a short delay
                setTimeout(() => {
                    this.initFlatpickr();
                    this.restoreFiltersFromURL();
                }, 100);
            }

            // Event listeners
            this.$filtersBar.on('change', 'select', this.handleFilterChange.bind(this));

            // Handle browser back/forward
            window.addEventListener('popstate', this.handlePopState.bind(this));
        }

        /**
         * Initialize flatpickr on date input
         */
        initFlatpickr() {
            const $dateInput = this.$filtersBar.find('#ts-filter-date');

            if ($dateInput.length && window.flatpickr) {
                const currentYear = new Date().getFullYear();

                this.flatpickrInstance = flatpickr($dateInput.get(0), {
                    mode: 'single',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'F j, Y',
                    allowInput: false,
                    minDate: new Date(currentYear - 1, 0, 1),
                    maxDate: new Date(currentYear + 3, 11, 31),
                    onChange: () => {
                        this.handleFilterChange();
                    }
                });

                console.log('Flatpickr initialized:', this.flatpickrInstance);
            } else {
                console.error('Flatpickr not available');
            }
        }

        /**
         * Restore filter values from current URL
         */
        restoreFiltersFromURL() {
            const params = new URLSearchParams(window.location.search);

            // Destination (single select)
            const destination = params.get('destination');
            if (destination) {
                this.$filtersBar.find('#ts-filter-destination').val(destination);
            }

            // Interest (single select)
            const interest = params.get('interest');
            if (interest) {
                this.$filtersBar.find('#ts-filter-interest').val(interest);
            }

            // Date (flatpickr)
            const date = params.get('date');
            if (date && this.flatpickrInstance) {
                this.flatpickrInstance.setDate(date, false);
            }

            // Days (single select)
            const days = params.get('days');
            if (days) {
                this.$filtersBar.find('#ts-filter-days').val(days);
            }
        }

        /**
         * Handle filter change event
         */
        handleFilterChange(e) {
            this.fetchAndRender();
        }

        /**
         * Handle browser back/forward
         */
        handlePopState(e) {
            this.restoreFiltersFromURL();
            this.fetchAndRender();
        }

        /**
         * Get current filter values
         */
        getFilterValues() {
            const filters = {};

            // Destination
            const destination = this.$filtersBar.find('#ts-filter-destination').val();
            if (destination) {
                filters.destination = destination;
            }

            // Interest (single select)
            const interest = this.$filtersBar.find('#ts-filter-interest').val();
            if (interest) {
                filters.interest = interest;
            }

            // Date (flatpickr)
            const date = this.flatpickrInstance ? this.flatpickrInstance.selectedDates[0] : null;
            if (date) {
                // Format as Y-m-d
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                filters.date = `${year}-${month}-${day}`;
            }

            // Days
            const days = this.$filtersBar.find('#ts-filter-days').val();
            if (days) {
                filters.days = days;
            }

            return filters;
        }

        /**
         * Build query string from filters
         */
        buildQueryString(filters) {
            const params = new URLSearchParams();

            Object.keys(filters).forEach(key => {
                const value = filters[key];

                if (Array.isArray(value)) {
                    value.forEach(v => params.append(key, v));
                } else {
                    params.append(key, value);
                }
            });

            return params.toString();
        }

        /**
         * Fetch and render packages
         */
        fetchAndRender() {
            const filters = this.getFilterValues();
            const queryString = this.buildQueryString(filters);

            // Build REST URL
            const url = this.restUrl + (queryString ? '?' + queryString : '');

            // Add loading state
            this.$wrapper.addClass('ts-loading');
            this.$results.css('opacity', '0.5');

            // Fetch packages
            fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.html !== undefined) {
                        // Update results
                        this.$results.html(data.html);

                        // Update URL with pushState
                        const newUrl = this.pageUrl + (queryString ? '?' + queryString : '');
                        window.history.pushState({ filters: filters }, '', newUrl);

                        // Scroll to results (optional)
                        // this.scrollToResults();
                    } else {
                        this.showError('No results found');
                    }
                })
                .catch(error => {
                    console.error('TravelSearch fetch error:', error);
                    this.showError('Error loading packages. Please try again.');
                })
                .finally(() => {
                    this.$wrapper.removeClass('ts-loading');
                    this.$results.css('opacity', '1');
                });
        }

        /**
         * Show error message
         */
        showError(message) {
            this.$results.html('<p class="ts-error">' + message + '</p>');
        }

        /**
         * Scroll to results (optional)
         */
        scrollToResults() {
            const offset = this.$wrapper.offset().top - 100;
            $('html, body').animate({ scrollTop: offset }, 300);
        }
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function () {
        $('.ts-packages-wrapper').each(function () {
            new TravelSearchFilters(this);
        });
    });

})(jQuery);
