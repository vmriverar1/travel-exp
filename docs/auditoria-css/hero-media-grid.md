# Auditoría: HeroMediaGrid

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/template/hero-media-grid.css`
**Categoría:** Bloque Template
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores y valores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#212121` | `.hero-media-grid__main` background | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#E78C85` | Discount badge, View button, Activity dot | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#d97b75` | View button hover | ❌ No existe | **PROBLEMA:** Coral dark no está en theme.json |
| `#f5f5f5` | Map placeholder, gallery placeholder | ❌ No existe | Variable local para placeholders |
| `#757575` | Gallery placeholder text | ❌ No existe | Crear variable o usar gray con opacity |
| `#000` | Video background | ❌ No exacto | `var(--wp--preset--color--contrast)` |
| `white` | Discount badge text, button text, activity text | ✅ Sí | `var(--wp--preset--color--base)` |
| `rgba(0, 0, 0, 0.3)` | Box shadow, overlays | ❌ No existe | Variable local |
| `rgba(0, 0, 0, 0.5)` | Activity indicator bg | ❌ No existe | Variable local |
| `rgba(255, 255, 255, 0.5)` | Video placeholder | ❌ No existe | Variable local |
| `#e0e0e0` | Editor border | ❌ No existe | Variable local |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Grid | `65%`, `35%`, `1fr` | Layout columns |
| Heights | `400px`, `500px`, `545px`, `200px`, `150px` | Carousels, min-heights |
| Border-radius | `12px`, `2rem` | Cards, buttons |
| Spacing | `2rem`, `1.5rem`, `1rem`, `0.75rem`, etc. | Múltiples usos |
| Font-size | `0.875rem`, `0.75rem`, `0.8125rem` | Text sizes |
| Font-weight | `700`, `600` | Text weights |
| Transitions | `0.3s ease` | Hover effects |
| Shadows | `0 4px 8px`, `0 4px 12px`, `0 6px 16px` | Elevaciones |
| Gap | `12px`, `8px` | Grid gaps |

---

## Análisis

### ⚠️ Problemas Principales

1. **Color Coral (#E78C85):** Usado extensivamente pero NO existe en theme.json
   - Discount badge background
   - View button background
   - Activity dot active state

2. **Valores de grid hardcodeados:** `65%` / `35%` sin variables
3. **Múltiples heights específicos:** Sin sistema de variables
4. **Colores de overlay sin variables:** Múltiples valores rgba

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota) - Similar al coral
- Contrast: #111111 (negro)

**HeroMediaGrid usa:**
- Coral: #E78C85 ← No en theme.json
- Coral dark: #d97b75 ← No en theme.json

### Decisión Requerida

1. **Opción A:** Cambiar todo a Secondary (#C66E65) de theme.json
2. **Opción B:** Agregar coral (#E78C85) a theme.json como color oficial
3. **Opción C:** Crear variable local `--hero-accent-color`

**Recomendación:** Opción A - Usar Secondary para consistencia con theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.hero-gallery__discount-badge {
  background: #E78C85;
  color: white;
}

.hero-gallery__view-button {
  background: #E78C85;
  color: white;
}

.hero-gallery__view-button:hover {
  background: #d97b75;
}

.activity-dot.active {
  background: #E78C85;
}

/* DESPUÉS */
.hero-gallery__discount-badge {
  background: var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--base);
}

.hero-gallery__view-button {
  background: var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--base);
}

.hero-gallery__view-button:hover {
  background: var(--wp--preset--color--secondary-80);
}

.activity-dot.active {
  background: var(--wp--preset--color--secondary);
}
```

### Variables locales necesarias:

```css
.hero-media-grid {
  /* Layout proportions */
  --hero-grid-main-width: 65%;
  --hero-grid-sidebar-width: 35%;
  --hero-grid-gap: 12px;
  --hero-grid-gap-mobile: 8px;

  /* Heights */
  --hero-gallery-height-mobile: 246px;
  --hero-gallery-height-tablet: 500px;
  --hero-gallery-height-desktop: 545px;
  --hero-sidebar-height-desktop: 545px;
  --hero-sidebar-min-height-mobile: 200px;
  --hero-sidebar-min-height-xs: 150px;

  /* Colors */
  --hero-dark-bg: var(--wp--preset--color--contrast);
  --hero-placeholder-bg: #f5f5f5;
  --hero-placeholder-text: #757575;

  /* Overlays */
  --hero-overlay-light: rgba(0, 0, 0, 0.3);
  --hero-overlay-medium: rgba(0, 0, 0, 0.5);
  --hero-overlay-white: rgba(255, 255, 255, 0.5);

  /* Spacing */
  --hero-spacing-xs: 0.5rem;
  --hero-spacing-sm: 0.75rem;
  --hero-spacing-md: 1rem;
  --hero-spacing-lg: 1.5rem;
  --hero-spacing-xl: 2rem;

  /* Border radius */
  --hero-border-radius: 12px;
  --hero-button-border-radius: 2rem;

  /* Transitions */
  --hero-transition: 0.3s ease;

  /* Shadows */
  --hero-shadow-sm: 0 4px 8px rgba(0, 0, 0, 0.3);
  --hero-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.3);
  --hero-shadow-lg: 0 6px 16px rgba(0, 0, 0, 0.4);

  /* Editor */
  --hero-editor-border: #e0e0e0;
}
```

### Mapeo de font-sizes a theme.json:

| Actual | Theme.json equivalente |
|--------|------------------------|
| `0.875rem` (14px) | `var(--wp--preset--font-size--small)` |
| `0.75rem` (12px) | `var(--wp--preset--font-size--tiny)` |
| `0.8125rem` (13px) | Crear variable local |

---

## CSS Personalizado Necesario: **SÍ**

**Razón:**
- Grid proportions específicas (65/35)
- Multiple height breakpoints
- Sistema completo de overlays
- Shadows y elevaciones
- Layout complejo con Swiper carousel

---

## Selectores Específicos: ✅ OK

Todos los selectores usan prefijos `.hero-media-grid__`, `.hero-gallery__`, `.activity-`. Sigue metodología BEM consistentemente.

---

## Accesibilidad

✅ **BUENO:**
- Focus visible styles para botón
- Activity indicator con estructura semántica

❌ **FALTA:**
- `prefers-reduced-motion` para transitions
- `prefers-color-scheme` consideración

### Mejora sugerida:

```css
@media (prefers-reduced-motion: reduce) {
  .hero-gallery__view-button,
  .hero-media-grid__map-overlay,
  .activity-dot {
    transition: none;
  }
}
```

---

## Responsive Design: ✅ EXCELENTE

- ✅ Grid adapta de 65/35 a 1fr en tablet
- ✅ Heights progresivos (mobile → tablet → desktop)
- ✅ Sidebar cambia de vertical stack a horizontal grid en mobile
- ✅ Breakpoints en 1024px, 768px, 480px
- ✅ Mantiene 2 columnas para sidebar en mobile (UX inteligente)

---

## Integración con Swiper

⚠️ **OBSERVACIÓN:** Este bloque depende de Swiper.js para el carousel. Los estilos están preparados para:
- `.swiper-wrapper`
- `.swiper-slide`
- Controles de navegación

**Verificar:** Que los estilos de Swiper no entren en conflicto con theme.json.

---

## Próximos Pasos

1. ❓ **Decidir color coral:** Usar Secondary o agregar a theme.json
2. Crear todas las variables locales propuestas
3. Reemplazar colores hardcodeados por variables
4. Agregar `prefers-reduced-motion`
5. Mapear font-sizes a theme.json
6. Testing con Swiper en diferentes breakpoints
7. Verificar overflow y grid behavior en edge cases
8. Commit: `refactor(hero-media-grid): use theme.json variables and improve responsive`
