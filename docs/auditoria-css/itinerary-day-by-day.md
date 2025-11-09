# Auditoría: Itinerary Day by Day

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/itinerary-day-by-day.css`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS con fallbacks**, pero las variables principales NO existen en theme.json.

### Variables encontradas:

| Variable CSS | Fallback | Líneas | ¿Existe en theme.json? | Variable theme.json equivalente |
|--------------|----------|---------|------------------------|--------------------------------|
| `--color-gray-700` | `#616161` | 180, 219, 365, 402 | ❌ No existe | Crear variable local |
| `--color-gray-900` | `#212121` | 198, 378 | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `--color-coral` | `#E78C85` | 227, 344, 373, 426 | ❌ No existe | **PROBLEMA:** Usar `--wp--preset--color--secondary` (#C66E65) |
| `--color-teal-light` | `#E3F2F5` | 247 | ❌ No existe | Derivar de primary con opacity |
| `--color-teal-dark` | `#3A7A8C` | 248 | ❌ No existe | Similar a `--wp--preset--color--primary` (#17565C) |
| `--border-radius-sm` | `4px` | 249 | ❌ No existe | Crear variable local |
| `--border-radius-md` | `6px` | 419 | ❌ No existe | Crear variable local |
| `--color-gray-100` | `#F5F5F5` | 294, 418 | ❌ No existe | Crear variable local |
| `--color-gray-600` | `#757575` | 420 | ❌ No existe | Usar `var(--wp--preset--color--gray)` (#666666) |
| `--color-gray-300` | `#E0E0E0` | 452 | ❌ No existe | Crear variable local |

### Colores hardcodeados (sin variables):

| Color | Uso | Líneas |
|-------|-----|--------|
| `#FFF6F5` | Header background (pink tint) | 35 |
| `#FFE8E5` | Header hover (pink darker) | 46 |
| `#212121` | Header text | 36, 82, 90, 124 |
| `#D0D0D0` | Pagination dots | 328 |
| `#A0A0A0` | Pagination hover | 339 |
| `rgba(0, 0, 0, 0.08)` | Header hover shadow | 47 |
| `rgba(0, 0, 0, 0.1)` | Gallery shadow | 281 |

### Valores hardcodeados (spacing, sizing, etc.):

| Tipo | Valores |
|------|---------|
| Border-radius | `50px`, `12px`, `4px`, `6px` |
| Padding | Multiple (0.5rem-2rem range) |
| Gap | `0.5rem`, `0.75rem`, `1.5rem` |
| Margin | Multiple |
| Font-size | `20px`, `16px`, `0.875rem`, `0.9375rem`, `1rem` |
| Transitions | `0.3s ease` |
| Box-shadow | `0 2px 8px` |
| Icon size | `24px` (width/height) |
| Gallery aspect-ratio | `4/3` |
| Float width | `45%`, max `400px` |
| Pagination dots | `10px` width/height, `5px` margin |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa el **color Coral** (#E78C85) en múltiples lugares, que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**Itinerary Day by Day usa:**
- Coral: #E78C85 (bullets, pagination dots, focus outline)
- Pink tints: #FFF6F5, #FFE8E5 (header backgrounds)
- Teal: #E3F2F5, #3A7A8C (meal badges)

### Problemas Adicionales

1. **Coral en 4 lugares:**
   - List bullets (actividades, items) - líneas 227, 373
   - Pagination active dot - línea 344
   - Focus outline - línea 426

2. **Pink backgrounds:** Usa #FFF6F5 y #FFE8E5 que son tints del coral, no están en theme.json

3. **Teal variables:** Usa --color-teal-light y --color-teal-dark que son similares a primary pero no exactos

4. **Escala de grises:** Usa gray-100, gray-300, gray-600, gray-700, gray-900

5. **Galería Swiper:** Tiene estilos específicos para Swiper.js con !important

### Sistema de Colores del Bloque

- **Header:** Pink tint (derivado de coral)
- **Bullets:** Coral
- **Meal badges:** Teal (similar a primary)
- **Pagination active:** Coral
- **Focus outline:** Coral

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85) y pink tints
2. **Opción B:** Cambiar coral por Secondary (#C66E65) y derivar pink tints de secondary
3. **Opción C:** Crear variables locales dentro del bloque para toda la paleta

**Recomendación:** Opción B - Usar Secondary y derivar tints
- Usar `--wp--preset--color--secondary` (#C66E65) en lugar de coral
- Derivar pink backgrounds del secondary con opacity
- Usar `--wp--preset--color--primary` para meal badges

---

## Plan de Refactorización

### Paso 1: Reemplazar coral por secondary

```css
/* ANTES */
.itinerary-day__activities-list li::before {
    color: var(--color-coral, #E78C85);
}

.itinerary-day__items-list li::before {
    color: var(--color-coral, #E78C85);
}

.itinerary-gallery-slider .swiper-pagination-bullet-active {
    background: var(--color-coral, #E78C85);
}

.itinerary-day__header:focus-visible {
    outline: 2px solid var(--color-coral, #E78C85);
}

/* DESPUÉS */
.itinerary-day__activities-list li::before {
    color: var(--wp--preset--color--secondary);
}

.itinerary-day__items-list li::before {
    color: var(--wp--preset--color--secondary);
}

.itinerary-gallery-slider .swiper-pagination-bullet-active {
    background: var(--wp--preset--color--secondary);
}

.itinerary-day__header:focus-visible {
    outline: 2px solid var(--wp--preset--color--secondary);
}
```

### Paso 2: Derivar pink backgrounds de secondary

```css
/* ANTES */
.itinerary-day__header {
    background: #FFF6F5;
}

.itinerary-day__header:hover {
    background: #FFE8E5;
}

/* DESPUÉS - Usar variables locales derivadas */
.itinerary-day__header {
    background: var(--itinerary-header-bg);
}

.itinerary-day__header:hover {
    background: var(--itinerary-header-hover-bg);
}
```

### Paso 3: Reemplazar teal por primary

```css
/* ANTES */
.itinerary-day__meal-badge {
    background: var(--color-teal-light, #E3F2F5);
    color: var(--color-teal-dark, #3A7A8C);
}

/* DESPUÉS */
.itinerary-day__meal-badge {
    background: var(--itinerary-meal-bg);
    color: var(--wp--preset--color--primary);
}
```

### Paso 4: Variables locales necesarias

```css
.itinerary-day-by-day {
    /* Colors from theme.json */
    --itinerary-accent: var(--wp--preset--color--secondary); /* #C66E65 */
    --itinerary-primary: var(--wp--preset--color--primary); /* #17565C */
    --itinerary-text: var(--wp--preset--color--contrast);
    --itinerary-gray: var(--wp--preset--color--gray);

    /* Derived colors */
    --itinerary-header-bg: #FFF3F2; /* Derivado de secondary con opacity */
    --itinerary-header-hover-bg: #FFE5E3; /* Derivado de secondary más oscuro */
    --itinerary-meal-bg: #E0F2F1; /* Derivado de primary con opacity */

    /* Local grays */
    --itinerary-gray-100: #F5F5F5;
    --itinerary-gray-300: #E0E0E0;
    --itinerary-gray-600: #757575;
    --itinerary-gray-700: #616161;
    --itinerary-gray-900: #212121;

    /* Pagination dots */
    --itinerary-dot-inactive: #D0D0D0;
    --itinerary-dot-hover: #A0A0A0;

    /* Border radius */
    --itinerary-radius-sm: 4px;
    --itinerary-radius-md: 6px;
    --itinerary-radius-lg: 12px;
    --itinerary-radius-pill: 50px;

    /* Spacing */
    --itinerary-spacing-xs: 0.5rem;
    --itinerary-spacing-sm: 0.75rem;
    --itinerary-spacing-md: 1rem;
    --itinerary-spacing-lg: 1.5rem;
    --itinerary-spacing-xl: 2rem;

    /* Transitions */
    --itinerary-transition: 0.3s ease;

    /* Gallery */
    --itinerary-gallery-width: 45%;
    --itinerary-gallery-max-width: 400px;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razones:**
1. Accordion complejo con header, content, y animaciones
2. Galería de imágenes con Swiper.js (requiere overrides con !important)
3. Float layout para galería con text wrap
4. Múltiples estados de UI (open/closed, hover, focus)
5. Escala de grises personalizada
6. Border-radius específicos (pill, lg, md, sm)
7. Sistema de bullets personalizados para listas
8. Meal badges con colores específicos
9. Pagination dots custom styling
10. Print styles (expande todos los accordions)

---

## Selectores Específicos: ⚠️ MIXED

1. **Bien scoped:** La mayoría usa `.itinerary-day-by-day` y `.itinerary-day__*`
2. **Editor overrides:** Usa `.editor-styles-wrapper` para ciertos estilos (líneas 167, 172, 256, etc.)
3. **Swiper overrides:** Usa selectores específicos de Swiper con !important
4. **Global selectors:** Algunos usan selectores de clases de Swiper sin prefix

**Acción requerida:**
- Los selectores de Swiper son necesarios pero están bien scoped dentro de `.itinerary-gallery-slider`

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. Crear variables locales con prefijo `--itinerary-`
3. Reemplazar `--color-coral` por `--wp--preset--color--secondary`
4. Derivar pink backgrounds (#FFF6F5, #FFE8E5) del secondary
5. Reemplazar teal colors por primary
6. Actualizar pagination dots active color
7. Actualizar focus outline color
8. Testing del accordion (open/close)
9. Testing de la galería Swiper
10. Testing responsive (desktop, tablet, mobile)
11. Commit: `refactor(itinerary-day-by-day): migrate to theme.json colors and local variables`

---

## Notas Adicionales

**Buenas prácticas encontradas:**
- ✅ Usa variables CSS con fallbacks
- ✅ Accordion accesible con [hidden] y focus-visible
- ✅ Responsive design completo
- ✅ Galería con aspect-ratio para evitar layout shift
- ✅ Float layout con clearfix
- ✅ Print styles (expande accordions automáticamente)
- ✅ Animaciones suaves (slideDown)
- ✅ Cursor states (grab/grabbing para galería)
- ✅ User-select: none en imágenes para mejor UX

**Características del bloque:**
- **Accordion:** Día por día expandible
- **Galería flotante:** Float right con text wrap
- **Swiper.js:** Slider de imágenes con pagination dots
- **Secciones:** Description, Activities, Meals, Items, Meta info
- **Meal badges:** Diseño especial con fondo teal
- **Bullets personalizados:** Coral bullets para listas

**Mejoras recomendadas:**
- Considerar usar CSS custom properties para hacer los colores de meal badges configurables
- Evaluar si se puede eliminar !important de estilos de Swiper
- Los pink backgrounds (#FFF6F5, #FFE8E5) deberían calcularse con `color-mix()` en lugar de hardcodear
