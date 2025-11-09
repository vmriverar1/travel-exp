<?php
/**
 * Breadcrumb Template
 *
 * @var array $breadcrumbs Array of breadcrumb items with 'title' and 'url'
 * @var bool $is_preview Whether this is preview mode
 */

defined('ABSPATH') || exit;

if (empty($breadcrumbs)) {
    return;
}
?>

<nav class="breadcrumb-navigation" aria-label="<?php esc_attr_e('Breadcrumb', 'travel-blocks'); ?>">
    <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <?php if (!empty($crumb['url'])): ?>
                    <a href="<?php echo esc_url($crumb['url']); ?>" itemprop="item">
                        <span itemprop="name"><?php echo esc_html($crumb['title']); ?></span>
                    </a>
                <?php else: ?>
                    <span itemprop="name" aria-current="page"><?php echo esc_html($crumb['title']); ?></span>
                <?php endif; ?>
                <meta itemprop="position" content="<?php echo esc_attr($index + 1); ?>" />
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
