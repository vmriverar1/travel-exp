# Auditoría: ItineraryDayByDay (Package)

**Fecha:** 2025-11-09
**Bloque:** 07/XX Package
**Tiempo:** 45 min
**⚠️ ESTADO:** CRÍTICO - EXCELENTE - Código de alta calidad con funcionalidad compleja

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/itinerary-day-by-day`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Template path:** `/templates/itinerary-day-by-day.php`
- **Campo ACF principal:** `itinerary` (repeater field)
- **Estructura de datos de itinerary:** Este es el corazón del paquete - NUNCA cambiar sin análisis exhaustivo
- **JavaScript files:** `itinerary-day-by-day.js` (accordion), `itinerary-swiper.js` (gallery)
- **CDN dependency:** Swiper 11.0.0 desde jsdelivr.net
- **Data attributes:** `data-default-state`, `data-initialized`, `data-day-index`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **extract() en load_template** ⚠️ (línea 283) - potencialmente peligroso
- **get_post_data() es largo** (81 líneas) - método más complejo del bloque
- **CDN externo (Swiper)** - Dependencia externa crítica (líneas 46-60)
- **Gallery parsing complejo** con manejo de arrays/IDs (líneas 206-227)
- **Items sorting** - usort() en items (líneas 250-252)
- **Public API exposed** - window.TravelBlocks.Itinerary con métodos públicos

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** Para detectar modo preview
- **IconHelper:** Para renderizar iconos SVG en template
- **Swiper 11.0.0:** CDN externo (jsdelivr.net) - CRÍTICO para galleries
- **ACF field 'itinerary':** Repeater field complejo con subfields (NO registrado en código)
- **Taxonomy 'type_service':** Para los tipos de servicio de cada item
- **JavaScript:** Dos archivos separados (accordion + swiper)

### 🎯 FUNCIONALIDAD CRÍTICA DEL NEGOCIO
Este bloque maneja el **itinerario día por día**, que es:
- La información más importante de un paquete turístico
- Afecta directamente la experiencia del usuario
- Contiene imágenes, descripciones, actividades, alojamiento
- Usa acordeón para UX (móvil-friendly)
- Integra Swiper para galerías de cada día

**NUNCA modificar sin testing exhaustivo en:**
- ✅ Diferentes cantidad de días (1, 3, 7, 15 días)
- ✅ Con y sin galerías
- ✅ Con y sin items/servicios
- ✅ Diferentes estados del acordeón (first_open, all_open, all_closed)
- ✅ Modo móvil y desktop
- ✅ Modo impresión (debe expandir todo)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/ItineraryDayByDay.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** `/templates/itinerary-day-by-day.php` (189 líneas)
**Assets:**
- CSS: `/assets/blocks/itinerary-day-by-day.css` (469 líneas)
- JS Accordion: `/assets/blocks/itinerary-day-by-day.js` (232 líneas)
- JS Swiper: `/assets/blocks/itinerary-swiper.js` (117 líneas)

**Tipo:** [ ] ACF  [X] Gutenberg Nativo (pero usa ACF fields del post)

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- EditorHelper (para detectar editor mode)
- IconHelper (para iconos SVG)
- **Swiper 11.0.0** (CDN externo - CRÍTICO)
- ACF field 'itinerary' (repeater - NO registrado en código)
- Taxonomy 'type_service' (para items)

**Líneas de Código:**
- **Clase PHP:** 287 líneas
- **Template:** 189 líneas
- **JavaScript Accordion:** 232 líneas
- **JavaScript Swiper:** 117 líneas
- **CSS:** 469 líneas
- **TOTAL:** 1,294 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Bloque CRÍTICO que muestra el itinerario día por día de un paquete turístico en formato acordeón, con galerías de imágenes (Swiper), actividades, servicios, alojamiento y altitud máxima.

**Funcionalidad Principal:**

1. **Acordeón de días:**
   - Header clickeable con día y título
   - Contenido expandible/colapsable
   - Estados configurables: first_open, all_open, all_closed
   - Smooth scroll al abrir
   - Keyboard accessible (Enter/Space)

2. **Información por día:**
   - Número de día (e.g., "Day 1")
   - Título del día (e.g., "Arrival in Cusco")
   - Descripción HTML (WYSIWYG content)
   - Galería de imágenes (Swiper slider)
   - Lista de items/servicios (con type_service taxonomy)
   - Alojamiento (hotel name)
   - Altitud máxima (metros)
   - Límite/restricción (opcional)

3. **Galería Swiper:**
   - Loop infinito
   - Touch/drag enabled
   - Pagination dots (clickeable)
   - Keyboard navigation
   - Mousewheel control
   - Lazy loading de imágenes
   - Float right con text wrap (desktop)
   - Full width (móvil)

4. **Items/Servicios:**
   - Order field para sorting
   - Type service taxonomy (e.g., Transfer, Lunch, Visit)
   - Text description
   - Auto-sorted por order field

5. **Data Sources:**
   - Preview mode: Datos hardcoded de ejemplo
   - Post mode: ACF repeater field 'itinerary'
   - Filtering: Skip inactive days (active = false)
   - Flexible order: Usa field 'order' o index+1

6. **Accesibilidad:**
   - ARIA attributes (aria-expanded, aria-controls)
   - Hidden attribute para contenido colapsado
   - Button type="button" (no form submit)
   - Keyboard navigation
   - Print-friendly (expand all)

**Inputs (ACF Repeater 'itinerary' - NO registrado en código):**
```php
$itinerary = [ // Repeater
    [
        'active' => true,         // Boolean
        'order' => 1,             // Number
        'title' => '...',         // Text
        'content' => '...',       // WYSIWYG
        'gallery' => [ ... ],     // Gallery (array de image IDs o arrays)
        'items' => [              // Repeater
            [
                'order' => 1,              // Number
                'type_service' => 123,     // Taxonomy term ID
                'text' => '...',           // Text
            ],
        ],
        'accommodation' => '...', // Text
        'altitude' => '3400',     // Text/Number
        'limit' => '',            // Text
    ],
];
```

**Outputs:**
- Section con acordeón de días
- Headers con día + título + altitud preview
- Panels con descripción, galería, items, meta info
- Swiper sliders inicializados automáticamente
- Placeholder si no hay días
- Error message si exception (WP_DEBUG)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'itinerary-day-by-day';
private string $title = 'Itinerary Day-by-Day';
private string $description = 'Accordion-style day-by-day itinerary with activities, meals, and accommodation';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (18 líneas)
2. enqueue_assets(): void - Encola assets (43 líneas)
3. render($attributes, $content, $block): string - Renderiza (47 líneas)
```

**Métodos Privados:**
```php
4. get_preview_data(): array - Preview data (48 líneas)
5. get_post_data(int $post_id): array - Obtiene datos del post (81 líneas) ⚠️ MÁS LARGO
6. load_template(string $template_name, array $data = []): void - Carga template (16 líneas)
```

**Total:** 6 métodos, 287 líneas

**Métodos más largos:**
1. ⚠️ `get_post_data()` - **81 líneas** (excede recomendación de 50 líneas)
2. ✅ `get_preview_data()` - **48 líneas** (límite aceptable)
3. ✅ `render()` - **47 líneas** (aceptable)
4. ✅ `enqueue_assets()` - **43 líneas** (aceptable)
5. ✅ `register()` - **18 líneas** (excelente)
6. ✅ `load_template()` - **16 líneas** (excelente)

**Observación:** ⚠️ `get_post_data()` con **81 líneas** excede la recomendación de 50 líneas. Es un método complejo que maneja:
- Parsing del repeater field 'itinerary'
- Filtering de días inactivos
- Processing de gallery (arrays vs IDs)
- Processing de items con taxonomy lookups
- Sorting de items por order field

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/itinerary-day-by-day`
- api_version: 2
- category: `template-blocks`
- icon: `list-view`
- keywords: ['itinerary', 'schedule', 'days', 'accordion', 'package']
- supports: anchor, html: false
- render_callback: `[$this, 'render']`
- show_in_rest: true

**Enqueue Assets:**
- **Swiper CSS:** CDN (jsdelivr.net) - Swiper 11.0.0 ⚠️
- **Swiper JS:** CDN (jsdelivr.net) - Swiper 11.0.0 ⚠️
- **Block CSS:** `/assets/blocks/itinerary-day-by-day.css` (depends on Swiper CSS)
- **Accordion JS:** `/assets/blocks/itinerary-day-by-day.js` (NO dependencies)
- **Swiper Init JS:** `/assets/blocks/itinerary-swiper.js` (depends on Swiper JS)
- Encolado en método separado `enqueue_assets()`
- Hook: `enqueue_block_assets` (frontend + editor)

**⚠️ DEPENDENCIA EXTERNA CDN:**
```php
wp_enqueue_style(
    'swiper-css',
    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
    [],
    '11.0.0'
);

wp_enqueue_script(
    'swiper-js',
    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
    [],
    '11.0.0',
    true
);
```

**Problema de seguridad/disponibilidad:**
- ❌ Si CDN cae, galleries NO funcionan
- ❌ Dependencia de terceros (jsdelivr.net)
- ❌ Posible vector de ataque (CDN compromise)
- ✅ Versión fijada (11.0.0) - Bueno para estabilidad
- ⚠️ **RECOMENDACIÓN:** Self-host Swiper para producción

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Campos:** ❌ **NO REGISTRA CAMPOS** (asume que ACF field 'itinerary' existe)

---

## 5. Campos Meta

**Definición:** ❌ **NO REGISTRA CAMPOS EN CÓDIGO**

**Campo ACF usado (asume que existe):**
- `itinerary` - ACF Repeater field complejo

**Estructura esperada del repeater:**
```php
// ACF Repeater 'itinerary'
$itinerary = [
    [
        'active' => true,           // Boolean - Si está activo
        'order' => 1,               // Number - Número del día (override index)
        'title' => 'Arrival',       // Text - Título del día
        'content' => '<p>...</p>',  // WYSIWYG - Descripción HTML
        'gallery' => [              // Gallery field
            123,                    // Image ID (if return_format = 'id')
            ['url' => '...', 'alt' => '...'], // Image array (if return_format = 'array')
        ],
        'items' => [                // Repeater - Servicios/actividades
            [
                'order' => 1,              // Number
                'type_service' => 123,     // Taxonomy - type_service term ID
                'text' => 'Description',   // Text
            ],
        ],
        'accommodation' => 'Hotel Name', // Text
        'altitude' => '3400',            // Text/Number - Metros
        'limit' => '',                   // Text - Límites/restricciones
    ],
];
```

**Problemas:**
- ❌ **NO registra campos** - Depende 100% de que ACF field 'itinerary' exista
- ❌ **NO documenta campos** - No hay PHPDoc de estructura esperada
- ❌ **NO valida campos** - get_field() sin validación
- ⚠️ **Manejo flexible de gallery** - Soporta IDs o arrays (bueno, pero no documenta)
- ⚠️ **Taxonomy lookup dentro del loop** - Potencial N+1 query problem (líneas 234-240)
- ✅ **Skip inactive days** - Buena feature (línea 198-200)
- ✅ **Flexible order** - Usa field 'order' o index+1 (línea 203)
- ✅ **Items sorting** - usort() por order field (líneas 250-252)

**Taxonomy Dependency:**
- Usa taxonomy 'type_service' para items
- get_term() lookup por cada item (potencial N+1)
- NO valida que term existe (!is_wp_error)

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Get post_id (línea 91)
2. Detecta preview mode con EditorHelper (línea 92)
3. Get data: preview vs post (líneas 94-98)
4. Early return si empty days (líneas 100-102)
5. Generate unique block_id (línea 104)
6. Build class_name con attributes (líneas 105-109)
7. Build data array (líneas 111-121)
8. Load template con ob_start/ob_get_clean (líneas 123-125)
9. Try-catch con error display si WP_DEBUG (líneas 127-134)

**Flujo de Datos:**
```
render()
  → is_preview?
    → YES: get_preview_data()
      → 3 días hardcoded de ejemplo (Cusco, Sacred Valley, Machu Picchu)
    → NO: get_post_data()
      → get_field('itinerary')
      → foreach day:
        → Skip si active = false
        → Get day_number (order ?? index+1)
        → Parse gallery (array/ID format)
        → Parse items (get_term for type_service)
        → Sort items by order
        → Build day array
  → load_template()
    → extract() variables ⚠️
    → include template
```

**Variables al Template:**
```php
$data = [
    'block_id' => 'itinerary-' . uniqid(),
    'class_name' => 'itinerary-day-by-day itinerary-day-by-day--default' . $attributes['className'],
    'days' => [
        [
            'day_number' => 1,
            'day_title' => 'Arrival in Cusco',
            'day_description' => '<p>...</p>',
            'day_gallery' => [
                ['url' => '...', 'alt' => '...'],
            ],
            'day_items' => [
                ['order' => 1, 'type_service' => 'Transfer', 'text' => '...'],
            ],
            'day_accommodation' => 'Hotel Name',
            'day_altitude' => '3400',
            'day_limit' => '',
        ],
    ],
    'accordion_style' => 'default',     // Hardcoded ⚠️
    'default_state' => 'first_open',    // Hardcoded ⚠️
    'show_day_numbers' => true,         // Hardcoded ⚠️
    'show_meals' => true,               // Hardcoded ⚠️ (no usado en template)
    'show_accommodation' => true,       // Hardcoded ⚠️
    'is_preview' => $is_preview,
];
```

**⚠️ Problema:** Muchas opciones hardcoded que deberían ser configurables:
- accordion_style
- default_state
- show_day_numbers
- show_meals (ni siquiera se usa)
- show_accommodation

**Manejo de Errores:**
- ✅ Try-catch en render()
- ✅ Error message si WP_DEBUG
- ✅ Empty return si no hay días
- ✅ Template check en load_template
- ⚠️ NO valida estructura de days array
- ⚠️ NO valida que gallery items sean válidos
- ⚠️ NO maneja errores de get_term() (taxonomy missing)

---

## 7. Funcionalidades Adicionales

### 7.1 Gallery Processing

**Método:** `get_post_data()` líneas 206-227

**Funcionalidad:**
- Soporta dos formatos de ACF gallery:
  1. **Array format:** `return_format = 'array'` → full image data
  2. **ID format:** `return_format = 'id'` → solo image ID

**Código:**
```php
$gallery = [];
if (!empty($day['gallery']) && is_array($day['gallery'])) {
    foreach ($day['gallery'] as $image) {
        // ACF array format
        if (is_array($image) && !empty($image['url'])) {
            $gallery[] = [
                'url' => $image['url'],
                'alt' => $image['alt'] ?? '',
            ];
        }
        // ACF ID format (fallback)
        elseif (is_numeric($image)) {
            $image_data = wp_get_attachment_image_src($image, 'large');
            if ($image_data) {
                $gallery[] = [
                    'url' => $image_data[0],
                    'alt' => get_post_meta($image, '_wp_attachment_image_alt', true),
                ];
            }
        }
    }
}
```

**Calidad:** 8/10 - Flexible y robusto

**Problemas:**
- ⚠️ NO sanitiza 'alt' text
- ⚠️ NO valida que 'url' sea URL válida
- ⚠️ wp_get_attachment_image_src puede retornar false (pero lo valida)
- ⚠️ Hardcoded size 'large' (debería ser configurable)
- ✅ Maneja ambos formatos de ACF (excelente)
- ✅ Fallback to empty string para alt

### 7.2 Items Processing

**Método:** `get_post_data()` líneas 229-253

**Funcionalidad:**
- Parse items/servicios de cada día
- Lookup de taxonomy term 'type_service' por ID
- Sorting por field 'order'

**Código:**
```php
$items = [];
if (!empty($day['items']) && is_array($day['items'])) {
    foreach ($day['items'] as $item) {
        $type_service_id = $item['type_service'] ?? null;
        $type_service_name = '';
        if ($type_service_id) {
            $term = get_term($type_service_id, 'type_service');
            if ($term && !is_wp_error($term)) {
                $type_service_name = $term->name;
            }
        }

        $items[] = [
            'order' => $item['order'] ?? 1,
            'type_service' => $type_service_name,
            'text' => $item['text'] ?? '',
        ];
    }

    // Sort items by order
    usort($items, function($a, $b) {
        return $a['order'] - $b['order'];
    });
}
```

**Calidad:** 7/10 - Funciona pero tiene problemas

**Problemas:**
- ⚠️ **N+1 Query Problem:** get_term() dentro del loop (puede hacer MUCHAS queries)
- ⚠️ NO sanitiza 'text' field
- ⚠️ NO valida que 'order' sea número
- ⚠️ Default order = 1 (todos sin order quedarán igual → sorting inconsistente)
- ⚠️ NO maneja caso de taxonomy 'type_service' no existente
- ✅ Valida !is_wp_error() (bueno)
- ✅ usort() por order (correcto)
- ✅ Fallback a empty string

**Recomendación:** Usar get_terms() con IDs array para evitar N+1:
```php
// Get all term IDs first
$term_ids = array_filter(array_column($day['items'], 'type_service'));

// Single query for all terms
$terms = get_terms([
    'taxonomy' => 'type_service',
    'include' => $term_ids,
    'hide_empty' => false,
]);

// Build lookup array
$terms_lookup = [];
foreach ($terms as $term) {
    $terms_lookup[$term->term_id] = $term->name;
}

// Then use lookup in loop
foreach ($day['items'] as $item) {
    $type_service_name = $terms_lookup[$item['type_service']] ?? '';
    // ...
}
```

### 7.3 JavaScript - Accordion

**Archivo:** `/assets/blocks/itinerary-day-by-day.js` (232 líneas)

**Funcionalidades:**
- ✅ IIFE pattern (encapsulado)
- ✅ Init guard (dataset.initialized = 'true')
- ✅ Public API expuesto (window.TravelBlocks.Itinerary)
- ✅ Accordion toggle
- ✅ Keyboard accessibility (Enter/Space)
- ✅ Smooth scroll al abrir
- ✅ ARIA attributes (aria-expanded)
- ✅ Hidden attribute management
- ✅ Print-friendly (beforeprint event → expand all)
- ✅ Gutenberg integration (wp.data.subscribe)
- ✅ Default states (first_open, all_open, all_closed)

**Métodos públicos:**
```javascript
window.TravelBlocks.Itinerary = {
    init: initItineraryBlocks,
    expandAll: expandAll,
    collapseAll: collapseAll,
    navigateToDay: navigateToDay,
};
```

**Calidad:** 9.5/10 - EXCELENTE código JavaScript

**Fortalezas:**
- Clean code, bien estructurado
- Separation of concerns (funciones pequeñas)
- Accesibilidad completa
- Error handling (null checks)
- Smooth UX (scroll into view)
- Print handling automático
- Public API para integración externa

**Problemas:**
- ⚠️ NO maneja caso de Gutenberg no disponible (typeof wp check ok)
- ✅ Todo muy bien hecho

### 7.4 JavaScript - Swiper

**Archivo:** `/assets/blocks/itinerary-swiper.js` (117 líneas)

**Funcionalidades:**
- ✅ Check de Swiper availability
- ✅ Polling fallback si Swiper no carga inmediatamente (max 5s)
- ✅ Init guard (gallery.swiper check)
- ✅ Auto-ID generation si falta
- ✅ Gutenberg integration
- ✅ Public API expuesto
- ✅ Console logging para debugging

**Configuración Swiper:**
```javascript
new Swiper(gallery, {
    loop: true,
    slidesPerView: 1,
    grabCursor: true,
    touchEventsTarget: 'container',
    pagination: {
        el: gallery.querySelector('.swiper-pagination'),
        clickable: true,
        dynamicBullets: false,
    },
    autoHeight: false,
    spaceBetween: 0,
    speed: 400,
    effect: 'slide',
    keyboard: {
        enabled: true,
        onlyInViewport: true,
    },
    mousewheel: {
        forceToAxis: true,
    },
});
```

**Calidad:** 8.5/10 - Muy bien hecho

**Fortalezas:**
- Polling para CDN lento (max 5s, 50 attempts)
- Console errors informativos
- Keyboard + mousewheel enabled
- Grabcursor UX

**Problemas:**
- ⚠️ **Dependencia de CDN** - Si CDN falla después de 5s, galleries NO funcionan
- ⚠️ Console.log en producción (debería ser WP_DEBUG conditional)
- ✅ setTimeout 100ms en Gutenberg re-init (puede causar flicker, pero necesario)

### 7.5 CSS

**Archivo:** `/assets/blocks/itinerary-day-by-day.css` (469 líneas)

**Características:**
- ✅ CSS Variables (custom properties)
- ✅ Theme.json integration (--wp--preset--color--secondary)
- ✅ Responsive design (@media max-width: 767px, 768px, 640px)
- ✅ Print styles (expand accordion, borders)
- ✅ Accessibility (focus-visible)
- ✅ Animations (slideDown keyframe)
- ✅ Float gallery layout (desktop: float right, mobile: full width)
- ✅ Swiper customization (no arrows, custom pagination)
- ✅ Semantic sections (bem-like naming)

**Organización:**
```css
/* ===== CONTAINER ===== */
/* ===== DAY ITEM (ACCORDION ITEM) ===== */
/* ===== DAY HEADER (ACCORDION TRIGGER) ===== */
/* ===== HEADER LEFT (Day Number + Title) ===== */
/* ===== HEADER RIGHT (Altitude + Toggle) ===== */
/* ===== DAY CONTENT (ACCORDION PANEL) ===== */
/* ===== CONTENT SECTIONS ===== */
/* ===== PLACEHOLDER ===== */
/* ===== ACCESSIBILITY ===== */
/* ===== ANIMATIONS ===== */
/* ===== PRINT STYLES ===== */
```

**Calidad:** 9/10 - Excelente CSS moderno

**Fortalezas:**
- Bien organizado por secciones
- Comentarios descriptivos
- Variables CSS
- Theme.json integration
- Responsive completo
- Print styles
- Accessibility (focus-visible)
- Animations suaves

**Problemas:**
- ⚠️ Hardcoded colors (#FFF6F5, #FFE8E5, #212121) - Deberían ser CSS variables
- ⚠️ .itinerary-day__number { display: none; } - ¿Por qué existe si está hidden?
- ⚠️ .itinerary-day__altitude-preview { display: none; } - Igual
- ⚠️ Float layout (gallery) puede ser problemático en algunos diseños
- ✅ Fallback colors en var() (excelente)

### 7.6 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.7 Dependencias Externas

**CDN:**
- Swiper 11.0.0 (jsdelivr.net) - **CRÍTICO** ⚠️

**Helpers Internos:**
- EditorHelper (detectar preview mode)
- IconHelper (renderizar iconos en template)

**WordPress:**
- ACF (field 'itinerary')
- Taxonomy 'type_service'

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ⚠️ **VIOLA MODERADAMENTE**
- Clase hace varias cosas:
  - Render
  - Data transformation (gallery, items)
  - Template loading
  - Assets enqueuing (Swiper CDN)
  - Preview data generation
- **get_post_data() es muy largo** (81 líneas) - Hace demasiado:
  - Parse repeater
  - Filter days
  - Process gallery
  - Process items + taxonomy lookup
  - Sorting
- Podría dividirse en:
  - ItineraryDayByDayBlock (render)
  - ItineraryParser (gallery/items processing)
- **Impacto:** MEDIO - Método get_post_data largo y complejo

**OCP:** ⚠️ **VIOLA**
- No hereda de BlockBase → Difícil extender
- Accordion style/state hardcoded → No se pueden agregar fácilmente
- **Impacto:** MEDIO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con mejores bloques
- **Impacto:** MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Acoplado directamente a:
  - ACF (get_field hardcoded)
  - Taxonomy 'type_service'
  - EditorHelper
  - IconHelper
  - **Swiper CDN externo** (CRÍTICO)
- No hay abstracción/interfaces
- **Impacto:** ALTO - Especialmente CDN dependency

### 8.2 Problemas Clean Code

**Complejidad:**
- ⚠️ **get_post_data() tiene 81 líneas** - EXCEDE recomendación de 50 líneas
- ✅ Otros métodos <50 líneas
- ⚠️ get_post_data tiene alta complejidad ciclomática (múltiples loops, conditionals)
- ⚠️ get_preview_data también largo (48 líneas) - En el límite

**Anidación:**
- ⚠️ get_post_data tiene 4-5 niveles de anidación en algunos puntos
- foreach → if → foreach → if → if (líneas 206-227, 231-247)
- **Impacto:** MEDIO - Dificulta lectura

**Duplicación:**
- ⚠️ Lógica de gallery parsing podría reutilizarse
- ⚠️ Patrón get field → empty check → default repetido
- ✅ No hay duplicación crítica

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ✅ Nombres consistentes

**Código Sin Uso:**
- ⚠️ `show_meals` = true en render pero **NO SE USA** en template
- ⚠️ `.itinerary-day__number` en CSS pero hidden (display: none)
- ⚠️ `.itinerary-day__altitude-preview` en CSS pero hidden

**DocBlocks:**
- ❌ **0/6 métodos documentados** (0%)
- ✅ Header de archivo tiene descripción básica
- ❌ NO documenta estructura esperada de ACF field
- ❌ NO documenta params/return types
- ❌ NO documenta dependencia de Swiper CDN
- **Impacto:** ALTO - Código complejo sin documentación

**Magic Values:**
- ⚠️ 'default', 'first_open', true hardcoded en render (deberían ser configurables)
- ⚠️ 'large' hardcoded en image size (línea 218)
- ⚠️ 'itinerary-' prefix hardcoded (línea 104)
- ⚠️ Swiper version '11.0.0' hardcoded (debería ser constante)
- ⚠️ CDN URL hardcoded (debería ser configurable)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ❌ **NO sanitiza campos ACF** antes de usar
- ❌ Gallery 'url' NO validada (puede ser XSS si malicious)
- ❌ Gallery 'alt' NO sanitizada
- ❌ Items 'text' NO sanitizado
- ❌ Accommodation NO sanitizado
- ⚠️ Template usa esc_html/esc_attr pero data ya debería estar limpia
- **Impacto:** MEDIO - Riesgo de XSS si data está comprometida

**Escapado:**
- ✅ Template usa esc_html(), esc_attr(), esc_url() correctamente
- ✅ wp_kses_post() en day_description (línea 133 template)
- ✅ IconHelper debe escapar SVG (asumimos que sí)

**CDN Dependency:**
- ❌ **RIESGO CRÍTICO:** Swiper cargado desde CDN externo
  - Si jsdelivr.net es comprometido → XSS en todo el sitio
  - Si CDN cae → Galleries NO funcionan
  - No hay integrity check (SRI)
- **Impacto:** ALTO - Vector de ataque potencial
- **Recomendación:** Self-host Swiper o usar SRI

**extract():**
- ⚠️ **Usa extract() en load_template** (línea 283)
- Usa EXTR_SKIP (más seguro que default)
- **Impacto:** BAJO - Pero es mala práctica
- **Recomendación:** Pasar variables directamente

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ⚠️ **N+1 Query Problem:** get_term() en loop (líneas 234-240)
- ✅ No hace queries directas

**XSS:**
- ✅ Template escapa correctamente
- ⚠️ Data NO sanitizada antes de template

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ✅ **Template es limpio** (solo presentación)
- ✅ Lógica en clase, presentación en template
- ⚠️ get_post_data hace demasiado (debería dividirse)

**Acoplamiento:**
- ⚠️ Acoplamiento a EditorHelper
- ⚠️ Acoplamiento a IconHelper
- ⚠️ Acoplamiento a ACF
- ⚠️ Acoplamiento a taxonomy 'type_service'
- ❌ **ALTO acoplamiento a Swiper CDN externo**
- **Impacto:** ALTO

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Duplica código (load_template)
- **Impacto:** MEDIO

**Performance:**
- ⚠️ **N+1 Query Problem** en items taxonomy lookup
- ⚠️ CDN puede ser lento (pero tiene polling fallback)
- ⚠️ No usa caché (pero data viene de post meta)
- **Impacto:** MEDIO para packages con muchos días/items

**Otros:**
- ❌ **NO usa block.json** (debería para Gutenberg moderno)
- ❌ **CDN sin SRI** (Subresource Integrity) - Riesgo de seguridad
- ⚠️ **Hardcoded options** no configurables (accordion_style, default_state, etc.)
- ⚠️ **Código sin uso** (show_meals, hidden CSS classes)

---

## 9. Recomendaciones de Refactorización

### Prioridad Alta (CRÍTICO)

**1. Self-host Swiper (CRÍTICO)**
- **Acción:**
  ```php
  // Download Swiper 11.0.0 to /assets/vendor/swiper/
  wp_enqueue_style(
      'swiper-css',
      TRAVEL_BLOCKS_URL . 'assets/vendor/swiper/swiper-bundle.min.css',
      [],
      '11.0.0'
  );

  wp_enqueue_script(
      'swiper-js',
      TRAVEL_BLOCKS_URL . 'assets/vendor/swiper/swiper-bundle.min.js',
      [],
      '11.0.0',
      true
  );
  ```
- **Razón:**
  - Elimina dependencia externa
  - Elimina riesgo de CDN compromise
  - Elimina riesgo de CDN downtime
  - Mejor performance (no CORS, cache local)
- **Riesgo:** BAJO - Solo cambiar URL
- **Precauciones:**
  - Download Swiper 11.0.0 exactly
  - Mantener versión fijada
  - Actualizar URLs
- **Esfuerzo:** 30 min

**2. Fix N+1 Query Problem en items**
- **Acción:**
  ```php
  // Get all term IDs first
  $all_term_ids = [];
  foreach ($itinerary as $day) {
      if (!empty($day['items'])) {
          foreach ($day['items'] as $item) {
              if (!empty($item['type_service'])) {
                  $all_term_ids[] = $item['type_service'];
              }
          }
      }
  }

  // Single query for all terms
  $terms_lookup = [];
  if (!empty($all_term_ids)) {
      $terms = get_terms([
          'taxonomy' => 'type_service',
          'include' => array_unique($all_term_ids),
          'hide_empty' => false,
      ]);

      foreach ($terms as $term) {
          $terms_lookup[$term->term_id] = $term->name;
      }
  }

  // Then use lookup in loop
  $type_service_name = $terms_lookup[$item['type_service']] ?? '';
  ```
- **Razón:** Evitar múltiples queries de taxonomy (puede ser 10-50+ queries)
- **Riesgo:** BAJO - Mejora clara
- **Precauciones:** Mantener validación de term exists
- **Esfuerzo:** 1 hora

**3. Refactorizar get_post_data() (dividir en métodos)**
- **Acción:**
  ```php
  private function get_post_data(int $post_id): array
  {
      $itinerary = get_field('itinerary', $post_id);

      if (!is_array($itinerary) || empty($itinerary)) {
          return [];
      }

      // Pre-load all terms once (fix N+1)
      $terms_lookup = $this->preload_type_service_terms($itinerary);

      $days = [];
      foreach ($itinerary as $index => $day) {
          if (!$this->is_day_active($day)) {
              continue;
          }

          $days[] = [
              'day_number' => $this->get_day_number($day, $index),
              'day_title' => $day['title'] ?? '',
              'day_description' => $day['content'] ?? '',
              'day_gallery' => $this->parse_gallery($day['gallery'] ?? []),
              'day_items' => $this->parse_items($day['items'] ?? [], $terms_lookup),
              'day_accommodation' => $day['accommodation'] ?? '',
              'day_altitude' => $day['altitude'] ?? '',
              'day_limit' => $day['limit'] ?? '',
          ];
      }

      return $days;
  }

  private function is_day_active(array $day): bool
  {
      return !isset($day['active']) || $day['active'];
  }

  private function get_day_number(array $day, int $index): int
  {
      return !empty($day['order']) ? $day['order'] : ($index + 1);
  }

  private function parse_gallery(array $gallery): array { /* ... */ }
  private function parse_items(array $items, array $terms_lookup): array { /* ... */ }
  private function preload_type_service_terms(array $itinerary): array { /* ... */ }
  ```
- **Razón:** get_post_data() tiene 81 líneas - Demasiado largo y complejo
- **Riesgo:** MEDIO - Requiere testing exhaustivo
- **Precauciones:**
  - Probar con diferentes estructuras de data
  - Validar gallery formats
  - Validar items sorting
- **Esfuerzo:** 3 horas

### Prioridad Media

**4. Heredar de BlockBase**
- **Acción:** `class ItineraryDayByDay extends BlockBase`
- **Razón:** Consistencia, evita duplicación
- **Riesgo:** MEDIO - Requiere refactorizar
- **Esfuerzo:** 2 horas

**5. Hacer opciones configurables**
- **Acción:**
  ```php
  // En render, get attributes:
  $accordion_style = $attributes['accordionStyle'] ?? 'default';
  $default_state = $attributes['defaultState'] ?? 'first_open';
  $show_day_numbers = $attributes['showDayNumbers'] ?? true;
  $show_accommodation = $attributes['showAccommodation'] ?? true;

  // Pasar a template
  $data = [
      // ...
      'accordion_style' => $accordion_style,
      'default_state' => $default_state,
      'show_day_numbers' => $show_day_numbers,
      'show_accommodation' => $show_accommodation,
  ];
  ```
- **Razón:** Actualmente hardcoded, no configurable
- **Riesgo:** BAJO - Solo agrega configurabilidad
- **Esfuerzo:** 1.5 horas

**6. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con:
  - Descripción de funcionalidad
  - @param types
  - @return types
  - Estructura esperada de ACF field
  - Dependencias (Swiper)
- **Razón:** Código complejo sin documentación (0/6 métodos)
- **Riesgo:** NINGUNO
- **Esfuerzo:** 1.5 horas

**7. Sanitizar campos ACF**
- **Acción:**
  ```php
  private function sanitize_day_data(array $day): array
  {
      return [
          'title' => sanitize_text_field($day['title'] ?? ''),
          'content' => wp_kses_post($day['content'] ?? ''),
          'accommodation' => sanitize_text_field($day['accommodation'] ?? ''),
          'altitude' => sanitize_text_field($day['altitude'] ?? ''),
          'limit' => sanitize_text_field($day['limit'] ?? ''),
          // ...
      ];
  }
  ```
- **Razón:** Seguridad, prevenir XSS
- **Riesgo:** BAJO
- **Esfuerzo:** 1 hora

**8. Eliminar extract() de load_template**
- **Acción:** Similar a InclusionsExclusions - pasar $data directamente
- **Razón:** extract() es mala práctica
- **Riesgo:** MEDIO - Requiere actualizar template
- **Esfuerzo:** 1 hora

**9. Convertir magic values a constantes**
- **Acción:**
  ```php
  private const DEFAULT_ACCORDION_STYLE = 'default';
  private const DEFAULT_STATE = 'first_open';
  private const DEFAULT_IMAGE_SIZE = 'large';
  private const SWIPER_VERSION = '11.0.0';
  private const SWIPER_CDN_URL = 'https://cdn.jsdelivr.net/npm/swiper@%s/swiper-bundle.min.%s';
  ```
- **Razón:** Mantenibilidad, claridad
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

### Prioridad Baja

**10. Eliminar código sin uso**
- **Acción:**
  - Eliminar `show_meals` (no se usa)
  - Eliminar `.itinerary-day__number { display: none; }` CSS
  - Eliminar `.itinerary-day__altitude-preview { display: none; }` CSS
  - O documentar por qué existen si son para feature futura
- **Razón:** Clean code, reducir confusión
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**11. Crear block.json**
- **Acción:** Migrar de register_block_type() a block.json
- **Razón:** Gutenberg moderno, mejor performance
- **Riesgo:** BAJO
- **Esfuerzo:** 1 hora

**12. Agregar SRI a CDN** (si no se self-host)
- **Acción:**
  ```php
  wp_enqueue_script(
      'swiper-js',
      'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
      [],
      '11.0.0',
      [
          'in_footer' => true,
          'strategy' => 'defer',
      ]
  );

  // Add integrity attribute
  add_filter('script_loader_tag', function($tag, $handle) {
      if ($handle === 'swiper-js') {
          $integrity = 'sha384-...'; // Generate SRI hash
          $tag = str_replace(' src=', ' integrity="' . $integrity . '" crossorigin="anonymous" src=', $tag);
      }
      return $tag;
  }, 10, 2);
  ```
- **Razón:** Seguridad (prevenir CDN compromise)
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**13. Mejorar CSS (variables para colors)**
- **Acción:**
  ```css
  :root {
      --itinerary-header-bg: #FFF6F5;
      --itinerary-header-bg-hover: #FFE8E5;
      --itinerary-text-color: #212121;
  }

  .itinerary-day__header {
      background: var(--itinerary-header-bg);
      color: var(--itinerary-text-color);
  }
  ```
- **Razón:** Theming, mantenibilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

### Fase 1 - CRÍTICO (Esta semana)
1. Self-host Swiper (30 min) - **CRÍTICO para seguridad**
2. Fix N+1 Query Problem (1 hora) - **CRÍTICO para performance**
3. Refactorizar get_post_data() (3 horas) - **CRÍTICO para mantenibilidad**

**Total Fase 1:** 4.5 horas

### Fase 2 - Alta Prioridad (Próximas 2 semanas)
4. Heredar de BlockBase (2 horas)
5. Hacer opciones configurables (1.5 horas)
6. Agregar DocBlocks (1.5 horas)
7. Sanitizar campos ACF (1 hora)
8. Eliminar extract() (1 hora)
9. Convertir magic values a constantes (20 min)

**Total Fase 2:** 7.5 horas

### Fase 3 - Baja Prioridad (Cuando haya tiempo)
10. Eliminar código sin uso (15 min)
11. Crear block.json (1 hora)
12. Agregar SRI a CDN (30 min) - Solo si NO se self-host
13. Mejorar CSS variables (30 min)

**Total Fase 3:** 2 horas

**Total Refactorización Completa:** ~14 horas

**Precauciones Generales:**
- ✅ **TESTING EXHAUSTIVO:** Este es el bloque más crítico del sistema
- ✅ **Probar con diferentes cantidad de días** (1, 3, 7, 15+)
- ✅ **Probar con y sin galerías**
- ✅ **Probar con y sin items**
- ✅ **Probar estados del acordeón** (first_open, all_open)
- ✅ **Probar responsive** (móvil + desktop)
- ✅ **Probar print mode**
- ✅ **Probar keyboard navigation**
- ⚠️ NO cambiar lógica de Swiper sin testing
- ⚠️ NO cambiar estructura de data sin migration plan
- ⚠️ SIEMPRE validar que galleries funcionan después de cambios

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra 3 días de ejemplo)
- [ ] Frontend funciona (muestra datos reales del ACF)
- [ ] ACF field 'itinerary' funciona

### Días (Days)
- [ ] Múltiples días se muestran correctamente (1, 3, 7, 15+)
- [ ] Day number funciona (field 'order' o index+1)
- [ ] Day title se muestra
- [ ] Day description (HTML) se muestra
- [ ] Días inactivos (active=false) se skipean

### Acordeón
- [ ] Headers clickeables
- [ ] Toggle open/close funciona
- [ ] default_state='first_open' funciona
- [ ] default_state='all_open' funciona
- [ ] Smooth scroll funciona al abrir
- [ ] Keyboard navigation (Enter/Space) funciona
- [ ] ARIA attributes correctos (aria-expanded, aria-controls)
- [ ] Hidden attribute funciona

### Galerías (Swiper)
- [ ] Swiper se carga correctamente
  - [ ] Self-hosted (no CDN) ✅ Recomendado
  - [ ] CDN con SRI ⚠️ Alternativa
- [ ] Galerías se inicializan
- [ ] Loop funciona
- [ ] Pagination dots funcionan (clickeable)
- [ ] Touch/drag funciona
- [ ] Keyboard navigation funciona (arrows)
- [ ] Mousewheel funciona
- [ ] Lazy loading funciona (eager first, lazy rest)
- [ ] Float right (desktop) funciona
- [ ] Full width (móvil) funciona
- [ ] Gallery formats funcionan:
  - [ ] ACF array format (['url' => ..., 'alt' => ...])
  - [ ] ACF ID format (numeric)

### Items/Servicios
- [ ] Items se muestran correctamente
- [ ] Type service taxonomy funciona
- [ ] Sorting por 'order' funciona
- [ ] NO hay N+1 query problem ✅ Debe estar fixed
- [ ] Empty items se maneja correctamente

### Meta Info
- [ ] Accommodation se muestra
- [ ] Altitude se muestra (metros)
- [ ] Altitude en header preview (si visible)
- [ ] Limit/restrictions se muestra (si tiene)

### JavaScript
- [ ] Accordion JS se inicializa
- [ ] Init guard funciona (no double-init)
- [ ] Swiper JS se inicializa
- [ ] Swiper polling funciona (si CDN lento)
- [ ] Gutenberg integration funciona (wp.data.subscribe)
- [ ] Print mode expande todo (beforeprint)
- [ ] Public API expuesta:
  - [ ] window.TravelBlocks.Itinerary.init()
  - [ ] window.TravelBlocks.Itinerary.expandAll()
  - [ ] window.TravelBlocks.Itinerary.collapseAll()
  - [ ] window.TravelBlocks.Itinerary.navigateToDay()
  - [ ] window.TravelBlocks.ItineraryGallery.init()

### CSS
- [ ] Estilos se aplican correctamente
- [ ] Responsive funciona (móvil + desktop)
- [ ] Animations funcionan (slideDown)
- [ ] Print styles funcionan
- [ ] Focus states funcionan (accessibility)
- [ ] Gallery float funciona (desktop)
- [ ] Gallery full-width funciona (móvil)
- [ ] Swiper pagination custom styles

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] NO usa extract() (si se eliminó)
- [ ] get_post_data() refactorizado (<50 líneas)
- [ ] Métodos separados:
  - [ ] parse_gallery()
  - [ ] parse_items()
  - [ ] preload_type_service_terms()
  - [ ] is_day_active()
  - [ ] get_day_number()
- [ ] N+1 fixed (single query para terms)
- [ ] Opciones configurables (accordion_style, default_state, etc.)
- [ ] Constantes definidas
- [ ] block.json (si se creó)

### Seguridad
- [ ] Swiper self-hosted (no CDN) ✅ Recomendado
- [ ] O CDN con SRI (Subresource Integrity)
- [ ] Campos ACF sanitizados
- [ ] Template escapa todo (esc_html, esc_attr, esc_url)
- [ ] wp_kses_post() en day_description
- [ ] Gallery URLs validadas

### Clean Code
- [ ] Métodos <50 líneas ✅
- [ ] get_post_data() refactorizado
- [ ] Anidación <3 niveles
- [ ] DocBlocks en todos los métodos
- [ ] No magic values (constantes)
- [ ] No código sin uso (show_meals eliminado)

### Performance
- [ ] N+1 Query Problem fixed ✅
- [ ] Swiper lazy loading funciona
- [ ] No queries innecesarias
- [ ] Assets minificados

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funcionalidad excelente y compleja
- ✅ JavaScript muy bien estructurado (9.5/10)
- ✅ Acordeón con accesibilidad completa
- ✅ Swiper bien integrado
- ✅ Template limpio y semántico
- ✅ CSS moderno y responsive
- ✅ Error handling robusto
- ❌ **NO hereda de BlockBase**
- ❌ **CRÍTICO: Swiper desde CDN externo** (riesgo de seguridad)
- ❌ **N+1 Query Problem** en taxonomy lookup
- ❌ **get_post_data() muy largo** (81 líneas)
- ❌ **NO tiene DocBlocks** (0/6 métodos)
- ⚠️ extract() en load_template
- ⚠️ Opciones hardcoded (no configurables)
- ⚠️ NO sanitiza campos ACF
- ⚠️ Código sin uso (show_meals, CSS hidden)

### Puntuación: 7.0/10

**Razones para la puntuación:**
- ➕ Funcionalidad excelente y compleja (+2)
- ➕ JavaScript excepcional (+1.5)
- ➕ Acordeón con accesibilidad completa (+1)
- ➕ Swiper bien integrado (+1)
- ➕ Template limpio (+0.5)
- ➕ CSS moderno (+0.5)
- ➕ Error handling (+0.5)
- ➖ CDN externo (riesgo seguridad) (-1)
- ➖ N+1 Query Problem (-0.5)
- ➖ get_post_data() muy largo (-0.5)
- ➖ NO hereda BlockBase (-0.5)
- ➖ Sin DocBlocks (-0.5)
- ➖ No sanitiza data (-0.5)

### Fortalezas
1. **JavaScript excepcional:** Accordion + Swiper con API pública, keyboard, print
2. **Accesibilidad completa:** ARIA, keyboard, hidden, smooth scroll
3. **Funcionalidad rica:** Acordeón, galerías, items, meta info
4. **Template limpio:** Bien estructurado, semántico, escapado correcto
5. **CSS moderno:** Variables, responsive, animations, print styles
6. **Error handling:** Try-catch, validaciones, empty states
7. **Flexible data:** Soporta múltiples formatos de gallery (array/ID)
8. **Smart filtering:** Skip inactive days, flexible day number
9. **Items sorting:** usort() por order field
10. **Preview mode:** Datos de ejemplo bien estructurados (Cusco, Machu Picchu)

### Debilidades
1. ❌ **CDN externo CRÍTICO** - Swiper desde jsdelivr.net (seguridad/disponibilidad)
2. ❌ **N+1 Query Problem** - get_term() en loop (performance)
3. ❌ **get_post_data() muy largo** - 81 líneas (mantenibilidad)
4. ❌ **NO documenta** - 0/6 métodos con DocBlocks
5. ❌ **NO hereda de BlockBase** - Inconsistente
6. ⚠️ **NO sanitiza** campos ACF antes de usar
7. ⚠️ **extract() usado** - Mala práctica
8. ⚠️ **Opciones hardcoded** - accordion_style, default_state no configurables
9. ⚠️ **Magic values** no son constantes
10. ⚠️ **Código sin uso** - show_meals, CSS hidden classes
11. ⚠️ **NO usa block.json** - Debería para Gutenberg moderno
12. ⚠️ **Anidación alta** - 4-5 niveles en get_post_data

### Recomendación Principal

**Este es un BLOQUE CRÍTICO BIEN HECHO pero con problemas de seguridad y performance que deben resolverse INMEDIATAMENTE.**

**Prioridad CRÍTICA (Esta semana - 4.5 horas):**
1. Self-host Swiper (30 min) - **ELIMINAR dependencia CDN**
2. Fix N+1 Query Problem (1 hora) - **MEJORAR performance**
3. Refactorizar get_post_data() (3 horas) - **MEJORAR mantenibilidad**

**Prioridad Alta (2 semanas - 7.5 horas):**
4. Heredar de BlockBase (consistencia)
5. Hacer opciones configurables (UX)
6. DocBlocks (documentación)
7. Sanitización (seguridad)
8. Eliminar extract() (mejor práctica)
9. Constantes (clean code)

**Prioridad Baja (Cuando haya tiempo - 2 horas):**
10. Eliminar código sin uso
11. block.json
12. SRI (si NO se self-host)
13. CSS variables

**Esfuerzo total:** ~14 horas de refactorización

**Veredicto:** Este es un BLOQUE BIEN HECHO con funcionalidad compleja y JavaScript excepcional. El acordeón está perfecto, Swiper bien integrado, y la accesibilidad es completa. Los principales problemas son: 1) **CDN externo (CRÍTICO)**, 2) **N+1 queries (performance)**, 3) **get_post_data() largo (mantenibilidad)**. **PRIORIDAD: Resolver problemas críticos esta semana, luego mejoras arquitectónicas.**

### Dependencias Identificadas

**Helpers Internos:**
- EditorHelper (detectar preview mode)
- IconHelper (renderizar iconos SVG)

**ACF:**
- Campo 'itinerary' (repeater field complejo) - **NO registrado en código**

**Taxonomy:**
- 'type_service' - Para tipos de servicio de items

**JavaScript:**
- itinerary-day-by-day.js (232 líneas) - Accordion functionality
- itinerary-swiper.js (117 líneas) - Swiper gallery initialization

**CSS:**
- itinerary-day-by-day.css (469 líneas)

**CDN Externo:**
- **Swiper 11.0.0** (jsdelivr.net) - **CRÍTICO** ⚠️

### Funcionalidades JavaScript Identificadas

**Accordion (itinerary-day-by-day.js):**
- ✅ Init con guard (dataset.initialized)
- ✅ Toggle open/close
- ✅ Default states (first_open, all_open, all_closed)
- ✅ Keyboard accessibility (Enter/Space)
- ✅ ARIA management (aria-expanded)
- ✅ Hidden attribute management
- ✅ Smooth scroll al abrir
- ✅ Print handling (beforeprint → expand all)
- ✅ Gutenberg integration (wp.data.subscribe)
- ✅ Public API:
  - init()
  - expandAll(blockId)
  - collapseAll(blockId)
  - navigateToDay(blockId, dayIndex)

**Swiper Gallery (itinerary-swiper.js):**
- ✅ Swiper availability check
- ✅ Polling fallback (max 5s) si CDN lento
- ✅ Init guard (gallery.swiper check)
- ✅ Auto-ID generation
- ✅ Loop infinito
- ✅ Touch/drag enabled
- ✅ Pagination dots (clickeable)
- ✅ Keyboard navigation
- ✅ Mousewheel control
- ✅ Gutenberg integration
- ✅ Console logging para debugging
- ✅ Public API:
  - init()

**Total JavaScript:** 349 líneas (232 + 117)

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** ALTA - Self-host Swiper, fix N+1, refactorizar get_post_data()
**Próxima revisión:** Después de refactorización Fase 1 (crítica)
