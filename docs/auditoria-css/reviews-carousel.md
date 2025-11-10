# Auditoría: ReviewsCarousel (Mini Reviews List)

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/reviews-carousel.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

**NOTA:** Este archivo se llama "reviews-carousel" pero el código implementa "mini-reviews-list" (lista vertical simple, NO carousel).

---

## Variables CSS Usadas

**USA variables CSS** con fallbacks hardcodeados.

### Variables con fallbacks:

| Variable CSS | Fallback | Uso |
|--------------|----------|-----|
| `var(--color-gray-50, #FAFAFA)` | `#FAFAFA` | Card background |
| `var(--color-gray-100, #F5F5F5)` | `#F5F5F5` | Card hover, placeholder |
| `var(--color-gray-200, #EEEEEE)` | `#EEEEEE` | Avatar background |
| `var(--color-gray-600, #757575)` | `#757575` | Country text, placeholder |
| `var(--color-gray-700, #616161)` | `#616161` | Review text |
| `var(--color-gray-900, #212121)` | `#212121` | Author name |
| `var(--border-radius-md, 6px)` | `6px` | Card, placeholder |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| Ninguno directo | - | - | - |

**✅ BUENA PRÁCTICA:** No hay colores hardcodeados sin variables.

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `0.875rem`, `0.8125rem`, `0.75rem`, `0.6875rem` | Varios tamaños de texto |
| Spacing | `1.5rem`, `1rem`, `0.875rem`, `0.75rem`, `0.5rem`, `4px`, `2px` | Padding, margin, gap |
| Avatar size | `32px` | Avatar dimensions |
| Box-shadow | `0 2px 8px rgba(0, 0, 0, 0.08)` | Card hover |
| Transition | `all 0.3s ease` | Hover effect |

---

## Análisis

### ✅ Bloque Bien Estructurado

Este es uno de los bloques **mejor implementados**:

**Características positivas:**
- ✅ Usa variables CSS para TODOS los colores
- ✅ Sistema de grises coherente
- ✅ No hay colores hardcodeados
- ✅ Simple y enfocado (solo lista vertical)
- ✅ Buenos selectores con prefijo `.mini-review-`
- ✅ Responsive design
- ✅ Include print styles

**PROBLEMA MENOR:**
- ⚠️ Nombre del archivo no coincide con la funcionalidad (reviews-carousel vs mini-reviews-list)

### Decisión Requerida

**Opción A:** Renombrar archivo a `mini-reviews-list.css`
**Opción B:** Mantener nombre actual (por compatibilidad)

**Recomendación:** Opción B - Mantener por compatibilidad, documentar discrepancia

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* MAPEO A THEME.JSON */

/* ANTES */
.mini-review-card {
  background: var(--color-gray-50, #FAFAFA);
}

.mini-review-author-name {
  color: var(--color-gray-900, #212121);
}

/* DESPUÉS */
.mini-review-card {
  background: var(--wp--preset--color--base);
}

.mini-review-author-name {
  color: var(--wp--preset--color--contrast);
}
```

**NOTA:** Los grises intermedios (gray-50, gray-100, etc.) no existen en theme.json actual. Opciones:
1. Agregar grises a theme.json
2. Mantener variables custom
3. Aproximar a los existentes

### Variables locales necesarias:

```css
.mini-reviews-list {
  /* Typography scale */
  --mr-text-xs: 0.6875rem;
  --mr-text-sm: 0.75rem;
  --mr-text-base: 0.8125rem;
  --mr-text-md: 0.875rem;

  /* Spacing */
  --mr-spacing-xs: 0.5rem;
  --mr-spacing-sm: 0.75rem;
  --mr-spacing-md: 0.875rem;
  --mr-spacing-lg: 1rem;
  --mr-spacing-xl: 1.5rem;

  /* Avatar */
  --mr-avatar-size: 32px;

  /* Shadow */
  --mr-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);

  /* Transition */
  --mr-transition: all 0.3s ease;
}
```

---

## CSS Personalizado Necesario: **MÍNIMO**

**Razón:** Bloque muy simple con diseño básico. Solo necesita variables para typography y spacing.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.mini-review-`, no hay conflictos globales.

---

## Próximos Pasos

1. ⚡ **PRIORIDAD BAJA** - Bloque funciona muy bien
2. (Opcional) Mapear grises a theme.json si se agregan
3. (Opcional) Crear variables locales para typography y spacing
4. Documentar discrepancia de nombre (reviews-carousel vs mini-reviews-list)
5. Testing en editor y frontend
6. Commit: `refactor(reviews-carousel): add local CSS variables, document naming`

---

## Notas Adicionales

Este bloque es un **excelente ejemplo** de cómo implementar CSS con variables correctamente. Puede servir de referencia para refactorizar otros bloques.
