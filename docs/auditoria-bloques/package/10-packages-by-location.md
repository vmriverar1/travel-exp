# Auditoría: PackagesByLocation (Package)

**Fecha:** 2025-11-09
**Bloque:** 10/XX Package
**Tiempo:** 30 min
**⚠️ ESTADO:** NECESITA REFACTORIZACIÓN URGENTE - Métodos muy largos y NO usa ContentQueryHelper
**⚠️ NOTA IMPORTANTE:** Bloque de archivo/filtrado de paquetes por ubicación

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `acf/packages-by-location`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Category:** `template-blocks`
- **Icon:** `location`
- **ACF fields:** NO cambiar keys (prefijo `field_pbl_`)

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **NO usa ContentQueryHelper** ❌ (inconsistente, debería usarlo)
- **WP_Query directa** ⚠️ (hardcoded, sin abstracción)
- **Renderizado inline** (NO usa template separado - 173 líneas de HTML)
- **ACF dependency:** Bloque completamente ACF

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **ContentQueryHelper:** ❌ NO usa (pero DEBERÍA)
- **ACF:** acf_register_block_type(), acf_add_local_field_group(), get_field()
- **WordPress:** WP_Query, paginate_links, is_singular, get_query_var
- **Post Type:** 'package' (custom post type)
- **Taxonomy/Meta:** 'destination' (ACF relationship/meta field)

### ⚠️ IMPORTANTE - PROBLEMAS CRÍTICOS
**ACLARACIÓN CRÍTICA:**
1. **Método render() DEMASIADO LARGO:** 173 líneas (3.5x límite recomendado)
2. **Método register_acf_fields() DEMASIADO LARGO:** 181 líneas (3.6x límite recomendado)
3. **NO usa ContentQueryHelper:** Query hardcoded, sin reutilización
4. **Todo inline:** CSS/HTML dentro de método render(), sin separación
5. **Sin validación:** get_field() sin sanitización ni type checking
6. **Magic values everywhere:** Números, strings hardcoded sin constantes

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/PackagesByLocation.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** ❌ NO usa template separado (renderizado inline - 173 líneas)
**Assets:**
- CSS: ❌ NO tiene archivo CSS separado (estilos inline en HTML)
- JS: ❌ NO tiene JavaScript

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico crítico)
- ❌ NO usa ContentQueryHelper (problema arquitectónico crítico)
- ACF functions (acf_register_block_type, acf_add_local_field_group, get_field)
- WP_Query (query directa sin abstracción)
- WordPress template functions (the_post_thumbnail, the_permalink, the_title, etc.)
- WordPress pagination (paginate_links)
- WordPress conditional tags (is_singular, get_query_var)

**Líneas de Código:**
- **Clase PHP:** 394 líneas
  - register(): 24 líneas
  - register_acf_fields(): 181 líneas ❌ (DEMASIADO LARGO)
  - render(): 173 líneas ❌ (DEMASIADO LARGO)
- **Template:** 0 líneas (todo inline)
- **JavaScript:** 0 líneas
- **CSS:** 0 líneas (todo inline)
- **TOTAL:** 394 líneas (PHP puro)

---

## 2. Propósito y Funcionalidad

**Descripción:** Bloque ACF que muestra una grilla de paquetes turísticos filtrados por ubicación/destino. Puede funcionar en modo automático (detecta la ubicación actual si estás en una página de location) o manual (seleccionas una ubicación específica).

**Funcionalidad Principal:**
1. **Filter Mode (Auto/Manual):**
   - **Auto:** Detecta si estás en `is_singular('location')` y usa get_the_ID()
   - **Manual:** Usa location ID seleccionado en campo ACF 'location'
   - Si no hay location_id → muestra preview placeholder (editor) o no renderiza (frontend)

2. **Query de Packages:**
   - WP_Query directa (NO usa ContentQueryHelper)
   - Post type: 'package'
   - Meta query: 'destination' = $location_id
   - Paginación: get_query_var('paged')
   - Posts per page configurable (default: 12)

3. **Display Options:**
   - Section title opcional
   - Columns: 2, 3, o 4 (default: 3)
   - Posts per page: 1-50 (default: 12)
   - Show pagination: true/false

4. **Card Display Options:**
   - Show image (featured image)
   - Show price (price_from ACF field)
   - Show duration (duration ACF field)
   - Show rating (rating ACF field)
   - Show excerpt (excerpt con length configurable)
   - Excerpt length: 5-50 words (default: 20)

5. **Renderizado:**
   - Grid con CSS grid inline
   - Cards con estilos inline
   - Pagination con paginate_links()
   - Empty state si no hay packages
   - Preview mode en editor

**Inputs (ACF - Registrado en código):**
- `filter_mode` (select): 'auto' | 'manual'
- `location` (post_object): Location post ID (condicional si filter_mode = 'manual')
- `section_title` (text): Título opcional de sección
- `columns` (select): '2' | '3' | '4'
- `posts_per_page` (number): 1-50
- `show_pagination` (true_false): boolean
- `show_image` (true_false): boolean
- `show_price` (true_false): boolean
- `show_duration` (true_false): boolean
- `show_rating` (true_false): boolean
- `show_excerpt` (true_false): boolean
- `excerpt_length` (number): 5-50 (condicional si show_excerpt = true)

**Outputs:**
- Grid de package cards
- Pagination links
- Empty state message
- Preview placeholder (editor)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'packages-by-location';
private string $title = 'Packages by Location';
private string $description = 'Display packages filtered by location/destination';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque ACF (24 líneas)
2. register_acf_fields(): void - Registra campos ACF (181 líneas) ❌ DEMASIADO LARGO
3. render($block, $content = '', $is_preview = false, $post_id = 0): void - Renderiza (173 líneas) ❌ DEMASIADO LARGO
```

**Total:** 3 métodos, 394 líneas

**Métodos más largos:**
1. ❌ `register_acf_fields()` - **181 líneas** (CRÍTICO - 3.6x límite recomendado)
2. ❌ `render()` - **173 líneas** (CRÍTICO - 3.5x límite recomendado)
3. ✅ `register()` - **24 líneas** (aceptable)

**Observación:** ❌ **2/3 métodos exceden CRÍTICAMENTE el límite de 50 líneas**

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (ACF Block)

**Configuración:**
- name: `packages-by-location`
- title: `Packages by Location` (traducible)
- description: `Display packages filtered by location/destination` (traducible)
- category: `template-blocks`
- icon: `location`
- keywords: ['packages', 'location', 'destination', 'filter', 'archive']
- supports:
  - anchor: true
  - html: false
  - align: ['wide', 'full']
- render_callback: `[$this, 'render']`

**Enqueue Assets:**
- ❌ **NO encola assets** (todo inline)
- ❌ **NO tiene CSS separado**
- ❌ **NO tiene JavaScript**
- ⚠️ **Estilos inline en método render()** (problema de separación de concerns)

**Block.json:** ❌ No existe (ACF blocks no usan block.json)

**Campos:** ✅ Registra campos en `register_acf_fields()` (pero método muy largo)

---

## 5. Campos Meta

**Definición:** ✅ Registra campos con `acf_add_local_field_group()`

**Group Key:** `group_packages_by_location_block`

**Campos Registrados:**

**Filter Settings:**
1. `filter_mode` (select)
   - Key: `field_pbl_filter_mode`
   - Choices: 'auto' (detect current location) | 'manual' (select specific location)
   - Default: 'auto'
   - UI: 1

2. `location` (post_object)
   - Key: `field_pbl_location`
   - Post type: ['location']
   - Return format: 'id'
   - UI: 1
   - Conditional logic: filter_mode == 'manual'

**Display Settings (Tab):**
3. `section_title` (text)
   - Key: `field_pbl_section_title`
   - Placeholder: 'Available Packages'

4. `columns` (select)
   - Key: `field_pbl_columns`
   - Choices: '2' | '3' | '4'
   - Default: '3'

5. `posts_per_page` (number)
   - Key: `field_pbl_posts_per_page`
   - Default: 12
   - Min: 1, Max: 50

6. `show_pagination` (true_false)
   - Key: `field_pbl_show_pagination`
   - Default: 1

**Card Options (Tab):**
7. `show_image` (true_false) - Default: 1
8. `show_price` (true_false) - Default: 1
9. `show_duration` (true_false) - Default: 1
10. `show_rating` (true_false) - Default: 1
11. `show_excerpt` (true_false) - Default: 1
12. `excerpt_length` (number)
    - Default: 20, Min: 5, Max: 50
    - Conditional: show_excerpt == 1

**Location:**
- Bloque: `acf/packages-by-location`

**Problemas:**
- ❌ **Método register_acf_fields() DEMASIADO LARGO:** 181 líneas (crítico)
- ❌ **Array gigante:** Todo el field group en un solo array anidado
- ❌ **Difícil de mantener:** Cambiar un campo requiere navegar 181 líneas
- ⚠️ **NO usa constantes** para field keys
- ⚠️ **Emojis en labels** ('🎨', '🎴') - puede dar problemas de encoding

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**

**Paso 1: Determinar Location ID (líneas 222-234)**
```php
$filter_mode = get_field('filter_mode') ?: 'auto';
$location_id = null;

if ($filter_mode === 'auto') {
    if (is_singular('location')) {
        $location_id = get_the_ID();
    }
} else {
    $location_id = get_field('location');
}
```

**Paso 2: Early Return si no hay location (líneas 237-245)**
```php
if (!$location_id) {
    if ($is_preview) {
        echo '<preview placeholder>';
    }
    return;
}
```

**Paso 3: Get Display Settings (líneas 248-259)**
```php
$section_title = get_field('section_title');
$columns = get_field('columns') ?: '3';
$posts_per_page = get_field('posts_per_page') ?: 12;
$show_pagination = get_field('show_pagination');
// Card options...
$show_image = get_field('show_image');
$show_price = get_field('show_price');
// etc...
```

**Paso 4: Get Paged (línea 262)**
```php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
```

**Paso 5: WP_Query (líneas 265-277)**
```php
$packages_query = new \WP_Query([
    'post_type' => 'package',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'destination',
            'value' => $location_id,
            'compare' => '='
        ]
    ]
]);
```

**Paso 6: Get Location Name (línea 280)**
```php
$location_name = get_the_title($location_id);
```

**Paso 7: Output HTML (líneas 283-391)**
- Inline HTML con estilos inline
- Loop de packages
- Conditional rendering de card elements
- Pagination
- Empty state

**Flujo de Datos:**
```
render($block, $content, $is_preview, $post_id)
  → get_field('filter_mode')
    → 'auto': is_singular('location')? get_the_ID() : null
    → 'manual': get_field('location')
  → $location_id?
    → NO: preview placeholder o return
    → YES:
      → get_field() x 10 (display settings)
      → get_query_var('paged')
      → new WP_Query (meta_query: destination = $location_id)
      → get_the_title($location_id)
      → output HTML inline (173 líneas)
        → while ($packages_query->have_posts())
          → the_post()
          → get_field('duration', 'price_from', 'rating')
          → conditional card elements
        → paginate_links()
      → wp_reset_postdata()
```

**Manejo de Errores:**
- ✅ Early return si no hay location_id
- ✅ Preview placeholder en editor
- ✅ Empty state si no hay packages
- ✅ Fallback defaults para campos (?: operator)
- ❌ **NO valida tipos** de get_field()
- ❌ **NO sanitiza** $location_id antes de query
- ❌ **NO valida** que $posts_per_page sea número
- ❌ **NO valida** que $columns sea valor válido

---

## 7. Funcionalidades Adicionales

### 7.1 Filter Mode (Auto/Manual)

**Funcionalidad:**
- **Auto mode:** Detecta si estás en `is_singular('location')` y usa get_the_ID()
- **Manual mode:** Usa location ID seleccionado en ACF field
- Conditional logic en ACF: campo 'location' solo visible si filter_mode = 'manual'

**Calidad:** 8/10 - Buena funcionalidad, flexible

**Problemas:**
- ⚠️ NO valida que $location_id sea válido después de obtenerlo
- ⚠️ En auto mode, si NO es singular('location'), $location_id queda null (correcto, pero podría loggear)

### 7.2 Preview Mode

**Funcionalidad:**
- Solo en editor ($is_preview)
- Muestra placeholder con emoji 📍
- Mensaje instructivo: "Select a location or use this block on a single location page"
- Estilos inline con background #f0f0f0

**Calidad:** 7/10 - Útil pero básico

**Problemas:**
- ⚠️ Estilos inline (debería usar clase CSS)
- ⚠️ NO usa EditorHelper para detectar editor

### 7.3 WP_Query Directa

**Funcionalidad:**
```php
$packages_query = new \WP_Query([
    'post_type' => 'package',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'destination',
            'value' => $location_id,
            'compare' => '='
        ]
    ]
]);
```

**Calidad:** 4/10 - Funciona pero NO usa ContentQueryHelper

**Problemas CRÍTICOS:**
- ❌ **NO usa ContentQueryHelper** - Inconsistente con mejores bloques
- ❌ **Query hardcoded** - Difícil de reutilizar
- ❌ **NO sanitiza $location_id** - Potencial SQL injection (aunque WP_Query lo maneja)
- ❌ **NO valida $posts_per_page** - Puede ser string, null, etc.
- ❌ **Meta query sin preparación** - Aunque WP_Query lo maneja, no es best practice
- ⚠️ **NO hay caché** - Query se ejecuta en cada load
- ⚠️ **NO hay logging** de errores de query

**DEBERÍA SER:**
```php
use Travel\Blocks\Helpers\ContentQueryHelper;

$args = [
    'post_type' => 'package',
    'posts_per_page' => absint($posts_per_page),
    'paged' => $paged,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'destination',
            'value' => absint($location_id),
            'compare' => '=',
            'type' => 'NUMERIC'
        ]
    ]
];

$packages_query = ContentQueryHelper::query($args);
```

### 7.4 Pagination

**Funcionalidad:**
```php
if ($show_pagination && $packages_query->max_num_pages > 1):
    echo paginate_links([
        'total' => $packages_query->max_num_pages,
        'current' => $paged,
        'prev_text' => '← Previous',
        'next_text' => 'Next →',
    ]);
endif;
```

**Calidad:** 7/10 - Funciona correctamente

**Problemas:**
- ⚠️ Inline en método render() (debería estar en template)
- ⚠️ Textos NO traducibles ('← Previous', 'Next →')
- ⚠️ NO hay clase CSS wrapper para estilar

### 7.5 Card Rendering

**Funcionalidad:**
- Loop de packages con the_post()
- Conditional rendering basado en show_* fields
- Featured image con the_post_thumbnail()
- ACF fields: duration, price_from, rating
- Excerpt con wp_trim_words()
- Estilos inline para todo

**Calidad:** 5/10 - Funciona pero muy acoplado

**Problemas CRÍTICOS:**
- ❌ **TODO inline** en método render() (173 líneas)
- ❌ **Estilos inline** en HTML (difícil de mantener)
- ❌ **NO usa template separado** - Debería usar /templates/packages-by-location.php
- ❌ **NO sanitiza ACF fields** (duration, price_from, rating)
- ❌ **NO valida tipos** de ACF fields
- ⚠️ **Magic values** (colors, sizes) hardcoded
- ⚠️ **NO reutilizable** - No puedes usar este card design en otros bloques

### 7.6 JavaScript

**Archivo:** ❌ NO tiene JavaScript

**Razón:** Bloque estático, no necesita interactividad

### 7.7 CSS

**Archivo:** ❌ NO tiene CSS separado

**Problema CRÍTICO:** TODO inline en HTML

**Estilos inline (línea 287-391):**
- Wrapper: padding, max-width, margin
- Grid: display:grid, grid-template-columns, gap
- Cards: background, border-radius, box-shadow, padding
- Image: width, height, object-fit
- Typography: font-size, font-weight, color, line-height
- Button: padding, background, color, border-radius

**Calidad:** 3/10 - Funciona pero TERRIBLE práctica

**Problemas:**
- ❌ **NO hay archivo CSS separado** - Debería tener /assets/blocks/packages-by-location.css
- ❌ **Estilos inline** - Difícil de mantener
- ❌ **Magic values** hardcoded (colores, tamaños)
- ❌ **NO reutilizable** - Duplicación con otros bloques de packages
- ❌ **NO usa CSS custom properties** (--color-primary, etc.)
- ❌ **NO responsive** - Solo usa inline styles
- ⚠️ **Inline styles tienen alta especificidad** - Difícil de override

### 7.8 Hooks Propios

**Ninguno** - No usa hooks personalizados

**Oportunidad perdida:**
- Podría tener `apply_filters('packages_by_location_query_args', $args)`
- Podría tener `do_action('packages_by_location_before_grid', $packages_query)`
- Podría tener `apply_filters('packages_by_location_card_classes', $classes)`

### 7.9 Dependencias Externas

**ACF:**
- acf_register_block_type()
- acf_add_local_field_group()
- get_field() x 10+
- function_exists('acf_register_block_type')
- function_exists('acf_add_local_field_group')

**WordPress:**
- WP_Query (directa, sin abstracción)
- get_query_var('paged')
- is_singular('location')
- get_the_ID()
- get_the_title($location_id)
- the_post_thumbnail(), the_permalink(), the_title(), has_excerpt(), get_the_excerpt()
- wp_trim_words()
- paginate_links()
- wp_reset_postdata()
- esc_html(), esc_attr(), esc_url()
- number_format()
- __() (translations)

**Custom:**
- Post type: 'package'
- Post type: 'location'
- ACF field: 'destination' (en package posts)
- ACF field: 'duration' (en package posts)
- ACF field: 'price_from' (en package posts)
- ACF field: 'rating' (en package posts)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ❌ **VIOLA GRAVEMENTE**
- Clase hace MUCHAS cosas:
  1. Registrar bloque
  2. Definir 12 campos ACF (181 líneas)
  3. Obtener location ID (auto/manual)
  4. Hacer WP_Query
  5. Renderizar HTML (173 líneas)
  6. Renderizar cards (loop inline)
  7. Renderizar pagination
  8. Renderizar preview
  9. Aplicar estilos CSS (inline)
- **Impacto:** CRÍTICO - Imposible de mantener, difícil de testear

**OCP:** ❌ **VIOLA**
- No hereda de BlockBase → Difícil extender
- Query hardcoded → No se puede cambiar sin modificar código
- Card design hardcoded → No se puede customizar
- **Impacto:** ALTO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con otros bloques de packages
- **Impacto:** ALTO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ❌ **VIOLA GRAVEMENTE**
- Acoplado directamente a:
  - ACF (get_field sin abstracción)
  - WP_Query (sin ContentQueryHelper)
  - WordPress template functions (sin abstracción)
  - HTML inline (sin template engine)
  - CSS inline (sin stylesheet)
- **NO hay abstracción** en ningún lado
- **Impacto:** CRÍTICO - Difícil de testear, imposible de reutilizar

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ **2/3 métodos exceden CRÍTICAMENTE límite de 50 líneas**
- ❌ `register_acf_fields()`: **181 líneas** (3.6x límite) - CRÍTICO
- ❌ `render()`: **173 líneas** (3.5x límite) - CRÍTICO
- ✅ `register()`: 24 líneas (aceptable)
- **Clase total:** 394 líneas (muy largo)

**Anidación:**
- ⚠️ Máximo 4-5 niveles (en card loop)
- ⚠️ Anidación profunda en register_acf_fields (arrays)
- ⚠️ Anidación profunda en render (if/while/if)

**Duplicación:**
- ⚠️ Lógica de cards duplicada con otros bloques de packages
- ⚠️ Estilos duplicados con otros bloques
- ⚠️ Preview placeholder duplicado con otros bloques

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ⚠️ Prefijos ACF inconsistentes (pbl_ vs otros bloques)

**Código Sin Uso:**
- ⚠️ $content param en render() no usado
- ⚠️ $post_id param en render() no usado

**DocBlocks:**
- ❌ **0/3 métodos documentados** (0%)
- ❌ NO documenta params
- ❌ NO documenta return types
- ❌ NO documenta estructura de ACF fields
- ❌ NO documenta dependencies
- **Impacto:** ALTO - Código muy largo sin documentación

**Magic Values:**
- ❌ '3' (default columns)
- ❌ 12 (default posts_per_page)
- ❌ 20 (default excerpt_length)
- ❌ '1200px' (max-width)
- ❌ '3rem', '2rem', '1.5rem' (paddings)
- ❌ '#fff', '#f0f0f0', '#666', '#555', '#333', '#0073aa' (colors)
- ❌ '2rem', '12px', '8px', '4px' (border-radius, gaps)
- ❌ '250px' (image height)
- ❌ '2rem', '1.25rem', '1.5rem', '0.875rem', '0.9rem' (font-sizes)
- ❌ Docenas de magic values más...

### 8.3 Problemas de Seguridad

**Sanitización:**
- ❌ **NO sanitiza $location_id** antes de WP_Query (aunque WP_Query lo maneja)
- ❌ **NO valida tipo de $location_id** (puede ser string, array, etc.)
- ❌ **NO sanitiza $posts_per_page** (debería usar absint())
- ❌ **NO sanitiza $columns** (debería validar contra choices)
- ❌ **NO sanitiza ACF fields** (duration, price_from, rating)
- ✅ get_field() de ACF sanitiza automáticamente
- **Impacto:** MEDIO - WordPress/ACF manejan la mayoría, pero no es best practice

**Escapado:**
- ✅ **Usa esc_html()** para titles, texts
- ✅ **Usa esc_attr()** para attributes
- ✅ **Usa esc_url()** para URLs (aunque solo implícito en the_permalink)
- ⚠️ **number_format() sin escapado** (debería usar esc_html)
- ⚠️ **$columns en grid-template-columns** sin validación (aunque es controlado)
- **Impacto:** BAJO - Mayormente correcto

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ⚠️ Meta query sin sanitización explícita (aunque WP_Query lo maneja)
- ❌ NO usa 'type' => 'NUMERIC' en meta query (debería)

**XSS:**
- ✅ Escapado mayormente correcto

**CSRF:**
- ✅ N/A - No tiene formularios

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ❌ **NO hay separación** - TODO en un método render() de 173 líneas
- ❌ **NO usa template** - HTML inline
- ❌ **NO usa stylesheet** - CSS inline
- ❌ **Lógica + presentación + estilos mezclados**
- **Impacto:** CRÍTICO - Imposible de mantener

**Acoplamiento:**
- ❌ **Acoplamiento CRÍTICO** a ACF (10+ get_field calls)
- ❌ **Acoplamiento CRÍTICO** a WP_Query (sin abstracción)
- ❌ **Acoplamiento CRÍTICO** a WordPress template functions
- ❌ **NO usa ContentQueryHelper** - Inconsistente con mejores bloques
- **Impacto:** CRÍTICO

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Pierde funcionalidades compartidas
  - Difícil de extender
- **Impacto:** ALTO

**Caché:**
- ❌ **NO hay caché** - WP_Query se ejecuta en cada load
- ❌ **NO usa transients** para query results
- **Impacto:** MEDIO - Performance degradada

**Otros:**
- ❌ **NO usa ContentQueryHelper** (CRÍTICO)
- ❌ **NO usa EditorHelper** para preview mode
- ❌ **NO usa template engine** (inline HTML)
- ❌ **NO usa CSS file** (inline styles)
- ❌ **NO reutiliza components** (card design duplicado)
- ❌ **register_acf_fields() gigante** (181 líneas)
- ⚠️ **Método render() gigante** (173 líneas)
- ⚠️ **Sin hooks propios** para extensibilidad

---

## 9. Recomendaciones de Refactorización

### Prioridad CRÍTICA (URGENTE)

**1. Usar ContentQueryHelper para WP_Query**
- **Acción:**
  ```php
  use Travel\Blocks\Helpers\ContentQueryHelper;

  $args = [
      'post_type' => 'package',
      'posts_per_page' => absint($posts_per_page),
      'paged' => $paged,
      'post_status' => 'publish',
      'meta_query' => [
          [
              'key' => 'destination',
              'value' => absint($location_id),
              'compare' => '=',
              'type' => 'NUMERIC'
          ]
      ]
  ];

  $packages_query = ContentQueryHelper::query($args);
  ```
- **Razón:** Consistencia, reutilización, caché, abstracción
- **Riesgo:** BAJO - ContentQueryHelper ya existe
- **Esfuerzo:** 30 min
- **Precauciones:**
  - Verificar que ContentQueryHelper existe y funciona
  - Testear paginación después del cambio
  - Verificar que meta_query funciona igual

**2. Separar template a archivo externo**
- **Acción:** Crear `/templates/packages-by-location.php` con todo el HTML del render()
- **Razón:** Separación de concerns, mantenibilidad, reutilización
- **Riesgo:** BAJO
- **Esfuerzo:** 1 hora
- **Estructura:**
  ```php
  // En render():
  $data = $this->prepare_data($block, $is_preview);
  include locate_template('templates/packages-by-location.php', false, false, $data);

  // En prepare_data():
  return [
      'location_id' => $location_id,
      'section_title' => $section_title,
      'columns' => $columns,
      'packages_query' => $packages_query,
      'show_image' => $show_image,
      // etc...
  ];
  ```

**3. Extraer CSS a archivo separado**
- **Acción:** Crear `/assets/blocks/packages-by-location.css` con todos los estilos
- **Razón:** Separación de concerns, mantenibilidad, reutilización, performance
- **Riesgo:** BAJO
- **Esfuerzo:** 1 hora
- **Incluir:**
  - Grid styles
  - Card styles
  - Pagination styles
  - Preview styles
  - Responsive styles
  - Usar CSS custom properties para colors/sizes

**4. Refactorizar register_acf_fields() - Extraer a constantes/arrays**
- **Acción:**
  ```php
  private const FIELD_GROUP_KEY = 'group_packages_by_location_block';
  private const FIELD_PREFIX = 'field_pbl_';

  private function get_filter_fields(): array { /* ... */ }
  private function get_display_fields(): array { /* ... */ }
  private function get_card_fields(): array { /* ... */ }

  public function register_acf_fields(): void
  {
      if (!function_exists('acf_add_local_field_group')) {
          return;
      }

      $fields = array_merge(
          $this->get_filter_fields(),
          $this->get_display_fields(),
          $this->get_card_fields()
      );

      acf_add_local_field_group([
          'key' => self::FIELD_GROUP_KEY,
          'title' => 'Packages by Location Block Settings',
          'fields' => $fields,
          'location' => [/* ... */],
      ]);
  }
  ```
- **Razón:** Reducir complejidad, mejorar mantenibilidad, SRP
- **Riesgo:** BAJO
- **Esfuerzo:** 1.5 horas

**5. Validar y sanitizar inputs**
- **Acción:**
  ```php
  $location_id = absint($location_id);
  if (!$location_id || get_post_type($location_id) !== 'location') {
      return $this->render_preview();
  }

  $posts_per_page = absint($posts_per_page);
  $posts_per_page = max(1, min(50, $posts_per_page));

  $columns = in_array($columns, ['2', '3', '4'], true) ? $columns : '3';
  ```
- **Razón:** Seguridad, robustez, prevención de errores
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

### Prioridad Alta

**6. Heredar de BlockBase**
- **Acción:** `class PackagesByLocation extends BlockBase`
- **Razón:** Consistencia, funcionalidades compartidas
- **Riesgo:** MEDIO - Requiere refactorizar estructura
- **Esfuerzo:** 2 horas

**7. Extraer método prepare_query_args()**
- **Acción:**
  ```php
  private function prepare_query_args(int $location_id, int $posts_per_page, int $paged): array
  {
      return [
          'post_type' => self::POST_TYPE,
          'posts_per_page' => $posts_per_page,
          'paged' => $paged,
          'post_status' => 'publish',
          'meta_query' => [
              [
                  'key' => 'destination',
                  'value' => $location_id,
                  'compare' => '=',
                  'type' => 'NUMERIC'
              ]
          ]
      ];
  }
  ```
- **Razón:** SRP, testabilidad, reutilización
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**8. Extraer método get_location_id()**
- **Acción:**
  ```php
  private function get_location_id(string $filter_mode): ?int
  {
      if ($filter_mode === 'auto') {
          return is_singular('location') ? absint(get_the_ID()) : null;
      }

      $location = get_field('location');
      return $location ? absint($location) : null;
  }
  ```
- **Razón:** SRP, testabilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**9. Extraer componente de Card reutilizable**
- **Acción:** Crear `/templates/components/package-card.php` con HTML del card
- **Razón:** Reutilización, consistencia con otros bloques de packages
- **Riesgo:** MEDIO - Requiere coordinar con otros bloques
- **Esfuerzo:** 2 horas

**10. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con PHPDoc
- **Razón:** Mantenibilidad, onboarding de developers
- **Riesgo:** NINGUNO
- **Esfuerzo:** 1 hora

### Prioridad Media

**11. Convertir magic values a constantes**
- **Acción:**
  ```php
  private const POST_TYPE = 'package';
  private const LOCATION_POST_TYPE = 'location';
  private const META_KEY_DESTINATION = 'destination';
  private const DEFAULT_COLUMNS = '3';
  private const DEFAULT_POSTS_PER_PAGE = 12;
  private const DEFAULT_EXCERPT_LENGTH = 20;
  private const MAX_WIDTH = '1200px';
  // etc...
  ```
- **Razón:** Mantenibilidad, claridad
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**12. Usar EditorHelper para preview mode**
- **Acción:**
  ```php
  use Travel\Blocks\Helpers\EditorHelper;

  if (!$location_id) {
      if (EditorHelper::is_editor()) {
          return $this->render_preview();
      }
      return;
  }
  ```
- **Razón:** Detección correcta de editor
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**13. Extraer método render_preview()**
- **Acción:**
  ```php
  private function render_preview(): void
  {
      include locate_template('templates/packages-by-location-preview.php');
  }
  ```
- **Razón:** SRP, separación de concerns
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**14. Agregar hooks propios para extensibilidad**
- **Acción:**
  ```php
  $args = apply_filters('packages_by_location_query_args', $args, $location_id);
  do_action('packages_by_location_before_grid', $packages_query);
  $card_classes = apply_filters('packages_by_location_card_classes', 'package-card', $post);
  ```
- **Razón:** Extensibilidad para otros plugins/themes
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**15. Implementar caché para query results**
- **Acción:**
  ```php
  $cache_key = 'packages_location_' . $location_id . '_page_' . $paged;
  $packages = get_transient($cache_key);

  if (false === $packages) {
      $packages_query = ContentQueryHelper::query($args);
      set_transient($cache_key, $packages_query, HOUR_IN_SECONDS);
  }
  ```
- **Razón:** Performance
- **Riesgo:** MEDIO - Requiere invalidación de caché
- **Esfuerzo:** 1 hora

### Prioridad Baja

**16. Traducir todos los strings**
- **Acción:** Usar __() para todos los strings user-facing
- **Razón:** i18n
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**17. Agregar unit tests**
- **Acción:** Crear tests para get_location_id(), prepare_query_args(), etc.
- **Razón:** Calidad, prevención de regresiones
- **Riesgo:** BAJO
- **Esfuerzo:** 3 horas

**18. Responsive design mejorado**
- **Acción:** Media queries en CSS para mobile/tablet
- **Razón:** UX móvil
- **Riesgo:** BAJO
- **Esfuerzo:** 1 hora

---

## 10. Plan de Acción

### Fase 1 - CRÍTICA (Esta semana - URGENTE)
1. Usar ContentQueryHelper (30 min)
2. Separar template a archivo (1 hora)
3. Extraer CSS a archivo (1 hora)
4. Refactorizar register_acf_fields() (1.5 horas)
5. Validar y sanitizar inputs (30 min)

**Total Fase 1:** 4.5 horas

### Fase 2 - Alta Prioridad (Próximas 2 semanas)
6. Heredar de BlockBase (2 horas)
7. Extraer prepare_query_args() (30 min)
8. Extraer get_location_id() (20 min)
9. Extraer componente Card reutilizable (2 horas)
10. Agregar DocBlocks (1 hora)

**Total Fase 2:** 5 horas 50 min

### Fase 3 - Media Prioridad (Mes próximo)
11. Convertir magic values a constantes (30 min)
12. Usar EditorHelper (15 min)
13. Extraer render_preview() (20 min)
14. Agregar hooks propios (30 min)
15. Implementar caché (1 hora)

**Total Fase 3:** 2 horas 35 min

### Fase 4 - Baja Prioridad (Cuando haya tiempo)
16. Traducir strings (30 min)
17. Unit tests (3 horas)
18. Responsive mejorado (1 hora)

**Total Fase 4:** 4 horas 30 min

**Total Refactorización Completa:** ~17 horas

**Precauciones Generales:**
- ⚠️ **CRÍTICO:** Testear paginación después de cada cambio
- ⚠️ **CRÍTICO:** Verificar que auto/manual mode sigue funcionando
- ⚠️ **CRÍTICO:** Verificar que meta_query funciona con ContentQueryHelper
- ⚠️ NO cambiar keys de ACF fields (field_pbl_*)
- ⚠️ NO cambiar block name (packages-by-location)
- ⚠️ Testear con location que no tiene packages (empty state)
- ⚠️ Testear con location que tiene 100+ packages (pagination)
- ⚠️ Validar que CSS funciona en todas las columnas (2, 3, 4)

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Filter mode 'auto' funciona (detecta singular location)
- [ ] Filter mode 'manual' funciona (usa location seleccionado)
- [ ] Preview mode funciona (muestra placeholder si no hay location)
- [ ] Frontend funciona (muestra packages)
- [ ] Todos los ACF fields funcionan

### Query
- [ ] ContentQueryHelper usado (si se implementó)
- [ ] WP_Query funciona correctamente
- [ ] Meta query filtra por destination
- [ ] Paginación funciona (múltiples páginas)
- [ ] posts_per_page respetado
- [ ] Caché implementado (si se agregó)

### Display Settings
- [ ] Section title se muestra
- [ ] Columnas (2, 3, 4) funcionan
- [ ] Posts per page funciona (1-50)
- [ ] Pagination se muestra/oculta correctamente

### Card Options
- [ ] Show image funciona
- [ ] Show price funciona
- [ ] Show duration funciona
- [ ] Show rating funciona
- [ ] Show excerpt funciona
- [ ] Excerpt length funciona

### Template
- [ ] Template separado funciona (si se creó)
- [ ] Variables pasadas correctamente
- [ ] Loop de packages funciona
- [ ] Conditional rendering funciona

### CSS
- [ ] CSS separado funciona (si se creó)
- [ ] Grid styles funcionan
- [ ] Card styles funcionan
- [ ] Responsive funciona (mobile/tablet/desktop)
- [ ] Pagination styles funcionan
- [ ] Preview styles funcionan

### Seguridad
- [ ] location_id sanitizado (absint)
- [ ] posts_per_page sanitizado (absint)
- [ ] columns validado (in_array)
- [ ] ACF fields escapados (esc_html, esc_attr, esc_url)
- [ ] Meta query con type => 'NUMERIC'

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] ContentQueryHelper usado (si se agregó)
- [ ] EditorHelper usado (si se agregó)
- [ ] Template separado (si se creó)
- [ ] CSS separado (si se creó)
- [ ] Constantes definidas (si se agregaron)
- [ ] Métodos extraídos (prepare_query_args, get_location_id, etc.)

### Clean Code
- [ ] register_acf_fields() <50 líneas (si se refactorizó)
- [ ] render() <50 líneas (si se refactorizó)
- [ ] Anidación <3 niveles
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] No magic values (si se convirtieron a constantes)
- [ ] Código reutilizable (componente Card)

### Performance
- [ ] CSS solo se carga cuando es necesario
- [ ] WP_Query optimizado
- [ ] Caché implementado (si se agregó)
- [ ] Lazy loading de imágenes
- [ ] Transients para query results (si se agregó)

### Extensibilidad
- [ ] Hooks propios funcionan (si se agregaron)
- [ ] Filters funcionan (si se agregaron)
- [ ] Actions funcionan (si se agregaron)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ❌ **Métodos CRÍTICAMENTE largos** (181 y 173 líneas)
- ❌ **NO usa ContentQueryHelper** (inconsistente con mejores bloques)
- ❌ **NO hereda de BlockBase** (inconsistente)
- ❌ **TODO inline** (HTML, CSS en método render())
- ❌ **Sin separación de concerns** (MVC violado)
- ❌ **Sin DocBlocks** (0/3 métodos)
- ❌ **Sin validación/sanitización** de inputs
- ❌ **Magic values everywhere** (docenas de valores hardcoded)
- ❌ **Viola SRP, DIP, LSP** gravemente
- ✅ Funcionalidad correcta (funciona)
- ✅ Escapado de seguridad mayormente correcto
- ✅ Lógica de filter mode (auto/manual) útil

### Puntuación: 4.5/10

**Razones para la puntuación:**
- ➕ Funcionalidad correcta y útil (+1.5)
- ➕ Escapado de seguridad mayormente correcto (+1)
- ➕ Filter mode auto/manual útil (+0.5)
- ➕ Conditional logic en ACF fields (+0.5)
- ➕ Pagination implementada (+0.5)
- ➕ Card options flexibles (+0.5)
- ➖ NO usa ContentQueryHelper (-1.5) **CRÍTICO**
- ➖ Métodos DEMASIADO largos (-2) **CRÍTICO**
- ➖ TODO inline (HTML/CSS) (-1.5) **CRÍTICO**
- ➖ NO hereda BlockBase (-0.5)
- ➖ Sin DocBlocks (-0.5)
- ➖ Sin validación/sanitización (-0.5)
- ➖ Magic values everywhere (-0.5)
- ➖ Viola SOLID gravemente (-0.5)

### Fortalezas
1. **Funcionalidad correcta:** Filtrado por location funciona (auto/manual)
2. **Flexibilidad:** Muchas opciones de display y card options
3. **Pagination:** Implementada correctamente con paginate_links
4. **Conditional logic:** ACF fields con conditional logic útil
5. **Escapado:** Mayormente correcto (esc_html, esc_attr)
6. **Preview mode:** Placeholder útil en editor
7. **Empty state:** Mensaje si no hay packages
8. **Fallback defaults:** Usa ?: operator para defaults

### Debilidades (CRÍTICAS)
1. ❌ **NO usa ContentQueryHelper** - Inconsistente, query hardcoded
2. ❌ **Métodos DEMASIADO largos** - 181 y 173 líneas (3.5x límite)
3. ❌ **TODO inline** - HTML y CSS en método render()
4. ❌ **Sin separación de concerns** - Lógica + presentación + estilos mezclados
5. ❌ **NO hereda de BlockBase** - Inconsistente con otros bloques
6. ❌ **Sin validación** - get_field() sin sanitización ni type checking
7. ❌ **Sin DocBlocks** - 0/3 métodos documentados
8. ❌ **Magic values everywhere** - Docenas de valores hardcoded
9. ❌ **Viola SOLID gravemente** - SRP, DIP, LSP violados
10. ❌ **NO reutilizable** - Card design duplicado con otros bloques
11. ❌ **Sin caché** - WP_Query se ejecuta en cada load
12. ❌ **Sin hooks propios** - No extensible

### Recomendación Principal

**Este bloque REQUIERE REFACTORIZACIÓN URGENTE.**

**PROBLEMAS CRÍTICOS:**
1. **Métodos gigantes:** 181 y 173 líneas → Refactorizar URGENTE
2. **NO usa ContentQueryHelper:** Inconsistente → Cambiar URGENTE
3. **TODO inline:** HTML/CSS → Separar a archivos URGENTE
4. **Sin separación de concerns:** MVC violado → Refactorizar URGENTE

**Prioridad CRÍTICA (Esta semana - 4.5 horas):**
1. Usar ContentQueryHelper (30 min)
2. Separar template a archivo (1 hora)
3. Extraer CSS a archivo (1 hora)
4. Refactorizar register_acf_fields() - Extraer a métodos (1.5 horas)
5. Validar y sanitizar inputs (30 min)

**Prioridad Alta (2 semanas - 5 horas 50 min):**
6. Heredar de BlockBase (2 horas)
7. Extraer prepare_query_args() (30 min)
8. Extraer get_location_id() (20 min)
9. Extraer componente Card reutilizable (2 horas)
10. Agregar DocBlocks (1 hora)

**Esfuerzo mínimo para hacer el código aceptable:** ~10 horas (Fase 1 + Fase 2)

**Esfuerzo total para refactorización completa:** ~17 horas

**Veredicto:** Este bloque es **EL MÁS PROBLEMÁTICO** de todos los bloques de Package auditados hasta ahora. Métodos CRÍTICAMENTE largos, NO usa ContentQueryHelper (cuando debería), TODO inline sin separación de concerns, viola SOLID gravemente. Aunque la funcionalidad es correcta y útil, el código es IMPOSIBLE de mantener y completamente inconsistente con mejores bloques. **PRIORIDAD: Refactorización URGENTE esta semana (Fase 1), luego Fase 2 en próximas 2 semanas.**

**ACCIÓN INMEDIATA REQUERIDA:**
1. Usar ContentQueryHelper (CRÍTICO)
2. Separar template a archivo (CRÍTICO)
3. Extraer CSS a archivo (CRÍTICO)
4. Refactorizar register_acf_fields() (CRÍTICO)

### Dependencias Identificadas

**ACF:**
- acf_register_block_type()
- acf_add_local_field_group()
- get_field() x 10+ (sin sanitización)
- 12 ACF fields registrados (filter_mode, location, section_title, columns, posts_per_page, show_pagination, show_image, show_price, show_duration, show_rating, show_excerpt, excerpt_length)

**WordPress Query:**
- WP_Query (directa, sin ContentQueryHelper) ❌
- get_query_var('paged')
- paginate_links()
- wp_reset_postdata()

**WordPress Conditional Tags:**
- is_singular('location')
- get_the_ID()
- get_the_title($location_id)

**WordPress Template Functions:**
- the_post_thumbnail(), the_permalink(), the_title(), has_excerpt(), get_the_excerpt()
- wp_trim_words()
- has_post_thumbnail()

**Custom Post Types:**
- 'package' (packages to display)
- 'location' (filter by location)

**ACF Fields (en package posts):**
- 'destination' (meta query field) - Relationship/ID de location
- 'duration' (card display)
- 'price_from' (card display)
- 'rating' (card display)

**Helpers que DEBERÍA usar pero NO:**
- ❌ ContentQueryHelper (para WP_Query)
- ❌ EditorHelper (para preview mode)

**JavaScript:**
- ❌ NO tiene JavaScript

**CSS:**
- ❌ NO tiene CSS separado (todo inline)

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** CRÍTICA - Refactorización URGENTE (Fase 1 esta semana)
**Próxima revisión:** Después de Fase 1 (4.5 horas)
