# Auditoría: TeamCarousel

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/TeamCarousel/style.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS de theme.json** - Usa algunas CSS custom properties inline (`--card-height`, `data-columns`) pero no conecta con theme.json.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#fff`, `white`, `#ffffff` | `.tc-slide`, `.tc-info`, múltiples | ⚠️ Usar semantic | Usar `var(--wp--preset--color--base)` |
| `#2c2c2c` | `.tc-name` | ⚠️ Similar | Usar `var(--wp--preset--color--contrast)` (#111111) o crear variable |
| `#666` | `.tc-description` | ❌ No exacto | Usar `var(--wp--preset--color--gray)` (#666666) |
| `#888` | `.tc-position`, `.tc-achievements`, `.tc-dot:hover` | ❌ No existe | Usar gray con opacity |
| `#d46a3f` | `.tc-achievements li::before`, `.tc-dot.is-active` | ❌ No existe | **PROBLEMA:** Color naranja/terracota no está en theme.json |
| `#f0f0f0`, `#e0e0e0` | `.tc-profile-card .tc-image-wrapper` border, skeleton shimmer | ❌ No existen | Usar gray scale de theme.json |
| `#111` | `.tc-nav` | ⚠️ Similar | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#ccc` | `.tc-dot` | ❌ No existe | Usar gray con opacity |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `1.5rem`, `1.25rem`, `1rem`, `0.875rem`, `0.8125rem`, `1.125rem` | Names, positions, descriptions |
| Font-weight | `700`, `500` | Names, positions |
| Spacing | `40px`, `20px`, `2rem`, `1.5rem`, `1rem`, `0.5rem`, `0.375rem`, `10px` | Padding, margin, gap |
| Border-radius | `12px`, `50%`, `5px` | Cards, images, dots |
| Transition | `0.3s ease`, `0.5s ease-out`, `0.6s ease-out` | Hover effects, animations, skeleton |
| Box-shadow | `0 4px 12px rgba(0, 0, 0, 0.15)`, `0 6px 20px rgba(0, 0, 0, 0.2)`, `0 2px 4px rgba(0, 0, 0, 0.1)` | Navigation, cards |
| Dimensions | `150px`, `120px`, `400px`, `350px`, `320px`, `250px`, `48px`, `40px`, `28px`, `20px`, `10px`, `8px` | Images, navigation, dots |

---

## Análisis

### ✅ NO usa Coral/Purple problemático

Este bloque **NO usa** la paleta Coral/Purple (#E78C85, #311A42) que es problemática en otros bloques.

### ⚠️ Problema Principal

El bloque usa **color naranja/terracota (#d46a3f)** para:
- Bullets de achievements list
- Dots activos
- Outline en focus states

Este color **NO existe en theme.json**, pero es **similar** al Secondary (#C66E65 - salmon/terracota). Podría ser una buena aproximación.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota) ← Similar a #d46a3f

**TeamCarousel usa:**
- Accent: #d46a3f (naranja/terracota) ⚠️ Similar a Secondary
- Neutrals: #2c2c2c, #666, #888, grays

### Skeleton Loader

El bloque incluye un skeleton loader con shimmer animation:
- Usa grays (#f0f0f0, #e0e0e0) no definidos en theme.json
- Animation es custom pero funcional

### Dos Variaciones

El bloque tiene dos layouts:
1. **Profile Card:** Avatar circular + info centrada
2. **Full Body Portrait:** Imagen vertical + info abajo

Ambos comparten los mismos colores.

### Decisión Requerida

1. **Opción A:** Reemplazar #d46a3f → `var(--wp--preset--color--secondary)` (#C66E65)
2. **Opción B:** Crear variable local para accent color
3. **Opción C:** Agregar #d46a3f como "accent" en theme.json

**Recomendación:** Opción A - Usar Secondary de theme.json (muy similar visualmente)

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.tc-achievements li::before {
  color: #d46a3f;
}

.tc-dot.is-active {
  background: #d46a3f;
  border-color: #d46a3f;
}

.tc-nav:focus-visible,
.tc-dot:focus-visible {
  outline: 3px solid #d46a3f;
}

.tc-slide:focus {
  outline: 2px solid #d46a3f;
}

/* DESPUÉS */
.tc-achievements li::before {
  color: var(--wp--preset--color--secondary); /* #C66E65 */
}

.tc-dot.is-active {
  background: var(--wp--preset--color--secondary);
  border-color: var(--wp--preset--color--secondary);
}

.tc-nav:focus-visible,
.tc-dot:focus-visible {
  outline: 3px solid var(--wp--preset--color--secondary);
}

.tc-slide:focus {
  outline: 2px solid var(--wp--preset--color--secondary);
}
```

### Variables locales necesarias:

```css
.tc-carousel {
  /* Colors */
  --tc-accent: var(--wp--preset--color--secondary, #C66E65);
  --tc-text-primary: var(--wp--preset--color--contrast, #2c2c2c);
  --tc-text-secondary: var(--wp--preset--color--gray, #666);
  --tc-text-tertiary: #888; /* Crear en theme.json si se necesita */
  --tc-bg: var(--wp--preset--color--base, #fff);

  /* Spacing */
  --tc-gap-lg: var(--wp--preset--spacing--60, 2rem);
  --tc-gap-md: var(--wp--preset--spacing--50, 1.5rem);
  --tc-gap-sm: var(--wp--preset--spacing--40, 1rem);

  /* Transitions */
  --tc-transition: 0.3s ease;
  --tc-transition-slow: 0.5s ease-out;

  /* Dimensions */
  --tc-avatar-size: 150px;
  --tc-avatar-size-mobile: 120px;
  --tc-nav-size: 48px;
  --tc-nav-size-mobile: 40px;
  --tc-dot-size: 10px;
  --tc-dot-active-width: 28px;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:**
- Desktop grid layout con data-columns attribute
- Mobile scroll-snap carousel
- Skeleton loader con shimmer animation
- Navigation arrows
- Pagination dots con active state expansion
- Two layout variations (profile card vs full body)
- Hover effects
- Accessibility focus states

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.tc-*`, no hay conflictos globales.

---

## Próximos Pasos

1. ✅ Prioridad media - Solo un color (#d46a3f) necesita actualización
2. Reemplazar #d46a3f → `var(--wp--preset--color--secondary)` (#C66E65)
3. Reemplazar #2c2c2c → `var(--wp--preset--color--contrast)` donde sea apropiado
4. Reemplazar #666 → `var(--wp--preset--color--gray)` (#666666)
5. Implementar spacing scale de theme.json
6. Considerar agregar #888 (gray-400) a theme.json si se usa frecuentemente
7. Testing de skeleton loader
8. Testing de grid/carousel responsive behavior
9. Testing de keyboard navigation y focus states
10. Commit: `refactor(team-carousel): align accent color with theme.json`
