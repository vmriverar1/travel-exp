# Auditoría: TrustBadges (Package)

**Fecha:** 2025-11-09
**Bloque:** 21/XX Package
**Tiempo:** 40 min
**⚠️ ESTADO:** CRÍTICO - Inconsistencia grave entre PHP y template
**⚠️ NOTA IMPORTANTE:** Template espera estructura de datos completamente diferente

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/trust-badges`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Icon:** `shield-alt`
- **Category:** `template-blocks`
- **Keywords:** trust, badges, certifications, awards

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **Usa template separado** ✅ (trust-badges.php)
- **⚠️ INCONSISTENCIA CRÍTICA:** Estructura de datos incompatible entre PHP y template
- **Meta key:** `trust_badges` (array de badges)
- **Lógica de fallback:** get_default_badges() si NO hay meta

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** ✅ Usa is_editor_mode() correctamente
- **IconHelper:** ⚠️ Template lo usa pero PHP NO prepara icon correctamente
- **Template:** trust-badges.php (49 líneas - ⚠️ INCOMPATIBLE con PHP)
- **CSS:** trust-badges.css (176 líneas - múltiples layouts y tamaños)

### ⚠️ IMPORTANTE - INCONSISTENCIA ESTRUCTURA DE DATOS

**ACLARACIÓN CRÍTICA:** El bloque tiene una **inconsistencia GRAVE** de estructura de datos:

**PHP genera badges con:**
```php
$badges[] = [
    'icon' => 'shield-alt',      // string - icono dashicon
    'label' => 'ATOL Protected', // string - texto del badge
    'image' => '',               // string - URL de imagen
];
```

**Template espera badges con:**
```php
$badge['badge_type']    // 'image' o 'icon' - NO se envía
$badge['title']         // string - NO se envía (usa 'label')
$badge['description']   // string - NO se envía
$badge['icon']          // string - OK
$badge['image']['sizes']['thumbnail'] // array - NO se envía (solo URL string)
$badge['image']['url']  // string - OK pero dentro de array
```

**RESULTADO:** ⛔ **El template NO va a renderizar correctamente** porque:
- Usa `$badge['badge_type']` que NO existe (línea 23)
- Usa `$badge['title']` en lugar de `$badge['label']` (líneas 26, 37)
- Usa `$badge['image']['sizes']['thumbnail']` cuando solo hay string (línea 25)
- Usa `$show_descriptions` que NO se pasa (línea 38)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/TrustBadges.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** ✅ `/templates/trust-badges.php` (49 líneas - ⚠️ INCOMPATIBLE con PHP)
**Assets:**
- CSS: `/assets/blocks/trust-badges.css` (176 líneas - múltiples layouts y tamaños)
- JS: ❌ NO tiene JavaScript

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- ✅ EditorHelper::is_editor_mode() (correctamente usado)
- ⚠️ IconHelper (template lo usa pero PHP NO lo prepara)
- WordPress get_post_meta() (trust_badges)
- WordPress wp_get_attachment_image_url() (para IDs de imagen)

**Líneas de Código:**
- **Clase PHP:** 126 líneas
- **Template:** 49 líneas
- **JavaScript:** 0 líneas
- **CSS:** 176 líneas
- **TOTAL:** 351 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Bloque para mostrar badges de confianza y certificaciones (ATOL, TripAdvisor, Awards, ABTA). Diseñado para reforzar credibilidad en páginas de paquetes.

**Funcionalidad Principal:**
1. **Obtención de badges:**
   - Lee meta `trust_badges` del post
   - Valida formato (array)
   - Normaliza estructura (icon, label, image)
   - Fallback a get_default_badges() si vacío

2. **Datos de cada badge:**
   - icon: Dashicon name (ej: 'shield-alt')
   - label: Texto del badge (ej: 'ATOL Protected')
   - image: URL de imagen (si existe)
   - Soporta image ID (lo convierte a URL)
   - Soporta strings directos (legacy)

3. **Preview mode:**
   - Muestra 4 badges hardcoded de ejemplo
   - Ejemplos: ATOL Protected, TripAdvisor 5★, Best Tour Operator 2024, ABTA Member
   - NO usa datos reales en editor

4. **Template rendering:**
   - Usa load_template() con extract()
   - ⚠️ **PROBLEMA:** Estructura de datos incompatible con template

5. **CSS avanzado:**
   - 3 layouts: horizontal, grid, vertical
   - 3 tamaños: small, medium, large
   - 3 alineaciones: left, center, right
   - Soporta icons y custom images
   - Responsive con media queries

**Inputs (meta field):**
- Meta key: `trust_badges`
- Formato esperado: Array de arrays
- Cada badge puede ser:
  - Array con icon/label/image
  - String (se convierte a badge simple)

**Outputs:**
- Lista de badges con:
  - Icono SVG (via IconHelper) O
  - Imagen custom (thumbnail size)
  - Título del badge
  - Descripción opcional (⚠️ NO se usa)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'trust-badges';
private string $title = 'Trust Badges';
private string $description = 'Trust badges and certifications';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (14 líneas)
2. enqueue_assets(): void - Encola CSS (6 líneas)
3. render($attributes, $content, $block): string - Renderiza (23 líneas)
```

**Métodos Privados:**
```php
4. get_preview_data(): array - Datos de preview (9 líneas)
5. get_post_data(int $post_id): array - Lee meta y normaliza (36 líneas)
6. get_default_badges(): array - Badges por defecto (9 líneas)
```

**Métodos Protegidos:**
```php
7. load_template(string $template_name, array $data = []): void - Carga template (10 líneas)
```

**Total:** 7 métodos, 126 líneas

**Métodos más largos:**
1. ⚠️ `get_post_data()` - **36 líneas** (aceptable)
2. ✅ `render()` - **23 líneas** (excelente)
3. ✅ `register()` - **14 líneas** (excelente)
4. ✅ `load_template()` - **10 líneas** (excelente)

**Observación:** ✅ TODOS los métodos <60 líneas (excelente)

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/trust-badges`
- api_version: 2
- category: `template-blocks`
- icon: `shield-alt`
- keywords: ['trust', 'badges', 'certifications', 'awards']
- supports: anchor: true, html: false
- render_callback: `[$this, 'render']`

**Enqueue Assets:**
- CSS: `/assets/blocks/trust-badges.css` (sin condiciones)
- Hook: `enqueue_block_assets`
- ⚠️ **NO hay conditional loading** - CSS se carga siempre (incluso en páginas sin el bloque)

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Attributes:** ❌ **NO DEFINE ATTRIBUTES** - Todo hardcoded/meta

---

## 5. Campos Meta

**Definición:** Meta field personalizado (NO ACF)

**Meta Key:** `trust_badges`

**Formato Esperado:**
```php
// Array de badges
[
    [
        'icon' => 'shield-alt',        // Dashicon name
        'label' => 'ATOL Protected',   // Texto del badge
        'text' => 'ATOL Protected',    // Legacy fallback
        'image' => 123,                // Image ID o URL
    ],
    // o simplemente:
    'Certified Operator', // String → se convierte a badge simple
]
```

**Validación:**
- ✅ is_array() check en badges_raw
- ✅ empty() check antes de procesar
- ✅ Fallback a get_default_badges() si vacío
- ✅ Soporta formato legacy (text en lugar de label)
- ✅ Soporta strings directos (array items)
- ✅ Convierte image ID a URL con wp_get_attachment_image_url()
- ✅ is_numeric() check para detectar IDs

**Normalización:**
- String → Array con icon: 'shield-alt', label: string, image: ''
- Array sin label → Usa 'text' como fallback
- Image ID → Convierte a URL (size: 'medium')
- Image URL → Se mantiene como está

**Debería tener attributes para:**
- Layout (horizontal, grid, vertical)
- Tamaño de badges (small, medium, large)
- Alineación (left, center, right)
- Mostrar/ocultar descripciones
- Título de sección

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Try-catch wrapper (líneas 37-56)
2. Get post_id con get_the_ID() (línea 38)
3. Check preview mode con EditorHelper::is_editor_mode() (línea 39)
4. Si preview: get_preview_data() (línea 41)
5. Si NO preview: get_post_data($post_id) (línea 41)
6. Early return si empty badges (línea 42)
7. Generate block_id con uniqid() (línea 45)
8. Append className si existe (línea 46)
9. Build $data array (líneas 44-49)
10. Output con ob_start/load_template/ob_get_clean (líneas 51-53)
11. Catch exceptions con mensaje de error en WP_DEBUG (líneas 54-56)

**Flujo de Datos:**
```
render()
  → EditorHelper::is_editor_mode()?
    → YES: get_preview_data()
      → return 4 hardcoded preview badges
    → NO: get_post_data($post_id)
      → get_post_meta($post_id, 'trust_badges', true)
      → is_array check
      → Loop: normalizar cada badge
        → Array: extract icon/label/text/image
        → String: convert to array
        → Image ID: wp_get_attachment_image_url()
      → Fallback: get_default_badges() si vacío
      → return badges array
  → empty check on badges
  → load_template('trust-badges', $data)
    → extract($data) - Solo 4 variables
    → include template - ⚠️ Template espera estructura diferente
```

**Variables al Template:**
```php
$block_id = 'trust-badges-abc123'; // string ✅
$class_name = 'trust-badges custom-class'; // string ✅
$badges = [/* array de badges */]; // array ✅ pero estructura incompatible
$is_preview = false; // bool ✅

// ⚠️ FALTAN (template las espera):
$section_title // NO definida - línea 14, 15
$show_descriptions // NO definida - línea 38

// ⚠️ ESTRUCTURA INCOMPATIBLE:
// PHP usa: ['icon' => ..., 'label' => ..., 'image' => '...']
// Template espera: ['badge_type' => ..., 'title' => ..., 'image' => ['sizes' => [...], 'url' => ...]]
```

**⚠️ PROBLEMA CRÍTICO:** El template NO va a renderizar correctamente porque:
1. Usa `$badge['badge_type']` que NO existe (línea 23)
2. Usa `$badge['title']` en lugar de `$badge['label']` (líneas 26, 37)
3. Usa `$badge['image']['sizes']['thumbnail']` cuando solo hay URL string (línea 25)
4. Usa `$show_descriptions` que NO se pasa (línea 38)
5. Usa `$section_title` que NO se pasa (líneas 14-15)

**Manejo de Errores:**
- ✅ Try-catch wrapper en render()
- ✅ WP_DEBUG check antes de mostrar error
- ✅ Escapado de error con esc_html()
- ✅ Return empty string si error y NO WP_DEBUG
- ✅ File exists check en load_template()
- ✅ Empty check en badges antes de renderizar

---

## 7. Funcionalidades Adicionales

### 7.1 Normalización de Badges

**Método:** `get_post_data()` (líneas 69-104)

**Funcionalidad:**
```php
// 1. Get meta
$badges_raw = get_post_meta($post_id, 'trust_badges', true);

// 2. Validación
if (!is_array($badges_raw) || empty($badges_raw)) {
    return $this->get_default_badges();
}

// 3. Loop y normalización
foreach ($badges_raw as $badge) {
    if (is_array($badge)) {
        // Array: extraer icon, label, text, image
        $image_url = '';
        if (!empty($badge['image'])) {
            if (is_numeric($badge['image'])) {
                // Image ID → URL
                $image_url = wp_get_attachment_image_url($badge['image'], 'medium');
            } else {
                // Ya es URL
                $image_url = $badge['image'];
            }
        }

        $badges[] = [
            'icon' => $badge['icon'] ?? 'shield-alt',
            'label' => $badge['label'] ?? $badge['text'] ?? '',
            'image' => $image_url,
        ];
    } elseif (is_string($badge)) {
        // String directo → badge simple
        $badges[] = [
            'icon' => 'shield-alt',
            'label' => $badge,
            'image' => '',
        ];
    }
}

// 4. Fallback si vacío después de normalizar
return !empty($badges) ? $badges : $this->get_default_badges();
```

**Características:**
- ✅ is_array() check en badges_raw
- ✅ empty() check antes de procesar
- ✅ Soporta formato legacy (text en lugar de label)
- ✅ Soporta strings directos en array
- ✅ Convierte image ID a URL con wp_get_attachment_image_url()
- ✅ is_numeric() check para detectar IDs
- ✅ Nullish coalescing (??) para defaults
- ✅ Doble fallback: label → text → ''
- ✅ Fallback final a get_default_badges()
- ⚠️ **NO valida** que image URL sea válida
- ⚠️ **Image size hardcoded** ('medium')

**Calidad:** 8/10 - Muy robusto con múltiples fallbacks

**Problemas:**
1. ⚠️ Image size hardcoded ('medium') - debería ser configurable
2. ⚠️ NO valida URLs de imagen
3. ⚠️ **Estructura incompatible con template** (mayor problema)

### 7.2 Default Badges

**Método:** `get_default_badges()` (líneas 106-113)

**Funcionalidad:**
```php
return [
    ['icon' => 'shield-alt', 'label' => __('Secure Booking', 'travel-blocks'), 'image' => ''],
    ['icon' => 'star-filled', 'label' => __('Top Rated', 'travel-blocks'), 'image' => ''],
    ['icon' => 'yes-alt', 'label' => __('Certified Operator', 'travel-blocks'), 'image' => ''],
];
```

**Características:**
- ✅ Traducibles con __()
- ✅ 3 badges genéricos
- ✅ Estructura idéntica a get_post_data()
- ✅ Icons variados (shield-alt, star-filled, yes-alt)
- ✅ Textos descriptivos

**Calidad:** 9/10 - Excelente defaults

### 7.3 Preview Data

**Método:** `get_preview_data()` (líneas 59-67)

**Funcionalidad:**
- Retorna 4 badges hardcoded de ejemplo
- Ejemplos realistas de industria de viajes:
  1. ATOL Protected (shield-alt)
  2. TripAdvisor 5★ (star-filled)
  3. Best Tour Operator 2024 (awards)
  4. ABTA Member (shield)
- Todos con icon, label, image vacío

**Características:**
- ✅ Datos muy realistas
- ✅ Estructura idéntica a get_post_data()
- ✅ Icons variados
- ✅ Textos descriptivos
- ✅ NO usa __() (correcto para preview)

**Calidad:** 9/10 - Excelente preview data

### 7.4 Template Loading

**Método:** `load_template()` (líneas 115-124)

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

**Observación:** ✅ Correcto - No necesita JS

### 7.6 CSS

**Archivo:** `/assets/blocks/trust-badges.css` (176 líneas)

**Características:**
- ✅ 3 layouts: horizontal, grid, vertical
- ✅ 3 tamaños: small (32px), medium (48px), large (64px)
- ✅ 3 alineaciones: left, center, right
- ✅ Soporta icons SVG y custom images
- ✅ Responsive breakpoint: 767px (mobile → 1 col)
- ✅ CSS variables (var(--color-gray-900), var(--border-radius-md))
- ✅ Grid responsive con auto-fit
- ✅ Flexbox para layouts horizontal/vertical
- ✅ Box-shadow en layout grid
- ✅ Padding/gap consistentes

**Organización:**
- Secciones claras: base, title, layouts, items, content, alignment, responsive
- Comentarios descriptivos
- Mobile-first approach

**Calidad:** 9/10 - Muy completo y bien organizado

**Problemas menores:**
- ⚠️ Algunos colores hardcoded (#212121, #757575) - deberían usar variables
- ⚠️ Selectores `.trust-badges--horizontal` pero NO se aplican desde PHP (NO hay attributes)

### 7.7 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.8 Dependencias Externas

- WordPress get_post_meta() (trust_badges)
- WordPress wp_get_attachment_image_url() (image ID → URL)
- EditorHelper::is_editor_mode() ✅
- IconHelper (⚠️ template lo usa pero PHP NO lo prepara correctamente)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ **CUMPLE**
- Clase tiene una responsabilidad clara: mostrar trust badges
- Métodos bien enfocados
- NO hay complejidad excesiva
- **Impacto:** NINGUNO

**OCP:** ⚠️ **VIOLA**
- NO hereda de BlockBase → Difícil extender
- Layouts/tamaños hardcoded en CSS (NO configurables vía PHP)
- **Impacto:** MEDIO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con mejores bloques
- **Impacto:** MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Acoplado directamente a:
  - Meta key 'trust_badges' hardcoded
  - Estructura específica de template (incompatible)
  - Image size 'medium' hardcoded
- No hay abstracción/interfaces
- **Impacto:** MEDIO

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **Método más largo: 36 líneas** (excelente)
- ✅ Complejidad ciclomática baja
- ✅ Métodos cortos y enfocados

**Anidación:**
- ✅ **Máximo 3 niveles** de anidación (aceptable)
- ✅ Código legible

**Duplicación:**
- ✅ **NO hay duplicación significativa**
- ✅ Preview/post/default data tienen estructura idéntica (correcto)

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ✅ Propiedades claras

**Código Sin Uso:**
- ✅ No detectado

**DocBlocks:**
- ❌ **0/7 métodos documentados** (0%)
- ❌ Header de archivo básico
- ❌ NO documenta params/return types
- **Impacto:** MEDIO

**Magic Values:**
- ⚠️ 'trust_badges' hardcoded (debería ser constante)
- ⚠️ 'medium' hardcoded (size - debería ser constante)
- ⚠️ 'shield-alt' hardcoded (default icon - debería ser constante)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ get_post_meta() es seguro
- ✅ wp_get_attachment_image_url() es seguro
- ✅ NO hay inputs de usuario directos
- **Impacto:** NINGUNO - Perfecto

**Escapado:**
- ⚠️ **Template usa escapado** pero variables NO se pasan correctamente
- ✅ esc_attr(), esc_url(), esc_html() presentes en template
- ⚠️ **Warnings** por variables undefined
- **Impacto:** MEDIO - Template tiene warnings

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ✅ Usa get_post_meta() (no queries directas)

**XSS:**
- ✅ Template tiene escapado correcto (cuando variables existen)
- ⚠️ **Problema:** Variables undefined pueden causar warnings

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ✅ **Template separado** (trust-badges.php)
- ⚠️ **Template incompatible** con estructura de datos de la clase
- ✅ Lógica de negocio en clase
- ✅ Estilos en CSS separado

**Acoplamiento:**
- ⚠️ **Acoplamiento medio** - Meta key hardcoded, image size hardcoded
- ⚠️ Template espera estructura diferente (alto acoplamiento incorrecto)
- **Impacto:** ALTO

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Pierde funcionalidades compartidas
- **Impacto:** MEDIO

**Caché:**
- ✅ N/A - get_post_meta() tiene object cache propio

**Otros:**
- ❌ **NO usa block.json** (debería para Gutenberg moderno)
- ✅ **Usa EditorHelper** correctamente
- ❌ **NO tiene attributes** (layouts/tamaños hardcoded en CSS)

---

## 9. Recomendaciones de Refactorización

### Prioridad CRÍTICA

**1. ⛔ ARREGLAR INCOMPATIBILIDAD PHP ↔ TEMPLATE**
- **Acción:**
  ```php
  // OPCIÓN A: Cambiar PHP para que genere estructura que template espera
  foreach ($badges_raw as $badge) {
      if (is_array($badge)) {
          $image_data = null;
          if (!empty($badge['image'])) {
              if (is_numeric($badge['image'])) {
                  $image_id = $badge['image'];
                  $image_data = [
                      'sizes' => [
                          'thumbnail' => wp_get_attachment_image_url($image_id, 'thumbnail'),
                      ],
                      'url' => wp_get_attachment_image_url($image_id, 'medium'),
                  ];
              } else {
                  $image_data = [
                      'sizes' => ['thumbnail' => $badge['image']],
                      'url' => $badge['image'],
                  ];
              }
          }

          $badges[] = [
              'badge_type' => !empty($image_data) ? 'image' : 'icon',
              'title' => $badge['label'] ?? $badge['text'] ?? '',
              'description' => $badge['description'] ?? '',
              'icon' => $badge['icon'] ?? 'shield',
              'image' => $image_data,
          ];
      }
  }

  // Y en render() agregar:
  $data = [
      'block_id' => 'trust-badges-' . uniqid(),
      'class_name' => 'trust-badges' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
      'badges' => $badges,
      'section_title' => '', // Agregar (vacío = no mostrar)
      'show_descriptions' => false, // Agregar
      'is_preview' => $is_preview,
  ];

  // OPCIÓN B: Cambiar template para usar estructura actual de PHP
  // (Más simple y menos riesgoso)
  ```
- **Razón:** ⛔ **CRÍTICO** - Template NO renderiza correctamente ahora
- **Riesgo:** MEDIO - Opción A requiere cambiar lógica, Opción B solo template
- **Recomendación:** Usar Opción B (cambiar template)
- **Esfuerzo:** 45 min

**2. ⛔ AGREGAR VARIABLES FALTANTES AL TEMPLATE**
- **Acción:**
  ```php
  // En render() - líneas 44-49:
  $data = [
      'block_id' => 'trust-badges-' . uniqid(),
      'class_name' => 'trust-badges' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
      'badges' => $badges,
      'section_title' => '', // Agregar (vacío = no mostrar)
      'show_descriptions' => false, // Agregar
      'is_preview' => $is_preview,
  ];
  ```
- **Razón:** ⛔ **CRÍTICO** - Template usa variables undefined
- **Riesgo:** BAJO - Solo agregar variables con defaults
- **Esfuerzo:** 15 min

### Prioridad Alta

**3. Heredar de BlockBase**
- **Acción:** `class TrustBadges extends BlockBase`
- **Razón:** Consistencia, funcionalidades compartidas
- **Riesgo:** MEDIO - Requiere refactorizar
- **Esfuerzo:** 1 hora

**4. Agregar Block Attributes**
- **Acción:**
  ```php
  // En register():
  'attributes' => [
      'layout' => ['type' => 'string', 'default' => 'horizontal'], // horizontal, grid, vertical
      'size' => ['type' => 'string', 'default' => 'medium'], // small, medium, large
      'alignment' => ['type' => 'string', 'default' => 'center'], // left, center, right
      'showDescriptions' => ['type' => 'boolean', 'default' => false],
      'sectionTitle' => ['type' => 'string', 'default' => ''],
  ],

  // Aplicar en render():
  $layout = $attributes['layout'] ?? 'horizontal';
  $size = $attributes['size'] ?? 'medium';
  $alignment = $attributes['alignment'] ?? 'center';

  $class_name = "trust-badges trust-badges--{$layout} trust-badges--{$size} trust-badges--align-{$alignment}";
  ```
- **Razón:** Hacer layouts/tamaños configurables (ahora CSS existe pero NO se aplica)
- **Riesgo:** BAJO
- **Esfuerzo:** 1.5 horas (incluye actualizar render() y crear inspector controls)

**5. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con params, returns, description
- **Razón:** Documentación para mantenimiento
- **Riesgo:** NINGUNO
- **Esfuerzo:** 30 min

### Prioridad Media

**6. Convertir hardcoded values a constantes**
- **Acción:**
  ```php
  private const META_KEY = 'trust_badges';
  private const IMAGE_SIZE = 'medium';
  private const IMAGE_SIZE_THUMB = 'thumbnail';
  private const DEFAULT_ICON = 'shield-alt';

  // Uso:
  $badges_raw = get_post_meta($post_id, self::META_KEY, true);
  $image_url = wp_get_attachment_image_url($image_id, self::IMAGE_SIZE);
  ```
- **Razón:** Mantenibilidad, fácil cambiar en un solo lugar
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**7. Validación de Image URLs**
- **Acción:**
  ```php
  if (!empty($badge['image']) && !is_numeric($badge['image'])) {
      // Validar que sea URL válida
      if (filter_var($badge['image'], FILTER_VALIDATE_URL)) {
          $image_url = $badge['image'];
      }
  }
  ```
- **Razón:** Seguridad, evitar URLs maliciosas
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**8. Conditional CSS loading**
- **Acción:**
  ```php
  public function enqueue_assets(): void
  {
      if (!is_admin() && has_block('travel-blocks/trust-badges')) {
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

**10. Agregar soporte para descripciones**
- **Acción:**
  ```php
  // En get_post_data():
  $badges[] = [
      'icon' => $badge['icon'] ?? 'shield-alt',
      'label' => $badge['label'] ?? $badge['text'] ?? '',
      'description' => $badge['description'] ?? '', // Agregar
      'image' => $image_url,
  ];

  // En render():
  $data['show_descriptions'] = $attributes['showDescriptions'] ?? false;
  ```
- **Razón:** Aprovechar campo description que template ya soporta
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

### Fase 0 - CRÍTICO (Esta semana)
1. ⛔ **Arreglar incompatibilidad PHP ↔ Template** (45 min) - Cambiar template para usar estructura actual
2. ⛔ **Agregar variables faltantes** (15 min) - section_title, show_descriptions

**Total Fase 0:** 1 hora

### Fase 1 - Alta Prioridad (Esta semana)
3. Heredar de BlockBase (1 hora)
4. Agregar Block Attributes (1.5 horas)
5. Agregar DocBlocks (30 min)

**Total Fase 1:** 3 horas

### Fase 2 - Media Prioridad (Próximas 2 semanas)
6. Convertir hardcoded a constantes (20 min)
7. Validación de Image URLs (15 min)
8. Conditional CSS loading (15 min)

**Total Fase 2:** 50 min

### Fase 3 - Baja Prioridad (Cuando haya tiempo)
9. Crear block.json (45 min)
10. Agregar soporte para descripciones (30 min)

**Total Fase 3:** 1 hora 15 min

**Total Refactorización Completa:** ~6 horas 5 min

**Precauciones Generales:**
- ⛔ **MUY IMPORTANTE:** Primero arreglar incompatibilidad template (Fase 0.1)
- ⛔ **MUY IMPORTANTE:** Agregar variables faltantes (Fase 0.2)
- ⚠️ **NO cambiar** estructura de meta 'trust_badges' sin consultar (podría romper datos existentes)
- ✅ SIEMPRE probar con badges reales después de cambios
- ✅ Verificar que icons SVG funcionan correctamente
- ✅ Probar con images personalizadas (URLs e IDs)

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra 4 badges hardcoded)
- [ ] Frontend funciona (muestra badges desde meta)
- [ ] ⛔ **NO hay PHP warnings** por variables undefined

### Obtención de Badges
- [ ] Lee meta 'trust_badges' correctamente
- [ ] Normaliza arrays con icon/label/image
- [ ] Soporta strings directos (legacy)
- [ ] Soporta formato legacy (text en lugar de label)
- [ ] Convierte image IDs a URLs
- [ ] Mantiene image URLs directas
- [ ] Fallback a get_default_badges() funciona
- [ ] Empty check funciona

### Template
- [ ] load_template() carga correctamente
- [ ] extract() crea todas las variables necesarias
- [ ] ⛔ **Todas las variables esperadas están disponibles**
- [ ] section_title se muestra si existe (línea 14-15)
- [ ] Badges se muestran correctamente (línea 19)
- [ ] badge_type funciona (icon vs image) (línea 23)
- [ ] Icons SVG se muestran (línea 31 - vía IconHelper)
- [ ] Custom images se muestran (línea 24-28)
- [ ] Títulos se muestran correctamente (línea 37)
- [ ] show_descriptions funciona (línea 38)

### CSS
- [ ] Estilos se aplican correctamente
- [ ] Layout horizontal funciona
- [ ] Layout grid funciona (con box-shadow)
- [ ] Layout vertical funciona
- [ ] Tamaños funcionan (small: 32px, medium: 48px, large: 64px)
- [ ] Alineaciones funcionan (left, center, right)
- [ ] Icons SVG tienen tamaño correcto
- [ ] Custom images tienen height correcto
- [ ] Responsive funciona (767px → 1 col)
- [ ] Conditional loading funciona (si se agregó)

### Attributes (si se agregaron)
- [ ] layout attribute funciona
- [ ] size attribute funciona
- [ ] alignment attribute funciona
- [ ] showDescriptions attribute funciona
- [ ] sectionTitle attribute funciona
- [ ] Classes se aplican correctamente según attributes

### Clean Code
- [ ] Métodos <60 líneas ✅ (ya cumple)
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] Constantes en lugar de magic values (si se cambiaron)

### Performance
- [ ] CSS solo se carga donde se necesita (si se agregó conditional)
- [ ] Image size optimizado (medium/thumbnail)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Código PHP bien estructurado (126 líneas)
- ✅ Normalización robusta con múltiples fallbacks
- ✅ Soporta formato legacy (text, strings directos)
- ✅ Convierte image IDs a URLs automáticamente
- ✅ Preview data muy realista (4 badges de viajes)
- ✅ CSS completo con 3 layouts, 3 tamaños, 3 alineaciones (176 líneas)
- ✅ Usa EditorHelper correctamente
- ✅ Try-catch wrapper en render()
- ✅ Empty checks en lugares correctos
- ⛔ **INCOMPATIBILIDAD CRÍTICA: Estructura de datos PHP ≠ Template**
- ⛔ **Variables undefined en template** (section_title, show_descriptions)
- ❌ NO hereda de BlockBase
- ❌ NO tiene attributes (layouts/tamaños en CSS pero NO configurables)
- ❌ NO tiene DocBlocks (0/7 métodos)

### Puntuación: 6.5/10

**Razones para la puntuación:**
- ➕ Normalización muy robusta (+1)
- ➕ Soporta formatos legacy (+0.5)
- ➕ CSS avanzado (3 layouts, 3 tamaños) (+1)
- ➕ Preview data realista (+0.5)
- ➕ Fallbacks múltiples (+0.5)
- ➕ Código bien estructurado (+0.5)
- ➕ Try-catch wrapper (+0.5)
- ➕ Convierte IDs a URLs (+0.5)
- ➖ ⛔ **Estructura incompatible PHP ↔ Template** (-2) ← CRÍTICO
- ➖ ⛔ **Variables undefined en template** (-1) ← CRÍTICO
- ➖ NO hereda BlockBase (-0.5)
- ➖ NO attributes (layouts NO configurables) (-0.5)
- ➖ Sin DocBlocks (-0.5)

### Fortalezas
1. **Normalización robusta:** Múltiples formatos soportados (arrays, strings, legacy)
2. **Conversión automática:** Image IDs → URLs con wp_get_attachment_image_url()
3. **Fallbacks múltiples:** label → text → '', badges → default_badges
4. **CSS avanzado:** 3 layouts (horizontal, grid, vertical), 3 tamaños, 3 alineaciones
5. **Preview data realista:** 4 badges de industria de viajes (ATOL, TripAdvisor, etc.)
6. **Código limpio:** Métodos cortos (<40 líneas), buena legibilidad
7. **Usa EditorHelper:** Correctamente implementado
8. **Try-catch wrapper:** Manejo de errores robusto
9. **Early return:** Si NO hay badges, NO renderiza
10. **Icons variados:** shield-alt, star-filled, awards, yes-alt

### Debilidades
1. ⛔ **INCOMPATIBILIDAD CRÍTICA PHP ↔ TEMPLATE** - Estructuras de datos completamente diferentes
2. ⛔ **Variables undefined en template** - section_title, show_descriptions
3. ❌ **NO hereda de BlockBase** - Inconsistente con arquitectura
4. ❌ **NO tiene attributes** - Layouts/tamaños en CSS pero NO configurables vía Gutenberg
5. ❌ **NO tiene DocBlocks** (0/7 métodos)
6. ⚠️ **NO conditional CSS loading** - CSS se carga siempre
7. ⚠️ **Image size hardcoded** ('medium')
8. ⚠️ **Meta key hardcoded** ('trust_badges')
9. ⚠️ **NO valida Image URLs** - Acepta cualquier string
10. ⚠️ **IconHelper NO preparado desde PHP** - Template lo usa directamente

### Recomendación Principal

**Este bloque tiene DOS PROBLEMAS CRÍTICOS que deben resolverse INMEDIATAMENTE:**

**PROBLEMA CRÍTICO 1:** ⛔ La estructura de datos que genera PHP NO coincide con la que espera el template. PHP usa `['icon', 'label', 'image']` pero template espera `['badge_type', 'title', 'description', 'image' => ['sizes', 'url']]`.

**PROBLEMA CRÍTICO 2:** ⛔ El template usa variables `$section_title` y `$show_descriptions` que NO se pasan desde PHP, causando PHP warnings.

**Prioridad 0 - CRÍTICO (Esta semana - 1 hora):**
1. ⛔ **Arreglar incompatibilidad PHP ↔ Template** (45 min)
   - OPCIÓN RECOMENDADA: Cambiar template para usar estructura actual de PHP
   - Cambiar `$badge['title']` → `$badge['label']`
   - Cambiar `$badge['image']['sizes']['thumbnail']` → `$badge['image']`
   - Eliminar uso de `$badge['badge_type']` (detectar con `!empty($badge['image'])`)
2. ⛔ **Agregar variables faltantes** (15 min)
   - Agregar `$section_title = ''` en $data
   - Agregar `$show_descriptions = false` en $data

**Prioridad 1 - Alta (Esta semana - 3 horas):**
3. Heredar de BlockBase (1 hora)
4. Agregar Block Attributes para layouts/tamaños (1.5 horas)
5. Agregar DocBlocks (30 min)

**Prioridad 2 - Media (2 semanas - 50 min):**
6. Constantes para hardcoded values (20 min)
7. Validación de Image URLs (15 min)
8. Conditional CSS loading (15 min)

**Prioridad 3 - Baja (Cuando haya tiempo - 1h 15min):**
9. block.json (45 min)
10. Soporte para descripciones (30 min)

**Esfuerzo total:** ~6 horas 5 min

**Veredicto:** Este bloque tiene **excelente normalización de datos** y **CSS muy completo**, pero sufre de DOS problemas críticos:
1. Estructura de datos incompatible → Template NO renderiza correctamente
2. Variables undefined → Genera PHP warnings

**ACCIÓN URGENTE:** Antes de cualquier otra refactorización, DEBEN resolverse ambos problemas críticos. Sin esto:
- Template NO muestra badges correctamente (campo 'title' undefined)
- PHP warnings en producción (mala UX, logs llenos)
- IconHelper NO funciona (espera icon preparado desde PHP)

**PRIORIDAD: CRÍTICA - El bloque NO funciona correctamente en su estado actual.**

### Dependencias Identificadas

**WordPress:**
- get_post_meta() - Obtener badges desde meta
- wp_get_attachment_image_url() - Convertir image IDs a URLs
- get_the_ID() - Post ID actual

**Helpers:**
- EditorHelper::is_editor_mode() ✅
- IconHelper (⚠️ template lo usa pero PHP NO lo prepara)

**Meta Fields:**
- 'trust_badges' (array de badges)

**JavaScript:**
- ❌ **NO tiene JavaScript**

**CSS:**
- trust-badges.css (176 líneas)
- 3 layouts: horizontal, grid, vertical
- 3 tamaños: small, medium, large
- 3 alineaciones: left, center, right
- Responsive breakpoint: 767px

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** ⛔ **CRÍTICA** - Resolver incompatibilidad estructura de datos + agregar variables faltantes
**Próxima revisión:** Después de resolver problemas críticos
