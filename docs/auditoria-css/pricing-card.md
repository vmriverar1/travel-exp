# Auditoría: PricingCard

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/pricing-card.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#F9F9F9` | `.pricing-card` background | ❌ No exacto | Usar `var(--wp--preset--color--base)` (#FAFAFA) |
| `#ffffff` | `.pricing-card__white-box` | ✅ Sí | Usar `var(--wp--preset--color--base)` |
| `#202C2E` | Textos duration/price | ❌ No existe | **PROBLEMA:** Color dark no está en theme.json |
| `#E78C85` | `.pricing-card__cta-button` | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#d97a73` | Button hover | ❌ No existe | **PROBLEMA:** Color coral dark no está en theme.json |
| `#757575` | `.pricing-card__per-person-text` | ✅ Sí | Usar `var(--wp--preset--color--gray)` (#666666 similar) |
| `#424242` | `.pricing-card__private-service` | ❌ No exacto | Similar a contrast |
| `#212121` | Títulos y labels | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `3rem`, `1.5rem`, `18px`, `16px`, `14px`, `11px`, `10px` | Varios tamaños de texto |
| Spacing | `2rem`, `1.5rem`, `1rem`, `0.5rem`, `0.25rem` | Padding, margin, gap |
| Border-radius | `12px`, `50px`, `6px` | Card, button, y box radius |
| Transition | `0.3s ease`, `all 0.3s ease` | Hover effects |
| Box-shadow | `0 4px 12px rgba(231, 140, 133, 0.3)` | Button hover |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa la **paleta Coral (#E78C85)** que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**PricingCard usa:**
- Primary: #E78C85 (coral)
- Dark: #202C2E (dark teal/navy)

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85)
2. **Opción B:** Cambiar pricing-card para usar Secondary (#C66E65) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque para coral

**Recomendación:** Opción B - Alinear con theme.json usando Secondary

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.pricing-card {
  background: #F9F9F9;
}

.pricing-card__cta-button {
  background: #E78C85;
  color: #ffffff;
}

.pricing-card__cta-button:hover {
  background: #d97a73;
}

/* DESPUÉS */
.pricing-card {
  background: var(--wp--preset--color--base);
}

.pricing-card__cta-button {
  background: var(--wp--preset--color--secondary); /* #C66E65 */
  color: var(--wp--preset--color--base);
}

.pricing-card__cta-button:hover {
  background: #b35e56; /* Darker secondary */
}
```

### Variables locales necesarias:

```css
.pricing-card {
  /* Spacing local */
  --pricing-spacing-xs: 0.25rem;
  --pricing-spacing-sm: 0.5rem;
  --pricing-spacing-md: 1rem;
  --pricing-spacing-lg: 1.5rem;
  --pricing-spacing-xl: 2rem;

  /* Border radius */
  --pricing-radius-sm: 6px;
  --pricing-radius-md: 12px;
  --pricing-radius-full: 50px;

  /* Transitions */
  --pricing-transition: all 0.3s ease;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Necesita variables locales para spacing, border-radius y transitions que theme.json no soporta. También tiene un diseño complejo con grid layout.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.pricing-card__`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral vs theme.json secondary)
2. Reemplazar colores hardcodeados por variables de theme.json
3. Crear variables locales para spacing, border-radius y transitions
4. Testing en editor y frontend
5. Commit: `refactor(pricing-card): align with theme.json color palette`
