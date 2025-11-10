# Auditoría: TaxonomyArchiveHero (Template)

**Fecha:** 2025-11-09
**Bloque:** 6/X Template
**Tiempo:** 45 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `taxonomy-archive-hero`
- **Namespace:** `travel/taxonomy-archive-hero` (Template block)
- **Métodos públicos:** `register()`, `register_block()`, `enqueue_assets()`, `render_block()`, `register_fields()`
- **Clases CSS:** Comparte con HeroCarousel (`.hc-hero-carousel`, `.hc-card`, etc.)
- **Templates compartidos:** Usa templates de HeroCarousel (`bottom.php`, `top.php`, `side_left.php`, `side_right.php`)

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **Comparte assets con HeroCarousel** - Cambios en CSS/JS afectan ambos bloques
- **Usa ContentQueryHelper** - Dependencia externa crítica
- **InnerBlocks** - El contenido del hero viene de bloques anidados
- **Fallback de imágenes** - Lógica compleja de obtención de imágenes desde taxonomía

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Template/TaxonomyArchiveHero.php`
**Namespace:** `Travel\Blocks\Blocks\Template` (⚠️ Incorrecto - `\Blocks\Blocks\`)
**Templates:**
- `/wp-content/plugins/travel-blocks/src/Blocks/HeroCarousel/templates/bottom.php` (compartido)
- `/wp-content/plugins/travel-blocks/src/Blocks/HeroCarousel/templates/top.php` (compartido)
- `/wp-content/plugins/travel-blocks/src/Blocks/HeroCarousel/templates/side_left.php` (compartido)
- `/wp-content/plugins/travel-blocks/src/Blocks/HeroCarousel/templates/side_right.php` (compartido)

**Assets:**
- CSS: `/assets/blocks/HeroCarousel/style.css` (1656 líneas - compartido)
- JS: `/assets/blocks/HeroCarousel/carousel.js` (355 líneas - compartido)
- JS Editor: `/assets/blocks/HeroCarousel/editor.js` (solo admin)

**Tipo:** [ ] ACF  [X] Gutenberg Nativo con ACF (Hybrid)

---

## 2. Propósito y Funcionalidad

**Descripción:** Hero carousel específico para archives de taxonomías con fallback automático de imágenes desde packages relacionados con el término de taxonomía actual.

**Diferencia con ACF/HeroCarousel:**
- **Template/TaxonomyArchiveHero:** Para usar en taxonomy archives, detecta término de taxonomía y busca imágenes automáticamente
- **ACF/HeroCarousel:** Para insertar manualmente en cualquier página, sin detección automática de taxonomía

**Inputs:**
- **Hero image** (opcional) - Si no se proporciona, busca automáticamente:
  1. Random package de la taxonomía actual
  2. Random package cualquiera
  3. Random imagen de media library
  4. Fallback a picsum.photos
- **InnerBlocks** - Contenido del hero (headings, párrafos, botones)
- **Dynamic source** - Contenido de cards: manual, packages, posts, o deal
- **Layout variations** - bottom, top, side_left, side_right
- **Cards repeater** - Cards manuales (si no usa dynamic source)
- **Múltiples opciones de estilo** - colores, alineación, dimensiones, carousel

**Outputs:**
- HTML completo de hero carousel con cards
- Compatible con Query Loop (aunque es Template block)
- Soporta WordPress Block Supports (spacing, colors, typography)

**Contextos soportados:**
- Taxonomy archives (detecta término automáticamente)
- Preview en editor (usa imagen random)
- Cualquier página (funciona como HeroCarousel normal)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: Ninguna
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```
Ninguna (todos métodos públicos y privados)
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor vacío (comentario indica que Plugin.php llama directamente)
2. register(): void - Registra bloque, campos y assets (15 líneas)
3. register_block(): void - Registra bloque ACF con acf_register_block_type (74 líneas)
4. enqueue_assets(): void - Encola CSS y JS compartidos con HeroCarousel (27 líneas)
5. render_block(array $block, string $content, bool $is_preview): void - Renderiza bloque (160 líneas) ⚠️ MUY LARGO
6. register_fields(): void - Registra campos ACF (691 líneas) ❌ EXCESIVAMENTE LARGO
```

**Métodos Privados:**
```
1. get_taxonomy_or_random_image(bool $is_preview): array|null - Obtiene imagen con fallback (38 líneas)
2. get_random_package_image_from_taxonomy(string $taxonomy, int $term_id): array|null - Imagen de package con taxonomía (36 líneas)
3. get_random_package_image(): array|null - Imagen de package cualquiera (28 líneas)
4. get_random_media_image(): array|null - Imagen random de media library (24 líneas)
5. get_demo_cards(): array - Retorna cards demo hardcoded (135 líneas) ⚠️ LARGO
```

**Líneas totales:** 1263 líneas ❌ EXCESIVAMENTE LARGO

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type()` (líneas 24-94)

**Configuración:**
- name: `taxonomy-archive-hero`
- title: "Taxonomy Archive Hero"
- description: "Hero carousel for taxonomy archives with automatic image fallback"
- category: `template-blocks`
- icon: `archive`
- keywords: ['hero', 'taxonomy', 'archive', 'background', 'slider']
- mode: `preview`
- render_callback: `[$this, 'render_block']`

**Block.json:** No existe

**Supports:**
- align: ['wide', 'full']
- spacing: margin, padding, blockGap
- color: background, text, gradients
- typography: fontSize, lineHeight
- anchor, customClassName

**Example:** Incluye preview con InnerBlocks de ejemplo (heading, paragraph, buttons)

---

## 5. Campos ACF

**Definición:** `register_fields()` - 691 líneas ❌ MÉTODO EXCESIVAMENTE LARGO

**Tabs:**
1. **⚙️ General** - Layout, columns, proporción texto/cards
2. **🎨 Card Styles** - Colores de botones/badges, alineación texto/botones
3. **🖼️ Hero Content** - Hero image, mensaje sobre InnerBlocks
4. **📏 Dimensions** - Heights (mobile/tablet/desktop), cards height/width, negative margins
5. **🎬 Carousel** - Arrows, dots, autoplay, delay
6. **📦 Dynamic Content** (inyectado por ContentQueryHelper) - Source, limit, orderby, filters
7. **🃏 Cards** - Repeater de cards manuales (condicional si source = 'none')
8. **🔍 Filter Fields** (inyectado por ContentQueryHelper) - Filtros de taxonomías

**Total campos:** ~50+ campos (muy extenso)

**Uso de ContentQueryHelper:**
- `ContentQueryHelper::get_dynamic_content_fields('tah')` - Campos de dynamic content con prefix 'tah'
- `ContentQueryHelper::get_filter_fields('tah')` - Campos de filtros con prefix 'tah'

**Configuración de cards:** Repeater con 12 sub-fields por card:
- image, category, badge_color_variant, title, excerpt, date, link, cta_text, location, price

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render_block()` (líneas 126-286) - 160 líneas ⚠️ MUY LARGO

**Obtención de Datos:**

1. **Block wrapper attributes** (líneas 128-130)
   ```php
   $block_wrapper_attributes = get_block_wrapper_attributes(['class' => 'hero-carousel-wrapper']);
   ```

2. **ACF fields** (líneas 133-170)
   - Layout variation, button/badge colors, text/button alignment
   - Content proportion, hero image
   - Columns, margins, heights
   - Carousel settings (arrows, dots, autoplay)

3. **Hero image con fallback** (líneas 148-150)
   - Si no hay hero_image, llama `get_taxonomy_or_random_image($is_preview)`
   - Fallback en cascada: taxonomy package → random package → media library → picsum

4. **InnerBlocks content** (líneas 152-154)
   - `$content` contiene el HTML de InnerBlocks
   - `$has_hero_text` verifica si hay contenido

5. **Dynamic source detection** (líneas 173-229)
   - **package:** `ContentQueryHelper::get_content('tah', 'package')`
   - **post:** `ContentQueryHelper::get_content('tah', 'post')`
   - **deal:** `ContentQueryHelper::get_deal_packages($deal_id, 'tah')`
   - **none/manual:** `get_field('cards')` o `get_demo_cards()`

6. **Demo images fallback** (líneas 213-228)
   - Si cards manuales tienen imagen vacía, rellena con picsum.photos
   - Usa `&$card` para modificar por referencia
   - `unset($card)` rompe la referencia (buena práctica)

7. **Display fields** (líneas 236-237) - ⚠️ Variables no usadas
   ```php
   $display_fields_packages = get_field('tah_mat_dynamic_visible_fields') ?: [];
   $display_fields_posts = get_field('tah_mat_dynamic_visible_fields') ?: [];
   ```

**Procesamiento:**

1. **Template data array** (líneas 240-272)
   - 32 variables pasadas al template
   - Incluye todas las configuraciones y datos

2. **Template loading** (líneas 275-285)
   - Ruta dinámica: `HeroCarousel/templates/{$layout_variation}.php`
   - Usa `extract()` para convertir array en variables
   - Output buffering con `ob_start()` / `ob_get_clean()`

**Lógica en Template:**
- Templates esperan: `$hero_image`, `$hero_content`, `$cards`, `$columns_desktop`, etc.
- Estilos inline generados dinámicamente (heights, margins)
- Skeleton loader (oculto en preview)
- Carousel o grid según `$is_carousel`

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:** Sí - `carousel.js` (compartido con HeroCarousel)
- Clase `HeroCarousel` con navegación
- IntersectionObserver para scroll tracking
- Autoplay con pause on hover
- Keyboard navigation (arrows)
- Responsive (dots solo en mobile)

**REST API:** No usa directamente (ContentQueryHelper puede cachear con transients)

**Hooks Propios:** No define

**Dependencias Externas:**
- `ContentQueryHelper` - Para dynamic content y filtros ⚠️ DEPENDENCIA FUERTE
- `acf_register_block_type()` - ACF Pro
- `get_field()` - ACF Pro
- `get_queried_object()` - WordPress core
- Templates de HeroCarousel - ⚠️ DEPENDENCIA FUERTE

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ❌ **VIOLA** - Clase hace demasiadas cosas
- Ubicación: Toda la clase
- Impacto: ALTO
- Responsabilidades mezcladas:
  1. Registro de bloque ACF
  2. Enqueue de assets
  3. Renderizado complejo con lógica de negocio
  4. Obtención de imágenes con múltiples fallbacks
  5. Generación de datos demo (135 líneas hardcoded)
  6. Registro de 50+ campos ACF (691 líneas)

**OCP:** ⚠️ **VIOLA PARCIALMENTE**
- Ubicación: `register_fields()` (líneas 570-1261)
- Impacto: MEDIO
- Problema: Agregar nuevo tipo de campo requiere modificar método gigante
- ✅ Usa ContentQueryHelper para inyectar campos (buena práctica)

**LSP:** ✅ N/A - No extiende nada

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA** - Dependencias directas sin abstracción
- Ubicación: Múltiple
- Impacto: MEDIO
- Problemas:
  - Instancia directa de funciones WP globales (get_queried_object, get_posts, etc.)
  - Dependencia fuerte de ContentQueryHelper (líneas 177, 185, 195, 573, 574)
  - Dependencia fuerte de templates HeroCarousel
  - Dificulta testing unitario

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ **CRÍTICO: Método `register_fields()` tiene 691 líneas** (570-1261)
  - Ubicación: register_fields()
  - Impacto: CRÍTICO - Imposible de mantener
  - Debería dividirse en múltiples métodos: `get_general_fields()`, `get_style_fields()`, `get_hero_fields()`, etc.

- ❌ **CRÍTICO: Método `render_block()` tiene 160 líneas** (126-286)
  - Ubicación: render_block()
  - Impacto: ALTO - Demasiada lógica en un método
  - Debería extraer: `prepare_hero_image()`, `prepare_cards_data()`, `prepare_template_data()`

- ❌ **Método `get_demo_cards()` tiene 135 líneas de datos hardcoded** (433-568)
  - Ubicación: get_demo_cards()
  - Impacto: MEDIO - Datos deberían estar en archivo JSON o config
  - 6 cards demo con todos los campos (imagen, category, title, excerpt, etc.)

**Anidación:**
- ⚠️ Anidación de 4 niveles en `register_fields()`
  - Ubicación: Líneas 843-906 (conditional_logic anidado)
  - Impacto: MEDIO - Dificulta lectura

- ✅ Anidación aceptable en `render_block()` (máximo 3 niveles)

**Duplicación:**
- ❌ **CRÍTICO: Duplicación total con ACF/HeroCarousel**
  - Ubicación: Toda la clase
  - Impacto: CRÍTICO
  - Duplicaciones:
    1. `register_fields()` - Casi idéntico a HeroCarousel (691 líneas)
    2. `render_block()` - 80% idéntico a HeroCarousel (160 líneas)
    3. `get_demo_cards()` - Idéntico a HeroCarousel (135 líneas)
    4. Métodos de imagen: get_random_package_image, get_random_media_image (idénticos)
  - **Diferencias reales:**
    - `get_taxonomy_or_random_image()` - Único método nuevo (38 líneas)
    - `get_random_package_image_from_taxonomy()` - Único método nuevo (36 líneas)
    - Prefix 'tah' en ContentQueryHelper (vs 'hc' en HeroCarousel)

- ⚠️ Métodos de obtención de imágenes duplicados:
  - `get_random_package_image()` - Idéntico en HeroCarousel y TaxonomyArchiveHero
  - `get_random_media_image()` - Idéntico en HeroCarousel y TaxonomyArchiveHero

**Nombres:**
- ✅ Nombres descriptivos y claros
- ✅ Métodos privados bien nombrados
- ⚠️ Variable `$display_fields_posts` usa mismo campo que `$display_fields_packages` (líneas 236-237) - probablemente error

**Código Sin Uso:**
- ⚠️ **Variables no usadas** (líneas 236-237, 269-270)
  ```php
  $display_fields_packages = get_field('tah_mat_dynamic_visible_fields') ?: [];
  $display_fields_posts = get_field('tah_mat_dynamic_visible_fields') ?: [];
  ```
  Estas variables se pasan al template pero no se usan en ningún template

- ⚠️ Constructor vacío (líneas 9-11)
  ```php
  public function __construct() {
      // Los métodos se llaman directamente desde Plugin.php
  }
  ```

**Magic Numbers:**
- ✅ Valores por defecto bien definidos (45%, 3 columns, 450px, etc.)
- ⚠️ Algunos valores hardcoded en demo: 310-316 (líneas 217, 437-565)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ Type hints en parámetros: `array $block`, `string $content`, `bool $is_preview`
- ✅ Funciones WP sanitizan automáticamente (get_field, get_queried_object, etc.)
- ⚠️ `$content` viene de InnerBlocks - Se confía en que WordPress lo sanitiza

**Escapado:**
- ✅ Template escapa correctamente (esc_attr, esc_url, esc_html)
- ✅ Data pasada al template es limpia
- ⚠️ Uso de `extract()` (línea 278) - No es inseguro pero puede causar conflictos de variables

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas, solo funciones WP
- ✅ ContentQueryHelper usa WP_Query correctamente
- ✅ Parámetros de taxonomía sanitizados (líneas 339-346)

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto** (línea 3)
  - Actual: `Travel\Blocks\Blocks\Template`
  - Esperado: `Travel\Blocks\Template`
  - Impacto: BAJO - Funciona pero no sigue PSR-4
  - **NOTA:** Mismo problema que otros bloques Template

**Separación MVC:**
- ⚠️ **Separación parcial**
  - Controller: ✅ Clase PHP
  - View: ✅ Templates separados (pero compartidos con HeroCarousel)
  - Model: ❌ Lógica de negocio mezclada en controller
  - Problema: `render_block()` tiene demasiada lógica de preparación de datos

**Acoplamiento:**
- ❌ **ALTO ACOPLAMIENTO** con HeroCarousel
  - Comparte: Templates, CSS, JS
  - Problema: Cambios en HeroCarousel afectan este bloque
  - Impacto: ALTO

- ❌ **ALTO ACOPLAMIENTO** con ContentQueryHelper
  - Ubicación: Líneas 177, 185, 195, 573, 574
  - Problema: No hay interfaz ni abstracción
  - Impacto: MEDIO-ALTO - Dificulta testing

**Otros:**
- ❌ **CRÍTICO: 95% de código duplicado con HeroCarousel**
  - **Solo 74 líneas son únicas** (métodos de taxonomía)
  - **1189 líneas son duplicadas** (94% del archivo)
  - Esto es un problema SEVERO de arquitectura
  - Debería compartir clase base o ser una extensión

- ❌ **Sin block.json**
  - WordPress recomienda block.json para bloques nativos
  - Impacto: BAJO (funciona sin él)

- ⚠️ **Comparte templates con HeroCarousel**
  - Ubicación: Línea 275
  - Impacto: MEDIO - Cambios en templates afectan ambos bloques
  - Ventaja: Consistencia visual
  - Desventaja: Requiere mantener compatibilidad

- ⚠️ **Uso de `extract()`** (línea 278)
  - Puede causar conflictos de nombres de variables
  - Dificulta debugging
  - Considerado anti-pattern en código moderno

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque está en uso en producción. NO cambiar block name, métodos públicos ni estructura de templates.**

### Prioridad CRÍTICA

**1. ❌ REFACTOR ARQUITECTURAL: Consolidar con HeroCarousel**
- **Acción:** Crear arquitectura compartida:
  ```php
  abstract class HeroCarouselBase {
      // Métodos comunes: render_block, register_fields, get_demo_cards, etc.
  }

  class HeroCarousel extends HeroCarouselBase {
      // Específico: sin detección de taxonomía
  }

  class TaxonomyArchiveHero extends HeroCarouselBase {
      // Específico: get_taxonomy_or_random_image()
  }
  ```
- **Razón:** 94% de código duplicado es INACEPTABLE
- **Riesgo:** ALTO - Refactor arquitectural mayor
- **Precauciones:**
  - Mantener ambos bloques funcionando EXACTAMENTE igual
  - NO cambiar block names ni métodos públicos
  - Testing extensivo después del refactor
  - Considerar hacer en múltiples PRs pequeños
- **Esfuerzo:** 2-3 días

**2. ❌ Dividir `register_fields()` - 691 líneas es INACEPTABLE**
- **Acción:** Dividir en métodos pequeños:
  ```php
  private function get_general_fields(): array
  private function get_style_fields(): array
  private function get_hero_fields(): array
  private function get_dimension_fields(): array
  private function get_carousel_fields(): array
  private function get_cards_fields(): array
  ```
- **Razón:** Método gigante imposible de mantener
- **Riesgo:** MEDIO - Es método privado
- **Precauciones:**
  - Verificar que ACF fields se registran correctamente
  - Testing de todos los campos en editor
- **Esfuerzo:** 4-6h

**3. ❌ Dividir `render_block()` - 160 líneas es EXCESIVO**
- **Acción:** Extraer lógica a métodos privados:
  ```php
  private function prepare_hero_image(bool $is_preview): array
  private function prepare_cards_data(): array
  private function prepare_template_data(): array
  ```
- **Razón:** Método muy largo con demasiada responsabilidad
- **Riesgo:** MEDIO
- **Precauciones:**
  - Mantener output exacto
  - Testing visual después del refactor
- **Esfuerzo:** 2-3h

### Prioridad Alta

**4. Extraer datos demo a archivo JSON**
- **Acción:** Mover `get_demo_cards()` a `demo-cards.json` o config
- **Razón:** 135 líneas de datos hardcoded no deberían estar en clase
- **Riesgo:** BAJO
- **Precauciones:** Verificar que preview funciona
- **Esfuerzo:** 1-2h

**5. Corregir namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\Template` a `Travel\Blocks\Template`
- **Razón:** No sigue PSR-4, tiene `\Blocks\Blocks\`
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:**
  - Actualizar composer.json
  - `composer dump-autoload`
  - Verificar que bloque sigue registrándose
- **Esfuerzo:** 30 min

**6. Eliminar código sin uso**
- **Acción:** Remover variables no usadas:
  - `$display_fields_packages`, `$display_fields_posts` (líneas 236-237, 269-270)
- **Razón:** Variables que se pasan al template pero no se usan
- **Riesgo:** BAJO
- **Precauciones:** Verificar que templates no las usan
- **Esfuerzo:** 15 min

### Prioridad Media

**7. Crear servicio compartido para imágenes**
- **Acción:** Extraer lógica de imágenes a `ImageFallbackService`:
  ```php
  class ImageFallbackService {
      public function get_taxonomy_image($taxonomy, $term_id): ?array
      public function get_random_package_image(): ?array
      public function get_random_media_image(): ?array
  }
  ```
- **Razón:** Lógica duplicada entre HeroCarousel y TaxonomyArchiveHero
- **Riesgo:** MEDIO
- **Precauciones:** Mantener output exacto
- **Esfuerzo:** 2-3h

**8. Crear block.json**
- **Acción:** Crear block.json con metadata del bloque
- **Razón:** WordPress recomienda block.json para bloques nativos
- **Riesgo:** BAJO
- **Precauciones:** Mantener compatibilidad con registro PHP
- **Esfuerzo:** 1h

**9. Reemplazar `extract()` con variables individuales**
- **Acción:** Pasar variables individualmente al template:
  ```php
  include $template_file; // Template accede a $template_data directamente
  ```
- **Razón:** `extract()` puede causar conflictos y dificulta debugging
- **Riesgo:** BAJO-MEDIO (requiere cambiar templates)
- **Precauciones:** Actualizar todos los templates
- **Esfuerzo:** 2h

### Prioridad Baja

**10. Agregar filtros para extender**
- **Acción:** Agregar hooks:
  ```php
  apply_filters('travel_blocks/taxonomy_archive_hero/hero_image', $hero_image, $post_id)
  apply_filters('travel_blocks/taxonomy_archive_hero/cards', $cards, $dynamic_source)
  ```
- **Razón:** Permitir customización sin modificar código
- **Riesgo:** BAJO
- **Precauciones:** Documentar filtros
- **Esfuerzo:** 1h

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **CRÍTICO:** Refactor arquitectural - Consolidar con HeroCarousel (2-3 días)
2. **CRÍTICO:** Dividir `register_fields()` en métodos pequeños (4-6h)
3. **CRÍTICO:** Dividir `render_block()` en métodos pequeños (2-3h)
4. **ALTO:** Extraer datos demo a JSON (1-2h)
5. **ALTO:** Corregir namespace (30 min)
6. **ALTO:** Eliminar código sin uso (15 min)
7. Crear servicio compartido para imágenes (2-3h)
8. Crear block.json (1h)
9. Reemplazar extract() (2h)
10. Agregar filtros de extensión (1h)

**Precauciones Generales:**
- ⛔ NO cambiar block name `taxonomy-archive-hero`
- ⛔ NO cambiar métodos públicos
- ⛔ NO cambiar estructura de templates (compartidos con HeroCarousel)
- ⛔ NO cambiar clases CSS (compartidas con HeroCarousel)
- ✅ Testing: Verificar en taxonomy archives
- ✅ Testing: Verificar detección de taxonomía
- ✅ Testing: Verificar fallback de imágenes
- ✅ Testing: Verificar preview en editor
- ✅ Testing: Verificar dynamic content (packages, posts, deals)

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en inserter
- [ ] Se puede insertar en taxonomy archives
- [ ] Preview funciona en editor con datos demo
- [ ] Frontend funciona en taxonomy archives
- [ ] Detección automática de taxonomía funciona
- [ ] Fallback de imágenes funciona correctamente:
  - [ ] Package de taxonomía actual
  - [ ] Package random
  - [ ] Media library
  - [ ] Picsum fallback
- [ ] InnerBlocks funcionan para contenido del hero
- [ ] Dynamic content funciona (packages, posts, deals)
- [ ] Cards manuales funcionan
- [ ] Layouts funcionan (bottom, top, side_left, side_right)
- [ ] Carousel funciona (navegación, autoplay, dots)

### Arquitectura
- [ ] Código compartido con HeroCarousel refactorizado (si se implementó)
- [ ] Métodos divididos correctamente (register_fields, render_block)
- [ ] Namespace correcto (si se cambió)
- [ ] Datos demo en archivo separado (si se implementó)
- [ ] block.json creado (si se implementó)

### Seguridad
- [ ] Escapado en templates correcto
- [ ] Type hints correctos
- [ ] No hay SQL injection (usa WP_Query)

### Clean Code
- [ ] Sin métodos >100 líneas
- [ ] Sin duplicación de código
- [ ] Sin código sin uso
- [ ] Nombres claros y descriptivos

---

## 12. Comparación con ACF/HeroCarousel

### Similitudes

✅ **CASI TODO ES IDÉNTICO** - 94% del código es duplicado

| Aspecto | Idéntico? |
|---------|-----------|
| `register_fields()` | ✅ 99% idéntico (691 líneas) |
| `render_block()` | ⚠️ 80% idéntico (160 líneas) |
| `get_demo_cards()` | ✅ 100% idéntico (135 líneas) |
| `get_random_package_image()` | ✅ 100% idéntico (28 líneas) |
| `get_random_media_image()` | ✅ 100% idéntico (24 líneas) |
| `enqueue_assets()` | ✅ 100% idéntico (27 líneas) |
| Templates | ✅ Comparte los mismos templates |
| CSS | ✅ Comparte el mismo CSS (1656 líneas) |
| JavaScript | ✅ Comparte el mismo JS (355 líneas) |

### Diferencias Críticas

**SOLO 74 líneas son únicas** (6% del archivo):

| Aspecto | HeroCarousel | TaxonomyArchiveHero |
|---------|--------------|---------------------|
| **Propósito** | Hero general para cualquier página | Hero específico para taxonomy archives |
| **Tipo** | ACF Block | ACF Block (híbrido con Template) |
| **Block name** | `hero-carousel` | `taxonomy-archive-hero` |
| **Category** | `acf-blocks` | `template-blocks` |
| **Prefix ContentQueryHelper** | `hc` | `tah` |
| **Métodos únicos** | Ninguno | `get_taxonomy_or_random_image()` (38 líneas)<br>`get_random_package_image_from_taxonomy()` (36 líneas) |
| **Detección de taxonomía** | ❌ No | ✅ Sí (líneas 291-328) |
| **Fallback de imágenes** | Random package → Media → Picsum | **Taxonomy package** → Random package → Media → Picsum |
| **Código** | 1226 líneas | 1263 líneas (+37 líneas para taxonomía) |

### ¿Hay Duplicación?

**Respuesta:** ❌ **SÍ, DUPLICACIÓN CRÍTICA - 94% del código es idéntico**

**Análisis:**
1. **1189 líneas duplicadas** (94%)
2. **74 líneas únicas** (6%) - Solo lógica de taxonomía
3. Ambos comparten: Templates, CSS, JS, campos ACF, demo data

**Problema:** Esta NO es herencia ni composición, es COPY-PASTE masivo.

**Evidencia:**
- `register_fields()`: 691 líneas idénticas
- `render_block()`: 128/160 líneas idénticas (80%)
- `get_demo_cards()`: 135 líneas idénticas
- Templates compartidos: 4 archivos PHP
- Assets compartidos: 1 CSS (1656 líneas), 1 JS (355 líneas)

### Recomendación URGENTE

❌ **ESTO NO ES SOSTENIBLE**

**Opciones de refactor:**

**Opción A: Clase Base Abstracta** (RECOMENDADO)
```php
abstract class HeroCarouselBase {
    protected function get_hero_image(bool $is_preview): array {
        // Implementación por defecto
        return $this->get_random_package_image();
    }

    protected function render_block() {
        $hero_image = $this->get_hero_image($is_preview);
        // Resto del código común
    }
}

class HeroCarousel extends HeroCarouselBase {
    // Usa implementación por defecto
}

class TaxonomyArchiveHero extends HeroCarouselBase {
    protected function get_hero_image(bool $is_preview): array {
        // Override: Busca en taxonomía primero
        $taxonomy_image = $this->get_taxonomy_or_random_image($is_preview);
        if ($taxonomy_image) return $taxonomy_image;
        return parent::get_hero_image($is_preview);
    }
}
```

**Opción B: Composición con Servicio**
```php
class ImageFallbackService {
    public function get_image(array $strategies): array {
        foreach ($strategies as $strategy) {
            $image = $strategy();
            if ($image) return $image;
        }
        return $this->get_default_image();
    }
}

// Ambos bloques usan el servicio con diferentes estrategias
```

**Opción C: Convertir TaxonomyArchiveHero en opción de HeroCarousel**
- Agregar campo "Auto-detect taxonomy image" a HeroCarousel
- Unificar ambos bloques en uno solo
- TaxonomyArchiveHero se vuelve alias/wrapper

**Recomendación:** Opción A (clase base) + Opción B (servicio de imágenes)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ❌ **94% de código duplicado con HeroCarousel** (CRÍTICO)
- ❌ **Método de 691 líneas** - register_fields() (INACEPTABLE)
- ❌ **Método de 160 líneas** - render_block() (EXCESIVO)
- ❌ **135 líneas de datos hardcoded** - get_demo_cards()
- ⚠️ **Namespace incorrecto** (PSR-4)
- ⚠️ **Alto acoplamiento** con HeroCarousel y ContentQueryHelper
- ⚠️ **Variables no usadas** (display_fields_*)
- ✅ **Seguridad OK** (escapado completo)
- ✅ **Funcionalidad única bien implementada** (detección de taxonomía)

### Puntuación: 3/10
**Nota:** La funcionalidad es excelente (9/10), pero la arquitectura es PÉSIMA (1/10)

**Fortalezas:**
- ✅ Funcionalidad única y útil (detección de taxonomía)
- ✅ Fallback de imágenes bien pensado (4 niveles)
- ✅ Seguridad correcta (escapado, sanitización)
- ✅ Usa ContentQueryHelper correctamente
- ✅ Soporta InnerBlocks
- ✅ Templates compartidos (consistencia visual)
- ✅ Métodos de taxonomía bien implementados (74 líneas únicas)

**Debilidades CRÍTICAS:**
- ❌ **94% de código duplicado** - INACEPTABLE (1189 líneas)
- ❌ **register_fields(): 691 líneas** - Método gigante
- ❌ **render_block(): 160 líneas** - Método muy largo
- ❌ **get_demo_cards(): 135 líneas** - Datos hardcoded
- ❌ **Violación masiva de DRY** - Copy-paste de HeroCarousel
- ❌ **Violación de SRP** - Clase hace TODO
- ❌ **Sin abstracción** - No usa herencia ni composición
- ⚠️ Namespace incorrecto
- ⚠️ Variables no usadas

**Recomendación:**
1. **URGENTE:** Refactor arquitectural completo - Consolidar con HeroCarousel
2. **CRÍTICO:** Dividir métodos gigantes
3. **ALTO:** Corregir namespace y eliminar código sin uso

**Métricas:**
- **Líneas totales:** 1263 líneas
- **Líneas únicas:** 74 líneas (6%)
- **Líneas duplicadas:** 1189 líneas (94%) ❌
- **Método más largo:** register_fields() - 691 líneas ❌
- **Segundo más largo:** render_block() - 160 líneas ⚠️
- **Tercero más largo:** get_demo_cards() - 135 líneas ⚠️

**Tiempo estimado de refactor completo:** 3-5 días

---

**Auditoría completada:** 2025-11-09
**Refactorización:** URGENTE (refactor arquitectural) + CRÍTICO (dividir métodos gigantes)
