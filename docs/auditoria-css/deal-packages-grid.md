# Auditoría: DealPackagesGrid

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/deal-packages-grid.css`
**Categoría:** Bloque ACF - Deal
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#fff` | Card background, button text | ✅ Sí | Usar `var(--wp--preset--color--base)` |
| `#2563eb` | Blue primary (border, badge, price, button) | ❌ No existe | **MISMA PALETA** que DealInfoCard |
| `#1d4ed8` | Blue dark (button hover) | ❌ No existe | Derivado de blue primary |
| `#1e293b` | Dark text (title) | ❌ No exacto | Similar a contrast |
| `#475569` | Gray text (excerpt) | ❌ No existe | Gray medium |
| `#64748b` | Gray light text (meta, empty state) | ❌ No existe | Gray light |
| `#94a3b8` | Gray icons | ❌ No existe | Gray for icons |
| `#e2e8f0` | Light gray (border, footer divider) | ❌ No existe | Gray extra light |
| `#cbd5e1` | Hover border | ❌ No existe | Gray for hover |
| `#f1f5f9` | Image placeholder | ❌ No existe | Gray lightest |
| `rgba(0, 0, 0, 0.1)`, `rgba(0, 0, 0, 0.15)` | Shadows | N/A | Create local variables |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `1.5rem`, `1.25rem`, `1.125rem`, `1rem`, `0.9375rem`, `0.875rem`, `0.75rem` | Varios tamaños |
| Spacing | `3rem`, `2rem`, `1.5rem`, `1.25rem`, `1rem`, `0.875rem`, `0.625rem`, `0.5rem`, `0.375rem`, `0.25rem`, `0.125rem` | Padding, margin, gap |
| Border-radius | `12px`, `6px` | Card, badge, button |
| Aspect ratio | `16/9` | Image aspect ratio |
| Box-shadow | Múltiples | Card shadows |
| Transition | `all 0.3s ease`, `all 0.2s ease`, `transform 0.3s ease` | Hover effects |
| Transform | `translateY(-4px)`, `translateX(2px)`, `scale(1.05)` | Hover lift, button, image zoom |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa la **MISMA paleta Blue/Gray** que DealInfoCard, pero **NO usa variables CSS**.

**Paleta Deals (compartida con DealInfoCard):**
- Blue Primary: #2563eb
- Blue Dark: #1d4ed8
- Grays: #94a3b8, #1e293b, #475569, #64748b, #e2e8f0, #cbd5e1, #f1f5f9

### Hallazgos Importantes

⚠️ **NO usa variables CSS en absoluto**
⚠️ **Colores duplicados** de DealInfoCard (no reutilizables)
✅ **Grid responsive** con breakpoints
✅ Buenos selectores con prefijo `.deal-package-`
✅ Image aspect ratio con CSS
✅ Empty state
✅ Badge positioning

### Decisión Requerida

**IMPORTANTE:** Este bloque debe compartir variables con DealInfoCard.

1. **Opción A:** Crear archivo compartido `deals-common.css` con variables
2. **Opción B:** Agregar paleta Deals a theme.json (mismo que DealInfoCard)
3. **Opción C:** Usar variables locales en cada bloque (duplicación)

**Recomendación:** Opción B - Agregar a theme.json (consistencia con DealInfoCard)

---

## Plan de Refactorización

### Agregar a theme.json (mismo que DealInfoCard):

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
        }
      ]
    }
  }
}
```

### Cambios CSS:

```css
/* DESPUÉS */
.deal-package-card {
  border-color: var(--wp--preset--color--gray-light);
  background: var(--wp--preset--color--base);
}

.deal-package-card__badge {
  background: var(--wp--preset--color--deal-primary);
  color: var(--wp--preset--color--base);
}

.deal-package-card__title a {
  color: var(--wp--preset--color--contrast);
}

.deal-package-card__title a:hover {
  color: var(--wp--preset--color--deal-primary);
}

.deal-package-card__price-value {
  color: var(--wp--preset--color--deal-primary);
}

.deal-package-card__button {
  background: var(--wp--preset--color--deal-primary);
}

.deal-package-card__button:hover {
  background: var(--wp--preset--color--deal-dark);
}
```

### Variables locales necesarias:

```css
.deal-packages-grid {
  /* Typography scale */
  --dpg-text-xs: 0.75rem;
  --dpg-text-sm: 0.875rem;
  --dpg-text-base: 0.9375rem;
  --dpg-text-lg: 1rem;
  --dpg-text-xl: 1.125rem;
  --dpg-text-2xl: 1.25rem;
  --dpg-text-3xl: 1.5rem;

  /* Spacing */
  --dpg-spacing-2xs: 0.125rem;
  --dpg-spacing-xs: 0.25rem;
  --dpg-spacing-sm: 0.375rem;
  --dpg-spacing-md: 0.625rem;
  --dpg-spacing-lg: 1rem;
  --dpg-spacing-xl: 1.25rem;
  --dpg-spacing-2xl: 1.5rem;
  --dpg-spacing-3xl: 2rem;
  --dpg-spacing-4xl: 3rem;

  /* Border radius */
  --dpg-radius-sm: 6px;
  --dpg-radius-lg: 12px;

  /* Shadows */
  --dpg-shadow-sm: 0 12px 24px rgba(0, 0, 0, 0.1);

  /* Transitions */
  --dpg-transition: all 0.3s ease;
  --dpg-transition-fast: all 0.2s ease;

  /* Image */
  --dpg-image-aspect: 16/9;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque de grid con:
- Múltiples columnas responsive (1, 2, 3)
- Image aspect ratio
- Card hover effects
- Meta information layout
- Empty state

---

## Selectores Específicos: ✅ OK

Todos los selectores usan prefijos `.deal-packages-grid`, `.deal-package-card`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **DECISIÓN CRÍTICA:** Coordinar con DealInfoCard para paleta compartida
2. Agregar colores deal-primary, deal-dark a theme.json (si no se hizo con DealInfoCard)
3. Reemplazar todos los colores hardcodeados por variables theme.json
4. Crear variables locales para typography, spacing, borders
5. Testing en editor y frontend (verificar grid responsive)
6. Commit: `refactor(deal-packages-grid): migrate to Deal color palette`

---

## Notas Adicionales

**IMPORTANTE:** Este bloque DEBE compartir variables con:
- DealInfoCard
- DealsSlider

Recomiendo crear un sistema consistente para todos los bloques Deal:
1. **deals-variables.css** - Variables compartidas
2. **Paleta Deal en theme.json** - Colores centralizados
3. **Documentación Deal Design System** - Guía de uso
