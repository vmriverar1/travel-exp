# Auditoría: PromoCard

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/promo-card.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS** con fallbacks hardcodeados.

### Variables con fallbacks:

| Variable CSS | Fallback | Uso |
|--------------|----------|-----|
| `var(--border-radius-lg, 12px)` | `12px` | Card border radius |
| `var(--border-radius-md, 6px)` | `6px` | Placeholder, button |
| `var(--border-radius-sm, 4px)` | `4px` | Button border radius |
| `var(--color-gray-200, #E0E0E0)` | `#E0E0E0` | Bordered card border |
| `var(--color-teal, #4A90A4)` | `#4A90A4` | Button secondary, border hover |
| `var(--color-coral, #E78C85)` | `#E78C85` | Button primary |
| `var(--color-gray-100, #F5F5F5)` | `#F5F5F5` | Placeholder background |
| `var(--color-gray-600, #757575)` | `#757575` | Placeholder text |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#E78C85` | Button primary (coral) | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#d97a74` | Button primary hover | ❌ No existe | **PROBLEMA:** Coral dark no está |
| `#4A90A4` | Button secondary (teal) | ❌ No existe | **PROBLEMA:** Teal no está en theme.json |
| `#3d7a8a` | Button secondary hover | ❌ No existe | **PROBLEMA:** Teal dark no está |
| `rgba(0, 0, 0, 0.05)` | Image border | N/A | Crear variable local |
| `rgba(231, 140, 133, 0.3)` | Button primary shadow | N/A | Derivado de coral |
| `rgba(74, 144, 164, 0.3)` | Button secondary shadow | N/A | Derivado de teal |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `1.5rem`, `1.25rem`, `1rem`, `0.9375rem` | Títulos y texto |
| Spacing | `2.5rem`, `2rem`, `1.5rem`, `1rem`, `0.5rem` | Padding, margin, gap |
| Box-shadow | `0 4px 16px rgba(0, 0, 0, 0.1)`, `0 8px 24px rgba(0, 0, 0, 0.15)` | Elevated card |
| Image sizes | `150px`, `100px`, `200px`, `120px`, `80px` | Image width/height |
| Transition | `all 0.3s ease` | Hover effects |
| Transform | `translateY(-4px)`, `translateY(-2px)` | Hover lift |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **paleta Coral/Teal** que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal oscuro)
- Secondary: #C66E65 (salmon/terracota)

**PromoCard usa:**
- Coral: #E78C85
- Teal: #4A90A4 (teal claro)

### Hallazgos Positivos

✅ Ya usa sistema de variables CSS para algunos valores
✅ Usa variables de border-radius
✅ Buenos selectores con prefijo `.promo-card`
✅ Responsive design
✅ Múltiples variantes (flat, elevated, bordered)

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral y teal
2. **Opción B:** Mapear coral → Secondary (#C66E65), teal → Primary (#17565C)
3. **Opción C:** Mantener variables custom

**Recomendación:** Opción B - Alinear con theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.promo-card__button--primary {
  background: var(--color-coral, #E78C85);
  color: white;
}

.promo-card__button--primary:hover {
  background: #d97a74;
}

.promo-card__button--secondary {
  background: var(--color-teal, #4A90A4);
  color: white;
}

/* DESPUÉS */
.promo-card__button--primary {
  background: var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--base);
}

.promo-card__button--primary:hover {
  background: color-mix(in srgb, var(--wp--preset--color--secondary) 85%, black);
}

.promo-card__button--secondary {
  background: var(--wp--preset--color--primary);
  color: var(--wp--preset--color--base);
}
```

### Variables locales necesarias:

```css
.promo-card {
  /* Spacing */
  --promo-spacing-sm: 0.5rem;
  --promo-spacing-md: 1rem;
  --promo-spacing-lg: 1.5rem;
  --promo-spacing-xl: 2rem;
  --promo-spacing-2xl: 2.5rem;

  /* Border radius (complementar theme.json) */
  --promo-radius-sm: var(--border-radius-sm, 4px);
  --promo-radius-md: var(--border-radius-md, 6px);
  --promo-radius-lg: var(--border-radius-lg, 12px);

  /* Image sizes */
  --promo-img-sm: 100px;
  --promo-img-md: 150px;
  --promo-img-lg: 200px;

  /* Effects */
  --promo-transition: all 0.3s ease;
  --promo-lift-sm: -2px;
  --promo-lift-md: -4px;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque necesita variables locales para spacing, image sizes, y effects. Tiene múltiples variantes de estilo (flat, elevated, bordered).

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.promo-card`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir mapeo de colores coral/teal a theme.json**
2. Reemplazar `--color-coral` → `--wp--preset--color--secondary`
3. Reemplazar `--color-teal` → `--wp--preset--color--primary`
4. Crear variables locales para spacing, sizes y effects
5. Testing en editor y frontend (verificar todas las variantes)
6. Commit: `refactor(promo-card): migrate to theme.json color system`
