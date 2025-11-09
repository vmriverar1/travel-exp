# Auditoría: QuickFacts (Package)

**Fecha:** 2025-11-09
**Bloque:** 16/XX Package
**Tiempo:** 35 min
**✅ ESTADO:** BUENO - Bloque funcional con template consistente
**✅ NOTA IMPORTANTE:** Template coincide correctamente con variables del PHP

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/quick-facts`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Campos meta:** `highlights` (array), `duration`, `physical_difficulty`, `service_type`, `departure`
- **Icon:** `list-view`
- **Category:** `travel`
- **Keywords:** facts, features, highlights, package

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **Usa template separado** ✅ (quick-facts.php)
- **✅ CONSISTENCIA CORRECTA:** Template y PHP coinciden perfectamente
- **Highlights dependency:** Asume estructura específica de meta field
- **Fallback a basic fields:** Si NO hay highlights, usa duration/difficulty/service_type/departure

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** ✅ Usa is_editor_mode() correctamente
- **IconHelper:** ✅ Usa get_icon_svg() para iconos
- **Meta fields:** highlights (array complejo), duration, physical_difficulty, service_type, departure
- **Template:** quick-facts.php (42 líneas)
- **CSS:** quick-facts.css (176 líneas - múltiples layouts y variantes)

### ✅ IMPORTANTE - TEMPLATE CONSISTENTE
**ACLARACIÓN CRÍTICA:** El bloque tiene **consistencia correcta** entre PHP y template:

**PHP pasa al template:**
```php
$data = [
    'block_id' => 'quick-facts-abc123',
    'class_name' => 'quick-facts quick-facts--grid-2...',
    'section_title' => '', // Vacío
    'facts' => [...], // Array de facts con icon/label/value
    'layout' => 'grid-2',
    'icon_size' => 'medium',
    'icon_color' => '#4A90A4',
    'card_style' => 'default',
    'show_icons' => true,
    'is_preview' => false,
];
```

**Template espera:**
```php
$block_id // String - ID único
$class_name // String - Clases CSS
$section_title // String - Título opcional (puede estar vacío)
$facts // Array - Lista de facts con icon/label/value
$icon_size // String - small/medium/large
$icon_color // String - Color hex
```

**RESULTADO:** ✅ **El template funciona correctamente** con el código PHP actual.

### ✅ IMPORTANTE - ESTRUCTURA DE HIGHLIGHTS
**ACLARACIÓN CRÍTICA:** El bloque espera que highlights tenga una de estas estructuras:

**Opción 1 - Array de objetos con icon/text/label/value:**
```php
$highlights = [
    ['icon' => 'clock', 'text' => '4 days'],
    ['icon' => 'users', 'label' => 'Group', 'value' => 'Small'],
];
```

**Opción 2 - Array de strings:**
```php
$highlights = ['Free WiFi', 'Breakfast included', 'Airport pickup'];
```

**Fallback - Si NO hay highlights:**
Usa duration, physical_difficulty, service_type, departure para crear facts básicos.

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/QuickFacts.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** ✅ `/templates/quick-facts.php` (42 líneas - ✅ CONSISTENTE con PHP)
**Assets:**
- CSS: `/assets/blocks/quick-facts.css` (176 líneas - incluye layouts grid/list y variantes)
- JS: ❌ NO tiene JavaScript

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- ✅ EditorHelper::is_editor_mode() (correctamente usado)
- ✅ IconHelper::get_icon_svg() (para iconos SVG)
- Post meta fields (highlights, duration, physical_difficulty, service_type, departure)

**Líneas de Código:**
- **Clase PHP:** 189 líneas
- **Template:** 42 líneas
- **JavaScript:** 0 líneas
- **CSS:** 176 líneas
- **TOTAL:** 407 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Muestra datos rápidos del paquete (duración, dificultad, tipo, etc.) en formato de lista con iconos SVG. Útil para destacar información clave del paquete de forma visual.

**Funcionalidad Principal:**
1. **Display de highlights del paquete:**
   - Lista de datos rápidos con icono, label y value
   - Usa highlights meta field (array)
   - Fallback a basic meta fields si NO hay highlights
   - Iconos SVG configurables con color

2. **Transformación de formato:**
   - Highlights pueden ser arrays de objetos (con icon/text/label/value)
   - O arrays de strings simples
   - Normaliza a formato: ['icon' => '', 'label' => '', 'value' => '']

3. **Fallback a meta fields básicos:**
   - Si NO hay highlights → usa duration, physical_difficulty, service_type, departure
   - Genera facts automáticamente con iconos predefinidos
   - Asegura que siempre haya contenido

4. **Preview mode:**
   - Muestra datos de ejemplo hardcoded
   - 6 facts de ejemplo (duration, group size, difficulty, starting point, best time, meals)
   - NO usa datos reales en editor

5. **Template rendering:**
   - Usa load_template() con extract()
   - ✅ Variables coinciden correctamente
   - Usa IconHelper para iconos SVG

**Inputs (Meta fields - NO registrados en código):**
- `highlights` (array) - Lista de highlights con icon/text/label/value
- `duration` (string) - Duración del paquete (fallback)
- `physical_difficulty` (string) - Dificultad física (fallback)
- `service_type` (string) - Tipo de servicio (fallback)
- `departure` (string) - Punto de partida (fallback)

**Outputs:**
- Lista de facts con:
  - Iconos SVG con color configurable
  - Label (opcional, uppercase, pequeño)
  - Value (principal, bold)
  - Layouts: list (vertical), grid-2, grid-3, grid-4
  - Card styles: default, card (con shadow), bordered
  - Icon sizes: small (24px), medium (32px), large (48px)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'quick-facts';
private string $title = 'Quick Facts';
private string $description = 'Display quick facts and key information with icons';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (16 líneas)
2. enqueue_assets(): void - Encola CSS (7 líneas)
3. render($attributes, $content, $block): string - Renderiza (32 líneas)
```

**Métodos Privados:**
```php
4. get_preview_data(): array - Datos de preview (8 líneas)
5. get_post_data(int $post_id): array - Datos reales del post (53 líneas)
```

**Métodos Protegidos:**
```php
6. load_template(string $template_name, array $data = []): void - Carga template (11 líneas)
```

**Total:** 6 métodos, 189 líneas

**Métodos más largos:**
1. ✅ `get_post_data()` - **53 líneas** (aceptable, pero cerca del límite de 50)
2. ✅ `render()` - **32 líneas** (excelente)
3. ✅ `register()` - **16 líneas** (excelente)
4. ✅ `load_template()` - **11 líneas** (excelente)

**Observación:** ✅ TODOS los métodos están bien dimensionados (<60 líneas)

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/quick-facts`
- api_version: 2
- category: `travel`
- icon: `list-view`
- keywords: ['facts', 'features', 'highlights', 'package']
- supports: anchor: true, html: false
- render_callback: `[$this, 'render']`

**Enqueue Assets:**
- CSS: `/assets/blocks/quick-facts.css` (sin condiciones)
- Hook: `enqueue_block_assets`
- ⚠️ **NO hay conditional loading** - CSS se carga siempre (incluso en páginas sin el bloque)

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Campos:** ❌ **NO REGISTRA CAMPOS** (asume que meta fields existen)

---

## 5. Campos Meta

**Definición:** ❌ **NO REGISTRA CAMPOS EN CÓDIGO**

**Campos usados (asume que existen):**
- `highlights` (array) - Lista de highlights con estructura flexible:
  - Opción 1: Array de objetos con 'icon', 'text', 'label', 'value'
  - Opción 2: Array de strings simples
- `duration` (string) - Duración del paquete (fallback)
- `physical_difficulty` (string) - Dificultad física (fallback)
- `service_type` (string) - Tipo de servicio (fallback)
- `departure` (string) - Punto de partida (fallback)

**Problemas:**
- ❌ **NO registra campos** - Depende de que estén definidos externamente
- ❌ **NO documenta estructura esperada de highlights** (puede causar errores)
- ❌ **NO documenta qué campos son required vs optional**
- ✅ Tiene fallback robusto a basic meta fields
- ⚠️ **Hardcoded icon_color** (#4A90A4 teal)
- ⚠️ **Hardcoded layout/icon_size** (grid-2, medium)

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Try-catch wrapper (líneas 57-102)
2. Get post_id con get_the_ID() (línea 58)
3. Check preview mode con EditorHelper::is_editor_mode() (línea 59)
4. Si preview: get_preview_data() (línea 62)
5. Si NO preview: get_post_data($post_id) (línea 64)
6. Early return si empty($facts) (líneas 67-69)
7. Generate block_id con uniqid() (línea 71)
8. Append className si existe (líneas 74-76)
9. Build $data array (líneas 78-89)
10. Output con ob_start/load_template/ob_get_clean (líneas 91-93)
11. Catch exceptions con mensaje de error en WP_DEBUG (líneas 95-101)

**Flujo de Datos:**
```
render()
  → EditorHelper::is_editor_mode()?
    → YES: get_preview_data()
      → return hardcoded preview data (6 facts)
    → NO: get_post_data($post_id)
      → get highlights meta field
      → is_array() && !empty()?
        → NO: Fallback to basic meta fields
          → get duration (icon: clock)
          → get physical_difficulty (icon: compass)
          → get service_type (icon: users)
          → get departure (icon: map-pin)
          → return basic facts array
        → YES: Transform highlights format
          → foreach highlights
          → if is_array → extract icon/text/label/value
          → if is_string → icon=check, value=string
          → return transformed facts array
  → empty check on $facts
  → load_template('quick-facts', $data)
    → extract($data)
    → include template
      → foreach facts
        → IconHelper::get_icon_svg($icon, $size, $color)
        → esc_html($label)
        → esc_html($value)
```

**Variables al Template:**
```php
$block_id = 'quick-facts-abc123'; // string
$class_name = 'quick-facts quick-facts--grid-2 quick-facts--medium quick-facts--default'; // string
$section_title = ''; // string (siempre vacío - hardcoded)
$facts = [
    ['icon' => 'clock', 'label' => 'Duration', 'value' => '4 days'],
    ['icon' => 'users', 'label' => 'Group Size', 'value' => 'Small'],
    ...
]; // array
$layout = 'grid-2'; // string
$icon_size = 'medium'; // string (small/medium/large)
$icon_color = '#4A90A4'; // string (teal)
$card_style = 'default'; // string
$show_icons = true; // bool
$is_preview = false; // bool
```

**✅ CORRECTO:** El template usa las variables correctamente y todas están disponibles.

**Manejo de Errores:**
- ✅ Try-catch wrapper en render()
- ✅ WP_DEBUG check antes de mostrar error
- ✅ Escapado de error con esc_html()
- ✅ Return empty string si error y NO WP_DEBUG
- ✅ File exists check en load_template()
- ✅ Empty check en $facts antes de renderizar
- ✅ is_array() checks en get_post_data()

---

## 7. Funcionalidades Adicionales

### 7.1 Fallback a Basic Meta Fields

**Método:** `get_post_data()` (líneas 120-146)

**Funcionalidad:**
```php
if (!is_array($highlights) || empty($highlights)) {
    // Fallback: create basic facts from package meta
    $facts = [];

    $duration = get_post_meta($post_id, 'duration', true);
    if ($duration) {
        $facts[] = ['icon' => 'clock', 'label' => 'Duration', 'value' => $duration];
    }

    $difficulty = get_post_meta($post_id, 'physical_difficulty', true);
    if ($difficulty) {
        $facts[] = ['icon' => 'compass', 'label' => 'Difficulty', 'value' => ucfirst($difficulty)];
    }

    $service_type = get_post_meta($post_id, 'service_type', true);
    if ($service_type) {
        $facts[] = ['icon' => 'users', 'label' => 'Type', 'value' => ucfirst($service_type)];
    }

    $departure = get_post_meta($post_id, 'departure', true);
    if ($departure) {
        $facts[] = ['icon' => 'map-pin', 'label' => 'Starting Point', 'value' => $departure];
    }

    return $facts;
}
```

**Características:**
- ✅ Verifica is_array() && !empty() antes de usar highlights
- ✅ Crea facts automáticamente desde meta fields básicos
- ✅ Iconos predefinidos para cada tipo de dato
- ✅ Labels descriptivos en inglés
- ✅ ucfirst() para difficulty y service_type
- ⚠️ **Labels NO usan traducción** (hardcoded en inglés)
- ⚠️ **Iconos hardcoded** (clock, compass, users, map-pin)

**Calidad:** 8/10 - Fallback robusto pero sin traducción

### 7.2 Transformación de Highlights

**Método:** `get_post_data()` (líneas 149-169)

**Funcionalidad:**
```php
// Transform highlights format
$facts = [];
foreach ($highlights as $highlight) {
    if (is_array($highlight)) {
        // Highlight is already an array with icon/label/value
        $facts[] = [
            'icon' => $highlight['icon'] ?? 'check',
            'label' => '',
            'value' => $highlight['text'] ?? $highlight['label'] ?? $highlight['value'] ?? '',
        ];
    } elseif (is_string($highlight) && !empty($highlight)) {
        // Highlight is a simple string
        $facts[] = [
            'icon' => 'check',
            'label' => '',
            'value' => $highlight,
        ];
    }
}
```

**Características:**
- ✅ Soporta múltiples formatos de highlights
- ✅ Formato 1: Array con icon/text/label/value
- ✅ Formato 2: String simple
- ✅ Normaliza a formato consistente
- ✅ Usa operador ?? para defaults
- ✅ Default icon: 'check'
- ✅ Verifica is_string() && !empty()
- ⚠️ **Label siempre vacío** (línea 156) - Ignora label de highlights
- ⚠️ **Prioridad text > label > value** puede ser confusa

**Calidad:** 7/10 - Flexible pero label siempre vacío

**Observaciones:**
- ⚠️ **PROBLEMA:** Label se establece como '' incluso si highlight tiene 'label'
- ⚠️ **CONFUSO:** Usa 'text', 'label' o 'value' indistintamente para el value
- ✅ Flexible para diferentes estructuras de datos

### 7.3 Preview Data

**Método:** `get_preview_data()` (líneas 105-115)

**Funcionalidad:**
```php
return [
    ['icon' => 'clock', 'label' => 'Duration', 'value' => '4 days / 3 nights'],
    ['icon' => 'users', 'label' => 'Group Size', 'value' => 'Small group (max 12)'],
    ['icon' => 'compass', 'label' => 'Difficulty', 'value' => 'Moderate'],
    ['icon' => 'map-pin', 'label' => 'Starting Point', 'value' => 'Cusco, Peru'],
    ['icon' => 'calendar', 'label' => 'Best Time', 'value' => 'May - September'],
    ['icon' => 'check', 'label' => 'Meals', 'value' => 'All included'],
];
```

**Características:**
- ✅ 6 facts de ejemplo realistas
- ✅ Todos tienen icon, label y value
- ✅ Iconos variados (clock, users, compass, map-pin, calendar, check)
- ✅ Datos representativos de un paquete turístico
- ✅ Labels descriptivos
- ⚠️ **Labels en inglés** (no traducidos)

**Calidad:** 9/10 - Completo y realista

### 7.4 Template Loading

**Método:** `load_template()` (líneas 172-186)

**Funcionalidad:**
- Construye path: TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php'
- Check file_exists()
- Si NO existe: muestra warning en WP_DEBUG
- extract($data, EXTR_SKIP) → Convierte array keys a variables
- include $template_path

**Calidad:** 7/10 - Estándar pero con extract()

**Problemas:**
- ⚠️ **extract() es peligroso** - Puede sobrescribir variables (usa EXTR_SKIP, mejor)
- ⚠️ **NO documenta** que usa extract
- ⚠️ **NO valida** que $data sea array
- ✅ File exists check presente
- ✅ WP_DEBUG check antes de warning
- ✅ Escapado con esc_html() en warning

### 7.5 JavaScript

**Archivo:** ❌ NO tiene JavaScript

**Razón:** El bloque es puramente presentacional, no necesita interacción

**Observación:** ✅ Correcto - No necesita JS

### 7.6 CSS

**Archivo:** `/assets/blocks/quick-facts.css` (176 líneas)

**Características:**
- ✅ **Layouts:**
  - list (vertical con gap 1rem)
  - grid-2 (2 columnas, flex-direction: column con gap 0.5rem)
  - grid-3 (3 columnas, grid)
  - grid-4 (4 columnas, grid)
- ✅ **Card Styles:**
  - default (sin estilos especiales)
  - card (background white, box-shadow, hover effect)
  - bordered (border 2px, hover cambia color)
- ✅ **Icon Sizes:**
  - small: 24px
  - medium: 32px (default)
  - large: 48px
- ✅ **Size Variations:**
  - Afectan font-size de label y value
  - small: label 0.75rem, value 1rem
  - medium: label 0.875rem, value 1.125rem (default)
  - large: label 1rem, value 1.25rem
- ✅ **Responsive:**
  - 1023px: grid-4 → 2 columnas
  - 767px: todos los grids → 1 columna
  - Reduce font-sizes y padding
- ✅ **CSS Variables:**
  - var(--color-gray-900), var(--color-gray-600), etc.
  - var(--border-radius-md)
  - Fallbacks incluidos

**Organización:**
- ✅ Secciones claras (base, layouts, item, card styles, icon, content, sizes, responsive)
- ✅ Comentarios descriptivos
- ✅ Cascada lógica

**Calidad:** 9/10 - Completo, flexible y bien organizado

**Observaciones:**
- ✅ Muy flexible con variantes de layout, card y size
- ✅ Responsive design robusto
- ✅ Usa CSS variables con fallbacks
- ⚠️ **Nota:** grid-2 usa flex-direction: column (NO es grid real)

### 7.7 Template

**Archivo:** `/templates/quick-facts.php` (42 líneas)

**Características:**
- ✅ Usa IconHelper::get_icon_svg() para iconos
- ✅ Escapado correcto con esc_attr(), esc_html()
- ✅ Early return si empty($facts)
- ✅ Conditional rendering de section_title
- ✅ Loop limpio con foreach
- ✅ Icon sizes configurables (líneas 23-28)
- ✅ Operador ?? para default icon y size

**Calidad:** 9/10 - Limpio, seguro y eficiente

**Observaciones:**
- ✅ Variables coinciden con las del PHP
- ✅ Código muy legible
- ✅ NO hay lógica de negocio (solo presentación)

### 7.8 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.9 Dependencias Externas

- Post meta get_post_meta() (highlights, duration, physical_difficulty, service_type, departure)
- WordPress get_the_ID()
- EditorHelper::is_editor_mode() ✅
- IconHelper::get_icon_svg() ✅

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ **CUMPLE**
- Clase tiene una responsabilidad clara: renderizar quick facts
- Métodos bien enfocados
- NO hay complejidad excesiva
- **Impacto:** NINGUNO

**OCP:** ⚠️ **VIOLA**
- No hereda de BlockBase → Difícil extender
- Hardcoded values (icon_color, layout, icon_size) → NO configurable desde atributos
- **Impacto:** MEDIO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con mejores bloques
- **Impacto:** MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Acoplado directamente a:
  - Post meta get_post_meta()
  - IconHelper (pero es una abstracción, OK)
  - Estructura específica de highlights
- No hay abstracción/interfaces para data source
- **Impacto:** BAJO - Acoplamiento normal para WordPress

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **TODOS los métodos <60 líneas** (BUENO)
- ⚠️ get_post_data() con **53 líneas** (aceptable pero cerca del límite de 50)
- ✅ Complejidad ciclomática baja

**Anidación:**
- ✅ **Máximo 2 niveles** de anidación (excelente)
- ✅ Código muy legible

**Duplicación:**
- ✅ **NO hay duplicación** significativa
- ✅ Código DRY

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ✅ Propiedades claras

**Código Sin Uso:**
- ⚠️ **section_title siempre vacío** (línea 81) - Variable sin uso real
- ⚠️ **layout, card_style, show_icons** se pasan al template pero NO se usan (solo en class_name)

**DocBlocks:**
- ❌ **0/6 métodos documentados** (0%)
- ❌ Header de archivo básico
- ❌ NO documenta params/return types
- **Impacto:** MEDIO

**Magic Values:**
- ⚠️ '#4A90A4' hardcoded (debería ser constante)
- ⚠️ 'grid-2', 'medium', 'default' hardcoded (deberían ser configurables desde atributos)
- ⚠️ 'clock', 'compass', 'users', 'map-pin' iconos hardcoded en fallback
- ⚠️ 'Duration', 'Difficulty', 'Type', 'Starting Point' labels hardcoded sin traducción

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ get_post_meta() de WordPress es seguro
- ✅ NO hay inputs de usuario directos
- ✅ is_array() checks antes de usar highlights
- **Impacto:** NINGUNO - Perfecto

**Escapado:**
- ✅ **Template usa escapado correcto:**
  - esc_attr() para atributos HTML (línea 11)
  - esc_html() para contenido de texto (líneas 15, 33, 34)
- ✅ Escapado en error messages (línea 98)
- **Impacto:** NINGUNO - Perfecto

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ✅ No hace queries directas

**XSS:**
- ✅ **Protección completa** - Todo escapado correctamente

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ✅ **Template separado** (quick-facts.php)
- ✅ **Template consistente** con datos de la clase
- ✅ Lógica de negocio en clase
- ✅ Estilos en CSS separado

**Acoplamiento:**
- ✅ **Bajo acoplamiento** - Solo meta fields e IconHelper
- ✅ NO hay dependencias complejas
- **Impacto:** NINGUNO

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Pierde funcionalidades compartidas
- **Impacto:** MEDIO

**Caché:**
- ✅ N/A - No necesita caché (data de post meta)

**Otros:**
- ❌ **NO usa block.json** (debería para Gutenberg moderno)
- ✅ **Usa EditorHelper** correctamente
- ✅ **Usa IconHelper** correctamente

---

## 9. Recomendaciones de Refactorización

### Prioridad Alta

**1. Heredar de BlockBase**
- **Acción:** `class QuickFacts extends BlockBase`
- **Razón:** Consistencia, funcionalidades compartidas
- **Riesgo:** MEDIO - Requiere refactorizar
- **Esfuerzo:** 1 hora

**2. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con params, returns, description
- **Razón:** Documentación para mantenimiento
- **Riesgo:** NINGUNO
- **Esfuerzo:** 30 min

**3. Hacer valores configurables desde atributos**
- **Acción:**
  ```php
  'layout' => $attributes['layout'] ?? 'grid-2',
  'icon_size' => $attributes['iconSize'] ?? 'medium',
  'icon_color' => $attributes['iconColor'] ?? '#4A90A4',
  'card_style' => $attributes['cardStyle'] ?? 'default',
  'section_title' => $attributes['sectionTitle'] ?? '',
  ```
- **Razón:** Flexibilidad para configurar desde editor
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min
- **Precauciones:** Mantener defaults para compatibilidad

**4. Usar label de highlights correctamente**
- **Acción:**
  ```php
  if (is_array($highlight)) {
      $facts[] = [
          'icon' => $highlight['icon'] ?? 'check',
          'label' => $highlight['label'] ?? '', // USAR label si existe
          'value' => $highlight['text'] ?? $highlight['value'] ?? '',
      ];
  }
  ```
- **Razón:** Aprovechar label de highlights en lugar de ignorarlo
- **Riesgo:** MEDIO - Puede cambiar apariencia visual
- **Esfuerzo:** 15 min

### Prioridad Media

**5. Convertir hardcoded values a constantes**
- **Acción:**
  ```php
  private const DEFAULT_ICON_COLOR = '#4A90A4';
  private const DEFAULT_LAYOUT = 'grid-2';
  private const DEFAULT_ICON_SIZE = 'medium';
  private const DEFAULT_CARD_STYLE = 'default';

  private const FALLBACK_ICONS = [
      'duration' => 'clock',
      'difficulty' => 'compass',
      'service_type' => 'users',
      'departure' => 'map-pin',
  ];

  // Uso:
  'icon_color' => $attributes['iconColor'] ?? self::DEFAULT_ICON_COLOR,
  ```
- **Razón:** Mantenibilidad, configurabilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**6. Agregar traducción a labels de fallback**
- **Acción:**
  ```php
  if ($duration) {
      $facts[] = [
          'icon' => 'clock',
          'label' => __('Duration', 'travel-blocks'),
          'value' => $duration
      ];
  }
  ```
- **Razón:** Soporte multi-idioma
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**7. Conditional CSS loading**
- **Acción:**
  ```php
  public function enqueue_assets(): void
  {
      if (!is_admin() && (is_singular('package') || has_block('travel-blocks/quick-facts'))) {
          wp_enqueue_style(...);
      }
  }
  ```
- **Razón:** Performance - Solo cargar CSS donde se necesita
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**8. Documentar estructura de highlights**
- **Acción:**
  ```php
  /**
   * Get post data from meta fields
   *
   * Expects 'highlights' meta field with one of these formats:
   *
   * Format 1 - Array of objects:
   * [
   *     ['icon' => 'clock', 'label' => 'Duration', 'value' => '4 days'],
   *     ['icon' => 'users', 'text' => 'Small group'],
   * ]
   *
   * Format 2 - Array of strings:
   * ['Free WiFi', 'Breakfast included', 'Airport pickup']
   *
   * If no highlights, falls back to duration/difficulty/service_type/departure
   *
   * @param int $post_id
   * @return array Array of facts with icon/label/value
   */
  private function get_post_data(int $post_id): array
  ```
- **Razón:** Claridad para desarrolladores
- **Riesgo:** NINGUNO
- **Esfuerzo:** 10 min

### Prioridad Baja

**9. Crear block.json**
- **Acción:** Migrar de register_block_type() a block.json con atributos definidos
- **Razón:** Gutenberg moderno, mejor performance
- **Riesgo:** BAJO
- **Esfuerzo:** 45 min

**10. Simplificar get_post_data()**
- **Acción:**
  ```php
  private function get_post_data(int $post_id): array
  {
      $highlights = get_post_meta($post_id, 'highlights', true);

      if (!is_array($highlights) || empty($highlights)) {
          return $this->get_fallback_facts($post_id);
      }

      return $this->transform_highlights($highlights);
  }

  private function get_fallback_facts(int $post_id): array { ... }
  private function transform_highlights(array $highlights): array { ... }
  ```
- **Razón:** SRP, métodos más pequeños y enfocados
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**11. Usar section_title o eliminar**
- **Acción:**
  ```php
  // OPCIÓN A: Usar section_title desde atributos
  'section_title' => $attributes['sectionTitle'] ?? '',

  // OPCIÓN B: Eliminar completamente si no se usa
  // Remover de $data array y de template
  ```
- **Razón:** Eliminar código sin uso o hacerlo funcional
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min

---

## 10. Plan de Acción

### Fase 1 - Alta Prioridad (Esta semana)
1. Heredar de BlockBase (1 hora)
2. Agregar DocBlocks (30 min)
3. Hacer valores configurables desde atributos (30 min)
4. Usar label de highlights correctamente (15 min)

**Total Fase 1:** 2 horas 15 min

### Fase 2 - Media Prioridad (Próximas 2 semanas)
5. Convertir hardcoded a constantes (20 min)
6. Traducción labels de fallback (15 min)
7. Conditional CSS loading (15 min)
8. Documentar estructura highlights (10 min)

**Total Fase 2:** 1 hora

### Fase 3 - Baja Prioridad (Cuando haya tiempo)
9. Crear block.json (45 min)
10. Simplificar get_post_data() (30 min)
11. Usar o eliminar section_title (10 min)

**Total Fase 3:** 1 hora 25 min

**Total Refactorización Completa:** ~4 horas 40 min

**Precauciones Generales:**
- ⚠️ **NO cambiar** estructura esperada de highlights sin consultar
- ⚠️ **Verificar** que iconos existen en IconHelper antes de usar
- ⚠️ **Probar** con paquetes reales que tengan y NO tengan highlights
- ✅ SIEMPRE probar fallback a basic meta fields
- ✅ Verificar que iconos SVG se muestran correctamente

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra 6 facts de ejemplo)
- [ ] Frontend funciona (muestra datos reales)
- [ ] ✅ Variables del template coinciden con las del PHP

### Datos de Highlights
- [ ] Highlights con estructura de objetos funciona
- [ ] Highlights con strings simples funciona
- [ ] Label se usa correctamente si existe en highlights
- [ ] Icon default 'check' funciona para highlights sin icon
- [ ] Escapado correcto en todos los outputs

### Fallback a Basic Fields
- [ ] Fallback funciona si NO hay highlights
- [ ] duration se muestra correctamente (icon: clock)
- [ ] physical_difficulty se muestra (icon: compass)
- [ ] service_type se muestra (icon: users)
- [ ] departure se muestra (icon: map-pin)
- [ ] ucfirst() funciona en difficulty y service_type
- [ ] Labels están traducidos (si se agregó traducción)

### Template
- [ ] load_template() carga correctamente
- [ ] extract() crea variables correctamente
- [ ] IconHelper::get_icon_svg() funciona
- [ ] Icon sizes funcionan (small/medium/large)
- [ ] Icon color se aplica correctamente
- [ ] section_title se muestra si existe (o se eliminó si no se usa)

### CSS
- [ ] Estilos se aplican correctamente
- [ ] Layouts funcionan (list, grid-2, grid-3, grid-4)
- [ ] Card styles funcionan (default, card, bordered)
- [ ] Icon sizes funcionan (small 24px, medium 32px, large 48px)
- [ ] Size variations funcionan (afectan font-size)
- [ ] Responsive funciona (1023px, 767px)
- [ ] Hover effects funcionan (card, bordered)
- [ ] Conditional loading funciona (si se agregó)

### Seguridad
- [ ] ✅ esc_html() en todos los outputs de texto
- [ ] ✅ esc_attr() en atributos HTML
- [ ] get_post_meta() se usa correctamente
- [ ] is_array() checks funcionan

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] Constantes definidas (si se agregaron)
- [ ] block.json (si se creó)
- [ ] Atributos configurables desde editor (si se agregó)

### Clean Code
- [ ] Métodos <50 líneas (si se refactorizó get_post_data())
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] Constantes en lugar de magic values (si se cambiaron)
- [ ] Traducción en labels (si se agregó)

### Performance
- [ ] CSS solo se carga donde se necesita (si se agregó conditional)
- [ ] NO hay queries innecesarias
- [ ] Iconos SVG se cargan eficientemente

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Código PHP bien estructurado (189 líneas)
- ✅ Template consistente con PHP (variables coinciden)
- ✅ Fallback robusto a basic meta fields
- ✅ Transformación flexible de highlights
- ✅ Usa IconHelper para iconos SVG
- ✅ Escapado correcto en template
- ✅ CSS completo con variantes (176 líneas)
- ✅ Responsive design robusto
- ✅ Try-catch wrapper en render()
- ⚠️ Valores hardcoded (icon_color, layout, icon_size)
- ⚠️ section_title siempre vacío (sin uso)
- ⚠️ label de highlights se ignora
- ❌ NO hereda de BlockBase
- ❌ NO tiene DocBlocks (0/6 métodos)

### Puntuación: 7.5/10

**Razones para la puntuación:**
- ➕ Template consistente con PHP (+1.5) ← IMPORTANTE
- ➕ Fallback robusto a basic fields (+1)
- ➕ Transformación flexible de highlights (+0.5)
- ➕ Usa IconHelper correctamente (+0.5)
- ➕ Escapado perfecto (+0.5)
- ➕ CSS flexible con variantes (+1)
- ➕ Try-catch wrapper (+0.5)
- ➖ NO hereda BlockBase (-1)
- ➖ Valores hardcoded (-0.5)
- ➖ Sin DocBlocks (-0.5)
- ➖ label de highlights ignorado (-0.5)
- ➖ section_title sin uso (-0.5)

### Fortalezas
1. **Template consistente:** Variables coinciden perfectamente entre PHP y template (diferencia clave con otros bloques)
2. **Fallback robusto:** Crea facts desde duration/difficulty/service_type/departure si NO hay highlights
3. **Transformación flexible:** Soporta múltiples formatos de highlights (array de objetos o strings)
4. **IconHelper:** Usa abstracción correcta para iconos SVG
5. **Escapado perfecto:** esc_attr(), esc_html() en todos los outputs
6. **CSS flexible:** Múltiples variantes de layout (list, grid-2/3/4), card (default, card, bordered), sizes
7. **Responsive design:** Breakpoints en 1023px y 767px
8. **Try-catch wrapper:** Manejo de errores robusto
9. **Early return:** Si empty($facts), NO renderiza (eficiente)
10. **Código limpio:** Métodos relativamente cortos, buena legibilidad

### Debilidades
1. ❌ **NO hereda de BlockBase** - Inconsistente con arquitectura
2. ❌ **NO tiene DocBlocks** (0/6 métodos)
3. ⚠️ **Valores hardcoded** - icon_color, layout, icon_size deberían ser configurables desde atributos
4. ⚠️ **section_title sin uso** - Siempre vacío, código muerto
5. ⚠️ **label de highlights ignorado** - Siempre se establece como '' incluso si existe
6. ⚠️ **Labels sin traducción** - Fallback usa labels en inglés sin __()
7. ⚠️ **NO documenta estructura de highlights** - Puede causar confusión
8. ⚠️ **NO conditional CSS loading** - CSS se carga siempre
9. ⚠️ **get_post_data() largo** - 53 líneas (cerca del límite)
10. ⚠️ **Prioridad confusa** - Usa 'text' ?? 'label' ?? 'value' para value

### Recomendación Principal

**Este bloque tiene BUENA CALIDAD y funciona correctamente.**

**Prioridad 1 - Alta (Esta semana - 2h 15min):**
1. Heredar de BlockBase (1 hora)
2. Agregar DocBlocks (30 min)
3. Hacer valores configurables desde atributos (30 min)
4. Usar label de highlights correctamente (15 min)

**Prioridad 2 - Media (2 semanas - 1h):**
5. Constantes para hardcoded values (20 min)
6. Traducción labels de fallback (15 min)
7. Conditional CSS loading (15 min)
8. Documentar estructura highlights (10 min)

**Prioridad 3 - Baja (Cuando haya tiempo - 1h 25min):**
9. block.json (45 min)
10. Simplificar get_post_data() (30 min)
11. Usar o eliminar section_title (10 min)

**Esfuerzo total:** ~4 horas 40 min

**Veredicto:** Este bloque tiene **código de buena calidad** con template consistente, fallback robusto y escapado perfecto. Las mejoras principales son arquitectónicas (heredar BlockBase, DocBlocks) y de flexibilidad (valores configurables desde atributos). **NO hay problemas críticos.**

**PRIORIDAD: MEDIA - El bloque funciona bien, pero necesita mejoras arquitectónicas.**

### Dependencias Identificadas

**Meta Fields:**
- `highlights` (array) - Estructura flexible (objetos con icon/text/label/value o strings)
- `duration` (string) - Duración del paquete (fallback)
- `physical_difficulty` (string) - Dificultad física (fallback)
- `service_type` (string) - Tipo de servicio (fallback)
- `departure` (string) - Punto de partida (fallback)

**WordPress:**
- get_the_ID() - Obtener post ID

**Helpers:**
- EditorHelper::is_editor_mode() ✅
- IconHelper::get_icon_svg() ✅

**JavaScript:**
- ❌ **NO tiene JavaScript**

**CSS:**
- quick-facts.css (176 líneas)
- Layouts múltiples (list, grid-2/3/4)
- Card styles múltiples (default, card, bordered)
- Icon sizes múltiples (small, medium, large)
- Size variations
- Responsive design

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** MEDIA - Mejoras arquitectónicas recomendadas
**Próxima revisión:** Después de refactorización Fase 1
