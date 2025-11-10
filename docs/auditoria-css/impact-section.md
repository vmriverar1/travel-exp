# Auditoría: Impact Section

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/impact-section.css`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS con fallbacks**, pero las variables principales NO existen en theme.json.

### Variables encontradas:

| Variable CSS | Fallback | Líneas | ¿Existe en theme.json? | Variable theme.json equivalente |
|--------------|----------|---------|------------------------|--------------------------------|
| `--border-radius-lg` | `12px` | 72 | ❌ No existe | Crear variable local |
| `--border-radius-md` | `6px` | 141 | ❌ No existe | Crear variable local |
| `--border-radius-sm` | `4px` | 125 | ❌ No existe | Crear variable local |
| `--color-gray-900` | `#212121` | 75, 102 | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `--color-gray-700` | `#424242` | 109 | ❌ No existe | Crear variable local |
| `--color-gray-600` | `#757575` | 142 | ❌ No existe | Usar `var(--wp--preset--color--gray)` (#666666) |
| `--color-gray-100` | `#F5F5F5` | 140 | ❌ No existe | Crear variable local |
| `--color-coral` | `#E78C85` | 123 | ❌ No existe | **PROBLEMA:** Usar `--wp--preset--color--secondary` (#C66E65) |

### Valores hardcodeados (sin variables):

| Tipo | Valor | Uso | Línea |
|------|-------|-----|-------|
| Color | `white` | Text, background | 33, 47, 56, 124 |
| Color | `#d97a74` | Button hover (coral dark) | 131 |
| Color | `rgba(255, 255, 255, 0.95)` | Tile background | 71 |
| Color | `rgba(0, 0, 0, 0.3)` | Text shadows | 48, 57 |
| Color | `rgba(0, 0, 0, 0.15)` | Tile shadow | 73 |
| Color | `rgba(0, 0, 0, 0.25)` | Hover shadow | 80 |
| Color | `rgba(231, 140, 133, 0.4)` | Button shadow (coral) | 127 |
| Color | `rgba(231, 140, 133, 0.5)` | Button hover shadow (coral) | 133 |
| Spacing | `5rem`, `4rem`, `3rem`, `2rem`, `1.5rem`, `1rem` | Padding, margin, gap | Multiple |
| Max-width | `1200px`, `800px` | Container, message width | 30, 55 |
| Font-size | `2.5rem`, `2rem`, `1.75rem`, `1.5rem`, `1.25rem`, `1.125rem`, `1rem`, `0.9375rem` | Text sizes | Multiple |
| Transition | `0.3s ease` | Hover effects | 74, 126 |
| Box-shadow | `0 4px 16px`, `0 8px 24px`, `0 2px 8px` | Shadows | Multiple |
| Transform | `translateY(-8px)`, `translateY(-4px)` | Hover effects | 79, 132 |
| Icon size | `80px`, `60px` | Icon dimensions | 84, 85, 197, 198 |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa el **color Coral** (#E78C85) para el botón CTA, que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**Impact Section usa:**
- Coral: #E78C85 (button)
- Coral Dark: #d97a74 (hover)

### Problemas Adicionales

1. **Escala de grises personalizada:** Usa gray-100, gray-600, gray-700, gray-900 que no coinciden exactamente con theme.json
2. **Valores RGBA con coral:** Usa rgba con valores hardcodeados del coral en sombras (líneas 127, 133)
3. **Border radius:** Usa tres tamaños de border-radius que no están definidos globalmente
4. **Text shadows:** Usa text shadows con rgba hardcodeado

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85)
2. **Opción B:** Cambiar botón para usar Secondary (#C66E65) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque para coral

**Recomendación:** Opción B - Usar `--wp--preset--color--secondary`
- Migrar de coral (#E78C85) a secondary (#C66E65)
- Calcular hover state basado en secondary

---

## Plan de Refactorización

### Paso 1: Reemplazar coral por secondary

```css
/* ANTES */
.impact-section__button {
    background: var(--color-coral, #E78C85);
    box-shadow: 0 4px 16px rgba(231, 140, 133, 0.4);
}

.impact-section__button:hover {
    background: #d97a74;
    box-shadow: 0 8px 24px rgba(231, 140, 133, 0.5);
}

/* DESPUÉS */
.impact-section__button {
    background: var(--wp--preset--color--secondary); /* #C66E65 */
    box-shadow: 0 4px 16px rgba(198, 110, 101, 0.4); /* Secondary rgba */
}

.impact-section__button:hover {
    background: #B35D54; /* Secondary darker */
    box-shadow: 0 8px 24px rgba(198, 110, 101, 0.5);
}
```

### Paso 2: Variables locales necesarias

```css
.impact-section {
    /* Colors */
    --impact-cta: var(--wp--preset--color--secondary);
    --impact-cta-hover: #B35D54; /* Secondary darker */
    --impact-text: var(--wp--preset--color--contrast);
    --impact-gray-light: #F5F5F5;
    --impact-gray-mid: var(--wp--preset--color--gray);
    --impact-gray-dark: #424242;

    /* Backgrounds */
    --impact-tile-bg: rgba(255, 255, 255, 0.95);

    /* Shadows */
    --impact-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --impact-shadow-md: 0 4px 16px rgba(0, 0, 0, 0.15);
    --impact-shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.25);
    --impact-shadow-cta: 0 4px 16px rgba(198, 110, 101, 0.4);
    --impact-shadow-cta-hover: 0 8px 24px rgba(198, 110, 101, 0.5);

    /* Text shadows */
    --impact-text-shadow-sm: 0 1px 4px rgba(0, 0, 0, 0.3);
    --impact-text-shadow-md: 0 2px 8px rgba(0, 0, 0, 0.3);

    /* Border radius */
    --impact-radius-sm: 4px;
    --impact-radius-md: 6px;
    --impact-radius-lg: 12px;

    /* Spacing */
    --impact-spacing-sm: 1rem;
    --impact-spacing-md: 1.5rem;
    --impact-spacing-lg: 2rem;
    --impact-spacing-xl: 3rem;
    --impact-spacing-2xl: 4rem;
    --impact-spacing-3xl: 5rem;

    /* Transitions */
    --impact-transition: 0.3s ease;

    /* Container widths */
    --impact-max-width: 1200px;
    --impact-content-width: 800px;
}
```

### Paso 3: Reemplazar gray-900 por contrast

```css
/* ANTES */
.impact-section__tile {
    color: var(--color-gray-900, #212121);
}

/* DESPUÉS */
.impact-section__tile {
    color: var(--impact-text);
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razones:**
1. Necesita variables locales para border-radius (3 tamaños)
2. Necesita escala de grises personalizada para tiles
3. Necesita múltiples valores de shadows (tiles, buttons, text)
4. Necesita spacing específico no disponible en theme.json
5. Necesita valores de transform y transitions personalizados
6. Background overlay con rgba específico

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.impact-section`, no hay conflictos globales.

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. Crear variables locales con prefijo `--impact-`
3. Reemplazar `--color-coral` por `--wp--preset--color--secondary`
4. Actualizar valores RGBA en shadows para usar secondary (#C66E65)
5. Reemplazar gray-900 por contrast donde sea apropiado
6. Testing en editor y frontend (verificar contraste de botón sobre fondo)
7. Commit: `refactor(impact-section): migrate to theme.json colors and local variables`

---

## Notas Adicionales

**Buenas prácticas encontradas:**
- ✅ Usa variables CSS con fallbacks
- ✅ Selectores bien scoped con prefijo `.impact-section`
- ✅ Responsive design con múltiples breakpoints
- ✅ Estados de hover bien implementados
- ✅ Accessibility: text shadows para legibilidad
- ✅ Grid system para tiles responsive
- ✅ Placeholder state para editor

**Características del bloque:**
- Hero section con imagen de fondo y overlay
- 3 tiles en grid (se colapsan a 1 columna en mobile)
- Botón CTA con efecto hover
- Text shadows para legibilidad sobre imagen
- Tiles con hover elevation effect

**Mejoras recomendadas:**
- Considerar usar `color-mix()` CSS para generar hover state automáticamente
- Evaluar si el text shadow es suficiente para contraste en todas las imágenes
- Considerar añadir aria-labels para los iconos de tiles
