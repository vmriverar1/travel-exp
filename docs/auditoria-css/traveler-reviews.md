# Auditoría: TravelerReviews

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/traveler-reviews.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS** con fallbacks hardcodeados.

### Variables con fallbacks:

| Variable CSS | Fallback | Uso |
|--------------|----------|-----|
| `var(--color-gray-900, #212121)` | `#212121` | Títulos, nombres |
| `var(--color-gray-700, #616161)` | `#616161` | Content text |
| `var(--color-gray-600, #757575)` | `#757575` | Subtítulos, origin |
| `var(--color-gray-500, #9E9E9E)` | `#9E9E9E` | Date, traveler type |
| `var(--color-gray-300, #E0E0E0)` | `#E0E0E0` | Filter button border |
| `var(--color-gray-200, #EEEEEE)` | `#EEEEEE` | Card border |
| `var(--color-gray-100, #F5F5F5)` | `#F5F5F5` | Avatar background |
| `var(--color-gray-50, #FAFAFA)` | `#FAFAFA` | Placeholder background |
| `var(--color-coral, #E78C85)` | `#E78C85` | Filter active, show more button |
| `var(--border-radius-lg, 8px)` | `8px` | Card, placeholder |
| `var(--border-radius-full, 50px)` | `50px` | Filter buttons |
| `var(--border-radius-md, 6px)` | `6px` | Show more button |
| `var(--border-radius-sm, 4px)` | `4px` | Platform badges |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `white` | Card background, filter bg | ✅ Sí | Usar `var(--wp--preset--color--base)` |
| `#E78C85` | Coral - filters, button | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#34E0A1` | TripAdvisor badge | ❌ No existe | Brand color - OK hardcodeado |
| `#4285F4` | Google badge | ❌ No existe | Brand color - OK hardcodeado |
| `#1877F2` | Facebook badge | ❌ No existe | Brand color - OK hardcodeado |
| `rgba(0, 0, 0, 0.1)` | Card hover shadow | N/A | Crear variable local |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `2.5rem`, `1.75rem`, `1.125rem`, `1rem`, `0.9375rem`, `0.875rem`, `0.8125rem`, `0.75rem` | Varios tamaños |
| Spacing | `4rem`, `3rem`, `2rem`, `1.5rem`, `1rem`, `0.75rem`, `0.5rem`, `0.375rem`, `0.25rem`, `4px`, `2px` | Padding, margin, gap |
| Avatar size | `48px` | Avatar dimensions |
| Grid columns | `var(--grid-columns, 3)` | Dynamic grid |
| Box-shadow | `0 8px 16px rgba(0, 0, 0, 0.1)` | Card hover |
| Transition | `all 0.3s ease` | Hover effects |
| Transform | `translateY(-4px)` | Card hover lift |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **color coral (#E78C85)** que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**TravelerReviews usa:**
- Coral: #E78C85 (filters, button)
- Brand colors: TripAdvisor green, Google blue, Facebook blue (OK mantener)

### Hallazgos Positivos

✅ Ya usa sistema de variables CSS completo
✅ Usa variables de border-radius
✅ Buenos selectores con prefijo `.traveler-reviews`
✅ Responsive design completo
✅ Sistema de filtros interactivo
✅ Grid dinámico con CSS Grid
✅ Pagination/show more
✅ Include print styles
✅ Platform badges con brand colors correctos

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral
2. **Opción B:** Reemplazar coral por Secondary de theme.json
3. **Opción C:** Mantener coral como variable custom

**Recomendación:** Opción B - Usar Secondary de theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.traveler-reviews__filter-button:hover {
  border-color: var(--color-coral, #E78C85);
  color: var(--color-coral, #E78C85);
}

.traveler-reviews__filter-button.active {
  background: var(--color-coral, #E78C85);
  border-color: var(--color-coral, #E78C85);
  color: white;
}

.traveler-reviews__show-more {
  border: 2px solid var(--color-coral, #E78C85);
  color: var(--color-coral, #E78C85);
}

/* DESPUÉS */
.traveler-reviews__filter-button:hover {
  border-color: var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--secondary);
}

.traveler-reviews__filter-button.active {
  background: var(--wp--preset--color--secondary);
  border-color: var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--base);
}

.traveler-reviews__show-more {
  border: 2px solid var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--secondary);
}
```

### Variables locales necesarias:

```css
.traveler-reviews {
  /* Typography scale */
  --tr-text-xs: 0.75rem;
  --tr-text-sm: 0.8125rem;
  --tr-text-base: 0.875rem;
  --tr-text-md: 0.9375rem;
  --tr-text-lg: 1rem;
  --tr-text-xl: 1.125rem;
  --tr-text-2xl: 1.75rem;
  --tr-text-3xl: 2.5rem;

  /* Spacing */
  --tr-spacing-2xs: 0.25rem;
  --tr-spacing-xs: 0.375rem;
  --tr-spacing-sm: 0.5rem;
  --tr-spacing-md: 0.75rem;
  --tr-spacing-lg: 1rem;
  --tr-spacing-xl: 1.5rem;
  --tr-spacing-2xl: 2rem;
  --tr-spacing-3xl: 3rem;
  --tr-spacing-4xl: 4rem;

  /* Avatar */
  --tr-avatar-size: 48px;

  /* Shadows */
  --tr-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);

  /* Transitions */
  --tr-transition: all 0.3s ease;

  /* Grid */
  --tr-grid-columns: 3;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque complejo con sistema de filtros, grid dinámico, pagination, y platform badges. Necesita variables para typography, spacing, y layout.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.traveler-reviews`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir sobre color coral** (mantener vs theme.json)
2. Reemplazar `--color-coral` → `--wp--preset--color--secondary`
3. Mantener brand colors para platform badges (TripAdvisor, Google, Facebook)
4. Mapear grises a theme.json
5. Crear variables locales para typography, spacing y layout
6. Testing en editor y frontend (verificar filtros y pagination)
7. Commit: `refactor(traveler-reviews): migrate to theme.json colors`
