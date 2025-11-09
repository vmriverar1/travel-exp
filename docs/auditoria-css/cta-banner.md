# Auditoría: CTA Banner

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/cta-banner.css`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS con fallbacks**, pero las variables principales NO existen en theme.json.

### Variables encontradas:

| Variable CSS | Fallback | Líneas | ¿Existe en theme.json? | Variable theme.json equivalente |
|--------------|----------|---------|------------------------|--------------------------------|
| `--color-purple` | `#311A42` | 108, 124 | ❌ No existe | **PROBLEMA:** Color purple no está en theme.json, usar `--wp--preset--color--primary` (#17565C) |
| `--color-gray-900` | `#212121` | 75 | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `--border-radius-md` | `6px` | 100 | ❌ No existe | Crear variable local |

### Valores hardcodeados (sin variables):

| Tipo | Valor | Uso | Línea |
|------|-------|-----|-------|
| Color | `white` | Text, button background/border | 71, 107, 118, 119, 123 |
| Color | `rgba(0, 0, 0, 0.5)` | Overlay background | 20 |
| Color | `rgba(0, 0, 0, 0.2)` | Hover shadow | 113 |
| Color | `transparent` | Border color | 103, 117 |
| Spacing | `5rem`, `4rem`, `3rem`, `2rem`, `1.5rem`, `1rem`, `0.5rem` | Padding, margin, gap | Multiple |
| Max-width | `1200px`, `800px`, `50%` | Content constraints | 27, 33, 129 |
| Font-size | `2.5rem`, `2rem`, `1.75rem`, `1.125rem`, `1rem` | Text sizes | Multiple |
| Opacity | `0.9`, `0.95` | Text transparency | 52, 66 |
| Transition | `0.3s ease` | Button transitions | 102 |
| Box-shadow | `0 8px 16px` | Hover shadow | 113 |
| Letter-spacing | `0.1em` | Subtitle | 50 |
| Border | `2px solid` | Button border | 103 |
| Transform | `translateY(-2px)` | Hover effect | 112 |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa el **color Purple** (#311A42) que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**CTA Banner usa:**
- Purple: #311A42

### Problemas Adicionales

1. **Color purple obsoleto:** Usa purple como color de texto principal en botones
2. **Border radius:** Usa `--border-radius-md` que no está definido globalmente
3. **Gray-900:** Usa una versión diferente de contrast (#212121 vs #111111)
4. **Diseño basado en overlay:** Usa overlay oscuro pero no define variable para el color

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir purple (#311A42)
2. **Opción B:** Cambiar banner para usar Primary (#17565C) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque para purple

**Recomendación:** Opción B - Usar `--wp--preset--color--primary` (teal)
- El teal es más moderno y consistente con el tema
- Elimina dependencia de colores legacy
- Mejora consistencia visual con otros bloques

---

## Plan de Refactorización

### Paso 1: Reemplazar purple por primary

```css
/* ANTES */
.cta-banner__button--primary {
    background: white;
    color: var(--color-purple, #311A42);
}

.cta-banner__button--secondary:hover {
    background: white;
    color: var(--color-purple, #311A42);
}

/* DESPUÉS */
.cta-banner__button--primary {
    background: white;
    color: var(--wp--preset--color--primary); /* #17565C teal */
}

.cta-banner__button--secondary:hover {
    background: white;
    color: var(--wp--preset--color--primary);
}
```

### Paso 2: Reemplazar gray-900 por contrast

```css
/* ANTES */
.cta-banner--dark {
    color: var(--color-gray-900, #212121);
}

/* DESPUÉS */
.cta-banner--dark {
    color: var(--wp--preset--color--contrast); /* #111111 */
}
```

### Paso 3: Variables locales necesarias

```css
.cta-banner {
    /* Border radius */
    --ctab-radius-md: 6px;

    /* Spacing */
    --ctab-spacing-xs: 0.5rem;
    --ctab-spacing-sm: 1rem;
    --ctab-spacing-md: 1.5rem;
    --ctab-spacing-lg: 2rem;
    --ctab-spacing-xl: 3rem;
    --ctab-spacing-2xl: 4rem;
    --ctab-spacing-3xl: 5rem;

    /* Content widths */
    --ctab-max-width: 1200px;
    --ctab-content-width: 800px;
    --ctab-split-width: 50%;

    /* Overlay */
    --ctab-overlay-opacity: 0.5;
    --ctab-overlay-color: rgba(0, 0, 0, var(--ctab-overlay-opacity));

    /* Effects */
    --ctab-transition: 0.3s ease;
    --ctab-text-opacity-high: 0.95;
    --ctab-text-opacity-mid: 0.9;
}
```

### Paso 4: Usar variables locales

```css
/* Overlay con variable */
.cta-banner--overlay::before {
    background: var(--ctab-overlay-color);
}

/* Border radius */
.cta-banner__button {
    border-radius: var(--ctab-radius-md);
}

/* Max widths */
.cta-banner__inner {
    max-width: var(--ctab-max-width);
}

.cta-banner__content {
    max-width: var(--ctab-content-width);
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razones:**
1. Necesita variables locales para border-radius
2. Necesita valores de spacing específicos no disponibles en theme.json
3. Necesita variables para opacidades de overlay y texto
4. Necesita valores de max-width para layout
5. Necesita transiciones personalizadas

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.cta-banner`, no hay conflictos globales.

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. Crear variables locales con prefijo `--ctab-`
3. Reemplazar `--color-purple` por `--wp--preset--color--primary` (teal)
4. Reemplazar `--color-gray-900` por `--wp--preset--color--contrast`
5. Implementar variables para overlay, spacing, y widths
6. Testing en editor y frontend (especialmente verificar contraste con teal)
7. Commit: `refactor(cta-banner): migrate to theme.json colors and local variables`

---

## Notas Adicionales

**Buenas prácticas encontradas:**
- ✅ Usa variables CSS con fallbacks
- ✅ Selectores bien scoped con prefijo `.cta-banner`
- ✅ Responsive design con múltiples breakpoints
- ✅ Modificadores BEM para variantes (--light, --dark, --split)
- ✅ Flexbox para layout flexible de botones
- ✅ Transitions suaves
- ✅ Sistema de alineación (left, center, right)

**Consideraciones de diseño:**
- ⚠️ Al cambiar de purple (#311A42) a teal (#17565C), verificar contraste suficiente sobre blanco
- ⚠️ El teal es más claro que purple, puede necesitar ajuste en el peso de fuente
- ✅ El overlay oscuro (0.5 opacity) funciona bien con ambos esquemas de color

**Mejoras recomendadas:**
- Considerar usar CSS custom properties para hacer el overlay configurable desde el editor
- Evaluar si el transform translateY necesita variable para consistencia con otros bloques
