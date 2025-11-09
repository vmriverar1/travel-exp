# Auditoría: StaticHero

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/StaticHero/style.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `rgba(0, 0, 0, 0.4)` | `.acf-gbr-static-hero__overlay` background | ❌ No existe | Crear variable local con opacity |
| `#fff` | `.acf-gbr-static-hero__overlay` color | ⚠️ Usar semantic | Usar `var(--wp--preset--color--base)` |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `clamp(2rem, 5vw, 3rem)`, `clamp(1.2rem, 3vw, 1.6rem)` | Title, subtitle |
| Spacing | `6rem 2rem`, `0.5rem` | Padding, margin |
| Min-height | `100vh` | Hero height |

---

## Análisis

### ✅ Bloque Muy Minimalista

Este es el bloque **MÁS SIMPLE** de todos los auditados:
- Solo 39 líneas de CSS
- Usa `clamp()` para responsive typography ✅
- No usa colores de marca específicos
- Solo usa negro con opacity para overlay

### Problemas Menores

1. **Overlay opacity hardcodeada:** `rgba(0, 0, 0, 0.4)` debería ser una variable CSS customizable
2. **Color de texto hardcodeado:** `#fff` debería usar variable de theme.json
3. **No usa spacing scale:** `6rem 2rem` podría usar `--wp--preset--spacing--*`

### ✅ NO HAY PROBLEMAS con coral/purple

Este bloque **NO usa** la paleta Coral/Purple problemática.

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.acf-gbr-static-hero__overlay {
  background: rgba(0, 0, 0, 0.4);
  color: #fff;
  padding: 6rem 2rem;
}

/* DESPUÉS */
.acf-gbr-static-hero__overlay {
  background: rgba(0, 0, 0, var(--hero-overlay-opacity, 0.4));
  color: var(--wp--preset--color--base);
  padding: var(--wp--preset--spacing--100, 6rem) var(--wp--preset--spacing--60, 2rem);
}
```

### Variables locales necesarias:

```css
.acf-gbr-static-hero {
  /* Overlay */
  --hero-overlay-opacity: 0.4;
  --hero-overlay-color: rgb(0, 0, 0);

  /* Typography */
  --hero-title-size: clamp(2rem, 5vw, 3rem);
  --hero-subtitle-size: clamp(1.2rem, 3vw, 1.6rem);

  /* Spacing */
  --hero-padding-y: var(--wp--preset--spacing--100, 6rem);
  --hero-padding-x: var(--wp--preset--spacing--60, 2rem);
}
```

---

## CSS Personalizado Necesario: **SÍ (mínimo)**

**Razón:**
- Overlay con opacity personalizada
- Responsive clamp() typography
- Fullscreen height (`100vh`)
- Background image positioning

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.acf-gbr-static-hero*`, no hay conflictos globales.

---

## Próximos Pasos

1. ✅ Bajo prioridad - No hay problemas críticos
2. Reemplazar `#fff` → `var(--wp--preset--color--base)`
3. Considerar hacer overlay-opacity customizable vía editor
4. Implementar spacing scale de theme.json
5. Testing de legibilidad de texto sobre diferentes imágenes
6. Commit: `refactor(static-hero): use theme.json variables`
