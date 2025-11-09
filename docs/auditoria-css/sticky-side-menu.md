# Auditoría: StickySideMenu

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/sticky-side-menu.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS propias** - Define `--shadow-blur`, `--shadow-alpha`, `--offset-top` pero NO usa variables de theme.json.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#E78C85` | `.btn-cta--primary`, `.sticky-side-menu__menu a:hover`, `.current-menu-item > a` | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#d67a73` | `.btn-cta--primary:hover` | ❌ No existe | **PROBLEMA:** Color coral dark no está en theme.json |
| `#311A42` | `.btn-cta--secondary` | ❌ No existe | **PROBLEMA:** Color purple no está en theme.json |
| `#4a2862` | `.btn-cta--secondary:hover` | ❌ No existe | **PROBLEMA:** Color purple dark no está en theme.json |
| `#CEA02D` | `.btn-cta--gold` | ❌ No existe | Crear variable gold en theme.json |
| `#b8902a` | `.btn-cta--gold:hover` | ❌ No existe | Variante de gold |
| `#1A1A1A` | `.btn-cta--dark`, `.sticky-side-menu__hamburger`, `.sticky-side-menu__menu a`, `.btn-cta--white`, `.sticky-side-menu__close` | ⚠️ Similar | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#154D52` | `.sticky-side-menu__phone` | ⚠️ Similar | Muy similar a Primary (#17565C), usar Primary |
| `white`, `#fff`, `#ffffff` | Múltiples | ⚠️ Usar semantic | Usar `var(--wp--preset--color--base)` |
| `#f5f5f5` | `.btn-cta--white:hover`, `.sticky-side-menu__hamburger:hover`, `.sticky-side-menu__close:hover` | ❌ No existe | Usar gray con opacity |
| `#666` | `.sticky-side-menu__empty` | ❌ No exacto | Usar `var(--wp--preset--color--gray)` (#666666) |
| `#333333` | `.btn-cta--dark:hover` | ⚠️ Similar | Usar contrast con opacity |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `16px`, `18px`, `14px`, `13px`, `12px` | Phone, CTA, menu, mobile |
| Font-weight | `700`, `600`, `500` | Phone, CTA, menu |
| Spacing | `8px`, `12px`, `10px`, `20px`, `6px`, `3px`, `4px`, `16px`, `24px`, `40px`, `80px` | Padding, margin, gap |
| Border-radius | `20px`, `25px`, `50%`, `8px`, `10px`, `16px` | Menu, buttons, hamburger |
| Transition | `0.3s ease`, `0.4s cubic-bezier(0.4, 0, 0.2, 1)` | Hover effects, slide-in |
| Box-shadow | `0 2px 8px rgba(0, 0, 0, 0.15)`, `-4px 0 20px rgba(0, 0, 0, 0.15)` | Menu shadow |
| Transform | `translateX(100%)`, `translateX(0)`, `translateY(-4px)`, `rotate(45deg)` | Animations |
| Width/Height | `44px`, `36px`, `20px`, `2px`, `400px`, `320px`, `100vh` | Arrows, hamburger, nav panel |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **paleta Coral/Purple completa** (#E78C85, #311A42) en los botones CTA y enlaces del menú. Estos colores **NO existen en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**StickySideMenu usa:**
- Primary: #E78C85 (coral) - **PROBLEMA**
- Secondary: #311A42 (purple) - **PROBLEMA**
- Gold: #CEA02D
- Phone: #154D52 (muy similar a Primary #17565C ✅)
- White, Dark variants

### Variantes de Botón CTA

El bloque define 6 variantes de color para el botón CTA:
- `btn-cta--primary` → #E78C85 (coral) ❌
- `btn-cta--secondary` → #311A42 (purple) ❌
- `btn-cta--white` → white ✅
- `btn-cta--gold` → #CEA02D ⚠️
- `btn-cta--dark` → #1A1A1A ✅
- `btn-cta--transparent` → transparent ✅

### ✅ Aspecto Positivo

El color del teléfono (`#154D52`) es **muy similar** al Primary de theme.json (`#17565C`), casi idéntico. Solo necesita actualización menor.

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
.sticky-side-menu__phone {
  color: #154D52;
}

.btn-cta--primary {
  background: #E78C85;
  color: white;
}

.btn-cta--secondary {
  background: #311A42;
  color: white;
}

.sticky-side-menu__menu a:hover {
  color: #E78C85;
  border-bottom-color: #E78C85;
}

/* DESPUÉS */
.sticky-side-menu__phone {
  color: var(--wp--preset--color--primary); /* #17565C */
}

.btn-cta--primary {
  background: var(--wp--preset--color--secondary); /* #C66E65 */
  color: var(--wp--preset--color--base);
}

.btn-cta--secondary {
  background: var(--wp--preset--color--primary); /* #17565C */
  color: var(--wp--preset--color--base);
}

.sticky-side-menu__menu a:hover {
  color: var(--wp--preset--color--secondary);
  border-bottom-color: var(--wp--preset--color--secondary);
}
```

### Variables locales necesarias:

```css
.sticky-side-menu {
  /* Shadow (ya existe) */
  --shadow-blur: 12px;
  --shadow-alpha: 0.3;

  /* Spacing */
  --menu-spacing-xs: var(--wp--preset--spacing--20, 0.25rem);
  --menu-spacing-sm: var(--wp--preset--spacing--30, 0.5rem);
  --menu-spacing-md: var(--wp--preset--spacing--40, 1rem);
  --menu-spacing-lg: var(--wp--preset--spacing--50, 1.5rem);

  /* Transitions */
  --menu-transition: 0.3s ease;
  --menu-slide-transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);

  /* Dimensions */
  --menu-panel-width: 400px;
  --menu-panel-width-mobile: 320px;
  --hamburger-size: 20px;
  --hamburger-bar-height: 2px;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:**
- Fixed positioning con slide-in/out animations
- Hamburger button con animación (3 líneas → X)
- Slide-in panel navigation
- Overlay backdrop
- Sticky behavior con scroll trigger
- Shadow customizable

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.sticky-side-menu*` y `.btn-cta--*`, no hay conflictos globales.

**Nota:** `.btn-cta--*` es específico de este bloque, no colisiona con otros.

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral/purple vs theme.json)
2. Actualizar `#154D52` → `var(--wp--preset--color--primary)` (#17565C)
3. Reemplazar #E78C85 → `var(--wp--preset--color--secondary)` (#C66E65)
4. Reemplazar #311A42 → `var(--wp--preset--color--primary)` o crear variable
5. Implementar spacing scale de theme.json
6. Testing de animaciones y sticky behavior
7. Testing de hamburger animation
8. Testing de overlay y keyboard accessibility
9. Commit: `refactor(sticky-side-menu): align colors with theme.json`
