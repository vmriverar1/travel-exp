# Auditoría: ProductMetadata

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/product-metadata.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS** con fallbacks hardcodeados.

### Variables con fallbacks:

| Variable CSS | Fallback | Uso |
|--------------|----------|-----|
| `var(--color-gray-900, #212121)` | `#212121` | Títulos, textos oscuros |
| `var(--color-gray-700, #616161)` | `#616161` | Textos secundarios |
| `var(--color-gray-600, #757575)` | `#757575` | Reviews count, meta items |
| `var(--color-coral, #E78C85)` | `#E78C85` | Primary variant |
| `var(--color-coral-light, #FFF0EF)` | `#FFF0EF` | Separator primary |
| `var(--color-purple, #311A42)` | `#311A42` | Secondary variant |
| `var(--color-purple-light, #4A2B5E)` | `#4A2B5E` | Secondary icons/separator |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#00AF87` | `.product-metadata__stars` (print only) | ❌ No existe | TripAdvisor green - OK hardcodeado |
| `#E78C85` | Coral en fallbacks | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#311A42` | Purple en fallbacks | ❌ No existe | **PROBLEMA:** Color purple no está en theme.json |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `2.5rem`, `2rem`, `1.75rem`, `3.75rem`, `0.875rem`, etc. | Varios tamaños de texto |
| Spacing | `1rem`, `1.5rem`, `0.75rem`, etc. | Padding, margin, gap |
| Border-radius | `4px` | Focus outline |
| Height | `24px`, `60px` | Logo y SVG sizes |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **paleta Coral/Purple** que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**ProductMetadata usa:**
- Coral: #E78C85, #FFF0EF
- Purple: #311A42, #4A2B5E

### Hallazgos Positivos

✅ Ya usa sistema de variables CSS completo
✅ Incluye variantes de color (primary, secondary)
✅ Buen diseño responsive
✅ Incluye print styles
✅ Accesibilidad con focus-visible
✅ High contrast mode support

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral y purple
2. **Opción B:** Cambiar a Primary/Secondary de theme.json
3. **Opción C:** Mantener variables custom documentadas

**Recomendación:** Opción B - Alinear con theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.product-metadata__meta-line--primary {
  color: var(--color-coral, #E78C85);
}

.product-metadata__meta-line--secondary {
  color: var(--color-purple, #311A42);
}

/* DESPUÉS */
.product-metadata__meta-line--primary {
  color: var(--wp--preset--color--primary); /* #17565C teal */
}

.product-metadata__meta-line--secondary {
  color: var(--wp--preset--color--secondary); /* #C66E65 salmon */
}
```

### Variables locales necesarias:

```css
.product-metadata {
  /* Typography scale */
  --meta-text-xs: 0.6875rem;
  --meta-text-sm: 0.75rem;
  --meta-text-base: 0.875rem;
  --meta-text-lg: 0.9375rem;
  --meta-text-xl: 1rem;
  --meta-text-2xl: 1.75rem;
  --meta-text-3xl: 2rem;
  --meta-text-4xl: 2.5rem;
  --meta-text-5xl: 3.75rem;

  /* Spacing */
  --meta-spacing-xs: 0.25rem;
  --meta-spacing-sm: 0.5rem;
  --meta-spacing-md: 0.75rem;
  --meta-spacing-lg: 1rem;
  --meta-spacing-xl: 1.5rem;
  --meta-spacing-2xl: 3rem;

  /* Heights */
  --meta-logo-height: 24px;
  --meta-icon-size: 60px;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque necesita typography scale y spacing system que theme.json no proporciona completamente. También tiene diseño especial para duration con iconos grandes.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.product-metadata__`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral/purple vs theme.json)
2. Mapear `--color-coral` → `--wp--preset--color--primary`
3. Mapear `--color-purple` → `--wp--preset--color--secondary`
4. Mapear grises a theme.json equivalentes
5. Crear variables locales para typography scale y spacing
6. Testing en editor y frontend
7. Commit: `refactor(product-metadata): migrate to theme.json color system`
