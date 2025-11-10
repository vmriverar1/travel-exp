# Auditoría: RelatedPostsGrid (Package)

**Fecha:** 2025-11-09
**Bloque:** 18/XX Package
**Tiempo:** 35 min
**⚠️ ESTADO:** CRÍTICO - Inconsistencia grave entre PHP y template
**⚠️ NOTA IMPORTANTE:** Template espera 7+ variables que NO se pasan desde PHP

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/related-posts-grid`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Icon:** `grid-view`
- **Category:** `template-blocks`
- **Keywords:** related, posts, blog, articles

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **Usa template separado** ✅ (related-posts-grid.php)
- **⚠️ INCONSISTENCIA CRÍTICA:** Template espera 7+ variables que NO se pasan desde PHP
- **NO usa ContentQueryHelper** ❌ (hace WP_Query directo - problema arquitectónico)
- **Lógica de relacionados:** Filtra por taxonomy 'destination'

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** ✅ Usa is_editor_mode() correctamente
- **WordPress Query:** WP_Query directo (NO usa ContentQueryHelper)
- **Taxonomy:** 'destination' (para posts relacionados)
- **Template:** related-posts-grid.php (79 líneas - ⚠️ INCONSISTENTE)
- **CSS:** related-posts-grid.css (255 líneas - grid responsive con overlay)

### ⚠️ IMPORTANTE - INCONSISTENCIA TEMPLATE

**ACLARACIÓN CRÍTICA:** El bloque tiene una **inconsistencia GRAVE** entre PHP y template:

**PHP pasa al template:**
```php
$data = [
    'block_id' => 'related-posts-grid-abc123',
    'class_name' => 'related-posts-grid custom-class',
    'posts' => [/* array de posts */],
    'section_title' => 'Take a look to this reading!',
    'is_preview' => false,
];
```

**Template espera (PERO NO SE PASAN):**
```php
$section_subtitle // NO se pasa - línea 12, 18
$button_text // NO se pasa - línea 36
$show_category_badge // NO se pasa - línea 42
$show_excerpt // NO se pasa - línea 50
$excerpt_length // NO se pasa - línea 53
$show_more_button_text // NO se pasa - línea 69
$show_more_button_url // NO se pasa - línea 71
```

**RESULTADO:** ⛔ **El template va a generar PHP warnings** por variables indefinidas.

### ⚠️ IMPORTANTE - NO USA ContentQueryHelper

**ACLARACIÓN CRÍTICA:** A diferencia de otros bloques de query (PackagesByLocation, SearchResults), este bloque:
- ❌ **NO usa ContentQueryHelper::get_posts()**
- ❌ Hace WP_Query directo
- ❌ NO sigue el patrón arquitectónico establecido

Esto es **inconsistente** con la arquitectura del plugin y dificulta mantenimiento.

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/RelatedPostsGrid.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** ✅ `/templates/related-posts-grid.php` (79 líneas - ⚠️ INCONSISTENTE con PHP)
**Assets:**
- CSS: `/assets/blocks/related-posts-grid.css` (255 líneas - grid responsive con overlay)
- JS: ❌ NO tiene JavaScript

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- ❌ NO usa ContentQueryHelper (problema arquitectónico)
- ✅ EditorHelper::is_editor_mode() (correctamente usado)
- WordPress WP_Query (query directo)
- WordPress wp_get_post_terms() (taxonomy)
- WordPress get_the_category() (categorías de posts)

**Líneas de Código:**
- **Clase PHP:** 162 líneas
- **Template:** 79 líneas
- **JavaScript:** 0 líneas
- **CSS:** 255 líneas
- **TOTAL:** 496 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Grid de posts relacionados con overlay hover, categoría badge y metadata. Diseñado para mostrar artículos de blog relacionados al final de páginas de paquetes.

**Funcionalidad Principal:**
1. **Query de posts relacionados:**
   - Filtra posts por taxonomy 'destination'
   - Busca posts con misma destination que el post actual
   - Limita a 3 posts (hardcoded)
   - Ordena por fecha (DESC)
   - Solo posts publicados

2. **Datos de cada post:**
   - ID
   - Título
   - Permalink
   - Excerpt
   - Thumbnail (size: 'medium_large')
   - Fecha
   - Categorías (array con name/slug)

3. **Preview mode:**
   - Muestra 3 posts hardcoded de ejemplo
   - Datos realistas sobre viajes en Sudamérica
   - NO usa datos reales en editor

4. **Template rendering:**
   - Usa load_template() con extract()
   - ⚠️ **PROBLEMA:** Pasa solo 5 variables pero template espera 12+

5. **CSS responsive:**
   - Grid con columns configurables (2, 3, 4)
   - Hover con overlay gradient
   - Botón "Read More" en overlay
   - Responsive breakpoints (1023px, 767px)

**Inputs (NO configurables - todo hardcoded):**
- ❌ **NO tiene attributes de configuración**
- ❌ Número de posts hardcoded (3)
- ❌ Taxonomía hardcoded ('destination')
- ❌ Título de sección hardcoded ("Take a look to this reading!")

**Outputs:**
- Grid de 3 posts relacionados con:
  - Imagen con hover overlay
  - Badge de categoría (⚠️ pero variable NO se pasa)
  - Título del post
  - Excerpt (⚠️ pero variables NO se pasan)
  - Fecha
  - Botón "Show More" (⚠️ pero variables NO se pasan)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'related-posts-grid';
private string $title = 'Related Posts Grid';
private string $description = 'Display related blog posts in a grid';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (14 líneas)
2. enqueue_assets(): void - Encola CSS (6 líneas)
3. render($attributes, $content, $block): string - Renderiza (24 líneas)
```

**Métodos Privados:**
```php
4. get_preview_data(): array - Datos de preview (30 líneas)
5. get_post_data(int $post_id): array - Query de posts relacionados (56 líneas)
```

**Métodos Protegidos:**
```php
6. load_template(string $template_name, array $data = []): void - Carga template (10 líneas)
```

**Total:** 6 métodos, 162 líneas

**Métodos más largos:**
1. ⚠️ `get_post_data()` - **56 líneas** (aceptable pero al límite)
2. ✅ `get_preview_data()` - **30 líneas** (bueno)
3. ✅ `render()` - **24 líneas** (excelente)
4. ✅ `register()` - **14 líneas** (excelente)

**Observación:** ✅ TODOS los métodos <60 líneas (aceptable)

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/related-posts-grid`
- api_version: 2
- category: `template-blocks`
- icon: `grid-view`
- keywords: ['related', 'posts', 'blog', 'articles']
- supports: anchor: true, html: false
- render_callback: `[$this, 'render']`

**Enqueue Assets:**
- CSS: `/assets/blocks/related-posts-grid.css` (sin condiciones)
- Hook: `enqueue_block_assets`
- ⚠️ **NO hay conditional loading** - CSS se carga siempre (incluso en páginas sin el bloque)

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Attributes:** ❌ **NO DEFINE ATTRIBUTES** - Todo hardcoded

---

## 5. Campos Meta

**Definición:** ❌ **NO TIENE CAMPOS META**

**Razón:** El bloque NO es configurable:
- ❌ Número de posts hardcoded (3)
- ❌ Taxonomy hardcoded ('destination')
- ❌ Título hardcoded ("Take a look to this reading!")
- ❌ Order/orderby hardcoded
- ❌ NO permite configurar qué mostrar (excerpt, category, etc.)

**Debería tener attributes para:**
- Número de posts a mostrar
- Columnas del grid
- Mostrar/ocultar excerpt
- Mostrar/ocultar categoría
- Longitud del excerpt
- Título y subtítulo personalizables
- Botón "Show More" configurable

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Try-catch wrapper (líneas 35-58)
2. Get post_id con get_the_ID() (línea 38)
3. Check preview mode con EditorHelper::is_editor_mode() (línea 39)
4. Si preview: get_preview_data() (línea 41)
5. Si NO preview: get_post_data($post_id) (línea 41)
6. Early return si NO hay posts (línea 42)
7. Generate block_id con uniqid() (línea 45)
8. Append className si existe (línea 46)
9. Hardcoded section_title (línea 48 - ⚠️ NO configurable)
10. Build $data array (líneas 44-50)
11. Output con ob_start/load_template/ob_get_clean (líneas 52-54)
12. Catch exceptions con mensaje de error en WP_DEBUG (líneas 55-57)

**Flujo de Datos:**
```
render()
  → EditorHelper::is_editor_mode()?
    → YES: get_preview_data()
      → return 3 hardcoded preview posts
    → NO: get_post_data($post_id)
      → wp_get_post_terms($post_id, 'destination')
      → WP_Query con tax_query (si hay destinations)
      → Loop: get_the_title/permalink/excerpt/thumbnail/date
      → get_the_category() para cada post
      → wp_reset_postdata()
      → return posts array
  → empty check on posts
  → load_template('related-posts-grid', $data)
    → extract($data) - ⚠️ Solo 5 variables
    → include template - ⚠️ Template espera 12+ variables
```

**Variables al Template:**
```php
$block_id = 'related-posts-grid-abc123'; // string ✅
$class_name = 'related-posts-grid custom-class'; // string ✅
$posts = [/* array de posts */]; // array ✅
$section_title = 'Take a look to this reading!'; // string ✅
$is_preview = false; // bool ✅

// ⚠️ FALTAN (template las espera):
$section_subtitle // NO definida - línea 12, 18
$button_text // NO definida - línea 36
$show_category_badge // NO definida - línea 42
$show_excerpt // NO definida - línea 50
$excerpt_length // NO definida - línea 53
$show_more_button_text // NO definida - línea 69
$show_more_button_url // NO definida - línea 71
```

**⚠️ PROBLEMA CRÍTICO:** El template va a generar **PHP warnings** por "Undefined variable" en líneas 12, 36, 42, 50, 53, 69, 71.

**Manejo de Errores:**
- ✅ Try-catch wrapper en render()
- ✅ WP_DEBUG check antes de mostrar error
- ✅ Escapado de error con esc_html()
- ✅ Return empty string si error y NO WP_DEBUG
- ✅ File exists check en load_template()
- ✅ Empty check en posts antes de renderizar

---

## 7. Funcionalidades Adicionales

### 7.1 Query de Posts Relacionados

**Método:** `get_post_data()` (líneas 93-149)

**Funcionalidad:**
```php
// 1. Get destinations del post actual
$destinations = wp_get_post_terms($post_id, 'destination', ['fields' => 'ids']);

// 2. Query posts
$args = [
    'post_type' => 'post',
    'posts_per_page' => 3, // ⚠️ Hardcoded
    'post_status' => 'publish',
    'orderby' => 'date', // ⚠️ Hardcoded
    'order' => 'DESC',
];

// 3. Si hay destinations → tax_query
if (!empty($destinations) && !is_wp_error($destinations)) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'destination',
            'field' => 'term_id',
            'terms' => $destinations,
        ],
    ];
}

// 4. WP_Query directo
$query = new \WP_Query($args);

// 5. Loop y build data
while ($query->have_posts()) {
    $query->the_post();
    // ... get title, permalink, excerpt, thumbnail, date, categories
}

// 6. wp_reset_postdata()
```

**Características:**
- ✅ Filtra por taxonomy 'destination' (lógica de relacionados)
- ✅ is_wp_error check en wp_get_post_terms
- ✅ wp_reset_postdata() presente
- ✅ Fallback: Si NO hay destinations → muestra posts más recientes
- ⚠️ **NO usa ContentQueryHelper** (inconsistente)
- ⚠️ **posts_per_page hardcoded** (debería ser configurable)
- ⚠️ **orderby/order hardcoded** (debería ser configurable)
- ⚠️ **Taxonomy 'destination' hardcoded** (podría ser configurable)

**Calidad:** 6/10 - Funcional pero con muchas limitaciones

**Problemas:**
1. ❌ NO usa ContentQueryHelper (rompe patrón arquitectónico)
2. ❌ posts_per_page hardcoded (3)
3. ❌ orderby hardcoded ('date')
4. ❌ Taxonomy hardcoded ('destination')
5. ❌ NO permite excluir post actual (podría aparecer en relacionados)
6. ❌ NO permite configurar image size (usa 'medium_large' hardcoded)

### 7.2 Construcción de Post Data

**Método:** `get_post_data()` (líneas 123-144)

**Funcionalidad:**
```php
$post_data = [
    'id' => get_the_ID(),
    'title' => get_the_title(),
    'permalink' => get_permalink(),
    'excerpt' => get_the_excerpt(),
    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium_large'),
    'date' => get_the_date(),
    'categories' => [],
];

$categories = get_the_category();
if (!empty($categories)) {
    foreach ($categories as $category) {
        $post_data['categories'][] = [
            'name' => $category->name,
            'slug' => $category->slug,
        ];
    }
}
```

**Características:**
- ✅ Estructura clara y completa
- ✅ thumbnail con size 'medium_large' (optimización)
- ✅ get_the_excerpt() automático
- ✅ Categorías en array estructurado
- ✅ Empty check en categories antes de loop
- ⚠️ **NO sanitiza excerpt** (confía en WordPress)
- ⚠️ **Image size hardcoded** ('medium_large')

**Calidad:** 8/10 - Bien estructurado

### 7.3 Preview Data

**Método:** `get_preview_data()` (líneas 60-91)

**Funcionalidad:**
- Retorna 3 posts hardcoded de ejemplo
- Datos realistas sobre viajes en Sudamérica:
  1. "Top 10 Travel Tips for South America"
  2. "Hidden Gems in Peru"
  3. "Sustainable Tourism Guide"
- Incluye categorías: Travel Tips, Destinations, Sustainability
- Fechas: October 15, 10, 5 de 2025

**Características:**
- ✅ Datos muy realistas y útiles
- ✅ Estructura idéntica a get_post_data()
- ✅ Incluye categorías variadas
- ✅ Textos descriptivos en inglés
- ✅ thumbnail vacío (correcto para preview)

**Calidad:** 9/10 - Excelente preview data

### 7.4 Template Loading

**Método:** `load_template()` (líneas 151-160)

**Funcionalidad:**
- Construye path: TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php'
- Check file_exists()
- Si NO existe: muestra warning en WP_DEBUG
- extract($data, EXTR_SKIP) → Convierte array keys a variables
- include $template_path

**Calidad:** 7/10 - Estándar pero con inconsistencias

**Problemas:**
- ⚠️ **extract() es peligroso** - Puede sobrescribir variables (usa EXTR_SKIP, mejor)
- ⚠️ **NO documenta** que usa extract
- ⚠️ **NO valida** que $data sea array
- ⚠️ **INCONSISTENCIA:** Variables extraídas NO cubren todas las esperadas en template
- ✅ File exists check presente
- ✅ WP_DEBUG check antes de warning
- ✅ Escapado con esc_html() en warning

### 7.5 JavaScript

**Archivo:** ❌ NO tiene JavaScript

**Razón:** El bloque es puramente presentacional

**Observación:** ✅ Correcto - No necesita JS (hover/overlay son CSS)

### 7.6 CSS

**Archivo:** `/assets/blocks/related-posts-grid.css` (255 líneas)

**Características:**
- ✅ Grid responsive con columns configurables (2, 3, 4)
- ✅ Card con box-shadow y hover (translateY -4px)
- ✅ Imagen con overlay gradient en hover
- ✅ Botón "Read More" en overlay
- ✅ Badge de categoría con color teal
- ✅ Responsive breakpoints: 1023px, 767px
- ✅ CSS variables (var(--color-teal), var(--border-radius-md))
- ✅ Hover effects (scale en imagen, opacity en overlay)
- ✅ Flexbox en content (push date to bottom)

**Organización:**
- Secciones claras: header, grid, item, image, content, footer
- Comentarios descriptivos
- Mobile-first approach (sort of)

**Calidad:** 9/10 - Muy completo y bien organizado

**Problemas menores:**
- ⚠️ Algunos colores hardcoded (#212121, #757575, etc.) - deberían usar variables
- ⚠️ Selector `.related-posts-grid--columns-X` pero NO se aplica desde PHP (NO hay attributes)

### 7.7 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.8 Dependencias Externas

- WordPress WP_Query (query directo - ❌ NO usa ContentQueryHelper)
- WordPress wp_get_post_terms() (taxonomy 'destination')
- WordPress get_the_category() (categorías)
- WordPress get_the_post_thumbnail_url() (thumbnail)
- WordPress get_the_excerpt() (excerpt)
- WordPress get_the_ID(), get_the_title(), get_permalink(), get_the_date()
- EditorHelper::is_editor_mode() ✅

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ **CUMPLE**
- Clase tiene una responsabilidad clara: mostrar grid de posts relacionados
- Métodos bien enfocados
- NO hay complejidad excesiva
- **Impacto:** NINGUNO

**OCP:** ⚠️ **VIOLA**
- NO hereda de BlockBase → Difícil extender
- Todo hardcoded (posts_per_page, taxonomy, orderby) → NO configurable
- **Impacto:** ALTO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- NO usa ContentQueryHelper cuando debería
- Inconsistente con mejores bloques
- **Impacto:** ALTO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Acoplado directamente a:
  - WP_Query (debería usar ContentQueryHelper)
  - Taxonomy 'destination' hardcoded
  - Estructura específica de template
- No hay abstracción/interfaces
- **Impacto:** ALTO - Rompe patrón arquitectónico

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **Método más largo: 56 líneas** (aceptable)
- ✅ Complejidad ciclomática baja
- ✅ get_post_data() podría dividirse pero NO es crítico

**Anidación:**
- ✅ **Máximo 2-3 niveles** de anidación (aceptable)
- ✅ Código legible

**Duplicación:**
- ✅ **NO hay duplicación significativa**
- ✅ Preview data y post data tienen estructura idéntica (correcto)

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ✅ Propiedades claras

**Código Sin Uso:**
- ✅ No detectado

**DocBlocks:**
- ❌ **0/6 métodos documentados** (0%)
- ❌ Header de archivo básico
- ❌ NO documenta params/return types
- **Impacto:** MEDIO

**Magic Values:**
- ⚠️ 3 hardcoded (posts_per_page - debería ser constante)
- ⚠️ 'destination' hardcoded (debería ser configurable)
- ⚠️ 'date', 'DESC' hardcoded (deberían ser configurables)
- ⚠️ 'medium_large' hardcoded (debería ser constante)
- ⚠️ 'Take a look to this reading!' hardcoded (debería ser configurable)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ WP_Query es seguro
- ✅ wp_get_post_terms() es seguro
- ✅ get_the_category() es seguro
- ✅ NO hay inputs de usuario directos
- **Impacto:** NINGUNO - Perfecto

**Escapado:**
- ⚠️ **Template usa escapado** pero variables NO se pasan correctamente
- ✅ esc_attr(), esc_url(), esc_html() presentes en template
- ⚠️ **PHP warnings** por variables indefinidas
- **Impacto:** MEDIO - Template tiene warnings

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ✅ Usa WP_Query (no queries directas)

**XSS:**
- ✅ Template tiene escapado correcto (cuando variables existen)
- ⚠️ **Problema:** Variables undefined pueden causar warnings

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ✅ **Template separado** (related-posts-grid.php)
- ⚠️ **Template inconsistente** con datos de la clase
- ✅ Lógica de negocio en clase
- ✅ Estilos en CSS separado

**Acoplamiento:**
- ⚠️ **Acoplamiento medio** - WP_Query directo, taxonomy 'destination' hardcoded
- ❌ **NO usa ContentQueryHelper** (rompe patrón)
- **Impacto:** ALTO

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Pierde funcionalidades compartidas
- **Impacto:** MEDIO

**Queries:**
- ❌ **NO usa ContentQueryHelper** (problema arquitectónico grave)
- ❌ **WP_Query directo** (inconsistente con PackagesByLocation, SearchResults)
- **Impacto:** ALTO

**Caché:**
- ✅ N/A - WP_Query tiene object cache propio

**Otros:**
- ❌ **NO usa block.json** (debería para Gutenberg moderno)
- ✅ **Usa EditorHelper** correctamente
- ❌ **NO tiene attributes** (todo hardcoded)

---

## 9. Recomendaciones de Refactorización

### Prioridad CRÍTICA

**1. ⛔ ARREGLAR INCONSISTENCIA PHP ↔ TEMPLATE**
- **Acción:**
  ```php
  // En render() - líneas 44-50:
  $data = [
      'block_id' => 'related-posts-grid-' . uniqid(),
      'class_name' => 'related-posts-grid' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
      'posts' => $posts,
      'section_title' => __('Take a look to this reading!', 'travel-blocks'),
      'section_subtitle' => '', // Agregar
      'button_text' => __('Read More', 'travel-blocks'), // Agregar
      'show_category_badge' => true, // Agregar
      'show_excerpt' => true, // Agregar
      'excerpt_length' => 20, // Agregar
      'show_more_button_text' => '', // Agregar (vacío = no mostrar)
      'show_more_button_url' => '', // Agregar (vacío = no mostrar)
      'is_preview' => $is_preview,
  ];
  ```
- **Razón:** ⛔ **CRÍTICO** - Template genera PHP warnings ahora
- **Riesgo:** BAJO - Solo agregar variables con valores default
- **Esfuerzo:** 30 min

**2. ⛔ MIGRAR A ContentQueryHelper**
- **Acción:**
  ```php
  // En get_post_data() - reemplazar WP_Query:
  use Travel\Blocks\Helpers\ContentQueryHelper;

  private function get_post_data(int $post_id): array
  {
      $destinations = wp_get_post_terms($post_id, 'destination', ['fields' => 'ids']);

      $tax_query = [];
      if (!empty($destinations) && !is_wp_error($destinations)) {
          $tax_query = [
              [
                  'taxonomy' => 'destination',
                  'field' => 'term_id',
                  'terms' => $destinations,
              ],
          ];
      }

      $args = [
          'post_type' => 'post',
          'posts_per_page' => 3,
          'post_status' => 'publish',
          'orderby' => 'date',
          'order' => 'DESC',
          'tax_query' => $tax_query,
      ];

      $posts = ContentQueryHelper::get_posts($args);

      return $posts; // ContentQueryHelper ya retorna formato correcto
  }
  ```
- **Razón:** ⛔ **CRÍTICO** - Seguir patrón arquitectónico
- **Riesgo:** MEDIO - Verificar que ContentQueryHelper retorna estructura correcta
- **Precauciones:**
  - Verificar que ContentQueryHelper::get_posts() retorna id, title, permalink, excerpt, thumbnail, date, categories
  - Si NO → adaptar estructura después
- **Esfuerzo:** 1.5 horas (incluye verificación y testing)

### Prioridad Alta

**3. Heredar de BlockBase**
- **Acción:** `class RelatedPostsGrid extends BlockBase`
- **Razón:** Consistencia, funcionalidades compartidas
- **Riesgo:** MEDIO - Requiere refactorizar
- **Esfuerzo:** 1 hora

**4. Agregar Block Attributes**
- **Acción:**
  ```php
  // En register():
  'attributes' => [
      'postsPerPage' => ['type' => 'number', 'default' => 3],
      'columns' => ['type' => 'number', 'default' => 3],
      'showExcerpt' => ['type' => 'boolean', 'default' => true],
      'excerptLength' => ['type' => 'number', 'default' => 20],
      'showCategoryBadge' => ['type' => 'boolean', 'default' => true],
      'sectionTitle' => ['type' => 'string', 'default' => 'Take a look to this reading!'],
      'sectionSubtitle' => ['type' => 'string', 'default' => ''],
      'buttonText' => ['type' => 'string', 'default' => 'Read More'],
      'showMoreButtonText' => ['type' => 'string', 'default' => ''],
      'showMoreButtonUrl' => ['type' => 'string', 'default' => ''],
  ],
  ```
- **Razón:** Hacer bloque configurable (ahora todo hardcoded)
- **Riesgo:** BAJO
- **Esfuerzo:** 2 horas (incluye actualizar render() para usar attributes)

**5. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con params, returns, description
- **Razón:** Documentación para mantenimiento
- **Riesgo:** NINGUNO
- **Esfuerzo:** 30 min

### Prioridad Media

**6. Convertir hardcoded values a constantes**
- **Acción:**
  ```php
  private const DEFAULT_POSTS_PER_PAGE = 3;
  private const DEFAULT_COLUMNS = 3;
  private const DEFAULT_TAXONOMY = 'destination';
  private const IMAGE_SIZE = 'medium_large';
  private const DEFAULT_SECTION_TITLE = 'Take a look to this reading!';
  private const DEFAULT_BUTTON_TEXT = 'Read More';
  private const DEFAULT_EXCERPT_LENGTH = 20;

  // Uso:
  'posts_per_page' => $attributes['postsPerPage'] ?? self::DEFAULT_POSTS_PER_PAGE,
  ```
- **Razón:** Mantenibilidad, configurabilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**7. Excluir post actual de relacionados**
- **Acción:**
  ```php
  $args = [
      'post_type' => 'post',
      'posts_per_page' => 3,
      'post_status' => 'publish',
      'orderby' => 'date',
      'order' => 'DESC',
      'post__not_in' => [$post_id], // Agregar esto
  ];
  ```
- **Razón:** Evitar que post actual aparezca en relacionados
- **Riesgo:** BAJO
- **Esfuerzo:** 5 min

**8. Conditional CSS loading**
- **Acción:**
  ```php
  public function enqueue_assets(): void
  {
      if (!is_admin() && has_block('travel-blocks/related-posts-grid')) {
          wp_enqueue_style(...);
      }
  }
  ```
- **Razón:** Performance - Solo cargar CSS donde se necesita
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

### Prioridad Baja

**9. Crear block.json**
- **Acción:** Migrar de register_block_type() a block.json
- **Razón:** Gutenberg moderno, mejor performance
- **Riesgo:** BAJO
- **Esfuerzo:** 45 min

**10. Agregar configuración de taxonomy**
- **Acción:**
  ```php
  // Attribute:
  'relatedByTaxonomy' => ['type' => 'string', 'default' => 'destination'],

  // En get_post_data():
  $taxonomy = $attributes['relatedByTaxonomy'] ?? 'destination';
  $terms = wp_get_post_terms($post_id, $taxonomy, ['fields' => 'ids']);
  ```
- **Razón:** Flexibilidad para relacionar por categoría, tag, etc.
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**11. Mejorar fallback sin relacionados**
- **Acción:**
  ```php
  // Si NO hay posts relacionados → mostrar placeholder o mensaje
  if (empty($posts)) {
      return '<div class="related-posts-grid-placeholder">'
           . esc_html__('No related posts found.', 'travel-blocks')
           . '</div>';
  }
  ```
- **Razón:** UX - Informar al usuario si NO hay relacionados
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min

---

## 10. Plan de Acción

### Fase 0 - CRÍTICO (Esta semana)
1. ⛔ **Arreglar inconsistencia PHP ↔ Template** (30 min) - Agregar variables faltantes
2. ⛔ **Migrar a ContentQueryHelper** (1.5 horas) - Seguir patrón arquitectónico

**Total Fase 0:** 2 horas

### Fase 1 - Alta Prioridad (Esta semana)
3. Heredar de BlockBase (1 hora)
4. Agregar Block Attributes (2 horas)
5. Agregar DocBlocks (30 min)

**Total Fase 1:** 3.5 horas

### Fase 2 - Media Prioridad (Próximas 2 semanas)
6. Convertir hardcoded a constantes (30 min)
7. Excluir post actual (5 min)
8. Conditional CSS loading (15 min)

**Total Fase 2:** 50 min

### Fase 3 - Baja Prioridad (Cuando haya tiempo)
9. Crear block.json (45 min)
10. Configuración de taxonomy (30 min)
11. Mejorar fallback sin relacionados (10 min)

**Total Fase 3:** 1 hora 25 min

**Total Refactorización Completa:** ~7 horas 55 min

**Precauciones Generales:**
- ⛔ **MUY IMPORTANTE:** Primero agregar variables al template (Fase 0.1)
- ⛔ **MUY IMPORTANTE:** Migrar a ContentQueryHelper (Fase 0.2) antes de otras refactorizaciones
- ⚠️ **Verificar** que ContentQueryHelper retorna estructura correcta
- ⚠️ **NO cambiar** lógica de relacionados (destination) sin consultar
- ✅ SIEMPRE probar con posts reales después de cambios
- ✅ Verificar que grid responsive funciona correctamente

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra 3 posts hardcoded)
- [ ] Frontend funciona (muestra posts relacionados)
- [ ] ⛔ **NO hay PHP warnings** por variables undefined

### Query de Posts
- [ ] Filtra por taxonomy 'destination' correctamente
- [ ] Muestra posts relacionados (misma destination)
- [ ] Fallback funciona (posts recientes si NO hay destination)
- [ ] Limita a 3 posts (o valor configurado)
- [ ] ⛔ **Post actual NO aparece en relacionados**
- [ ] ⛔ **Usa ContentQueryHelper::get_posts()** (si se migró)

### Datos de Posts
- [ ] Título se muestra correctamente
- [ ] Permalink funciona
- [ ] Thumbnail se muestra (size medium_large)
- [ ] Excerpt se muestra (si show_excerpt = true)
- [ ] Fecha se muestra
- [ ] Categoría badge se muestra (si show_category_badge = true)
- [ ] Escapado correcto en todos los outputs

### Template
- [ ] load_template() carga correctamente
- [ ] extract() crea todas las variables necesarias
- [ ] ⛔ **Todas las variables esperadas están disponibles**
- [ ] section_title se muestra (línea 15)
- [ ] section_subtitle se muestra si existe (línea 18)
- [ ] button_text se muestra en overlay (línea 36)
- [ ] show_category_badge funciona (línea 42)
- [ ] show_excerpt funciona (línea 50)
- [ ] excerpt_length se aplica (línea 53)
- [ ] show_more_button_text/url funcionan (líneas 69-71)

### CSS
- [ ] Estilos se aplican correctamente
- [ ] Grid funciona (3 columnas default)
- [ ] Card hover funciona (translateY -4px, box-shadow)
- [ ] Imagen scale en hover funciona
- [ ] Overlay gradient en hover funciona
- [ ] Botón "Read More" aparece en hover
- [ ] Badge de categoría se muestra correctamente
- [ ] Responsive funciona (1023px → 2 cols, 767px → 1 col)
- [ ] Conditional loading funciona (si se agregó)

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] ⛔ **Usa ContentQueryHelper** (si se migró)
- [ ] Block attributes funcionan (si se agregaron)
- [ ] Constantes definidas (si se agregaron)
- [ ] block.json (si se creó)

### Clean Code
- [ ] Métodos <60 líneas ✅ (ya cumple)
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] Constantes en lugar de magic values (si se cambiaron)

### Performance
- [ ] CSS solo se carga donde se necesita (si se agregó conditional)
- [ ] Thumbnail usa size optimizado (medium_large)
- [ ] WP_Query con límite (posts_per_page)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Código PHP bien estructurado (162 líneas)
- ✅ Lógica de relacionados por 'destination' (inteligente)
- ✅ Preview data muy realista (3 posts sobre viajes)
- ✅ CSS completo con grid responsive y overlay (255 líneas)
- ✅ Usa EditorHelper correctamente
- ✅ Try-catch wrapper en render()
- ✅ wp_reset_postdata() presente
- ✅ Empty checks en lugares correctos
- ⛔ **INCONSISTENCIA CRÍTICA: Template espera 7+ variables que NO se pasan**
- ⛔ **NO usa ContentQueryHelper** (rompe patrón arquitectónico)
- ❌ NO hereda de BlockBase
- ❌ NO tiene attributes (todo hardcoded)
- ❌ NO tiene DocBlocks (0/6 métodos)

### Puntuación: 6.0/10

**Razones para la puntuación:**
- ➕ Lógica de relacionados inteligente (+1)
- ➕ Preview data realista (+0.5)
- ➕ CSS completo con overlay (+1)
- ➕ Grid responsive (+0.5)
- ➕ Query con fallback (+0.5)
- ➕ Código bien estructurado (+0.5)
- ➕ Try-catch wrapper (+0.5)
- ➕ wp_reset_postdata presente (+0.5)
- ➖ ⛔ **Template NO tiene variables** (-2.5) ← CRÍTICO
- ➖ ⛔ **NO usa ContentQueryHelper** (-1.5) ← CRÍTICO
- ➖ NO hereda BlockBase (-0.5)
- ➖ TODO hardcoded (NO attributes) (-0.5)
- ➖ Sin DocBlocks (-0.5)

### Fortalezas
1. **Lógica de relacionados:** Filtra por taxonomy 'destination' (smart)
2. **Fallback inteligente:** Si NO hay destination → posts recientes
3. **Preview data:** 3 posts realistas sobre viajes en Sudamérica
4. **CSS completo:** Grid responsive con overlay gradient en hover
5. **Código limpio:** Métodos cortos (<60 líneas), buena legibilidad
6. **Usa EditorHelper:** Correctamente implementado
7. **Try-catch wrapper:** Manejo de errores robusto
8. **Early return:** Si NO hay posts, NO renderiza
9. **wp_reset_postdata:** Presente (evita bugs de query)
10. **is_wp_error check:** En wp_get_post_terms (robusto)

### Debilidades
1. ⛔ **INCONSISTENCIA CRÍTICA PHP ↔ TEMPLATE** - Template espera 7+ variables que NO se pasan
2. ⛔ **NO usa ContentQueryHelper** - Rompe patrón arquitectónico (WP_Query directo)
3. ❌ **NO hereda de BlockBase** - Inconsistente con arquitectura
4. ❌ **NO tiene attributes** - TODO hardcoded (posts_per_page, taxonomy, orderby)
5. ❌ **NO tiene DocBlocks** (0/6 métodos)
6. ⚠️ **NO exclude post actual** - Podría aparecer en relacionados
7. ⚠️ **NO conditional CSS loading** - CSS se carga siempre
8. ⚠️ **posts_per_page hardcoded** (3)
9. ⚠️ **Taxonomy 'destination' hardcoded** - Debería ser configurable
10. ⚠️ **section_title hardcoded** - NO permite personalización

### Recomendación Principal

**Este bloque tiene DOS PROBLEMAS CRÍTICOS que deben resolverse INMEDIATAMENTE:**

**PROBLEMA CRÍTICO 1:** ⛔ El template espera 7+ variables que NO se pasan desde PHP. Esto causará **PHP warnings** en producción.

**PROBLEMA CRÍTICO 2:** ⛔ El bloque NO usa ContentQueryHelper cuando DEBERÍA (PackagesByLocation y SearchResults SÍ lo usan). Esto rompe el patrón arquitectónico.

**Prioridad 0 - CRÍTICO (Esta semana - 2 horas):**
1. ⛔ **Arreglar inconsistencia PHP ↔ Template** (30 min)
   - Agregar variables faltantes: section_subtitle, button_text, show_category_badge, show_excerpt, excerpt_length, show_more_button_text, show_more_button_url
   - Todas con valores default sensatos
2. ⛔ **Migrar a ContentQueryHelper** (1.5 horas)
   - Reemplazar WP_Query directo por ContentQueryHelper::get_posts()
   - Verificar estructura de retorno
   - Probar exhaustivamente

**Prioridad 1 - Alta (Esta semana - 3.5 horas):**
3. Heredar de BlockBase (1 hora)
4. Agregar Block Attributes (2 horas) - Hacer configurable
5. Agregar DocBlocks (30 min)

**Prioridad 2 - Media (2 semanas - 50 min):**
6. Constantes para hardcoded values (30 min)
7. Excluir post actual (5 min)
8. Conditional CSS loading (15 min)

**Prioridad 3 - Baja (Cuando haya tiempo - 1h 25min):**
9. block.json (45 min)
10. Configuración de taxonomy (30 min)
11. Fallback sin relacionados (10 min)

**Esfuerzo total:** ~7 horas 55 min

**Veredicto:** Este bloque tiene **buena lógica de negocio** (relacionados por destination) y **CSS bien hecho**, pero sufre de DOS problemas críticos:
1. Template inconsistente → Genera PHP warnings
2. NO usa ContentQueryHelper → Rompe patrón arquitectónico

**ACCIÓN URGENTE:** Antes de cualquier otra refactorización, DEBEN resolverse ambos problemas críticos. Sin esto:
- Template genera warnings (mala UX, logs llenos)
- Código inconsistente con otros bloques de query (mantenimiento difícil)

**PRIORIDAD: CRÍTICA - El bloque funciona pero genera warnings y NO sigue patrón.**

### Dependencias Identificadas

**WordPress:**
- WP_Query (query directo - ❌ debería usar ContentQueryHelper)
- wp_get_post_terms() - Obtener terms de taxonomy
- get_the_category() - Obtener categorías de posts
- get_the_post_thumbnail_url() - Obtener thumbnail
- get_the_excerpt() - Obtener excerpt
- get_the_ID(), get_the_title(), get_permalink(), get_the_date()

**Helpers:**
- EditorHelper::is_editor_mode() ✅
- ContentQueryHelper (⛔ **NO lo usa pero DEBERÍA**)

**Taxonomy:**
- 'destination' (hardcoded - para relacionados)

**JavaScript:**
- ❌ **NO tiene JavaScript**

**CSS:**
- related-posts-grid.css (255 líneas)
- Grid responsive con columns
- Hover overlay con gradient
- Botón "Read More" en overlay
- Badge de categoría
- Responsive breakpoints

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** ⛔ **CRÍTICA** - Resolver inconsistencia template + migrar a ContentQueryHelper
**Próxima revisión:** Después de resolver problemas críticos
