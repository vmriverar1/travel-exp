# Auditoría: TrustBadges

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/trust-badges.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS** con fallbacks hardcodeados.

### Variables con fallbacks:

| Variable CSS | Fallback | Uso |
|--------------|----------|-----|
| `var(--color-gray-900, #212121)` | `#212121` | Títulos |
| `var(--color-gray-600, #757575)` | `#757575` | Description, placeholder |
| `var(--color-gray-100, #F5F5F5)` | `#F5F5F5` | Placeholder background |
| `var(--border-radius-md, 6px)` | `6px` | Card, placeholder |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `white` | Card background (grid style) | ✅ Sí | Usar `var(--wp--preset--color--base)` |
| `rgba(0, 0, 0, 0.06)` | Card box-shadow | N/A | Crear variable local |

**✅ BUENA PRÁCTICA:** Muy pocos colores hardcodeados.

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `1.5rem`, `1.25rem`, `1rem`, `0.875rem`, `0.8125rem` | Varios tamaños de texto |
| Spacing | `2rem`, `1.5rem`, `1rem`, `0.75rem`, `0.5rem` | Padding, margin, gap |
| Icon sizes | `64px`, `48px`, `32px` | SVG/image dimensions |
| Min-width | `200px` | Grid layout |
| Max-width | `400px` | Vertical layout |
| Box-shadow | `0 2px 8px rgba(0, 0, 0, 0.06)` | Card shadow |

---

## Análisis

### ✅ Bloque Simple y Bien Estructurado

Este bloque es **simple y bien implementado**:

**Características positivas:**
- ✅ Usa variables CSS para casi todos los colores
- ✅ Sistema de grises coherente
- ✅ Muy pocos colores hardcodeados
- ✅ Buenos selectores con prefijo `.trust-badges`
- ✅ Múltiples layouts (horizontal, grid, vertical)
- ✅ Múltiples tamaños (small, medium, large)
- ✅ Múltiples alineaciones (left, center, right)
- ✅ Responsive design

**PROBLEMA MENOR:**
- Sistema de grises custom (gray-100, gray-600, gray-900) que no mapea directamente a theme.json

### Decisión Requerida

**Opción A:** Mapear grises a theme.json (aproximando valores)
**Opción B:** Agregar escala de grises a theme.json
**Opción C:** Mantener variables custom

**Recomendación:** Opción A - Mapear a theme.json aproximando valores

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* MAPEO A THEME.JSON */

/* ANTES */
.trust-badges__title {
  color: var(--color-gray-900, #212121);
}

.trust-badges__item-title {
  color: var(--color-gray-900, #212121);
}

.trust-badges__item-description {
  color: var(--color-gray-600, #757575);
}

/* DESPUÉS */
.trust-badges__title {
  color: var(--wp--preset--color--contrast);
}

.trust-badges__item-title {
  color: var(--wp--preset--color--contrast);
}

.trust-badges__item-description {
  color: var(--wp--preset--color--gray);
}

/* Card background */
.trust-badges--grid .trust-badges__item {
  background: var(--wp--preset--color--base);
}
```

### Variables locales necesarias:

```css
.trust-badges {
  /* Typography scale */
  --tb-text-xs: 0.8125rem;
  --tb-text-sm: 0.875rem;
  --tb-text-base: 1rem;
  --tb-text-lg: 1.125rem;
  --tb-text-xl: 1.25rem;
  --tb-text-2xl: 1.5rem;

  /* Spacing */
  --tb-spacing-sm: 0.5rem;
  --tb-spacing-md: 1rem;
  --tb-spacing-lg: 1.5rem;
  --tb-spacing-xl: 2rem;

  /* Icon sizes */
  --tb-icon-sm: 32px;
  --tb-icon-md: 48px;
  --tb-icon-lg: 64px;

  /* Layout */
  --tb-max-width: 400px;
  --tb-min-width: 200px;

  /* Shadow */
  --tb-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}
```

---

## CSS Personalizado Necesario: **MÍNIMO**

**Razón:** Bloque simple con layouts flexibles. Solo necesita variables para typography, spacing, icon sizes y layout constraints.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.trust-badges`, no hay conflictos globales.

---

## Próximos Pasos

1. ⚡ **PRIORIDAD BAJA** - Bloque funciona bien
2. Mapear grises a theme.json (gray-900 → contrast, gray-600 → gray)
3. Crear variables locales para typography, spacing e icon sizes
4. Testing en editor y frontend (verificar todas las variantes de layout)
5. Commit: `refactor(trust-badges): migrate to theme.json colors`

---

## Notas Adicionales

Bloque bien estructurado con múltiples variantes. Buen ejemplo de flexibilidad sin complejidad excesiva.
