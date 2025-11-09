<?php
$seo_text = get_field('seo_text') ?: 'Texto descriptivo. Texto descriptivo.';
$cta_text = get_field('cta_text') ?: 'View Trip';

// Paquetes seleccionados (CPT)
$packages = get_field('selected_packages') ?: [];
if (empty($packages)) return;
?>

<section class="travel-packages-block">
    <!-- === GRID PRINCIPAL (DESKTOP) === -->
    <div class="travel-destinations__grid desktop-only">

        <!-- === COLUMNA 4 (TEXTO + IMAGEN) === -->
        <div class="travel-column column-4">
            <article class="travel-destination-text">
                <h3 class="travel-destinations__heading">
                    <?php echo esc_html(get_field('packages_title') ?: 'Popular Packages'); ?>
                </h3>
                <p class="travel-destinations__text"><?php echo esc_html($seo_text); ?></p>
            </article>

            <?php
            $pkg6 = $packages[6] ?? null;
            if ($pkg6):
                $image = get_field('main_image', $pkg6->ID) ?: get_the_post_thumbnail_url($pkg6->ID, 'large');
                $raw_price = get_field('price_from', $pkg6->ID);
                $price = $raw_price ? '$' . number_format((float) $raw_price, 0, '.', ',') : null;
                $day = get_field('days', $pkg6->ID) ?: 'Full Day';
                $locs  = get_field('locations', $pkg6->ID);
                $tag   = get_field('tag_label', $pkg6->ID) ?: 'By Train';
                if (is_array($locs)) {
                    $loc_names = [];
                    foreach ($locs as $loc_id) {
                        $post_obj = get_post($loc_id);
                        if ($post_obj) {
                            $loc_names[] = $post_obj->post_title;
                        }
                    }
                    $locs = implode(', ', $loc_names);
                }
            ?>
                <article class="travel-destination-item large travel-package-card">
                    <div class="card-thumb">
                        <?php if ($image): ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title($pkg6)); ?>">
                        <?php endif; ?>

                        <span class="card-tag"><?php echo esc_html($tag); ?></span>
                        <button class="favorite-btn" type="button" aria-label="Add to favorites">
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.81383 14.7194C7.73973 14.7194 7.66563 14.6911 7.60898 14.6348L1.524 8.59288C1.49335 8.56261 1.47085 8.52692 1.45688 8.48929C-0.503553 6.45203 -0.48687 3.20042 1.51546 1.18412C3.04253 -0.354195 5.53643 -0.362731 7.07436 1.16434L7.78745 1.87239L8.49551 1.15929C10.0222 -0.378638 12.5161 -0.387561 14.054 1.13951C16.0703 3.14184 16.1103 6.39345 14.1642 8.44428C14.1502 8.4823 14.1281 8.518 14.0978 8.54826L8.05554 14.6332C8.00123 14.6879 7.92712 14.719 7.84992 14.7194C7.84332 14.7198 7.83828 14.7194 7.83246 14.719C7.82625 14.7194 7.82004 14.7194 7.81383 14.7194ZM2.0175 8.26271L7.83013 14.0346L13.602 8.22197C13.6164 8.18473 13.6385 8.1502 13.6672 8.1211C15.4717 6.30383 15.4612 3.35716 13.6439 1.5527C12.3337 0.251432 10.2092 0.259192 8.90831 1.56938L7.99541 2.48888C7.88212 2.60294 7.69783 2.60333 7.58377 2.49043L6.66427 1.57753C5.35408 0.276263 3.22953 0.283634 1.92827 1.59382C1.05416 2.47414 0.575015 3.64233 0.57967 4.88268C0.583938 6.12342 1.07123 7.28773 1.95155 8.16184C1.98103 8.19171 2.00315 8.22585 2.0175 8.26271Z" fill="black" />
                            </svg>
                        </button>

                        <div class="card-overlay">
                            <div class="card-left">
                                <h4 class="card-title"><?php echo esc_html(get_the_title($pkg6)); ?></h4>
                                <div class="card-info">
                                    <?php if ($locs): ?><span><?php echo esc_html($locs); ?></span><?php endif; ?>
                                    <?php if ($day): ?>
                                        <span>
                                            <?php echo esc_html(is_numeric($day) ? "{$day} Days" : $day); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($price): ?><span> | From <?php echo esc_html($price); ?></span><?php endif; ?>
                                </div>
                            </div>
                            <a href="<?php echo esc_url(get_permalink($pkg6)); ?>" class="card-button">
                                <?php echo esc_html($cta_text); ?>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endif; ?>
        </div>

        <!-- === COLUMNA 2 y 3 (50% + 50%) === -->
        <?php
        $columns = [['start' => 2, 'end' => 3], ['start' => 4, 'end' => 5]];
        foreach ($columns as $col_index => $range):
        ?>
            <div class="travel-column column-<?php echo $col_index + 2; ?>">
                <?php
                for ($i = $range['start']; $i <= $range['end']; $i++):
                    $pkg = $packages[$i] ?? null;
                    if (!$pkg) continue;
                    $image = get_field('main_image', $pkg->ID) ?: get_the_post_thumbnail_url($pkg->ID, 'large');
                    $raw_price = get_field('price_from', $pkg->ID);
                    $price = $raw_price ? '$' . number_format((float) $raw_price, 0, '.', ',') : null;
                    $day = get_field('days', $pkg->ID) ?: 'Full Day';
                    $locs  = get_field('locations', $pkg->ID);
                    $tag   = get_field('tag_label', $pkg->ID) ?: 'By Train';
                    if (is_array($locs)) {
                        $loc_names = [];
                        foreach ($locs as $loc_id) {
                            $post_obj = get_post($loc_id);
                            if ($post_obj) {
                                $loc_names[] = $post_obj->post_title;
                            }
                        }
                        $locs = implode(', ', $loc_names);
                    }
                ?>
                    <article class="travel-destination-item medium travel-package-card">
                        <div class="card-thumb">
                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title($pkg)); ?>">
                            <?php endif; ?>

                            <span class="card-tag"><?php echo esc_html($tag); ?></span>
                            <button class="favorite-btn" type="button" aria-label="Add to favorites">

                                <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.81383 14.7194C7.73973 14.7194 7.66563 14.6911 7.60898 14.6348L1.524 8.59288C1.49335 8.56261 1.47085 8.52692 1.45688 8.48929C-0.503553 6.45203 -0.48687 3.20042 1.51546 1.18412C3.04253 -0.354195 5.53643 -0.362731 7.07436 1.16434L7.78745 1.87239L8.49551 1.15929C10.0222 -0.378638 12.5161 -0.387561 14.054 1.13951C16.0703 3.14184 16.1103 6.39345 14.1642 8.44428C14.1502 8.4823 14.1281 8.518 14.0978 8.54826L8.05554 14.6332C8.00123 14.6879 7.92712 14.719 7.84992 14.7194C7.84332 14.7198 7.83828 14.7194 7.83246 14.719C7.82625 14.7194 7.82004 14.7194 7.81383 14.7194ZM2.0175 8.26271L7.83013 14.0346L13.602 8.22197C13.6164 8.18473 13.6385 8.1502 13.6672 8.1211C15.4717 6.30383 15.4612 3.35716 13.6439 1.5527C12.3337 0.251432 10.2092 0.259192 8.90831 1.56938L7.99541 2.48888C7.88212 2.60294 7.69783 2.60333 7.58377 2.49043L6.66427 1.57753C5.35408 0.276263 3.22953 0.283634 1.92827 1.59382C1.05416 2.47414 0.575015 3.64233 0.57967 4.88268C0.583938 6.12342 1.07123 7.28773 1.95155 8.16184C1.98103 8.19171 2.00315 8.22585 2.0175 8.26271Z" fill="black" />
                                </svg>

                            </button>

                            <div class="card-overlay">
                                <div class="card-left">
                                    <h4 class="card-title"><?php echo esc_html(get_the_title($pkg)); ?></h4>
                                    <div class="card-info">
                                        <?php if ($locs): ?><p><?php echo esc_html($locs); ?></p><?php endif; ?>
                                        <?php if ($day): ?>
                                            <span>
                                                <?php echo esc_html(is_numeric($day) ? "{$day} Days" : $day); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($price): ?><span> | From <?php echo esc_html($price); ?></span><?php endif; ?>
                                    </div>
                                </div>
                                <a href="<?php echo esc_url(get_permalink($pkg)); ?>" class="card-button">
                                    <?php echo esc_html($cta_text); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endfor; ?>
            </div>
        <?php endforeach; ?>

        <!-- === COLUMNA 1 (20% + 80%) === -->
        <div class="travel-column column-1">
            <?php
            $pkg1 = $packages[0] ?? null;
            $pkg2 = $packages[1] ?? null;
            foreach ([$pkg1, $pkg2] as $i => $pkg):
                if (!$pkg) continue;
                $image = get_field('main_image', $pkg->ID) ?: get_the_post_thumbnail_url($pkg->ID, 'large');
                $raw_price = get_field('price_from', $pkg->ID);
                $price = $raw_price ? '$' . number_format((float) $raw_price, 0, '.', ',') : null;
                $day = get_field('days', $pkg->ID) ?: 'Full Day';
                $locs  = get_field('locations', $pkg->ID);
                $tag   = get_field('tag_label', $pkg->ID) ?: 'By Train';
                $class = $i === 0 ? 'small' : 'large';
                if (is_array($locs)) {
                    $loc_names = [];
                    foreach ($locs as $loc_id) {
                        $post_obj = get_post($loc_id);
                        if ($post_obj) {
                            $loc_names[] = $post_obj->post_title;
                        }
                    }
                    $locs = implode(', ', $loc_names);
                }
            ?>
                <article class="travel-destination-item <?php echo esc_attr($class); ?> travel-package-card">
                    <div class="card-thumb">
                        <?php if ($image): ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title($pkg)); ?>">
                        <?php endif; ?>

                        <span class="card-tag"><?php echo esc_html($tag); ?></span>
                        <button class="favorite-btn" type="button" aria-label="Add to favorites">

                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.81383 14.7194C7.73973 14.7194 7.66563 14.6911 7.60898 14.6348L1.524 8.59288C1.49335 8.56261 1.47085 8.52692 1.45688 8.48929C-0.503553 6.45203 -0.48687 3.20042 1.51546 1.18412C3.04253 -0.354195 5.53643 -0.362731 7.07436 1.16434L7.78745 1.87239L8.49551 1.15929C10.0222 -0.378638 12.5161 -0.387561 14.054 1.13951C16.0703 3.14184 16.1103 6.39345 14.1642 8.44428C14.1502 8.4823 14.1281 8.518 14.0978 8.54826L8.05554 14.6332C8.00123 14.6879 7.92712 14.719 7.84992 14.7194C7.84332 14.7198 7.83828 14.7194 7.83246 14.719C7.82625 14.7194 7.82004 14.7194 7.81383 14.7194ZM2.0175 8.26271L7.83013 14.0346L13.602 8.22197C13.6164 8.18473 13.6385 8.1502 13.6672 8.1211C15.4717 6.30383 15.4612 3.35716 13.6439 1.5527C12.3337 0.251432 10.2092 0.259192 8.90831 1.56938L7.99541 2.48888C7.88212 2.60294 7.69783 2.60333 7.58377 2.49043L6.66427 1.57753C5.35408 0.276263 3.22953 0.283634 1.92827 1.59382C1.05416 2.47414 0.575015 3.64233 0.57967 4.88268C0.583938 6.12342 1.07123 7.28773 1.95155 8.16184C1.98103 8.19171 2.00315 8.22585 2.0175 8.26271Z" fill="black" />
                            </svg>

                        </button>

                        <div class="card-overlay">
                            <div class="card-left">
                                <h4 class="card-title"><?php echo esc_html(get_the_title($pkg)); ?></h4>
                                <div class="card-info">
                                    <?php if ($locs): ?><p><?php echo esc_html($locs); ?></p><?php endif; ?>
                                    <?php if ($day): ?>
                                        <span>
                                            <?php echo esc_html(is_numeric($day) ? "{$day} Days" : $day); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($price): ?><span> | From <?php echo esc_html($price); ?></span><?php endif; ?>
                                </div>
                            </div>
                            <a href="<?php echo esc_url(get_permalink($pkg)); ?>" class="card-button">
                                <?php echo esc_html($cta_text); ?>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- === MOBILE === -->
    <aside class="travel-destinations__content mobile-only">
        <h3 class="travel-destinations__heading">
            <?php echo esc_html(get_field('packages_title') ?: 'Popular Packages'); ?>
        </h3>
        <p class="travel-destinations__text"><?php echo esc_html($seo_text); ?></p>
    </aside>

    <section class="travel-swiper-block travel-swiper--packages swiper-rows-1 mobile-only">
        <div class="tsb-swiper swiper">
            <div class="swiper-wrapper">
                <?php foreach ($packages as $pkg):
                    $image = get_field('main_image', $pkg->ID) ?: get_the_post_thumbnail_url($pkg->ID, 'large');
                    $price = get_field('price_from', $pkg->ID);
                    $day = get_field('days', $pkg->ID) ?: 'Full Day';
                    $locs  = get_field('locations', $pkg->ID);
                    $tag   = get_field('tag_label', $pkg->ID) ?: 'By Train';
                    if (is_array($locs)) {
                        $locs = implode(', ', array_map(fn($term) => is_object($term) ? $term->name : $term, $locs));
                    }
                ?>
                    <div class="swiper-slide">
                        <article class="travel-destination-item travel-package-card">
                            <div class="card-thumb">
                                <?php if ($image): ?>
                                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title($pkg)); ?>">
                                <?php endif; ?>

                                <span class="card-tag"><?php echo esc_html($tag); ?></span>
                                <button class="favorite-btn" type="button" aria-label="Add to favorites">

                                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.81383 14.7194C7.73973 14.7194 7.66563 14.6911 7.60898 14.6348L1.524 8.59288C1.49335 8.56261 1.47085 8.52692 1.45688 8.48929C-0.503553 6.45203 -0.48687 3.20042 1.51546 1.18412C3.04253 -0.354195 5.53643 -0.362731 7.07436 1.16434L7.78745 1.87239L8.49551 1.15929C10.0222 -0.378638 12.5161 -0.387561 14.054 1.13951C16.0703 3.14184 16.1103 6.39345 14.1642 8.44428C14.1502 8.4823 14.1281 8.518 14.0978 8.54826L8.05554 14.6332C8.00123 14.6879 7.92712 14.719 7.84992 14.7194C7.84332 14.7198 7.83828 14.7194 7.83246 14.719C7.82625 14.7194 7.82004 14.7194 7.81383 14.7194ZM2.0175 8.26271L7.83013 14.0346L13.602 8.22197C13.6164 8.18473 13.6385 8.1502 13.6672 8.1211C15.4717 6.30383 15.4612 3.35716 13.6439 1.5527C12.3337 0.251432 10.2092 0.259192 8.90831 1.56938L7.99541 2.48888C7.88212 2.60294 7.69783 2.60333 7.58377 2.49043L6.66427 1.57753C5.35408 0.276263 3.22953 0.283634 1.92827 1.59382C1.05416 2.47414 0.575015 3.64233 0.57967 4.88268C0.583938 6.12342 1.07123 7.28773 1.95155 8.16184C1.98103 8.19171 2.00315 8.22585 2.0175 8.26271Z" fill="black" />
                                    </svg>

                                </button>

                                <div class="card-overlay">
                                    <div class="card-left">
                                        <h4 class="card-title"><?php echo esc_html(get_the_title($pkg)); ?></h4>
                                        <div class="card-info">
                                            <?php if ($locs): ?><span><?php echo esc_html($locs); ?></span><?php endif; ?>
                                            <?php if ($day): ?>
                                                <span>
                                                    <?php echo esc_html(is_numeric($day) ? "{$day} Days" : $day); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($price): ?><span> | From <?php echo esc_html($price); ?></span><?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="<?php echo esc_url(get_permalink($pkg)); ?>" class="card-button">
                                        <?php echo esc_html($cta_text); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-controls">
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination__mobile"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
</section>