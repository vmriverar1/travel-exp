<?php

/**

 * Block: Posts Carousel Native (CSS Scroll-Snap)

 *

 * Native CSS scroll-snap carousel with vanilla JavaScript.

 * No external dependencies (Swiper, etc.).

 *

 * ✅ REFACTORED v2.0.0: Now extends PostsCarouselUnified

 * This maintains 100% backward compatibility with existing content.

 * All functionality moved to PostsCarouselUnified to eliminate duplication.

 *

 * Previous Issues (NOW RESOLVED):

 * - ~70% code duplication with PostsCarousel ✅ ELIMINATED

 * - Does NOT inherit from BlockBase ✅ NOW INHERITS (via PostsCarouselUnified)

 * - Template MVC violations ✅ NOW USES BlockBase PATTERN

 * - No DocBlocks ✅ NOW FULLY DOCUMENTED

 * - Shared logic in 2 places ✅ NOW IN ONE PLACE

 *

 * Benefits of Consolidation:

 * - Eliminates ~250 lines of duplicated code

 * - Now properly inherits from BlockBase

 * - Single source of truth for bug fixes

 * - Consistent architecture with Material variant

 * - Easier maintenance and testing

 * - 100% backward compatible (no migration needed)

 *

 * Features (currently working):

 * - CSS scroll-snap native carousel

 * - Vanilla JavaScript (no libraries)

 * - Manual cards OR dynamic via ContentQueryHelper

 * - Desktop grid + Mobile slider

 * - Show/hide fields: category, location, price

 *

 * @package Travel\Blocks\ACF

 * @since 1.0.0

 * @version 2.0.0 - Refactored: Now wrapper around PostsCarouselUnified (eliminates duplication)

 *

 * @see PostsCarouselUnified Base class with shared logic

 * @see PostsCarousel Sibling block (Material variant)

 */



namespace Travel\Blocks\Blocks\ACF;



class PostsCarouselNative extends PostsCarouselUnified

{

    /**

     * Constructor - Delegates to unified implementation with 'native' variant.

     *

     * This simple constructor is all that's needed. All functionality

     * is inherited from PostsCarouselUnified parent class.

     *

     * Block name 'acf-gbr-posts-carousel' is preserved for backward compatibility.

     * All existing content continues to work without any migration.

     *

     * Architecture upgrade: Now properly inherits from BlockBase (via PostsCarouselUnified)

     * Previously this class did NOT inherit from BlockBase, causing architectural inconsistency.

     */

    public function __construct()

    {

        // Call parent with 'native' variant

        // This sets up the block with Native CSS configuration

        parent::__construct('native');

    }

}

