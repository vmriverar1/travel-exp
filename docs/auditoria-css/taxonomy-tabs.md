# Auditoría: TaxonomyTabs

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/taxonomy-tabs.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS propias** - Define `:root` con variables locales (`--tt-*`) y variables inline (`--cards-per-row`, `--card-gap`) pero NO usa variables de theme.json.

### Variables locales definidas:

```css
:root {
  --tt-primary-color: #2563eb;
  --tt-secondary-color: #64748b;
  --tt-accent-color: #f59e0b;
  --tt-text-color: #1e293b;
  --tt-bg-color: #ffffff;
  --tt-border-color: #e2e8f0;
  --tt-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  --tt-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}
```

⚠️ **PROBLEMA:** Estas variables NO se usan en el CSS. El bloque define variables pero luego usa colores hardcodeados diferentes.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#E78C85` | `.tt-card__badge--primary`, `.tt-card__button--primary`, `.tt-arrow--prev`, `.tt-dot.is-active` (mobile) | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#dc7b74` / `#d97a74` | `.tt-card__badge--primary:hover`, `.tt-card__button--primary:hover` | ❌ No existe | **PROBLEMA:** Color coral dark no está en theme.json |
| `#311A42` | `.tt-card__badge`, `.tt-card__badge--secondary`, `.tt-card__button--secondary`, `.tt-nav__item.is-active` (underline), hover glow gradient | ❌ No existe | **PROBLEMA:** Color purple no está en theme.json |
| `#402753` | `.tt-card__badge--secondary:hover`, `.tt-card__button--secondary:hover` | ❌ No existe | **PROBLEMA:** Color purple dark no está en theme.json |
| `#CEA02D` | `.tt-card__badge--gold`, `.tt-card__button--gold`, hover glow gradient | ❌ No existe | Crear variable gold en theme.json |
| `#b88f25` | `.tt-card__badge--gold:hover`, `.tt-card__button--gold:hover` | ❌ No existe | Variante de gold |
| `#1A1A1A` | `.tt-card__badge--dark`, `.tt-card__button--dark` | ⚠️ Similar | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#2d2d2d` | `.tt-card__badge--dark:hover`, `.tt-card__button--dark:hover` | ⚠️ Similar | Usar contrast con opacity |
| `white`, `#fff`, `#ffffff` | Múltiples | ⚠️ Usar semantic | Usar `var(--wp--preset--color--base)` |
| `#94a3b8`, `#64748b`, `#cbd5e1`, `#e2e8f0`, `#f5f5f5` | Tabs, borders, backgrounds | ❌ No existen | Usar gray scale de theme.json |
| `black`, `#000`, `#000000` | Active tabs hero-overlap, badges | ⚠️ Usar semantic | Usar `var(--wp--preset--color--contrast)` |
| `#2563eb` | `.tt-dot.is-active` (desktop) | ❌ No existe | Este es el `--tt-primary-color` definido pero no usado en cards |
| `#1976d2`, `#1565c0` | `.tt-card__button--read-more` | ❌ No existen | Crear variable o eliminar variant |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `22px`, `20px`, `18px`, `14px`, `13px`, `12px`, `11px`, `24px` | Titles, excerpts, badges, buttons, tabs |
| Font-weight | `700`, `600`, `500`, `400`, `900` | Titles, badges, tabs |
| Spacing | `40px`, `32px`, `24px`, `20px`, `16px`, `12px`, `8px`, `6px`, `4px` | Padding, margin, gap |
| Border-radius | `12px`, `20px`, `24px`, `8px`, `18px`, `999px`, `50%`, `4px` | Cards, badges, buttons, dots |
| Transition | `0.3s ease`, `0.2s ease`, `0.3s cubic-bezier(0.4, 0, 0.2, 1)`, `0.4s ease` | Hover effects, animations |
| Box-shadow | Multiple complex shadows | Cards, badges, buttons, arrows |
| Min-height | `450px`, `400px`, `380px` | Cards |

---

## Análisis

### ⚠️ Problemas Principales

1. **Variables definidas pero NO usadas:** El bloque define `--tt-primary-color`, `--tt-secondary-color`, etc. en `:root` pero luego usa colores hardcodeados completamente diferentes (#E78C85, #311A42).

2. **Paleta Coral/Purple completa:** Usa #E78C85 (coral) y #311A42 (purple) extensivamente en badges, botones, tabs. Estos colores **NO existen en theme.json**.

3. **Múltiples variantes de color:** Define 7 variantes (primary, secondary, white, gold, dark, transparent, read-more) con colores hardcodeados.

4. **Importa Google Fonts:** Importa 'Saira Condensed' e 'Inter' que pueden no estar en el theme.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**TaxonomyTabs usa:**
- Primary coral: #E78C85 ❌
- Secondary purple: #311A42 ❌
- Gold: #CEA02D ⚠️
- Blue (definido pero poco usado): #2563eb ⚠️
- Multiple grays no documentados

### Google Fonts Import

```css
@import url('https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
```

⚠️ Estas fuentes pueden no estar alineadas con el theme. Verificar si theme.json define estas fuentes.

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85), purple (#311A42) y gold (#CEA02D)
2. **Opción B:** Cambiar bloque para usar Primary (#17565C) y Secondary (#C66E65) de theme.json
3. **Opción C:** Usar las variables `--tt-*` ya definidas y mapearlas a theme.json
4. **Opción D:** Eliminar variables `--tt-*` no usadas y crear nuevas mapeadas a theme.json

**Recomendación:** Opción D - Limpiar variables no usadas y alinear con theme.json

---

## Plan de Refactorización

### Paso 1: Eliminar variables `:root` no usadas

```css
/* ELIMINAR ESTO */
:root {
  --tt-primary-color: #2563eb;
  --tt-secondary-color: #64748b;
  --tt-accent-color: #f59e0b;
  --tt-text-color: #1e293b;
  --tt-bg-color: #ffffff;
  --tt-border-color: #e2e8f0;
  --tt-shadow: ...;
  --tt-shadow-lg: ...;
}
```

### Paso 2: Crear variables locales mapeadas a theme.json

```css
.taxonomy-tabs {
  /* Colors from theme.json */
  --tt-primary: var(--wp--preset--color--primary); /* #17565C */
  --tt-secondary: var(--wp--preset--color--secondary); /* #C66E65 */
  --tt-base: var(--wp--preset--color--base);
  --tt-contrast: var(--wp--preset--color--contrast);
  --tt-gray: var(--wp--preset--color--gray);

  /* Spacing */
  --tt-gap-sm: var(--wp--preset--spacing--30, 0.5rem);
  --tt-gap-md: var(--wp--preset--spacing--40, 1rem);
  --tt-gap-lg: var(--wp--preset--spacing--50, 1.5rem);

  /* Transitions */
  --tt-transition-fast: 0.2s ease;
  --tt-transition-normal: 0.3s ease;

  /* Layout (ya existen) */
  --cards-per-row: 3;
  --card-gap: 24px;
}
```

### Paso 3: Reemplazar colores hardcodeados

```css
/* ANTES */
.tt-card__badge--primary {
  background: #E78C85;
  color: #fff;
}

.tt-card__button--primary {
  background: #E78C85;
  color: #fff;
}

.tt-arrow--prev {
  background: #E78C85;
  color: #ffffff;
}

/* DESPUÉS */
.tt-card__badge--primary {
  background: var(--tt-secondary); /* #C66E65 */
  color: var(--tt-base);
}

.tt-card__button--primary {
  background: var(--tt-secondary);
  color: var(--tt-base);
}

.tt-arrow--prev {
  background: var(--tt-secondary);
  color: var(--tt-base);
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:**
- Tabs system con múltiples estilos (pills, underline, buttons)
- Cards grid con mobile slider
- Navigation arrows y dots
- Hero overlap variant
- Multiple hover effects
- Badge y button variants
- Animations (fadeIn, scale, transform)

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.tt-*` o `.taxonomy-tabs*`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral/purple vs theme.json)
2. Eliminar variables `:root` no usadas (`--tt-primary-color`, etc.)
3. Crear variables locales `.taxonomy-tabs` mapeadas a theme.json
4. Reemplazar #E78C85 → `var(--wp--preset--color--secondary)` (#C66E65)
5. Reemplazar #311A42 → `var(--wp--preset--color--primary)` (#17565C)
6. Verificar Google Fonts - ¿están en theme.json?
7. Implementar spacing y font-size scales de theme.json
8. Testing de tabs navigation
9. Testing de mobile slider
10. Testing de hero-overlap variant
11. Commit: `refactor(taxonomy-tabs): clean unused variables and align with theme.json`
