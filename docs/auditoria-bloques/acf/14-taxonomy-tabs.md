# Auditoría: TaxonomyTabs (ACF)

**Fecha:** 2025-11-09
**Bloque:** 14/15 ACF
**Tiempo:** 60 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-taxonomy-tabs`
- **Namespace:** `acf/travel-taxonomy-tabs`
- **Campos ACF:** `tt_dynamic_source`, `tt_selected_taxonomies_package`, `tt_selected_terms_*`, etc.
- **JavaScript:** `taxonomy-tabs.js` + `taxonomy-tabs-editor.js` - manejan tabs navigation y mobile slider
- **Clases CSS:** `.taxonomy-tabs`, `.tt-nav__item`, `.is-active`, `.tt-panel`, `.tt-cards-grid` - usadas en JavaScript
- **ACF Filters:** `acf/load_field/*` - cargan opciones dinámicas de taxonomías

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **ContentQueryHelper:** Usa helper para obtener cards dinámicas (packages, posts, deals)
- **Taxonomías completas vs individuales:** Lógica compleja para tabs (líneas 686-854)
- **Mobile slider:** TaxonomyTabsSlider class (158 líneas de JS)
- **Repeater reconstruction:** Método especial para Gutenberg block data (líneas 1278-1311)
- **Tab overrides:** Sistema de personalización de nombres e íconos

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/TaxonomyTabs.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/wp-content/plugins/travel-blocks/templates/taxonomy-tabs.php`

**Assets:**
- CSS: `/assets/blocks/taxonomy-tabs.css` (1105 líneas - INCLUYE Google Fonts ⚠️)
- JS: `/assets/blocks/taxonomy-tabs.js` (398 líneas - tabs + slider)
- JS: `/assets/blocks/taxonomy-tabs-editor.js` (solo admin - filter repeater)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo  [ ] Usa InnerBlocks

---

## 2. Propósito y Funcionalidad

**Descripción:** Organiza contenido dinámico (Packages, Posts, Deals) en tabs navegables por taxonomía. Cada tab muestra cards filtradas. Soporta taxonomías completas o términos individuales. Mobile: Slider con flechas y dots.

**Inputs (ACF):**
- **General:** `tt_dynamic_source` (package/post/deal)
- **Taxonomías Package:** `tt_selected_taxonomies_package` (completas), `tt_selected_terms_package_type`, `tt_selected_terms_interest`, `tt_selected_locations_cpt`
- **Taxonomías Post:** `tt_selected_taxonomies_post`, `tt_selected_terms_category`, `tt_selected_terms_post_tag`
- **Tab Overrides:** Repeater para personalizar nombres e íconos
- **Apariencia:** `tt_tabs_style` (pills/underline/buttons/hero-overlap), `tt_tabs_alignment`, `tt_cards_per_row`, `tt_card_gap`, color variants
- **Slider Mobile:** `tt_card_height`, `tt_show_arrows`, `tt_arrows_position`, `tt_show_dots`, `tt_autoplay`, `tt_slider_speed`
- **Filtros:** Desde ContentQueryHelper (posts_per_page, filters, etc.)

**Outputs:**
- Tabs navegables con contenido dinámico
- Cards en grid (desktop) o slider (mobile)
- 4 estilos de tabs (pills, underline, buttons, hero-overlap)
- Mobile: Slider con 3 posiciones de flechas (sides/overlay/bottom)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: Ninguna (❌ NO hereda de BlockBase)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private $name = 'travel-taxonomy-tabs'
```

**Métodos Públicos:**
```
1. __construct(): void - Define ACF filters para load choices
2. register(): void - Registra bloque, campos y assets
3. register_block(): void - Configura ACF block
4. register_fields(): void - Define campos ACF
5. enqueue_assets(): void - Encola CSS y 2 JS
6. render($block, $content, $is_preview, $post_id): void - Renderiza bloque
7. load_*_choices($field): array - 5 métodos para cargar opciones ACF dinámicas
8. load_selected_terms_for_override($field): array - Carga términos para repeater override
```

**Métodos Privados:**
```
1. get_cards_for_term($term_id, $taxonomy, $source): array - Obtiene cards para término
2. get_cards_for_location_cpt($location_id, $source): array - Obtiene cards para location
3. get_cards_for_taxonomy($taxonomy, $source): array - Obtiene cards para taxonomía completa
4. get_preview_tabs($source, $taxonomy): array - Genera tabs demo
5. get_sample_cards($source, $count): array - Genera cards demo
6. get_taxonomy_choices($taxonomy): array - Obtiene términos de taxonomía
7. get_locations_cpt_choices(): array - Obtiene locations CPT
8. reconstruct_repeater_from_block_data($block_data, $repeater_name, $subfields): array - Reconstruye repeater desde Gutenberg
9. prepare_icon_data($icon_id): array|null - Prepara data de ícono
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (NO usa BlockBase)

**Configuración:**
- name: `travel-taxonomy-tabs`
- category: `travel`
- icon: `tagcloud`
- keywords: ['tabs', 'taxonomy', 'categories', 'packages', 'cards']
- supports: align=[wide,full], spacing, color, anchor, customClassName
- enqueue_assets: Doble registro (línea 49 y hooks líneas 29-30)

**Block.json:** No existe

---

## 5. Campos ACF

**Definición:** [X] PHP inline (517 líneas de campos - MASIVO)

**Grupo:** `group_taxonomy_tabs`

**Estructura por Tabs:**
1. **⚙️ General:** tt_dynamic_source (package/post/deal), preview_mode
2. **🏷️ Taxonomías:**
   - Instructions message
   - Package: taxonomías completas + términos individuales (package_type, interest, locations_cpt)
   - Post: taxonomías completas + términos individuales (category, post_tag)
   - **Repeater:** tt_tab_overrides (term_id, custom_name, icon)
3. **Filtros:** Desde ContentQueryHelper (posts_per_page, active_promo, etc.)
4. **🎨 Apariencia:** tabs_style, tabs_alignment, cards_per_row, card_gap, color variants
5. **⚙️ Slider Mobile:** card_height, show_arrows, arrows_position, show_dots, autoplay, delay, speed

**Campos Complejos:**
- **Repeater tt_tab_overrides:** Permite personalizar nombre e ícono de cada tab
- **ACF Filters:** 5 filtros para cargar opciones dinámicas (líneas 16-23)
- **Conditional logic:** Campos de taxonomía dependen de `tt_dynamic_source`
- **ContentQueryHelper integration:** get_dynamic_content_fields() y get_filter_fields()

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. **Block data handling:** Intenta `$block['data']` primero, fallback a `get_field()` (líneas 582-683)
   - ⚠️ Doble source para soportar Gutenberg editor
2. **Taxonomías selection:**
   - Collect selected taxonomies (completas): líneas 601-634
   - Collect individual terms: líneas 615-646
   - Collect locations CPT: línea 626
3. **Tab overrides:** Reconstruct repeater desde block data (líneas 650-661)
   - Usa método especial `reconstruct_repeater_from_block_data()` (líneas 1278-1311)
4. **Apariencia y slider settings:** líneas 664-683

**Procesamiento:**
1. **Build tabs array:** (líneas 686-854)
   - **Preview mode:** Genera sample data (líneas 688-690)
   - **Real mode:**
     - Process complete taxonomies (líneas 695-765)
       - Special handling para locations_cpt (líneas 697-727)
       - Regular taxonomies (líneas 731-764)
     - Process individual terms (líneas 768-810)
     - Process individual locations (líneas 813-854)
   - Cada tab tiene: id, name, slug, icon (opcional), cards array
2. **Get cards:** Usa métodos privados:
   - `get_cards_for_taxonomy()` para taxonomías completas
   - `get_cards_for_term()` para términos individuales
   - `get_cards_for_location_cpt()` para locations
   - Todos usan ContentQueryHelper para prepare card data

**Variables al Template:**
```php
- block_wrapper_attributes, block_id, align
- tabs (array complejo con id, name, slug, icon, cards)
- tabs_style, tabs_alignment, cards_per_row, card_gap
- button_color_variant, badge_color_variant
- display_fields_packages, display_fields_posts
- is_preview
- card_height, show_arrows, arrows_position, show_dots
- autoplay, autoplay_delay, slider_speed
```

**Template Loading:**
- Template: `/templates/taxonomy-tabs.php`
- Usa `extract()` + `include` (líneas 887-890)

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa directamente

**JavaScript:** ✅ SÍ - 2 archivos CRÍTICOS

**1. taxonomy-tabs.js (398 líneas):**
- **Tab navigation:** Keyboard + click handlers (líneas 34-137)
- **TaxonomyTabsSlider class:** (líneas 158-377)
  - Solo activo en mobile (< 768px)
  - CSS scroll-snap behavior
  - Touch events para swipe
  - Autoplay opcional
  - 3 posiciones de flechas (sides/overlay/bottom)
  - Dots pagination
- **Custom event:** Dispara `taxonomyTabChange` para analytics (líneas 88-95)
- **Global function:** `window.initTaxonomyTabs` para re-init

**2. taxonomy-tabs-editor.js:**
- Solo en admin (línea 564)
- Filtra select de repeater override basado en checkboxes seleccionados

**Dependencias Externas:**
- ✅ **ContentQueryHelper** (CRÍTICO)
  - `get_dynamic_content_fields()` (línea 80)
  - `get_filter_fields()` (línea 81)
  - `prepare_package_card_data()` (líneas 941, 1000, 1386, 1434)
  - `prepare_post_card_data()` (líneas 943, 1002, 1436)
- ⚠️ **Google Fonts en CSS** (líneas 7-8)
  - Saira Condensed
  - Inter

**ACF Filters:**
- 5 filtros para load choices (líneas 16-23):
  - `acf/load_field/name=tt_selected_terms_package_type`
  - `acf/load_field/name=tt_selected_terms_interest`
  - `acf/load_field/name=tt_selected_locations_cpt`
  - `acf/load_field/name=tt_selected_terms_category`
  - `acf/load_field/name=tt_selected_terms_post_tag`
- 1 filtro para repeater override (línea 23)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ❌ **VIOLA**
- Clase hace: registro, renderizado, ACF filters, field registration, asset enqueueing, build tabs, get cards, demo data, repeater reconstruction
- **14 métodos públicos + 9 métodos privados**
- Ubicación: Toda la clase (1444 líneas - ARCHIVO GIGANTE)
- Impacto: CRÍTICO - Múltiples responsabilidades

**OCP:** ⚠️ Parcial
- Difícil extender sin modificar (no hereda de BlockBase)

**LSP:** ✅ N/A - No hereda

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ❌ **VIOLA**
- Dependencia directa de ContentQueryHelper sin abstracción
- Ubicación: Líneas 80-81, 941, 943, 1000, 1002, 1386, 1434, 1436
- Impacto: MEDIO - Acoplamiento directo

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ **render(): 313 líneas** (578-891) - **CATASTRÓFICO**
  - Ubicación: Línea 578
  - Impacto: **CRÍTICO** - Método GIGANTESCO
  - Lógica compleja: Gutenberg data handling, taxonomías, terms, locations, tabs building
- ❌ **register_fields(): 428 líneas** (73-539) - **CATASTRÓFICO**
  - Ubicación: Línea 73
  - Impacto: **CRÍTICO** - Segundo método gigante
- ⚠️ **get_cards_for_taxonomy(): 92 líneas** (1352-1443)
  - Ubicación: Línea 1352
  - Impacto: ALTO - Largo pero solo query

**Anidación:**
- ❌ **Anidación de 4-5 niveles** en render()
  - Ubicación: Líneas 686-854 (build tabs)
  - Impacto: ALTO - Muy difícil de seguir

**Duplicación:**
- ❌ **Código CASI IDÉNTICO con otros bloques**
  - ContentQueryHelper usage (idéntico a HeroCarousel, FlexibleGridCarousel)
  - Tab styles system (pills/underline/buttons)
  - Mobile slider (similar a PostsCarousel)
  - Card rendering (idéntico a otros bloques)
  - Color variants (duplicado de otros bloques)
  - Ubicación: Todo el archivo
  - Impacto: CRÍTICO - Duplicación masiva

**Nombres:**
- ✅ Nombres descriptivos en general
- ⚠️ Prefijo `tt` en campos ACF (abreviación)

**Código Sin Uso:**
- ⚠️ `$display_fields_posts` parece sin uso (línea 859)
- ⚠️ `preview_mode` field pero usa `$is_preview` también (línea 663)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ ContentQueryHelper debe sanitizar datos dinámicos
- ⚠️ Repeater data reconstruction (líneas 1278-1311) - confiar en Gutenberg
- ⚠️ Icon data preparation (líneas 1320-1342) - validar attachment ID

**Escapado:**
- ⚠️ Template debe escapar todos los outputs
- ⚠️ Icon URLs desde attachments (verificar escapado)

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas (usa WP_Query y ContentQueryHelper)

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 3
  - Impacto: BAJO (pero inconsistente)

**Separación MVC:**
- ✅ Controller (clase) / View (template) separados
- ❌ Demo data hardcodeado en controller (líneas 1014-1092)
- ❌ Lógica de negocio compleja en render() (debería estar en servicios)

**Acoplamiento:**
- ❌ **Alto acoplamiento con ContentQueryHelper**
  - No usa inyección de dependencias
  - Llama directamente métodos estáticos
- ⚠️ Doble registro de assets (líneas 29-30 + 49)

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Todos los demás bloques heredan de BlockBase
  - Implementa todo manualmente
  - Ubicación: Línea 7
  - Impacto: CRÍTICO - Inconsistencia arquitectónica

**Otros:**
- ❌ **Archivo GIGANTE: 1444 líneas** (el más grande auditado)
- ⚠️ **Google Fonts en CSS** (líneas 7-8) - Debería estar en theme
- ⚠️ **CSS de 1105 líneas** - Muy largo
- ⚠️ **Método especial para Gutenberg:** reconstruct_repeater_from_block_data() - Workaround complejo

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**BLOQUE EXTREMADAMENTE COMPLEJO - 1444 líneas PHP + 398 líneas JS + 1105 líneas CSS. Refactorización CRÍTICA necesaria.**

### Prioridad Crítica

**1. CRÍTICO: Dividir render() - 313 líneas**
- **Acción:** Extraer lógica a métodos:
  ```php
  private function get_block_settings($block_data): array
  private function collect_selected_taxonomies($block_data, $source): array
  private function collect_tab_overrides($block_data): array
  private function build_tabs_array($taxonomies, $terms, $locations, $overrides, $source): array
  private function process_taxonomy_tab($taxonomy, $source, $overrides): array
  private function process_term_tab($term_id, $source, $overrides): array
  private function process_location_tab($location_id, $source, $overrides): array
  ```
- **Razón:** 313 líneas es CATASTRÓFICO (el peor método auditado)
- **Riesgo:** ALTO - Lógica muy compleja (Gutenberg, taxonomías, terms)
- **Precauciones:**
  - ⛔ NO cambiar output final
  - ⛔ Mantener lógica de taxonomías completas vs individuales
  - ⛔ Mantener lógica de tab overrides
  - ✅ Testing exhaustivo con diferentes combinaciones
- **Esfuerzo:** 6-8h

**2. CRÍTICO: Dividir register_fields() - 428 líneas**
- **Acción:** Extraer a métodos separados:
  ```php
  private function get_general_tab_fields(): array
  private function get_taxonomies_tab_fields(): array
  private function get_filters_tab_fields(): array
  private function get_appearance_tab_fields(): array
  private function get_slider_tab_fields(): array
  ```
- **Razón:** 428 líneas es CATASTRÓFICO
- **Riesgo:** BAJO - Solo organización de campos
- **Precauciones:** Mantener field keys exactos
- **Esfuerzo:** 3-4h

**3. CRÍTICO: Crear servicio TaxonomyTabsBuilder**
- **Acción:** Extraer lógica de build tabs a servicio separado
  ```php
  class TaxonomyTabsBuilder {
    public function buildTabs($config): array
    private function buildTaxonomyTab(...): array
    private function buildTermTab(...): array
    private function buildLocationTab(...): array
  }
  ```
- **Razón:** Separar lógica de negocio de controller
- **Riesgo:** MEDIO - Requiere refactorización significativa
- **Precauciones:**
  - ⛔ Mantener output exacto
  - ✅ Testing exhaustivo
- **Esfuerzo:** 4-6h

### Prioridad Alta

**4. Decidir estrategia de herencia con BlockBase**
- **Acción:** ¿Por qué NO hereda de BlockBase?
  - Opción A: Refactorizar para heredar de BlockBase
  - Opción B: Mantener independiente (justificar)
- **Razón:** Inconsistencia con otros bloques
- **Riesgo:** ALTO - Requiere refactorización significativa
- **Esfuerzo:** 6-8h (si se hereda)

**5. Mover Google Fonts a theme**
- **Acción:** Eliminar @import de CSS y cargar en theme
- **Razón:** CSS no debería cargar fonts (responsabilidad del theme)
- **Riesgo:** BAJO - Solo mover código
- **Precauciones:** Verificar que fonts se carguen en theme
- **Esfuerzo:** 30 min

**6. Consolidar sistema de tabs con otros bloques**
- **Acción:** Similar a HeroCarousel, este bloque comparte mucho código
  - ContentQueryHelper usage (idéntico)
  - Card rendering (idéntico)
  - Color variants (duplicado)
  - Slider mobile (similar a PostsCarousel)
- **Razón:** Duplicación MASIVA de código
- **Riesgo:** ALTO - Pero beneficio enorme
- **Esfuerzo:** 10-15h (análisis + implementación + testing)

### Prioridad Media

**7. Simplificar Gutenberg data handling**
- **Acción:** Crear helper para Gutenberg block data
  ```php
  private function getBlockField($block_data, $field_name, $default = null)
  ```
- **Razón:** Duplicación de lógica `$block_data['field'] ?? get_field('field')`
- **Riesgo:** BAJO
- **Esfuerzo:** 1h

**8. Corregir Namespace**
- **Acción:** Cambiar a `Travel\Blocks\ACF`
- **Razón:** PSR-4 y consistencia
- **Riesgo:** MEDIO - Actualizar autoload
- **Esfuerzo:** 30 min

**9. Eliminar doble registro de assets**
- **Acción:** Líneas 29-30 + línea 49
- **Razón:** Duplicación de lógica
- **Riesgo:** MEDIO - Verificar carga en editor Y frontend
- **Esfuerzo:** 30 min

### Prioridad Baja

**10. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **PRIMERO:** Dividir render() (313 líneas) - CRÍTICO
2. **SEGUNDO:** Dividir register_fields() (428 líneas) - CRÍTICO
3. Crear servicio TaxonomyTabsBuilder
4. Simplificar Gutenberg data handling
5. Decidir estrategia BlockBase
6. Mover Google Fonts a theme
7. Consolidar con otros bloques (análisis global)
8. Corregir namespace
9. Eliminar duplicaciones
10. Crear block.json (opcional)

**Precauciones Generales:**
- ⛔ NO cambiar block name `travel-taxonomy-tabs`
- ⛔ NO cambiar nombres de campos ACF (field_tt_*)
- ⛔ NO romper ACF filters (load_field)
- ⛔ NO romper integración con ContentQueryHelper
- ⛔ NO romper JavaScript (tabs navigation + slider)
- ⛔ NO cambiar clases CSS usadas en JS
- ⛔ NO romper lógica de taxonomías completas vs individuales
- ⛔ NO romper tab overrides system
- ✅ Testing: Todas las taxonomías (package_type, interest, locations, category, post_tag)
- ✅ Testing: Taxonomías completas vs términos individuales
- ✅ Testing: Tab overrides (nombres, íconos)
- ✅ Testing: 4 estilos de tabs (pills, underline, buttons, hero-overlap)
- ✅ Testing: Mobile slider (3 posiciones de flechas, dots, autoplay)
- ✅ Testing: ContentQueryHelper integration (packages, posts, deals)

---

## 11. Checklist Post-Refactorización

### Funcionalidad CRÍTICA
- [ ] Bloque aparece en catálogo
- [ ] Fuente de contenido funciona (package/post/deal)
- [ ] Taxonomías completas funcionan (todas como UN tab)
- [ ] Términos individuales funcionan (cada uno como tab)
- [ ] Locations CPT funcionan
- [ ] Tab overrides funcionan (custom name + icon)
- [ ] ACF filters cargan opciones correctamente
- [ ] Tabs navigation funciona (click + keyboard)
- [ ] Contenido dinámico funciona (ContentQueryHelper)
- [ ] Cards se muestran correctamente
- [ ] 4 estilos de tabs funcionan (pills/underline/buttons/hero-overlap)
- [ ] Desktop: Grid layout funciona
- [ ] Mobile: Slider funciona
- [ ] Mobile: 3 posiciones de flechas funcionan (sides/overlay/bottom)
- [ ] Mobile: Dots pagination funciona
- [ ] Mobile: Autoplay funciona
- [ ] Mobile: Touch swipe funciona
- [ ] Color variants funcionan (buttons, badges)
- [ ] Filtros funcionan (posts_per_page, active_promo)
- [ ] Preview mode funciona
- [ ] Gutenberg editor funciona (block data reconstruction)

### Arquitectura
- [ ] Métodos <50 líneas (si se dividió)
- [ ] Hereda de BlockBase (si se decidió)
- [ ] Namespace correcto (si se cambió)
- [ ] Sin duplicación masiva (si se consolidó)
- [ ] Google Fonts en theme (si se movió)

### Seguridad
- [ ] Template escapa outputs correctamente
- [ ] Icon data validada correctamente

### Clean Code
- [ ] Métodos pequeños y enfocados
- [ ] Sin código duplicado masivo
- [ ] Sin anidación excesiva

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funciona correctamente
- ✅ Feature-rich (taxonomías completas + individuales)
- ✅ JavaScript profesional (tabs + slider)
- ✅ Mobile slider bien implementado
- ❌ **1444 LÍNEAS PHP** (archivo más grande auditado)
- ❌ **render(): 313 líneas** (método más grande auditado)
- ❌ **register_fields(): 428 líneas** (CATASTRÓFICO)
- ❌ NO hereda de BlockBase (inconsistencia crítica)
- ❌ Duplicación masiva con otros bloques
- ⚠️ Google Fonts en CSS (debería estar en theme)
- ⚠️ Namespace incorrecto

### Puntuación: 4/10

**Fortalezas:**
- Funcionalidad COMPLETA y muy potente
- Sistema de taxonomías completas vs individuales (único)
- Tab overrides (personalización de nombres e íconos)
- 4 estilos de tabs bien implementados
- Mobile slider profesional (3 posiciones de flechas)
- JavaScript robusto (tabs navigation + slider)
- Integración con ContentQueryHelper
- ACF filters para load choices dinámicas
- Gutenberg data reconstruction (workaround necesario)

**Debilidades:**
- **CRÍTICO:** Archivo de 1444 líneas (el más grande auditado)
- **CRÍTICO:** Método render() de 313 líneas (el peor hasta ahora)
- **CRÍTICO:** Método register_fields() de 428 líneas (CATASTRÓFICO)
- **CRÍTICO:** NO hereda de BlockBase (inconsistencia)
- **CRÍTICO:** Duplicación masiva con otros bloques
- Google Fonts en CSS (responsabilidad del theme)
- Namespace incorrecto
- Anidación excesiva (4-5 niveles)
- Demo data hardcodeado (78 líneas)

**Recomendación:** **REFACTORIZACIÓN CRÍTICA URGENTE** - Este es el bloque más complejo y problemático auditado. Requiere división de métodos gigantes, consolidación con otros bloques y decisión sobre BlockBase.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** **CRÍTICA Y URGENTE** - Priorizar división de métodos
