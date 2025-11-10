# Auditoría: StaticCTA (ACF)

**Fecha:** 2025-11-09
**Bloque:** 11/15 ACF
**Tiempo:** 25 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `static-cta`
- **Namespace ACF:** `acf/static-cta`
- **Campos ACF:** `title`, `subtitle`, `background_type`, `background_image`, `background_color`, `overlay_opacity`, `buttons`
- **Template path:** `/templates/static-cta.php`
- **Sub-fields en repeater buttons:** `text`, `url`, `style`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **SÍ hereda de BlockBase** ✅ (como PostsCarousel, HeroSection, SideBySideCards)
- **NO usa ContentQueryHelper** (es bloque estático, no necesita queries)
- Template simple (77 líneas)
- Solo CSS, sin JavaScript
- Bloque CTA estático clásico

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/StaticCTA.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/templates/static-cta.php`
**Assets:**
- CSS: `/assets/blocks/static-cta.css`
- JS: Ninguno

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

**Dependencias:**
- ✅ BlockBase (extiende correctamente)
- ❌ NO usa ContentQueryHelper (es estático, no lo necesita)
- Sin dependencias externas

---

## 2. Propósito y Funcionalidad

**Descripción:** Bloque Call-to-Action con background (imagen/color/gradiente), título, subtítulo y botones. Diseñado para conversión y promociones.

**Diferencia con otros bloques:**
- HeroSection: Hero completo con más opciones, altura variable
- HeroCarousel: Hero con carousel de slides
- StaticCTA: **CTA simple** enfocado en conversión, background + botones

**Inputs (ACF):**

**Contenido:**
- `title` (text, required, default: "Ready to Start Your Adventure?")
- `subtitle` (textarea, 2 rows, default: "Book your dream Peru tour today...")

**Background:**
- `background_type` (radio: image/color/gradient, default: image)
- `background_image` (image, array, condicional si type=image)
- `background_color` (color_picker, default: #e74c3c, condicional si type=color)
- `overlay_opacity` (range 0-100%, step 10, default: 50%, condicional si type=image)

**Buttons:**
- `buttons` (repeater, min: 1, max: 2):
  - `text` (text, required)
  - `url` (url, required)
  - `style` (select: primary/secondary/outline, default: primary)

**Outputs:**
- Section con background (imagen/color/gradiente)
- Overlay opcional (si imagen)
- Título y subtítulo centrados
- Botones CTA (1-2)
- Responsive automático

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ✅ **BlockBase** (correcto)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades (heredadas de BlockBase):**
```
protected string $name = 'static-cta';
protected string $title = 'Static CTA';
protected string $description = 'Call-to-action section with background and buttons';
protected string $category = 'travel';
protected string $icon = 'megaphone';
protected array $keywords = ['cta', 'call to action', 'banner', 'promo'];
protected string $mode = 'preview';
protected array $supports = ['align' => ['full', 'wide'], 'mode', 'multiple'];
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor con configuración (14 líneas)
2. register(): void - Registra bloque y campos ACF (142 líneas)
3. render($block, $content, $is_preview, $post_id): void - Renderiza bloque (26 líneas)
4. enqueue_assets(): void - Encola CSS (8 líneas)
```

**Métodos Privados:**
```
Ninguno
```

**Total:** 237 líneas (bloque simple y directo)

---

## 4. Registro del Bloque

**Método:** `parent::register()` + `acf_add_local_field_group` (hereda de BlockBase)

**Configuración:**
- name: `static-cta`
- title: "Static CTA"
- category: `travel`
- icon: `megaphone`
- keywords: ['cta', 'call to action', 'banner', 'promo']
- render_callback: Heredado de BlockBase
- supports: align (full/wide), mode, multiple

**Enqueue Assets:**
- CSS: `/assets/blocks/static-cta.css` (siempre)
- Método: `enqueue_assets()` separado ✅
- Sin JavaScript (no necesario)

**Block.json:** No existe (usa registro ACF)

**Campos ACF:** ✅ Registrados en `register()` con estructura plana (sin tabs)

---

## 5. Campos ACF

**Definición:** ✅ `acf_add_local_field_group` en método `register()`

**Estructura:**
- `title` (text, required)
- `subtitle` (textarea)
- `background_type` (radio: image/color/gradient)
- `background_image` (image, condicional)
- `background_color` (color_picker, condicional)
- `overlay_opacity` (range, condicional)
- `buttons` (repeater 1-2):
  - `text` (text, required)
  - `url` (url, required)
  - `style` (select: primary/secondary/outline)

**Conditional Logic:**
- `background_image` solo si `background_type == 'image'`
- `background_color` solo si `background_type == 'color'`
- `overlay_opacity` solo si `background_type == 'image'`

**Validación:**
- `title` required
- `text` y `url` de botones required
- Min 1 botón, max 2
- Overlay 0-100%

**Prefijos:**
- ✅ Consistentes: `field_cta_*` para todos los campos

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Get campos ACF directamente (líneas 197-203)
2. Prepara $data array con todas las variables (líneas 206-216)
3. Llama `load_template('static-cta', $data)` ✅

**Variables al Template:**
- ✅ Pasa $data array explícitamente (no usa $GLOBALS)
- ✅ Template extrae variables con nombres claros
- ✅ Separación clara entre lógica y presentación

**Manejo de Errores:**
- ⚠️ **NO tiene try-catch** en render()
- ⚠️ Template NO valida si hay título/botones antes de mostrar
- Impacto: BAJO - ACF devuelve valores default

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:**
- ❌ No usa JavaScript
- Todo es CSS estático

**REST API:** No usa

**Hooks Propios:**
- Ninguno (usa hooks de BlockBase)

**Dependencias Externas:**
- ❌ Ninguna
- ✅ Todo local (CSS)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ **CUMPLE**
- Clase: Configuración y registro
- Template: Presentación
- Separación clara
- Impacto: N/A

**OCP:** ✅ **CUMPLE**
- Hereda de BlockBase (extensible)
- Configuración por ACF fields

**LSP:** ✅ **CUMPLE**
- Hereda correctamente de BlockBase
- Implementa métodos esperados

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ✅ **CUMPLE**
- Depende de abstracción (BlockBase)

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ __construct(): 14 líneas (excelente)
- ✅ register(): 142 líneas (largo pero es fields ACF, aceptable)
- ✅ render(): 26 líneas (excelente)
- ✅ enqueue_assets(): 8 líneas (excelente)
- **Total:** 237 líneas (simple y claro)

**Anidación:**
- ✅ <3 niveles en todos los métodos
- ✅ Template simple

**Duplicación:**
- ✅ **NO hay duplicación significativa**
- Funcionalidad única: CTA estático simple

**Nombres:**
- ✅ Block name claro: `static-cta`
- ✅ Campos ACF descriptivos
- ✅ Prefijo `field_cta_` consistente

**Código Sin Uso:**
- ✅ No detectado
- ✅ Todo el código es necesario

**DocBlocks:**
- ✅ Header class completo (líneas 2-9)
- ✅ Template bien documentado (líneas 2-16)
- ✅ Métodos tienen DocBlocks (register, render, enqueue_assets)
- **Excelente documentación**

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ `get_field()` con valores default
- ✅ No hace queries

**Escapado:**
- ✅ Template usa `esc_url()` (líneas 36, 65)
- ✅ Template usa `esc_html()` (líneas 54, 58, 66)
- ✅ Template usa `esc_attr()` (líneas 19, 22, 23, 29, 38, 44, 47, 65)
- ⚠️ **Background-image inline** (línea 36)
  - `background-image: url(' . esc_url($background_image['url']) . ');`
  - ✅ Usa esc_url() pero dentro de CSS inline
  - Impacto: BAJO - esc_url() es suficiente
- ⚠️ **Gradient hardcoded** (línea 40)
  - `background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);`
  - Sin escapado (pero es valor hardcoded seguro)
  - Impacto: N/A

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No hace queries

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 11
  - Impacto: BAJO (funciona pero no sigue convención exacta)

**Separación MVC:**
- ✅ **EXCELENTE SEPARACIÓN MVC**
  - Modelo: ACF fields
  - Vista: Template PHP
  - Controlador: render() en clase
  - Template NO hace queries ✅
  - Impacto: N/A

**Acoplamiento:**
- ✅ **Acoplamiento BAJO**
  - Usa BlockBase (abstracción)
  - Template recibe datos por $data
  - Sin dependencias externas
  - Impacto: N/A

**Herencia:**
- ✅ **Hereda correctamente de BlockBase**
  - Consistente con otros bloques bien hechos
  - Usa parent::register()
  - Usa load_template()
  - Impacto: N/A

**Otros:**
- ✅ Prefijos consistentes (`field_cta_*`)
- ✅ Pasa datos por $data (no usa $GLOBALS)
- ✅ Registra campos ACF correctamente
- ⚠️ **NO tiene try-catch en render()** (menor)

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque está MUY BIEN implementado. Solo optimizaciones menores.**

### Prioridad Media

**1. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\ACF` a `Travel\Blocks\ACF`
- **Razón:** Seguir PSR-4 estrictamente
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:** Verificar que composer autoload mapea correctamente
- **Esfuerzo:** 30 min

**2. Agregar try-catch en render()**
- **Acción:** Envolver render() en try-catch como otros bloques
- **Razón:** Manejo de errores consistente
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min
- **Código:**
  ```php
  public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
  {
      try {
          // Get field values
          $title = get_field('title');
          // ... resto del código ...

          // Load template
          $this->load_template('static-cta', $data);
      } catch (\Exception $e) {
          if (defined('WP_DEBUG') && WP_DEBUG) {
              echo '<div style="padding: 20px; background: #ffebee; border: 2px solid #f44336;">';
              echo '<h3>Error en Static CTA</h3>';
              echo '<p>' . esc_html($e->getMessage()) . '</p>';
              echo '</div>';
          }
      }
  }
  ```

### Prioridad Baja

**3. Agregar validación en template**
- **Acción:** Validar que hay título o botones antes de mostrar section
- **Razón:** Prevenir section vacía
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min
- **Código:**
  ```php
  // Al inicio del template
  if (empty($title) && empty($buttons)) {
      if (defined('WP_DEBUG') && WP_DEBUG) {
          echo '<p>Static CTA: Configure título y botones</p>';
      }
      return;
  }
  ```

**4. Hacer gradiente configurable**
- **Acción:** Agregar campo ACF para gradiente personalizado
- **Razón:** Más flexibilidad
- **Riesgo:** MEDIO - Añade complejidad
- **Precauciones:** Validar formato de gradiente
- **Esfuerzo:** 1 hora
- **Recomendación:** ⚠️ Solo si se necesita frecuentemente

**5. Agregar opción de altura personalizada**
- **Acción:** Agregar campo `min_height` (range 300-800px)
- **Razón:** Flexibilidad de diseño
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min
- **Recomendación:** ⚠️ Solo si se necesita

---

## 10. Plan de Acción

**Decisión Principal:** ✅ **MANTENER** - Bloque bien implementado

### Optimizaciones Recomendadas
1. Corregir namespace (30 min) - Prioridad Media
2. Agregar try-catch (10 min) - Prioridad Media
3. Agregar validación en template (10 min) - Prioridad Baja

**Total:** 50 min de optimizaciones

**Precauciones Generales:**
- ⛔ NO cambiar nombres de campos ACF (rompe contenido existente)
- ⛔ NO cambiar block name (rompe contenido existente)
- ✅ Template es simple y funcional
- ✅ No necesita ContentQueryHelper (es estático)

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [X] Bloque aparece en catálogo
- [X] Se puede insertar correctamente
- [X] Preview funciona en editor
- [X] Frontend funciona correctamente
- [X] Campos ACF aparecen en editor

### Background
- [X] Background imagen funciona
- [X] Background color funciona
- [X] Background gradiente funciona
- [X] Overlay funciona (solo con imagen)
- [X] Overlay opacity funciona

### Contenido
- [X] Título se muestra
- [X] Subtítulo se muestra
- [X] Botones funcionan (1-2)
- [X] Estilos de botones funcionan (primary/secondary/outline)
- [X] URLs de botones funcionan

### Arquitectura
- [X] Hereda de BlockBase
- [X] Template recibe $data (no $GLOBALS)
- [X] load_template() funciona
- [ ] Namespace correcto (⚠️ pendiente)
- [ ] Try-catch en render() (⚠️ pendiente)

### Seguridad
- [X] Template escapa URLs
- [X] Template escapa HTML
- [X] Template escapa atributos
- [X] Background-image escapado

### Clean Code
- [X] DocBlocks presentes
- [X] Métodos cortos
- [X] Sin duplicación
- [X] Código simple y claro

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Hereda de BlockBase correctamente
- ✅ Registra campos ACF con conditional logic
- ✅ Template simple y claro
- ✅ Separación MVC excelente
- ✅ Escapado de seguridad correcto
- ✅ Sin JavaScript (no necesario)
- ✅ Sin dependencias externas
- ✅ Funcionalidad clara (CTA simple)
- ✅ DocBlocks completos
- ✅ Prefijos consistentes
- ⚠️ Namespace incorrecto (menor)
- ⚠️ Sin try-catch en render() (menor)

### Puntuación: 8.5/10

**Fortalezas:**
- Arquitectura MVC excelente
- Herencia correcta de BlockBase
- Campos ACF bien estructurados con conditional logic
- Template muy simple y claro (77 líneas)
- Escapado de seguridad completo
- Funcionalidad clara y enfocada (CTA)
- Sin dependencias externas
- Sin JavaScript innecesario
- DocBlocks completos
- Prefijos consistentes
- Código muy simple (237 líneas)
- Bloque ligero y rápido

**Debilidades:**
- ⚠️ Namespace `Travel\Blocks\Blocks\ACF` (debería ser `Travel\Blocks\ACF`)
- ⚠️ Sin try-catch en render()
- ⚠️ Template no valida contenido vacío
- ⚠️ Gradiente hardcoded (no configurable)

**Recomendación:**
✅ **MANTENER** - Bloque bien implementado

**Razones:**
1. Arquitectura MVC excelente
2. Herencia correcta de BlockBase
3. Campos ACF claros y simples
4. Template simple y seguro
5. Funcionalidad enfocada (CTA)
6. Sin problemas críticos
7. Código muy simple (237 líneas)
8. Solo optimizaciones menores opcionales

**Acción recomendada:**
1. Corregir namespace (30 min)
2. Agregar try-catch (10 min)
3. Agregar validación template (10 min)

**Comparación:**
| Aspecto | StaticHero | StaticCTA |
|---------|-----------|-----------|
| Líneas | 63 | 237 |
| Hereda BlockBase | ❌ No | ✅ Sí |
| Registra ACF fields | JSON | ✅ PHP |
| Template queries | ❌ No | ✅ No |
| DocBlocks | ❌ No | ✅ Sí |
| $GLOBALS | ❌ Sí | ✅ No |
| Try-catch | ❌ No | ⚠️ No |
| Conditional Logic | ❌ No | ✅ Sí |
| **Puntuación** | 4/10 | **8.5/10** |
| **Recomendación** | ⚠️ Refactorizar | ✅ **MANTENER** |

**Veredicto:** Bloque muy bien implementado. Arquitectura excelente, código simple y claro, seguridad correcta. Es un buen ejemplo de bloque ACF estático simple. Solo tiene optimizaciones menores opcionales (namespace, try-catch). **Perfecto para CTAs simples.**

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** BAJA - Solo optimizaciones menores opcionales
