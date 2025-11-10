# Auditoría: ProductGalleryHero (Package)

**Fecha:** 2025-11-09
**Bloque:** 13/XX Package
**Tiempo:** 45 min
**⚠️ ESTADO:** CRÍTICO - Galería hero con dependencias CDN y doble inicialización JS
**🔴 CRITICIDAD:** ALTA - Maneja galería de imágenes principal (hero gallery)
**⚠️ NOTA IMPORTANTE:** Usa librerías externas CDN (Swiper + GLightbox) y tiene código JS inline en template

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/product-gallery-hero`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Campo meta principal:** `gallery` (ACF Gallery field)
- **Campos adicionales:** `promo_tag`, `promo_tag_color`, `promo_enabled`, `activity_level`
- **Icon:** `format-gallery`
- **Category:** `travel`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **Usa template separado** ✅ (product-gallery-hero.php - 207 líneas)
- **JavaScript INLINE en template** 🔴 (líneas 132-206) + archivo JS separado
- **Dependencias CDN externas** 🔴 (Swiper + GLightbox desde jsdelivr.net)
- **Lazy loading en todas las imágenes** ⚠️ (primera debería ser eager)
- **NO usa responsive images** ⚠️ (solo 'large' size, no srcset)
- **ACF dependency:** Campo `gallery` debe ser array de IDs o array con estructura `[{ID, url, alt}]`

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** ✅ Usa is_editor_mode() correctamente
- **IconHelper:** ✅ Para flechas de navegación SVG
- **Swiper 11.0.0:** 🔴 Desde CDN jsdelivr.net (slider principal)
- **GLightbox 3.2.0:** 🔴 Desde CDN jsdelivr.net (lightbox)
- **ACF Gallery field:** gallery (puede ser array de IDs o arrays completos)
- **Template:** product-gallery-hero.php (207 líneas con JS inline)
- **CSS:** product-gallery-hero.css (341 líneas)
- **JS:** product-gallery-hero.js (48 líneas - parece redundante)

### 🔴 RIESGOS CRÍTICOS - DEPENDENCIAS CDN
**ALERTA DE SEGURIDAD Y PERFORMANCE:**
Este bloque carga **dos librerías pesadas desde CDN público** (jsdelivr.net):
1. **Swiper 11.0.0** - Librería de carrusel (~150KB)
2. **GLightbox 3.2.0** - Librería de lightbox (~50KB)

**PROBLEMAS:**
- ⚠️ Dependencia de CDN externo (SPOF - Single Point of Failure)
- ⚠️ Si CDN cae, galería se rompe completamente
- ⚠️ Posible violación GDPR (requests a servidores externos)
- ⚠️ Performance degradado en redes lentas
- ⚠️ Carga SIEMPRE incluso si no hay bloque en página (línea 58: `if (!is_admin())`)

**RECOMENDACIÓN:** Migrar a assets locales o cargar condicionalmente solo cuando bloque está presente.

### ⚠️ PROBLEMA - DOBLE INICIALIZACIÓN JS
**ACLARACIÓN CRÍTICA:** Este bloque tiene JavaScript en **DOS lugares diferentes**:
1. **Template inline** (líneas 132-206): Inicialización completa de Swiper + GLightbox
2. **Archivo JS separado** (product-gallery-hero.js): Solo marca como inicializado

Esto es **redundante y confuso**. El archivo JS prácticamente no hace nada útil.

### ⚠️ PROBLEMA - LAZY LOADING MAL IMPLEMENTADO
Todas las imágenes tienen `loading="lazy"` (línea 59, 67), incluyendo la primera imagen visible. Esto causa:
- Retraso en LCP (Largest Contentful Paint)
- Primera imagen debería ser `loading="eager"` o sin atributo

### ⚠️ PROBLEMA - NO USA RESPONSIVE IMAGES
Las imágenes usan solo un tamaño fijo (líneas 57, 65):
```php
$image['sizes']['large'] ?? $image['url']
```

**FALTA:**
- `srcset` para diferentes densidades de pantalla
- `sizes` attribute para responsive loading
- Optimización para mobile/tablet/desktop

---

## 1. Información General

**Ubicación:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/src/Blocks/Package/ProductGalleryHero.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** ✅ `/templates/product-gallery-hero.php` (207 líneas - incluye JS inline)
**Assets:**
- CSS: `/assets/blocks/product-gallery-hero.css` (341 líneas)
- JS: `/assets/blocks/product-gallery-hero.js` (48 líneas - redundante)
- CDN Swiper CSS: https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css
- CDN Swiper JS: https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js
- CDN GLightbox CSS: https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css
- CDN GLightbox JS: https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- ✅ EditorHelper::is_editor_mode() (correctamente usado)
- ✅ IconHelper::get_icon_svg() (para flechas navegación)
- 🔴 Swiper 11.0.0 (CDN externo - jsdelivr.net)
- 🔴 GLightbox 3.2.0 (CDN externo - jsdelivr.net)
- ACF Gallery field (gallery)
- WordPress meta functions (get_post_meta, get_post_thumbnail_id)

**Líneas de Código:**
- **Clase PHP:** 280 líneas
- **Template:** 207 líneas (incluye 75 líneas de JS inline)
- **JavaScript:** 48 líneas (archivo separado redundante)
- **CSS:** 341 líneas
- **TOTAL:** 876 líneas

**Métodos más largos:**
1. `get_post_data()` - 70 líneas (189-258) ⚠️ Demasiado largo
2. `render()` - 42 líneas (115-156)
3. `enqueue_assets()` - 54 líneas (56-110) - Carga todas las dependencias CDN
4. `load_template()` - 17 líneas (263-279)

---

## 2. Propósito y Funcionalidad

**Descripción:** Galería de imágenes full-width con carrusel Swiper, cinta promocional diagonal, thumbnails circulares, indicador de nivel de actividad, botón "View Photos", y lightbox GLightbox para visualización ampliada.

**Funcionalidad Principal:**

1. **Carrusel de imágenes (Swiper):**
   - Loop infinito
   - Efecto fade con crossfade
   - Autoplay opcional (configurable)
   - Navegación con flechas
   - Paginación con thumbnails
   - Lazy loading (swiper interno)

2. **Galería de imágenes:**
   - Obtiene de campo meta 'gallery' (ACF)
   - Soporta array de IDs o array de objetos `[{ID, url, alt}]`
   - Fallback a featured image si no hay galería
   - Placeholder si no hay imágenes

3. **Lightbox (GLightbox):**
   - Click en imagen abre lightbox
   - Navegación táctil
   - Loop infinito
   - Botón "View Photos" abre primera imagen

4. **Cinta promocional diagonal:**
   - Texto personalizable (promo_tag)
   - Color personalizable (promo_tag_color)
   - Posiciones: top-left (default) o top-right
   - Rotación -45deg o 45deg

5. **Indicador de nivel de actividad:**
   - Icono de montaña SVG
   - Label de nivel (Low/Moderate/High/Very High)
   - Dots visuales (2-5 dots según nivel)
   - Posición: bottom-left

6. **Elementos UI:**
   - Navegación con iconos SVG (IconHelper)
   - Thumbnails circulares o cuadrados
   - Botón "View all Photos" (bottom-right)
   - Responsive design

**Flujo de Datos:**
```
get_the_ID() → get_post_data($post_id)
  ├─ get_post_meta('gallery') → Array de imágenes
  │   ├─ Si es ID numérico: wp_get_attachment_image_url()
  │   └─ Si es array: usa url/alt directos
  ├─ get_post_meta('promo_tag', 'promo_tag_color', 'promo_enabled')
  ├─ get_post_meta('activity_level')
  └─ Fallback: get_post_thumbnail_id() si no hay gallery

load_template('product-gallery-hero', $data)
  ├─ Render HTML con Swiper structure
  ├─ JS inline: new Swiper() + GLightbox()
  └─ Event listeners para botón View Photos
```

---

## 3. Análisis de Código

### ✅ Fortalezas

1. **Try-catch en render** ✅
   - Captura excepciones
   - Muestra error solo en WP_DEBUG
   - Retorna string vacío en producción

2. **Modo preview robusto** ✅
   - Detecta editor con EditorHelper
   - Datos de preview con URLs de picsum.photos
   - Evita errores en editor

3. **Fallback a featured image** ✅
   - Si no hay gallery, usa featured image
   - Previene galería vacía

4. **Placeholder elegante** ✅
   - Mensaje informativo si no hay imágenes
   - Return temprano para evitar render vacío

5. **Soporte flexible para gallery field** ✅
   - Acepta array de IDs numéricos
   - Acepta array de objetos ACF completos
   - Extrae correctamente url/alt en ambos casos

6. **CSS bien estructurado** ✅
   - Variables CSS para colores
   - Media queries responsive
   - Print styles
   - Loading states
   - Focus states para accesibilidad

7. **Alignment support** ✅
   - Soporta alignwide y alignfull
   - Cálculos correctos con viewport

### 🔴 Problemas Críticos

1. **Dependencias CDN externas** 🔴🔴🔴
   ```php
   wp_enqueue_style('swiper-css',
       'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css'
   );
   ```
   - SPOF (Single Point of Failure)
   - Riesgo de seguridad
   - Posible violación GDPR
   - Performance degradado

2. **JavaScript inline en template** 🔴🔴
   - 75 líneas de JS en template PHP (132-206)
   - Viola separación de responsabilidades
   - Dificulta mantenimiento
   - No se puede minimizar/cachear
   - Se repite en cada instancia del bloque

3. **Lazy loading mal implementado** 🔴
   ```php
   loading="lazy"  // Línea 59, 67 - en TODAS las imágenes
   ```
   - Primera imagen debería ser `eager`
   - Daña LCP (Largest Contentful Paint)
   - Core Web Vitals afectados

4. **NO usa responsive images** 🔴
   ```php
   src="<?php echo esc_url($image['sizes']['large'] ?? $image['url']); ?>"
   ```
   - Falta `srcset` attribute
   - Falta `sizes` attribute
   - Carga imagen grande en mobile
   - Desperdicia bandwidth

5. **Carga incondicional de assets** 🔴
   ```php
   if (!is_admin()) {  // Línea 58
       // Carga SIEMPRE, incluso si no hay bloque
   ```
   - Debería verificar si hay bloque en página
   - Carga ~200KB de librerías innecesarias

6. **NO hereda de BlockBase** ⚠️
   - Inconsistente con arquitectura
   - Duplica método load_template()
   - Pierde beneficios de clase base

### ⚠️ Problemas Menores

1. **Usa extract() con EXTR_SKIP** ⚠️
   ```php
   extract($data, EXTR_SKIP);  // Línea 276
   ```
   - Potencial problema de seguridad
   - Variables mágicas en scope
   - Dificulta debug

2. **Archivo JS redundante** ⚠️
   - product-gallery-hero.js casi no hace nada
   - Solo marca `data-initialized="true"`
   - Inicialización real está en template

3. **Hardcoded SVG en template** ⚠️
   - Icono de montaña inline (líneas 94-103)
   - Debería estar en IconHelper
   - Dificulta reutilización

4. **Lógica de activity level duplicada** ⚠️
   ```php
   $activity_labels = [
       'low' => 'Low',
       'moderate' => 'Moderate',
       // ...
   ];
   ```
   - Debería estar en helper o config
   - Dificulta i18n

5. **Magic numbers** ⚠️
   - Swiper speed: 600 (línea 171)
   - Retry interval: 100ms (línea 142)
   - Sin constantes descriptivas

6. **Falta validación de datos** ⚠️
   - No valida que $gallery sea array
   - No valida estructura de imágenes
   - Confía en data del campo ACF

---

## 4. Violaciones SOLID

### S - Single Responsibility ❌

**Violaciones:**
1. **Clase hace demasiadas cosas:**
   - Registra bloque
   - Carga 6 assets diferentes (4 CDN + 2 locales)
   - Obtiene datos de múltiples campos meta
   - Cuenta/mapea activity levels
   - Renderiza template
   - Maneja preview y fallbacks

   **Impacto:** Difícil de mantener, testing complejo

2. **Template mezcla HTML + JS:**
   - Template tiene 75 líneas de JavaScript inline
   - Viola separación de responsabilidades
   - Dificulta caching y minificación

### O - Open/Closed ⚠️

**Cumplimiento parcial:**
- ✅ Usa template separado (extensible)
- ✅ Usa hooks de WordPress
- ❌ Activity levels hardcoded (no extensible)
- ❌ CDN URLs hardcoded

### L - Liskov Substitution ❌

**Violación:**
- NO hereda de BlockBase
- Si heredara, no sería sustituible porque duplica load_template()
- Inconsistente con otros bloques del sistema

### I - Interface Segregation ✅

**Cumplimiento:**
- No usa interfaces innecesarias
- Métodos públicos mínimos (register, enqueue_assets, render)

### D - Dependency Inversion ❌

**Violaciones:**
1. **Depende de implementaciones concretas:**
   - Hardcoded CDN URLs (no inyectables)
   - Hardcoded meta keys
   - Hardcoded template paths

2. **Debería depender de abstracciones:**
   - AssetManagerInterface
   - GalleryDataProvider
   - ConfigurationManager

---

## 5. Seguridad

### ✅ Buenas Prácticas

1. **Escape de output** ✅
   - `esc_attr()` en todos los atributos HTML
   - `esc_url()` en todas las URLs
   - `esc_html()` en texto visible
   - `esc_js()` en JS inline

2. **Try-catch en render** ✅
   - Captura excepciones
   - No expone errores en producción

3. **Verificación de archivos** ✅
   ```php
   if (!file_exists($template_path)) {
   ```

### 🔴 Riesgos de Seguridad

1. **Dependencias CDN sin SRI** 🔴🔴
   ```php
   wp_enqueue_style('swiper-css',
       'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css'
   );
   ```
   - NO usa Subresource Integrity (SRI)
   - CDN comprometido = código malicioso
   - Violación de Content Security Policy

   **SOLUCIÓN:**
   ```php
   wp_enqueue_style('swiper-css', $url, [], $version, 'all', [
       'integrity' => 'sha384-...',
       'crossorigin' => 'anonymous'
   ]);
   ```

2. **extract() con user data** ⚠️
   ```php
   extract($data, EXTR_SKIP);
   ```
   - Aunque usa EXTR_SKIP, sigue siendo riesgoso
   - Crea variables en scope de forma mágica
   - Dificulta auditorías de seguridad

3. **JavaScript inline con PHP variables** ⚠️
   ```php
   const blockId = '<?php echo esc_js($block_id); ?>';
   ```
   - Aunque usa esc_js(), mezclar PHP/JS es arriesgado
   - Mejor usar wp_localize_script()

4. **No valida datos de gallery** ⚠️
   - Confía que get_post_meta() retorna datos válidos
   - No valida estructura de array
   - Podría causar errores si ACF se desactiva

### Recomendaciones

1. Migrar CDN a assets locales
2. Añadir SRI hashes si se mantiene CDN
3. Eliminar extract(), usar variables directas
4. Validar estructura de $gallery antes de usar
5. Mover JS inline a archivo separado con wp_localize_script()

---

## 6. Performance

### ⚠️ Problemas de Performance

1. **Librerías pesadas desde CDN** 🔴
   - Swiper: ~150KB (minified)
   - GLightbox: ~50KB (minified)
   - Total: ~200KB de dependencias externas
   - Carga en CADA página (incluso sin bloque)

2. **Lazy loading mal implementado** 🔴
   - Primera imagen con `loading="lazy"`
   - Retrasa LCP (Largest Contentful Paint)
   - Afecta Core Web Vitals

3. **Sin responsive images** 🔴
   ```php
   src="<?php echo esc_url($image['sizes']['large'] ?? $image['url']); ?>"
   ```
   - Carga imagen "large" en todos los dispositivos
   - Mobile descarga imagen desktop completa
   - Desperdicia bandwidth

4. **JavaScript inline repetido** ⚠️
   - 75 líneas de JS en cada instancia del bloque
   - No se puede cachear
   - No se puede minimizar
   - Aumenta HTML size

5. **Sin preloading crítico** ⚠️
   - No preload de primera imagen
   - No preconnect a CDN
   - Retrasa FCP/LCP

### Recomendaciones

1. **Optimizar carga de imágenes:**
   ```php
   // Primera imagen
   <img
       src="<?php echo esc_url($image['large']); ?>"
       srcset="<?php echo esc_attr($image['srcset']); ?>"
       sizes="(max-width: 768px) 100vw, (max-width: 1024px) 100vw, 1280px"
       loading="eager"  // Primera imagen
       fetchpriority="high"
   />

   // Resto de imágenes
   loading="lazy"
   ```

2. **Migrar a assets locales:**
   - Descargar Swiper y GLightbox
   - Servir desde /assets/vendor/
   - Minimizar y concatenar

3. **Carga condicional:**
   ```php
   if (has_block('travel-blocks/product-gallery-hero')) {
       wp_enqueue_style('swiper-css');
   }
   ```

4. **Mover JS inline a archivo:**
   - Usar wp_localize_script() para pasar data
   - Permitir caching y minificación

5. **Preload crítico:**
   ```php
   add_action('wp_head', function() {
       if (has_block('travel-blocks/product-gallery-hero')) {
           echo '<link rel="preconnect" href="https://cdn.jsdelivr.net">';
           // O mejor: preload primera imagen
       }
   });
   ```

---

## 7. Mantenibilidad

### ✅ Aspectos Positivos

1. **Código bien comentado** ✅
   - DocBlocks en todos los métodos
   - Comentarios explicativos en template
   - Variables documentadas

2. **Template separado** ✅
   - Lógica separada de presentación
   - Fácil personalizar HTML

3. **Código limpio y legible** ✅
   - Indentación consistente
   - Nombres descriptivos
   - Estructura clara

4. **CSS bien organizado** ✅
   - BEM naming convention
   - Secciones comentadas
   - Media queries agrupadas

### ⚠️ Problemas de Mantenibilidad

1. **JavaScript en 2 lugares** 🔴
   - Template inline (75 líneas)
   - Archivo JS separado (48 líneas)
   - Confusión sobre dónde modificar

2. **Activity levels hardcoded** ⚠️
   - Arrays de labels y dots duplicables
   - Difícil i18n
   - No reutilizable

3. **Magic numbers sin constantes** ⚠️
   ```php
   'speed' => 600,
   setTimeout(initGallery, 100);
   ```

4. **No usa BlockBase** ⚠️
   - Duplica load_template()
   - Inconsistente con otros bloques
   - Dificulta cambios globales

5. **CDN versions hardcoded** ⚠️
   - Actualizar requiere cambiar 4 URLs
   - Sin gestión centralizada de versiones

---

## 8. Testing

### Estado Actual: ❌ NO HAY TESTS

**Cobertura:** 0%

### Tests Recomendados

1. **Unit Tests:**
   ```php
   test_render_returns_empty_when_no_images()
   test_render_uses_featured_image_as_fallback()
   test_gallery_handles_numeric_ids()
   test_gallery_handles_acf_objects()
   test_activity_level_labels_mapping()
   test_discount_badge_shows_when_enabled()
   test_discount_badge_hidden_when_disabled()
   ```

2. **Integration Tests:**
   ```php
   test_swiper_initialization()
   test_glightbox_initialization()
   test_view_button_opens_lightbox()
   test_assets_enqueued_correctly()
   ```

3. **Visual Regression:**
   - Gallery con 3 imágenes
   - Gallery con 1 imagen (fallback)
   - Sin imágenes (placeholder)
   - Con discount badge
   - Con activity level
   - Mobile/tablet/desktop

---

## 9. Arquitectura

### Diseño Actual

```
ProductGalleryHero (standalone class)
├─ register() → register_block_type()
├─ enqueue_assets() → 6 assets (4 CDN + 2 local)
├─ render() → Try-catch wrapper
│   ├─ EditorHelper::is_editor_mode()
│   ├─ get_preview_data() o get_post_data()
│   └─ load_template()
├─ get_preview_data() → Mock data
├─ get_post_data() → get_post_meta()
└─ load_template() → extract() + include

Template (product-gallery-hero.php)
├─ HTML structure (Swiper)
├─ Discount badge
├─ Activity indicator
├─ View button
└─ JS inline (75 líneas) 🔴
    ├─ Swiper init
    └─ GLightbox init

JS separado (product-gallery-hero.js)
└─ data-initialized flag ⚠️ (redundante)
```

### ❌ Problemas Arquitectónicos

1. **No hereda de BlockBase**
   - Duplica load_template()
   - Pierde beneficios de estandarización

2. **Responsabilidades mezcladas**
   - Template tiene lógica JS
   - Clase maneja 6 assets diferentes
   - Mapeo de activity levels en render

3. **Alto acoplamiento con CDN**
   - Hardcoded jsdelivr.net URLs
   - No inyectable, no testeable

4. **Dependencias no explícitas**
   - IconHelper usado en template sin inyección
   - ACF field structure asumida

### ✅ Arquitectura Recomendada

```php
class ProductGalleryHero extends BlockBase
{
    private GalleryDataProvider $dataProvider;
    private AssetManager $assetManager;
    private ConfigProvider $config;

    public function __construct(
        GalleryDataProvider $dataProvider,
        AssetManager $assetManager,
        ConfigProvider $config
    ) {
        $this->dataProvider = $dataProvider;
        $this->assetManager = $assetManager;
        $this->config = $config;
    }

    protected function get_data(int $post_id): array
    {
        return $this->dataProvider->get_gallery_data($post_id);
    }

    public function enqueue_assets(): void
    {
        $this->assetManager->enqueue_gallery_assets();
    }
}

class GalleryDataProvider
{
    public function get_gallery_data(int $post_id): array
    {
        // Lógica de obtención de datos
    }

    private function get_activity_config(): array
    {
        return $this->config->get('activity_levels');
    }
}

class AssetManager
{
    public function enqueue_gallery_assets(): void
    {
        // Carga condicional
        if (!has_block('travel-blocks/product-gallery-hero')) {
            return;
        }

        // Assets locales (no CDN)
    }
}
```

---

## 10. Documentación

### ✅ Bien Documentado

1. **DocBlock de clase** ✅
   - Descripción completa
   - Features listadas
   - Package y version

2. **DocBlocks de métodos** ✅
   - Todos los métodos tienen DocBlock
   - Tipos de retorno especificados

3. **Comentarios en template** ✅
   - Variables disponibles documentadas
   - Secciones HTML comentadas

4. **CSS bien comentado** ✅
   - Secciones marcadas
   - Media queries explicadas

### ⚠️ Falta Documentación

1. **README específico del bloque** ❌
   - Cómo configurar ACF gallery
   - Dependencias externas (CDN)
   - Troubleshooting

2. **Ejemplos de uso** ❌
   - Código de ejemplo
   - Screenshots

3. **Documentación de CDN** ❌
   - Por qué se usa CDN
   - Plan de migración a local

4. **Changelog** ❌
   - Historial de cambios
   - Breaking changes

---

## 11. Comparación con Estándares del Proyecto

### Estándares Cumplidos ✅

1. ✅ Namespace correcto: `Travel\Blocks\Blocks\Package`
2. ✅ Template en `/templates/`
3. ✅ Assets en `/assets/blocks/`
4. ✅ Usa EditorHelper para preview
5. ✅ Try-catch en render
6. ✅ Escape de output (esc_attr, esc_url, esc_html)
7. ✅ Support para alignment (wide, full)
8. ✅ Keywords descriptivos

### Estándares NO Cumplidos ❌

1. ❌ NO hereda de BlockBase (inconsistente)
2. ❌ JavaScript inline en template (viola separación)
3. ❌ Usa extract() (no recomendado)
4. ❌ CDN externo (riesgo de seguridad/performance)
5. ❌ NO usa SRI para CDN
6. ❌ Carga incondicional de assets
7. ❌ NO usa responsive images (srcset/sizes)
8. ❌ Lazy loading en primera imagen

---

## 12. Recomendaciones Priorizadas

### 🔴 Prioridad CRÍTICA (Hacer YA)

1. **Migrar CDN a assets locales** 🔴🔴🔴
   - Descargar Swiper 11.0.0 y GLightbox 3.2.0
   - Colocar en `/assets/vendor/`
   - Actualizar enqueue_assets()
   - **IMPACTO:** Seguridad, GDPR, Performance
   - **ESFUERZO:** 2 horas

2. **Añadir SRI si se mantiene CDN** 🔴🔴
   - Calcular hashes SHA-384
   - Añadir integrity attribute
   - **IMPACTO:** Seguridad
   - **ESFUERZO:** 30 min

3. **Implementar responsive images** 🔴🔴
   ```php
   <img
       src="<?php echo esc_url($image['sizes']['large']); ?>"
       srcset="<?php echo wp_get_attachment_image_srcset($image_id, 'large'); ?>"
       sizes="(max-width: 768px) 100vw, (max-width: 1024px) 100vw, 1280px"
       loading="<?php echo $is_first ? 'eager' : 'lazy'; ?>"
       fetchpriority="<?php echo $is_first ? 'high' : 'auto'; ?>"
   />
   ```
   - **IMPACTO:** Performance, Core Web Vitals
   - **ESFUERZO:** 1 hora

4. **Carga condicional de assets** 🔴
   ```php
   if (has_block('travel-blocks/product-gallery-hero')) {
       // enqueue assets
   }
   ```
   - **IMPACTO:** Performance global
   - **ESFUERZO:** 30 min

### ⚠️ Prioridad ALTA (Próxima semana)

5. **Mover JS inline a archivo separado**
   - Eliminar <script> de template (líneas 132-206)
   - Consolidar en product-gallery-hero.js
   - Usar wp_localize_script() para data
   - **IMPACTO:** Caching, Mantenibilidad
   - **ESFUERZO:** 2 horas

6. **Heredar de BlockBase**
   - Extender BlockBase
   - Eliminar load_template() duplicado
   - Implementar get_data() abstract
   - **IMPACTO:** Consistencia arquitectónica
   - **ESFUERZO:** 1 hora

7. **Eliminar extract()**
   - Usar variables directas en template
   - **IMPACTO:** Seguridad, Code quality
   - **ESFUERZO:** 30 min

### ℹ️ Prioridad MEDIA (Próximo mes)

8. **Extraer activity levels a config**
   - Crear ActivityLevelConfig helper
   - Centralizar labels y dots
   - **IMPACTO:** Reutilización, i18n
   - **ESFUERZO:** 1 hora

9. **Añadir tests unitarios**
   - Tests de render
   - Tests de data processing
   - **IMPACTO:** Confiabilidad
   - **ESFUERZO:** 4 horas

10. **Mover SVG de montaña a IconHelper**
    - Añadir 'mountain' icon a IconHelper
    - Reemplazar inline SVG
    - **IMPACTO:** Reutilización
    - **ESFUERZO:** 30 min

### 📝 Prioridad BAJA (Backlog)

11. **Crear README del bloque**
12. **Añadir ejemplos de uso**
13. **Documentar troubleshooting**

---

## 13. Puntuación Final

### Puntuación por Categoría

| Categoría | Puntuación | Peso | Ponderado |
|-----------|------------|------|-----------|
| **SOLID Principles** | 3/10 | 20% | 0.60 |
| **Seguridad** | 5/10 | 25% | 1.25 |
| **Performance** | 4/10 | 20% | 0.80 |
| **Mantenibilidad** | 6/10 | 15% | 0.90 |
| **Testing** | 0/10 | 10% | 0.00 |
| **Documentación** | 7/10 | 10% | 0.70 |

### 📊 PUNTUACIÓN TOTAL: 4.25/10

**Rating:** ⚠️ **NECESITA MEJORAS URGENTES**

### Justificación

**Fortalezas principales:**
- ✅ Template bien estructurado y legible
- ✅ CSS responsive y bien organizado
- ✅ Manejo robusto de preview y fallbacks
- ✅ Try-catch y escape de output correctos
- ✅ Soporte para alignment

**Problemas críticos que bajan la nota:**
- 🔴 Dependencias CDN sin SRI (riesgo seguridad)
- 🔴 JavaScript inline en template (anti-pattern)
- 🔴 NO usa responsive images (mal performance)
- 🔴 Lazy loading mal implementado (daña LCP)
- 🔴 Carga incondicional de 200KB (desperdicio)
- ⚠️ NO hereda de BlockBase (inconsistencia)
- ⚠️ Usa extract() (riesgo seguridad menor)
- ⚠️ 0% cobertura de tests

### Veredicto

Este bloque es **FUNCIONAL pero PROBLEMÁTICO**. Cumple su propósito (galería hero con slider y lightbox), pero tiene serios problemas de:
- **Seguridad:** CDN sin SRI
- **Performance:** 200KB cargados siempre, lazy loading mal, sin responsive images
- **Arquitectura:** JS inline, no usa BlockBase, responsabilidades mezcladas

**RECOMENDACIÓN:** Refactorizar urgentemente siguiendo las 4 prioridades críticas antes de continuar desarrollo.

---

## 14. Checklist de Mejoras

### 🔴 URGENTE (Esta semana)

- [ ] Migrar Swiper y GLightbox a assets locales
- [ ] Añadir SRI hashes si se mantiene CDN temporalmente
- [ ] Implementar responsive images (srcset/sizes)
- [ ] Cambiar primera imagen a loading="eager"
- [ ] Implementar carga condicional de assets

### ⚠️ IMPORTANTE (Próxima semana)

- [ ] Mover JavaScript inline a archivo separado
- [ ] Usar wp_localize_script() para pasar data
- [ ] Heredar de BlockBase
- [ ] Eliminar extract(), usar variables directas
- [ ] Añadir preload de primera imagen

### ℹ️ MEJORAS (Próximo sprint)

- [ ] Extraer activity levels a config/helper
- [ ] Mover SVG montaña a IconHelper
- [ ] Añadir tests unitarios
- [ ] Validar estructura de gallery antes de usar
- [ ] Añadir constantes para magic numbers

### 📝 DOCUMENTACIÓN

- [ ] Crear README.md del bloque
- [ ] Documentar dependencias externas
- [ ] Añadir ejemplos de configuración ACF
- [ ] Documentar troubleshooting

---

## 15. Dependencias Críticas Identificadas

### Librerías Externas CDN 🔴

1. **Swiper 11.0.0**
   - URL CSS: https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css
   - URL JS: https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js
   - Tamaño: ~150KB total
   - Uso: Carrusel principal de imágenes
   - Riesgo: Alto (SPOF, seguridad, GDPR)

2. **GLightbox 3.2.0**
   - URL CSS: https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css
   - URL JS: https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js
   - Tamaño: ~50KB total
   - Uso: Lightbox/modal para ampliar imágenes
   - Riesgo: Alto (SPOF, seguridad)

### Helpers Internos

1. **EditorHelper**
   - Método: `is_editor_mode($post_id)`
   - Uso: Detectar modo preview
   - Criticidad: Media

2. **IconHelper**
   - Método: `get_icon_svg('arrow-right', 32, '#FFFFFF')`
   - Uso: Iconos SVG de navegación
   - Criticidad: Baja (solo UI)

### WordPress Functions

1. **ACF Functions (implícitas)**
   - `get_post_meta('gallery')` - Espera ACF Gallery field
   - Estructura esperada: Array de IDs o `[{ID, url, alt}]`
   - Criticidad: Alta

2. **WordPress Image Functions**
   - `wp_get_attachment_image_url()`
   - `get_post_thumbnail_id()`
   - `get_post_thumbnail_url()`
   - Criticidad: Alta

---

## 16. Notas Adicionales

### 🎯 Contexto del Bloque

Este es el **hero principal de galería de producto**, probablemente usado en la parte superior de páginas de paquetes turísticos. Su correcta funcionalidad es **crítica** para la experiencia de usuario.

### ⚠️ Riesgos de Modificación

**ALTA PRECAUCIÓN:**
- Cambiar estructura de gallery field romperá integración ACF
- Modificar clases CSS romperá estilos Swiper/GLightbox
- Cambiar data-attributes romperá inicialización JS
- Eliminar CDN sin reemplazar romperá slider completamente

### 💡 Oportunidades de Mejora

1. **Lazy loading progresivo:** Solo cargar Swiper cuando bloque está visible
2. **Thumbnail optimizado:** Usar tamaños pequeños para pagination
3. **Integración con WordPress Gallery Block:** Reutilizar galería nativa
4. **Touch gestures:** Añadir swipe en mobile sin Swiper (reduce dependencias)

### 🔗 Bloques Relacionados

- **PackageVideo:** Otro bloque de medios, probablemente comparte estilos hero
- **ProductMedia (si existe):** Podría compartir lógica de galería

### 📊 Métricas Sugeridas

Si se implementan las mejoras:
- **Reducción de peso:** ~200KB menos (CDN → local + carga condicional)
- **Mejora LCP:** ~500ms (eager loading + responsive images)
- **Mejora cache:** ~80% (JS external vs inline)

---

**FIN DE AUDITORÍA**

