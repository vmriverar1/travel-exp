<?php
/**
 * Template: FAQ Accordion Block
 *
 * @var array  $block               Block settings
 * @var bool   $is_preview          Whether in preview mode
 * @var string $section_title       Section title
 * @var string $section_description Section description
 * @var array  $faq_items           FAQ items array
 * @var string $schema              JSON-LD schema markup
 *
 * @package Travel\Blocks
 */

// Generate unique block ID
$block_id = 'faq-' . $block['id'];

// Block classes
$classes = ['acf-block', 'acf-block-faq-accordion', 'faq-accordion'];

if (!empty($block['className'])) {
    $classes[] = $block['className'];
}
if (!empty($block['align'])) {
    $classes[] = 'align' . $block['align'];
}

?>
<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>">

    <?php if ($section_title): ?>
        <div class="faq-accordion__header">
            <h2 class="faq-accordion__title"><?php echo esc_html($section_title); ?></h2>
            <?php if ($section_description): ?>
                <p class="faq-accordion__description"><?php echo esc_html($section_description); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($faq_items)): ?>
        <div class="faq-accordion__items">
            <?php foreach ($faq_items as $index => $item): ?>
                <?php
                $item_id = $block_id . '-item-' . $index;
                $is_open = !empty($item['open_default']);
                ?>
                <div class="faq-accordion__item <?php echo $is_open ? 'is-open' : ''; ?>" data-faq-item>
                    <button class="faq-accordion__question"
                            aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                            aria-controls="<?php echo esc_attr($item_id); ?>"
                            data-faq-trigger>
                        <span class="faq-accordion__question-text"><?php echo esc_html($item['question']); ?></span>
                        <span class="faq-accordion__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path class="faq-accordion__icon-vertical" d="M10 4V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path class="faq-accordion__icon-horizontal" d="M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-accordion__answer"
                         id="<?php echo esc_attr($item_id); ?>"
                         data-faq-content
                         <?php if (!$is_open): ?>hidden<?php endif; ?>>
                        <div class="faq-accordion__answer-inner">
                            <?php echo wp_kses_post($item['answer']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="faq-accordion__empty"><?php _e('No FAQ items added yet.', 'travel-blocks'); ?></p>
    <?php endif; ?>

    <?php if ($schema && !$is_preview): ?>
        <script type="application/ld+json">
        <?php echo $schema; ?>
        </script>
    <?php endif; ?>

</section>
