# Auditoría: PostsCarousel (ACF)

**Fecha:** 2025-11-09
**Bloque:** 7/15 ACF
**Tiempo:** 35 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `posts-carousel`
- **Namespace:** `acf/posts-carousel`
- **Campos ACF:** `cards`, `card_style`, `button_color_variant`, `badge_color_variant`, `text_alignment`, `button_alignment`, `show_favorite`, `show_arrows`, `arrows_position`, `show_dots`, `autoplay`, `autoplay_delay`, `slider_speed`, `hover_effect`, `card_gap`, `desktop_columns`, `tablet_columns`, `card_height`
- **ContentQueryHelper prefix:** `pc_mat` (usado en dynamic content fields)
- **Template path:** `/templates/posts-carousel.php`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **DUPLICACIÓN DETECTADA:** Existe `PostsCarouselNative` con funcionalidad similar
- Método `load_template()` heredado de BlockBase
- Dependencia de ContentQueryHelper para contenido dinámico
- JavaScript vanilla (438 líneas) - puede tener dependencias complejas

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/PostsCarousel.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/wp-content/plugins/travel-blocks/templates/posts-carousel.php`
**Assets:**
- CSS: `/assets/blocks/posts-carousel.css` (1584 líneas)
- JS: `/assets/blocks/posts-carousel.js` (438 líneas)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

**Dependencias:**
- ContentQueryHelper (para contenido dinámico)
- JavaScript vanilla (slider mobile + grid desktop)
- No usa librerías externas (implementación custom)

---

## 2. Propósito y Funcionalidad

**Descripción:** Grid de 3 columnas en desktop con hover effects + Slider Material Design en mobile. Soporta contenido manual (ACF Repeater) y dinámico (Packages/Posts).

**Inputs (ACF):**

**Tab 1: Card Styles**
- `card_style` (select): Estilo de card (overlay/vertical/overlay-split)
- `button_color_variant` (select): Color del botón (primary/secondary/white/gold/dark/transparent/read-more/line-arrow)
- `badge_color_variant` (select): Color de badge (primary/secondary/white/gold/dark/transparent)
- `text_alignment` (select): Alineación de texto (left/center/right)
- `button_alignment` (select): Alineación de botón (left/center/right)
- `show_favorite` (true_false): Mostrar botón de favoritos

**Tab 2: Contenido Dinámico** (via ContentQueryHelper)
- Todos los campos de `ContentQueryHelper::get_dynamic_content_fields('pc_mat')`
- Incluye: dynamic_source, dynamic_limit, dynamic_orderby, dynamic_order, visible_fields, cta_text, badge_taxonomy

**Tab 3: Cards** (Manual - Repeater)
- `cards` (repeater, max 12):
  - `image` (image, optional)
  - `title` (text, required, maxlength 100)
  - `excerpt` (textarea, maxlength 200)
  - `link` (url)
  - `category` (text, maxlength 30)
  - `badge_color_variant` (select, individual override)
  - `cta_text` (text, maxlength 30)
  - `location` (text, maxlength 50)
  - `price` (text, maxlength 20)

**Tab 4: Filtros** (via ContentQueryHelper)
- Todos los campos de `ContentQueryHelper::get_filter_fields('pc_mat')`

**Tab 5: Slider Settings (Mobile)**
- `show_arrows` (true_false, default: true)
- `arrows_position` (select): sides/overlay/bottom
- `show_dots` (true_false, default: true)
- `autoplay` (true_false, default: false)
- `autoplay_delay` (range, 2-10s)
- `slider_speed` (range, 0.2-1s)

**Tab 6: Desktop Grid Settings**
- `desktop_columns` (range, 1-6, default: 3)
- `tablet_columns` (range, 1-4, default: 2)
- `hover_effect` (select): zoom/squeeze/lift/glow/tilt/fade/slide/none
- `card_gap` (range, 12-48px, default: 24)
- `card_height` (range, 300-700px, default: 450)

**Outputs:**
- Desktop: Grid responsive con hover effects
- Mobile: Slider Material Design con navegación
- Contenido manual o dinámico (Packages/Posts/Deal)
- Demo cards si no hay contenido

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ✅ **BlockBase**
- Implementa: Ninguna
- Traits: Ninguno
- Usa: ContentQueryHelper (helper)

**Propiedades:**
```
Heredadas de BlockBase:
- $name, $title, $description, $category, $icon, $keywords, $mode, $supports
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor (36 líneas)
2. enqueue_assets(): void - Encola CSS y JS (18 líneas)
3. register(): void - Registra bloque y campos ACF (437 líneas - 84 + 353 de ACF fields)
4. render($block, $content, $is_preview, $post_id): void - Renderiza bloque (194 líneas)
```

**Métodos Privados:**
```
1. get_placeholder_image(): string - Genera URL de placeholder (5 líneas)
2. get_demo_cards(): array - Retorna demo cards (35 líneas)
```

---

## 4. Registro del Bloque

**Método:** `parent::register()` + `acf_add_local_field_group` (heredado de BlockBase)

**Configuración:**
- name: `posts-carousel`
- title: "Posts Carousel (Material)"
- category: `travel`
- icon: `images-alt2`
- keywords: ['posts', 'carousel', 'slider', 'material', 'grid']
- mode: `preview`
- supports: align=[wide,full], mode=true, multiple=true, anchor=true

**Block.json:** No existe

---

## 5. Campos ACF

**Definición:** [X] PHP inline (acf_add_local_field_group)

**Grupo:** `group_block_posts_carousel`

**Campos:** 27 campos principales + subcampos del repeater + campos de ContentQueryHelper

**Estructura:**
1. **Tab: Card Styles** (6 campos)
2. **Dynamic Content Fields** (via ContentQueryHelper con prefix `pc_mat`)
3. **Tab: Cards** (1 repeater con 9 subcampos, condicional a dynamic_source=none)
4. **Filter Fields** (via ContentQueryHelper con prefix `pc_mat`)
5. **Tab: Slider Settings** (6 campos)
6. **Tab: Desktop Grid** (5 campos)

**Condicionales:**
- Tab "Cards" solo visible si `pc_mat_dynamic_source == 'none'`
- `arrows_position` solo visible si `show_arrows == true`
- `autoplay_delay` solo visible si `autoplay == true`

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Check dynamic source: `get_field('pc_mat_dynamic_source')`
2. Si dynamic:
   - `ContentQueryHelper::get_content('pc_mat', 'package')` o
   - `ContentQueryHelper::get_content('pc_mat', 'post')` o
   - `ContentQueryHelper::get_deal_packages($deal_id, 'pc_mat')`
3. Si manual:
   - `get_field('cards')`
   - Fallback: `get_demo_cards()` si vacío
4. Get settings (26 campos diferentes)

**Procesamiento:**
1. Try-catch wrapper para manejo de errores (líneas 522-714)
2. Logging extensivo con `travel_info()` (10+ llamadas)
3. Prepara array `$data` con 23 keys (líneas 650-675)
4. Llama a `load_template('posts-carousel', $data)` (línea 683)

**Variables al Template:**
```php
- $block_id, $align
- $card_style, $button_color_variant, $badge_color_variant
- $text_alignment, $button_alignment, $show_favorite
- $cards (array)
- $show_arrows, $arrows_position, $show_dots
- $autoplay, $autoplay_delay, $slider_speed
- $hover_effect, $card_gap, $desktop_columns, $tablet_columns, $card_height
- $display_fields_packages, $display_fields_posts
- $is_preview, $block
```

**Manejo de Errores:**
- Try-catch completo con logging
- Error display en WP_DEBUG mode (detallado)
- Error display en production mode (genérico)
- Stack trace disponible en debug mode

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:**
- ✅ Sí usa (438 líneas)
- Implementación: Vanilla JS (sin dependencias)
- Funcionalidad: Slider mobile + hover effects desktop
- Enqueue: Frontend + Editor

**REST API:** No usa

**Hooks Propios:** No define

**Dependencias Externas:**
- ContentQueryHelper (interno)
- Placeholder images: picsum.photos (externo)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ⚠️ **PARCIAL**
- Clase hace demasiado: configuración, rendering, demo data, queries
- 756 líneas totales es excesivo
- Impacto: MEDIO

**OCP:** ✅ Cumple - Puede extenderse sin modificar

**LSP:** ✅ Cumple - Respeta contrato de BlockBase

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ Parcial
- Dependencia de funciones globales ACF (get_field)
- Dependencia de ContentQueryHelper (acoplamiento medio)
- Impacto: BAJO-MEDIO

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ **Método register() tiene 437 líneas** (incluye 353 de ACF fields inline)
- ❌ **Método render() tiene 194 líneas** (excede límite de 30)
- ✅ __construct(): 36 líneas
- ✅ enqueue_assets(): 18 líneas
- ✅ get_demo_cards(): 35 líneas
- **Crítico:** Métodos muy largos dificultan mantenimiento

**Anidación:**
- ⚠️ render() tiene 3-4 niveles de anidación (try-catch + if-else + while)

**Duplicación:**
- ❌ **DUPLICACIÓN CRÍTICA DETECTADA**
  - Existe `PostsCarouselNative` (bloque similar)
  - Ambos hacen carousels de posts
  - PostsCarouselNative: 274 líneas, sin herencia de BlockBase
  - Funcionalidad ~70% duplicada
  - Ubicación: `/src/Blocks/ACF/PostsCarouselNative.php`
  - Impacto: **CRÍTICO** - Mantenimiento doble, inconsistencias

**Nombres:**
- ✅ Nombres descriptivos y claros
- ⚠️ Prefix `pc_mat` es confuso (¿qué significa "mat"?)

**Código Sin Uso:**
- ⚠️ `get_placeholder_image()` usa picsum.photos random (no es ideal para producción)
- ⚠️ `get_demo_cards()` siempre retorna 3 cards hardcoded

**DocBlocks:**
- ✅ **BUENO** - Métodos públicos tienen PHPDoc
- ⚠️ Métodos privados no tienen PHPDoc
- ⚠️ Header class tiene descripción pero incompleta

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ get_field() con fallbacks seguros
- ⚠️ Verificar que template escapa todas las variables

**Escapado:**
- ⚠️ **Template debe escapar** (no visto en auditoría completa)
- ⚠️ Verificar escapado de: title, excerpt, link, category, location, price, cta_text
- ⚠️ Template maneja HTML inline (card_style variations)

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas
- ✅ Usa ContentQueryHelper (WP_Query con prepared statements)

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 13
  - Impacto: BAJO (funciona pero no sigue convención)

**Separación MVC:**
- ✅ **EXCELENTE** - Controller (clase) / View (template) bien separados
- ✅ Usa método `load_template()` de BlockBase

**Acoplamiento:**
- ⚠️ **Acoplamiento MEDIO-ALTO**
  - Dependencia fuerte de ContentQueryHelper
  - Dependencia de ACF functions
  - Prefix hardcoded `pc_mat` en múltiples lugares
  - Impacto: MEDIO

**Herencia:**
- ✅ **SÍ hereda de BlockBase** (correcto)
- ✅ Usa métodos heredados correctamente

**Otros:**
- ❌ **ACF fields inline** (353 líneas en register())
  - Debería estar en archivo separado
  - Dificulta lectura y mantenimiento
  - Impacto: MEDIO
- ⚠️ Logging excesivo con `travel_info()` (10+ llamadas)
  - Puede afectar performance
  - Impacto: BAJO

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque tiene duplicación crítica con PostsCarouselNative. PRIORITARIO resolver duplicación antes de refactorizar.**

### Prioridad CRÍTICA

**1. 🚨 RESOLVER DUPLICACIÓN con PostsCarouselNative**
- **Acción:**
  - Investigar diferencias entre PostsCarousel y PostsCarouselNative
  - Decidir cuál mantener o fusionar funcionalidades
  - PostsCarousel: 756 líneas, hereda BlockBase, muy completo
  - PostsCarouselNative: 274 líneas, NO hereda BlockBase, más simple
- **Razón:** Mantenimiento doble, confusión para usuarios, inconsistencias
- **Riesgo:** CRÍTICO - Afecta a contenido existente
- **Precauciones:**
  - ⛔ NO borrar ninguno hasta migrar contenido
  - ⛔ Verificar qué block_name usa cada página
  - Ejecutar: `grep -r "acf/posts-carousel\|acf-gbr-posts-carousel" wp-content/uploads/`
  - Crear plan de migración si hay contenido
- **Esfuerzo:** 3-4 horas (investigación + plan + migración)

### Prioridad Alta

**2. Extraer ACF fields a archivo separado**
- **Acción:** Mover definición de campos (líneas 83-513) a:
  - `/src/Blocks/ACF/PostsCarousel/fields.php`
  - Importar con `require_once` en register()
- **Razón:** Método register() tiene 437 líneas (353 son ACF fields)
- **Riesgo:** BAJO - Solo reorganización
- **Precauciones:**
  - Mantener exact field keys
  - Mantener conditional_logic intacto
- **Esfuerzo:** 1 hora

**3. Refactorizar método render()**
- **Acción:** Dividir render() (194 líneas) en métodos privados:
  ```php
  private function get_cards_data(): array
  private function get_block_settings(): array
  private function prepare_template_data(): array
  ```
- **Razón:** Método muy largo, difícil de mantener
- **Riesgo:** MEDIO - Lógica compleja
- **Precauciones:**
  - Mantener try-catch wrapper
  - Preservar logging
  - Testing exhaustivo
- **Esfuerzo:** 2 horas

**4. Verificar template escapa correctamente**
- **Acción:** Revisar `/templates/posts-carousel.php`:
  - ✅ `esc_url($card['link'])`
  - ✅ `esc_html($card['title'])`
  - ✅ `esc_html($card['excerpt'])`
  - ✅ `esc_attr($card['category'])`
  - ✅ `esc_html($card['location'])`
  - ✅ `esc_html($card['price'])`
  - ✅ `esc_html($card['cta_text'])`
- **Razón:** Seguridad
- **Riesgo:** ALTO - Critical si no está escapado
- **Precauciones:** No romper output HTML
- **Esfuerzo:** 30 min

### Prioridad Media

**5. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\ACF` a `Travel\Blocks\ACF`
- **Razón:** No sigue PSR-4, tiene `\Blocks\Blocks\`
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:**
  - Actualizar composer.json si es necesario
  - Ejecutar `composer dump-autoload`
  - Verificar que bloque sigue registrándose
- **Esfuerzo:** 30 min

**6. Reducir logging en producción**
- **Acción:** Wrap `travel_info()` calls en:
  ```php
  if (defined('WP_DEBUG') && WP_DEBUG) {
      travel_info(...);
  }
  ```
- **Razón:** 10+ llamadas a logging pueden afectar performance
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**7. Mejorar demo cards**
- **Acción:** En lugar de picsum.photos random:
  - Usar placeholder local del theme
  - O generar URLs determinísticas
- **Razón:** Dependencia externa, no es ideal para producción
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

### Prioridad Baja

**8. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Precauciones:** Mantener registro ACF funcionando
- **Esfuerzo:** 1 hora

**9. Documentar prefix `pc_mat`**
- **Acción:** Agregar comentario explicando significado
- **Razón:** `pc_mat` es confuso (¿material design?)
- **Riesgo:** BAJO
- **Esfuerzo:** 5 min

**10. Agregar validación de campos requeridos**
- **Acción:** En `render()`, verificar que cards tienen title:
  ```php
  if (empty($card['title'])) {
      continue; // Skip invalid cards
  }
  ```
- **Razón:** Prevenir errores si datos están incompletos
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. 🚨 **CRÍTICO:** Resolver duplicación con PostsCarouselNative (investigación)
2. Verificar template escapa correctamente (seguridad)
3. Extraer ACF fields a archivo separado
4. Refactorizar método render()
5. Corregir namespace
6. Reducir logging en producción
7. Mejorar demo cards
8. Crear block.json (opcional)
9. Documentar prefix `pc_mat` (opcional)
10. Agregar validación de campos requeridos (opcional)

**Precauciones Generales:**
- ⛔ NO cambiar block name `posts-carousel`
- ⛔ NO cambiar nombres de campos ACF
- ⛔ NO cambiar ContentQueryHelper prefix `pc_mat`
- ⛔ NO eliminar PostsCarouselNative sin plan de migración
- ⛔ NO modificar JavaScript sin testing (438 líneas complejas)
- ✅ Testing: Insertar bloque, configurar campos manual y dinámico
- ✅ Testing: Verificar slider mobile y grid desktop
- ✅ Testing: Verificar todos los card_style variants
- ✅ Testing: Verificar hover effects
- ✅ Testing: Verificar contenido dinámico (packages, posts, deals)

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo (categoría "travel")
- [ ] Se puede insertar correctamente
- [ ] Campos ACF aparecen correctamente
- [ ] Preview funciona en editor
- [ ] Frontend funciona correctamente

### Contenido Manual
- [ ] Cards repeater funciona
- [ ] Image upload funciona
- [ ] Demo cards aparecen si no hay contenido
- [ ] Todos los campos del repeater funcionan

### Contenido Dinámico
- [ ] Dynamic source selector funciona
- [ ] Packages query funciona
- [ ] Blog posts query funciona
- [ ] Deal packages query funciona
- [ ] Filtros se aplican correctamente
- [ ] Visible fields se respetan
- [ ] CTA text personalizado funciona

### Estilos y Diseño
- [ ] card_style variants funcionan (overlay/vertical/overlay-split)
- [ ] button_color_variant funciona (8 opciones)
- [ ] badge_color_variant funciona (6 opciones)
- [ ] text_alignment funciona
- [ ] button_alignment funciona
- [ ] show_favorite funciona

### Slider Mobile
- [ ] Slider funciona en mobile
- [ ] Arrows navigation funciona
- [ ] Arrows position funciona (sides/overlay/bottom)
- [ ] Dots pagination funciona
- [ ] Autoplay funciona (si activado)
- [ ] Autoplay delay funciona

### Grid Desktop
- [ ] Grid 3 columnas funciona en desktop
- [ ] desktop_columns funciona (1-6)
- [ ] tablet_columns funciona (1-4)
- [ ] hover_effect funciona (8 opciones)
- [ ] card_gap funciona
- [ ] card_height funciona

### Arquitectura
- [ ] Namespace correcto (si se cambió)
- [ ] Hereda de BlockBase (ya OK)
- [ ] load_template() funciona
- [ ] ACF fields en archivo separado (si se movió)
- [ ] Método render() refactorizado (si se dividió)

### Seguridad
- [ ] Template escapa title
- [ ] Template escapa excerpt
- [ ] Template escapa link
- [ ] Template escapa category
- [ ] Template escapa location
- [ ] Template escapa price
- [ ] Template escapa cta_text
- [ ] Validación de campos requeridos (si se agregó)

### Duplicación
- [ ] Duplicación con PostsCarouselNative resuelta
- [ ] Plan de migración ejecutado (si aplica)
- [ ] Contenido existente no roto

### Clean Code
- [ ] Logging reducido en producción (si se cambió)
- [ ] Demo cards mejoradas (si se cambió)
- [ ] Prefix documentado (si se agregó)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ⚠️ **DUPLICACIÓN CRÍTICA CON PostsCarouselNative**
- ✅ Hereda de BlockBase correctamente
- ⚠️ Métodos demasiado largos (register: 437, render: 194)
- ⚠️ ACF fields inline (353 líneas)
- ⚠️ Namespace incorrecto
- ✅ Usa ContentQueryHelper correctamente
- ✅ Separación MVC correcta
- ⚠️ Logging excesivo
- ⚠️ Dependencia externa (picsum.photos)
- ⚠️ JavaScript complejo (438 líneas sin auditar)

### Puntuación: 6.5/10

**Fortalezas:**
- Hereda de BlockBase (mejor que PostsCarouselNative, FlexibleGridCarousel, HeroCarousel)
- Usa ContentQueryHelper para contenido dinámico
- Separación MVC correcta con load_template()
- Manejo de errores robusto con try-catch
- Soporta múltiples fuentes de contenido (manual, packages, posts, deals)
- Muchas opciones de personalización
- Demo cards para preview

**Debilidades:**
- ❌ **DUPLICACIÓN CRÍTICA** con PostsCarouselNative (~70% funcionalidad duplicada)
- ❌ Métodos muy largos (register: 437, render: 194)
- ❌ ACF fields inline (353 líneas dificultan lectura)
- ⚠️ Namespace incorrecto (Travel\Blocks\Blocks\ACF)
- ⚠️ Logging excesivo (10+ llamadas)
- ⚠️ Dependencia externa (picsum.photos)
- ⚠️ Prefix confuso (`pc_mat`)
- ⚠️ 756 líneas totales (muy complejo)

**Recomendación:**
🚨 **ACCIÓN CRÍTICA REQUERIDA**

Este bloque está mejor implementado que PostsCarouselNative (hereda BlockBase, más completo), pero la duplicación es CRÍTICA.

**Paso 1:** Investigar cuál bloque se usa en producción
**Paso 2:** Decidir estrategia (mantener este, migrar contenido, o fusionar)
**Paso 3:** Refactorizar métodos largos y extraer ACF fields

La duplicación es el problema #1. Todo lo demás es secundario.

**Comparación:**
- **PostsCarousel:** 756 líneas, hereda BlockBase ✅, muy completo, complejo
- **PostsCarouselNative:** 274 líneas, NO hereda BlockBase ❌, más simple
- **Recomendación:** Mantener PostsCarousel, migrar contenido de PostsCarouselNative

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** CRÍTICA - Resolver duplicación antes de cualquier refactorización
