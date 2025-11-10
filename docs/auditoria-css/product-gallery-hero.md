# Auditoría: ProductGalleryHero

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/product-gallery-hero.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA algunas variables CSS** con fallbacks hardcodeados.

### Variables con fallbacks:

| Variable CSS | Fallback | Uso |
|--------------|----------|-----|
| `var(--color-gray-900, #212121)` | `#212121` | Background |
| `var(--color-gray-100, #F5F5F5)` | `#F5F5F5` | Placeholder bg |
| `var(--color-gray-600, #757575)` | `#757575` | Placeholder text |
| `var(--color-gray-200, #EEEEEE)` | `#EEEEEE` | Loading state |
| `var(--color-gray-400, #BDBDBD)` | `#BDBDBD` | Loading spinner |
| `var(--color-coral, #E78C85)` | `#E78C85` | Activity dots, buttons, spinner |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `white` | Varios elementos | ✅ Sí | Usar `var(--wp--preset--color--base)` |
| `#E78C85` | `.gallery-hero__view-button`, `.activity-dot.active` | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#d97b75` | Button hover | ❌ No existe | **PROBLEMA:** Coral dark no está en theme.json |
| `rgba(0, 0, 0, ...)` | Overlays, shadows | N/A | Crear variables locales |
| `rgba(255, 255, 255, ...)` | Pagination bullets | N/A | Crear variables locales |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Height | `400px`, `500px`, `600px` | Swiper heights responsive |
| Font-size | `0.875rem`, `0.75rem` | Badge y label text |
| Spacing | Varios rem values | Padding, margin, gap |
| Border-radius | `2rem`, `2px`, `50%` | Varios elementos |
| Box-shadow | Multiple | Badges, buttons |
| Transform | `rotate(-45deg)`, `scale()` | Discount badge, animations |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **variables CSS propias** (`--color-coral`, `--color-gray-*`) que **NO coinciden con theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)
- Base: #FAFAFA (white)
- Contrast: #111111 (dark)

**ProductGalleryHero usa:**
- `--color-coral`: #E78C85
- `--color-gray-*`: Sistema de grises custom

### Hallazgos Positivos

✅ Ya usa sistema de variables CSS (parcialmente)
✅ Buenos selectores específicos con prefijo
✅ Buen diseño responsive
✅ Incluye estados de loading y print styles
✅ Accesibilidad con focus-visible

### Decisión Requerida

1. **Opción A:** Mapear variables custom a theme.json
2. **Opción B:** Mantener variables custom pero documentarlas
3. **Opción C:** Reemplazar completamente por theme.json

**Recomendación:** Opción A - Mapear a theme.json manteniendo compatibilidad

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.product-gallery-hero {
  background: var(--color-gray-900, #212121);
}

.gallery-hero__view-button {
  background: #E78C85;
}

.gallery-hero__view-button:hover {
  background: #d97b75;
}

/* DESPUÉS */
.product-gallery-hero {
  background: var(--wp--preset--color--contrast);
}

.gallery-hero__view-button {
  background: var(--wp--preset--color--secondary);
}

.gallery-hero__view-button:hover {
  background: color-mix(in srgb, var(--wp--preset--color--secondary) 80%, black);
}
```

### Variables locales necesarias:

```css
.product-gallery-hero {
  /* Heights */
  --gallery-height-mobile: 400px;
  --gallery-height-tablet: 500px;
  --gallery-height-desktop: 600px;

  /* Spacing */
  --gallery-spacing-sm: 0.5rem;
  --gallery-spacing-md: 1rem;
  --gallery-spacing-lg: 1.5rem;
  --gallery-spacing-xl: 2rem;

  /* Border radius */
  --gallery-radius-sm: 2px;
  --gallery-radius-full: 2rem;

  /* Overlays */
  --gallery-overlay-dark: rgba(0, 0, 0, 0.3);
  --gallery-overlay-darker: rgba(0, 0, 0, 0.7);
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque complejo con Swiper, overlays, badges, animaciones. Necesita variables locales para valores que theme.json no soporta (heights, overlays, transforms).

---

## Selectores Específicos: ✅ OK

Todos los selectores usan prefijos `.product-gallery-hero`, `.gallery-hero__`, `.activity-indicator__`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir mapeo de variables custom a theme.json**
2. Reemplazar `--color-coral` por `--wp--preset--color--secondary`
3. Reemplazar sistema `--color-gray-*` por equivalentes theme.json
4. Crear variables locales para heights, overlays y spacing
5. Testing en editor y frontend (verificar Swiper)
6. Commit: `refactor(product-gallery-hero): migrate to theme.json variables`
