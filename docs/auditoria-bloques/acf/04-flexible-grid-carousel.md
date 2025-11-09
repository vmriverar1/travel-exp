# Auditoría: FlexibleGridCarousel (ACF)

**Fecha:** 2025-11-09
**Bloque:** 4/15 ACF
**Tiempo:** 45 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `flexible-grid-carousel`
- **Namespace:** `acf/flexible-grid-carousel`
- **Campos ACF:** `items` (flexible_content), `columns_desktop`, `text_position_mobile`, `show_arrows`, `show_dots`, etc.
- **Sub-layouts:** `card`, `text_block`
- **JavaScript:** `carousel.js` - maneja carrusel y selectores CSS
- **Clases CSS:** `.flexible-grid-carousel-wrapper` - usada en JavaScript

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Usa `ContentQueryHelper` para contenido dinámico (packages, posts, deals)
- Método `get_demo_items()` - puede ser referenciado externamente
- Dependencia de Swiper.js/carousel library (verificar carousel.js)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/FlexibleGridCarousel.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/wp-content/plugins/travel-blocks/src/Blocks/FlexibleGridCarousel/templates/flexible-grid.php`
**Assets:**
- CSS: `/assets/blocks/FlexibleGridCarousel/style.css`
- JS: `/assets/blocks/FlexibleGridCarousel/carousel.js` (CRÍTICO)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

---

## 2. Propósito y Funcionalidad

**Descripción:** Grid flexible que combina cards y bloques de texto WYSIWYG. En desktop muestra grid, en mobile muestra cards en carrusel y texto separado.

**Inputs (ACF):**
- **General:** `columns_desktop` (2/3/4), `text_position_mobile` (above/below)
- **Styles:** `button_color_variant`, `badge_color_variant`, `text_alignment`, `button_alignment`
- **Carousel:** `show_arrows`, `show_dots`, `enable_autoplay`, `autoplay_delay`
- **Dynamic Content:** `fgc_dynamic_source` (none/package/post/deal)
- **Items:** `items` (flexible_content) con layouts:
  - `card`: image, category, badge_color, title, description, location, price, link, cta_text
  - `text_block`: content (WYSIWYG)

**Outputs:**
- Grid responsivo en desktop
- Carrusel en mobile
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
3. register_block(): void - Configura ACF block
4. enqueue_assets(): void - Encola CSS y JS
5. render_block($block, $content, $is_preview): void - Renderiza bloque
6. register_fields(): void - Define campos ACF
```

**Métodos Privados:**
```
1. get_demo_items(): array - Genera datos demo (7 items: cards + text blocks)
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (NO usa BlockBase)

**Configuración:**
- name: `flexible-grid-carousel`
- category: `travel`
- icon: `grid-view`
- keywords: ['flexible', 'grid', 'carousel', 'cards', 'text', 'wysiwyg']
- supports: align=[wide,full], spacing, color, typography, anchor, customClassName
- enqueue_assets: Doble registro (línea 54 y hooks línea 15-16)

**Block.json:** No existe

---

## 5. Campos ACF

**Definición:** [X] PHP inline (extensos - 698 líneas de campos)

**Grupo:** `group_flexible_grid_carousel`

**Estructura por Tabs:**
1. **General:** columns_desktop, text_position_mobile
2. **Card Styles:** button_color, badge_color, text_alignment, button_alignment
3. **Carousel:** show_arrows, show_dots, enable_autoplay, autoplay_delay
4. **Dynamic Content:** Desde `ContentQueryHelper` (fgc_dynamic_source, filtros)
5. **Items (Manual):** flexible_content con 2 layouts

**Campos Complejos:**
- Flexible Content con 2 layouts (card: 11 sub-fields, text_block: 1 sub-field)
- Condicionales: Items tab solo visible si dynamic_source = 'none'
- Integración con ContentQueryHelper para contenido dinámico

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render_block()`

**Obtención de Datos:**
1. ACF fields: columns_desktop, text_position_mobile, show_arrows, etc.
2. **Dynamic content check** (líneas 97-130):
   - `package`: ContentQueryHelper::get_content('fgc', 'package')
   - `post`: ContentQueryHelper::get_content('fgc', 'post')
   - `deal`: ContentQueryHelper::get_deal_packages($deal_id, 'fgc')
   - `none`: Manual items desde ACF repeater
3. Si no hay items → `get_demo_items()` (7 items demo)
4. Separación de cards y text_blocks (líneas 156-166)

**Procesamiento:**
- Rellena imágenes vacías con Picsum demo (líneas 139-151)
- Separa cards de text_blocks con `original_index`
- Prepara array `$template_data` (22 variables)

**Variables al Template:**
```php
- $block_wrapper_attributes
- $items, $cards, $text_blocks
- $columns_desktop, $text_position_mobile
- $button_color_variant, $badge_color_variant
- $text_alignment, $button_alignment
- $show_arrows, $show_dots, $enable_autoplay, $autoplay_delay
- $display_fields_packages, $display_fields_posts
- $is_preview
```

**Template Loading:**
- Usa `extract()` + `include` (líneas 197-198)
- ⚠️ Verifica existencia de template

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:** ✅ SÍ - `carousel.js`
- Maneja carrusel en mobile
- Usa clases CSS como selectores

**REST API:** No usa directamente

**Hooks Propios:** No define

**Dependencias Externas:**
- ✅ **ContentQueryHelper** (crítico para dynamic content)
- ⚠️ Carousel library (verificar carousel.js)

**Helper Integration:**
- Líneas 363-364: `ContentQueryHelper::get_dynamic_content_fields('fgc')`
- Líneas 364: `ContentQueryHelper::get_filter_fields('fgc')`
- Líneas 102, 110, 120: `ContentQueryHelper::get_content()` y `get_deal_packages()`

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ❌ **VIOLA**
- Clase hace: registro, renderizado, demo data, field registration
- Ubicación: Toda la clase (720 líneas)
- Impacto: ALTO - Múltiples responsabilidades

**OCP:** ⚠️ Parcial
- Difícil extender sin modificar

**LSP:** ✅ N/A - No hereda

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ❌ **VIOLA**
- Dependencia directa de ContentQueryHelper sin abstracción
- Ubicación: Líneas 102, 110, 120, 363-364
- Impacto: MEDIO - Acoplamiento directo

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ **render_block(): 127 líneas** (76-202)
  - Ubicación: Línea 76
  - Impacto: CRÍTICO - Método ENORME
- ❌ **register_fields(): 363 líneas** (357-719)
  - Ubicación: Línea 357
  - Impacto: CRÍTICO - Método GIGANTE
- ❌ **get_demo_items(): 150 líneas** (204-355)
  - Ubicación: Línea 204
  - Impacto: ALTO - Solo datos demo pero muy largo

**Anidación:**
- ⚠️ **Anidación de 3-4 niveles** en render_block()
  - Ubicación: Líneas 97-153 (dynamic source + fallback)
  - Impacto: MEDIO

**Duplicación:**
- ⚠️ **Código similar con HeroCarousel**
  - Ambos usan ContentQueryHelper de forma similar
  - Ambos tienen demo data
  - Ambos tienen campos de carousel similares
  - Impacto: MEDIO

**Nombres:**
- ✅ Nombres descriptivos en general
- ⚠️ Prefijo `fgc` en campos ACF (abreviación)

**Código Sin Uso:**
- ⚠️ `$display_fields_posts` duplicado (líneas 169-170)
- ⚠️ `travel_info()` debug (líneas 104-106, 111-115, 121-126)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ ContentQueryHelper debe sanitizar datos dinámicos
- ⚠️ Demo data no sanitizada (hardcoded, OK para demo)

**Escapado:**
- ⚠️ Template debe escapar WYSIWYG content (text_block)
- ⚠️ Verificar escapado en template `flexible-grid.php`

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
- ✅ Controller (clase) / View (template) separados
- ❌ Demo data hardcodeado en controller (debería estar en config/JSON)

**Acoplamiento:**
- ❌ **Alto acoplamiento con ContentQueryHelper**
  - No usa inyección de dependencias
  - Llama directamente métodos estáticos
- ⚠️ Doble registro de assets (líneas 15-16 + 54)

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Todos los demás bloques heredan de BlockBase
  - Implementa todo manualmente
  - Ubicación: Línea 7
  - Impacto: ALTO - Inconsistencia arquitectónica

**Otros:**
- ⚠️ Archivo en ubicación inconsistente:
  - Clase: `/src/Blocks/ACF/FlexibleGridCarousel.php`
  - Template: `/src/Blocks/FlexibleGridCarousel/templates/` (sin ACF/)
  - Assets: `/assets/blocks/FlexibleGridCarousel/` (sin ACF/)

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque tiene dependencias externas (ContentQueryHelper, carousel.js). NO romper integración.**

### Prioridad Alta

**1. CRÍTICO: Decidir estrategia de herencia**
- **Acción:** ¿Por qué NO hereda de BlockBase? Usuario debe decidir:
  - Opción A: Refactorizar para heredar de BlockBase (como otros bloques)
  - Opción B: Mantener independiente (justificar razón)
- **Razón:** Todos los demás bloques heredan de BlockBase
- **Riesgo:** ALTO - Requiere refactorización significativa
- **Precauciones:**
  - ⛔ NO cambiar block name
  - ⛔ NO cambiar ACF field names
  - ✅ Migrar lógica a métodos de BlockBase
- **Esfuerzo:** 6-8h (si se hereda de BlockBase)

**2. Dividir register_fields() - Método gigante**
- **Acción:** Extraer a métodos separados:
  ```php
  private function get_general_fields(): array
  private function get_style_fields(): array
  private function get_carousel_fields(): array
  private function get_item_fields(): array
  ```
- **Razón:** 363 líneas es CRÍTICO
- **Riesgo:** BAJO - Solo organización
- **Precauciones:** Mantener field keys exactos
- **Esfuerzo:** 2h

**3. Dividir render_block() - Método muy largo**
- **Acción:** Extraer lógica a métodos:
  ```php
  private function get_items_data(): array
  private function process_items($items): array
  private function prepare_template_data($cards, $text_blocks, ...): array
  ```
- **Razón:** 127 líneas viola KISS
- **Riesgo:** MEDIO - Lógica compleja
- **Precauciones:**
  - ⛔ Mantener output exacto
  - ✅ Testing exhaustivo de contenido dinámico
- **Esfuerzo:** 3h

**4. Mover demo data a archivo JSON/config**
- **Acción:** Crear `/config/demo-data/flexible-grid-carousel.json`
- **Razón:** 150 líneas de datos hardcodeados
- **Riesgo:** BAJO
- **Precauciones:** Mantener estructura exacta
- **Esfuerzo:** 1h

### Prioridad Media

**5. Corregir Namespace**
- **Acción:** Cambiar a `Travel\Blocks\ACF`
- **Razón:** PSR-4 y consistencia
- **Riesgo:** MEDIO - Actualizar autoload
- **Precauciones:** Composer dump-autoload
- **Esfuerzo:** 30 min

**6. Eliminar duplicación de display_fields**
- **Acción:** Líneas 169-170 están duplicadas
- **Razón:** Código duplicado innecesario
- **Riesgo:** BAJO
- **Esfuerzo:** 5 min

**7. Verificar y limpiar doble registro de assets**
- **Acción:** Líneas 15-16 registran hooks + línea 54 en config
  - Verificar si es necesario doble registro
  - Mantener solo uno
- **Razón:** Duplicación de lógica
- **Riesgo:** MEDIO - Puede afectar carga de assets
- **Precauciones:**
  - ⚠️ Verificar que assets se cargan en editor Y frontend
  - ✅ Testing en ambos contextos
- **Esfuerzo:** 30 min

**8. Consolidar con HeroCarousel (verificar duplicación)**
- **Acción:** Analizar si se puede compartir código:
  - Campos de carousel similares
  - Lógica de ContentQueryHelper similar
  - Demo data similar
- **Razón:** Evitar duplicación funcional
- **Riesgo:** MEDIO - Requiere análisis profundo
- **Esfuerzo:** 4h (análisis + implementación)

### Prioridad Baja

**9. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**10. Mejorar prefijos en campos ACF**
- **Acción:** `fgc` → más descriptivo o documentar
- **Razón:** Claridad
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min (solo documentación)

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **PRIMERO:** Usuario decide estrategia de herencia (BlockBase sí/no)
2. Dividir register_fields() en métodos
3. Dividir render_block() en métodos
4. Mover demo data a JSON
5. Corregir namespace
6. Eliminar duplicaciones
7. Verificar doble registro assets
8. Analizar consolidación con HeroCarousel
9. Crear block.json (opcional)

**Precauciones Generales:**
- ⛔ NO cambiar block name `flexible-grid-carousel`
- ⛔ NO cambiar nombres de campos ACF (field_fgc_*)
- ⛔ NO cambiar clases CSS en template
- ⛔ NO romper integración con ContentQueryHelper
- ⛔ NO romper carousel.js (verificar selectores CSS)
- ✅ Testing: Contenido manual, dinámico (packages/posts/deals), carrusel mobile
- ✅ Testing: Demo data si no hay items
- ✅ Testing: Separación cards/text_blocks en mobile

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Campos ACF aparecen en todas las tabs
- [ ] Preview funciona en editor
- [ ] Frontend funciona en desktop (grid)
- [ ] Frontend funciona en mobile (carrusel)
- [ ] Contenido manual funciona (items repeater)
- [ ] Contenido dinámico funciona (packages)
- [ ] Contenido dinámico funciona (posts)
- [ ] Contenido dinámico funciona (deals)
- [ ] Demo data aparece si no hay items
- [ ] Cards y text_blocks se separan correctamente
- [ ] Text_position_mobile funciona (above/below)
- [ ] Carrusel funciona (arrows, dots, autoplay)
- [ ] Estilos funcionan (button/badge colors, alignments)

### Arquitectura
- [ ] Hereda de BlockBase (si se decidió)
- [ ] Namespace correcto (si se cambió)
- [ ] Métodos <30 líneas (si se dividió)
- [ ] Demo data en JSON (si se movió)
- [ ] Sin duplicación de assets (si se corrigió)

### Seguridad
- [ ] Template escapa WYSIWYG content
- [ ] ContentQueryHelper sanitiza datos dinámicos

### Clean Code
- [ ] Métodos pequeños y enfocados
- [ ] Sin código duplicado
- [ ] Sin código sin uso

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funciona correctamente
- ✅ Integración con ContentQueryHelper (dynamic content)
- ✅ Grid/Carrusel responsivo
- ❌ NO hereda de BlockBase (inconsistencia crítica)
- ❌ Métodos ENORMES (363 y 127 líneas)
- ❌ 150 líneas de demo data hardcodeado
- ⚠️ Namespace incorrecto
- ⚠️ Doble registro de assets

### Puntuación: 5.5/10

**Fortalezas:**
- Funcionalidad completa y flexible (cards + text blocks)
- Integración con ContentQueryHelper para contenido dinámico
- Soporte para múltiples fuentes (manual, packages, posts, deals)
- Responsivo (grid desktop, carrusel mobile)

**Debilidades:**
- Arquitectura inconsistente (NO hereda de BlockBase)
- Métodos gigantes (CRÍTICO: 363 líneas, 127 líneas)
- Demo data hardcodeado (150 líneas)
- Duplicación de código con HeroCarousel
- Namespace incorrecto

**Recomendación:** REFACTORIZACIÓN CRÍTICA NECESARIA - Decidir estrategia de herencia y dividir métodos gigantes.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** CRÍTICA - Requiere decisión arquitectónica
