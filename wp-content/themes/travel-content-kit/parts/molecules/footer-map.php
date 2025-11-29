<?php
/**
 * Molecule: Footer Map
 * Mapa decorativo mundial (opcional)
 */

// Check if ACF is available
if (!function_exists('get_field')) {
    function get_field($field, $context = null) { return null; }
}

$map_image = get_field('footer_map_image', 'option');
?>

<?php if ($map_image): ?>
<div class="footer-map" aria-hidden="true">
    <img src="<?php echo esc_url($map_image); ?>" alt="" class="footer-map__image" loading="lazy" />
</div>
<?php else: ?>
<div class="footer-map" aria-hidden="true">
    <svg class="footer-map__svg" viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg">
        <!-- Mapa mundial simplificado con puntos de destino -->
        <!-- Este SVG es decorativo y representa conexiones globales -->

        <!-- Continentes simplificados -->
        <g class="footer-map__continents" opacity="0.1">
            <!-- América del Sur simplificada -->
            <path d="M180,180 Q190,160 210,150 L220,160 Q230,170 230,190 L240,220 Q235,240 220,250 L200,245 Q190,235 185,220 L180,200 Z" fill="currentColor"/>

            <!-- América del Norte simplificada -->
            <path d="M120,80 Q140,70 160,75 L175,90 Q180,110 175,130 L160,145 Q145,150 130,145 L115,125 Q110,105 120,80 Z" fill="currentColor"/>

            <!-- Europa simplificada -->
            <path d="M380,100 Q395,95 410,100 L420,115 Q422,130 415,140 L400,145 Q385,142 380,130 L380,115 Z" fill="currentColor"/>

            <!-- Asia simplificada -->
            <path d="M480,120 Q520,110 560,120 L580,145 Q590,170 580,195 L560,210 Q530,220 500,210 L480,190 Q475,165 480,120 Z" fill="currentColor"/>

            <!-- África simplificada -->
            <path d="M390,180 Q405,175 420,180 L430,210 Q435,240 425,265 L410,275 Q395,273 385,260 L380,235 Q380,205 390,180 Z" fill="currentColor"/>
        </g>

        <!-- Rutas de vuelo curvas -->
        <g class="footer-map__routes">
            <!-- Ruta América del Sur a América del Norte -->
            <path d="M220,200 Q170,140 145,110" stroke="currentColor" stroke-width="1" fill="none" opacity="0.3" stroke-dasharray="3,3"/>

            <!-- Ruta América a Europa -->
            <path d="M160,130 Q270,100 395,115" stroke="currentColor" stroke-width="1" fill="none" opacity="0.3" stroke-dasharray="3,3"/>

            <!-- Ruta Europa a Asia -->
            <path d="M410,120 Q450,115 500,140" stroke="currentColor" stroke-width="1" fill="none" opacity="0.3" stroke-dasharray="3,3"/>

            <!-- Ruta América a Asia (arco superior) -->
            <path d="M160,110 Q320,60 520,130" stroke="currentColor" stroke-width="1" fill="none" opacity="0.3" stroke-dasharray="3,3"/>
        </g>

        <!-- Marcadores de destinos -->
        <g class="footer-map__markers">
            <!-- Cusco/Machu Picchu (principal) -->
            <circle cx="220" cy="200" r="4" fill="var(--wp--preset--color--secondary)">
                <animate attributeName="r" values="4;5;4" dur="2s" repeatCount="indefinite"/>
            </circle>

            <!-- Nueva York -->
            <circle cx="145" cy="110" r="3" fill="var(--wp--preset--color--secondary)" opacity="0.8"/>

            <!-- Londres -->
            <circle cx="395" cy="115" r="3" fill="var(--wp--preset--color--secondary)" opacity="0.8"/>

            <!-- Tokio -->
            <circle cx="580" cy="145" r="3" fill="var(--wp--preset--color--secondary)" opacity="0.8"/>

            <!-- Sydney -->
            <circle cx="620" cy="240" r="3" fill="var(--wp--preset--color--secondary)" opacity="0.8"/>
        </g>
    </svg>
</div>
<?php endif; ?>
