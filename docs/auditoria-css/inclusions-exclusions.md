# Auditoría: Inclusions & Exclusions

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/inclusions-exclusions.css`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS con fallbacks**, pero las variables principales NO existen en theme.json.

### Variables encontradas:

| Variable CSS | Fallback | Líneas | ¿Existe en theme.json? | Variable theme.json equivalente |
|--------------|----------|---------|------------------------|--------------------------------|
| `--border-radius-md` | `6px` | 47, 60, 102, 103, 124, 132, 271 | ❌ No existe | Crear variable local |
| `--color-gray-200` | `#EEEEEE` | 59, 100, 131, 152 | ❌ No existe | Crear variable local |
| `--color-gray-300` | `#E0E0E0` | 68, 324 | ❌ No existe | Crear variable local |
| `--color-gray-50` | `#FAFAFA` | 67 | ❌ No existe | Crear variable local |
| `--color-gray-900` | `#212121` | 84, 174 | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `--color-gray-700` | `#616161` | 205 | ❌ No existe | Crear variable local |
| `--color-gray-600` | `#757575` | 90, 272 | ❌ No existe | Usar `var(--wp--preset--color--gray)` (#666666) |
| `--color-gray-100` | `#F5F5F5` | 270 | ❌ No existe | Crear variable local |
| `--color-success-light` | `#E8F5E9` | 137, 160 | ❌ No existe | Crear variable local |
| `--color-success-lighter` | `#F1F8E9` | 258 | ❌ No existe | Crear variable local |
| `--color-error` | `#F44336` | 253 | ❌ No existe | Crear variable local |
| `--color-error-light` | `#FFEBEE` | 141, 164, 262 | ❌ No existe | Crear variable local |
| `--color-error-lighter` | `#FFEBEE` | 262 | ❌ No existe | Crear variable local |
| `--color-coral` | `#E78C85` | 294 | ❌ No existe | **PROBLEMA:** Usar `--wp--preset--color--secondary` (#C66E65) |

### Colores hardcodeados (sin variables):

| Color | Uso | Líneas |
|-------|-----|--------|
| `white` | Backgrounds | 46, 58, 123 |
| `#E78C85` | Inclusions icon fill, background (coral) | 225, 231, 236 |
| `#FFFFFF` | Icon fill white | 247 |
| `#FFF3CD` | Empty hint background | 597 |
| `#FFE69C` | Empty hint border | 598 |
| `#856404` | Empty hint text | 601 |
| `rgba(0, 0, 0, 0.08)` | Box shadow | 126 |

### Valores hardcodeados (spacing, sizing, etc.):

| Tipo | Valores |
|------|---------|
| Border-radius | `6px`, `50%` |
| Padding | `1rem`, `1.25rem`, `1.5rem`, `2rem`, `3rem`, `4px` |
| Gap | `0.75rem`, `1rem`, `1.5rem`, `2rem` |
| Margin | `0.125rem`, `0.5rem`, `1rem`, `1.5rem` |
| Font-size | `0.875rem`, `0.9375rem`, `1.125rem`, `1.25rem` |
| Border | `2px solid` |
| Transition | `0.3s ease` |
| Box-shadow | `0 2px 8px rgba(0, 0, 0, 0.08)` |
| Icon size | `24px` (width/height) |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa el **color Coral** (#E78C85) para los iconos de inclusiones, que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**Inclusions & Exclusions usa:**
- Coral: #E78C85 (inclusions icons, accordion focus outline)
- Success colors: #E8F5E9, #F1F8E9 (green tints)
- Error colors: #F44336, #FFEBEE (red tints)

### Problemas Adicionales

1. **Escala de grises completa:** Usa gray-50, gray-100, gray-200, gray-300, gray-600, gray-700, gray-900 que no coinciden con theme.json
2. **Colores de estado:** Usa success/error colors personalizados que no existen en theme.json
3. **Border radius:** Solo un tamaño pero no está definido globalmente
4. **Coral hardcoded:** Líneas 225, 231, 236 tienen #E78C85 hardcoded (no usa la variable)
5. **Focus outline:** Usa coral para el focus del accordion (línea 294)

### Sistema de Colores del Bloque

El bloque tiene un sistema de colores específico:
- **Inclusions (✓):** Coral (#E78C85) → Debería ser Secondary
- **Exclusions (✗):** Red/Error (#F44336) → OK, pero crear variable local

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85) y colores de estado
2. **Opción B:** Cambiar iconos de inclusions para usar Secondary (#C66E65) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque para todos los colores

**Recomendación:** Opción B + C combinadas:
- Usar `--wp--preset--color--secondary` para inclusions (coral → secondary)
- Crear variables locales para success/error colors
- Crear escala de grises local

---

## Plan de Refactorización

### Paso 1: Reemplazar coral hardcoded por variable secondary

```css
/* ANTES */
.inclusions-exclusions__column--inclusions .inclusions-exclusions__item-icon svg,
.inclusions-exclusions__list--inclusions .inclusions-exclusions__item-icon svg {
    fill: #E78C85;
}

.inclusions-exclusions__column--inclusions .inclusions-exclusions__header-icon svg,
.inclusions-exclusions__accordion-item--inclusions .inclusions-exclusions__accordion-icon svg {
    fill: #E78C85;
}

.inclusions-exclusions__accordion-item--inclusions .inclusions-exclusions__item-icon {
    background-color: #E78C85;
}

/* DESPUÉS */
.inclusions-exclusions__column--inclusions .inclusions-exclusions__item-icon svg,
.inclusions-exclusions__list--inclusions .inclusions-exclusions__item-icon svg {
    fill: var(--wp--preset--color--secondary); /* #C66E65 */
}

.inclusions-exclusions__column--inclusions .inclusions-exclusions__header-icon svg,
.inclusions-exclusions__accordion-item--inclusions .inclusions-exclusions__accordion-icon svg {
    fill: var(--wp--preset--color--secondary);
}

.inclusions-exclusions__accordion-item--inclusions .inclusions-exclusions__item-icon {
    background-color: var(--wp--preset--color--secondary);
}
```

### Paso 2: Reemplazar focus outline coral

```css
/* ANTES */
.inclusions-exclusions--accordion .inclusions-exclusions__accordion-header:focus-visible {
    outline: 2px solid var(--color-coral, #E78C85);
}

/* DESPUÉS */
.inclusions-exclusions--accordion .inclusions-exclusions__accordion-header:focus-visible {
    outline: 2px solid var(--wp--preset--color--secondary);
}
```

### Paso 3: Variables locales necesarias

```css
.inclusions-exclusions {
    /* Colors from theme.json */
    --incex-text: var(--wp--preset--color--contrast);
    --incex-gray: var(--wp--preset--color--gray);
    --incex-inclusions: var(--wp--preset--color--secondary); /* #C66E65 */

    /* Local grays */
    --incex-gray-50: #FAFAFA;
    --incex-gray-100: #F5F5F5;
    --incex-gray-200: #EEEEEE;
    --incex-gray-300: #E0E0E0;
    --incex-gray-600: #757575;
    --incex-gray-700: #616161;
    --incex-gray-900: #212121;

    /* Success colors (green tints for inclusions) */
    --incex-success: #4CAF50;
    --incex-success-light: #E8F5E9;
    --incex-success-lighter: #F1F8E9;

    /* Error colors (red tints for exclusions) */
    --incex-error: #F44336;
    --incex-error-light: #FFEBEE;

    /* Border radius */
    --incex-radius-md: 6px;
    --incex-radius-full: 50%;

    /* Spacing */
    --incex-spacing-xs: 0.75rem;
    --incex-spacing-sm: 1rem;
    --incex-spacing-md: 1.5rem;
    --incex-spacing-lg: 2rem;
    --incex-spacing-xl: 3rem;

    /* Effects */
    --incex-transition: 0.3s ease;
    --incex-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);

    /* Icon size */
    --incex-icon-size: 24px;
}
```

### Paso 4: Actualizar usos de variables

Reemplazar todas las referencias a las variables antiguas por las nuevas con prefijo `--incex-`.

---

## CSS Personalizado Necesario: **SÍ**

**Razones:**
1. Necesita escala completa de grises para diferentes estados de UI
2. Necesita colores de estado (success/error) con variantes light y lighter
3. Necesita border-radius específico
4. Necesita spacing personalizado para diferentes layouts
5. Sistema de 3 layouts: two-column, stacked, accordion
6. Sistema de 3 estilos: default, cards, bordered
7. Gradientes para cards style (líneas 258, 262)
8. Animaciones para accordion

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.inclusions-exclusions`, no hay conflictos globales.

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. Crear variables locales con prefijo `--incex-`
3. Reemplazar `#E78C85` hardcoded por `--wp--preset--color--secondary`
4. Reemplazar focus outline coral por secondary
5. Actualizar todas las variables gray-* por las nuevas
6. Testing en los 3 layouts (two-column, stacked, accordion)
7. Testing en los 3 estilos (default, cards, bordered)
8. Verificar contraste de secondary (#C66E65) para iconos
9. Commit: `refactor(inclusions-exclusions): migrate to theme.json colors and local variables`

---

## Notas Adicionales

**Buenas prácticas encontradas:**
- ✅ Usa variables CSS con fallbacks
- ✅ Selectores bien scoped con prefijo `.inclusions-exclusions`
- ✅ Múltiples layouts flexibles (two-column, stacked, accordion)
- ✅ Múltiples estilos visuales (default, cards, bordered)
- ✅ Responsive design
- ✅ Estados de accesibilidad (focus-visible)
- ✅ Animaciones suaves (slideDown)
- ✅ Print styles (expande accordions automáticamente)
- ✅ Placeholder state para editor

**Características del bloque:**
- **3 layouts:** Two-column, Stacked, Accordion
- **3 estilos:** Default, Cards, Bordered
- **2 columnas:** Inclusions (✓ coral/green), Exclusions (✗ red)
- **Sistema de iconos:** Circle backgrounds para accordion, flat para otros
- **Gradientes:** Cards style usa gradientes sutiles
- **Interactividad:** Accordion con toggle y animación

**Mejoras recomendadas:**
- Considerar usar `color-mix()` para generar las variantes light/lighter de colors
- Los gradientes usan `--color-success-lighter` y `--color-error-lighter` que deberían actualizarse también
- Evaluar si el contraste de secondary (#C66E65) es suficiente para los iconos
