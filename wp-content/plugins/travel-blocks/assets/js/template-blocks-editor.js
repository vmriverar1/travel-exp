/**
 * Template Blocks Editor Registration
 *
 * Registers native Template Blocks in the block editor
 */
(function(wp) {
    if (!wp || !wp.blocks) {
        console.error('WordPress blocks API not available');
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { Disabled } = wp.components;
    const ServerSideRender = wp.serverSideRender;
    const { __ } = wp.i18n;

    console.log('Registering Template Blocks...');

    // Helper function to create server-side render component
    function createServerSideEdit(blockName) {
        return function(props) {
            if (!ServerSideRender) {
                return el('div', {
                    style: {
                        padding: '20px',
                        border: '1px dashed #ccc',
                        textAlign: 'center',
                        color: '#666'
                    }
                }, '⚠️ Preview not available in editor. Block will render on frontend.');
            }

            return el(Disabled, {},
                el(ServerSideRender, {
                    block: blockName,
                    attributes: props.attributes
                })
            );
        };
    }

    // Helper function to register a block
    function registerTemplateBlock(name, title, description, icon, keywords) {
        try {
            registerBlockType('travel-blocks/' + name, {
                title: title,
                description: description,
                icon: icon,
                category: 'template-blocks',
                keywords: keywords || [],
                supports: {
                    anchor: true,
                    html: false
                },
                edit: createServerSideEdit('travel-blocks/' + name),
                save: function() {
                    return null;
                }
            });
            console.log('✓ ' + title + ' registered');
        } catch(e) {
            console.error('Error registering ' + title + ':', e);
        }
    }

    // Register all Template Blocks
    registerTemplateBlock('breadcrumb', 'Breadcrumb Navigation', 'Hierarchical breadcrumb navigation', 'arrow-right-alt', ['breadcrumb', 'navigation']);
    registerTemplateBlock('hero-media-grid', 'Hero Media Grid', 'Gallery carousel with map and video', 'format-gallery', ['hero', 'gallery', 'map', 'video']);
    registerTemplateBlock('package-header', 'Package Header', 'Package title, overview, and metadata', 'heading', ['header', 'title', 'metadata']);
    registerTemplateBlock('itinerary-day-by-day', 'Itinerary Day-by-Day', 'Accordion-style day-by-day itinerary', 'list-view', ['itinerary', 'schedule', 'days']);
    registerTemplateBlock('dates-and-prices', 'Dates and Prices', 'Departures calendar with pricing', 'calendar-alt', ['dates', 'prices', 'calendar']);
    registerTemplateBlock('inclusions-exclusions', 'Inclusions & Exclusions', 'What\'s included and not included', 'yes-alt', ['inclusions', 'exclusions', 'included']);
    registerTemplateBlock('contact-form', 'Contact Form', 'Quick contact form with AJAX', 'email', ['contact', 'form', 'inquiry']);
    registerTemplateBlock('pricing-card', 'Pricing Card', 'Sticky sidebar conversion card', 'cart', ['pricing', 'price', 'booking', 'conversion']);
    registerTemplateBlock('reviews-carousel', 'Reviews Carousel', 'Customer reviews mini-carousel', 'star-filled', ['reviews', 'testimonials', 'carousel']);
    // related-packages is now an ACF block, registered via PHP
    registerTemplateBlock('contact-planner-form', 'Contact Planner Form', 'Contact form with background image', 'email-alt', ['contact', 'planner', 'form']);
    registerTemplateBlock('traveler-reviews', 'Traveler Reviews', 'Large grid of reviews with filters', 'groups', ['reviews', 'travelers', 'testimonials']);
    registerTemplateBlock('related-posts-grid', 'Related Posts Grid', 'Related blog posts grid', 'admin-post', ['posts', 'blog', 'related', 'grid']);
    registerTemplateBlock('impact-section', 'Impact Section', 'Social responsibility section', 'heart', ['impact', 'social', 'responsibility']);
    registerTemplateBlock('trust-badges', 'Trust Badges', 'Trust badges and certifications', 'shield', ['trust', 'badges', 'certifications']);

    console.log('Template Blocks registration complete (14 native blocks)');
})(window.wp);
