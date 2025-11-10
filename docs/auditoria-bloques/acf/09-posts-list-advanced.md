# Auditoría: PostsListAdvanced (ACF)

**Fecha:** 2025-11-09
**Bloque:** 9/15 ACF
**Tiempo:** 25 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `acf-gbr-posts-list-advanced`
- **Namespace ACF:** `acf/acf-gbr-posts-list-advanced`
- **Campos ACF:** `pla_posts_per_page`, `pla_enable_swiper_mobile`
- **Template path:** `/src/Blocks/PostsListAdvanced/templates/editorial-grid.php`
- **Global variable:** `$GLOBALS['pla_block_wrapper_attributes']` (usado en template)

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** (como FlexibleGridCarousel, HeroCarousel, PostsCarouselNative)
- **Dependencia externa:** Swiper.js desde CDN (condicional)
- Template hace query WP_Query directa (sin ContentQueryHelper)
- NO tiene campos de filtros ni contenido dinámico
- MUY simple (116 líneas totales)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/PostsListAdvanced.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/src/Blocks/PostsListAdvanced/templates/editorial-grid.php`
**Assets:**
- CSS: `/assets/blocks/PostsListAdvanced/style.css` (base)
- JS: `/assets/blocks/PostsListAdvanced/view-swiper.js` (condicional)
- CSS Swiper: `https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css` (CDN, condicional)
- JS Swiper: `https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js` (CDN, condicional)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

**Dependencias:**
- ⚠️ **Swiper.js v11 desde CDN** (si `pla_enable_swiper_mobile == true`)
- WP_Query (query directa en template)

---

## 2. Propósito y Funcionalidad

**Descripción:** Grid editorial de blog posts con opción de Swiper en mobile. SSR optimizado para SEO. NO soporta contenido dinámico avanzado (sin ContentQueryHelper).

**Diferencia con otros bloques:**
- PostsCarousel: Grid desktop + Slider mobile, contenido manual/dinámico, ContentQueryHelper
- PostsCarouselNative: Carousel CSS scroll-snap, contenido dinámico, ContentQueryHelper
- PostsListAdvanced: Grid simple con Swiper opcional, solo blog posts, WP_Query directo

**Inputs (ACF):**

⚠️ **SOLO 2 CAMPOS** (muy simple)
- `pla_posts_per_page` (number, default: 6)
- `pla_enable_swiper_mobile` (true_false, condicional Swiper)

**NO tiene:**
- ❌ ContentQueryHelper fields (dynamic source, filters, etc.)
- ❌ Campos de estilo (card_style, button_color, etc.)
- ❌ Campos de configuración (columns, gap, hover, etc.)
- ❌ Selector de post type (solo 'post' hardcoded)

**Outputs:**
- Grid de blog posts (HTML cards con background-image inline)
- Desktop: Grid CSS
- Mobile: Swiper slider (si habilitado)
- SSR completo (sin JavaScript necesario excepto Swiper)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```
private string $name = 'acf-gbr-posts-list-advanced';
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor con hook acf/init (4 líneas)
2. register(): void - Registro del bloque ACF (40 líneas)
3. render($block, $content, $is_preview, $post_id): void - Renderiza bloque (53 líneas)
```

**Métodos Privados:**
```
Ninguno
```

**Total:** 116 líneas (el bloque MÁS simple de todos los auditados)

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (directo, no hereda de BlockBase)

**Configuración:**
- name: `acf-gbr-posts-list-advanced`
- title: "Posts List Advanced (SSR + Swiper Mobile)"
- category: `travel`
- icon: `slides`
- keywords: ['posts', 'slider', 'grid', 'responsive']
- render_callback: `[$this, 'render']`
- enqueue_assets: closure inline (no método separado)
- supports: align, mode, jsx, spacing, color, typography, anchor, customClassName

**Enqueue Assets (inline closure):**
- CSS base siempre encolado
- Swiper encolado condicionalmente en render() (no aquí)

**Block.json:** No existe

**Campos ACF:** ❌ **NO REGISTRA CAMPOS ACF** (no hay `register_fields()`)

---

## 5. Campos ACF

**Definición:** ❌ **NO DEFINE CAMPOS ACF**

**Problema:** El bloque usa `get_field('pla_posts_per_page')` y `get_field('pla_enable_swiper_mobile')` pero NO los registra.

**Campos esperados pero no definidos:**
- `pla_posts_per_page` (usado en línea 6 del template)
- `pla_enable_swiper_mobile` (usado en línea 71 del PHP)

**Impacto:** CRÍTICO - Los campos no aparecen en el editor, valores siempre null/default.

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Get block wrapper attributes: `get_block_wrapper_attributes()` (línea 66-68)
2. ⚠️ Detecta `pla_enable_swiper_mobile` (línea 71) - **campo no registrado**
3. Si Swiper habilitado:
   - Encola Swiper CSS desde CDN (líneas 74-80)
   - Encola Swiper JS desde CDN (líneas 82-91)
   - Agrega async/defer al script (líneas 89-90)
   - Encola view-swiper.js (líneas 94-100)
   - Localiza script con flags (líneas 102-104)
4. Guarda block_wrapper_attributes en `$GLOBALS` (línea 108)
5. Include template directamente (líneas 110-113)

**Template hace TODO el trabajo:**
- Get `pla_posts_per_page` (línea 6) - **campo no registrado**
- Crea WP_Query directo (líneas 7-12)
- Renderiza HTML (líneas 22-44)
- NO hay separación de lógica y presentación

**Variables al Template:**
- ❌ NO pasa variables explícitamente
- ✅ Template lee ACF fields directamente
- ✅ Template lee $GLOBALS['pla_block_wrapper_attributes']
- ✅ Template lee $block variable (no pasada, pero disponible)

**Manejo de Errores:**
- ❌ NO tiene try-catch
- ❌ NO valida si template existe
- ❌ NO tiene logging
- ✅ Template tiene fallback "No hay resultados" (línea 45)

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:**
- ✅ Sí usa (condicional)
- Implementación: Swiper.js v11 desde CDN
- Funcionalidad: Slider mobile
- Enqueue: Condicional basado en `pla_enable_swiper_mobile`
- ⚠️ Async/defer agregado manualmente (líneas 89-90)

**REST API:** No usa

**Hooks Propios:**
- `add_action('acf/init', [$this, 'register'])` (línea 11)

**Dependencias Externas:**
- ⚠️ **Swiper.js v11 desde CDN**
  - CSS: jsdelivr.net
  - JS: jsdelivr.net
  - Riesgo: Dependencia externa, requiere internet
  - Impacto: MEDIO - Puede fallar si CDN cae

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ⚠️ **PARCIAL**
- Template hace queries (debería estar en controller)
- Clase solo encola assets y include template
- Impacto: MEDIO

**OCP:** ⚠️ Difícil de extender (no hereda de BlockBase)

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con otros bloques ACF
- Impacto: MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ❌ **VIOLA**
- Dependencia hardcoded de CDN (Swiper)
- Template hace queries directas
- No usa ContentQueryHelper (inconsistente)
- Impacto: MEDIO

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ Todos los métodos muy cortos
- ✅ __construct(): 4 líneas
- ✅ register(): 40 líneas
- ✅ render(): 53 líneas
- ✅ **Total:** 116 líneas (excelente)

**Anidación:**
- ✅ <3 niveles en todos los métodos

**Duplicación:**
- ⚠️ **POSIBLE duplicación** con PostsCarousel/PostsCarouselNative
  - Funcionalidad similar: mostrar posts
  - Diferencia: más simple, solo blog posts
  - Impacto: BAJO-MEDIO

**Nombres:**
- ⚠️ **Block name largo:** `acf-gbr-posts-list-advanced`
  - Prefix `acf-gbr` inconsistente
  - ¿Qué significa `gbr`?
  - "Advanced" pero es el más simple
- ⚠️ Prefix `pla` es claro pero inconsistente

**Código Sin Uso:**
- ❌ **Constructor innecesario** (líneas 10-13)
  - Solo registra hook
  - Podría estar en Plugin.php

**DocBlocks:**
- ❌ **NO tiene DocBlocks** en métodos
- ❌ NO tiene header class
- ❌ Solo comentarios en template
- Impacto: ALTO - 0/3 métodos documentados

### 8.3 Problemas de Seguridad

**Sanitización:**
- ⚠️ `get_field()` sin validación (campos no registrados)
- ⚠️ Template hace query sin sanitización visible

**Escapado:**
- ✅ Template usa `esc_url()` (línea 31)
- ✅ Template usa `esc_html()` (línea 29)
- ❌ **Background-image inline SIN escapado** (línea 31)
  - `style="background-image:url('<?php echo esc_url($thumb); ?>');"`
  - ⚠️ esc_url dentro de CSS puede ser insuficiente
  - Impacto: BAJO-MEDIO

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ❌ **Template hace WP_Query directa**
  - Sin prepared statements (no necesario en WP_Query)
  - ✅ Pero WP_Query sanitiza internamente
  - Impacto: BAJO

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 3
  - Impacto: BAJO (funciona pero no sigue convención)

**Separación MVC:**
- ❌ **VIOLACIÓN GRAVE MVC**
  - Template hace queries directas (líneas 6-12)
  - Template tiene lógica de negocio
  - Clase solo encola assets
  - Impacto: ALTO

**Acoplamiento:**
- ❌ **Acoplamiento ALTO**
  - Dependencia hardcoded de CDN Swiper
  - Template acoplado a WP_Query
  - NO usa helpers (ContentQueryHelper)
  - `$GLOBALS` para pasar datos (anti-pattern)
  - Impacto: ALTO

**Herencia:**
- ❌ **NO hereda de BlockBase** (problema crítico)
  - Inconsistente con bloques bien hechos
  - Duplica funcionalidad
  - Impacto: ALTO

**Otros:**
- ❌ **NO REGISTRA CAMPOS ACF** (crítico)
  - Usa get_field() pero no registra fields
  - Campos no aparecen en editor
  - Impacto: **CRÍTICO**
- ❌ **$GLOBALS para pasar datos** (anti-pattern)
  - Línea 108: `$GLOBALS['pla_block_wrapper_attributes']`
  - Template lee de $GLOBALS (línea 14)
  - Debería pasar por extract() o $data
  - Impacto: MEDIO

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque tiene PROBLEMAS CRÍTICOS. Considerar deprecar o refactorizar completamente.**

### Prioridad CRÍTICA

**1. 🚨 REGISTRAR CAMPOS ACF**
- **Acción:** Crear método `register_fields()`:
  ```php
  private function register_fields(): void {
      acf_add_local_field_group([
          'key' => 'group_posts_list_advanced',
          'title' => 'Posts List Advanced - Settings',
          'fields' => [
              [
                  'key' => 'field_pla_posts_per_page',
                  'label' => 'Posts to Display',
                  'name' => 'pla_posts_per_page',
                  'type' => 'number',
                  'default_value' => 6,
                  'min' => 1,
                  'max' => 20,
              ],
              [
                  'key' => 'field_pla_enable_swiper_mobile',
                  'label' => 'Enable Swiper Mobile',
                  'name' => 'pla_enable_swiper_mobile',
                  'type' => 'true_false',
                  'default_value' => 0,
                  'ui' => 1,
              ],
          ],
          'location' => [[['param' => 'block', 'operator' => '==', 'value' => 'acf/acf-gbr-posts-list-advanced']]],
      ]);
  }
  ```
- **Razón:** Campos no aparecen en editor, bloque inútil sin ellos
- **Riesgo:** CRÍTICO - Bloque no funciona correctamente
- **Precauciones:** Verificar valores default
- **Esfuerzo:** 30 min

**2. Decidir: Deprecar o Refactorizar**
- **Análisis:**
  - ¿Se usa en producción? Verificar con: `grep -r "acf-gbr-posts-list-advanced"`
  - ¿Es necesario? Ya existen PostsCarousel y PostsCarouselNative
  - Diferencias: Este es MÁS simple (solo posts, sin filtros)
- **Opciones:**
  - **A) DEPRECAR:** Si PostsCarousel/PostsCarouselNative cubren necesidades
  - **B) REFACTORIZAR:** Si se usa y es diferente suficiente
- **Recomendación:** DEPRECAR (duplicación funcional con otros bloques)
- **Esfuerzo:** Variable (1 hora si deprecar, 4+ horas si refactorizar)

### Prioridad Alta (si se mantiene)

**3. Heredar de BlockBase**
- **Acción:** Cambiar `class PostsListAdvanced extends BlockBase`
- **Razón:** Consistencia, evita duplicación
- **Riesgo:** MEDIO - Requiere refactorizar register() y render()
- **Precauciones:**
  - Mover configuración a __construct()
  - Usar parent::register()
  - Usar load_template() en lugar de include
- **Esfuerzo:** 1.5 horas

**4. Mover query de template a clase**
- **Acción:** Eliminar WP_Query del template (líneas 6-12)
  - Query debe hacerse en render()
  - Template solo recibe array de posts
- **Razón:** Violación MVC
- **Riesgo:** MEDIO - Cambia flujo de datos
- **Precauciones:**
  - Pasar datos por $data, no $GLOBALS
  - Eliminar lógica del template
- **Esfuerzo:** 1 hora

**5. Migrar Swiper.js a local**
- **Acción:** Descargar Swiper.js v11 a:
  - `/assets/blocks/PostsListAdvanced/swiper-bundle.min.css`
  - `/assets/blocks/PostsListAdvanced/swiper-bundle.min.js`
  - Actualizar enqueue_assets para usar local
- **Razón:** Eliminar dependencia de CDN externo
- **Riesgo:** BAJO - Solo cambio de source
- **Precauciones:** Versionar Swiper en comentario
- **Esfuerzo:** 30 min

**6. Eliminar uso de $GLOBALS**
- **Acción:** Pasar block_wrapper_attributes por $data:
  ```php
  $data = [
      'block_wrapper_attributes' => $block_wrapper_attributes,
      'posts' => $posts,
      'enable_swiper' => $enable_swiper,
  ];
  $this->load_template('posts-list-advanced', $data);
  ```
- **Razón:** $GLOBALS es anti-pattern
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

### Prioridad Media

**7. Agregar DocBlocks**
- **Acción:** Agregar PHPDoc a todos los métodos
- **Razón:** Documentación, mantenibilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**8. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\ACF` a `Travel\Blocks\ACF`
- **Razón:** No sigue PSR-4
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Esfuerzo:** 30 min

**9. Mejorar escapado en template**
- **Acción:** Para background-image inline, usar:
  ```php
  <article class="pla-card" style="background-image:url(<?php echo esc_url($thumb); ?>);">
  ```
  (sin comillas internas)
- **Razón:** Escapado más robusto
- **Riesgo:** BAJO
- **Esfuerzo:** 5 min

### Prioridad Baja

**10. Eliminar constructor o mover hook**
- **Acción:** Registrar hook desde Plugin.php en lugar de __construct()
- **Razón:** Constructor innecesario
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min

**11. Renombrar block name**
- **Acción:** Cambiar de `acf-gbr-posts-list-advanced` a `posts-list`
- **Razón:** Nombre más claro, sin prefix confuso
- **Riesgo:** CRÍTICO - Rompe contenido existente
- **Precauciones:** Solo si no hay contenido en producción
- **Esfuerzo:** Variable

**12. Agregar ContentQueryHelper**
- **Acción:** Usar ContentQueryHelper en lugar de WP_Query directa
- **Razón:** Consistencia, reutilización, filtros avanzados
- **Riesgo:** MEDIO - Cambia arquitectura
- **Esfuerzo:** 2 horas

---

## 10. Plan de Acción

**Decisión Principal:** ¿Mantener o Deprecar?

### Opción A: DEPRECAR (Recomendado)
1. Verificar uso en producción: `grep -r "acf-gbr-posts-list-advanced"`
2. Si no se usa: Eliminar directamente
3. Si se usa: Migrar a PostsCarousel, eliminar

### Opción B: MANTENER (Solo si se usa mucho)
1. 🚨 Registrar campos ACF (CRÍTICO)
2. Heredar de BlockBase
3. Mover query de template a clase
4. Migrar Swiper.js a local
5. Eliminar uso de $GLOBALS
6. Agregar DocBlocks
7. Corregir namespace
8. Mejorar escapado en template
9. Eliminar constructor
10. Agregar ContentQueryHelper (opcional)

**Recomendación:** DEPRECAR - Funcionalidad duplicada con PostsCarousel

**Precauciones Generales:**
- ⛔ NO usar sin registrar campos ACF primero
- ⛔ NO cambiar block name sin migración
- ⛔ NO eliminar si se usa en producción sin plan
- ✅ Verificar uso: `wp db query "SELECT * FROM wp_posts WHERE post_content LIKE '%acf-gbr-posts-list-advanced%'"`

---

## 11. Checklist Post-Refactorización

### Funcionalidad (si se mantiene)
- [ ] Campos ACF registrados y aparecen en editor
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview funciona en editor
- [ ] Frontend funciona correctamente

### Query y Contenido
- [ ] WP_Query movido a clase (si se cambió)
- [ ] Posts se muestran correctamente
- [ ] Límite de posts funciona
- [ ] Fallback "No hay resultados" funciona

### Swiper
- [ ] Swiper toggle funciona
- [ ] Swiper se carga solo si habilitado
- [ ] Swiper funciona en mobile
- [ ] Swiper local (si se migró)

### Arquitectura
- [ ] Hereda de BlockBase (si se cambió)
- [ ] Namespace correcto (si se cambió)
- [ ] $GLOBALS eliminado (si se cambió)
- [ ] load_template() funciona (si se cambió)
- [ ] Constructor eliminado/mejorado (si se cambió)

### Seguridad
- [ ] Template escapa todos los campos
- [ ] Background-image escapado correctamente

### Duplicación
- [ ] Decisión tomada (deprecar vs mantener)
- [ ] Migración ejecutada (si se deprecó)
- [ ] Bloque eliminado (si se deprecó)

### Clean Code
- [ ] DocBlocks agregados (si se mantiene)
- [ ] ContentQueryHelper integrado (si se agregó)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ❌ **NO REGISTRA CAMPOS ACF** (problema CRÍTICO)
- ❌ NO hereda de BlockBase (inconsistente)
- ❌ Template hace queries directas (violación MVC)
- ❌ NO tiene DocBlocks (0/3 métodos)
- ❌ Usa $GLOBALS para pasar datos (anti-pattern)
- ❌ Dependencia de CDN externo (Swiper)
- ❌ Block name confuso (`acf-gbr-posts-list-advanced`)
- ❌ Namespace incorrecto
- ❌ Constructor innecesario
- ✅ Código MUY simple (116 líneas)
- ✅ Métodos cortos
- ⚠️ Funcionalidad duplicada con PostsCarousel/PostsCarouselNative

### Puntuación: 2/10

**Fortalezas:**
- Código extremadamente simple (116 líneas - el más corto auditado)
- Métodos muy cortos y enfocados
- Template tiene fallback
- No tiene logging excesivo

**Debilidades:**
- ❌ **NO REGISTRA CAMPOS ACF** - Bloque NO funciona correctamente
- ❌ NO hereda de BlockBase (problema arquitectónico grave)
- ❌ Template hace queries directas (violación MVC)
- ❌ NO tiene DocBlocks (0/3 métodos documentados)
- ❌ Usa $GLOBALS (anti-pattern)
- ❌ Dependencia de CDN externo (Swiper)
- ❌ Block name confuso (`acf-gbr`)
- ❌ Namespace incorrecto
- ❌ Constructor innecesario
- ⚠️ Funcionalidad duplicada (ya existe PostsCarousel)

**Recomendación:**
🚨 **DEPRECAR ESTE BLOQUE**

**Razones:**
1. **NO registra campos ACF** - Problema crítico que hace bloque inútil
2. Funcionalidad duplicada con PostsCarousel (que es superior)
3. Problemas arquitectónicos graves (no hereda BlockBase, violación MVC)
4. Dependencia externa (CDN Swiper)
5. Código usa anti-patterns ($GLOBALS)

**Acción recomendada:**
1. Verificar si hay contenido usando `acf-gbr-posts-list-advanced` en producción
2. Si no hay: Eliminar directamente
3. Si hay: Migrar a `posts-carousel` (que tiene más funcionalidades), eliminar

**Comparación:**
| Aspecto | PostsCarousel | PostsListAdvanced |
|---------|--------------|-------------------|
| Líneas | 756 | 116 |
| Hereda BlockBase | ✅ Sí | ❌ No |
| Registra ACF fields | ✅ Sí | ❌ **NO** |
| ContentQueryHelper | ✅ Sí | ❌ No |
| Template queries | ❌ No | ❌ Sí (malo) |
| Dependencia externa | ❌ No | ❌ Swiper CDN |
| DocBlocks | ✅ Sí | ❌ No |
| $GLOBALS | ❌ No | ❌ Sí (malo) |
| **Puntuación** | 6.5/10 | **2/10** |
| **Recomendación** | ✅ Mantener | ❌ **DEPRECAR** |

**Veredicto:** Este es el bloque con más problemas de todos los auditados. NO registra campos ACF (crítico), NO hereda BlockBase, tiene violaciones MVC graves, usa anti-patterns, y duplica funcionalidad. **DEPRECAR URGENTE.**

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** CRÍTICA - Deprecar urgente, bloque no funcional sin campos ACF
