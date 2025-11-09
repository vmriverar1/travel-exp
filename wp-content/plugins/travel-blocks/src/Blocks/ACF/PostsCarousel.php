<?php
/**
 * Block: Posts Carousel (Material Design)
 *
 * Material Design cards in 3-column desktop grid and mobile slider.
 * Supports manual cards or dynamic content from packages/posts/deals.
 *
 * ✅ REFACTORED v2.0.0: Now extends PostsCarouselUnified
 * This maintains 100% backward compatibility with existing content.
 * All functionality moved to PostsCarouselUnified to eliminate duplication.
 *
 * Previous Issues (NOW RESOLVED):
 * - ~70% code duplication with PostsCarouselNative ✅ ELIMINATED
 * - Shared logic scattered across 2 files ✅ NOW IN ONE PLACE
 * - Bug fixes required in 2 places ✅ NOW SINGLE SOURCE OF TRUTH
 *
 * Benefits of Consolidation:
 * - Eliminates ~800 lines of duplicated code
 * - Single source of truth for bug fixes
 * - Consistent behavior guaranteed
 * - Easier maintenance and testing
 * - 100% backward compatible (no migration needed)
 *
 * Features:
 * - Desktop: 3-column grid with hover effects
 * - Mobile: Material Design slider with navigation
 * - Manual cards via ACF repeater OR dynamic via ContentQueryHelper
 * - 6 button color variants + 6 badge variants
 * - Show/hide fields: category, location, price, excerpt, CTA
 * - Grid effects: squeeze, lift, glow, zoom
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 2.0.0 - Refactored: Now wrapper around PostsCarouselUnified (eliminates duplication)
 *
 * @see PostsCarouselUnified Base class with shared logic
 * @see PostsCarouselNative Sibling block (Native variant)
 */

namespace Travel\Blocks\ACF;

class PostsCarousel extends PostsCarouselUnified
{
    /**
     * Constructor - Delegates to unified implementation with 'material' variant.
     *
     * This simple constructor is all that's needed. All functionality
     * is inherited from PostsCarouselUnified parent class.
     *
     * Block name 'posts-carousel' is preserved for backward compatibility.
     * All existing content continues to work without any migration.
     */
    public function __construct()
    {
        // Call parent with 'material' variant
        // This sets up the block with Material Design configuration
        parent::__construct('material');
    }
}
