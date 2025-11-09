# Auditoría: SideBySideCards

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/side-by-side-cards.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS propias** - Define `--grid-columns`, `--card-gap`, `--image-width`, `--image-border-radius` pero NO usa variables de theme.json.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#E78C85` | `.sbs-card__badge--primary`, `.sbs-card__button--primary`, `.sbs-card__title a:hover`, `.sbs-card__divider`, `.sbs-card__location svg`, `.sbs-arrow:hover`, `.sbs-dot.is-active` | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#d97a74` | `.sbs-card__button--primary:hover` | ❌ No existe | **PROBLEMA:** Color coral dark no está en theme.json |
| `#311A42` | `.sbs-card__badge--secondary`, `.sbs-card__button--secondary`, `.sbs-card__price`, hover glow gradient | ❌ No existe | **PROBLEMA:** Color purple no está en theme.json |
| `#1f0f2a` | `.sbs-card__button--secondary:hover` | ❌ No existe | **PROBLEMA:** Color purple dark no está en theme.json |
| `#1A1A1A` | `.sbs-card__badge--white`, `.sbs-card__title`, `.sbs-card__button--white`, `.sbs-card__button--dark`, `.sbs-card__button--read-more` | ⚠️ Similar | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#CEA02D` | `.sbs-card__badge--gold`, `.sbs-card__button--gold`, hover glow gradient | ❌ No existe | Crear variable gold en theme.json |
| `#b58a25` | `.sbs-card__button--gold:hover` | ❌ No existe | Variante de gold, crear variable |
| `#666` | `.sbs-card__excerpt`, `.sbs-card__location` | ❌ No exacto | Usar `var(--wp--preset--color--gray)` (#666666) |
| `#ddd` | `.sbs-dot` | ❌ No existe | Usar gray con opacity |
| `white`, `#fff` | Múltiples | ⚠️ Usar semantic | Usar `var(--wp--preset--color--base)` |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `14px`, `13px`, `11px`, `18px`, `20px` | Title, excerpt, badge, button, price |
| Spacing | `16px`, `20px`, `24px`, `8px`, `6px`, `4px`, `12px` | Padding, margin, gap |
| Border-radius | `12px`, `20px`, `6px`, `8px`, `50%`, `4px` | Image, badge, button, dots |
| Transition | `0.3s ease`, `0.4s cubic-bezier(0.4, 0.0, 0.2, 1)` | Hover effects |
| Box-shadow | `0 2px 10px rgba(0,0,0,0.15)` | Arrows |
| Min/Max height | `130px`, `180px`, `250px` | Image heights |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **paleta Coral/Purple completa** (#E78C85, #311A42) en badges, botones, dividers, locations, etc. Estos colores **NO existen en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**SideBySideCards usa:**
- Primary: #E78C85 (coral) - **PROBLEMA**
- Secondary: #311A42 (purple) - **PROBLEMA**
- Gold: #CEA02D
- White, Dark variants

### Variantes de Color

El bloque define 6 variantes de color para badges y botones:
- `--primary` → #E78C85 (coral) ❌
- `--secondary` → #311A42 (purple) ❌
- `--white` → white ✅
- `--gold` → #CEA02D ⚠️
- `--dark` → #1A1A1A ✅
- `--transparent` → transparent ✅

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85), purple (#311A42) y gold (#CEA02D)
2. **Opción B:** Cambiar bloque para usar Primary (#17565C) y Secondary (#C66E65) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque para coral/purple/gold

**Recomendación:** Opción B - Alinear con theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.sbs-card__badge--primary {
  background: #E78C85;
  color: white;
}

.sbs-card__button--primary {
  background: #E78C85;
  color: white;
}

/* DESPUÉS */
.sbs-card__badge--primary {
  background: var(--wp--preset--color--secondary); /* #C66E65 */
  color: var(--wp--preset--color--base);
}

.sbs-card__button--primary {
  background: var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--base);
}
```

### Variables locales necesarias:

```css
.sbs-cards {
  /* Layout variables (ya existen) */
  --grid-columns: 3;
  --card-gap: 32px;
  --image-width: 40%;
  --image-border-radius: 12px;

  /* Spacing scale */
  --sbs-spacing-xs: var(--wp--preset--spacing--20, 0.25rem);
  --sbs-spacing-sm: var(--wp--preset--spacing--30, 0.5rem);
  --sbs-spacing-md: var(--wp--preset--spacing--40, 1rem);
  --sbs-spacing-lg: var(--wp--preset--spacing--50, 1.5rem);

  /* Transitions */
  --sbs-transition: 0.3s ease;
  --sbs-transition-bounce: 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);

  /* Font sizes */
  --sbs-font-xs: var(--wp--preset--font-size--tiny, 0.6875rem);
  --sbs-font-sm: var(--wp--preset--font-size--small, 0.8125rem);
  --sbs-font-md: var(--wp--preset--font-size--regular, 0.875rem);
  --sbs-font-lg: var(--wp--preset--font-size--medium, 1.125rem);
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:**
- Necesita variables locales para layout (grid-columns, card-gap, image-width)
- Transitions personalizadas
- Hover effects complejos (squeeze, lift, glow, zoom)
- Mobile slider con navegación custom

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.sbs-*`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral/purple vs theme.json)
2. Reemplazar #E78C85 → `var(--wp--preset--color--secondary)` (#C66E65)
3. Reemplazar #311A42 → `var(--wp--preset--color--primary)` (#17565C) o crear variable
4. Implementar spacing y font-size scales de theme.json
5. Consolidar variables locales
6. Testing de hover effects y mobile slider
7. Commit: `refactor(side-by-side-cards): align colors with theme.json`
