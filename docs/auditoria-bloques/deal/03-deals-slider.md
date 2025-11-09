# Auditoría: DealsSlider (Deal)

**Fecha:** 2025-11-09
**Bloque:** 3/3 Deal
**Tiempo:** 60 minutos

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `deals-slider`
- **Namespace:** `acf/deals-slider`
- **ACF Field Keys:**
  - `field_ds_deal_source` → `deal_source`
  - `field_ds_deal_manual` → `deal_manual`
  - `field_ds_show_countdown` → `show_countdown`
  - `field_ds_show_ribbon` → `show_ribbon`
  - `field_ds_bg_desktop` → `background_image_desktop`
  - `field_ds_bg_mobile` → `background_image_mobile`
  - `field_ds_bg_position` → `background_position`
  - `field_ds_countdown_text_1` → `countdown_text_1`
  - `field_ds_countdown_text_2` → `countdown_text_2`
  - `field_ds_view_button_text` → `view_button_text`
  - `field_ds_book_button_text` → `book_button_text`
  - `field_ds_autoplay` → `slider_autoplay`
  - `field_ds_autoplay_delay` → `slider_delay`
  - `field_ds_loop` → `slider_loop`
  - `field_ds_show_arrows` → `show_arrows`
  - `field_ds_show_dots` → `show_dots`
- **Clases CSS críticas:**
  - `.deals-slider`
  - `.deals-slider__countdown-bar`
  - `.deals-slider__swiper`
  - `.deals-slider__card`
  - `.deals-slider__arrow`
  - `.deals-slider__pagination`
  - `.swiper`, `.swiper-slide`, `.swiper-wrapper` (Swiper)
- **JavaScript Object:** `window.initDealsSlider` (global)
- **Swiper dependency:** Hardcoded CDN URL `https://cdn.jsdelivr.net/npm/swiper@11/`
- **Data attributes:**
  - `data-slider-config` (JSON con configuración Swiper)
  - `data-end-date` (fecha fin del deal)
  - `data-unit` (unidad countdown: days, hours, minutes, seconds)
  - `data-countdown-interval` (ID del interval para cleanup)

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Deal post type debe existir con custom fields: `active`, `end_date`, `discount_percentage`, `packages`
- Package post type debe existir con todos los meta fields usados
- Swiper library cargada desde CDN - cambiar URL rompe slider
- Template usa extract() heredado de BlockBase
- Iconos SVG inline hardcoded en template - refactor requiere IconHelper
- get_field() sin sanitización - agregar puede romper si datos son null
- El bloque depende de que el deal tenga packages con `promo_enabled = true`

---

## 1. Información General

**Ubicación:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/src/Blocks/Deal/DealsSlider.php`
**Namespace:** `Travel\Blocks\Blocks\Deal`
**Template:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/templates/deals-slider.php`
**Assets:**
- CSS: `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/deals-slider.css` (803 líneas)
- JS: `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/deals-slider.js` (276 líneas)

**Tipo:** [X] ACF Block (extiende BlockBase)

**Líneas de código:**
- Clase PHP: 587 líneas
- Template PHP: 333 líneas
- CSS: 803 líneas
- JavaScript: 276 líneas
- **TOTAL: 1999 líneas**

---

## 2. Propósito y Funcionalidad

**Descripción:**
Slider interactivo que muestra packages en oferta con contador regresivo, imagen de fondo personalizable y carousel de tarjetas usando Swiper.js. Permite seleccionar deals automáticamente (próximo a expirar) o manualmente.

**Inputs (ACF Fields):**
- `deal_source` - 'auto' o 'manual' (default: auto)
- `deal_manual` - Post object ID (condicional si manual)
- `show_countdown` - true/false (default: true)
- `show_ribbon` - true/false (default: true)
- `background_image_desktop` - Image array
- `background_image_mobile` - Image array (fallback: desktop)
- `background_position` - Select (center center, top center, etc.)
- `countdown_text_1` - Text (default: "Limited Time Offer")
- `countdown_text_2` - Text (default: "Book Now And Save!")
- `view_button_text` - Text (default: "View Trip")
- `book_button_text` - Text (default: "Book Now")
- `slider_autoplay` - true/false (default: true)
- `slider_delay` - Number 2000-15000ms (default: 6000)
- `slider_loop` - true/false (default: true)
- `show_arrows` - true/false (default: true)
- `show_dots` - true/false (default: true)

**Queries realizadas:**
1. `get_active_deal()` - WP_Query para deal activo más próximo a expirar
2. `get_deal_packages()` - get_post_meta para obtener packages del deal
3. `get_package_data()` - múltiples get_post_meta por package

**Outputs:**
- Countdown timer con días/horas/minutos/segundos
- Slider Swiper con tarjetas de packages
- Navegación con flechas y dots
- Background image responsive
- Ribbon "TOP SELLER" en cada tarjeta
- Metadata del package (tipo, días, rating, included services)

**Dependencias externas:**
- Swiper 11.0.0 (CDN: jsdelivr)
- ACF Pro
- Post types: deal, package
- Taxonomías: package_type, included_services

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `BlockBase` (abstract class)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
// Heredadas de BlockBase:
protected $name = 'deals-slider';
protected $title = 'Deals Slider';
protected $description = 'Slider con ofertas vigentes, contador regresivo y packages relacionados';
protected $icon = 'tickets-alt';
protected $keywords = ['deals', 'slider', 'countdown', 'offers', 'packages'];
protected $mode = 'preview';
```

**Métodos Públicos:**
```
__construct()                              (líneas 20-38)  - 19 líneas
enqueue_assets(): void                     (líneas 43-82)  - 40 líneas ⚠️
register(): void                           (líneas 87-360) - 274 líneas ❌ ENORME
render($block, $content, $is_preview, $post_id) (líneas 365-438) - 74 líneas ⚠️
```

**Métodos Privados:**
```
get_active_deal(): ?int                    (líneas 443-475) - 33 líneas
get_deal_data(int $deal_id): array        (líneas 480-488) - 9 líneas ✅
get_deal_packages(int $deal_id): array    (líneas 493-521) - 29 líneas
get_package_data(int $package_id): array  (líneas 526-585) - 60 líneas ⚠️
```

**Métodos Protected (heredados):**
```
load_template(string $name, array $data): void  (BlockBase línea 224-241) - 18 líneas
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type()` vía `BlockBase::register()` + override

**Configuración:**
- name: `deals-slider`
- title: "Deals Slider" (traducible)
- description: "Slider con ofertas vigentes, contador regresivo y packages relacionados"
- category: `travel`
- icon: `tickets-alt`
- keywords: `['deals', 'slider', 'countdown', 'offers', 'packages']`
- supports:
  - align: ['wide', 'full']
  - mode: true
  - multiple: true
  - anchor: true
- mode: `preview`
- render_callback: `[$this, 'render']`
- api_version: 2

**Hook adicional:**
- `enqueue_block_assets` - registrado en BlockBase línea 104
- `enqueue_block_editor_assets` - registrado en BlockBase línea 107

---

## 5. Campos ACF

**Definición:** Inline en `register()` - líneas 92-358 (267 líneas ❌)

**Estructura:**
- **Tab General:**
  - deal_source (select)
  - deal_manual (post_object, condicional)
  - show_countdown (true_false)
  - show_ribbon (true_false)

- **Tab Background:**
  - background_image_desktop (image)
  - background_image_mobile (image)
  - background_position (select)

- **Tab Texts:**
  - countdown_text_1 (text)
  - countdown_text_2 (text)
  - view_button_text (text)
  - book_button_text (text)

- **Tab Slider:**
  - slider_autoplay (true_false)
  - slider_delay (number, condicional)
  - slider_loop (true_false)
  - show_arrows (true_false)
  - show_dots (true_false)

**Total:** 15 campos + 4 tabs = 19 elementos ACF

**Conditional Logic:**
- `deal_manual` solo visible si `deal_source == 'manual'`
- `slider_delay` solo visible si `slider_autoplay == true`

---

## 6. Flujo de Renderizado

**Preparación:**
1. Obtiene `deal_source` via `get_field('deal_source')` (línea 368)
2. Si manual → `get_field('deal_manual')`, si auto → `get_active_deal()` (líneas 372-377)
3. Si no hay deal → muestra mensaje preview o return early (líneas 380-387)
4. Obtiene `get_deal_data($deal_id)` (línea 390)
5. Obtiene `get_deal_packages($deal_id)` (línea 393)
6. Si no hay packages → muestra mensaje preview o return early (líneas 396-403)
7. Construye array `$settings` con todos los ACF fields (líneas 406-421)
8. Construye `$block_id` y `$align` (líneas 424-425)
9. Pasa todo a template via `load_template()` (líneas 428-437)

**Variables al Template:**
```php
$block_id              // string: 'deals-slider-' + block['id'] o uniqid()
$align                 // string: align attribute ('full', 'wide', etc.)
$deal_data             // array: ['id', 'title', 'end_date', 'discount_percentage']
$packages              // array: lista de packages con datos completos
$settings              // array: todos los ACF fields + defaults
$is_preview            // bool: preview mode
```

**Template processing:**
- Template usa `extract()` heredado de BlockBase ⚠️
- Construye `$slider_config` JSON para JavaScript (líneas 35-41)
- Renderiza countdown bar si `show_countdown` (líneas 55-98)
- Loop de packages con Swiper slides (líneas 105-295)
- Renderiza navegación arrows/dots (líneas 302-329)
- Escapado con `esc_attr()`, `esc_url()`, `esc_html()`
- Iconos SVG inline hardcoded (líneas 61-70, 166-248)
- Cálculo de estrellas rating (líneas 194-223)

---

## 7. Funcionalidades Adicionales

**AJAX:** ❌ No

**JavaScript:** ✅ Sí (complejo)
- IIFE pattern (línea 10)
- `initDealsSlider()` - función principal (líneas 16-28)
- `initSwiper()` - inicializa Swiper con config (líneas 33-127)
- `initCountdown()` - countdown timer con setInterval (líneas 132-212)
- `updateCountdown()` - actualiza display cada segundo (líneas 160-202)
- `cleanupSlider()` - limpia intervals y Swiper instances (líneas 217-228)
- Re-inicialización para Gutenberg editor (líneas 243-260)
- Cleanup en beforeunload (líneas 265-268)
- Expone `window.initDealsSlider` globalmente (línea 273)
- **Características:**
  - Configuración dinámica desde data attribute
  - Keyboard navigation (a11y)
  - Autoplay con pause on hover
  - Responsive
  - Error handling con try/catch
  - Soporte para editor preview

**REST API:** ❌ No

**Hooks Propios:**
- Ninguno

**Dependencias externas:**
- **Swiper 11.0.0** (CDN jsdelivr) ⚠️
- ACF Pro
- BlockBase class
- WordPress functions: `get_field()`, `get_post_meta()`, `wp_get_post_terms()`, `WP_Query`

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP (Single Responsibility Principle):** ❌ **VIOLACIÓN ALTA**
- La clase hace demasiadas cosas:
  - Registro del bloque ✓
  - Enqueue de assets ✓
  - Rendering ✓
  - ACF fields registration (267 líneas) ❌
  - Deal query (WP_Query) ❌
  - Package data retrieval ❌
  - Data transformation ❌
- **`register()` tiene 274 líneas** - método gigante con ACF fields inline
- **`get_package_data()` tiene 60 líneas** - demasiado para un método
- **Debería separarse en:**
  - `DealsSliderBlock` - registro y coordinación
  - `DealRepository` - queries y data retrieval
  - `PackageDataProvider` - transformación de datos
  - `DealsSliderFields` - ACF fields configuration

**OCP (Open/Closed Principle):** ⚠️ **VIOLACIÓN MEDIA**
- `render()` usa if/else para preview vs production - no extensible
- `get_active_deal()` tiene lógica hardcoded de query - no se puede extender
- Iconos en template hardcoded - no se pueden personalizar sin editar template
- `get_package_data()` tiene mapeo de iconos hardcoded (líneas 241-248 del template)

**LSP (Liskov Substitution Principle):** ✅ **CUMPLE**
- Extiende BlockBase correctamente
- Implementa métodos abstractos según contrato

**ISP (Interface Segregation Principle):** ⚠️ **N/A pero mejorable**
- No implementa interfaces
- **Recomendación:** Crear `BlockInterface`, `RenderableInterface`

**DIP (Dependency Inversion Principle):** ❌ **VIOLACIÓN ALTA**
- Depende directamente de implementaciones concretas:
  - `get_field()` - ACF function directa
  - `get_post_meta()` - WordPress function directa
  - `wp_get_post_terms()` - WordPress function directa
  - `WP_Query` - instantiation directa (línea 468)
  - `wp_enqueue_style()`, `wp_enqueue_script()` - funciones directas
- **NO usa inyección de dependencias**
- **NO hay abstracciones/interfaces**
- Acoplamiento extremo a WordPress y ACF

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ `register()` tiene **274 líneas** - MÉTODO GIGANTE (debe ser <50 líneas)
- ⚠️ `render()` tiene 74 líneas (límite razonable pero mejorable)
- ⚠️ `get_package_data()` tiene 60 líneas (debería ser <30)
- ⚠️ `enqueue_assets()` tiene 40 líneas (razonable pero optimizable)
- ✅ Otros métodos son cortos (<35 líneas)

**Anidación:**
- ✅ Máximo 2-3 niveles en general
- ⚠️ Template tiene 4 niveles en algunos puntos (líneas 229-256)
- ✅ JavaScript bien estructurado, anidación controlada

**Duplicación:**
- ⚠️ `get_field()` sin sanitizar se repite 15 veces en render()
- ⚠️ `get_post_meta()` sin sanitizar se repite 10+ veces en get_package_data()
- ⚠️ Patrón `?: 'default'` se repite en múltiples lugares
- ❌ Iconos SVG duplicados entre template y posible IconHelper
- ✅ No hay duplicación entre métodos PHP

**Nombres:**
- ✅ Nombres descriptivos y claros en general
- ✅ Convención consistente (snake_case para ACF keys, camelCase para métodos)
- ⚠️ `$data` es genérico (podría ser `$template_data` o `$slider_data`)
- ⚠️ `$packages` podría ser `$deal_packages` para claridad
- ✅ Variables en template bien nombradas

**Código Sin Uso:**
- ✅ No hay código muerto
- ✅ Todos los métodos se utilizan
- ⚠️ `$discount_percentage` se obtiene pero nunca se usa (línea 486)

**Otros problemas:**
- ⚠️ Uso de `extract()` en `BlockBase::load_template()` - **MAL PRÁCTICA**
- ⚠️ `uniqid()` sin prefix puede generar colisiones (línea 424, template línea 22)
- ⚠️ Template de 333 líneas es muy largo - debería dividirse
- ❌ ACF fields inline hace que `register()` sea ilegible
- ⚠️ `number_format()` sin separador de miles puede ser confuso (template línea 269)

### 8.3 Problemas de Seguridad

**Sanitización:** ❌ **CRÍTICO**
- **Líneas 368-420:** TODOS los `get_field()` sin sanitizar
- **Líneas 485-486:** `get_post_meta()` sin sanitizar
- **Líneas 529-567:** Múltiples `get_post_meta()` sin sanitizar
- Datos van directamente al template sin sanitización previa
- `$deal_id` se usa sin validar que sea int válido
- `$package_id` se valida con `intval()` pero no se verifica que exista

**Escapado:** ✅ **BUENO**
- Template usa correctamente:
  - `esc_attr()` para atributos (múltiples líneas)
  - `esc_url()` para URLs (líneas 52, 114, 142, 276, 282)
  - `esc_html()` para texto (líneas 72, 73, 126, 143, 148, etc.)
  - `wp_json_encode()` para JSON (línea 48)
- JavaScript no manipula HTML directamente - solo textContent
- ✅ Muy buen escapado en template

**Nonces:** ✅ **N/A**
- No hay formularios ni AJAX - solo lectura
- No se necesitan nonces

**Capabilities:** ⚠️ **PARCIAL**
- `render()` NO verifica capabilities
- Cualquiera puede ver el bloque (probablemente OK para contenido público)
- NO hay verificación de permisos para queries

**SQL:** ⚠️ **RIESGO BAJO**
- `WP_Query` en `get_active_deal()` (línea 468) - **SEGURO** (usa WordPress API)
- Usa `get_post_meta()`, `wp_get_post_terms()` que están protegidos por WordPress
- NO hay queries SQL directas - ✅ bueno

**Validación de Input:**
- ❌ NO valida `$deal_id` antes de usar en queries
- ❌ NO valida que `$package_id` exista antes de `get_post_meta()`
- ❌ NO valida tipo de `deal_source` (podría ser value injection)
- ❌ NO valida rango de `slider_delay` (puede ser negativo o muy alto)
- ⚠️ `get_post_status($package_id)` valida publicación (línea 507) - ✅ bueno
- ⚠️ Verifica `promo_enabled` (líneas 511-513) - ✅ bueno

**XSS Potencial:**
- ✅ Template bien escapado - riesgo bajo
- ⚠️ `background_position` no validado contra lista permitida - podría inyectar CSS
- ⚠️ `promo_tag_color` no validado - podría inyectar CSS malicioso (template línea 124)
- **Riesgo:** BAJO-MEDIO si admin es comprometido

**Otros riesgos:**
- ⚠️ Swiper cargado desde CDN público - riesgo de supply chain attack
- ⚠️ CDN puede estar caído - single point of failure
- ⚠️ No hay Subresource Integrity (SRI) en CDN links

### 8.4 Problemas de Arquitectura

**Namespace:** ✅ **CORRECTO**
- `Travel\Blocks\Blocks\Deal` - apropiado y consistente

**Separación MVC:** ⚠️ **POBRE**
- **Model:** ❌ No hay clase separada - usa `get_active_deal()`, `get_deal_data()`, `get_package_data()` directamente
- **View:** ✅ Template separado en archivo independiente
- **Controller:** ⚠️ Clase hace de controller + model + repository
- **Recomendación:** Separar en DealRepository, PackageRepository

**Acoplamiento:** **MUY ALTO**
- Acoplado a ACF (15 `get_field()` calls)
- Acoplado a WordPress post meta structure
- Acoplado a post types específicos (deal, package)
- Acoplado a taxonomías (package_type, included_services)
- Acoplado a Swiper CDN URL
- Acoplado a BlockBase (herencia rígida)
- **NO usa inyección de dependencias**
- **Difícil de testear**

**Cohesión:** ⚠️ **MEDIA**
- Métodos relacionados entre sí ✓
- Pero `register()` hace demasiadas cosas distintas ✗
- ACF fields deberían estar separados ✗

**Otros problemas arquitectónicos:**

**1. ACF Fields Inline (267 líneas):**
- `register()` contiene giant array de ACF fields
- Hace el método ilegible e inmantenible
- **Debería estar en:**
  - Archivo JSON en `/acf-json/`
  - O clase separada `DealsSliderFields`
  - O método separado `get_acf_fields_config()`

**2. Assets cargados globalmente:**
- Hook `enqueue_block_assets` carga en TODAS las páginas (BlockBase línea 104)
- CSS: 803 líneas cargadas siempre
- JS: 276 líneas cargadas siempre
- Swiper: ~70KB cargado siempre desde CDN
- **Performance impact significativo**
- Debería usar `has_block()` para cargar condicionalmente

**3. Swiper desde CDN:**
- URL hardcoded: `https://cdn.jsdelivr.net/npm/swiper@11/`
- No hay fallback si CDN falla
- No hay SRI (Subresource Integrity)
- Versión hardcoded - difícil de actualizar
- **Recomendación:** Vendor local o usar npm package

**4. Template demasiado largo:**
- 333 líneas es excesivo para un template
- Debería dividirse en partials:
  - `deals-slider/countdown-bar.php`
  - `deals-slider/package-card.php`
  - `deals-slider/navigation.php`
- Mejora mantenibilidad y reutilización

**5. Iconos hardcoded:**
- SVG inline en template (líneas 61-70, 166-248)
- Mapeo de iconos hardcoded (líneas 241-248)
- Duplicación con posible IconHelper
- **Debería usar:** `IconHelper::get_icon_svg()`

**6. No hay interfaz para Repository pattern:**
- `get_active_deal()`, `get_deal_packages()` deberían estar en `DealRepository`
- `get_package_data()` debería estar en `PackageRepository`
- Permitiría testing con mocks

**7. Magic numbers:**
- `absint($package_id)` (línea 504) - sin validación de rango
- `floatval($rating)` (línea 577) - sin validación 0-5
- Delay range 2000-15000 definido en ACF pero no validado en PHP

---

## 9. Recomendaciones de Refactorización

### Prioridad Alta

**1. Extraer ACF fields de register() a método separado**
- **Acción:** Mover líneas 93-358 a método `get_acf_fields_config(): array`
- **Razón:** `register()` con 274 líneas es ilegible e inmantenible
- **Riesgo:** **BAJO** - Refactor puro, sin cambio funcional
- **Precauciones:**
  - Mantener exactamente la misma estructura del array
  - No cambiar field keys
  - Testing exhaustivo en editor
- **Esfuerzo:** 30 minutos
- **Código:**
```php
// En register():
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group($this->get_acf_fields_config());
}

// Nuevo método:
private function get_acf_fields_config(): array
{
    return [
        'key' => 'group_block_deals_slider',
        // ... resto del array
    ];
}
```

**2. Sanitizar todos los get_field() y get_post_meta()**
- **Acción:** Agregar sanitización a todas las llamadas
- **Razón:** Prevenir XSS y garantizar integridad de datos
- **Riesgo:** **ALTO** - Vulnerabilidad de seguridad
- **Precauciones:**
  - `sanitize_text_field()` para textos
  - `esc_url_raw()` para URLs de imágenes
  - `absint()` para IDs y delays
  - Validar `background_position` contra whitelist
  - Validar `promo_tag_color` con regex hex color
  - Mantener fallbacks
- **Esfuerzo:** 1 hora
- **Código:**
```php
// Ejemplo render():
$deal_source = sanitize_key(get_field('deal_source') ?: 'auto');
$deal_id = absint(get_field('deal_manual'));

// Ejemplo settings:
'countdown_text_1' => sanitize_text_field(get_field('countdown_text_1') ?: 'Limited Time Offer'),
'slider_delay' => max(2000, min(15000, absint(get_field('slider_delay')) ?: 6000)),

// En get_package_data():
'promo_tag_color' => $this->sanitize_hex_color(get_post_meta($package_id, 'promo_tag_color', true) ?: '#e78c85'),
```

**3. Cargar assets condicionalmente**
- **Acción:** Verificar `has_block()` antes de enqueue
- **Razón:** Performance - no cargar 1149 líneas de CSS/JS innecesariamente
- **Riesgo:** **MEDIO** - Puede afectar carga en editors
- **Precauciones:**
  - Verificar que funcione en Gutenberg editor
  - Verificar bloques reutilizables
  - Cache busting apropiado
- **Esfuerzo:** 1 hora
- **Código:**
```php
public function enqueue_assets(): void
{
    // Don't load in admin
    if (is_admin()) {
        return;
    }

    // Check if block is present
    if (!has_block('acf/deals-slider')) {
        return;
    }

    // ... enqueue logic
}
```

**4. Validar $deal_id y $package_id antes de queries**
- **Acción:** Agregar validaciones defensivas
- **Razón:** Prevenir errores con IDs inválidos
- **Riesgo:** **MEDIO** - Puede ocultar bugs
- **Precauciones:**
  - Usar `get_post($id)` para verificar existencia
  - Verificar post_status
  - Mantener mensajes claros en preview
- **Esfuerzo:** 30 minutos
- **Código:**
```php
// En get_deal_data():
if (!$deal_id || !get_post($deal_id)) {
    return [];
}

// En get_deal_packages():
foreach ($package_ids as $package_id) {
    $package_id = absint($package_id);

    if (!$package_id || get_post_status($package_id) !== 'publish') {
        continue;
    }
    // ...
}
```

**5. Swiper local en lugar de CDN**
- **Acción:** Instalar Swiper via npm, compilar local
- **Razón:** Eliminar dependencia de CDN externo, añadir SRI, mejor performance
- **Riesgo:** **MEDIO** - Cambio de infraestructura
- **Precauciones:**
  - Mantener misma versión (11.0.0)
  - Añadir a build process
  - Verificar que no rompa en producción
  - Fallback si build falla
- **Esfuerzo:** 2 horas
- **Código:**
```bash
npm install swiper@11.0.0 --save
# Build CSS/JS y mover a assets/vendor/
```
```php
wp_enqueue_style(
    'swiper',
    TRAVEL_BLOCKS_URL . 'assets/vendor/swiper/swiper-bundle.min.css',
    [],
    '11.0.0'
);
```

### Prioridad Media

**6. Separar responsabilidades - Crear DealRepository**
- **Acción:** Extraer `get_active_deal()`, `get_deal_data()`, `get_deal_packages()` a clase `DealRepository`
- **Razón:** Mejor testabilidad, SRP, reutilización
- **Riesgo:** **MEDIO** - Refactor arquitectónico
- **Precauciones:**
  - Crear interfaz `DealRepositoryInterface`
  - Inyectar via constructor o DI container
  - Testing exhaustivo
  - Mantener backwards compatibility
- **Esfuerzo:** 3-4 horas
- **Estructura:**
```php
interface DealRepositoryInterface
{
    public function getActiveDeal(): ?int;
    public function getDealData(int $dealId): array;
    public function getDealPackages(int $dealId): array;
}

class DealRepository implements DealRepositoryInterface
{
    // Implementación
}
```

**7. Separar responsabilidades - Crear PackageDataProvider**
- **Acción:** Extraer `get_package_data()` a clase `PackageDataProvider`
- **Razón:** Reutilización en otros bloques, mejor testing
- **Riesgo:** **MEDIO** - Refactor arquitectónico
- **Precauciones:**
  - Crear interfaz `PackageDataProviderInterface`
  - Compartir con otros bloques Package
  - Inyectar via DI
- **Esfuerzo:** 2-3 horas

**8. Dividir template en partials**
- **Acción:** Separar template en:
  - `deals-slider.php` (main)
  - `parts/countdown-bar.php`
  - `parts/package-card.php`
  - `parts/navigation.php`
- **Razón:** Mejor mantenibilidad, reutilización
- **Riesgo:** **BAJO** - Mejora de estructura
- **Precauciones:**
  - Pasar variables necesarias a cada partial
  - Verificar que no rompa estilos
- **Esfuerzo:** 2 horas

**9. Reemplazar iconos inline con IconHelper**
- **Acción:** Usar `IconHelper::get_icon_svg()` para todos los iconos
- **Razón:** Evitar duplicación, facilitar cambios
- **Riesgo:** **MEDIO** - Depende de que IconHelper tenga todos los iconos
- **Precauciones:**
  - Verificar que IconHelper tenga: clock, star, package types, included services
  - Añadir iconos faltantes a IconHelper primero
  - Mantener fallbacks
- **Esfuerzo:** 1-2 horas

**10. Eliminar extract() en BlockBase**
- **Acción:** Modificar `BlockBase::load_template()` para no usar extract()
- **Razón:** Mala práctica, dificulta debugging
- **Riesgo:** **ALTO** - Afecta TODOS los bloques que heredan de BlockBase
- **Precauciones:**
  - Actualizar TODOS los templates que usan BlockBase
  - Hacer en etapas
  - Testing exhaustivo de todos los bloques
  - O usar helper method `get($data, 'key', 'default')`
- **Esfuerzo:** 4-6 horas (afecta múltiples bloques)

**11. Agregar método helper sanitize_hex_color()**
- **Acción:** Crear método para validar colores hex
- **Razón:** Prevenir inyección CSS maliciosa en `promo_tag_color`
- **Riesgo:** **BAJO** - Mejora defensiva
- **Precauciones:** Validar formato #RRGGBB o #RGB
- **Esfuerzo:** 20 minutos
- **Código:**
```php
private function sanitize_hex_color(string $color): string
{
    $color = ltrim($color, '#');

    if (preg_match('/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
        return '#' . $color;
    }

    return '#e78c85'; // default
}
```

**12. Mejorar uniqid() con prefix**
- **Acción:** Cambiar `uniqid()` a `uniqid('deals-slider-', true)`
- **Razón:** Reducir colisiones, más legible en HTML
- **Riesgo:** **BAJO** - Cambio cosmético
- **Precauciones:** Ninguna
- **Esfuerzo:** 5 minutos

### Prioridad Baja

**13. Exportar ACF fields a JSON**
- **Acción:** Usar ACF's Local JSON feature, mover fields a `/acf-json/`
- **Razón:** Mejor versionado, performance, portabilidad
- **Riesgo:** **BAJO** - ACF feature estándar
- **Precauciones:**
  - Configurar `acf/settings/save_json`
  - Verificar que se cargue correctamente
  - Commit JSON files al repo
- **Esfuerzo:** 1 hora

**14. Agregar validación de background_position**
- **Acción:** Validar contra whitelist de valores permitidos
- **Razón:** Prevenir inyección CSS
- **Riesgo:** **BAJO** - Mejora defensiva
- **Precauciones:** Usar `in_array()` con whitelist
- **Esfuerzo:** 15 minutos
- **Código:**
```php
$allowed_positions = ['center center', 'top center', 'bottom center', 'center left', 'center right'];
$bg_position = in_array($settings['background_position'], $allowed_positions, true)
    ? $settings['background_position']
    : 'center center';
```

**15. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con @param, @return, @throws
- **Razón:** Mejor documentación, IDE autocomplete
- **Riesgo:** **NINGUNO** - Solo documentación
- **Precauciones:** Ninguna
- **Esfuerzo:** 45 minutos

**16. Agregar Unit Tests**
- **Acción:** Crear tests para métodos privados y render logic
- **Razón:** Garantizar funcionalidad, prevenir regresiones
- **Riesgo:** **NINGUNO** - Solo testing
- **Precauciones:** Mock WordPress functions, ACF
- **Esfuerzo:** 4-6 horas

**17. Optimizar CSS (eliminar duplicación)**
- **Acción:** Revisar custom properties, consolidar duplicados
- **Razón:** Reducir tamaño (803 líneas es mucho)
- **Riesgo:** **BAJO** - Puede romper estilos
- **Precauciones:** Testing visual exhaustivo
- **Esfuerzo:** 2 horas

**18. Extraer magic numbers a constantes**
- **Acción:** Crear constantes para valores hardcoded
- **Razón:** Mejor mantenibilidad
- **Riesgo:** **BAJO** - Refactor cosmético
- **Precauciones:** Ninguna
- **Esfuerzo:** 30 minutos
- **Ejemplo:**
```php
private const SLIDER_DELAY_MIN = 2000;
private const SLIDER_DELAY_MAX = 15000;
private const SLIDER_DELAY_DEFAULT = 6000;
private const MAX_INCLUDED_SERVICES_DISPLAY = 4;
```

**19. Agregar logging para debug**
- **Acción:** Log cuando no se encuentra deal o packages
- **Razón:** Facilitar debugging en producción
- **Riesgo:** **NINGUNO** - Solo logging
- **Precauciones:** Solo log si `WP_DEBUG`
- **Esfuerzo:** 30 minutos

**20. Implementar cache para deal queries**
- **Acción:** Usar transients para cachear `get_active_deal()`
- **Razón:** Performance - evitar query en cada pageload
- **Riesgo:** **MEDIO** - Puede mostrar datos desactualizados
- **Precauciones:**
  - TTL corto (5-15 minutos)
  - Invalidar cache al publicar/actualizar deals
  - Hook en `save_post_deal`
- **Esfuerzo:** 1-2 horas

---

## 10. Plan de Acción

**Fase 1: Seguridad y Mantenibilidad Crítica** (Inmediato - 1 día)
1. ✅ **Extraer ACF fields a método separado** (30 min) - Legibilidad crítica
2. ✅ **Sanitizar get_field() y get_post_meta()** (1h) - Seguridad
3. ✅ **Validar $deal_id y $package_id** (30 min) - Prevenir errores
4. ✅ **Agregar sanitize_hex_color()** (20 min) - Seguridad CSS

**Fase 2: Performance y Assets** (Corto plazo - 2-3 días)
5. ✅ **Cargar assets condicionalmente** (1h) - Performance
6. ✅ **Swiper local en lugar de CDN** (2h) - Confiabilidad
7. ✅ **Mejorar uniqid() con prefix** (5 min) - Mejor práctica

**Fase 3: Refactor Arquitectónico** (Mediano plazo - 1 semana)
8. ⚠️ **Crear DealRepository** (3-4h) - SRP, testabilidad
9. ⚠️ **Crear PackageDataProvider** (2-3h) - Reutilización
10. ⚠️ **Dividir template en partials** (2h) - Mantenibilidad
11. ⚠️ **Reemplazar iconos inline con IconHelper** (1-2h) - DRY

**Fase 4: Infraestructura y Calidad** (Largo plazo - 2 semanas)
12. ⚠️ **Eliminar extract() en BlockBase** (4-6h) - Afecta múltiples bloques
13. ⚠️ **Exportar ACF fields a JSON** (1h) - Mejor práctica
14. ⚠️ **Agregar Unit Tests** (4-6h) - Calidad
15. ⚠️ **Optimizar CSS** (2h) - Performance
16. ⚠️ **Implementar cache** (1-2h) - Performance

**Precauciones Generales:**
- ⛔ **NO cambiar** ACF field keys - rompe contenido existente
- ⛔ **NO cambiar** clases CSS críticas - rompe estilos y JavaScript
- ⛔ **NO cambiar** nombre del bloque - rompe contenido
- ⛔ **NO cambiar** estructura de `data-slider-config` - rompe JavaScript
- ⛔ **NO cambiar** Swiper class names - rompe biblioteca
- ⚠️ **CUIDADO** al modificar BlockBase - afecta TODOS los bloques
- ✅ **Testing exhaustivo** en editor Y frontend después de cada cambio
- ✅ **Backup de base de datos** antes de cambios
- ✅ **Verificar Swiper funciona** después de cambiar a local
- ✅ **Verificar countdown timer** funciona correctamente
- ✅ **Testing responsive** en móvil/tablet/desktop

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] El slider se renderiza correctamente
- [ ] Deal automático selecciona próximo a expirar
- [ ] Deal manual funciona correctamente
- [ ] Countdown timer actualiza cada segundo
- [ ] Countdown muestra días/horas/minutos/segundos correctos
- [ ] Countdown se detiene al expirar
- [ ] Swiper se inicializa correctamente
- [ ] Autoplay funciona (si habilitado)
- [ ] Autoplay pausa en hover
- [ ] Loop funciona (si habilitado)
- [ ] Flechas de navegación funcionan
- [ ] Dots de paginación funcionan
- [ ] Keyboard navigation funciona (a11y)
- [ ] Background images se muestran (desktop/mobile)
- [ ] Ribbon "TOP SELLER" aparece
- [ ] Stars rating calcula correctamente (full/half/empty)
- [ ] Included services icons se muestran
- [ ] Precios se formatean correctamente
- [ ] Botones "View Trip" y "Book Now" funcionan
- [ ] Links apuntan a URLs correctas
- [ ] Preview mode muestra mensaje si no hay deal
- [ ] Preview mode muestra mensaje si no hay packages

### Arquitectura
- [ ] ACF fields extraídos a método separado
- [ ] `register()` tiene <100 líneas
- [ ] Assets se cargan solo cuando el bloque está presente
- [ ] Swiper cargado localmente (no CDN)
- [ ] No hay warnings/notices en logs
- [ ] No hay errores en console del browser
- [ ] Template dividido en partials (si se hizo)
- [ ] DealRepository creado (si se hizo)
- [ ] PackageDataProvider creado (si se hizo)

### Seguridad
- [ ] Todos los `get_field()` sanitizados
- [ ] Todos los `get_post_meta()` sanitizados
- [ ] `$deal_id` validado antes de queries
- [ ] `$package_id` validado antes de queries
- [ ] `promo_tag_color` validado (hex color)
- [ ] `background_position` validado (whitelist)
- [ ] `slider_delay` validado (rango 2000-15000)
- [ ] Todos los outputs escapados en template
- [ ] No hay XSS posible
- [ ] No hay inyección CSS posible
- [ ] Swiper tiene SRI (si CDN) o local

### Performance
- [ ] CSS no se carga en páginas sin el bloque
- [ ] JS no se carga en páginas sin el bloque
- [ ] Swiper no se carga en páginas sin el bloque
- [ ] No hay console errors
- [ ] Countdown no causa memory leaks
- [ ] Swiper instances se limpian correctamente
- [ ] Imágenes tienen lazy loading
- [ ] Cache implementado para queries (si se hizo)

### Compatibilidad
- [ ] Funciona en Gutenberg editor
- [ ] Funciona en frontend
- [ ] Re-inicialización funciona en editor preview
- [ ] Funciona con diferentes themes
- [ ] Responsive en móvil (≤480px)
- [ ] Responsive en tablet (481-991px)
- [ ] Responsive en desktop (>991px)
- [ ] Funciona con align wide/full
- [ ] Funciona con bloques reutilizables
- [ ] Compatible con Full Site Editing

### Regresión
- [ ] Bloques existentes siguen funcionando
- [ ] ACF fields existentes se leen correctamente
- [ ] No rompe otros sliders
- [ ] No rompe countdown en otros lugares
- [ ] No rompe otros bloques que usan Swiper
- [ ] Otros bloques Deal funcionan
- [ ] BlockBase no roto (si se modificó)

### Testing Específico
- [ ] Countdown funciona con deals que expiran hoy
- [ ] Countdown funciona con deals que expiran en >30 días
- [ ] Slider funciona con 1 package
- [ ] Slider funciona con múltiples packages
- [ ] Slider funciona sin packages (muestra mensaje)
- [ ] Autoplay se puede deshabilitar
- [ ] Loop se puede deshabilitar
- [ ] Arrows se pueden ocultar
- [ ] Dots se pueden ocultar
- [ ] Background mobile fallback funciona
- [ ] Rating con 0 estrellas no rompe
- [ ] Rating con 5 estrellas funciona
- [ ] Rating con .5 (half star) funciona
- [ ] Packages sin included_services no rompen
- [ ] Packages sin thumbnail no rompen

---

## 📊 Resumen Ejecutivo

### Estado Actual

**El bloque DealsSlider es un slider complejo y funcional con excelente UX y diseño, pero con problemas significativos de arquitectura y seguridad.** El código es generalmente legible y bien estructurado visualmente, con separación entre clase PHP y template. Usa Swiper.js de forma efectiva y tiene un countdown timer bien implementado. Sin embargo, sufre de responsabilidades mezcladas (SRP violation), sanitización faltante, assets cargados globalmente, y un método `register()` gigante de 274 líneas lleno de ACF fields inline.

**Hallazgos principales:**
- ❌ **Sanitización crítica faltante** - Todos los `get_field()` y `get_post_meta()` sin sanitizar
- ❌ **Método register() gigante** - 274 líneas con ACF fields inline (ilegible)
- ❌ **Assets globales** - 803 CSS + 276 JS + Swiper cargados en todas las páginas
- ❌ **Swiper desde CDN** - Dependencia externa sin SRI, single point of failure
- ⚠️ **Violaciones SOLID severas** - SRP, DIP
- ⚠️ **Acoplamiento muy alto** - WordPress, ACF, post types, taxonomías
- ⚠️ **Template muy largo** - 333 líneas, debería dividirse
- ⚠️ **Validación faltante** - IDs, colores, rangos no validados
- ✅ **Escapado excelente** - Template muy bien protegido
- ✅ **JavaScript profesional** - Bien estructurado, a11y, error handling
- ✅ **UX excelente** - Countdown, autoplay, keyboard nav, responsive

### Puntuación: 6.8/10

**Desglose:**
- Funcionalidad: 9/10 (excelente, feature-rich, todo funciona)
- Seguridad: 5/10 (buen escapado, pero sanitización crítica faltante)
- Arquitectura: 4/10 (violaciones SOLID severas, acoplamiento alto)
- Clean Code: 6/10 (legible pero método gigante, template largo)
- Performance: 4/10 (assets globales, CDN externo)
- Mantenibilidad: 6/10 (bien estructurado pero difícil modificar)
- UX/Diseño: 9/10 (excelente diseño, responsive, accesible)

### Fortalezas

1. ✅ **JavaScript excepcional** - IIFE, error handling, cleanup, a11y, keyboard nav, custom events
2. ✅ **UX profesional** - Countdown timer funcional, autoplay con pause, responsive perfecto
3. ✅ **Escapado consistente y completo** - Uso correcto de esc_attr, esc_url, esc_html, wp_json_encode
4. ✅ **Diseño responsive excelente** - Mobile-first, breakpoints bien definidos, custom properties
5. ✅ **Accesibilidad considerada** - Swiper a11y config, aria-labels, keyboard navigation
6. ✅ **Feature-rich** - Auto/manual deal selection, countdown, slider, ribbons, ratings, icons
7. ✅ **Preview mode funcional** - Mensajes claros cuando no hay datos
8. ✅ **Documentación buena** - Comentarios claros, docblocks, variables documentadas
9. ✅ **Código limpio en general** - Métodos cortos (excepto register), nombres claros, lógica clara
10. ✅ **Template bien escapado** - Riesgo XSS muy bajo

### Debilidades

1. ❌ **Sanitización completamente faltante** - 15 `get_field()` + 10+ `get_post_meta()` sin sanitizar
2. ❌ **Método register() gigante** - 274 líneas, 267 de ACF fields inline, ilegible
3. ❌ **Assets cargados globalmente** - ~1150 líneas CSS/JS + Swiper en TODAS las páginas
4. ❌ **Swiper desde CDN público** - Sin SRI, single point of failure, supply chain risk
5. ⚠️ **Violación SRP severa** - Clase hace registro + enqueue + render + ACF + queries + data transformation
6. ⚠️ **Template muy largo** - 333 líneas, debería dividirse en partials
7. ⚠️ **Sin validación de inputs** - IDs, colores hex, rangos, post existence
8. ⚠️ **Acoplamiento extremo** - WordPress, ACF, post types, taxonomías, Swiper CDN
9. ⚠️ **No usa repositorios** - WP_Query directo, get_post_meta directo
10. ⚠️ **Iconos hardcoded** - SVG inline en template, mapeo hardcoded

### Problemas Específicos

**Método `register()` - 274 líneas:**
- Contiene 267 líneas de ACF fields config inline
- Hace el código completamente ilegible
- Dificulta versionado y review
- **Debe extraerse a método separado o JSON**

**Método `get_package_data()` - 60 líneas:**
- Demasiado largo para un método
- Múltiples `get_post_meta()` sin sanitizar
- Lógica de iconos hardcoded en template
- **Debe refactorizarse a PackageDataProvider**

**Template - 333 líneas:**
- Demasiado largo para mantenimiento
- Debería dividirse en partials
- Iconos SVG inline (líneas 61-70, 166-248)
- Mapeo de iconos hardcoded (líneas 241-248)

**Seguridad:**
- **CRÍTICO:** `promo_tag_color` no validado - puede inyectar CSS
- **CRÍTICO:** `background_position` no validado - puede inyectar CSS
- **ALTO:** Todos los textos sin sanitizar - riesgo XSS si admin comprometido
- **MEDIO:** IDs no validados - puede causar queries con valores inválidos

**Performance:**
- Assets se cargan con `enqueue_block_assets` hook → TODAS las páginas
- Swiper (~70KB) cargado siempre desde CDN
- 803 líneas CSS cargadas siempre
- 276 líneas JS cargadas siempre
- **Performance impact significativo**

### Comparación con otros bloques

**Mejor que:**
- Bloques ACF simples sin JavaScript
- Bloques que mezclan lógica y presentación
- Bloques sin a11y consideration

**Peor que:**
- Bloques con sanitización completa
- Bloques con assets condicionales
- Bloques con repositorios separados
- Bloques con ACF fields en JSON

**Similar a:**
- Otros bloques Deal en complejidad
- Bloques Package en estructura
- Bloques que usan sliders externos

### Recomendación

**REFACTORIZAR CON PRIORIDAD ALTA.** Aunque el bloque es funcional y tiene excelente UX, los problemas de sanitización son **críticos de seguridad** y deben resolverse inmediatamente. El método `register()` gigante hace el código inmantenible. Los assets globales impactan performance significativamente en todas las páginas del sitio.

**Ruta recomendada:**

1. **Inmediato (1 día):**
   - ✅ Sanitizar todos los `get_field()` y `get_post_meta()`
   - ✅ Extraer ACF fields de `register()` a método separado
   - ✅ Validar `$deal_id` y `$package_id`
   - ✅ Validar colores hex

2. **Corto plazo (1 semana):**
   - ✅ Cargar assets condicionalmente con `has_block()`
   - ✅ Mover Swiper a vendor local
   - ✅ Dividir template en partials

3. **Mediano plazo (1 mes):**
   - ⚠️ Crear `DealRepository` para queries
   - ⚠️ Crear `PackageDataProvider` para data transformation
   - ⚠️ Reemplazar iconos inline con `IconHelper`
   - ⚠️ Exportar ACF fields a JSON

4. **Largo plazo (3 meses):**
   - ⚠️ Eliminar `extract()` en BlockBase (afecta múltiples bloques)
   - ⚠️ Implementar cache para queries
   - ⚠️ Agregar unit tests
   - ⚠️ Optimizar CSS (803 líneas)

**El bloque tiene potencial para ser 9/10 con las refactorizaciones propuestas.** La funcionalidad y UX son excelentes, solo necesita mejorar arquitectura y seguridad.

### Notas Importantes

**Dependencias críticas:**
- Post type `deal` con meta: `active`, `end_date`, `discount_percentage`, `packages`
- Post type `package` con meta: `promo_enabled`, `days`, `physical_difficulty`, `rating`, `price_normal`, `price_offer`, `promo_tag`, `promo_tag_color`, `summary`
- Taxonomía `package_type`
- Taxonomía `included_services` con slugs: bus, train, tent, hotel, meals, guide
- Swiper.js library (CDN o local)
- ACF Pro plugin

**Riesgos al refactorizar:**
- Cambiar ACF field keys rompe contenido existente
- Cambiar clases CSS rompe estilos
- Cambiar Swiper config puede romper slider
- Modificar BlockBase afecta TODOS los bloques
- Cambiar template structure puede romper diseño

**Testing crítico:**
- Countdown timer con diferentes fechas
- Slider con 1, 2, y múltiples packages
- Responsive en todos los breakpoints
- Funcionalidad en editor Y frontend
- Cleanup de intervals (memory leaks)
- Re-inicialización en editor preview

---

### Métricas Finales

**Complejidad:**
- Métodos >50 líneas: 2 (`register()` 274 ❌, `render()` 74 ⚠️, `get_package_data()` 60 ⚠️)
- Métodos 30-50 líneas: 2 (`enqueue_assets()` 40, `get_active_deal()` 33)
- Métodos <30 líneas: 2 ✅
- Nivel de anidación máximo: 4 (template) ⚠️
- Complejidad ciclomática estimada: Media-Alta

**Deuda Técnica:**
- Sanitización faltante: **CRÍTICA** 🔴
- ACF fields inline: **ALTA** 🟠
- Assets globales: **ALTA** 🟠
- Swiper CDN: **MEDIA** 🟡
- Template largo: **MEDIA** 🟡
- Violaciones SOLID: **MEDIA** 🟡
- Total líneas: 1999 (considerable)

**Esfuerzo estimado refactorización completa:** 20-30 horas

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Pendiente - **Prioridad Alta** 🔴
**Próximo bloque:** Deal audit completo ✅ (3/3)
