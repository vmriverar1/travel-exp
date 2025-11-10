# Auditoría: DealInfoCard

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/deal-info-card.css`
**Categoría:** Bloque ACF - Deal
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#fff` | Card background, button text | ✅ Sí | Usar `var(--wp--preset--color--base)` |
| `#2563eb` | Blue primary (border, discount, price, button, icon) | ❌ No existe | **NUEVO COLOR:** Blue para deals |
| `#1d4ed8` | Blue dark (button hover) | ❌ No existe | Derivado de blue primary |
| `#94a3b8` | Gray border (expired/scheduled) | ❌ No existe | Nuevo gray |
| `#1e293b` | Dark text (titles) | ❌ No exacto | Similar a contrast |
| `#475569` | Gray text (dates, benefits) | ❌ No existe | Nuevo gray medium |
| `#64748b` | Gray light text (contact, labels) | ❌ No existe | Nuevo gray light |
| `#e2e8f0` | Light gray (divider) | ❌ No existe | Nuevo gray extra light |
| `#fef3c7` | Yellow background (scheduled badge) | ❌ No existe | Warning yellow |
| `#92400e` | Brown text (scheduled badge) | ❌ No existe | Warning text |
| `#fee2e2` | Red background (expired badge) | ❌ No existe | Error red |
| `#991b1b` | Red text (expired badge) | ❌ No existe | Error text |
| `rgba(37, 99, 235, 0.3)` | Button shadow | N/A | Derivado de blue |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `3rem`, `2.5rem`, `1.25rem`, `1.125rem`, `1rem`, `0.875rem`, `0.75rem` | Varios tamaños |
| Spacing | `2rem`, `1.5rem`, `1rem`, `0.875rem`, `0.75rem`, `0.625rem`, `0.5rem` | Padding, margin, gap |
| Border-radius | `12px`, `8px`, `6px` | Card, button, badges |
| Box-shadow | `0 4px 12px rgba(37, 99, 235, 0.3)` | Button hover |
| Transition | `all 0.2s ease` | Hover effects |
| Transform | `translateY(-1px)`, `translateY(0)` | Button hover/active |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa una **paleta completamente nueva (Blue/Gray)** que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon)
- Gray: #666666

**DealInfoCard usa (NUEVA PALETA):**
- Blue Primary: #2563eb
- Blue Dark: #1d4ed8
- Grays: #94a3b8, #1e293b, #475569, #64748b, #e2e8f0
- Status colors: Yellow (#fef3c7), Red (#fee2e2)

### Hallazgos Importantes

⚠️ **NO usa variables CSS en absoluto**
⚠️ **Paleta de colores única** solo para deals
⚠️ **Sistema de estados** (scheduled, expired, active)
⚠️ Sticky positioning

✅ Buenos selectores con prefijo `.deal-info-card`
✅ Responsive design
✅ Estados visuales claros

### Decisión Requerida

1. **Opción A:** Agregar paleta "Deals" a theme.json (blue + grays + status colors)
2. **Opción B:** Convertir a usar Primary/Secondary de theme.json (perder identidad visual)
3. **Opción C:** Crear variables locales dentro del bloque
4. **Opción D:** Crear theme.json separado para "Deals" section

**Recomendación:** Opción A - Agregar paleta Deals a theme.json

---

## Plan de Refactorización

### Opción A - Agregar a theme.json:

```json
{
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "deal-primary",
          "color": "#2563eb",
          "name": "Deal Blue"
        },
        {
          "slug": "deal-dark",
          "color": "#1d4ed8",
          "name": "Deal Blue Dark"
        },
        {
          "slug": "status-warning",
          "color": "#fef3c7",
          "name": "Warning Yellow"
        },
        {
          "slug": "status-error",
          "color": "#fee2e2",
          "name": "Error Red"
        }
      ]
    }
  }
}
```

### Cambios CSS:

```css
/* DESPUÉS */
.deal-info-card {
  border-color: var(--wp--preset--color--deal-primary);
}

.deal-info-card__discount-value {
  color: var(--wp--preset--color--deal-primary);
}

.deal-info-card__button {
  background: var(--wp--preset--color--deal-primary);
}

.deal-info-card__button:hover {
  background: var(--wp--preset--color--deal-dark);
}

.deal-info-card__status--scheduled {
  background: var(--wp--preset--color--status-warning);
}

.deal-info-card__status--expired {
  background: var(--wp--preset--color--status-error);
}
```

### Variables locales necesarias:

```css
.deal-info-card {
  /* Typography scale */
  --dic-text-xs: 0.75rem;
  --dic-text-sm: 0.875rem;
  --dic-text-base: 1rem;
  --dic-text-lg: 1.125rem;
  --dic-text-xl: 1.25rem;
  --dic-text-2xl: 2.5rem;
  --dic-text-3xl: 3rem;

  /* Spacing */
  --dic-spacing-xs: 0.25rem;
  --dic-spacing-sm: 0.5rem;
  --dic-spacing-md: 0.75rem;
  --dic-spacing-lg: 1rem;
  --dic-spacing-xl: 1.5rem;
  --dic-spacing-2xl: 2rem;

  /* Border radius */
  --dic-radius-sm: 6px;
  --dic-radius-md: 8px;
  --dic-radius-lg: 12px;

  /* Transitions */
  --dic-transition: all 0.2s ease;

  /* Sticky positioning */
  --dic-sticky-top: 2rem;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque específico de deals con:
- Paleta de colores única
- Sistema de estados (scheduled, expired, active)
- Sticky positioning
- Typography scale específica

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.deal-info-card`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **DECISIÓN CRÍTICA:** ¿Agregar paleta Deals a theme.json?
2. Si Opción A: Agregar colores deal-primary, deal-dark, status-warning, status-error a theme.json
3. Reemplazar todos los colores hardcodeados por variables theme.json
4. Crear variables locales para typography, spacing, borders
5. Testing en editor y frontend (verificar estados y sticky)
6. Commit: `refactor(deal-info-card): add Deal color palette to theme.json`

---

## Notas Adicionales

Este bloque introduce una **identidad visual completamente nueva** (blue) para la sección de Deals. Esto es intencional para diferenciar deals del resto del sitio. Recomiendo:
1. Documentar la paleta Deals
2. Crear guía de uso para todos los bloques Deal
3. Asegurar consistencia entre DealInfoCard, DealPackagesGrid y DealsSlider
