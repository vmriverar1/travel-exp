# Auditoría: RelatedPostsGrid

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/related-posts-grid.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS** con fallbacks hardcodeados.

### Variables con fallbacks:

| Variable CSS | Fallback | Uso |
|--------------|----------|-----|
| `var(--color-gray-900, #212121)` | `#212121` | Títulos |
| `var(--color-gray-600, #757575)` | `#757575` | Subtítulos, excerpt |
| `var(--color-gray-500, #9E9E9E)` | `#9E9E9E` | Date |
| `var(--color-gray-200, #E0E0E0)` | `#E0E0E0` | Image placeholder |
| `var(--color-gray-100, #F5F5F5)` | `#F5F5F5` | Placeholder background |
| `var(--color-coral, #E78C85)` | `#E78C85` | Read more button |
| `var(--color-teal, #4A90A4)` | `#4A90A4` | Category, title hover, button |
| `var(--border-radius-md, 8px)` | `8px` | Card, placeholder |
| `var(--border-radius-sm, 4px)` | `4px` | Button, read more |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `white` | Card background | ✅ Sí | Usar `var(--wp--preset--color--base)` |
| `#E78C85` | Read more button (coral) | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#4A90A4` | Category, title hover, show more button (teal) | ❌ No existe | **PROBLEMA:** Teal no está |
| `#3d7a8a` | Show more button hover | ❌ No existe | Teal dark |
| `rgba(74, 144, 164, 0.1)` | Category background | N/A | Derivado de teal |
| `rgba(74, 144, 164, 0.3)` | Button shadow | N/A | Derivado de teal |
| `rgba(0, 0, 0, 0.7)` | Overlay gradient | N/A | Crear variable local |
| `rgba(0, 0, 0, 0.08)`, `rgba(0, 0, 0, 0.12)` | Card shadows | N/A | Variables locales |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `2rem`, `1.5rem`, `1.25rem`, `1.125rem`, `1rem`, `0.9375rem`, `0.875rem`, `0.75rem` | Varios tamaños |
| Spacing | `3rem`, `2rem`, `1.5rem`, `1rem`, `0.75rem`, `0.625rem`, `0.5rem` | Padding, margin, gap |
| Height | `240px`, `200px` | Image heights |
| Border-radius | `12px` (category) | Pill shape |
| Box-shadow | Múltiples | Card shadows |
| Transition | `all 0.3s ease`, `transform 0.5s ease`, `opacity 0.3s ease`, `color 0.3s ease` | Hover effects |
| Transform | `translateY(-4px)`, `translateY(-2px)`, `scale(1.05)` | Hover lift y zoom |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **paleta Coral/Teal** que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal oscuro)
- Secondary: #C66E65 (salmon/terracota)

**RelatedPostsGrid usa:**
- Coral: #E78C85 (read more button)
- Teal: #4A90A4 (category, title hover, main button)

### Hallazgos Positivos

✅ Ya usa sistema de variables CSS
✅ Usa variables de border-radius
✅ Buenos selectores con prefijo `.related-posts-grid`
✅ Responsive design completo
✅ Múltiples columnas (2, 3, 4)
✅ Overlay con "Read More" en hover
✅ Buenas transiciones y animaciones

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral y teal
2. **Opción B:** Mapear coral → Secondary, teal → Primary
3. **Opción C:** Mantener variables custom

**Recomendación:** Opción B - Alinear con theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.related-posts-grid__read-more {
  background: var(--color-coral, #E78C85);
  color: white;
}

.related-posts-grid__category {
  color: var(--color-teal, #4A90A4);
  background: rgba(74, 144, 164, 0.1);
}

.related-posts-grid__show-more {
  background: var(--color-teal, #4A90A4);
  color: white;
}

/* DESPUÉS */
.related-posts-grid__read-more {
  background: var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--base);
}

.related-posts-grid__category {
  color: var(--wp--preset--color--primary);
  background: color-mix(in srgb, var(--wp--preset--color--primary) 10%, transparent);
}

.related-posts-grid__show-more {
  background: var(--wp--preset--color--primary);
  color: var(--wp--preset--color--base);
}
```

### Variables locales necesarias:

```css
.related-posts-grid {
  /* Typography scale */
  --rpg-text-xs: 0.75rem;
  --rpg-text-sm: 0.875rem;
  --rpg-text-base: 0.9375rem;
  --rpg-text-lg: 1rem;
  --rpg-text-xl: 1.125rem;
  --rpg-text-2xl: 1.25rem;
  --rpg-text-3xl: 1.5rem;
  --rpg-text-4xl: 2rem;

  /* Spacing */
  --rpg-spacing-sm: 0.5rem;
  --rpg-spacing-md: 0.75rem;
  --rpg-spacing-lg: 1rem;
  --rpg-spacing-xl: 1.5rem;
  --rpg-spacing-2xl: 2rem;
  --rpg-spacing-3xl: 3rem;

  /* Image heights */
  --rpg-img-height-mobile: 200px;
  --rpg-img-height-desktop: 240px;

  /* Shadows */
  --rpg-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
  --rpg-shadow-md: 0 8px 24px rgba(0, 0, 0, 0.12);

  /* Overlays */
  --rpg-overlay-gradient: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.7) 100%);

  /* Transitions */
  --rpg-transition-fast: 0.3s ease;
  --rpg-transition-slow: 0.5s ease;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque necesita typography scale, spacing system, image heights, shadows y overlays. Tiene overlay interactivo con gradient y múltiples grid layouts.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.related-posts-grid`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir mapeo de colores coral/teal a theme.json**
2. Reemplazar `--color-coral` → `--wp--preset--color--secondary`
3. Reemplazar `--color-teal` → `--wp--preset--color--primary`
4. Usar `color-mix()` para backgrounds semi-transparentes
5. Crear variables locales para typography, spacing, heights, shadows
6. Testing en editor y frontend (verificar overlay hover)
7. Commit: `refactor(related-posts-grid): migrate to theme.json colors`
