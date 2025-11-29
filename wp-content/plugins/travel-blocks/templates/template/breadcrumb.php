<?php

/**

 * Template: Breadcrumb Navigation (Template Block)

 *

 * Displays hierarchical breadcrumb navigation for packages

 *

 * @var array $breadcrumbs Array of breadcrumb items (title, url)

 * @var bool  $is_preview  Whether in preview mode

 *

 * @package Travel\Blocks

 */



// If no breadcrumbs, don't render

if (empty($breadcrumbs)) {

    return;

}

?>



<nav class="breadcrumb-navigation" aria-label="<?php esc_attr_e('Breadcrumb', 'travel-blocks'); ?>">

    <ol class="breadcrumb-list">

        <?php foreach ($breadcrumbs as $index => $item): ?>

            <?php

            $is_last = ($index === count($breadcrumbs) - 1);

            $has_url = !empty($item['url']);

            ?>

            <li class="breadcrumb-item <?php echo $is_last ? 'breadcrumb-item--current' : ''; ?>">

                <?php if ($has_url && !$is_last): ?>

                    <a href="<?php echo esc_url($item['url']); ?>" class="breadcrumb-link">

                        <?php echo esc_html($item['title']); ?>

                    </a>

                <?php else: ?>

                    <span class="breadcrumb-text">

                        <?php echo esc_html($item['title']); ?>

                    </span>

                <?php endif; ?>



                <?php if (!$is_last): ?>

                    <span class="breadcrumb-separator" aria-hidden="true">/</span>

                <?php endif; ?>

            </li>

        <?php endforeach; ?>

    </ol>

</nav>

