# Auditoría: HeroCarousel (ACF)

**Fecha:** 2025-11-09
**Bloque:** 5/15 ACF
**Tiempo:** 50 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `hero-carousel`
- **Namespace:** `acf/hero-carousel`
- **Campos ACF:** `layout_variation`, `hero_image`, `cards` (repeater), etc.
- **JavaScript:** `carousel.js` + `editor.js` - manejan carrusel y padding fix
- **Clases CSS:** `.hero-carousel-wrapper` - usada en JavaScript
- **InnerBlocks:** Usa InnerBlocks para hero text content (línea 153-154)

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **Layout variations:** 4 templates diferentes (bottom, top, side_left, side_right)
- Usa `ContentQueryHelper` para contenido dinámico (packages, posts, deals)
- Editor.js solo se carga en admin (línea 115) - padding fix
- InnerBlocks rendering vía `$content` parameter (línea 250)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/HeroCarousel.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Templates:** `/wp-content/plugins/travel-blocks/src/Blocks/HeroCarousel/templates/`
- `bottom.php` (cards abajo, hero arriba)
- `top.php` (cards arriba, hero abajo)
- `side_left.php` (cards izquierda con half hidden)
- `side_right.php` (cards derecha con half hidden)

**Assets:**
- CSS: `/assets/blocks/HeroCarousel/style.css`
- JS: `/assets/blocks/HeroCarousel/carousel.js` (CRÍTICO - carrusel)
- JS: `/assets/blocks/HeroCarousel/editor.js` (solo admin - padding fix)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo  [X] Usa InnerBlocks

---

## 2. Propósito y Funcionalidad

**Descripción:** Hero section con imagen de fondo y cards en carrusel/grid. Soporta 4 variaciones de layout y contenido dinámico desde CPTs.

**Inputs (ACF):**
- **General:** `layout_variation` (4 opciones), `columns_desktop`, `content_proportion`
- **Styles:** `button_color_variant`, `badge_color_variant`, `text_alignment`, `button_alignment`
- **Hero Content:** `hero_image`, InnerBlocks para texto
- **Dimensions:** `cards_height`, `cards_width`, `hero_height_mobile/tablet/desktop`
- **Negative Margins:** Top, bottom, left, right (para overlaps)
- **Carousel:** `show_arrows`, `show_dots`, `enable_autoplay`, `autoplay_delay`
- **Dynamic Content:** `hc_dynamic_source` (none/package/post/deal)
- **Cards (Manual):** Repeater con 11 sub-fields

**Outputs:**
- Hero section con imagen de fondo
- InnerBlocks para contenido de hero (headings, paragraphs, buttons)
- Cards en grid o carrusel (depende de cantidad vs columns)
- Contenido dinámico desde Packages/Posts/Deals

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: Ninguna (❌ NO hereda de BlockBase)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```
Ninguna (todo local en métodos)
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor vacío
2. register(): void - Registra bloque, campos y assets
3. register_block(): void - Configura ACF block con InnerBlocks
4. enqueue_assets(): void - Encola CSS y 2 JS (carousel + editor)
5. render_block($block, $content, $is_preview): void - Renderiza bloque
6. register_fields(): void - Define campos ACF extensos
```

**Métodos Privados:**
```
1. get_demo_hero_image(): array - Genera hero image demo
2. get_demo_cards(): array - Genera 6 cards demo
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (NO usa BlockBase)

**Configuración:**
- name: `hero-carousel`
- category: `travel`
- icon: `slides`
- keywords: ['hero', 'carousel', 'cards', 'background', 'slider']
- supports: align=[wide,full], spacing, color, typography, anchor, customClassName
- **example:** Define InnerBlocks demo (heading, paragraph, buttons) - Líneas 56-93
- enqueue_assets: Doble registro (línea 54 y hooks línea 15-16)

**InnerBlocks:**
- Usa InnerBlocks para hero text content
- Example con 3 bloques: core/heading, core/paragraph, core/buttons
- Rendering vía `$content` parameter (línea 126)

**Block.json:** No existe

---

## 5. Campos ACF

**Definición:** [X] PHP inline (ENORMES - 691 líneas de campos)

**Grupo:** `group_hero_carousel`

**Estructura por Tabs:**
1. **General:** layout_variation, columns_desktop, content_proportion
2. **Card Styles:** button_color, badge_color, text_alignment, button_alignment
3. **Hero Content:** hero_image, InnerBlocks note
4. **Dimensions:** cards_height, cards_width, hero_height (mobile/tablet/desktop)
   - Negative margins: top, bottom, left, right (con condicionales por layout)
5. **Carousel:** show_arrows, show_dots, enable_autoplay, autoplay_delay
6. **Dynamic Content:** Desde `ContentQueryHelper` (hc_dynamic_source, filtros)
7. **Cards (Manual):** Repeater con 11 sub-fields

**Campos Complejos:**
- Repeater cards: 11 sub-fields (image, category, badge_color, title, excerpt, date, link, cta_text, location, price)
- Condicionales extensos: Negative margins solo para layouts específicos
- Range fields para dimensions
- Integración con ContentQueryHelper

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render_block()`

**Obtención de Datos:**
1. ACF fields: layout_variation, button_color_variant, hero_image, etc. (líneas 133-170)
2. **Hero image fallback:** Si no hay hero_image → `get_demo_hero_image()` (líneas 148-150)
3. **InnerBlocks content:** Captura `$content` parameter (línea 153-154)
4. **Dynamic content check** (líneas 173-229):
   - `package`: ContentQueryHelper::get_content('hc', 'package')
   - `post`: ContentQueryHelper::get_content('hc', 'post')
   - `deal`: ContentQueryHelper::get_deal_packages($deal_id, 'hc')
   - `none`: Manual cards desde ACF repeater
5. Si no hay cards → `get_demo_cards()` (6 cards demo) (líneas 210-228)
6. Determine carousel activation (líneas 232-233)

**Procesamiento:**
- Rellena imágenes vacías con Picsum demo (líneas 214-226)
- Calcula content_proportion y cards_proportion (líneas 142-143)
- Calcula si se necesita carrusel: `$total_cards > $columns_desktop` (línea 233)
- Prepara array `$template_data` (29 variables!)

**Variables al Template:**
```php
- 29 variables (líneas 240-272)
- Incluye: layout_variation, hero_image, hero_content (InnerBlocks)
- has_hero_text, cards, is_carousel
- Todas las dimensions, margins, carousel settings
- display_fields para packages y posts
```

**Template Loading:**
- Template dinámico según layout_variation (línea 275)
- Usa `extract()` + `include` (líneas 278-279)
- ⚠️ Verifica existencia de template

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:** ✅ SÍ - 2 archivos
- `carousel.js`: Maneja carrusel (frontend + editor)
- `editor.js`: Fix de padding en editor (solo admin) - Líneas 115-123

**InnerBlocks:** ✅ SÍ
- Permite agregar cualquier bloque Gutenberg en hero content
- Renderiza vía `$content` parameter
- Example con heading, paragraph, buttons (líneas 56-93)

**REST API:** No usa directamente

**Hooks Propios:** No define

**Dependencias Externas:**
- ✅ **ContentQueryHelper** (crítico para dynamic content)
- ⚠️ Carousel library (verificar carousel.js)

**Helper Integration:**
- Líneas 436-437: `ContentQueryHelper::get_dynamic_content_fields('hc')`
- Línea 1106: `ContentQueryHelper::get_filter_fields('hc')`
- Líneas 177, 185, 195: `ContentQueryHelper::get_content()` y `get_deal_packages()`

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ❌ **VIOLA**
- Clase hace: registro, renderizado, demo data (2 métodos), field registration, asset enqueueing
- Ubicación: Toda la clase (1126 líneas)
- Impacto: CRÍTICO - Múltiples responsabilidades

**OCP:** ⚠️ Parcial
- Difícil extender sin modificar

**LSP:** ✅ N/A - No hereda

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ❌ **VIOLA**
- Dependencia directa de ContentQueryHelper sin abstracción
- Ubicación: Líneas 177, 185, 195, 436-437, 1106
- Impacto: MEDIO - Acoplamiento directo

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ **render_block(): 158 líneas** (126-283)
  - Ubicación: Línea 126
  - Impacto: CRÍTICO - Método MUY LARGO
- ❌ **register_fields(): 691 líneas** (433-1124)
  - Ubicación: Línea 433
  - Impacto: **CATASTRÓFICO** - Método GIGANTESCO (el peor hasta ahora)
- ⚠️ **get_demo_cards(): 135 líneas** (296-431)
  - Ubicación: Línea 296
  - Impacto: ALTO - Solo datos demo pero muy largo

**Anidación:**
- ⚠️ **Anidación de 3-4 niveles** en render_block()
  - Ubicación: Líneas 173-229 (dynamic source + fallback + foreach)
  - Impacto: MEDIO

**Duplicación:**
- ❌ **Código CASI IDÉNTICO con FlexibleGridCarousel**
  - Ambos usan ContentQueryHelper de forma idéntica
  - Ambos tienen demo data similar
  - Ambos tienen campos de carousel idénticos
  - Ambos tienen campos de styles idénticos
  - Ubicación: Comparar FlexibleGridCarousel.php
  - Impacto: CRÍTICO - Duplicación masiva

**Nombres:**
- ✅ Nombres descriptivos en general
- ⚠️ Prefijo `hc` en campos ACF (abreviación)

**Código Sin Uso:**
- ⚠️ `$display_fields_posts` sin uso aparente (línea 237)
- ⚠️ `travel_info()` debug (líneas 178-182, 186-190, 196-201)
- ⚠️ Editor.js solo para padding fix (puede ser innecesario)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ ContentQueryHelper debe sanitizar datos dinámicos
- ⚠️ Demo data no sanitizada (hardcoded, OK para demo)
- ⚠️ InnerBlocks content sin sanitización extra (confianza en WP)

**Escapado:**
- ⚠️ Templates deben escapar InnerBlocks content
- ⚠️ Verificar escapado en 4 templates diferentes

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas (usa ContentQueryHelper)

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 3
  - Impacto: BAJO (pero inconsistente)

**Separación MVC:**
- ✅ Controller (clase) / View (4 templates) separados
- ❌ Demo data hardcodeado en controller (debería estar en config/JSON)

**Acoplamiento:**
- ❌ **Alto acoplamiento con ContentQueryHelper**
  - No usa inyección de dependencias
  - Llama directamente métodos estáticos
- ⚠️ Doble registro de assets (líneas 15-16 + 54)

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Todos los demás bloques auditados heredan de BlockBase (excepto FlexibleGridCarousel)
  - Implementa todo manualmente
  - Ubicación: Línea 7
  - Impacto: CRÍTICO - Inconsistencia arquitectónica

**InnerBlocks:**
- ✅ Buena implementación de InnerBlocks
- ⚠️ Pero agrega complejidad al bloque

**Otros:**
- ⚠️ 4 templates diferentes (complejidad de mantenimiento)
- ⚠️ Archivo en ubicación inconsistente:
  - Clase: `/src/Blocks/ACF/HeroCarousel.php`
  - Templates: `/src/Blocks/HeroCarousel/templates/` (sin ACF/)
  - Assets: `/assets/blocks/HeroCarousel/` (sin ACF/)

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**BLOQUE EXTREMADAMENTE COMPLEJO - 1126 líneas. Refactorización crítica necesaria.**

### Prioridad Crítica

**1. CRÍTICO: Consolidar con FlexibleGridCarousel**
- **Acción:** Ambos bloques comparten ~70% del código:
  - ContentQueryHelper integration (idéntica)
  - Campos carousel (idénticos)
  - Campos styles (idénticos)
  - Demo data (similar)
  - Lógica de dynamic content (idéntica)
  - **Decisión:** ¿Crear bloque base común o servicio compartido?
- **Razón:** Duplicación MASIVA de código
- **Riesgo:** ALTO - Pero el beneficio es enorme
- **Precauciones:**
  - ⛔ NO cambiar block names
  - ⛔ NO romper templates existentes
  - ✅ Crear servicio compartido para lógica común
- **Esfuerzo:** 8-12h (análisis + implementación + testing)

**2. CRÍTICO: Dividir register_fields() - 691 líneas**
- **Acción:** Extraer a métodos separados:
  ```php
  private function get_general_fields(): array
  private function get_style_fields(): array
  private function get_hero_fields(): array
  private function get_dimensions_fields(): array
  private function get_carousel_fields(): array
  private function get_cards_fields(): array
  ```
- **Razón:** 691 líneas es CATASTRÓFICO (el peor método auditado)
- **Riesgo:** BAJO - Solo organización
- **Precauciones:** Mantener field keys exactos
- **Esfuerzo:** 3h

**3. CRÍTICO: Dividir render_block() - 158 líneas**
- **Acción:** Extraer lógica a métodos:
  ```php
  private function get_hero_data(): array
  private function get_cards_data(): array
  private function determine_carousel_mode($cards, $columns): bool
  private function prepare_template_data(...): array
  private function load_layout_template($variation, $data): void
  ```
- **Razón:** 158 líneas viola KISS, mucha lógica compleja
- **Riesgo:** MEDIO - Lógica compleja (InnerBlocks, dynamic content)
- **Precauciones:**
  - ⛔ Mantener InnerBlocks funcionando
  - ⛔ Mantener output exacto
  - ✅ Testing exhaustivo de 4 layout variations
- **Esfuerzo:** 4h

### Prioridad Alta

**4. Decidir estrategia de herencia con BlockBase**
- **Acción:** ¿Por qué NO hereda de BlockBase?
  - Opción A: Refactorizar para heredar de BlockBase
  - Opción B: Mantener independiente (justificar)
- **Razón:** Inconsistencia con otros bloques
- **Riesgo:** ALTO - Requiere refactorización significativa
- **Precauciones:** Similar a FlexibleGridCarousel
- **Esfuerzo:** 6-8h (si se hereda)

**5. Mover demo data a archivos JSON/config**
- **Acción:** Crear:
  - `/config/demo-data/hero-carousel-hero.json`
  - `/config/demo-data/hero-carousel-cards.json`
- **Razón:** 135 líneas de datos hardcodeados
- **Riesgo:** BAJO
- **Precauciones:** Mantener estructura exacta
- **Esfuerzo:** 1h

### Prioridad Media

**6. Corregir Namespace**
- **Acción:** Cambiar a `Travel\Blocks\ACF`
- **Razón:** PSR-4 y consistencia
- **Riesgo:** MEDIO - Actualizar autoload
- **Precauciones:** Composer dump-autoload
- **Esfuerzo:** 30 min

**7. Verificar necesidad de editor.js**
- **Acción:** ¿Es realmente necesario padding fix?
  - Verificar si es workaround temporal
  - Considerar solución CSS
- **Razón:** Archivo adicional solo para fix
- **Riesgo:** BAJO - Solo investigación
- **Esfuerzo:** 30 min

**8. Eliminar doble registro de assets**
- **Acción:** Líneas 15-16 + línea 54
- **Razón:** Duplicación de lógica
- **Riesgo:** MEDIO - Verificar carga en editor Y frontend
- **Precauciones:** Testing exhaustivo
- **Esfuerzo:** 30 min

**9. Simplificar templates (4 → menos?)**
- **Acción:** Analizar si se pueden consolidar templates
  - ¿bottom y top son muy similares?
  - ¿side_left y side_right solo difieren en dirección?
- **Razón:** Mantenimiento de 4 templates
- **Riesgo:** MEDIO
- **Esfuerzo:** 2h

### Prioridad Baja

**10. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **PRIMERO:** Analizar consolidación con FlexibleGridCarousel (CRÍTICO)
2. **SEGUNDO:** Dividir register_fields() (691 líneas)
3. Dividir render_block() (158 líneas)
4. Mover demo data a JSON
5. Decidir estrategia BlockBase
6. Corregir namespace
7. Eliminar duplicaciones
8. Simplificar templates (si es posible)
9. Crear block.json (opcional)

**Precauciones Generales:**
- ⛔ NO cambiar block name `hero-carousel`
- ⛔ NO cambiar nombres de campos ACF (field_hc_*)
- ⛔ NO romper InnerBlocks
- ⛔ NO romper 4 layout variations
- ⛔ NO romper integración con ContentQueryHelper
- ⛔ NO romper carousel.js y editor.js
- ✅ Testing: 4 layouts, InnerBlocks, contenido manual/dinámico, carrusel
- ✅ Testing: Hero image, cards, negative margins, dimensions
- ✅ Testing: Editor.js padding fix funciona

---

## 11. Checklist Post-Refactorización

### Funcionalidad CRÍTICA
- [ ] Bloque aparece en catálogo
- [ ] 4 layout variations funcionan (bottom, top, side_left, side_right)
- [ ] InnerBlocks funciona en hero content
- [ ] Hero image se muestra correctamente
- [ ] Cards se muestran en grid o carrusel (según cantidad)
- [ ] Contenido manual funciona (cards repeater)
- [ ] Contenido dinámico funciona (packages)
- [ ] Contenido dinámico funciona (posts)
- [ ] Contenido dinámico funciona (deals)
- [ ] Demo data aparece si no hay contenido
- [ ] Negative margins funcionan (top, bottom, left, right)
- [ ] Dimensions funcionan (heights, widths)
- [ ] Content proportion funciona (text/cards split)
- [ ] Carrusel funciona (arrows, dots, autoplay)
- [ ] Estilos funcionan (button/badge colors, alignments)
- [ ] Editor.js padding fix funciona

### Arquitectura
- [ ] Consolidación con FlexibleGridCarousel (si se hizo)
- [ ] Hereda de BlockBase (si se decidió)
- [ ] Namespace correcto (si se cambió)
- [ ] Métodos <30 líneas (si se dividió)
- [ ] Demo data en JSON (si se movió)
- [ ] Sin duplicación de assets (si se corrigió)

### Seguridad
- [ ] Templates escapan InnerBlocks content
- [ ] ContentQueryHelper sanitiza datos dinámicos

### Clean Code
- [ ] Métodos pequeños y enfocados
- [ ] Sin código duplicado masivo
- [ ] Sin código sin uso

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funciona correctamente
- ✅ InnerBlocks bien implementado
- ✅ 4 layout variations (flexible)
- ✅ Integración con ContentQueryHelper
- ❌ **1126 LÍNEAS TOTALES** (archivo gigante)
- ❌ **register_fields(): 691 líneas** (CATASTRÓFICO)
- ❌ **render_block(): 158 líneas** (CRÍTICO)
- ❌ **Duplicación MASIVA con FlexibleGridCarousel**
- ❌ NO hereda de BlockBase (inconsistencia crítica)
- ⚠️ Namespace incorrecto
- ⚠️ 4 templates (complejidad de mantenimiento)

### Puntuación: 4/10

**Fortalezas:**
- Funcionalidad COMPLETA y muy flexible
- InnerBlocks para hero content (moderno)
- 4 layout variations (bottom, top, side_left, side_right)
- Integración con ContentQueryHelper para contenido dinámico
- Negative margins para overlaps creativos
- Demo data completo

**Debilidades:**
- **CRÍTICO:** Archivo de 1126 líneas (el más grande auditado)
- **CRÍTICO:** Método de 691 líneas (el peor hasta ahora)
- **CRÍTICO:** Duplicación masiva con FlexibleGridCarousel (~70% código compartido)
- Arquitectura inconsistente (NO hereda de BlockBase)
- 135 líneas de demo data hardcodeado
- 4 templates separados (complejidad)
- Namespace incorrecto

**Recomendación:** **REFACTORIZACIÓN CRÍTICA URGENTE** - Este es el bloque más complejo y problemático auditado hasta ahora. Requiere consolidación con FlexibleGridCarousel y división de métodos gigantes.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** **CRÍTICA Y URGENTE** - Priorizar consolidación
