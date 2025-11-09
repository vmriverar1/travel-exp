# Auditoría: PostsCarouselNative (ACF)

**Fecha:** 2025-11-09
**Bloque:** 8/15 ACF
**Tiempo:** 30 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `acf-gbr-posts-carousel` ⚠️ (nombre confuso, colisiona con PostsCarousel)
- **Namespace ACF:** `acf/acf-gbr-posts-carousel`
- **Campos ACF:** `pc_posts_per_page`, `pc_show_arrows`, `pc_show_dots`, `pc_autoplay`, `pc_autoplay_delay`
- **ContentQueryHelper prefix:** `pc` (diferente a PostsCarousel que usa `pc_mat`)
- **Template path:** `/src/Blocks/PostsCarousel/templates/editorial-carousel.php` ⚠️ (path confuso)

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **DUPLICACIÓN DETECTADA:** Existe `PostsCarousel` con funcionalidad MUY similar
- **NO hereda de BlockBase** (como FlexibleGridCarousel y HeroCarousel)
- Template hace query directa a WP_Query
- Usa CSS scroll-snap nativo (sin dependencias JS externas)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/PostsCarouselNative.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/src/Blocks/PostsCarousel/templates/editorial-carousel.php` ⚠️
**Assets:**
- CSS: `/assets/blocks/PostsCarousel/style.css`
- JS: `/assets/blocks/PostsCarousel/carousel.js` (vanilla JS)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

**Dependencias:**
- ContentQueryHelper (para contenido dinámico)
- CSS scroll-snap (nativo del navegador)
- JavaScript vanilla (sin librerías externas)

---

## 2. Propósito y Funcionalidad

**Descripción:** Carousel nativo con CSS scroll-snap y JavaScript vanilla. Sin dependencias externas. Soporta contenido dinámico (Packages/Posts/Deal) pero NO contenido manual.

**Diferencia con PostsCarousel:**
- PostsCarousel: Grid desktop + Slider mobile con Material Design
- PostsCarouselNative: Carousel nativo con CSS scroll-snap (siempre carousel)
- PostsCarousel: Soporta manual (repeater) + dinámico
- PostsCarouselNative: Solo dinámico (no tiene repeater)

**Inputs (ACF):**

**Tab: General**
- `pc_posts_per_page` (number, 1-20, default: 6)
- `pc_show_arrows` (true_false, default: true)
- `pc_show_dots` (true_false, default: true)
- `pc_autoplay` (true_false, default: false)
- `pc_autoplay_delay` (number, 1000-30000ms, default: 5000, condicional)

**Dynamic Content Fields** (via ContentQueryHelper)
- Todos los campos de `ContentQueryHelper::get_dynamic_content_fields('pc')`
- Incluye: dynamic_source, dynamic_limit, dynamic_orderby, dynamic_order, visible_fields, cta_text, badge_taxonomy

**Filter Fields** (via ContentQueryHelper)
- Todos los campos de `ContentQueryHelper::get_filter_fields('pc')`

**Outputs:**
- Carousel con CSS scroll-snap
- Navigation arrows (opcional)
- Pagination dots (opcional)
- Autoplay (opcional)
- Contenido dinámico (Packages/Posts/Deal)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno
- Usa: ContentQueryHelper (helper)

**Propiedades:**
```
private string $name = 'acf-gbr-posts-carousel'; // ⚠️ Nombre confuso
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor vacío (4 líneas)
2. register(): void - Registra bloque y campos (3 líneas wrapper)
3. register_block(): void - Registro del bloque ACF (33 líneas)
4. register_fields(): void - Registro de campos ACF (106 líneas)
5. enqueue_assets(): void - Encola CSS y JS (19 líneas)
6. render($block, $content, $is_preview, $post_id): void - Renderiza bloque (76 líneas)
```

**Métodos Privados:**
```
Ninguno
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (directo, no hereda de BlockBase)

**Configuración:**
- name: `acf-gbr-posts-carousel` ⚠️ (nombre confuso, `acf-gbr` prefix inconsistente)
- title: "Posts Carousel (Native CSS)"
- category: `travel`
- icon: `images-alt2` (mismo que PostsCarousel)
- keywords: ['posts', 'carousel', 'slider', 'native', 'scroll-snap']
- render_callback: `[$this, 'render']`
- enqueue_assets: `[$this, 'enqueue_assets']`
- supports: align, mode, jsx, spacing, color, typography, anchor, customClassName

**Block.json:** No existe

**Diferencia con PostsCarousel:**
- PostsCarousel usa `parent::register()` (hereda BlockBase)
- PostsCarouselNative usa `acf_register_block_type` directo

---

## 5. Campos ACF

**Definición:** [X] PHP inline (acf_add_local_field_group)

**Grupo:** `group_posts_carousel` ⚠️ (key genérico, puede colisionar)

**Campos:** 5 campos directos + campos de ContentQueryHelper

**Estructura:**
1. **Tab: General** (5 campos)
2. **Dynamic Content Fields** (via ContentQueryHelper con prefix `pc`)
3. **Filter Fields** (via ContentQueryHelper con prefix `pc`)

**Condicionales:**
- `pc_autoplay_delay` solo visible si `pc_autoplay == true`

**Diferencia con PostsCarousel:**
- PostsCarousel: 27 campos principales + repeater + ContentQueryHelper
- PostsCarouselNative: 5 campos + ContentQueryHelper (mucho más simple)
- PostsCarousel: Prefix `pc_mat`
- PostsCarouselNative: Prefix `pc`

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Get block wrapper attributes: `get_block_wrapper_attributes()`
2. Get ACF fields (5 campos)
3. Check dynamic source: `get_field('pc_dynamic_source')`
4. Si dynamic:
   - `ContentQueryHelper::get_content('pc', 'package')` o
   - `ContentQueryHelper::get_content('pc', 'post')` o
   - `ContentQueryHelper::get_deal_packages($deal_id, 'pc')`
5. Si no dynamic: `$items = []` (vacío, no hay fallback)

**Procesamiento:**
1. Prepara array `$data` con 8 keys (líneas 254-266)
2. Include template directamente: `include $template` (línea 268-271)
3. **NO usa load_template()** (no hereda de BlockBase)

**Variables al Template:**
```php
- $block_wrapper_attributes
- $block_id, $align
- $posts_per_page
- $show_arrows, $show_dots
- $autoplay, $autoplay_delay
- $is_preview
- $use_dynamic, $items
```

**Template hace query adicional:**
- Si NO es dinámico (`$use_dynamic == false`), template crea WP_Query directa (líneas 17-28)
- ⚠️ Duplicación de lógica query (clase + template)

**Manejo de Errores:**
- ❌ NO tiene try-catch
- ❌ NO tiene logging
- ❌ NO muestra mensaje si template no existe

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:**
- ✅ Sí usa (carousel.js)
- Implementación: Vanilla JS con CSS scroll-snap
- Funcionalidad: Carousel navigation, autoplay, dots
- Enqueue: Frontend + Editor

**REST API:** No usa

**Hooks Propios:** No define

**Dependencias Externas:**
- ContentQueryHelper (interno)
- CSS scroll-snap (nativo navegador)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ⚠️ **PARCIAL**
- Clase + Template hacen queries (duplicación)
- Template no debería hacer queries
- Impacto: MEDIO

**OCP:** ⚠️ Difícil de extender (no hereda de BlockBase)

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con otros bloques ACF
- Impacto: MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ Parcial
- Dependencia de funciones globales ACF (get_field)
- Dependencia de ContentQueryHelper
- Template hace queries directas
- Impacto: MEDIO

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ Todos los métodos <110 líneas
- ✅ __construct(): 4 líneas
- ✅ register(): 3 líneas
- ✅ register_block(): 33 líneas
- ✅ register_fields(): 106 líneas
- ✅ enqueue_assets(): 19 líneas
- ✅ render(): 76 líneas
- **Total:** 274 líneas (mucho mejor que PostsCarousel: 756)

**Anidación:**
- ✅ <3 niveles en todos los métodos

**Duplicación:**
- ❌ **DUPLICACIÓN CRÍTICA DETECTADA**
  - Existe `PostsCarousel` (bloque muy similar)
  - Funcionalidad ~70% duplicada
  - Ambos hacen carousels de posts
  - Diferencias:
    - PostsCarousel: Grid desktop + Slider mobile
    - PostsCarouselNative: Carousel siempre (CSS scroll-snap)
    - PostsCarousel: Manual + Dinámico
    - PostsCarouselNative: Solo Dinámico
  - Ubicación: `/src/Blocks/ACF/PostsCarousel.php`
  - Impacto: **CRÍTICO** - Mantenimiento doble, inconsistencias, confusión

**Nombres:**
- ❌ **Block name confuso:** `acf-gbr-posts-carousel`
  - Prefix `acf-gbr` es inconsistente con otros bloques
  - Colisiona con `posts-carousel`
  - ¿Qué significa `gbr`?
  - Impacto: MEDIO - Confusión
- ⚠️ Prefix `pc` es muy genérico (mismo que PostsCarousel usa `pc_mat`)
- ⚠️ Template path confuso: `/src/Blocks/PostsCarousel/` (singular, no plural)

**Código Sin Uso:**
- ✅ No se detectó código sin uso
- ⚠️ Template path en `/src/Blocks/PostsCarousel/` sugiere reorganización incompleta

**DocBlocks:**
- ❌ **NO tiene DocBlocks** en métodos
- ❌ Header class muy simple
- Impacto: ALTO - Dificulta mantenimiento

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ get_field() con fallbacks seguros
- ⚠️ Template hace query directa (sin sanitización visible)

**Escapado:**
- ⚠️ **Template debe escapar** (no visto en auditoría completa)
- ⚠️ Verificar escapado en editorial-carousel.php

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ⚠️ **Template hace WP_Query directa**
  - Líneas 17-28 en editorial-carousel.php
  - Duplicación con render()
  - Impacto: MEDIO

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 3
  - Impacto: BAJO (funciona pero no sigue convención)

**Separación MVC:**
- ❌ **Violación MVC**
  - Template hace queries directas (WP_Query)
  - Lógica de negocio en template
  - Debería estar en controller (clase)
  - Impacto: MEDIO-ALTO

**Acoplamiento:**
- ⚠️ **Acoplamiento MEDIO**
  - Dependencia de ContentQueryHelper
  - Template acoplado a WP_Query
  - Include directo de template (no usa load_template)

**Herencia:**
- ❌ **NO hereda de BlockBase** (problema crítico)
  - Inconsistente con PostsCarousel, HeroSection, etc.
  - Duplica funcionalidad de BlockBase
  - Impacto: ALTO

**Otros:**
- ❌ **Template path confuso:** `/src/Blocks/PostsCarousel/` (singular)
  - Assets en `/assets/blocks/PostsCarousel/`
  - Sugiere reorganización incompleta
  - Impacto: BAJO-MEDIO
- ❌ **Constructor vacío** (líneas 11-14)
  - No hace nada
  - Innecesario
  - Impacto: BAJO

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque tiene duplicación CRÍTICA con PostsCarousel. PRIORITARIO resolver duplicación.**

### Prioridad CRÍTICA

**1. 🚨 RESOLVER DUPLICACIÓN con PostsCarousel**
- **Acción:**
  - Investigar qué bloque se usa en producción
  - Decidir estrategia:
    - **Opción A:** Mantener PostsCarousel (más completo, hereda BlockBase)
    - **Opción B:** Migrar funcionalidad de PostsCarousel a este (más simple)
    - **Opción C:** Fusionar ambos en uno solo
  - Ejecutar plan de migración
- **Análisis preliminar:**
  - PostsCarousel: 756 líneas, hereda BlockBase ✅, muy completo, complejo
  - PostsCarouselNative: 274 líneas, NO hereda BlockBase ❌, más simple
  - **Recomendación:** Mantener PostsCarousel, deprecar PostsCarouselNative
- **Razón:** Mantenimiento doble, confusión, inconsistencias
- **Riesgo:** CRÍTICO - Afecta contenido existente
- **Precauciones:**
  - ⛔ NO borrar ninguno hasta migrar contenido
  - Ejecutar: `grep -r "acf-gbr-posts-carousel" wp-content/uploads/`
  - Crear script de migración
- **Esfuerzo:** 3-4 horas (investigación + migración)

### Prioridad Alta

**2. Heredar de BlockBase (si se mantiene este bloque)**
- **Acción:** Cambiar `class PostsCarouselNative` a `class PostsCarouselNative extends BlockBase`
- **Razón:** Consistencia con otros bloques, evita duplicación
- **Riesgo:** MEDIO - Requiere refactorizar register() y render()
- **Precauciones:**
  - Mover configuración a __construct()
  - Usar parent::register()
  - Usar load_template() en lugar de include
- **Esfuerzo:** 2 horas

**3. Mover lógica de query desde template a clase**
- **Acción:** Eliminar WP_Query del template (líneas 17-28 editorial-carousel.php)
  - Query debe hacerse en render()
  - Template solo recibe datos ya procesados
- **Razón:** Violación MVC, duplicación de lógica
- **Riesgo:** MEDIO - Cambia flujo de datos
- **Precauciones:**
  - Template debe recibir array de items ya formateado
  - Eliminar lógica condicional del template
- **Esfuerzo:** 1 hora

**4. Verificar template escapa correctamente**
- **Acción:** Revisar `/src/Blocks/PostsCarousel/templates/editorial-carousel.php`
  - Verificar escapado de todos los campos
- **Razón:** Seguridad
- **Riesgo:** ALTO - Critical si no está escapado
- **Esfuerzo:** 30 min

### Prioridad Media

**5. Agregar DocBlocks**
- **Acción:** Agregar PHPDoc a todos los métodos
- **Razón:** Documentación, mantenibilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**6. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\ACF` a `Travel\Blocks\ACF`
- **Razón:** No sigue PSR-4
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:**
  - Actualizar composer.json
  - Ejecutar `composer dump-autoload`
- **Esfuerzo:** 30 min

**7. Renombrar block name (solo si se mantiene)**
- **Acción:** Cambiar de `acf-gbr-posts-carousel` a `posts-carousel-native`
- **Razón:** Nombre más claro, sin prefix confuso
- **Riesgo:** CRÍTICO - Rompe contenido existente
- **Precauciones:**
  - ⛔ SOLO si no hay contenido en producción
  - Ejecutar script de migración
- **Esfuerzo:** Variable (depende de contenido existente)

### Prioridad Baja

**8. Eliminar constructor vacío**
- **Acción:** Eliminar `__construct()` vacío (líneas 11-14)
- **Razón:** Innecesario
- **Riesgo:** BAJO
- **Esfuerzo:** 2 min

**9. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**10. Reorganizar template path**
- **Acción:** Mover de `/src/Blocks/PostsCarousel/` a `/src/Blocks/PostsCarouselNative/`
- **Razón:** Consistencia, claridad
- **Riesgo:** BAJO - Solo reorganización
- **Esfuerzo:** 15 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. 🚨 **CRÍTICO:** Resolver duplicación con PostsCarousel
   - Investigar uso en producción
   - Decidir estrategia (deprecar este, mantener PostsCarousel)
2. Si se depreca: Crear script de migración, ejecutar, eliminar
3. Si se mantiene:
   - Heredar de BlockBase
   - Mover lógica de query desde template
   - Verificar escapado en template
   - Agregar DocBlocks
   - Corregir namespace
   - Eliminar constructor vacío
   - Crear block.json (opcional)

**Recomendación:** DEPRECAR este bloque, mantener PostsCarousel.

**Precauciones Generales:**
- ⛔ NO cambiar block name sin migración
- ⛔ NO eliminar sin verificar uso en producción
- ⛔ NO modificar si PostsCarousel se mantiene como estándar
- ✅ Ejecutar búsqueda de uso: `grep -r "acf-gbr-posts-carousel"`
- ✅ Verificar database: `wp db query "SELECT * FROM wp_posts WHERE post_content LIKE '%acf-gbr-posts-carousel%'"`

---

## 11. Checklist Post-Refactorización

### Funcionalidad (si se mantiene)
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Campos ACF aparecen correctamente
- [ ] Preview funciona en editor
- [ ] Frontend funciona correctamente

### Contenido Dinámico
- [ ] Dynamic source selector funciona
- [ ] Packages query funciona
- [ ] Blog posts query funciona
- [ ] Deal packages query funciona
- [ ] Filtros se aplican correctamente

### Carousel
- [ ] CSS scroll-snap funciona
- [ ] Navigation arrows funcionan
- [ ] Pagination dots funcionan
- [ ] Autoplay funciona (si activado)
- [ ] Responsive en mobile/tablet/desktop

### Arquitectura
- [ ] Hereda de BlockBase (si se cambió)
- [ ] Namespace correcto (si se cambió)
- [ ] Template NO hace queries (si se movió)
- [ ] load_template() funciona (si se cambió)
- [ ] Constructor eliminado (si se eliminó)

### Seguridad
- [ ] Template escapa todos los campos
- [ ] Query sanitizada (si se movió a clase)

### Duplicación
- [ ] Duplicación con PostsCarousel resuelta
- [ ] Script de migración ejecutado (si se deprecó)
- [ ] Contenido migrado (si aplica)
- [ ] Bloque eliminado del código (si se deprecó)

### Clean Code
- [ ] DocBlocks agregados (si se cambió)
- [ ] Template path reorganizado (si se cambió)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ❌ **DUPLICACIÓN CRÍTICA CON PostsCarousel (~70%)**
- ❌ NO hereda de BlockBase (inconsistente)
- ❌ Template hace queries directas (violación MVC)
- ❌ NO tiene DocBlocks
- ❌ Block name confuso (`acf-gbr-posts-carousel`)
- ❌ Namespace incorrecto
- ❌ Constructor vacío
- ✅ Código más simple que PostsCarousel (274 vs 756 líneas)
- ✅ Métodos cortos
- ✅ Usa ContentQueryHelper
- ⚠️ Template path confuso

### Puntuación: 4/10

**Fortalezas:**
- Código simple y corto (274 líneas)
- Métodos pequeños y enfocados
- Usa ContentQueryHelper para contenido dinámico
- CSS scroll-snap nativo (sin dependencias externas)
- No tiene logging excesivo

**Debilidades:**
- ❌ **DUPLICACIÓN CRÍTICA** con PostsCarousel
- ❌ NO hereda de BlockBase (problema arquitectónico mayor)
- ❌ Template hace queries directas (violación MVC)
- ❌ NO tiene DocBlocks (0/6 métodos documentados)
- ❌ Block name confuso (`acf-gbr-posts-carousel`)
- ❌ Namespace incorrecto
- ❌ Constructor vacío e innecesario
- ⚠️ Template path confuso (`/src/Blocks/PostsCarousel/`)
- ⚠️ Prefix genérico (`pc` vs `pc_mat` de PostsCarousel)

**Recomendación:**
🚨 **DEPRECAR ESTE BLOQUE**

**Razones:**
1. PostsCarousel es superior arquitectónicamente (hereda BlockBase)
2. PostsCarousel es más completo (manual + dinámico)
3. Mantener ambos es duplicación crítica
4. Este bloque tiene problemas arquitectónicos graves

**Acción recomendada:**
1. Verificar si hay contenido usando `acf-gbr-posts-carousel` en producción
2. Si no hay: Eliminar directamente
3. Si hay: Crear script de migración a `posts-carousel`, ejecutar, eliminar

**Comparación:**
| Aspecto | PostsCarousel | PostsCarouselNative |
|---------|--------------|---------------------|
| Líneas | 756 | 274 |
| Hereda BlockBase | ✅ Sí | ❌ No |
| Contenido Manual | ✅ Sí | ❌ No |
| Contenido Dinámico | ✅ Sí | ✅ Sí |
| DocBlocks | ✅ Sí | ❌ No |
| Template queries | ❌ No | ❌ Sí (malo) |
| Complejidad | Alta | Baja |
| **Recomendación** | **✅ MANTENER** | **❌ DEPRECAR** |

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** CRÍTICA - Deprecar este bloque, mantener PostsCarousel
