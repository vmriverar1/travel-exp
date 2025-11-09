<?php
/**
 * Template: Related Posts Grid Block
 */

if (empty($posts)) return;
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>">
    <div class="related-posts-grid__inner">

        <?php if ($section_title || $section_subtitle): ?>
            <div class="related-posts-grid__header">
                <?php if ($section_title): ?>
                    <h2 class="related-posts-grid__title"><?php echo esc_html($section_title); ?></h2>
                <?php endif; ?>
                <?php if ($section_subtitle): ?>
                    <p class="related-posts-grid__subtitle"><?php echo esc_html($section_subtitle); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="related-posts-grid__grid">
            <?php foreach ($posts as $post): ?>
                <article class="related-posts-grid__item">
                    <a href="<?php echo esc_url($post['permalink']); ?>" class="related-posts-grid__link">

                        <?php if ($post['thumbnail']): ?>
                            <div class="related-posts-grid__image-wrapper">
                                <img
                                    src="<?php echo esc_url($post['thumbnail']); ?>"
                                    alt="<?php echo esc_attr($post['title']); ?>"
                                    class="related-posts-grid__image"
                                />
                                <div class="related-posts-grid__overlay">
                                    <span class="related-posts-grid__read-more"><?php echo esc_html($button_text); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="related-posts-grid__content">
                            <?php if ($show_category_badge && !empty($post['categories'])): ?>
                                <span class="related-posts-grid__category">
                                    <?php echo esc_html($post['categories'][0]['name']); ?>
                                </span>
                            <?php endif; ?>

                            <h3 class="related-posts-grid__post-title"><?php echo esc_html($post['title']); ?></h3>

                            <?php if ($show_excerpt && !empty($post['excerpt'])): ?>
                                <p class="related-posts-grid__excerpt">
                                    <?php
                                    $excerpt = wp_trim_words($post['excerpt'], $excerpt_length, '...');
                                    echo esc_html($excerpt);
                                    ?>
                                </p>
                            <?php endif; ?>

                            <time class="related-posts-grid__date" datetime="<?php echo esc_attr($post['date']); ?>">
                                <?php echo esc_html($post['date']); ?>
                            </time>
                        </div>

                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($show_more_button_text && $show_more_button_url): ?>
            <div class="related-posts-grid__footer">
                <a href="<?php echo esc_url($show_more_button_url); ?>" class="related-posts-grid__show-more">
                    <?php echo esc_html($show_more_button_text); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>
