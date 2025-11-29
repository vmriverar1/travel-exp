<div id="search-modal" class="search-modal">
    <div class="search-modal__overlay"></div>

    <div class="search-modal__content">
        <button class="search-modal__close" aria-label="Close search">
            &times;
        </button>

        <div class="search-modal__form">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <label>
                    <span class="screen-reader-text"><?php _e('Search for:', 'textdomain'); ?></span>
                    <input type="search"
                        class="search-field"
                        placeholder=""
                        value="<?php echo get_search_query(); ?>"
                        name="s"
                    />
                </label>

                <button type="submit" class="search-submit">
                    Search
                </button>
            </form>
        </div>
    </div>
</div>