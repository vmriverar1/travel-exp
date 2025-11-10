# Auditoría: PromoCards

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/template/promo-cards.css`
**Categoría:** Bloque Template
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa valores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `rgba(0, 0, 0, 0.1)` | `.promo-card` box-shadow | ❌ No existe | Variable local para shadows |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Border-radius | `24px !important` | Cards (⚠️ usa !important) |
| Spacing | `1rem`, `1.5rem`, `0.75rem`, `0.5rem` | Padding, gap |
| Shadow | `0 2px 8px rgba(0, 0, 0, 0.1)` | Card elevation |
| Max-width | `1200px` | Container width |
| Min/Max heights | `250px`, `400px` | Mobile constraints |

---

## Análisis

### ✅ Aspectos Positivos

1. **CSS muy limpio y minimalista** - Solo 79 líneas
2. **Pocos colores hardcoded** - Casi no tiene problemas de color
3. **Grid responsive bien implementado** - 2 columnas → 1 columna en mobile

### ⚠️ Problemas Encontrados

1. **Uso de !important:**
   ```css
   border-radius: 24px !important;
   min-height: 250px !important;
   max-height: 400px !important;
   ```
   Esto sugiere que hay conflictos con otros estilos.

2. **Selector específico de otro bloque:**
   ```css
   .promo-card--pdf-enabled::before { display: none !important; }
   .promo-card--pdf-enabled::after { display: none !important; }
   ```
   Está ocultando estilos de `pdf-download-modal.css`. Esto es un "hack" que indica dependencia/conflicto entre bloques.

3. **Max-width hardcoded:** `1200px` debería usar el layout de theme.json

4. **Border-radius inconsistente:** Usa `24px` cuando otros bloques usan `12px` o `8px`

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.promo-cards__container {
  max-width: 1200px;
}

.promo-card {
  border-radius: 24px !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* DESPUÉS */
.promo-cards__container {
  max-width: var(--wp--style--global--wide-size);
}

.promo-card {
  border-radius: var(--promo-card-border-radius);
  box-shadow: var(--promo-card-shadow);
}
```

### Variables locales necesarias:

```css
.promo-cards {
  /* Layout */
  --promo-cards-max-width: var(--wp--style--global--wide-size);
  --promo-cards-gap-desktop: 1.5rem;
  --promo-cards-gap-mobile: 1rem;
  --promo-cards-gap-xs: 0.75rem;

  /* Card styling */
  --promo-card-border-radius: 24px;
  --promo-card-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

  /* Card dimensions */
  --promo-card-min-height-mobile: 250px;
  --promo-card-max-height-mobile: 400px;

  /* Spacing */
  --promo-cards-padding-desktop: 1rem;
  --promo-cards-padding-mobile: 0.5rem;
}
```

### Eliminar !important

**Investigar por qué se necesitan estos !important:**

1. `border-radius: 24px !important` - ¿Qué lo está sobrescribiendo?
2. `min-height: 250px !important` - ¿Hay otro estilo de altura?
3. `max-height: 400px !important` - ¿Conflicto con image height?

**Solución recomendada:** Aumentar especificidad del selector en lugar de usar !important.

### Resolver conflicto con pdf-download-modal.css

**Problema actual:**
```css
/* Hack para ocultar estilos de otro bloque */
.promo-card--pdf-enabled::before { display: none !important; }
.promo-card--pdf-enabled::after { display: none !important; }
```

**Solución recomendada:**
- Opción A: Modificar `pdf-download-modal.css` para que no aplique a `.promo-card`
- Opción B: Usar selectores más específicos en `pdf-download-modal.css`
- Opción C: Crear una clase `.promo-card--no-pdf-overlay` más explícita

---

## CSS Personalizado Necesario: **MÍNIMO**

**Razón:**
- Bloque muy simple
- Solo necesita variables para spacing y shadows
- Grid usa CSS estándar

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.promo-card` y `.promo-cards__`. Sigue metodología BEM.

---

## Accesibilidad

✅ **BUENO:**
- Usa cursor: pointer para interactividad
- Block element (no link) con display: block

❌ **FALTA:**
- No hay estados `:focus` o `:focus-visible`
- No hay `prefers-reduced-motion` (aunque no tiene animaciones complejas)

### Mejora sugerida:

```css
.promo-card--clickable:focus,
.promo-card--pdf-enabled:focus {
  outline: 2px solid var(--wp--preset--color--primary);
  outline-offset: 2px;
}

.promo-card--clickable:focus:not(:focus-visible) {
  outline: none;
}
```

---

## Responsive Design: ✅ BUENO

- ✅ Grid 2 columnas → 1 columna en 768px
- ✅ Gaps reducidos progresivamente (1.5rem → 1rem → 0.75rem)
- ✅ Min/max heights en mobile para control de aspecto
- ✅ Padding reducido en mobile

---

## Image Handling: ✅ OK

```css
.promo-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
```

Usa `object-fit: cover` correctamente para mantener aspect ratio.

---

## Integración con otros bloques

⚠️ **CONFLICTO DETECTADO:**

Este bloque tiene que "pelear" contra estilos de `pdf-download-modal.css`:

```css
/* Este es un código defensivo */
.promo-card--pdf-enabled::before { display: none !important; }
.promo-card--pdf-enabled::after { display: none !important; }
```

**Investigar:** ¿Por qué `pdf-download-modal.css` está afectando a `.promo-card`?

---

## Próximos Pasos

1. 🔍 **URGENTE:** Investigar conflicto con `pdf-download-modal.css`
2. Eliminar todos los `!important` y resolver conflictos de especificidad
3. Crear variables locales para spacing y shadows
4. Usar `var(--wp--style--global--wide-size)` para max-width
5. Agregar estados `:focus` para accesibilidad
6. Considerar si border-radius de 24px es intencional o debe alinearse con otros bloques (12px)
7. Testing de interactividad (clickable, pdf-enabled)
8. Commit: `refactor(promo-cards): remove !important, resolve pdf modal conflict`

---

## Notas Adicionales

**Clasificadores de variante:**
- `.promo-card--pdf-enabled` - Card con funcionalidad PDF
- `.promo-card--clickable` - Card clickeable

Esto sugiere que el bloque soporta diferentes modos de interacción. Asegurar que ambos tengan accesibilidad apropiada (keyboard, screen readers).
