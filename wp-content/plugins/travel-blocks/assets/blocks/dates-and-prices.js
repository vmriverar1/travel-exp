/**
 * Dates and Prices Block - Booking Interface JavaScript
 *
 * Handles year/month navigation and booking button interactions
 *
 * @package Travel\Blocks
 * @since 2.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all booking blocks on the page
     */
    function initBookingBlocks() {
        const blocks = document.querySelectorAll('.booking');

        blocks.forEach(block => {
            // Skip if already initialized
            if (block.dataset.initialized === 'true') {
                return;
            }

            // Mark as initialized
            block.dataset.initialized = 'true';

            // Get booking data from JSON script tag
            const dataScript = block.querySelector('.booking-data');
            if (!dataScript) {
                console.warn('Booking data not found in block');
                return;
            }

            let bookingData;
            try {
                bookingData = JSON.parse(dataScript.textContent);
            } catch (err) {
                console.error('Failed to parse booking data:', err);
                return;
            }

            // Initialize navigation
            initYearTabs(block, bookingData);
            initMonthNav(block, bookingData);
            initMonthSelect(block, bookingData);
            initBookingButtons(block);

            // Show initial month
            updateVisibleDates(block, bookingData.current_year, bookingData.current_month);
            updateMonthLabel(block, bookingData.current_year, bookingData.current_month, bookingData.month_names);
        });
    }

    /**
     * Initialize year tabs
     */
    function initYearTabs(block, bookingData) {
        const yearTabs = block.querySelectorAll('.year-tab');

        yearTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const selectedYear = this.dataset.year;

                // Update active state
                yearTabs.forEach(t => {
                    t.classList.remove('is-active');
                    t.setAttribute('aria-selected', 'false');
                });
                this.classList.add('is-active');
                this.setAttribute('aria-selected', 'true');

                // Update current year in block data
                block.dataset.currentYear = selectedYear;

                // Get first available month for this year
                const yearData = bookingData.grouped_dates[selectedYear];
                const firstMonth = yearData ? Object.keys(yearData)[0] : '01';
                block.dataset.currentMonth = firstMonth;

                // Update display
                updateVisibleDates(block, selectedYear, firstMonth);
                updateMonthLabel(block, selectedYear, firstMonth, bookingData.month_names);
                updateMonthNavButtons(block, bookingData);
            });
        });
    }

    /**
     * Initialize month navigation
     */
    function initMonthNav(block, bookingData) {
        const prevBtn = block.querySelector('.icon-btn--prev');
        const nextBtn = block.querySelector('.icon-btn--next');

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                navigateMonth(block, bookingData, -1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                navigateMonth(block, bookingData, 1);
            });
        }

        // Initial button state
        updateMonthNavButtons(block, bookingData);
    }

    /**
     * Initialize month select popover
     */
    function initMonthSelect(block, bookingData) {
        const blockId = block.id;
        const btn = block.querySelector('.icon-btn--select');
        const popover = block.querySelector('.month-popover');

        if (!btn || !popover) {
            return;
        }

        // Toggle popover on button click
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = !popover.hasAttribute('hidden');

            if (isOpen) {
                popover.setAttribute('hidden', '');
                btn.setAttribute('aria-expanded', 'false');
            } else {
                popover.removeAttribute('hidden');
                btn.setAttribute('aria-expanded', 'true');
                updatePopoverItems(block, bookingData);
            }
        });

        // Handle month selection
        const items = popover.querySelectorAll('.month-popover__item');
        items.forEach(item => {
            item.addEventListener('click', function() {
                const selectedMonth = this.dataset.month;
                const currentYear = block.dataset.currentYear;

                // Check if month has dates in current year
                const yearData = bookingData.grouped_dates[currentYear];
                if (yearData && yearData[selectedMonth]) {
                    block.dataset.currentMonth = selectedMonth;
                    updateVisibleDates(block, currentYear, selectedMonth);
                    updateMonthLabel(block, currentYear, selectedMonth, bookingData.month_names);
                    updateMonthNavButtons(block, bookingData);

                    // Close popover
                    popover.setAttribute('hidden', '');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Close popover when clicking outside
        document.addEventListener('click', function(e) {
            if (!block.contains(e.target) && !popover.hasAttribute('hidden')) {
                popover.setAttribute('hidden', '');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /**
     * Update popover items (mark active and disable unavailable)
     */
    function updatePopoverItems(block, bookingData) {
        const currentYear = block.dataset.currentYear;
        const currentMonth = block.dataset.currentMonth;
        const popover = block.querySelector('.month-popover');

        if (!popover) return;

        const yearData = bookingData.grouped_dates[currentYear];
        const items = popover.querySelectorAll('.month-popover__item');

        items.forEach(item => {
            const month = item.dataset.month;
            const hasData = yearData && yearData[month];

            // Mark active
            if (month === currentMonth) {
                item.classList.add('is-active');
            } else {
                item.classList.remove('is-active');
            }

            // Disable months without dates
            if (!hasData) {
                item.setAttribute('aria-disabled', 'true');
            } else {
                item.removeAttribute('aria-disabled');
            }
        });
    }

    /**
     * Navigate to previous or next month
     */
    function navigateMonth(block, bookingData, direction) {
        const currentYear = block.dataset.currentYear;
        const currentMonth = block.dataset.currentMonth;

        const result = getAdjacentMonth(
            bookingData.grouped_dates,
            currentYear,
            currentMonth,
            direction
        );

        if (result) {
            block.dataset.currentYear = result.year;
            block.dataset.currentMonth = result.month;

            updateVisibleDates(block, result.year, result.month);
            updateMonthLabel(block, result.year, result.month, bookingData.month_names);
            updateMonthNavButtons(block, bookingData);

            // Update year tab if year changed
            if (result.year !== currentYear) {
                const yearTabs = block.querySelectorAll('.year-tab');
                yearTabs.forEach(tab => {
                    if (tab.dataset.year === result.year) {
                        tab.classList.add('is-active');
                        tab.setAttribute('aria-selected', 'true');
                    } else {
                        tab.classList.remove('is-active');
                        tab.setAttribute('aria-selected', 'false');
                    }
                });
            }
        }
    }

    /**
     * Get adjacent month (previous or next)
     */
    function getAdjacentMonth(groupedDates, currentYear, currentMonth, direction) {
        const years = Object.keys(groupedDates).sort();
        const currentYearData = groupedDates[currentYear];

        if (!currentYearData) return null;

        const months = Object.keys(currentYearData).sort();
        const currentMonthIndex = months.indexOf(currentMonth);

        // Try within same year
        const newMonthIndex = currentMonthIndex + direction;
        if (newMonthIndex >= 0 && newMonthIndex < months.length) {
            return {
                year: currentYear,
                month: months[newMonthIndex]
            };
        }

        // Try adjacent year
        const currentYearIndex = years.indexOf(currentYear);
        const newYearIndex = currentYearIndex + direction;

        if (newYearIndex >= 0 && newYearIndex < years.length) {
            const newYear = years[newYearIndex];
            const newYearMonths = Object.keys(groupedDates[newYear]).sort();

            if (newYearMonths.length > 0) {
                return {
                    year: newYear,
                    month: direction > 0 ? newYearMonths[0] : newYearMonths[newYearMonths.length - 1]
                };
            }
        }

        return null;
    }

    /**
     * Update month navigation button states
     */
    function updateMonthNavButtons(block, bookingData) {
        const prevBtn = block.querySelector('.icon-btn--prev');
        const nextBtn = block.querySelector('.icon-btn--next');
        const currentYear = block.dataset.currentYear;
        const currentMonth = block.dataset.currentMonth;

        // Check if previous month exists
        const hasPrev = getAdjacentMonth(bookingData.grouped_dates, currentYear, currentMonth, -1) !== null;
        if (prevBtn) {
            prevBtn.disabled = !hasPrev;
        }

        // Check if next month exists
        const hasNext = getAdjacentMonth(bookingData.grouped_dates, currentYear, currentMonth, 1) !== null;
        if (nextBtn) {
            nextBtn.disabled = !hasNext;
        }
    }

    /**
     * Update visible dates based on year/month
     */
    function updateVisibleDates(block, year, month) {
        const allCards = block.querySelectorAll('.trip-card');

        allCards.forEach(card => {
            const cardYear = card.dataset.year;
            const cardMonth = card.dataset.month;

            if (cardYear === year && cardMonth === month) {
                card.classList.add('trip-card--visible');
                card.style.display = 'grid';
            } else {
                card.classList.remove('trip-card--visible');
                card.style.display = 'none';
            }
        });
    }

    /**
     * Update month label text
     */
    function updateMonthLabel(block, year, month, monthNames) {
        const monthLabel = block.querySelector('.month-label__text');
        if (monthLabel) {
            const monthName = monthNames[month] || month;
            monthLabel.textContent = `${monthName} ${year}`;
        }
    }

    /**
     * Initialize booking buttons
     */
    function initBookingButtons(block) {
        // Get package ID from booking data
        const dataScript = block.querySelector('.booking-data');
        let packageId = null;
        if (dataScript) {
            try {
                const bookingData = JSON.parse(dataScript.textContent);
                packageId = bookingData.package_id || null;
            } catch (err) {
                console.warn('Could not parse booking data for package ID');
            }
        }

        const buttons = block.querySelectorAll('.btn-primary');

        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const departureDate = this.dataset.departureDate;
                const returnDate = this.dataset.returnDate;
                const action = this.dataset.action || 'default';
                const anchor = this.dataset.anchor;

                if (!departureDate) {
                    return;
                }

                // Handle different button actions
                if (action === 'scroll_to_anchor' && anchor) {
                    // Scroll to anchor for fixed_week dates
                    e.preventDefault();
                    const targetElement = document.querySelector(anchor);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    } else {
                        console.warn('Anchor element not found:', anchor);
                    }
                    return;
                }

                if (action === 'contact') {
                    // Open contact modal or redirect to contact page
                    e.preventDefault();
                    // Dispatch event for external contact handler
                    const contactEvent = new CustomEvent('travelBlocksContactRequested', {
                        detail: {
                            departureDate: departureDate,
                            returnDate: returnDate,
                        },
                        bubbles: true,
                    });
                    document.dispatchEvent(contactEvent);

                    // TODO: Integrate with contact form or modal
                    console.log('Contact requested for dates:', {
                        departure: departureDate,
                        return: returnDate
                    });
                    return;
                }

                if (action === 'open_purchase_aside') {
                    // Open purchase aside for fixed_dates
                    e.preventDefault();
                    // Dispatch event for external purchase handler
                    const purchaseEvent = new CustomEvent('travelBlocksPurchaseRequested', {
                        detail: {
                            packageId: packageId,
                            departureDate: departureDate,
                            returnDate: returnDate,
                        },
                        bubbles: true,
                    });
                    document.dispatchEvent(purchaseEvent);

                    // TODO: Integrate with purchase aside/modal
                    console.log('Purchase aside requested for dates:', {
                        packageId: packageId,
                        departure: departureDate,
                        return: returnDate
                    });
                    return;
                }

                // Default action: Store dates and dispatch event
                // Store selected dates in sessionStorage
                try {
                    sessionStorage.setItem('selectedDepartureDate', departureDate);
                    if (returnDate) {
                        sessionStorage.setItem('selectedReturnDate', returnDate);
                    }
                } catch (err) {
                    console.warn('Could not store dates in sessionStorage', err);
                }

                // Dispatch custom event for external handlers
                const event = new CustomEvent('travelBlocksDateSelected', {
                    detail: {
                        departureDate: departureDate,
                        returnDate: returnDate,
                        formattedDeparture: formatDate(departureDate),
                        formattedReturn: returnDate ? formatDate(returnDate) : null,
                    },
                    bubbles: true,
                });
                document.dispatchEvent(event);

                // TODO: Integrate with booking form or wizard
                console.log('Booking clicked:', {
                    departure: departureDate,
                    return: returnDate
                });
            });
        });
    }

    /**
     * Format date for display
     */
    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        } catch (err) {
            return dateString;
        }
    }

    /**
     * Restore selected departure date from sessionStorage
     */
    function restoreSelectedDate() {
        try {
            const selectedDate = sessionStorage.getItem('selectedDepartureDate');
            if (selectedDate) {
                // Highlight the selected date in the UI
                const buttons = document.querySelectorAll('[data-departure-date="' + selectedDate + '"]');
                buttons.forEach(btn => {
                    btn.classList.add('btn-primary--selected');
                    btn.style.outline = '2px solid #6AA9FF';
                    btn.style.outlineOffset = '2px';
                });
            }
        } catch (err) {
            console.warn('Could not restore departure date from sessionStorage', err);
        }
    }

    /**
     * Public API for external use
     */
    function setYearMonth(blockId, year, month) {
        const block = document.getElementById(blockId);
        if (!block) return;

        const dataScript = block.querySelector('.booking-data');
        if (!dataScript) return;

        let bookingData;
        try {
            bookingData = JSON.parse(dataScript.textContent);
        } catch (err) {
            return;
        }

        block.dataset.currentYear = year;
        block.dataset.currentMonth = month;

        updateVisibleDates(block, year, month);
        updateMonthLabel(block, year, month, bookingData.month_names);
        updateMonthNavButtons(block, bookingData);

        // Update year tabs
        const yearTabs = block.querySelectorAll('.year-tab');
        yearTabs.forEach(tab => {
            if (tab.dataset.year === year) {
                tab.classList.add('is-active');
                tab.setAttribute('aria-selected', 'true');
            } else {
                tab.classList.remove('is-active');
                tab.setAttribute('aria-selected', 'false');
            }
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initBookingBlocks();
            restoreSelectedDate();
        });
    } else {
        initBookingBlocks();
        restoreSelectedDate();
    }

    // Re-initialize on Gutenberg block updates (editor)
    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(() => {
            initBookingBlocks();
        });
    }

    // Expose public API
    window.TravelBlocks = window.TravelBlocks || {};
    window.TravelBlocks.BookingDates = {
        init: initBookingBlocks,
        setYearMonth: setYearMonth,
        formatDate: formatDate,
    };
})();
