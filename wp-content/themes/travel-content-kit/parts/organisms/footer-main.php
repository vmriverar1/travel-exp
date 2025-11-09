<?php
/**
 * Organism: Footer Main
 * Footer completo del sitio
 */
?>

<footer class="footer-main" role="contentinfo">
    <div class="footer-main__container">

        <!-- Sección Superior: 3 columnas -->
        <div class="footer-main__top">

            <!-- Columna Izquierda: Navegación -->
            <div class="footer-main__column footer-main__column--nav">
                <?php
                // Top Experiences
                get_template_part('parts/molecules/nav-footer-column', null, [
                    'menu_location' => 'footer-top-experiences',
                    'collapsible' => true,
                ]);

                // Treks & Adventure
                get_template_part('parts/molecules/nav-footer-column', null, [
                    'menu_location' => 'footer-treks-adventure',
                    'collapsible' => true,
                ]);

                // Culture & History
                get_template_part('parts/molecules/nav-footer-column', null, [
                    'menu_location' => 'footer-culture-history',
                    'collapsible' => true,
                ]);

                // Destinations
                get_template_part('parts/molecules/nav-footer-column', null, [
                    'menu_location' => 'footer-destinations',
                    'collapsible' => true,
                ]);

                // Payment Methods
                get_template_part('parts/molecules/payment-methods');
                ?>
            </div>

            <!-- Columna Central: Identidad de marca -->
            <div class="footer-main__column footer-main__column--brand">
                <?php
                get_template_part('parts/atoms/logo-footer');
                get_template_part('parts/molecules/footer-map');
                get_template_part('parts/molecules/social-media-bar');
                get_template_part('parts/molecules/footer-company-info');
                ?>
            </div>

            <!-- Columna Derecha: Información corporativa -->
            <div class="footer-main__column footer-main__column--info">
                <?php
                // About Machu Picchu Peru
                get_template_part('parts/molecules/nav-footer-column', null, [
                    'menu_location' => 'footer-about',
                    'collapsible' => true,
                ]);

                // Extra Information
                get_template_part('parts/molecules/nav-footer-column', null, [
                    'menu_location' => 'footer-extra-info',
                    'collapsible' => true,
                ]);

                // Contact Info
                get_template_part('parts/molecules/contact-info');
                ?>
            </div>

        </div>

    </div>

    <!-- Barra Legal Inferior -->
    <?php get_template_part('parts/molecules/footer-legal-bar'); ?>

</footer>
