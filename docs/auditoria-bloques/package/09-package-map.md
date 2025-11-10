# Auditoría: PackageMap (Package)

**Fecha:** 2025-11-09
**Bloque:** 09/XX Package
**Tiempo:** 25 min
**⚠️ ESTADO:** EXCELENTE - Código simple, limpio y bien estructurado
**⚠️ NOTA IMPORTANTE:** NO es integración con API de mapas - solo muestra imagen estática

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/package-map`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Campo ACF:** `map_image` (ACF field del wizard)
- **Icon:** `location-alt`
- **Category:** `travel`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **NO usa API de mapas** ℹ️ (solo imagen estática, NO Leaflet/Google Maps)
- **Renderizado inline** (NO usa template separado)
- **ACF dependency:** Asume que el campo 'map_image' existe

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** NO usa (pero podría)
- **ACF field:** `map_image` (image/ID) - Asume que existe (NO lo registra)
- **NO hay API keys** ✅ (porque NO usa APIs de mapas)
- **NO hay JavaScript** ✅

### ⚠️ IMPORTANTE - ESTE NO ES UN BLOQUE DE API DE MAPAS
**ACLARACIÓN CRÍTICA:** Este bloque **NO integra con APIs de mapas** (Leaflet, Google Maps, Mapbox, etc.). Solo muestra una **imagen estática** del mapa de ruta que se sube en el wizard del paquete. No hay JavaScript, no hay interactividad, no hay API keys.

Si en el futuro se necesita un mapa interactivo, se debería crear un bloque nuevo (PackageInteractiveMap) que use Leaflet o similar.

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/PackageMap.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** ❌ NO usa template separado (renderizado inline en método render())
**Assets:**
- CSS: `/assets/blocks/package-map.css` (46 líneas)
- JS: ❌ NO tiene JavaScript

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- ACF field 'map_image' (NO lo registra, asume que existe)
- WordPress media functions (wp_get_attachment_image_url, get_post_meta)

**Líneas de Código:**
- **Clase PHP:** 126 líneas
- **Template:** 0 líneas (inline)
- **JavaScript:** 0 líneas
- **CSS:** 46 líneas
- **TOTAL:** 172 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Bloque que muestra la imagen estática del mapa de ruta de un paquete turístico. La imagen se sube en el step "Media & Gallery" del wizard del paquete.

**Funcionalidad Principal:**
1. **Display de imagen de mapa:**
   - Obtiene imagen del campo ACF 'map_image'
   - Soporta dos formatos: array (ACF image object) o ID (attachment ID)
   - Extrae URL y alt text automáticamente
   - Genera alt text por defecto si no existe

2. **Preview mode:**
   - Muestra SVG placeholder con emoji 📍
   - Muestra mensaje instructivo en editor

3. **Conditional rendering:**
   - Solo en páginas de paquete (is_singular('package'))
   - Preview mode en editor
   - No renderiza si no hay imagen

4. **Image optimization:**
   - Lazy loading (loading="lazy")
   - Size: 'large' (optimizado para web)
   - Semantic HTML (figure/img)

**Inputs (ACF - NO registrado en código):**
- `map_image` (image object | attachment ID) - Imagen del mapa de ruta

**Outputs:**
- Figure con imagen del mapa
- Alt text descriptivo
- Placeholder si no hay imagen (editor)
- Empty string si no hay imagen (frontend)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'package-map';
private string $title = 'Package Map';
private string $description = 'Displays the route map image for a package';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (18 líneas)
2. enqueue_assets(): void - Encola CSS (9 líneas)
3. render($attributes, $content): string - Renderiza (42 líneas)
```

**Métodos Protegidos:**
```php
4. render_preview(): string - Preview en editor (18 líneas)
```

**Total:** 4 métodos, 126 líneas

**Métodos más largos:**
1. ✅ `render()` - **42 líneas** (aceptable)
2. ✅ `register()` - **18 líneas** (excelente)
3. ✅ `render_preview()` - **18 líneas** (excelente)
4. ✅ `enqueue_assets()` - **9 líneas** (excelente)

**Observación:** ✅ TODOS los métodos están excelentemente dimensionados (<50 líneas)

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/package-map`
- api_version: 2
- category: `travel`
- icon: `location-alt`
- keywords: ['map', 'route', 'package']
- supports: anchor: true, align: false, html: false
- render_callback: `[$this, 'render']`
- show_in_rest: true

**Enqueue Assets:**
- CSS: `/assets/blocks/package-map.css` (solo frontend, solo singular package)
- Conditional loading: `!is_admin() && is_singular('package')`
- Hook: `enqueue_block_assets`
- ✅ **Optimización:** CSS solo se carga cuando es necesario

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Campos:** ❌ **NO REGISTRA CAMPOS** (asume que ACF field existe)

---

## 5. Campos Meta

**Definición:** ❌ **NO REGISTRA CAMPOS EN CÓDIGO**

**Campos usados (asume que existen):**
- `map_image` (ACF image field) - Del wizard step "Media & Gallery"

**Estructura esperada:**
```php
// Formato 1: ACF Image Object (array)
$map_image = [
    'id' => 123,
    'url' => 'https://example.com/wp-content/uploads/2024/01/map.jpg',
    'alt' => 'Route map description',
    'width' => 1200,
    'height' => 800,
];

// Formato 2: Attachment ID (integer)
$map_image = 123;
```

**Problemas:**
- ❌ **NO registra campo** - Depende de que esté definido en ACF externamente
- ❌ **NO documenta campo** - No hay PHPDoc de estructura esperada
- ❌ **NO valida campo** - get_field() sin validación de tipo
- ✅ **Maneja ambos formatos** - Array object o ID (flexible)

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Check context: is_singular('package')? (línea 63)
2. Si NO es package page → render_preview() (línea 64)
3. Get post_id (línea 67)
4. Get map_image field (línea 68)
5. Early return si no hay imagen (líneas 71-73)
6. Extract image_url y image_alt (líneas 76-77)
7. Early return si no hay URL (líneas 79-81)
8. Generate default alt si está vacío (líneas 84-86)
9. Output con ob_start/ob_get_clean (líneas 88-101)

**Flujo de Datos:**
```
render()
  → is_singular('package')?
    → NO: render_preview()
    → YES:
      → get_field('map_image')
        → empty? return ''
        → is_array? $map_image['url'] : wp_get_attachment_image_url($map_image, 'large')
        → is_array? $map_image['alt'] : get_post_meta($map_image, '_wp_attachment_image_alt', true)
        → generate default alt if empty
        → output HTML
```

**Variables al Output (inline, no template):**
```php
$image_url = 'https://example.com/.../map.jpg'; // string
$image_alt = 'Route map for Package Name'; // string
```

**Manejo de Errores:**
- ✅ Early return si no es singular('package')
- ✅ Early return si no hay map_image
- ✅ Early return si no hay image_url
- ✅ Fallback alt text si está vacío
- ✅ Maneja ambos formatos de imagen (array/ID)
- ⚠️ NO valida tipo de $map_image antes de usar

---

## 7. Funcionalidades Adicionales

### 7.1 Preview Mode

**Método:** `render_preview()`

**Funcionalidad:**
- Muestra SVG placeholder con emoji 📍 y texto "Route Map"
- Muestra mensaje instructivo: "Package Map - Select a map image in the Media & Gallery step of the wizard"
- Usa inline SVG data URI (no request externo)
- Tiene clase especial `.package-map-preview`

**Calidad:** 9/10 - Muy bien implementado, claro y útil

**Observación:** ⚠️ Se muestra en CUALQUIER contexto que NO sea singular('package'), incluyendo editor. Podría usar EditorHelper para detectar editor específicamente.

### 7.2 Image Handling

**Funcionalidad:**
- Soporta ACF Image Object (array)
- Soporta Attachment ID (integer)
- Auto-detecta formato con `is_array()`
- Extrae URL con wp_get_attachment_image_url($id, 'large')
- Extrae alt text de array o post meta
- Genera alt text descriptivo si no existe

**Calidad:** 9/10 - Muy flexible y robusto

**Problemas:**
- ⚠️ NO valida que $map_image sea array o int antes de usar
- ⚠️ NO sanitiza alt text (aunque esc_attr() lo maneja)

### 7.3 JavaScript

**Archivo:** ❌ NO tiene JavaScript

**Razón:** Es solo una imagen estática, no necesita interactividad

### 7.4 CSS

**Archivo:** `/assets/blocks/package-map.css` (46 líneas)

**Características:**
- ✅ Estilos simples y efectivos
- ✅ Responsive design (@media max-width: 768px)
- ✅ Border radius (8px desktop, 4px mobile)
- ✅ Object-fit: cover (mantiene aspect ratio)
- ✅ Fixed height (330px)
- ✅ Background placeholder (#f5f5f5)
- ✅ Preview mode styles (caption con border-left verde)

**Organización:**
- Secciones claras (wrapper, figure, image, preview, responsive)
- Comentarios descriptivos
- Cascada lógica

**Calidad:** 8/10 - Simple y efectivo

**Observación:** ⚠️ Height fijo (330px) puede no adaptarse bien a todos los aspect ratios de imágenes

### 7.5 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.6 Dependencias Externas

- ACF get_field() (asume que campo existe)
- WordPress media functions (wp_get_attachment_image_url, get_post_meta)
- WordPress conditional tags (is_singular, is_admin)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ **CUMPLE**
- Clase hace UNA cosa: mostrar imagen de mapa
- Métodos bien separados (register, enqueue, render, render_preview)
- NO hay responsabilidades mezcladas
- **Impacto:** NINGUNO

**OCP:** ⚠️ **VIOLA LEVEMENTE**
- No hereda de BlockBase → Difícil extender
- Hardcoded 'large' size → No configurable
- **Impacto:** BAJO - Es un bloque muy simple

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con mejores bloques
- **Impacto:** MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Acoplado directamente a:
  - ACF get_field()
  - WordPress media functions
  - Post meta
- No hay abstracción/interfaces
- **Impacto:** BAJO - Es aceptable para bloque simple

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **TODOS los métodos <50 líneas** (EXCELENTE)
- ✅ Método más largo: render() 42 líneas
- ✅ Clase total: 126 líneas (muy bueno)

**Anidación:**
- ✅ Máximo 2 niveles (excelente)
- ✅ NO hay anidación excesiva

**Duplicación:**
- ✅ NO hay duplicación

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ✅ Nombres consistentes

**Código Sin Uso:**
- ✅ No detectado

**DocBlocks:**
- ❌ **0/4 métodos documentados** (0%)
- ✅ Header de archivo tiene descripción básica
- ❌ NO documenta estructura esperada de map_image
- ❌ NO documenta params/return types
- **Impacto:** BAJO - Código es muy simple y auto-explicativo

**Magic Values:**
- ⚠️ 'large' hardcoded (debería ser configurable)
- ⚠️ 330px height en CSS (debería ser variable)
- ⚠️ 'package' post type hardcoded (pero correcto)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ⚠️ **NO sanitiza $map_image** antes de usar
- ⚠️ **NO valida tipo** de $map_image (asume array o int)
- ✅ get_field() de ACF es seguro
- **Impacto:** BAJO - ACF ya sanitiza

**Escapado:**
- ✅ **Usa esc_url()** para image_url (línea 93)
- ✅ **Usa esc_attr()** para image_alt (línea 94)
- ✅ Escapado correcto en preview SVG
- **Impacto:** NINGUNO - Perfecto

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ✅ No hace queries directas

**XSS:**
- ✅ **TODO escapado correctamente**

**API Keys:**
- ✅ **NO hay API keys** (no usa APIs de mapas)

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ✅ **Renderizado inline simple** (aceptable para bloque tan simple)
- ✅ Lógica mínima, solo presentación
- ⚠️ Podría usar template separado para consistencia

**Acoplamiento:**
- ⚠️ Acoplamiento a ACF (get_field)
- ⚠️ Acoplamiento a WordPress media functions
- **Impacto:** BAJO - Aceptable para este bloque

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Pierde funcionalidades compartidas
- **Impacto:** MEDIO

**Caché:**
- ✅ N/A - No necesita caché (data de ACF)

**Otros:**
- ❌ **NO usa block.json** (debería para Gutenberg moderno)
- ⚠️ **NO usa EditorHelper** para detectar preview mode
- ⚠️ **Image size hardcoded** ('large' no configurable)

---

## 9. Recomendaciones de Refactorización

### Prioridad Alta

**1. Heredar de BlockBase**
- **Acción:** `class PackageMap extends BlockBase`
- **Razón:** Consistencia, funcionalidades compartidas
- **Riesgo:** MEDIO - Requiere refactorizar
- **Precauciones:**
  - Mover config a properties
  - Usar parent::register()
  - Adaptar enqueue_assets()
- **Esfuerzo:** 1 hora

**2. Validar tipo de $map_image**
- **Acción:**
  ```php
  $map_image = get_field('map_image', $post_id);

  if (!$map_image || (!is_array($map_image) && !is_numeric($map_image))) {
      return '';
  }
  ```
- **Razón:** Prevenir errores si campo tiene tipo incorrecto
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**3. Usar EditorHelper para preview mode**
- **Acción:**
  ```php
  use Travel\Blocks\Helpers\EditorHelper;

  public function render(array $attributes = [], string $content = ''): string
  {
      $is_preview = EditorHelper::is_editor();

      if ($is_preview || !is_singular('package')) {
          return $this->render_preview();
      }
      // ...
  }
  ```
- **Razón:** Detectar editor correctamente, no solo is_singular
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

### Prioridad Media

**4. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos:
  ```php
  /**
   * Render the block on the frontend
   *
   * Displays the route map image for a package. Shows a preview
   * placeholder in editor mode or when not on a package page.
   *
   * @param array  $attributes Block attributes
   * @param string $content    Block content
   * @return string HTML output
   */
  public function render(array $attributes = [], string $content = ''): string
  ```
- **Razón:** Documentación para mantenimiento
- **Riesgo:** NINGUNO
- **Esfuerzo:** 30 min

**5. Hacer image size configurable**
- **Acción:**
  ```php
  $image_size = $attributes['imageSize'] ?? 'large';
  $image_url = is_array($map_image)
      ? $map_image['url']
      : wp_get_attachment_image_url($map_image, $image_size);
  ```
- **Razón:** Flexibilidad, configuración por usuario
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**6. Convertir magic values a constantes**
- **Acción:**
  ```php
  private const POST_TYPE = 'package';
  private const IMAGE_SIZE_DEFAULT = 'large';
  private const IMAGE_HEIGHT_CSS = '330px';
  ```
- **Razón:** Mantenibilidad, claridad
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**7. Separar template a archivo**
- **Acción:** Crear `/templates/package-map.php` con el HTML
- **Razón:** Consistencia con otros bloques, separación de concerns
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

### Prioridad Baja

**8. Crear block.json**
- **Acción:** Migrar de register_block_type() a block.json
- **Razón:** Gutenberg moderno, mejor performance
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**9. Hacer height configurable en CSS**
- **Acción:**
  ```css
  .package-map-image {
      height: var(--package-map-height, 330px);
  }
  ```
- **Razón:** Flexibilidad visual
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**10. Agregar srcset/sizes para responsive images**
- **Acción:**
  ```php
  echo wp_get_attachment_image($map_image, 'large', false, [
      'alt' => $image_alt,
      'class' => 'package-map-image',
      'loading' => 'lazy',
  ]);
  ```
- **Razón:** Performance, responsive images
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

---

## 10. Plan de Acción

### Fase 1 - Alta Prioridad (Esta semana)
1. Heredar de BlockBase (1 hora)
2. Validar tipo de $map_image (15 min)
3. Usar EditorHelper (15 min)

**Total Fase 1:** 1.5 horas

### Fase 2 - Media Prioridad (Próximas 2 semanas)
4. Agregar DocBlocks (30 min)
5. Hacer image size configurable (30 min)
6. Convertir magic values a constantes (15 min)
7. Separar template a archivo (30 min)

**Total Fase 2:** 1 hora 45 min

### Fase 3 - Baja Prioridad (Cuando haya tiempo)
8. Crear block.json (30 min)
9. Hacer height configurable (15 min)
10. Agregar srcset/sizes (20 min)

**Total Fase 3:** 1 hora

**Total Refactorización Completa:** ~4 horas 15 min

**Precauciones Generales:**
- ✅ Código ya es muy limpio, refactorizar gradualmente
- ✅ SIEMPRE probar con ambos formatos de imagen (array/ID)
- ✅ SIEMPRE verificar que funciona sin imagen (empty states)
- ⚠️ NO cambiar campo ACF 'map_image'
- ⚠️ Validar que CSS funciona después de cambios

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra placeholder)
- [ ] Frontend funciona (muestra imagen real)
- [ ] Campo 'map_image' funciona

### Image Handling
- [ ] ACF Image Object (array) funciona
- [ ] Attachment ID (int) funciona
- [ ] URL se extrae correctamente
- [ ] Alt text se extrae correctamente
- [ ] Alt text por defecto se genera si no existe
- [ ] Empty state funciona (no muestra nada si no hay imagen)

### Preview Mode
- [ ] Placeholder SVG se muestra
- [ ] Mensaje instructivo aparece
- [ ] NO se muestra en frontend

### CSS
- [ ] Estilos se aplican correctamente
- [ ] Border radius funciona
- [ ] Object-fit cover funciona
- [ ] Height 330px funciona
- [ ] Responsive funciona (border-radius 4px en mobile)
- [ ] Caption preview funciona (editor)

### Seguridad
- [ ] esc_url() en image_url ✅
- [ ] esc_attr() en image_alt ✅
- [ ] Tipo de $map_image validado (si se agregó)

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] EditorHelper usado (si se agregó)
- [ ] Template separado (si se creó)
- [ ] Constantes definidas (si se agregaron)
- [ ] block.json (si se creó)

### Clean Code
- [ ] Métodos <50 líneas ✅ (ya cumple)
- [ ] Anidación <3 niveles ✅ (ya cumple)
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] No magic values (si se convirtieron a constantes)

### Performance
- [ ] CSS solo se carga en singular('package') ✅
- [ ] Lazy loading funciona ✅
- [ ] Image size 'large' optimizado ✅
- [ ] srcset/sizes (si se agregó)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Código muy simple y limpio (126 líneas)
- ✅ Todos los métodos excelentemente dimensionados (<50 líneas)
- ✅ Escapado de seguridad correcto (esc_url, esc_attr)
- ✅ Maneja ambos formatos de imagen (array/ID)
- ✅ Preview mode claro y útil
- ✅ CSS simple y responsive
- ✅ Lazy loading implementado
- ✅ NO hay API keys ni integración compleja
- ❌ NO hereda de BlockBase
- ❌ NO tiene DocBlocks (0/4 métodos)
- ⚠️ NO valida tipo de $map_image
- ⚠️ NO usa EditorHelper
- ⚠️ Magic values hardcoded

### Puntuación: 8.0/10

**Razones para la puntuación:**
- ➕ Código muy simple y claro (+2)
- ➕ Excelentemente dimensionado (+1.5)
- ➕ Escapado de seguridad correcto (+1)
- ➕ Manejo flexible de formatos (+1)
- ➕ Preview mode útil (+0.5)
- ➕ CSS limpio y responsive (+0.5)
- ➕ Lazy loading (+0.5)
- ➕ Conditional CSS loading (+0.5)
- ➕ NO tiene complejidad innecesaria (+0.5)
- ➖ NO hereda BlockBase (-0.5)
- ➖ Sin DocBlocks (-0.5)

### Fortalezas
1. **Código muy simple:** Solo 126 líneas, métodos muy cortos, fácil de entender
2. **Seguridad correcta:** Escapado perfecto (esc_url, esc_attr)
3. **Flexibilidad de formato:** Maneja ACF image object y attachment ID
4. **Preview útil:** SVG placeholder claro con instrucciones
5. **CSS optimizado:** Solo se carga cuando es necesario (singular package)
6. **Lazy loading:** Implementado correctamente
7. **Alt text inteligente:** Auto-genera si no existe
8. **Responsive design:** Border radius ajustado para mobile
9. **Early returns:** Buen manejo de casos vacíos
10. **NO hay complejidad innecesaria:** No usa APIs, no tiene JavaScript, simple y efectivo

### Debilidades
1. ❌ **NO hereda de BlockBase** - Inconsistente
2. ❌ **NO documenta** - 0/4 métodos con DocBlocks
3. ⚠️ **NO valida tipo** de $map_image antes de usar
4. ⚠️ **NO usa EditorHelper** para detectar preview mode
5. ⚠️ **Magic values** hardcoded ('large', 330px, 'package')
6. ⚠️ **NO usa template separado** - Inline en render()
7. ⚠️ **NO usa block.json** - Debería para Gutenberg moderno
8. ⚠️ **Height fijo** en CSS (330px) puede no adaptarse bien
9. ⚠️ **Image size no configurable** - Hardcoded 'large'

### Recomendación Principal

**Este es un BLOQUE EXCELENTE - Simple, limpio y bien hecho.**

**ACLARACIÓN IMPORTANTE:** Este NO es un bloque de integración con API de mapas. Solo muestra una imagen estática. No hay API keys, no hay JavaScript, no hay interactividad. Es un bloque simple y efectivo.

**Prioridad Alta (Esta semana - 1.5 horas):**
1. Heredar de BlockBase (consistencia)
2. Validar tipo de $map_image (robustez)
3. Usar EditorHelper (mejor detección de preview)

**Prioridad Media (2 semanas - 1 hora 45 min):**
4. DocBlocks (documentación)
5. Image size configurable (flexibilidad)
6. Constantes (clean code)
7. Template separado (consistencia)

**Prioridad Baja (Cuando haya tiempo - 1 hora):**
8. block.json (moderno)
9. Height configurable (flexibilidad)
10. srcset/sizes (performance)

**Esfuerzo total:** ~4 horas 15 min de refactorización

**Veredicto:** Este es un EXCELENTE BLOQUE simple. El código es limpio, directo y efectivo. Los únicos problemas son arquitectónicos menores (no hereda BlockBase, sin DocBlocks) que son fáciles de corregir. La funcionalidad es perfecta para lo que necesita hacer: mostrar una imagen de mapa. **PRIORIDAD: Refactorización menor esta semana, código ya está muy bien.**

### Dependencias Identificadas

**ACF:**
- `map_image` field (image object o attachment ID)
- Asume que existe (NO lo registra)

**WordPress Media:**
- wp_get_attachment_image_url() (obtener URL de imagen)
- get_post_meta() (obtener alt text de attachment)

**WordPress Conditional Tags:**
- is_singular('package') (detectar contexto)
- is_admin() (conditional CSS loading)

**JavaScript:**
- ❌ **NO tiene JavaScript** (no necesario, solo imagen estática)

**CSS:**
- package-map.css (46 líneas)
- Simple y responsive

**NO TIENE:**
- ❌ API de mapas (Leaflet, Google Maps, Mapbox, etc.)
- ❌ API keys
- ❌ JavaScript de interactividad
- ❌ Llamadas AJAX
- ❌ Geolocalización
- ❌ Markers/Pins interactivos

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** BAJA - Refactorización menor (heredar BlockBase, validación, EditorHelper)
**Próxima revisión:** Después de refactorización Fase 1
