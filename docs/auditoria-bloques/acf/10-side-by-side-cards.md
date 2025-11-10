# Auditoría: SideBySideCards (ACF)

**Fecha:** 2025-11-09
**Bloque:** 10/15 ACF
**Tiempo:** 35 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `side-by-side-cards`
- **Namespace ACF:** `acf/side-by-side-cards`
- **Campos ACF:** `cards`, `show_favorite`, `sbs_dynamic_source`, `column_span_pattern`, `grid_columns`, `card_gap`, `hover_effect`, `image_position`, `image_width`, `image_border_radius`, `text_alignment`, `button_alignment`, `button_color_variant`, `badge_color_variant`, `show_arrows`, `show_dots`, `autoplay`, `autoplay_delay`
- **Template path:** `/templates/side-by-side-cards.php`
- **Sub-fields en repeater:** `column_span`, `image`, `title`, `excerpt`, `link`, `category`, `badge_color_variant`, `cta_text`, `location`, `price`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **SÍ hereda de BlockBase** ✅ (como PostsCarousel, HeroSection, ContactForm)
- **Usa ContentQueryHelper** ✅ (contenido dinámico de packages/posts/deals)
- Prefijo `sbs_` para dynamic content fields (no `field_sbs_` en algunos)
- Prefijo `field_sbs_` para campos manuales y de configuración
- Template muy robusto (215 líneas con fallbacks)
- Slider mobile nativo (JavaScript incluido)
- Grid desktop con hover effects avanzados

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/SideBySideCards.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/templates/side-by-side-cards.php`
**Assets:**
- CSS: `/assets/blocks/side-by-side-cards.css`
- JS: `/assets/blocks/side-by-side-cards.js` (slider mobile)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

**Dependencias:**
- ✅ BlockBase (extiende correctamente)
- ✅ ContentQueryHelper (para contenido dinámico)
- JavaScript vanilla (slider nativo mobile)

---

## 2. Propósito y Funcionalidad

**Descripción:** Cards horizontales con imagen y texto lado a lado. Desktop: Grid flexible. Mobile: Slider nativo. Soporte para contenido manual y dinámico (packages, posts, deals).

**Diferencia con otros bloques:**
- PostsCarousel: Cards verticales, imagen arriba
- FlexibleGridCarousel: Grid con column_span, sin slider mobile
- SideBySideCards: **Cards HORIZONTALES**, imagen a un lado, texto al otro

**Inputs (ACF):**

**Tab: Contenido**
- `show_favorite` (true_false, default: 1) - Botón de favoritos
- ContentQueryHelper fields: `dynamic_source` (package/post/deal/none), filtros, taxonomías
- `column_span_pattern` (text) - Patrón de ancho para dinámico (ej: "1,2,1,1")
- `cards` (repeater, manual, 1-12):
  - `column_span` (range 1-4, default: 1) - Espacios del grid
  - `image` (image, opcional)
  - `title` (text, required, max: 100)
  - `excerpt` (textarea, max: 200)
  - `link` (url)
  - `category` (text, max: 30) - Badge
  - `badge_color_variant` (select) - Color individual o global
  - `cta_text` (text, max: 30, default: "Ver más")
  - `location` (text, max: 50)
  - `price` (text, max: 20)

**Tab: Slider (Mobile)**
- `show_arrows` (true_false, default: 1)
- `show_dots` (true_false, default: 1)
- `autoplay` (true_false, default: 0)
- `autoplay_delay` (range 2-10s, default: 5s)

**Tab: Grid (Desktop)**
- `grid_columns` (range 2-8, default: 3)
- `card_gap` (range 0-64px, step 4, default: 32px)
- `hover_effect` (select: squeeze/lift/glow/zoom/none, default: squeeze)

**Tab: Estilos**
- `image_position` (select: left/right, default: left)
- `image_width` (range 30-60%, step 5, default: 40%)
- `image_border_radius` (range 0-40px, step 2, default: 12px)
- `text_alignment` (select: left/center/right, default: left)
- `button_alignment` (select: left/center/right, default: left)
- `button_color_variant` (select: primary/secondary/white/gold/dark/transparent/read-more, default: primary)
- `badge_color_variant` (select: primary/secondary/white/gold/dark/transparent, default: secondary)

**Outputs:**
- Desktop: Grid CSS con grid-template-columns y column-span
- Mobile: Slider nativo con scroll-snap
- Cards horizontales (imagen + texto lado a lado)
- Imagen con bordes redondeados (sin overlay)
- Badge de categoría
- Botón CTA
- Ubicación y precio
- Botón de favoritos (opcional)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ✅ **BlockBase** (correcto)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades (heredadas de BlockBase):**
```
protected string $name = 'side-by-side-cards';
protected string $title = 'Side by Side Cards (Horizontal)';
protected string $description = 'Cards horizontales: imagen + texto lado a lado...';
protected string $category = 'travel';
protected string $icon = 'align-pull-left';
protected array $keywords = ['cards', 'horizontal', 'side', 'slider', 'grid', 'image'];
protected string $mode = 'preview';
protected array $supports = [...];
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor con configuración (36 líneas)
2. enqueue_assets(): void - Encola CSS y JS (18 líneas)
3. register(): void - Registra bloque y campos ACF (221 líneas)
4. render($block, $content, $is_preview, $post_id): void - Renderiza bloque (121 líneas)
```

**Métodos Privados:**
```
5. get_placeholder_image(): string - URL placeholder (4 líneas)
6. get_demo_cards(): array - Cards de demostración (35 líneas)
```

**Total:** 665 líneas (muy completo)

---

## 4. Registro del Bloque

**Método:** `parent::register()` + `acf_add_local_field_group` (hereda de BlockBase)

**Configuración:**
- name: `side-by-side-cards`
- title: "Side by Side Cards (Horizontal)"
- category: `travel`
- icon: `align-pull-left`
- keywords: ['cards', 'horizontal', 'side', 'slider', 'grid', 'image']
- render_callback: Heredado de BlockBase
- supports: align (wide/full), mode, multiple, anchor

**Enqueue Assets:**
- CSS: `/assets/blocks/side-by-side-cards.css` (siempre)
- JS: `/assets/blocks/side-by-side-cards.js` (siempre, slider mobile)
- Método: `enqueue_assets()` separado ✅

**Block.json:** No existe (usa registro ACF)

**Campos ACF:** ✅ Registrados en `register()` con 4 tabs organizados

---

## 5. Campos ACF

**Definición:** ✅ `acf_add_local_field_group` en método `register()`

**Estructura:**
- **Tab: Contenido** (📝)
  - `show_favorite` - Toggle favoritos
  - ContentQueryHelper fields - Dynamic content (spread operator)
  - `column_span_pattern` - Patrón ancho dinámico
  - `cards` repeater - Cards manuales (10 sub-fields)
  - ContentQueryHelper filter fields (spread operator)

- **Tab: Slider (Mobile)** (⚙️)
  - `show_arrows`, `show_dots`, `autoplay`, `autoplay_delay`

- **Tab: Grid (Desktop)** (🖥️)
  - `grid_columns`, `card_gap`, `hover_effect`

- **Tab: Estilos** (🎨)
  - `image_position`, `image_width`, `image_border_radius`
  - `text_alignment`, `button_alignment`
  - `button_color_variant`, `badge_color_variant`

**Conditional Logic:**
- `column_span_pattern` solo si `dynamic_source != 'none'`
- `cards` repeater solo si `dynamic_source == 'none'`
- `autoplay_delay` solo si `autoplay == true`

**Validación:**
- `title` required
- Max lengths: title (100), excerpt (200), category (30), cta_text (30), location (50), price (20)
- Ranges: column_span (1-4), grid_columns (2-8), card_gap (0-64), autoplay_delay (2-10)

**Prefijos:**
- ⚠️ **INCONSISTENCIA:** `field_sbs_` para config fields pero `sbs_dynamic_source` (sin `field_`) para ContentQueryHelper
- Impacto: BAJO - Funciona pero no es consistente

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Detecta `dynamic_source` (línea 504)
2. Si dinámico:
   - `package`: `ContentQueryHelper::get_content('sbs', 'package')`
   - `post`: `ContentQueryHelper::get_content('sbs', 'post')`
   - `deal`: `ContentQueryHelper::get_deal_packages($deal_id, 'sbs')`
3. Si manual: `get_field('cards')`
4. Si vacío: `get_demo_cards()` (solo en preview)
5. Aplica `column_span_pattern` a dinámicos (líneas 528-554)
6. Get configuración de ACF fields (líneas 557-580)
7. Prepara $data array (líneas 587-608)
8. Llama `load_template('side-by-side-cards', $data)` ✅

**Variables al Template:**
- ✅ Pasa $data array explícitamente (no usa $GLOBALS)
- ✅ Template extrae variables con extract()
- ✅ Separación clara entre lógica y presentación

**Manejo de Errores:**
- ✅ Try-catch completo (líneas 502-620)
- ✅ Muestra mensaje de error en WP_DEBUG
- ✅ Template tiene fallback si no hay cards (líneas 16-23)
- ✅ Placeholder images con random para demo

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:**
- ✅ Sí usa
- Implementación: Slider nativo mobile (scroll-snap)
- Funcionalidad: Navegación, autoplay, dots, arrows
- Enqueue: Siempre (líneas 52-59)
- Archivo: `/assets/blocks/side-by-side-cards.js`

**REST API:** No usa

**Hooks Propios:**
- Ninguno (usa hooks de BlockBase)

**Dependencias Externas:**
- ❌ Ninguna
- ✅ Todo local (CSS, JS)
- ✅ ContentQueryHelper (interno)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ **CUMPLE**
- Clase: Configuración y registro
- Template: Presentación
- ContentQueryHelper: Queries
- JavaScript: Interacción
- Separación clara
- Impacto: N/A

**OCP:** ✅ **CUMPLE**
- Hereda de BlockBase (extensible)
- Usa ContentQueryHelper (pluggable)
- Configuración por ACF fields

**LSP:** ✅ **CUMPLE**
- Hereda correctamente de BlockBase
- Implementa métodos esperados

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ✅ **CUMPLE**
- Depende de abstracción (BlockBase, ContentQueryHelper)
- No depende de implementaciones concretas

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ __construct(): 36 líneas (bien)
- ✅ enqueue_assets(): 18 líneas (excelente)
- ⚠️ register(): 221 líneas (largo pero es fields ACF)
- ✅ render(): 121 líneas (aceptable con lógica dinámica)
- ✅ get_demo_cards(): 35 líneas (bien)
- **Total:** 665 líneas (muy completo)

**Anidación:**
- ✅ <3 niveles en todos los métodos
- ✅ Template bien estructurado

**Duplicación:**
- ✅ **NO hay duplicación significativa**
- Funcionalidad única: Cards HORIZONTALES
- Diferente de PostsCarousel (vertical) y FlexibleGridCarousel (sin slider mobile)

**Nombres:**
- ✅ Block name claro: `side-by-side-cards`
- ✅ Campos ACF descriptivos
- ⚠️ Prefijo `sbs_` inconsistente (algunos con `field_`, otros sin)

**Código Sin Uso:**
- ✅ No detectado
- ✅ get_placeholder_image() usado en get_demo_cards()
- ✅ get_demo_cards() usado en render()

**DocBlocks:**
- ✅ Header class completo (líneas 2-12)
- ✅ Template bien documentado (líneas 2-10)
- ✅ Métodos tienen DocBlocks
- ⚠️ DocBlocks de métodos podrían ser más detallados
- Impacto: BAJO - 4/6 métodos bien documentados

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ `get_field()` con validación
- ✅ ContentQueryHelper sanitiza queries
- ✅ Valores con defaults y fallbacks

**Escapado:**
- ✅ Template usa `esc_url()` (líneas 123, 126, 145, 187)
- ✅ Template usa `esc_html()` (líneas 138, 145, 151, 166, 175, 181, 188)
- ✅ Template usa `esc_attr()` (líneas 55, 59-63, 114, 120, 127, 137, 187, 206)
- ✅ `wp_kses_post()` para HTML permitido (líneas 166, 181)
- **Excelente escapado en template**

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ Usa ContentQueryHelper (internamente usa WP_Query sanitizado)
- ✅ No hace queries directas

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 14
  - Impacto: BAJO (funciona pero no sigue convención exacta)

**Separación MVC:**
- ✅ **EXCELENTE SEPARACIÓN MVC**
  - Modelo: ContentQueryHelper + ACF fields
  - Vista: Template PHP
  - Controlador: render() en clase
  - Template NO hace queries ✅
  - Impacto: N/A

**Acoplamiento:**
- ✅ **Acoplamiento BAJO**
  - Usa BlockBase (abstracción)
  - Usa ContentQueryHelper (reutilizable)
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
- ⚠️ **Prefijos inconsistentes** (algunos campos ACF `field_sbs_`, ContentQueryHelper sin `field_`)
  - Impacto: BAJO - Funciona, solo inconsistente
- ✅ Pasa datos por $data (no usa $GLOBALS)
- ✅ Registra campos ACF correctamente
- ✅ Template muy robusto con fallbacks

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

**2. Consistencia en prefijos de campos ACF**
- **Acción:** Decidir si ContentQueryHelper fields llevan `field_` o no
- **Razón:** Consistencia con otros bloques
- **Riesgo:** CRÍTICO - Cambiar nombres rompe contenido
- **Precauciones:** ⛔ SOLO hacer si es nuevo bloque sin contenido
- **Esfuerzo:** 1 hora
- **Recomendación:** ❌ **NO HACER** - Dejar como está

### Prioridad Baja

**3. Mejorar DocBlocks de métodos**
- **Acción:** Agregar @param y @return más detallados
- **Razón:** Mejor documentación para devs
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**4. Extraer lógica de column_span_pattern a método privado**
- **Acción:** Crear `apply_column_span_pattern(array $cards, string $pattern): array`
- **Razón:** Simplificar render(), mejorar testabilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min
- **Código:**
  ```php
  private function apply_column_span_pattern(array $cards, string $pattern): array
  {
      if (empty($pattern)) {
          foreach ($cards as &$card) {
              if (!isset($card['column_span'])) {
                  $card['column_span'] = 1;
              }
          }
          return $cards;
      }

      $pattern_array = array_map('intval', array_map('trim', explode(',', $pattern)));
      $pattern_array = array_filter($pattern_array, fn($val) => $val > 0);

      if (empty($pattern_array)) {
          return $cards;
      }

      foreach ($cards as $index => &$card) {
          $pattern_index = $index % count($pattern_array);
          $card['column_span'] = array_values($pattern_array)[$pattern_index];
      }

      return $cards;
  }
  ```

**5. Agregar validación de imagen en template**
- **Acción:** Validar que `$image_url` sea URL válida antes de mostrar
- **Razón:** Prevenir errores si placeholder falla
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min

---

## 10. Plan de Acción

**Decisión Principal:** ✅ **MANTENER** - Bloque muy bien implementado

### Optimizaciones Opcionales (si hay tiempo)
1. Corregir namespace (30 min)
2. Mejorar DocBlocks (20 min)
3. Extraer lógica de column_span_pattern (30 min)
4. Agregar validación de imagen (10 min)

**Total:** 1.5 horas de optimizaciones opcionales

**Precauciones Generales:**
- ⛔ NO cambiar nombres de campos ACF (rompe contenido existente)
- ⛔ NO cambiar block name (rompe contenido existente)
- ✅ Template es robusto, no necesita cambios
- ✅ ContentQueryHelper funciona bien
- ✅ JavaScript es simple y funcional

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [X] Bloque aparece en catálogo
- [X] Se puede insertar correctamente
- [X] Preview funciona en editor
- [X] Frontend funciona correctamente
- [X] Campos ACF aparecen en editor
- [X] Todos los tabs se muestran

### Contenido Dinámico
- [X] Contenido de packages funciona
- [X] Contenido de posts funciona
- [X] Contenido de deals funciona
- [X] column_span_pattern funciona
- [X] Contenido manual funciona
- [X] Demo cards se muestran si vacío

### Grid Desktop
- [X] Grid responsive funciona
- [X] column_span funciona (1-4 espacios)
- [X] grid_columns funciona (2-8 cols)
- [X] card_gap funciona
- [X] hover_effect funciona (squeeze/lift/glow/zoom/none)

### Slider Mobile
- [X] Slider funciona en mobile
- [X] Arrows funcionan
- [X] Dots funcionan
- [X] Autoplay funciona
- [X] Scroll-snap funciona

### Estilos
- [X] image_position funciona (left/right)
- [X] image_width funciona (30-60%)
- [X] image_border_radius funciona
- [X] text_alignment funciona
- [X] button_alignment funciona
- [X] button_color_variant funciona
- [X] badge_color_variant funciona (global e individual)

### Arquitectura
- [X] Hereda de BlockBase
- [X] Usa ContentQueryHelper
- [X] Template recibe $data (no $GLOBALS)
- [X] load_template() funciona
- [ ] Namespace correcto (⚠️ pendiente)

### Seguridad
- [X] Template escapa todos los campos
- [X] URLs escapadas con esc_url()
- [X] HTML escapado con esc_html()
- [X] wp_kses_post() para HTML permitido

### Clean Code
- [X] DocBlocks presentes
- [X] Métodos razonablemente cortos
- [X] Sin duplicación
- [ ] DocBlocks más detallados (opcional)
- [ ] column_span_pattern extraído (opcional)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Hereda de BlockBase correctamente
- ✅ Registra campos ACF completos (4 tabs organizados)
- ✅ Usa ContentQueryHelper para contenido dinámico
- ✅ Template robusto con fallbacks
- ✅ Separación MVC excelente
- ✅ Escapado de seguridad completo
- ✅ JavaScript vanilla simple
- ✅ Sin dependencias externas
- ✅ Funcionalidad única (cards horizontales)
- ⚠️ Namespace incorrecto (menor)
- ⚠️ Prefijos de campos inconsistentes (menor)
- ⚠️ DocBlocks podrían ser más detallados (menor)

### Puntuación: 9/10

**Fortalezas:**
- Arquitectura MVC excelente
- Herencia correcta de BlockBase
- ContentQueryHelper integrado perfectamente
- Campos ACF muy completos y organizados
- Template muy robusto con fallbacks
- Escapado de seguridad completo
- Funcionalidad única y clara
- Sin dependencias externas
- JavaScript simple y funcional
- Soporte dinámico + manual
- Grid flexible con hover effects
- Slider mobile nativo

**Debilidades:**
- ⚠️ Namespace `Travel\Blocks\Blocks\ACF` (debería ser `Travel\Blocks\ACF`)
- ⚠️ Prefijos de campos inconsistentes (`field_sbs_` vs `sbs_`)
- ⚠️ DocBlocks podrían ser más detallados
- ⚠️ Lógica de column_span_pattern podría extraerse a método

**Recomendación:**
✅ **MANTENER Y USAR COMO REFERENCIA**

**Razones:**
1. Arquitectura MVC excelente
2. Herencia correcta de BlockBase
3. ContentQueryHelper integrado
4. Campos ACF muy completos
5. Template robusto y seguro
6. Funcionalidad única (horizontal cards)
7. Sin problemas críticos
8. Solo optimizaciones menores opcionales

**Acción recomendada:**
1. ✅ Usar como referencia para otros bloques
2. Corregir namespace (opcional, 30 min)
3. Mejorar DocBlocks (opcional, 20 min)
4. Extraer lógica de column_span_pattern (opcional, 30 min)

**Comparación:**
| Aspecto | PostsCarousel | SideBySideCards |
|---------|--------------|-----------------|
| Líneas | 756 | 665 |
| Hereda BlockBase | ✅ Sí | ✅ Sí |
| Registra ACF fields | ✅ Sí | ✅ Sí |
| ContentQueryHelper | ✅ Sí | ✅ Sí |
| Template queries | ✅ No | ✅ No |
| Dependencia externa | ❌ No | ❌ No |
| DocBlocks | ✅ Sí | ✅ Sí |
| $GLOBALS | ❌ No | ❌ No |
| Orientación cards | Vertical | **Horizontal** |
| **Puntuación** | 6.5/10 | **9/10** |
| **Recomendación** | ✅ Mantener | ✅ **REFERENCIA** |

**Veredicto:** Este es uno de los bloques MEJOR implementados de todos los auditados. Arquitectura excelente, separación MVC clara, seguridad robusta, y funcionalidad única. Solo tiene optimizaciones menores opcionales. **USAR COMO REFERENCIA para futuros bloques.**

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** NINGUNA - Bloque excelente, solo optimizaciones opcionales
