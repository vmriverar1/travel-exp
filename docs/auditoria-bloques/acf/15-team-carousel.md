# Auditoría: TeamCarousel (ACF)

**Fecha:** 2025-11-09
**Bloque:** 15/15 ACF
**Tiempo:** 45 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `team-carousel`
- **Namespace:** `acf/team-carousel`
- **Campos ACF:** `layout_style`, `team_members` (repeater), `columns_desktop`, etc.
- **JavaScript:** `carousel.js` - maneja carousel nativo (CSS scroll-snap)
- **Clases CSS:** `.tc-carousel`, `.tc-slides`, `.tc-slide`, `.is-active` - usadas en JavaScript
- **ACF hook:** `acf.addAction('render_block_preview')` para re-init en editor

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **Layout variations:** 2 templates (profile-card, full-body)
- **Demo data:** get_demo_team_members() tiene 6 miembros con datos completos
- **Mobile-only carousel:** JavaScript solo activo en mobile (< 1024px)
- **Skeleton loader:** Animación de carga con shimmer effect

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/TeamCarousel.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Templates:** `/wp-content/plugins/travel-blocks/src/Blocks/TeamCarousel/templates/`
- `profile-card.php` (foto circular + descripción + achievements)
- `full-body.php` (foto vertical + nombre + posición)

**Assets:**
- CSS: `/assets/blocks/TeamCarousel/style.css` (534 líneas)
- JS: `/assets/blocks/TeamCarousel/carousel.js` (346 líneas - carousel nativo)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo  [ ] Usa InnerBlocks

---

## 2. Propósito y Funcionalidad

**Descripción:** Muestra perfiles de equipo en carousel/grid. Soporta 2 variaciones de layout. Desktop: Grid estático. Mobile: Carousel con scroll-snap. Incluye skeleton loader.

**Inputs (ACF):**
- **General:** `layout_style` (profile_card/full_body), `posts_to_display` (1-20), `columns_desktop` (2/3/4)
- **Dimensions:** `image_height` (solo full_body, 200-800px)
- **Carousel:** `show_arrows`, `show_dots`, `enable_autoplay`, `autoplay_delay` (1000-30000ms)
- **Team Members (Repeater):**
  - `image`: Foto del miembro
  - `name`: Nombre
  - `position`: Cargo (solo full_body)
  - `description`: Bio (solo profile_card)
  - `achievements`: Sub-repeater con textos (solo profile_card)

**Outputs:**
- Desktop: Grid 2/3/4 columnas (estático, sin carousel)
- Mobile: Carousel con scroll-snap (< 1024px)
- 2 layouts: Profile Card (circular) o Full Body Portrait (vertical)
- Demo data si no hay team members
- Skeleton loader con shimmer animation

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
1. get_demo_team_members($layout_style): array - Genera 6 miembros demo con datos completos
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (NO usa BlockBase)

**Configuración:**
- name: `team-carousel`
- category: `travel`
- icon: `groups`
- keywords: ['team', 'carousel', 'people', 'staff', 'profiles']
- supports: align=[wide,full], spacing, color, typography, anchor, customClassName
- enqueue_assets: Doble registro (línea 52 y hooks líneas 13-14)

**Block.json:** No existe

---

## 5. Campos ACF

**Definición:** [X] PHP inline (251 líneas de campos)

**Grupo:** `group_team_carousel`

**Estructura:**
- **Layout Style:** profile_card / full_body
- **Columns Desktop:** 2/3/4
- **Image Height:** 200-800px (solo full_body)
- **Posts to Display:** 1-20 miembros
- **Show Arrows/Dots:** true_false
- **Enable Autoplay:** true_false
- **Autoplay Delay:** 1000-30000ms
- **Team Members (Repeater):**
  - image: ACF image field
  - name: text (default: "Team Member Name")
  - position: text (solo full_body)
  - description: textarea (solo profile_card)
  - achievements: sub-repeater (solo profile_card)
    - achievement_text: text

**Campos Complejos:**
- Repeater con sub-repeater (achievements)
- Conditional logic para campos según layout_style
- Defaults bien definidos para preview

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render_block()`

**Obtención de Datos:**
1. ACF fields: layout_style, posts_to_display, columns_desktop, etc. (líneas 81-88)
2. Team members repeater (línea 91)
3. **Demo data fallback:** Si no hay team members → `get_demo_team_members()` (líneas 94-95)
4. **Image placeholder:** Si member tiene imagen vacía → Picsum placeholder (líneas 98-120)
5. Limit to posts_to_display (línea 124)

**Procesamiento:**
- Rellena imágenes vacías con Picsum (diferentes para profile_card vs full_body)
- Slice array a posts_to_display
- Prepara template_data array (líneas 127-138)

**Variables al Template:**
```php
- block_wrapper_attributes
- layout_style
- team_members (array con image, name, position/description/achievements)
- columns_desktop
- image_height
- show_arrows, show_dots, enable_autoplay, autoplay_delay
- is_preview
```

**Template Loading:**
- Template dinámico según layout_style (líneas 141-142)
- Convierte underscores a hyphens (profile_card → profile-card.php)
- Usa `extract()` + `include` (líneas 145-146)
- ⚠️ Verifica existencia de template (línea 144)

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:** ✅ SÍ - carousel.js (346 líneas)

**TeamCarousel Class:**
- **Mobile-only:** Solo activo en mobile (< 1024px) (líneas 39, 175, 186, 198)
- **CSS scroll-snap:** Usa scroll-snap nativo del navegador (líneas 204-208)
- **Navigation:** Arrows + dots + keyboard (arrows)
- **IntersectionObserver:** Detecta slide activo automáticamente (líneas 135-153)
- **Autoplay:** Solo en mobile si enabled (líneas 77-79, 260-263)
- **Skeleton loader:** Oculta skeleton después de 300ms (líneas 51-57)
- **Responsive:** Recrea dots al cambiar de desktop a mobile (líneas 299-328)
- **ACF hook:** Re-init en Gutenberg editor (líneas 343-345)

**Features JavaScript:**
- Smooth scroll to slide
- Touch-friendly (CSS scroll-snap)
- Autoplay pause on hover/focus
- Keyboard navigation (arrows)
- Dots navigation
- Arrows disabled en desktop (líneas 239-244)

**Dependencias Externas:**
- ✅ Ninguna - Implementación nativa sin librerías

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ OK
- Responsabilidad clara: Carousel de team members
- Separación adecuada entre clase, templates y JS

**OCP:** ✅ OK
- 2 layouts extensibles
- Fácil agregar nuevos layouts

**LSP:** ✅ N/A - No hereda

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ✅ OK
- Sin dependencias externas (no usa ContentQueryHelper ni otros helpers)

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **render_block(): 76 líneas** (74-150) - OK
- ⚠️ **register_fields(): 251 líneas** (336-590) - Largo pero aceptable para ACF fields
- ⚠️ **get_demo_team_members(): 179 líneas** (152-334) - Solo datos demo pero muy largo

**Anidación:**
- ✅ **Anidación baja** (1-2 niveles máximo)

**Duplicación:**
- ⚠️ **Demo data duplicado para cada layout**
  - Líneas 156-260 (profile_card): 6 miembros con achievements
  - Líneas 263-330 (full_body): 6 miembros con position
  - **Impacto:** BAJO - Solo demo data
- ⚠️ **Skeleton loader duplicado** (similar a otros bloques)
- ⚠️ **Carousel pattern** (similar a PostsCarousel, TaxonomyTabs)

**Nombres:**
- ✅ Nombres descriptivos y claros
- ✅ Prefijo `tc` en clases CSS (consistente)
- ✅ Prefijo `field_tc_` en campos ACF

**Código Sin Uso:**
- ✅ No se detectó código sin uso

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ Demo data hardcodeado (sin inputs del usuario)

**Escapado:**
- ⚠️ Templates deben escapar outputs (verificar templates)
- ⚠️ Image URLs desde ACF

**Nonces:**
- ✅ N/A - No tiene formularios

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 3
  - Impacto: BAJO (pero inconsistente)

**Separación MVC:**
- ✅ Controller (clase) / View (2 templates) bien separados
- ❌ Demo data hardcodeado en controller (179 líneas)

**Acoplamiento:**
- ✅ **Bajo acoplamiento** - No depende de otros bloques/helpers
- ⚠️ Doble registro de assets (líneas 13-14 + 52)

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Todos los demás bloques heredan de BlockBase
  - Implementa todo manualmente
  - Ubicación: Línea 5
  - Impacto: MEDIO - Inconsistencia arquitectónica (pero es simple)

**Otros:**
- ✅ Implementación nativa sin librerías (CSS scroll-snap)
- ✅ 2 templates bien separados
- ⚠️ Ubicación de archivos:
  - Clase: `/src/Blocks/ACF/TeamCarousel.php`
  - Templates: `/src/Blocks/TeamCarousel/templates/` (sin ACF/)
  - Assets: `/assets/blocks/TeamCarousel/` (sin ACF/)

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**BLOQUE SIMPLE Y FUNCIONAL. Refactorizaciones menores.**

### Prioridad Media

**1. Mover demo data a archivos JSON**
- **Acción:** Crear:
  - `/config/demo-data/team-carousel-profile-card.json`
  - `/config/demo-data/team-carousel-full-body.json`
- **Razón:** 179 líneas de datos hardcodeados
- **Riesgo:** BAJO
- **Precauciones:** Mantener estructura exacta
- **Esfuerzo:** 1h

**2. Decidir estrategia de herencia con BlockBase**
- **Acción:** ¿Por qué NO hereda de BlockBase?
  - Opción A: Refactorizar para heredar de BlockBase
  - Opción B: Mantener independiente (bloque simple)
- **Razón:** Inconsistencia con otros bloques
- **Riesgo:** MEDIO - Este bloque es simple, quizás no lo necesita
- **Esfuerzo:** 3-4h (si se hereda)

**3. Corregir Namespace**
- **Acción:** Cambiar a `Travel\Blocks\ACF`
- **Razón:** PSR-4 y consistencia
- **Riesgo:** MEDIO - Actualizar autoload
- **Precauciones:** Composer dump-autoload
- **Esfuerzo:** 30 min

**4. Eliminar doble registro de assets**
- **Acción:** Líneas 13-14 + línea 52
- **Razón:** Duplicación de lógica
- **Riesgo:** BAJO - Verificar carga en editor Y frontend
- **Precauciones:** Testing exhaustivo
- **Esfuerzo:** 30 min

### Prioridad Baja

**5. Consolidar skeleton loader**
- **Acción:** Crear componente compartido de skeleton loader
- **Razón:** Patrón repetido en varios bloques
- **Riesgo:** BAJO
- **Esfuerzo:** 2-3h (si se hace consolidación general)

**6. Consolidar carousel pattern**
- **Acción:** Analizar si se puede compartir con PostsCarousel, TaxonomyTabs
- **Razón:** Patrón similar (mobile slider, dots, arrows)
- **Riesgo:** MEDIO - Requiere análisis
- **Esfuerzo:** 4-6h (análisis + implementación)

**7. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **PRIMERO:** Mover demo data a JSON
2. Corregir namespace
3. Eliminar doble registro de assets
4. Decidir estrategia BlockBase (evaluar necesidad)
5. Consolidar skeleton loader (en refactorización general)
6. Consolidar carousel pattern (en refactorización general)
7. Crear block.json (opcional)

**Precauciones Generales:**
- ⛔ NO cambiar block name `team-carousel`
- ⛔ NO cambiar nombres de campos ACF (field_tc_*)
- ⛔ NO romper 2 layout variations
- ⛔ NO romper mobile carousel (CSS scroll-snap)
- ⛔ NO romper JavaScript (IntersectionObserver, autoplay)
- ⛔ NO romper ACF hook (render_block_preview)
- ✅ Testing: 2 layouts (profile-card, full-body)
- ✅ Testing: Desktop grid (2/3/4 columnas)
- ✅ Testing: Mobile carousel (scroll-snap, arrows, dots)
- ✅ Testing: Autoplay, keyboard navigation
- ✅ Testing: Skeleton loader
- ✅ Testing: Demo data (si no hay team members)
- ✅ Testing: Image placeholders (Picsum)

---

## 11. Checklist Post-Refactorización

### Funcionalidad CRÍTICA
- [ ] Bloque aparece en catálogo
- [ ] Layout profile_card funciona (circular + descripción + achievements)
- [ ] Layout full_body funciona (vertical + nombre + posición)
- [ ] Desktop: Grid 2 columnas funciona
- [ ] Desktop: Grid 3 columnas funciona
- [ ] Desktop: Grid 4 columnas funciona
- [ ] Desktop: NO muestra arrows/dots (grid estático)
- [ ] Mobile: Carousel funciona (scroll-snap)
- [ ] Mobile: Arrows funcionan (prev/next)
- [ ] Mobile: Dots funcionan (navigation)
- [ ] Mobile: Keyboard navigation funciona
- [ ] Mobile: Autoplay funciona (si enabled)
- [ ] Mobile: Autoplay pause on hover/focus
- [ ] IntersectionObserver actualiza active states
- [ ] Skeleton loader aparece y se oculta
- [ ] Demo data aparece si no hay team members
- [ ] Image placeholders funcionan (Picsum)
- [ ] Image height funciona (solo full_body)
- [ ] Posts to display funciona (1-20)
- [ ] Repeater achievements funciona (solo profile_card)
- [ ] Conditional fields funcionan (position/description/achievements)
- [ ] ACF hook re-init funciona en editor

### Arquitectura
- [ ] Namespace correcto (si se cambió)
- [ ] Hereda de BlockBase (si se decidió)
- [ ] Demo data en JSON (si se movió)
- [ ] Sin duplicación de assets

### Seguridad
- [ ] Templates escapan outputs correctamente

### Clean Code
- [ ] Sin código duplicado innecesario

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funciona correctamente
- ✅ Implementación nativa sin librerías (CSS scroll-snap)
- ✅ Código limpio y bien estructurado
- ✅ JavaScript profesional (IntersectionObserver)
- ✅ Mobile-only carousel (desktop = grid)
- ✅ 2 layouts bien diferenciados
- ✅ Skeleton loader con shimmer
- ✅ Demo data completo y realista
- ⚠️ NO hereda de BlockBase (inconsistencia menor)
- ⚠️ Demo data hardcodeado (179 líneas)
- ⚠️ Namespace incorrecto

### Puntuación: 7.5/10

**Fortalezas:**
- Bloque simple y funcional
- Implementación nativa sin dependencias externas
- CSS scroll-snap (moderno y performante)
- JavaScript limpio (346 líneas bien estructuradas)
- IntersectionObserver para auto-update active states
- Skeleton loader profesional
- 2 layouts bien implementados (profile-card, full-body)
- Desktop grid estático (no carousel innecesario)
- Mobile carousel con autoplay
- Demo data completo con 6 miembros realistas
- Conditional fields bien usados
- Sub-repeater para achievements

**Debilidades:**
- NO hereda de BlockBase (pero bloque simple)
- 179 líneas de demo data hardcodeado
- Namespace incorrecto
- Doble registro de assets
- Duplicación de carousel pattern con otros bloques (menor)
- No tiene block.json

**Recomendación:** **MANTENIMIENTO MENOR** - Bloque bien implementado. Solo necesita limpieza menor (demo data a JSON, namespace) y decisión sobre BlockBase. Es el bloque ACF más limpio y simple auditado.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** **BAJA PRIORIDAD** - Solo limpieza menor, bloque en buen estado
