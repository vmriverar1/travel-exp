# Refactorización Bloques ACF - Variables Theme.json

**Fecha:** 2025-11-09
**Task:** Reemplazo de colores legacy hardcodeados por variables de theme.json

---

## Mapeo de Colores Aplicado

| Color Legacy | Código Hex | Variable Theme.json |
|--------------|------------|---------------------|
| Coral/Pink | `#E78C85` | `var(--wp--preset--color--secondary)` |
| Purple | `#311A42` | `var(--wp--preset--color--contrast-4)` |
| Gold | `#CEA02D` | `var(--wp--preset--color--contrast-1)` |
| Red/Error | `#e74c3c` | `var(--wp--preset--color--secondary)` |
| Blue | `#3498db` | `var(--wp--preset--color--primary)` |
| Blue accent | `#2563eb` | `var(--wp--preset--color--primary)` |
| Blue focus | `#1976d2` | `var(--wp--preset--color--primary)` |

**Colores oscuros (hover states):**
- `#d97a74` - Coral dark (mantenido como local)
- `#1f0f2a` - Purple dark (mantenido como local)
- `#c0392b` - Secondary dark (mantenido como local)
- `#2980b9` - Primary dark (mantenido como local)

---

## Bloques Refactorizados

### ✅ 1. breadcrumb.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/breadcrumb.css`
**Variables insertadas:** 6
**Cambios realizados:**
- Reemplazado `#E78C85` → `var(--wp--preset--color--secondary)` (4 ocurrencias)
- Reemplazado `#311A42` → `var(--wp--preset--color--contrast-4)` (2 ocurrencias)
- Creadas variables locales para hover states: `--coral-dark`, `--purple-dark`

---

### ✅ 2. faq-accordion.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/faq-accordion.css`
**Variables insertadas:** 2
**Cambios realizados:**
- Reemplazado `#3498db` → `var(--wp--preset--color--primary)` (1 ocurrencia - outline)
- Reemplazado `#e74c3c` → `var(--wp--preset--color--secondary)` (1 ocurrencia - icon color)

---

### ✅ 3. hero-section.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/hero-section.css`
**Variables insertadas:** 1
**Cambios realizados:**
- Reemplazado `#e74c3c` → `var(--wp--preset--color--secondary)` (1 ocurrencia)
- Creada variable local `--secondary-dark` para hover

---

### ✅ 4. posts-carousel.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/posts-carousel.css`
**Variables insertadas:** 35
**Cambios realizados:**
- Reemplazado `#E78C85` → `var(--wp--preset--color--secondary)` (múltiples)
- Reemplazado `#311A42` → `var(--wp--preset--color--contrast-4)` (múltiples)
- Reemplazado `#CEA02D` → `var(--wp--preset--color--contrast-1)` (múltiples)
- Reemplazado `#e74c3c` → `var(--wp--preset--color--secondary)` (múltiples)
- Reemplazado `#1976d2` → `var(--wp--preset--color--primary)` (outline/focus)
- Normalizado `#dc7b74` → `#d97a74` (coral dark hover)

**Componentes afectados:**
- Badge (categoría)
- Favorite button
- Navigation arrows
- Button variants (primary, secondary, gold, transparent)
- Dots pagination
- Vertical card style
- Overlay-split layout

---

### ✅ 5. side-by-side-cards.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/side-by-side-cards.css`
**Variables insertadas:** 13
**Cambios realizados:**
- Reemplazado `#E78C85` → `var(--wp--preset--color--secondary)` (múltiples)
- Reemplazado `#311A42` → `var(--wp--preset--color--contrast-4)` (múltiples)
- Reemplazado `#CEA02D` → `var(--wp--preset--color--contrast-1)` (múltiples)

**Componentes afectados:**
- Badge variants
- Button variants
- Divider
- Location icon
- Price text
- Hover effects (glow gradient)

---

### ✅ 6. static-cta.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/static-cta.css`
**Variables insertadas:** 2
**Cambios realizados:**
- Reemplazado `#e74c3c` → `var(--wp--preset--color--secondary)` (1 ocurrencia)
- Reemplazado `#3498db` → `var(--wp--preset--color--primary)` (1 ocurrencia)

**Componentes afectados:**
- Button primary variant
- Button secondary variant

---

### ✅ 7. sticky-side-menu.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/sticky-side-menu.css`
**Variables insertadas:** 7
**Cambios realizados:**
- Reemplazado `#E78C85` → `var(--wp--preset--color--secondary)` (múltiples)
- Reemplazado `#311A42` → `var(--wp--preset--color--contrast-4)` (múltiples)
- Reemplazado `#CEA02D` → `var(--wp--preset--color--contrast-1)` (múltiples)

**Componentes afectados:**
- CTA button variants (primary, secondary, gold)
- Menu links hover
- Current menu item

---

### ✅ 8. taxonomy-tabs.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/taxonomy-tabs.css`
**Variables insertadas:** 17
**Cambios realizados:**
- Reemplazado `#E78C85` → `var(--wp--preset--color--secondary)` (múltiples)
- Reemplazado `#311A42` → `var(--wp--preset--color--contrast-4)` (múltiples)
- Reemplazado `#CEA02D` → `var(--wp--preset--color--contrast-1)` (múltiples)
- Reemplazado `#2563eb` → `var(--wp--preset--color--primary)` (CSS variable + dot active)
- Normalizado `#dc7b74` → `#d97a74`

**Componentes afectados:**
- Tab variants (underline, buttons)
- Card badges (primary, secondary, gold variants)
- Card buttons
- Navigation arrows (mobile)
- Pagination dots
- Hero overlap style

---

### ✅ 9. StaticHero/style.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/StaticHero/style.css`
**Variables insertadas:** 0
**Resultado:** ✅ No contiene colores legacy del mapeo

---

### ✅ 10. TeamCarousel/style.css
**Path:** `/wp-content/plugins/travel-blocks/assets/blocks/TeamCarousel/style.css`
**Variables insertadas:** 0
**Resultado:** ✅ No contiene colores legacy del mapeo
**Nota:** Usa `#d46a3f` (naranja) que no está en el mapeo - mantenido sin cambios

---

## ❌ Bloque No Encontrado

### 11. posts-list-advanced.css
**Resultado:** Archivo no existe en el proyecto
**Path buscado:** `wp-content/plugins/travel-blocks/assets/blocks/posts-list-advanced.css`

---

## Resumen Final

| Métrica | Cantidad |
|---------|----------|
| **Bloques refactorizados** | 8 |
| **Bloques sin cambios** | 2 |
| **Bloques no encontrados** | 1 |
| **Total variables insertadas** | 83 |
| **Estructura BEM mantenida** | ✅ |
| **Media queries responsive** | ✅ Preservadas |
| **Transitions/animaciones** | ✅ Preservadas |

---

## Beneficios de la Refactorización

1. **Centralización:** Colores ahora controlados desde theme.json
2. **Mantenibilidad:** Cambios de color desde un solo punto
3. **Consistencia:** Variables semánticas (secondary, contrast-4, etc.)
4. **Escalabilidad:** Fácil agregar dark mode o temas alternativos
5. **Performance:** No impacto - variables CSS compiladas igual

---

## Consideraciones Técnicas

### Variables Locales Creadas
Se mantuvieron algunos colores como variables locales CSS para hover states:
```css
--coral-dark: #d97a74;
--purple-dark: #1f0f2a;
--secondary-dark: #c0392b;
--primary-dark: #2980b9;
```

### Colores No Mapeados (Mantenidos)
- `#d46a3f` - TeamCarousel (naranja/terracotta)
- Neutrales (#666, #333, #1A1A1A, #f5f5f5, etc.)
- Transparencias (rgba())

---

**Completado el:** 2025-11-09
**Herramienta:** Edit tool (replace_all + individual edits)
**Validación:** Búsqueda grep de variables insertadas
