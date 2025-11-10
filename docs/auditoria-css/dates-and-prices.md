# Auditoría: Dates and Prices

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/dates-and-prices.css`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**DEFINE variables CSS en :root**, pero NINGUNA existe en theme.json. Este bloque define su propia paleta completa.

### Variables definidas en :root (líneas 12-53):

| Variable CSS | Valor | Uso | ¿Existe en theme.json? |
|--------------|-------|-----|------------------------|
| `--booking-bg` | `#FFFFFF` | Background | ❌ No (usar white) |
| `--booking-border` | `#E5E7EB` | Borders | ❌ No |
| `--booking-text` | `#1F2937` | Text | ❌ No (similar a contrast) |
| `--booking-muted` | `#6B7280` | Muted text | ❌ No (similar a gray) |
| `--gray-100` | `#F5F6F7` | Backgrounds | ❌ No |
| `--gray-300` | `#E5E7EB` | Borders | ❌ No |
| `--rose` | `#E78C85` | **CTA principal (coral)** | ❌ **PROBLEMA** |
| `--green-strong` | `#A8F04C` | Accent/deal | ❌ **PROBLEMA** |
| `--green-soft` | `#EBFED3` | Deal backgrounds | ❌ **PROBLEMA** |
| `--green-dark` | `#0A797E` | Deal text/price | ❌ No (similar a primary #17565C) |
| `--booking-cta` | `#E78C85` | Button background | ❌ **PROBLEMA (coral)** |
| `--booking-cta-text` | `#FFFFFF` | Button text | ❌ No |
| `--booking-cta-hover` | `#D97C76` | Hover state | ❌ **PROBLEMA (coral dark)** |
| `--booking-deal-bg` | `#EBFED3` | Deal card bg | ❌ **PROBLEMA** |
| `--booking-deal-border` | `#D6F6B0` | Deal border | ❌ **PROBLEMA** |
| `--booking-deal-badge` | `#0A797E` | Badge text | ❌ No |
| `--booking-deal-badge-bg` | `#DFF4C6` | Badge bg | ❌ **PROBLEMA** |
| `--booking-deal-price` | `#0A797E` | Price color | ❌ No |
| `--booking-soldout-bg` | `#F3F4F6` | Sold out bg | ❌ No |
| `--booking-soldout-text` | `#9CA3AF` | Sold out text | ❌ No |
| `--booking-alert-bg` | `#FFF5ED` | Alert background | ❌ No |
| `--booking-alert-border` | `#FFD9BF` | Alert border | ❌ No |
| `--booking-alert-accent` | `#FF7A1A` | Alert accent | ❌ No |
| `--booking-chip-soldout` | `#F1F2F4` | Chip bg | ❌ No |
| `--booking-chip-available` | `#F8FAFF` | Chip bg | ❌ No |
| `--booking-chip-deal` | `#EBFED3` | Chip bg | ❌ **PROBLEMA** |
| `--booking-chip-deal-text` | `#0A797E` | Chip text | ❌ No |

### Colores hardcodeados adicionales:

| Color | Uso | Líneas |
|-------|-----|--------|
| `#fff`, `#FFFFFF` | Backgrounds, text | Multiple |
| `#F9FAFB` | Hover states | 107, 112, 179, 240, 569 |
| `#D1D5DB` | Hover border, scrollbar | 108, 279 |
| `#6AA9FF` | Focus outline | 118, 188, 500 |
| `#F3F4F6` | Scrollbar track | 274 |
| `#9CA3AF` | Scrollbar hover | 284 |
| `rgba(0, 0, 0, 0.12)` | Box shadow | 217 |
| `rgba(0, 0, 0, 0.08)` | Hover shadow | 310 |
| `rgba(240, 138, 123, 0.3)` | CTA hover shadow (coral) | 491 |
| `#FFF3CD` | Empty hint bg | 597 |
| `#FFE69C` | Empty hint border | 598 |
| `#856404` | Empty hint text | 601 |
| `#E78C85` | Rose (select button) | 168, 169 |
| `#E07A73` | Rose hover | 174, 175 |

### Valores hardcodeados (spacing, sizing, etc.):

| Tipo | Valores |
|------|---------|
| Border-radius | `16px`, `12px`, `9999px`, `8px`, `6px`, `3px` |
| Padding | Multiple (4px-56px range) |
| Gap | `4px`, `6px`, `8px`, `10px`, `12px`, `16px` |
| Font-size | `11px`-`18px` |
| Height | `32px`, `36px`, `40px` |
| Transitions | `0.2s ease`, `0.15s ease` |
| Max-width | `880px`, `600px`, `220px` |
| Max-height | `480px`, `400px`, `320px` |
| Box-shadow | Multiple variants |
| Z-index | `10`, `9999` |

---

## Análisis

### ⚠️ PROBLEMA CRÍTICO

Este bloque define **UNA PALETA COMPLETA** en `:root` que **NO existe en theme.json**.

**Paleta personalizada usada:**
- **Rose/Coral:** #E78C85 (CTA principal - el mismo coral de otros bloques)
- **Green Strong:** #A8F04C (lima brillante para deals)
- **Green Soft:** #EBFED3 (verde suave para fondos)
- **Green Dark:** #0A797E (teal oscuro - similar a primary)

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)
- Gray: #666666
- Contrast: #111111

### Problemas Identificados

1. **Variables en :root global:** Define 26+ variables en `:root`, contaminando el scope global
2. **Paleta coral completa:** Usa rose (#E78C85) que es el coral legacy
3. **Paleta verde personalizada:** 3 verdes (#A8F04C, #EBFED3, #0A797E) que no existen en theme.json
4. **Colores de estado:** Orange alerts que no están definidos en theme.json
5. **Grises personalizados:** Escala de grises diferente a theme.json
6. **Focus outline:** Usa #6AA9FF (azul) que no está en theme.json
7. **Promo backgrounds:** Hardcoded en líneas 748-755 con !important

### Impacto

- **ALTO:** 756 líneas, archivo más grande hasta ahora
- **ALTO:** Define paleta completa en :root
- **MEDIO:** Usa sistema de colores muy específico para booking UI
- **BAJO:** Bien organizado pero completamente desacoplado de theme.json

---

## Plan de Refactorización

### Opción A: Migración Completa (Recomendada)

Reemplazar todas las variables con theme.json + variables locales scoped.

#### Paso 1: Remover :root y crear scope local

```css
/* ANTES */
:root {
    --rose: #E78C85;
    --green-strong: #A8F04C;
    --green-soft: #EBFED3;
    --green-dark: #0A797E;
    /* ... 26+ variables */
}

/* DESPUÉS */
.booking {
    /* CTA colors - usar secondary de theme.json */
    --booking-cta: var(--wp--preset--color--secondary); /* #C66E65 salmon */
    --booking-cta-hover: #B35D54; /* Darker salmon */

    /* Deal colors - usar primary + derivados */
    --booking-deal-text: var(--wp--preset--color--primary); /* #17565C teal */
    --booking-deal-bg: #E0F2F1; /* Teal light */
    --booking-deal-badge-bg: #B2DFDB; /* Teal lighter */
    --booking-deal-strong: #4CAF50; /* Green accent */

    /* Text colors */
    --booking-text: var(--wp--preset--color--contrast); /* #111111 */
    --booking-muted: var(--wp--preset--color--gray); /* #666666 */

    /* Borders */
    --booking-border: #E0E0E0;

    /* Backgrounds */
    --booking-bg: #FFFFFF;
    --booking-gray-light: #F5F5F5;

    /* Status colors */
    --booking-soldout-bg: #F5F5F5;
    --booking-soldout-text: #9E9E9E;

    /* Alert (orange) */
    --booking-alert-bg: #FFF5ED;
    --booking-alert-border: #FFD9BF;
    --booking-alert-accent: #FF7A1A;

    /* Focus */
    --booking-focus: #6AA9FF;

    /* Spacing */
    --booking-radius-sm: 6px;
    --booking-radius-md: 12px;
    --booking-radius-lg: 16px;
    --booking-radius-full: 9999px;

    /* Transitions */
    --booking-transition-fast: 0.15s ease;
    --booking-transition-base: 0.2s ease;
}
```

#### Paso 2: Mapeo de colores

| Variable Original | Nuevo Valor | Razón |
|-------------------|-------------|-------|
| `--rose` (#E78C85) | `--wp--preset--color--secondary` (#C66E65) | Eliminar coral, usar salmon |
| `--green-dark` (#0A797E) | `--wp--preset--color--primary` (#17565C) | Ya es similar al teal |
| `--green-strong` (#A8F04C) | `#4CAF50` o custom | Crear variable local para accent |
| `--green-soft` (#EBFED3) | Derivar de primary con opacity | Teal light calculado |
| `--booking-text` (#1F2937) | `--wp--preset--color--contrast` (#111111) | Usar contrast |
| `--booking-muted` (#6B7280) | `--wp--preset--color--gray` (#666666) | Usar gray |

#### Paso 3: Eliminar hardcoded colors

```css
/* ANTES */
.icon-btn--select {
    background: #E78C85;
    border-color: #E78C85;
}

.icon-btn--select:hover {
    background: #E07A73;
    border-color: #E07A73;
}

/* DESPUÉS */
.icon-btn--select {
    background: var(--booking-cta);
    border-color: var(--booking-cta);
}

.icon-btn--select:hover {
    background: var(--booking-cta-hover);
    border-color: var(--booking-cta-hover);
}
```

---

### Opción B: Migración Parcial

Mantener variables locales pero mapear a theme.json donde sea posible.

**Ventaja:** Menos cambios, menos riesgo de romper UI
**Desventaja:** Sigue usando paleta personalizada

---

## CSS Personalizado Necesario: **SÍ (CRÍTICO)**

**Razones:**
1. Sistema completo de booking UI con múltiples estados (available, soldout, deal)
2. Paleta de colores específica para deals y promos
3. Sistema completo de spacing, border-radius y transitions
4. Scrollbar personalizado
5. Sistema de chips y badges
6. Estados de focus, hover, disabled específicos
7. Responsive design complejo (mobile, tablet, desktop)
8. Print styles
9. Integration con API (promo backgrounds)

---

## Selectores Específicos: ⚠️ PROBLEMA

1. **:root global:** Define 26+ variables en `:root` (líneas 12-53) - **CONTAMINA SCOPE GLOBAL**
2. **Selectores OK:** El resto usa `.booking` prefix correctamente
3. **Promo classes:** Usa `.trip-card.booking-row--promo-*` con !important

**Acción requerida:**
- Mover todas las variables de `:root` a `.booking` para scope local
- Remover !important de promo backgrounds si es posible

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. **CRÍTICO:** Decidir estrategia de migración (Opción A vs B)
3. Mover variables de `:root` a `.booking`
4. Mapear colores a theme.json donde sea posible
5. Actualizar hardcoded colors (#E78C85, #E07A73) a usar variables
6. Revisar contraste de colores después del cambio
7. Testing extensivo (booking flow, deals, sold out states)
8. Commit: `refactor(dates-and-prices): migrate color system to theme.json and local scope`

---

## Notas Adicionales

**Buenas prácticas encontradas:**
- ✅ Código muy bien organizado con comentarios de sección
- ✅ Responsive design completo
- ✅ Estados de accesibilidad (focus-visible)
- ✅ Print styles
- ✅ Empty states
- ✅ Loading states
- ✅ Screen reader support (.sr-only)
- ✅ Custom scrollbar styling

**Problemas de arquitectura:**
- ❌ Variables en :root contaminan scope global
- ❌ Paleta completa personalizada desacoplada de theme.json
- ❌ No usa CSS custom properties de WordPress
- ❌ Hardcoded colors duplicados en múltiples lugares
- ❌ !important en promo backgrounds

**Complejidad del bloque:**
- **MUY ALTA:** 756 líneas, sistema completo de booking UI
- **Sistema de colores complejo:** 3 variantes (normal, deal, soldout)
- **Interactividad alta:** Year tabs, month navigation, popover, scroll
- **Responsive:** 2 breakpoints con cambios significativos de layout

**Recomendación Final:**
Este bloque es **CRÍTICO** y requiere refactorización **ALTA PRIORIDAD**. Sugiero:
1. Crear un plan de migración detallado con diseño
2. Validar nueva paleta con equipo de diseño
3. Testing A/B si es producción
4. Migración por fases (primero CTA, luego deals, luego resto)
