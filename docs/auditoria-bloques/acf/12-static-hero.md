# Auditoría: StaticHero (ACF)

**Fecha:** 2025-11-09
**Bloque:** 12/15 ACF
**Tiempo:** 30 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `acf-gbr-static-hero`
- **Namespace ACF:** `acf/acf-gbr-static-hero`
- **Campos ACF:** `sh_title`, `sh_subtitle`, `sh_background` (definidos en JSON)
- **Template path:** `/src/Blocks/StaticHero/template.php` (⚠️ ruta diferente)
- **Global variable:** `$GLOBALS['sh_block_wrapper_attributes']` (usado en template)
- **ACF JSON:** `/acf-json/group_acfgbr_static_hero.json`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (como PostsListAdvanced, FlexibleGridCarousel, HeroCarousel)
- **Campos ACF en JSON** (no en PHP) - `/acf-json/group_acfgbr_static_hero.json`
- Template hace precarga de imagen en `wp_head` (⚠️ puede causar problemas)
- Template con estilos inline hardcoded (no usa CSS externo completo)
- MUY simple (63 líneas totales en clase)
- Usa `add_action('wp_head')` dentro de template (⚠️ anti-pattern)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/StaticHero.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/src/Blocks/StaticHero/template.php` (⚠️ ruta diferente a otros bloques)
**Assets:**
- CSS: `/assets/blocks/StaticHero/style.css`
- JS: Ninguno

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- ACF JSON para campos (en lugar de registro en PHP)

**Campos ACF:**
- Definidos en: `/acf-json/group_acfgbr_static_hero.json`
- NO registrados en código PHP

---

## 2. Propósito y Funcionalidad

**Descripción:** Hero estático simple con título, subtítulo e imagen de background. Fullscreen (min-height: 100vh). Optimizado con precarga de imagen.

**Diferencia con otros bloques:**
- HeroSection: Hero completo con más opciones, hereda BlockBase
- HeroCarousel: Hero con carousel de slides
- StaticHero: **Hero MUY simple**, sin herencia, sin opciones

**Inputs (ACF - desde JSON):**
- `sh_title` (text) - Título del hero
- `sh_subtitle` (text) - Subtítulo del hero
- `sh_background` (image, return_format: array) - Imagen de background

**Outputs:**
- Section fullscreen (min-height: 100vh)
- Background image cover
- Overlay oscuro (rgba(0,0,0,0.4) hardcoded)
- Título y subtítulo centrados
- Precarga de imagen en `<head>`
- Tag `<noscript>` con imagen

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico crítico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```
private string $name = 'acf-gbr-static-hero';
```

**Métodos Públicos:**
```
1. register(): void - Registra bloque ACF (34 líneas)
2. render($block, $content, $is_preview, $post_id): void - Renderiza bloque (18 líneas)
```

**Métodos Privados:**
```
Ninguno
```

**Total:** 63 líneas (el segundo bloque MÁS simple, después de PostsListAdvanced)

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (directo, no hereda de BlockBase)

**Configuración:**
- name: `acf-gbr-static-hero` (⚠️ prefix confuso)
- title: "Static Hero"
- category: `travel`
- icon: `slides`
- keywords: ['hero', 'banner']
- render_callback: `[$this, 'render']`
- enqueue_assets: closure inline (no método separado)
- supports: align, mode, jsx, spacing, color, typography, anchor, customClassName

**Enqueue Assets (inline closure):**
- CSS: `/assets/blocks/StaticHero/style.css` (ruta con mayúsculas)
- Encolado en closure inline (líneas 39-41)
- ⚠️ No hay método `enqueue_assets()` separado

**Block.json:** No existe

**Campos ACF:** ❌ **NO REGISTRA CAMPOS EN PHP** (usa JSON en `/acf-json/`)

---

## 5. Campos ACF

**Definición:** ✅ Definidos en JSON `/acf-json/group_acfgbr_static_hero.json`

**Estructura JSON:**
```json
{
  "key": "group_acfgbr_static_hero",
  "title": "Static Hero",
  "fields": [
    {
      "key": "field_sh_title",
      "label": "Title",
      "name": "sh_title",
      "type": "text"
    },
    {
      "key": "field_sh_subtitle",
      "label": "Subtitle",
      "name": "sh_subtitle",
      "type": "text"
    },
    {
      "key": "field_sh_background",
      "label": "Background Image",
      "name": "sh_background",
      "type": "image",
      "return_format": "array"
    }
  ],
  "location": [[{"param": "block", "operator": "==", "value": "acf/acf-gbr-static-hero"}]]
}
```

**Campos:**
- `sh_title` (text) - Sin defaults, validación, etc.
- `sh_subtitle` (text) - Sin defaults, validación, etc.
- `sh_background` (image) - Sin opciones avanzadas

**Problemas:**
- ❌ **Campos MÍNIMOS** (sin required, defaults, instructions, max_length, etc.)
- ❌ **NO hay conditional logic**
- ❌ **NO hay validación**
- ⚠️ JSON funciona pero es menos flexible que PHP

**Prefijos:**
- ✅ Consistentes: `sh_*` para todos los campos

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Get block wrapper attributes: `get_block_wrapper_attributes()` (líneas 46-48)
2. Detecta si $is_preview es WP_Block (líneas 51-54)
3. Guarda block_wrapper_attributes en `$GLOBALS` (línea 57)
4. Include template directamente (líneas 59-60)

**Template hace TODO el trabajo:**
- Get campos ACF: `get_field('sh_title')`, `get_field('sh_subtitle')`, `get_field('sh_background')` (líneas 2-4)
- Extrae URL de imagen (línea 5)
- Genera ID y clases (líneas 6-7)
- Lee `$GLOBALS['sh_block_wrapper_attributes']` (línea 8)
- ⚠️ **Agrega hook `wp_head` DENTRO del template** (líneas 11-15)
  - Precarga de imagen
  - Esto es un ANTI-PATTERN (hook dentro de template)
- Renderiza HTML con estilos inline (líneas 18-49)

**Variables al Template:**
- ❌ **NO pasa variables explícitamente**
- ❌ Template lee ACF fields directamente
- ❌ Template lee `$GLOBALS['sh_block_wrapper_attributes']`
- ❌ Template asume que $block variable está disponible

**Manejo de Errores:**
- ❌ **NO tiene try-catch**
- ❌ **NO valida si template existe**
- ❌ **NO tiene logging**
- ⚠️ Template tiene defaults para title/subtitle (líneas 2-3)
  - `$title = get_field('sh_title') ?: 'Título por defecto';`
  - Pero no valida imagen

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:**
- ❌ No usa JavaScript

**REST API:** No usa

**Hooks Propios:**
- ⚠️ **`add_action('wp_head')` dentro de template** (línea 11)
  - Esto es un ANTI-PATTERN
  - Hook se agrega cada vez que se renderiza el bloque
  - Puede causar precargas duplicadas si hay múltiples instancias
  - Impacto: MEDIO - Funciona pero no es buena práctica

**Dependencias Externas:**
- ❌ Ninguna

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ❌ **VIOLA**
- Template hace get_field() directamente (debería recibir datos)
- Template agrega hooks (debería estar en clase)
- Template genera IDs y clases (debería estar en clase)
- Impacto: ALTO

**OCP:** ⚠️ Difícil de extender (no hereda de BlockBase)

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con otros bloques ACF
- Impacto: ALTO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ❌ **VIOLA**
- Template acoplado a get_field()
- Template acoplado a $GLOBALS
- Template acoplado a $block variable
- Impacto: ALTO

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ register(): 34 líneas (bien)
- ✅ render(): 18 líneas (excelente)
- ✅ **Total:** 63 líneas (muy simple)
- ⚠️ Template: 50 líneas (pero tiene lógica que debería estar en clase)

**Anidación:**
- ✅ <3 niveles en clase
- ✅ Template simple

**Duplicación:**
- ⚠️ **POSIBLE duplicación** con HeroSection/HeroCarousel
  - Funcionalidad similar: Hero con background
  - Diferencia: Más simple (solo estático)
  - ¿Es necesario tener 3 bloques de hero?
  - Impacto: MEDIO

**Nombres:**
- ⚠️ **Block name largo y confuso:** `acf-gbr-static-hero`
  - Prefix `acf-gbr` inconsistente con otros bloques
  - ¿Qué significa `gbr`?
  - Otros bloques usan nombres más simples
- ✅ Prefix `sh_` es claro

**Código Sin Uso:**
- ✅ No detectado

**DocBlocks:**
- ❌ **NO tiene DocBlocks** en métodos
- ❌ NO tiene header class (solo namespace en línea 3)
- ❌ Template tiene comentarios pero no PHPDoc
- Impacto: ALTO - 0/2 métodos documentados

### 8.3 Problemas de Seguridad

**Sanitización:**
- ⚠️ `get_field()` sin validación
- ⚠️ No valida que `sh_background` tenga URL

**Escapado:**
- ✅ Template usa `esc_url()` (líneas 5, 13, 47)
- ✅ Template usa `esc_html()` (líneas 41, 42)
- ✅ Template usa `esc_attr()` (líneas 6, 7, 19, 20, 47)
- ⚠️ **Background-image inline** (línea 23)
  - `background-image:url('<?php echo $bg_url; ?>');`
  - ❌ **SIN escapado** (solo esc_url en línea 5 al asignar, no al usar)
  - Debería ser: `background-image:url('<?php echo esc_url($bg_url); ?>');`
  - Impacto: MEDIO - Potencial XSS

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
  - Ubicación: Línea 3
  - Impacto: BAJO (funciona pero no sigue convención)

**Separación MVC:**
- ❌ **VIOLACIÓN GRAVE MVC**
  - Template hace get_field() directamente (debería recibir datos)
  - Template agrega hooks (debería estar en clase)
  - Template genera IDs y clases (debería estar en clase)
  - Clase solo encola assets
  - Impacto: ALTO

**Acoplamiento:**
- ❌ **Acoplamiento ALTO**
  - Template acoplado a get_field()
  - Template acoplado a $GLOBALS
  - Template acoplado a $block variable (no pasada)
  - Impacto: ALTO

**Herencia:**
- ❌ **NO hereda de BlockBase** (problema crítico)
  - Inconsistente con bloques bien hechos (SideBySideCards, StaticCTA, etc.)
  - Duplica funcionalidad
  - Impacto: ALTO

**Otros:**
- ❌ **Campos ACF en JSON** (no en PHP)
  - Menos flexible
  - Más difícil de mantener
  - Impacto: MEDIO
- ❌ **$GLOBALS para pasar datos** (anti-pattern)
  - Línea 57: `$GLOBALS['sh_block_wrapper_attributes']`
  - Template lee de $GLOBALS (línea 8)
  - Debería pasar por $data
  - Impacto: MEDIO
- ❌ **add_action('wp_head') dentro de template** (anti-pattern grave)
  - Línea 11 del template
  - Hook se agrega cada vez que renderiza
  - Puede causar duplicados
  - Impacto: ALTO
- ⚠️ **Ruta de template diferente:** `/src/Blocks/StaticHero/template.php`
  - Otros bloques usan `/templates/`
  - Inconsistente
  - Impacto: BAJO
- ⚠️ **Ruta de CSS con mayúsculas:** `/assets/blocks/StaticHero/style.css`
  - Otros bloques usan minúsculas
  - Inconsistente
  - Impacto: BAJO
- ❌ **Estilos inline hardcoded en template** (líneas 21-35)
  - Debería estar en CSS externo
  - Dificulta personalización
  - Impacto: MEDIO

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque tiene PROBLEMAS GRAVES. Considerar deprecar o refactorizar completamente.**

### Prioridad CRÍTICA

**1. 🚨 DECIDIR: Deprecar o Refactorizar**
- **Análisis:**
  - ¿Se usa en producción? Verificar con: `grep -r "acf-gbr-static-hero"`
  - ¿Es necesario? Ya existen HeroSection y HeroCarousel
  - Diferencias: Este es MÁS simple (sin opciones)
- **Opciones:**
  - **A) DEPRECAR:** Si HeroSection cubre necesidades (recomendado)
  - **B) REFACTORIZAR:** Si se usa mucho y es necesario
- **Recomendación:** **DEPRECAR** - Duplicación funcional con HeroSection
- **Esfuerzo:** Variable (1 hora si deprecar, 6+ horas si refactorizar completo)

**2. 🚨 Eliminar add_action dentro de template (si se mantiene)**
- **Acción:** Mover precarga de imagen a clase:
  ```php
  // En clase, agregar método:
  public function preload_hero_image(): void
  {
      $bg = get_field('sh_background');
      if (is_array($bg) && isset($bg['url'])) {
          echo '<link rel="preload" as="image" href="' . esc_url($bg['url']) . '" fetchpriority="high" importance="high">';
      }
  }

  // En render(), antes de include template:
  add_action('wp_head', [$this, 'preload_hero_image'], 1);
  ```
- **Razón:** Anti-pattern grave (hook dentro de template)
- **Riesgo:** MEDIO - Cambia flujo
- **Precauciones:** Verificar que hook se agrega solo una vez
- **Esfuerzo:** 30 min

**3. 🚨 Escapar background-image inline**
- **Acción:** En template línea 23:
  ```php
  background-image:url('<?php echo esc_url($bg_url); ?>');
  ```
- **Razón:** Potencial XSS
- **Riesgo:** BAJO - Solo cambio de escapado
- **Esfuerzo:** 5 min

### Prioridad Alta (solo si se mantiene y NO se depreca)

**4. Heredar de BlockBase**
- **Acción:** Cambiar `class StaticHero extends BlockBase`
- **Razón:** Consistencia, evita duplicación
- **Riesgo:** ALTO - Requiere refactorizar todo
- **Precauciones:**
  - Mover configuración a __construct()
  - Usar parent::register()
  - Usar load_template() en lugar de include
  - Pasar datos por $data
- **Esfuerzo:** 3 horas

**5. Migrar campos ACF de JSON a PHP**
- **Acción:** Crear método `register_fields()` en clase:
  ```php
  private function register_fields(): void
  {
      acf_add_local_field_group([
          'key' => 'group_static_hero',
          'title' => 'Static Hero - Settings',
          'fields' => [
              [
                  'key' => 'field_sh_title',
                  'label' => __('Title', 'travel-blocks'),
                  'name' => 'sh_title',
                  'type' => 'text',
                  'required' => 1,
                  'default_value' => __('Welcome', 'travel-blocks'),
                  'maxlength' => 100,
              ],
              [
                  'key' => 'field_sh_subtitle',
                  'label' => __('Subtitle', 'travel-blocks'),
                  'name' => 'sh_subtitle',
                  'type' => 'text',
                  'maxlength' => 200,
              ],
              [
                  'key' => 'field_sh_background',
                  'label' => __('Background Image', 'travel-blocks'),
                  'name' => 'sh_background',
                  'type' => 'image',
                  'return_format' => 'array',
                  'required' => 1,
                  'preview_size' => 'large',
              ],
          ],
          'location' => [[['param' => 'block', 'operator' => '==', 'value' => 'acf/acf-gbr-static-hero']]],
      ]);
  }
  ```
- **Razón:** Más flexible, mejor validación
- **Riesgo:** MEDIO - Eliminar JSON puede romper
- **Precauciones:** Probar que campos aparecen
- **Esfuerzo:** 1 hora

**6. Mover lógica de template a clase**
- **Acción:** Template debe solo recibir datos:
  ```php
  // En render():
  $bg = get_field('sh_background');
  $bg_url = is_array($bg) && isset($bg['url']) ? $bg['url'] : '';
  $title = get_field('sh_title') ?: __('Welcome', 'travel-blocks');
  $subtitle = get_field('sh_subtitle') ?: '';
  $block_id = 'static-hero-' . ($block['id'] ?? uniqid());
  $classes = ['acf-gbr-static-hero', 'align' . ($block['align'] ?? 'wide')];

  $data = [
      'title' => $title,
      'subtitle' => $subtitle,
      'bg_url' => $bg_url,
      'block_id' => $block_id,
      'classes' => implode(' ', $classes),
  ];

  $this->load_template('static-hero', $data);
  ```
- **Razón:** Violación MVC
- **Riesgo:** MEDIO - Cambia template
- **Esfuerzo:** 1 hora

**7. Eliminar uso de $GLOBALS**
- **Acción:** Pasar block_wrapper_attributes por $data (como #6)
- **Razón:** $GLOBALS es anti-pattern
- **Riesgo:** BAJO
- **Esfuerzo:** Incluido en #6

**8. Mover estilos inline a CSS externo**
- **Acción:** Extraer estilos de líneas 21-35 del template a `/assets/blocks/StaticHero/style.css`
  - Mantener solo estilos dinámicos (background-image, background-color)
- **Razón:** Mejor mantenibilidad, personalización
- **Riesgo:** MEDIO - Cambia cómo se aplican estilos
- **Esfuerzo:** 1 hora

### Prioridad Media

**9. Agregar DocBlocks**
- **Acción:** Agregar PHPDoc a todos los métodos
- **Razón:** Documentación, mantenibilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**10. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\ACF` a `Travel\Blocks\ACF`
- **Razón:** Seguir PSR-4
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Esfuerzo:** 30 min

**11. Consistencia en rutas**
- **Acción:**
  - Cambiar template de `/src/Blocks/StaticHero/template.php` a `/templates/static-hero.php`
  - Cambiar CSS de `/assets/blocks/StaticHero/style.css` a `/assets/blocks/static-hero.css`
- **Razón:** Consistencia con otros bloques
- **Riesgo:** MEDIO - Cambia rutas
- **Esfuerzo:** 30 min

### Prioridad Baja

**12. Renombrar block name**
- **Acción:** Cambiar de `acf-gbr-static-hero` a `static-hero`
- **Razón:** Nombre más claro, sin prefix confuso
- **Riesgo:** CRÍTICO - Rompe contenido existente
- **Precauciones:** Solo si no hay contenido en producción
- **Esfuerzo:** Variable

**13. Agregar más opciones (si se mantiene)**
- **Acción:** Agregar campos:
  - `overlay_opacity` (como StaticCTA)
  - `min_height` (custom height)
  - `text_alignment` (left/center/right)
  - `button` (CTA opcional)
- **Razón:** Más flexibilidad
- **Riesgo:** MEDIO - Añade complejidad
- **Esfuerzo:** 2 horas
- **Recomendación:** ⚠️ O simplemente usar HeroSection

---

## 10. Plan de Acción

**Decisión Principal:** ¿Mantener o Deprecar?

### Opción A: DEPRECAR (Recomendado)
1. Verificar uso en producción: `grep -r "acf-gbr-static-hero"` o `wp db query "SELECT * FROM wp_posts WHERE post_content LIKE '%acf-gbr-static-hero%'"`
2. Si no se usa: Eliminar directamente
3. Si se usa: Migrar a `HeroSection` (que tiene más opciones), eliminar

**Razones para deprecar:**
- Funcionalidad duplicada con HeroSection
- HeroSection es superior (hereda BlockBase, más opciones)
- Problemas arquitectónicos graves
- Refactorización completa requeriría 6+ horas

### Opción B: MANTENER (Solo si se usa mucho)

**Si se decide mantener:**

**Fase 1 - Crítica (obligatoria):**
1. 🚨 Eliminar add_action de template (30 min)
2. 🚨 Escapar background-image (5 min)

**Fase 2 - Alta prioridad (recomendada):**
3. Heredar de BlockBase (3 horas)
4. Migrar campos ACF a PHP (1 hora)
5. Mover lógica de template a clase (1 hora)
6. Eliminar $GLOBALS (incluido en #5)
7. Mover estilos a CSS (1 hora)

**Fase 3 - Media prioridad (opcional):**
8. Agregar DocBlocks (15 min)
9. Corregir namespace (30 min)
10. Consistencia en rutas (30 min)

**Total refactorización completa:** ~7 horas

**Recomendación:** DEPRECAR - No vale la pena 7 horas cuando HeroSection ya existe

**Precauciones Generales:**
- ⛔ NO usar sin fix críticos (add_action, escapado)
- ⛔ NO cambiar block name sin migración
- ⛔ NO eliminar JSON sin migrar campos a PHP primero
- ✅ Verificar uso: `wp db query "SELECT * FROM wp_posts WHERE post_content LIKE '%acf-gbr-static-hero%'"`

---

## 11. Checklist Post-Refactorización

### Funcionalidad (si se mantiene)
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview funciona en editor
- [ ] Frontend funciona correctamente
- [ ] Campos ACF aparecen en editor

### Hero
- [ ] Background image funciona
- [ ] Título se muestra
- [ ] Subtítulo se muestra
- [ ] Fullscreen (100vh) funciona
- [ ] Overlay funciona
- [ ] Precarga de imagen funciona (sin duplicados)

### Arquitectura (si se refactoriza)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] Campos ACF en PHP (si se migró de JSON)
- [ ] Template recibe $data (si se cambió)
- [ ] load_template() funciona (si se cambió)
- [ ] $GLOBALS eliminado (si se cambió)
- [ ] add_action fuera de template (crítico)

### Seguridad
- [ ] Background-image escapado (crítico)
- [ ] Template escapa todos los campos

### Deprecación (si se depreca)
- [ ] Decisión tomada (deprecar vs mantener)
- [ ] Migración ejecutada (si se deprecó)
- [ ] Bloque eliminado (si se deprecó)
- [ ] Contenido migrado a HeroSection (si se deprecó)

### Clean Code (si se mantiene)
- [ ] DocBlocks agregados (si se mantiene)
- [ ] Namespace correcto (si se cambió)
- [ ] Rutas consistentes (si se cambió)
- [ ] Estilos en CSS externo (si se cambió)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ❌ **NO hereda de BlockBase** (problema arquitectónico grave)
- ⚠️ Campos ACF en JSON (menos flexible que PHP)
- ❌ Template hace get_field() directamente (violación MVC)
- ❌ **add_action('wp_head') dentro de template** (anti-pattern grave)
- ❌ **Background-image SIN escapado** (potencial XSS)
- ❌ NO tiene DocBlocks (0/2 métodos)
- ❌ Usa $GLOBALS para pasar datos (anti-pattern)
- ❌ Estilos inline hardcoded (debería estar en CSS)
- ❌ Block name confuso (`acf-gbr-static-hero`)
- ❌ Namespace incorrecto
- ⚠️ Rutas inconsistentes (mayúsculas, `/src/Blocks/` en lugar de `/templates/`)
- ✅ Código MUY simple (63 líneas clase + 50 template)
- ⚠️ Funcionalidad duplicada con HeroSection

### Puntuación: 3/10

**Fortalezas:**
- Código extremadamente simple (63 líneas clase)
- Funcionalidad básica funciona
- Precarga de imagen (aunque mal implementada)
- Sin dependencias externas

**Debilidades:**
- ❌ **NO hereda de BlockBase** (problema arquitectónico grave)
- ❌ **add_action dentro de template** (anti-pattern grave)
- ❌ **Background-image SIN escapado** (seguridad)
- ❌ Template hace get_field() directamente (violación MVC)
- ❌ Campos ACF en JSON (menos flexible)
- ❌ NO tiene DocBlocks
- ❌ Usa $GLOBALS (anti-pattern)
- ❌ Estilos inline hardcoded
- ❌ Block name confuso
- ❌ Namespace incorrecto
- ⚠️ Rutas inconsistentes
- ⚠️ Funcionalidad duplicada con HeroSection

**Recomendación:**
🚨 **DEPRECAR ESTE BLOQUE**

**Razones:**
1. Funcionalidad duplicada con HeroSection (que es superior)
2. Problemas arquitectónicos graves (no hereda BlockBase, violación MVC)
3. Anti-patterns graves (add_action en template, $GLOBALS)
4. Problema de seguridad (background-image sin escapar)
5. Refactorización completa requeriría ~7 horas
6. HeroSection ya cubre esta necesidad con más opciones

**Acción recomendada:**
1. Verificar si hay contenido usando `acf-gbr-static-hero` en producción
2. Si no hay: Eliminar directamente
3. Si hay: Migrar a `HeroSection`, luego eliminar
4. Si por alguna razón DEBE mantenerse: Fix críticos obligatorios:
   - Eliminar add_action de template
   - Escapar background-image
   - Luego considerar refactorización completa

**Comparación:**
| Aspecto | HeroSection | StaticHero |
|---------|------------|-----------|
| Líneas | ~400 | 63 |
| Hereda BlockBase | ✅ Sí | ❌ **NO** |
| Registra ACF fields | ✅ PHP | ⚠️ JSON |
| Template queries | ✅ No | ❌ Sí (malo) |
| add_action en template | ❌ No | ❌ **SÍ (grave)** |
| Background escapado | ✅ Sí | ❌ **NO** |
| DocBlocks | ✅ Sí | ❌ No |
| $GLOBALS | ❌ No | ❌ Sí (malo) |
| Estilos | ✅ CSS | ❌ Inline |
| Opciones | ✅ Muchas | ❌ Mínimas |
| **Puntuación** | 7/10 | **3/10** |
| **Recomendación** | ✅ Mantener | ❌ **DEPRECAR** |

**Veredicto:** Este bloque tiene problemas GRAVES (no hereda BlockBase, add_action en template, XSS potencial, violación MVC, anti-patterns). Además, duplica funcionalidad con HeroSection que es superior. La refactorización completa requeriría ~7 horas, tiempo que no vale la pena cuando HeroSection ya existe y es mejor. **DEPRECAR URGENTE y migrar contenido a HeroSection.**

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** CRÍTICA - Deprecar urgente o fix críticos inmediatos (add_action, XSS)
