<?php
/**
 * FAQ Accordion Template
 *
 * @var string $title FAQ section title
 * @var array $faqs Array of FAQ items with 'question' and 'answer'
 * @var bool $is_preview Whether this is preview mode
 */

defined('ABSPATH') || exit;

if (empty($faqs)) {
    return;
}
?>

<div class="faq-accordion">
    <div class="faq-accordion__container">
        <?php if (!empty($title)): ?>
            <h2 class="faq-accordion__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <div class="faq-accordion__list" itemscope itemtype="https://schema.org/FAQPage">
            <?php foreach ($faqs as $index => $faq): ?>
                <div class="faq-accordion__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button
                        class="faq-accordion__question"
                        aria-expanded="false"
                        aria-controls="faq-answer-<?php echo esc_attr($index); ?>"
                        data-faq-toggle
                    >
                        <span itemprop="name"><?php echo esc_html($faq['question']); ?></span>
                        <svg class="faq-accordion__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <div
                        class="faq-accordion__answer"
                        id="faq-answer-<?php echo esc_attr($index); ?>"
                        itemscope itemprop="acceptedAnswer"
                        itemtype="https://schema.org/Answer"
                        hidden
                    >
                        <div class="faq-accordion__answer-content" itemprop="text">
                            <?php echo wp_kses_post(wpautop($faq['answer'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
