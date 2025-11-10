# Auditoría: QuickFacts

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/quick-facts.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS** con fallbacks hardcodeados.

### Variables con fallbacks:

| Variable CSS | Fallback | Uso |
|--------------|----------|-----|
| `var(--color-gray-900, #212121)` | `#212121` | Títulos |
| `var(--color-gray-600, #757575)` | `#757575` | Labels, placeholder |
| `var(--color-gray-200, #E0E0E0)` | `#E0E0E0` | Bordered items |
| `var(--color-gray-100, #F5F5F5)` | `#F5F5F5` | Placeholder background |
| `var(--color-teal, #4A90A4)` | `#4A90A4` | Border hover |
| `var(--border-radius-md, 6px)` | `6px` | Card, bordered, placeholder |
| `var(--border-radius-sm, 4px)` | `4px` | Border radius small |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `white` | `.quick-facts__item` (card style) | ✅ Sí | Usar `var(--wp--preset--color--base)` |
| `#4A90A4` | Teal border hover | ❌ No existe | **PROBLEMA:** Teal no está en theme.json |
| `rgba(0, 0, 0, 0.06)` | Card shadow | N/A | Crear variable local |
| `rgba(0, 0, 0, 0.1)` | Card hover shadow | N/A | Crear variable local |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `1.75rem`, `1.5rem`, `1.25rem`, `1.125rem`, `1rem`, `0.875rem`, `0.8125rem`, `0.75rem` | Varios tamaños |
| Spacing | `2rem`, `1.5rem`, `1rem`, `0.75rem`, `0.5rem`, `0.25rem` | Padding, margin, gap |
| Icon sizes | `48px`, `32px`, `24px` | SVG dimensions |
| Box-shadow | `0 2px 8px`, `0 4px 12px` | Card shadows |
| Transition | `all 0.3s ease`, `box-shadow 0.3s ease`, `border-color 0.3s ease` | Hover effects |

---

## Análisis

### ⚠️ Problema Menor

El bloque usa **teal (#4A90A4)** que NO existe en theme.json.

**theme.json tiene:**
- Primary: #17565C (teal oscuro)
- Secondary: #C66E65 (salmon)

**QuickFacts usa:**
- Teal: #4A90A4 (solo para border hover)

### Hallazgos Positivos

✅ Ya usa sistema de variables CSS
✅ Usa variables de border-radius
✅ Buenos selectores con prefijo `.quick-facts`
✅ Responsive design completo
✅ Múltiples layouts (list, grid-2, grid-3, grid-4)
✅ Múltiples estilos (card, bordered)
✅ Múltiples tamaños (small, medium, large)

### Decisión Requerida

1. **Opción A:** Reemplazar teal por Primary de theme.json (#17565C)
2. **Opción B:** Mantener teal como variable custom
3. **Opción C:** Eliminar color de border hover

**Recomendación:** Opción A - Usar Primary de theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.quick-facts--bordered .quick-facts__item:hover {
  border-color: var(--color-teal, #4A90A4);
}

/* DESPUÉS */
.quick-facts--bordered .quick-facts__item:hover {
  border-color: var(--wp--preset--color--primary);
}
```

### Variables locales necesarias:

```css
.quick-facts {
  /* Typography scale */
  --qf-text-xs: 0.75rem;
  --qf-text-sm: 0.8125rem;
  --qf-text-base: 0.875rem;
  --qf-text-lg: 1rem;
  --qf-text-xl: 1.125rem;
  --qf-text-2xl: 1.5rem;
  --qf-text-3xl: 1.75rem;

  /* Spacing */
  --qf-spacing-xs: 0.25rem;
  --qf-spacing-sm: 0.5rem;
  --qf-spacing-md: 1rem;
  --qf-spacing-lg: 1.5rem;
  --qf-spacing-xl: 2rem;

  /* Icon sizes */
  --qf-icon-sm: 24px;
  --qf-icon-md: 32px;
  --qf-icon-lg: 48px;

  /* Shadows */
  --qf-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
  --qf-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);

  /* Transitions */
  --qf-transition: all 0.3s ease;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque necesita typography scale, spacing system, icon sizes y shadows. Tiene múltiples variantes de layout y estilo.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.quick-facts`, no hay conflictos globales.

---

## Próximos Pasos

1. Reemplazar `--color-teal` → `--wp--preset--color--primary`
2. Reemplazar grises por equivalentes de theme.json
3. Crear variables locales para typography, spacing, icons y shadows
4. Testing en editor y frontend (verificar todos los layouts)
5. Commit: `refactor(quick-facts): migrate to theme.json variables`
