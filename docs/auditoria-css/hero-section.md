# Auditoría: HeroSection

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/hero-section.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores y valores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#000` | `.hero-section__overlay` background | ✅ Sí (conceptualmente) | Usar `var(--wp--preset--color--contrast)` o black literal |
| `#fff` | `.hero-section__title`, `.hero-section__subtitle`, `.hero-section__cta` | ✅ Sí | Usar `var(--wp--preset--color--base)` (#FFFFFF) |
| `#e74c3c` | `.btn-primary` background | ❌ No existe | **PROBLEMA:** Color rojo no está en theme.json |
| `#c0392b` | `.btn-primary:hover` | ❌ No existe | **PROBLEMA:** Color rojo hover no está en theme.json |
| `rgba(0, 0, 0, 0.3)` | Text shadow, hover shadow | ❌ N/A | Crear variable local |
| `rgba(0, 0, 0, 0.2)` | Button shadow | ❌ N/A | Crear variable local |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `clamp(2.5rem, 5vw, 4rem)`, `clamp(1.125rem, 2vw, 1.5rem)`, `1.125rem`, `1rem` | Tamaños de texto |
| Spacing | `2rem`, `1.5rem`, `1rem`, `2.5rem`, `0.875rem` | Margin, padding, gap |
| Heights | `400px`, `600px`, `800px`, `100vh`, `300px`, `450px` | Min-heights del hero |
| Border-radius | `4px` | Redondeo de botón |
| Transition | `all 0.3s ease` | Efectos de transición |
| Max-width | `1200px`, `800px` | Anchos máximos |
| Line-height | `1.2`, `1.5` | Alturas de línea |
| Font-weight | `700`, `600` | Pesos de fuente |
| Transform | `translateY(-2px)` | Hover effect |

---

## Análisis

### ⚠️ Problemas Principales

1. **Color de botón no alineado**: El bloque usa:
   - **Rojo** (#e74c3c) para botón primary - **NO existe en theme.json**
   - **Rojo oscuro** (#c0392b) para hover - **NO existe en theme.json**

2. **No integración con theme.json**: No usa ninguna variable de theme.json.

3. **Diseño simple**: Es un bloque relativamente simple comparado con otros, la refactorización debería ser directa.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)
- Base: #FFFFFF (white)
- Contrast: #111111 (black)

**HeroSection usa:**
- Rojo #e74c3c (button)
- Rojo oscuro #c0392b (button hover)
- White #fff (text)
- Black #000 (overlay)

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir el color rojo (#e74c3c)
2. **Opción B:** Cambiar HeroSection para usar Secondary (#C66E65) de theme.json para el botón
3. **Opción C:** Crear variables locales dentro del bloque

**Recomendación:** Opción B - Usar Secondary (#C66E65) de theme.json para el botón primary.

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.hero-section__title {
  color: #fff;
}

.hero-section__overlay {
  background-color: #000;
}

.hero-section__cta.btn-primary {
  background-color: #e74c3c;
  color: #fff;
}

.hero-section__cta.btn-primary:hover {
  background-color: #c0392b;
}

/* DESPUÉS */
.hero-section__title {
  color: var(--wp--preset--color--base);
}

.hero-section__overlay {
  background-color: var(--wp--preset--color--contrast);
}

.hero-section__cta.btn-primary {
  background-color: var(--wp--preset--color--secondary);
  color: var(--wp--preset--color--base);
}

.hero-section__cta.btn-primary:hover {
  background-color: var(--hero-section-btn-hover, #b55e54); /* Secondary más oscuro */
}
```

### Variables locales necesarias:

```css
.hero-section {
  /* Colores de theme.json */
  --hero-text-color: var(--wp--preset--color--base);
  --hero-overlay-bg: var(--wp--preset--color--contrast);
  --hero-btn-bg: var(--wp--preset--color--secondary);
  --hero-btn-color: var(--wp--preset--color--base);

  /* Colores locales (derivados) */
  --hero-btn-hover-bg: #b55e54; /* Secondary #C66E65 oscurecido ~10% */
  --hero-text-shadow: rgba(0, 0, 0, 0.3);
  --hero-btn-shadow: rgba(0, 0, 0, 0.2);
  --hero-btn-hover-shadow: rgba(0, 0, 0, 0.3);

  /* Spacing */
  --hero-spacing-sm: var(--wp--preset--spacing--30, 0.5rem);
  --hero-spacing-md: var(--wp--preset--spacing--50, 1rem);
  --hero-spacing-lg: 2rem;
  --hero-spacing-xl: 2.5rem;

  /* Heights */
  --hero-height-small: 400px;
  --hero-height-medium: 600px;
  --hero-height-large: 800px;
  --hero-height-full: 100vh;

  /* Transitions */
  --hero-transition: all 0.3s ease;

  /* Border radius */
  --hero-btn-radius: 4px;

  /* Max widths */
  --hero-content-max-width: 1200px;
  --hero-inner-max-width: 800px;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Necesita variables locales para:
1. Heights específicos del hero (small, medium, large, full)
2. Spacing del contenido interno
3. Transitions para efectos hover
4. Border radius del botón
5. Text shadows y box shadows
6. Max-widths del contenedor

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.hero-section`, no hay conflictos globales.

---

## Próximos Pasos

1. Reemplazar colores hardcodeados por variables de theme.json
2. Cambiar botón primary de rojo (#e74c3c) a Secondary (#C66E65)
3. Crear variable local para hover state del botón (secondary oscurecido)
4. Crear variables locales para spacing, heights, transitions y shadows
5. Testing en editor y frontend para asegurar que los colores se vean bien
6. Commit: `refactor(hero-section): use theme.json colors, add scoped CSS variables`
