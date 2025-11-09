# Auditoría: ProductMetadata (Package)

**Fecha:** 2025-11-09
**Bloque:** 14/XX Package
**Tiempo:** 35 min
**⚠️ ESTADO:** REGULAR - Template incompleto, duplicación con MetadataLine, lógica no usada

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/product-metadata`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Template path:** `/templates/product-metadata.php`
- **Campos meta:** `tripadvisor_rating`, `tripadvisor_url`, `total_reviews`, `show_rating_badge`, `departure`, `origin`, `physical_difficulty`, `difficulty`, `duration`, `days`, `service_type`, `type`
- **IconHelper dependency:** Usa `star` para rating
- **Schema.org markup:** AggregateRating structured data

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **extract() en load_template** ⚠️ (línea 187) - potencialmente peligroso
- **Lógica de negocio en template** ⚠️ (difficulty_labels, type_labels mappings)
- **Template NO renderiza metadata line** ⚠️ - Solo muestra rating y título
- **CSS tiene estilos para metadata line** ⚠️ - NO usados en template actual
- **get_post_data prepara metadata** ⚠️ - Pero NO se usa en template
- **DUPLICACIÓN SIGNIFICATIVA con MetadataLine** ⚠️ - Métodos idénticos
- **metadata_color hardcoded** - Siempre 'default' (no configurable)
- **package_title agregado manualmente** - No viene de get_post_data
- **show_tripadvisor, show_metadata hardcoded** - No configurables

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** Para detectar modo preview
- **IconHelper:** Para renderizar iconos SVG (star)
- **Post meta fields:** Asume que existen (NO los registra)
- **TripAdvisor:** SVG logo hardcoded en template
- **Schema.org:** Genera structured data para rating

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/ProductMetadata.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** `/templates/product-metadata.php` (96 líneas)
**Assets:**
- CSS: `/assets/blocks/product-metadata.css` (260 líneas)
- JS: ❌ No tiene JavaScript

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- EditorHelper (para detectar editor mode)
- IconHelper (para iconos SVG - star)
- Post meta fields (NO los registra, asume que existen)

**Líneas de Código:**
- **Clase PHP:** 192 líneas
- **Template:** 96 líneas
- **CSS:** 260 líneas
- **JavaScript:** 0 líneas
- **TOTAL:** 548 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Bloque que muestra rating de TripAdvisor y metadata del producto/paquete. Combina badge de rating con información clave del paquete.

**Funcionalidad Principal:**
1. **TripAdvisor Rating Badge:**
   - Logo de TripAdvisor (SVG hardcoded)
   - Rating visual con estrellas (1-5)
   - Número de reviews
   - Link a TripAdvisor URL
   - Schema.org AggregateRating markup

2. **Package Title:**
   - H1 con título del paquete
   - Responsive typography

3. **Metadata Line (NO RENDERIZADA):**
   - ⚠️ CSS preparado para: origin, difficulty, duration, type
   - ⚠️ Datos preparados en get_post_data pero NO usados en template
   - ⚠️ Template actual SOLO muestra rating, NO metadata line

4. **Color variants preparados (NO usados):**
   - CSS tiene variants: default, primary, secondary
   - metadata_color hardcoded a 'default'

5. **Múltiples fuentes de datos:**
   - TripAdvisor: rating, url, reviews, show_rating_badge
   - Wizard fields: departure, physical_difficulty, duration, service_type
   - Legacy fields: origin, difficulty, days, type

**Inputs (Post Meta - NO registrados en código):**
- `tripadvisor_rating` (float) - Rating de 0 a 5
- `tripadvisor_url` (string) - URL de TripAdvisor
- `total_reviews` (int) - Número de reviews
- `show_rating_badge` (bool/string) - Mostrar/ocultar badge
- `departure` (string) - Origen del paquete (prioridad alta)
- `origin` (string) - Origen del paquete (fallback)
- `physical_difficulty` (string) - Dificultad (prioridad alta)
- `difficulty` (string) - Dificultad (fallback)
- `duration` (string) - Duración del paquete
- `days` (string) - Días (fallback para duration)
- `service_type` (string) - Tipo de servicio (prioridad alta)
- `type` (string) - Tipo de servicio (fallback)

**Outputs:**
- Section con package title (H1)
- TripAdvisor rating badge con link
- Schema.org structured data (JSON-LD)
- ⚠️ NO renderiza metadata line (aunque prepara datos y tiene CSS)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'product-metadata';
private string $title = 'Product Metadata';
private string $description = 'Muestra rating de TripAdvisor y metadata del producto (origen, dificultad, duración, tipo)';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (18 líneas)
2. enqueue_assets(): void - Encola assets (10 líneas)
3. render($attributes, $content, $block): string - Renderiza (48 líneas)
```

**Métodos Privados:**
```php
4. get_preview_data(): array - Preview data (14 líneas)
5. get_post_data(int $post_id): array - Obtiene datos del post (15 líneas)
6. load_template(string $template_name, array $data = []): void - Carga template (17 líneas)
```

**Total:** 6 métodos, 192 líneas

**Métodos más largos:**
1. ✅ `render()` - **48 líneas** (aceptable)
2. ✅ `register()` - **18 líneas** (excelente)
3. ✅ `load_template()` - **17 líneas** (excelente)
4. ✅ `get_post_data()` - **15 líneas** (excelente)
5. ✅ `get_preview_data()` - **14 líneas** (excelente)
6. ✅ `enqueue_assets()` - **10 líneas** (excelente)

**Observación:** ✅ TODOS los métodos están MUY bien dimensionados (<50 líneas)

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/product-metadata`
- api_version: 2
- category: `travel`
- icon: `star-filled`
- keywords: ['product', 'metadata', 'rating', 'tripadvisor', 'package']
- supports: anchor, html: false
- render_callback: `[$this, 'render']`
- show_in_rest: ❌ **NO definido** (debería estar)

**Enqueue Assets:**
- CSS: `/assets/blocks/product-metadata.css` (solo frontend, NO editor)
- Encolado en método separado `enqueue_assets()`
- Hook: `enqueue_block_assets`
- Condición: `!is_admin()` (solo frontend)

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Campos:** ❌ **NO REGISTRA CAMPOS** (asume que existen en post meta)

---

## 5. Campos Meta

**Definición:** ❌ **NO REGISTRA CAMPOS EN CÓDIGO**

**Campos usados (asume que existen):**

**TripAdvisor fields:**
- `tripadvisor_rating` - Rating de 0 a 5 (float)
- `tripadvisor_url` - URL de TripAdvisor (string)
- `total_reviews` - Número de reviews (int)
- `show_rating_badge` - Mostrar/ocultar (bool/string)

**Package metadata fields:**
- `departure` - Del wizard (prioridad alta)
- `origin` - Fallback para departure
- `physical_difficulty` - Del wizard (prioridad alta)
- `difficulty` - Fallback para physical_difficulty
- `duration` - Duración del paquete
- `days` - Fallback para duration
- `service_type` - Del wizard (prioridad alta)
- `type` - Fallback para service_type

**Problemas:**
- ❌ **NO registra campos** - Depende de que estén definidos externamente
- ❌ **NO documenta campos** - No hay PHPDoc de estructura esperada
- ❌ **NO valida campos** - get_post_meta() sin validación
- ❌ **NO sanitiza campos** - Usa valores directamente
- ⚠️ **NO valida URL de TripAdvisor** - Acepta cualquier string
- ⚠️ **NO valida rating range** - Acepta cualquier float
- ✅ **Múltiples fallbacks** - Buena estrategia de migración

**Conversión de tipos:**
- `floatval()` para rating (línea 154)
- `intval()` para reviews (línea 156)
- `!== '0'` para show_rating_badge (línea 157) - Compara string '0'

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Get post_id con get_the_ID() (línea 75)
2. Detecta preview mode con EditorHelper (línea 78)
3. Get data: preview vs post (líneas 81-87)
4. Generate unique block_id (línea 90)
5. Build class_name con attributes (líneas 91-95)
6. Add datos al array (líneas 98-107)
7. Load template con ob_start/ob_get_clean (líneas 110-112)
8. Try-catch con error display si WP_DEBUG (líneas 114-121)

**Flujo de Datos:**
```
render()
  → is_preview?
    → YES: get_preview_data()
    → NO: get_post_data()
      → get_post_meta('tripadvisor_rating') → floatval()
      → get_post_meta('tripadvisor_url') ?: ''
      → get_post_meta('total_reviews') → intval()
      → get_post_meta('show_rating_badge') !== '0'
      → get_post_meta('departure') ?? 'origin'
      → get_post_meta('physical_difficulty') ?? 'difficulty'
      → get_post_meta('duration') ?? 'days' . ' days'
      → get_post_meta('service_type') ?? 'type'
  → load_template()
```

**Variables al Template:**
```php
$data = [
    'block_id' => 'product-metadata-' . uniqid(),
    'class_name' => 'product-metadata' . $attributes['className'],
    'package_data' => [
        'tripadvisor_rating' => float,
        'tripadvisor_url' => string,
        'total_reviews' => int,
        'show_rating_badge' => bool,
        'origin' => string,           // ⚠️ NO usado en template
        'difficulty' => string,        // ⚠️ NO usado en template
        'duration' => string,          // ⚠️ NO usado en template
        'type' => string,             // ⚠️ NO usado en template
    ],
    'is_preview' => bool,
    'show_tripadvisor' => true,       // ⚠️ Hardcoded
    'show_metadata' => true,          // ⚠️ Hardcoded, NO usado
    'metadata_color' => 'default',    // ⚠️ Hardcoded, NO usado
    'package_title' => string,        // ⚠️ Agregado aquí, NO en get_post_data
];
```

**Manejo de Errores:**
- ✅ Try-catch en render()
- ✅ Error message si WP_DEBUG
- ✅ Empty return si error y NO WP_DEBUG
- ⚠️ NO valida rating range (0-5)
- ⚠️ NO valida URL format de TripAdvisor
- ⚠️ NO valida que reviews sea positivo

---

## 7. Funcionalidades Adicionales

### 7.1 TripAdvisor Rating Badge

**Componentes:**
1. **Logo SVG hardcoded** (líneas 57-61)
   - Owl face de TripAdvisor
   - Color: #00AF87
   - Width: 120px, Height: 24px

2. **Star rating visual** (líneas 64-70)
   - Loop 1-5 con IconHelper
   - Filled si <= round(rating)
   - Empty si > round(rating)
   - aria-label para accessibility

3. **Reviews count** (líneas 72-76)
   - number_format() para separador de miles
   - Translatable string

4. **Link a TripAdvisor** (línea 55)
   - target="_blank"
   - rel="noopener noreferrer"
   - ⚠️ NO valida URL

**Calidad:** 7/10 - Funciona bien pero falta validación de URL

### 7.2 Schema.org Structured Data

**Método:** JSON-LD en template (líneas 81-93)

```json
{
  "@context": "https://schema.org",
  "@type": "AggregateRating",
  "ratingValue": "4.9",
  "bestRating": "5",
  "worstRating": "1",
  "ratingCount": "1250"
}
```

**Características:**
- ✅ Solo se renderiza si NOT is_preview
- ✅ Solo si rating > 0
- ✅ Usa esc_js() para escapar
- ⚠️ NO incluye itemReviewed (debería linkear al package)

**Calidad:** 7/10 - Correcto pero incompleto (falta itemReviewed)

### 7.3 Template con Lógica de Negocio

**Archivo:** `/templates/product-metadata.php`

**Lógica en template:**
- **Difficulty labels mapping** (líneas 28-35):
  ```php
  $difficulty_labels = [
      'easy' => __('Easy', 'travel-blocks'),
      'moderate' => __('Moderate', 'travel-blocks'),
      // ...
  ];
  $difficulty_text = $difficulty_labels[$difficulty] ?? ucfirst($difficulty);
  ```
- **Type labels mapping** (líneas 38-42):
  ```php
  $type_labels = [
      'shared' => __('Shared', 'travel-blocks'),
      'private' => __('Private', 'travel-blocks'),
  ];
  $type_text = $type_labels[$type] ?? ucfirst($type);
  ```

**⚠️ PROBLEMA CRÍTICO:**
- Lógica de labels preparada pero **NUNCA USADA**
- Template NO renderiza metadata line
- CSS tiene estilos para metadata line NO usados
- Variables $difficulty_text, $type_text definidas pero NO usadas

**Calidad:** 3/10 - ❌ **Código muerto en template**

### 7.4 CSS con Estilos No Usados

**Archivo:** `/assets/blocks/product-metadata.css` (260 líneas)

**Secciones:**
1. **Container** (líneas 12-14) - ✅ Usado
2. **Package Title** (líneas 18-36) - ✅ Usado
3. **TripAdvisor Badge** (líneas 40-82) - ✅ Usado
4. **Metadata Line** (líneas 85-148) - ❌ **NO USADO** (template no renderiza)
5. **Duration Special Design** (líneas 113-148) - ❌ **NO USADO**
6. **Color Variants** (líneas 152-184) - ❌ **NO USADO**
7. **Responsive** (líneas 189-228) - ⚠️ Parcialmente usado
8. **Print Styles** (líneas 233-240) - ✅ Usado
9. **Accessibility** (líneas 245-259) - ✅ Usado

**Características:**
- ✅ CSS Variables (custom properties)
- ✅ Theme.json integration
- ✅ Responsive design
- ✅ Print styles
- ✅ Accessibility (focus-visible, high contrast)
- ❌ **~40% del CSS NO se usa** (metadata line styles)

**Calidad:** 5/10 - ❌ **Mucho CSS sin uso**

### 7.5 JavaScript

**Ninguno** - No requiere JavaScript

### 7.6 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.7 Dependencias Externas

- EditorHelper (interno)
- IconHelper (interno)
- Post meta (asume campos existen)
- TripAdvisor (logo SVG hardcoded)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ⚠️ **VIOLA PARCIALMENTE**
- Clase hace render y preparación de datos ✅
- Prepara datos que NO usa (metadata fields) ❌
- Template tiene lógica de labels NO usada ❌
- CSS tiene estilos NO usados ❌
- **Impacto:** MEDIO - Confusión sobre propósito real del bloque

**OCP:** ⚠️ **VIOLA**
- No hereda de BlockBase → Difícil extender
- show_tripadvisor, show_metadata hardcoded → No configurables
- metadata_color hardcoded → No configurable
- **Impacto:** MEDIO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con mejores bloques
- **Impacto:** MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Acoplado directamente a:
  - Post meta (get_post_meta hardcoded)
  - EditorHelper
  - IconHelper (en template)
- No hay abstracción/interfaces
- **Impacto:** BAJO - Aceptable para este bloque

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **TODOS los métodos <50 líneas** (EXCELENTE)
- ✅ Método más largo: render() 48 líneas
- ✅ Clase total: 192 líneas (excelente)

**Anidación:**
- ✅ Máximo 2 niveles (excelente)
- ✅ NO hay anidación excesiva

**Duplicación:**
- ❌ **DUPLICACIÓN SIGNIFICATIVA con MetadataLine:**
  - `get_post_data()` comparte lógica de metadata (origin, difficulty, type)
  - `load_template()` IDÉNTICO a MetadataLine
  - Fallbacks duplicados (departure → origin, etc.)
- **Impacto:** ALTO - Código duplicado entre bloques

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ⚠️ Nombre "ProductMetadata" confuso (solo muestra rating, NO metadata completa)

**Código Sin Uso:**
- ❌ **ALTO - Múltiples problemas:**
  - Metadata fields preparados pero NO renderizados
  - difficulty_labels, type_labels en template NO usados
  - ~40% del CSS NO se usa (metadata line styles)
  - show_metadata variable NO usada
  - metadata_color variable NO usada
- **Impacto:** ALTO - Confusión, mantenimiento innecesario

**DocBlocks:**
- ✅ Header de archivo tiene descripción
- ✅ Template tiene @var docs (12 líneas)
- ❌ **1/6 métodos documentados** (17%)
- ❌ NO documenta params/return types en métodos
- ❌ NO documenta estructura de package_data
- **Impacto:** MEDIO - Código es simple pero docs ayudarían

**Magic Values:**
- ⚠️ true hardcoded para show_tripadvisor, show_metadata (líneas 103-104)
- ⚠️ 'default' hardcoded para metadata_color (línea 105)
- ⚠️ ' days' concatenado en get_post_data (línea 162)
- ⚠️ '0' comparado como string en show_rating_badge (línea 157)
- ⚠️ 5 hardcoded en star loop (template línea 65)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ❌ **NO sanitiza campos meta** antes de usar
- ❌ get_post_meta() devuelve valores directamente
- ⚠️ tripadvisor_url NO se valida (acepta cualquier string)
- ⚠️ Rating NO se valida range (acepta cualquier float)
- ⚠️ floatval() y intval() NO previenen valores negativos
- **Impacto:** MEDIO - Template escapa, pero URLs sin validar

**Escapado:**
- ✅ Template usa esc_html(), esc_attr(), esc_url() correctamente
- ✅ IconHelper debe escapar SVG (asumimos que sí)
- ✅ esc_js() en Schema.org JSON-LD

**extract():**
- ⚠️ **Usa extract() en load_template** (línea 187)
- Usa EXTR_SKIP (más seguro que default)
- **Impacto:** BAJO - Pero es mala práctica
- **Recomendación:** Pasar variables directamente

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ✅ No hace queries directas

**XSS:**
- ✅ Template escapa correctamente
- ⚠️ Pero URL de TripAdvisor sin validación

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ⚠️ **Template tiene lógica de negocio NO usada** (labels mappings)
- ⚠️ package_title agregado en render(), NO en get_post_data
- ✅ Lógica de datos en clase
- ❌ Lógica de presentación mezclada con negocio

**Acoplamiento:**
- ⚠️ Acoplamiento a EditorHelper
- ⚠️ Acoplamiento a IconHelper
- ⚠️ Acoplamiento a post meta
- ⚠️ Duplica lógica de MetadataLine
- **Impacto:** MEDIO - Debería compartir código con MetadataLine

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Duplica código (load_template)
- **Impacto:** MEDIO

**Caché:**
- ✅ N/A - No necesita caché (data de post meta)

**Otros:**
- ❌ **NO usa block.json** (debería para Gutenberg moderno)
- ❌ **Template incompleto** - NO renderiza metadata line preparada
- ❌ **CSS sin uso** - ~40% de estilos NO usados
- ❌ **Duplicación con MetadataLine** - Deberían compartir código base

---

## 9. Comparación con MetadataLine

### Similitudes (Duplicación)

**Código Duplicado:**
1. **load_template()** - IDÉNTICO (100%)
2. **get_post_data()** - Lógica de metadata compartida (~60%)
3. **Fallbacks** - departure → origin, physical_difficulty → difficulty, etc.
4. **Template lógica** - difficulty_labels, type_labels mappings
5. **Estructura de clase** - Mismo patrón

**Código Compartido que debería extraerse:**
```php
// Ambos bloques tienen esto:
'origin' => get_post_meta($post_id, 'departure', true) ?: get_post_meta($post_id, 'origin', true) ?: '',
'difficulty' => get_post_meta($post_id, 'physical_difficulty', true) ?: get_post_meta($post_id, 'difficulty', true) ?: '',
'type' => get_post_meta($post_id, 'service_type', true) ?: get_post_meta($post_id, 'type', true) ?: '',
```

### Diferencias

**ProductMetadata:**
- ✅ Tiene TripAdvisor rating data
- ✅ Tiene package title
- ✅ Tiene Schema.org markup
- ❌ NO renderiza metadata line (aunque prepara datos)
- ❌ Template incompleto

**MetadataLine:**
- ✅ Renderiza metadata line completa
- ✅ Tiene group_size y languages (de quick_facts)
- ✅ Template completo y funcional
- ❌ NO tiene TripAdvisor rating
- ❌ NO tiene package title

### Problema Arquitectónico

**❌ BLOQUES MAL DISEÑADOS:**

1. **ProductMetadata debería ser COMPOSICIÓN:**
   - Usar bloque TripAdvisor Rating (separado)
   - Usar bloque Package Title (separado)
   - Usar bloque MetadataLine (existente)
   - NO duplicar código

2. **O crear clase base compartida:**
   - AbstractPackageMetadata con lógica común
   - get_package_metadata() método compartido
   - Fallbacks centralizados

3. **Actualmente:**
   - ProductMetadata duplica código
   - Prepara datos que NO usa
   - Tiene CSS que NO usa
   - Template tiene lógica muerta

**Recomendación:** REFACTORIZAR completamente para eliminar duplicación

---

## 10. Recomendaciones de Refactorización

### Prioridad Alta

**1. Eliminar código muerto del template**
- **Acción:**
  - Eliminar difficulty_labels, type_labels mappings (NO usados)
  - Eliminar variables $difficulty_text, $type_text (NO usadas)
  - Eliminar extracción de origin, difficulty, duration, type (NO usados)
  ```php
  // ELIMINAR de template:
  // Lines 17-42 (label mappings NO usados)
  ```
- **Razón:** Código muerto confunde, aumenta mantenimiento
- **Riesgo:** NINGUNO - Código no se usa
- **Esfuerzo:** 15 min

**2. Eliminar CSS sin uso**
- **Acción:**
  - Eliminar `.product-metadata__meta-line` styles (~60 líneas)
  - Eliminar duration special design (~35 líneas)
  - Eliminar color variants (~30 líneas)
  - Mantener solo: container, title, rating badge, responsive (title), print, accessibility
  ```css
  /* ELIMINAR:
   * Lines 85-184 (metadata line styles)
   */
  ```
- **Razón:** ~40% del CSS NO se usa
- **Riesgo:** NINGUNO - Estilos no aplicados
- **Esfuerzo:** 20 min

**3. Eliminar metadata fields de get_post_data**
- **Acción:**
  ```php
  private function get_post_data(int $post_id): array
  {
      return [
          // TripAdvisor data
          'tripadvisor_rating' => floatval(get_post_meta($post_id, 'tripadvisor_rating', true)) ?: 0,
          'tripadvisor_url' => get_post_meta($post_id, 'tripadvisor_url', true) ?: '',
          'total_reviews' => intval(get_post_meta($post_id, 'total_reviews', true)) ?: 0,
          'show_rating_badge' => get_post_meta($post_id, 'show_rating_badge', true) !== '0',
          // ELIMINAR: origin, difficulty, duration, type (NO usados)
      ];
  }
  ```
- **Razón:** Datos preparados pero NO usados
- **Riesgo:** NINGUNO - Template no los usa
- **Esfuerzo:** 10 min

**4. Validar URL de TripAdvisor**
- **Acción:**
  ```php
  private function get_post_data(int $post_id): array
  {
      $tripadvisor_url = get_post_meta($post_id, 'tripadvisor_url', true) ?: '';

      // Validate TripAdvisor URL
      if ($tripadvisor_url && !filter_var($tripadvisor_url, FILTER_VALIDATE_URL)) {
          $tripadvisor_url = '';
      }

      // Optional: Validate it's actually a TripAdvisor URL
      if ($tripadvisor_url && strpos($tripadvisor_url, 'tripadvisor.com') === false) {
          $tripadvisor_url = '';
      }

      return [
          'tripadvisor_url' => esc_url($tripadvisor_url),
          // ...
      ];
  }
  ```
- **Razón:** Seguridad, prevenir XSS/phishing
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**5. Validar rating range**
- **Acción:**
  ```php
  $rating = floatval(get_post_meta($post_id, 'tripadvisor_rating', true));

  // Validate range 0-5
  if ($rating < 0 || $rating > 5) {
      $rating = 0;
  }

  return [
      'tripadvisor_rating' => $rating,
      // ...
  ];
  ```
- **Razón:** Data integrity, prevenir valores inválidos
- **Riesgo:** NINGUNO
- **Esfuerzo:** 10 min

### Prioridad Media

**6. Crear clase base compartida con MetadataLine**
- **Acción:** Extraer lógica común a AbstractPackageBlock o trait
  ```php
  abstract class AbstractPackageBlock
  {
      protected function load_template(string $template_name, array $data = []): void
      {
          // Shared logic
      }

      protected function get_package_metadata(int $post_id): array
      {
          return [
              'origin' => $this->get_with_fallback($post_id, ['departure', 'origin']),
              'difficulty' => $this->get_with_fallback($post_id, ['physical_difficulty', 'difficulty']),
              'type' => $this->get_with_fallback($post_id, ['service_type', 'type']),
          ];
      }

      private function get_with_fallback(int $post_id, array $keys): string
      {
          foreach ($keys as $key) {
              $value = get_post_meta($post_id, $key, true);
              if ($value) return sanitize_text_field($value);
          }
          return '';
      }
  }

  class ProductMetadata extends AbstractPackageBlock { }
  class MetadataLine extends AbstractPackageBlock { }
  ```
- **Razón:** DRY, eliminar duplicación
- **Riesgo:** MEDIO - Requiere refactorizar ambos bloques
- **Precauciones:** Testear ambos bloques después
- **Esfuerzo:** 3 horas

**7. Hacer show_tripadvisor configurable**
- **Acción:**
  ```php
  // En render():
  $show_tripadvisor = $attributes['showTripadvisor'] ?? true;
  $show_rating_badge = $attributes['showRatingBadge'] ?? true;

  $data = [
      // ...
      'show_tripadvisor' => $show_tripadvisor && $package_data['show_rating_badge'],
  ];
  ```
- **Razón:** Actualmente hardcoded, no configurable
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**8. Agregar itemReviewed a Schema.org**
- **Acción:**
  ```json
  {
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?php echo esc_js($package_title); ?>",
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "<?php echo esc_js($tripadvisor_rating); ?>",
      "bestRating": "5",
      "worstRating": "1",
      "ratingCount": "<?php echo esc_js($total_reviews); ?>"
    }
  }
  ```
- **Razón:** Schema.org markup incompleto
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**9. Heredar de BlockBase**
- **Acción:** `class ProductMetadata extends BlockBase`
- **Razón:** Consistencia, evita duplicación
- **Riesgo:** MEDIO - Requiere refactorizar
- **Precauciones:**
  - Mover config a properties
  - Usar parent::register()
  - Adaptar load_template()
- **Esfuerzo:** 2 horas

**10. Eliminar extract() de load_template**
- **Acción:**
  ```php
  protected function load_template(string $template_name, array $data = []): void
  {
      $template_path = TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php';

      if (!file_exists($template_path)) {
          // ... error handling ...
          return;
      }

      // Pass $data directly instead of extract
      include $template_path;
      // In template: use $data['key'] instead of $key
  }
  ```
- **Razón:** extract() es mala práctica, dificulta debugging
- **Riesgo:** MEDIO - Requiere actualizar template
- **Precauciones:** Actualizar template para usar $data array
- **Esfuerzo:** 1 hora

### Prioridad Baja

**11. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos
- **Razón:** Código sin documentación
- **Riesgo:** NINGUNO
- **Esfuerzo:** 45 min

**12. Sanitizar campos meta**
- **Acción:**
  ```php
  'tripadvisor_url' => esc_url(get_post_meta($post_id, 'tripadvisor_url', true)),
  'total_reviews' => max(0, intval(get_post_meta($post_id, 'total_reviews', true))),
  ```
- **Razón:** Seguridad, validación
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**13. Crear block.json**
- **Acción:** Migrar de register_block_type() a block.json
- **Razón:** Gutenberg moderno, mejor performance
- **Riesgo:** BAJO
- **Esfuerzo:** 45 min

**14. Mover package_title a get_post_data**
- **Acción:**
  ```php
  private function get_post_data(int $post_id): array
  {
      return [
          // ...
          'package_title' => get_the_title($post_id),
      ];
  }

  // En render(), eliminar:
  // 'package_title' => $is_preview ? '...' : get_the_title($post_id)
  ```
- **Razón:** Consistencia, todos los datos en un método
- **Riesgo:** NINGUNO
- **Esfuerzo:** 10 min

---

## 11. Plan de Acción

### Fase 1 - Alta Prioridad (Esta semana)
1. Eliminar código muerto del template (15 min)
2. Eliminar CSS sin uso (20 min)
3. Eliminar metadata fields de get_post_data (10 min)
4. Validar URL de TripAdvisor (20 min)
5. Validar rating range (10 min)

**Total Fase 1:** 1.25 horas

### Fase 2 - Media Prioridad (Próximas 2 semanas)
6. Crear clase base compartida con MetadataLine (3 horas)
7. Hacer show_tripadvisor configurable (30 min)
8. Agregar itemReviewed a Schema.org (20 min)
9. Heredar de BlockBase (2 horas)
10. Eliminar extract() (1 hora)

**Total Fase 2:** 6.75 horas

### Fase 3 - Baja Prioridad (Cuando haya tiempo)
11. Agregar DocBlocks (45 min)
12. Sanitizar campos meta (20 min)
13. Crear block.json (45 min)
14. Mover package_title a get_post_data (10 min)

**Total Fase 3:** 2 horas

**Total Refactorización Completa:** ~10 horas

**Precauciones Generales:**
- ⚠️ **CRÍTICO:** Refactorizar ProductMetadata Y MetadataLine juntos (eliminan duplicación)
- ✅ Crear AbstractPackageBlock para compartir código
- ✅ SIEMPRE validar URLs y ratings
- ⚠️ NO eliminar CSS sin verificar que template NO lo usa
- ⚠️ Testear Schema.org markup con Google Rich Results Test
- ⚠️ Mantener compatibilidad con campos legacy

---

## 12. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra preview data)
- [ ] Frontend funciona (muestra datos reales)
- [ ] TripAdvisor rating badge se muestra correctamente
- [ ] Package title se muestra correctamente

### TripAdvisor Rating
- [ ] Logo de TripAdvisor se muestra
- [ ] Estrellas se muestran correctamente (1-5)
- [ ] Reviews count se muestra con formato
- [ ] Link a TripAdvisor funciona (target="_blank", noopener)
- [ ] Rating 0 NO muestra badge (conditional rendering)
- [ ] show_rating_badge = false oculta badge

### Schema.org
- [ ] JSON-LD se genera correctamente
- [ ] NO se genera en preview mode
- [ ] NO se genera si rating = 0
- [ ] itemReviewed incluido (si se agregó)
- [ ] Pasa Google Rich Results Test

### Validación (si se agregó)
- [ ] URL de TripAdvisor validada (FILTER_VALIDATE_URL)
- [ ] URL inválida → badge NO se muestra
- [ ] Rating <0 o >5 → rating = 0
- [ ] Reviews negativos → 0

### CSS
- [ ] Estilos de container aplicados
- [ ] Package title responsive funciona
- [ ] Rating badge styles correctos
- [ ] Print styles funcionan
- [ ] Accessibility (focus-visible) funciona
- [ ] High contrast mode funciona
- [ ] Código CSS sin uso eliminado (si se hizo)

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] NO usa extract() (si se eliminó)
- [ ] Código compartido con MetadataLine extraído (si se hizo)
- [ ] AbstractPackageBlock creado (si se hizo)
- [ ] show_tripadvisor configurable (si se agregó)
- [ ] block.json (si se creó)

### Seguridad
- [ ] URL de TripAdvisor validada
- [ ] Rating range validado
- [ ] Template escapa todo (esc_html, esc_attr, esc_url, esc_js)
- [ ] IconHelper escapa SVG

### Clean Code
- [ ] Métodos <50 líneas ✅ (ya cumple)
- [ ] Anidación <3 niveles ✅ (ya cumple)
- [ ] Código muerto eliminado (template y CSS)
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] NO duplicación con MetadataLine (si se refactorizó)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Código simple y limpio (192 líneas)
- ✅ Todos los métodos <50 líneas
- ✅ TripAdvisor rating badge funcional
- ✅ Schema.org structured data
- ✅ Preview mode funciona
- ✅ Package title responsive
- ❌ **Template incompleto** - NO renderiza metadata line preparada
- ❌ **~40% CSS sin uso** - Metadata line styles NO aplicados
- ❌ **Código muerto en template** - Labels mappings NO usados
- ❌ **Duplicación ALTA con MetadataLine** - load_template, fallbacks, lógica compartida
- ❌ **Propósito confuso** - Nombre dice "metadata" pero solo muestra rating
- ❌ **NO valida URL** de TripAdvisor
- ❌ **NO valida rating range**
- ❌ **NO hereda de BlockBase**
- ❌ Documentación mínima (17% de métodos)
- ⚠️ extract() en load_template
- ⚠️ show_tripadvisor, show_metadata hardcoded

### Puntuación: 5.5/10

**Razones para la puntuación:**
- ➕ Código bien dimensionado (+1.5)
- ➕ TripAdvisor badge funciona (+1.5)
- ➕ Schema.org markup (+0.5)
- ➕ Preview mode (+0.5)
- ➕ Error handling (+0.5)
- ➕ Accessibility en CSS (+0.5)
- ➕ Responsive design (+0.5)
- ➖ Template incompleto (-1.5)
- ➖ ~40% CSS sin uso (-1)
- ➖ Código muerto en template (-0.8)
- ➖ Duplicación ALTA con MetadataLine (-1.5)
- ➖ NO valida URL/rating (-0.8)
- ➖ NO hereda BlockBase (-0.5)
- ➖ Sin DocBlocks (-0.4)
- ➖ extract() usado (-0.3)
- ➖ Propósito confuso (-0.5)

### Fortalezas
1. **TripAdvisor badge:** Logo, estrellas, reviews, link funcionan correctamente
2. **Schema.org:** Structured data para SEO
3. **Código simple:** Métodos pequeños, clara estructura
4. **Preview mode:** Funciona para editor
5. **Package title:** H1 responsive, bien diseñado
6. **Error handling:** Try-catch, empty states
7. **Accessibility:** aria-label, focus-visible, high contrast
8. **Translation ready:** __() en todos los strings
9. **Responsive typography:** Package title adapta a mobile
10. **Conditional rendering:** Badge solo si rating > 0

### Debilidades
1. ❌ **Template incompleto** - Prepara metadata pero NO la renderiza
2. ❌ **~40% CSS sin uso** - Metadata line styles NO aplicados
3. ❌ **Código muerto** - Labels mappings en template NO usados
4. ❌ **Duplicación ALTA** - load_template, fallbacks idénticos a MetadataLine
5. ❌ **NO valida URL** - Acepta cualquier string sin validación
6. ❌ **NO valida rating** - Acepta cualquier float (<0, >5)
7. ❌ **NO hereda BlockBase** - Inconsistente con mejores bloques
8. ❌ **Propósito confuso** - Nombre dice "metadata" pero solo muestra rating
9. ❌ **show_tripadvisor hardcoded** - No configurable
10. ❌ **NO documenta** - 1/6 métodos con DocBlocks (17%)
11. ⚠️ **extract() usado** - Mala práctica
12. ⚠️ **Schema.org incompleto** - Falta itemReviewed
13. ⚠️ **package_title fuera de get_post_data** - Inconsistente
14. ⚠️ **NO sanitiza campos** - Usa valores directamente

### Recomendación Principal

**Este bloque tiene PROBLEMAS ARQUITECTÓNICOS GRAVES y código SIN USO.**

**Prioridad CRÍTICA (Esta semana - 1.25 horas):**
1. Eliminar código muerto (template, CSS, get_post_data)
2. Validar URL de TripAdvisor
3. Validar rating range

**Prioridad Alta (2 semanas - 6.75 horas):**
4. Crear AbstractPackageBlock compartida con MetadataLine
5. Heredar de BlockBase
6. Hacer show_tripadvisor configurable
7. Agregar itemReviewed a Schema.org
8. Eliminar extract()

**Prioridad Media (Cuando haya tiempo - 2 horas):**
9. DocBlocks completos
10. Sanitizar campos
11. block.json
12. Mover package_title a get_post_data

**Esfuerzo total:** ~10 horas de refactorización

**Veredicto:** Este bloque tiene un **DISEÑO CONFUSO** - prepara metadata que NO usa, tiene CSS que NO aplica, y duplica código de MetadataLine. El nombre "ProductMetadata" promete más de lo que entrega (solo muestra rating de TripAdvisor, NO metadata completa). El código es simple y funciona, pero tiene **MUCHO código sin uso** y **ALTA duplicación**. **ACCIÓN REQUERIDA: Eliminar código muerto esta semana, luego refactorizar arquitectura para compartir código con MetadataLine.**

### Duplicación con MetadataLine

**Código Duplicado:**
1. ✅ `load_template()` - **100% IDÉNTICO** (17 líneas)
2. ✅ Fallbacks de metadata - **100% IDÉNTICO** (departure→origin, etc.)
3. ✅ Lógica de labels en template - **100% IDÉNTICO** (difficulty_labels, type_labels)

**Código Compartido que debería extraerse:**
- Método `get_with_fallback()` para campos meta
- Método `load_template()` a clase base o helper
- Labels mappings a constantes compartidas
- Lógica de metadata a método compartido

**Estimación de duplicación:** ~40% del código PHP duplicado

**Acción requerida:** Crear AbstractPackageBlock o PackageMetadataHelper

### Dependencias Identificadas

**Helpers Internos:**
- EditorHelper (detectar preview mode)
- IconHelper (renderizar icono star)

**Post Meta:**
- `tripadvisor_rating` (float)
- `tripadvisor_url` (string)
- `total_reviews` (int)
- `show_rating_badge` (bool/string)
- `departure`, `origin` (con fallback) - ⚠️ NO usado
- `physical_difficulty`, `difficulty` (con fallback) - ⚠️ NO usado
- `duration`, `days` (con fallback) - ⚠️ NO usado
- `service_type`, `type` (con fallback) - ⚠️ NO usado

**CSS:**
- product-metadata.css (260 líneas, ~40% sin uso)
- Theme.json integration (color variables)
- Responsive design

**Externos:**
- TripAdvisor logo SVG (hardcoded)
- Schema.org (JSON-LD structured data)

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** ALTA - Eliminar código muerto (crítico), validar datos (crítico), refactorizar arquitectura (alta)
**Próxima revisión:** Después de limpieza de código muerto y validaciones
