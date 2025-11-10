# Auditoría: FAQAccordion (Template)

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/faq-accordion.css`
**Categoría:** Bloque Template
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#333` | `.faq-accordion__title`, `.faq-accordion__question` | ❌ No exacto | `var(--wp--preset--color--contrast)` (#111111) |
| `#666` | `.faq-accordion__description` | ✅ Sí | `var(--wp--preset--color--gray)` (#666666) |
| `#555` | `.faq-accordion__answer-inner` | ❌ No existe | Crear variable local o usar gray |
| `#999` | `.faq-accordion__empty` | ❌ No existe | Usar gray con opacity |
| `#e0e0e0` | Border de items | ❌ No existe | Variable local para borders |
| `#fff` | Background de items | ✅ Sí | `var(--wp--preset--color--base)` |
| `#f8f9fa` | Hover background | ❌ No existe | Variable local para hover states |
| `#3498db` | Focus outline | ❌ No existe | Usar Primary o crear variable |
| `#e74c3c` | Icon color (rojo) | ❌ No existe | **PROBLEMA:** Color rojo no está en theme.json |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `clamp(...)`, `1.125rem`, `1rem`, `0.9375rem` | Tamaños de texto |
| Spacing | `3rem`, `2.5rem`, `2rem`, `1.5rem`, `1.25rem`, `1rem` | Margins, paddings |
| Border-radius | `8px` | Items redondeados |
| Transition | `0.3s ease`, `0.2s ease` | Animations |
| Font-weight | `700`, `600` | Text weights |
| Max-width | `900px`, `700px` | Container widths |

---

## Análisis

### ⚠️ Problemas Principales

1. **Color del icono:** Usa `#e74c3c` (rojo) que no existe en theme.json
2. **Colores de texto inconsistentes:** Usa #333, #555, #666 en lugar de las variables del tema
3. **Focus color:** Usa `#3498db` (azul) que no está en la paleta del tema

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)
- Contrast: #111111 (negro)
- Gray: #666666

**FAQ Accordion usa:**
- Icon: #e74c3c (rojo) ← No en theme.json
- Focus: #3498db (azul) ← No en theme.json

### Decisión Requerida

1. **Opción A:** Cambiar icon color a Secondary (#C66E65)
2. **Opción B:** Cambiar icon color a Primary (#17565C)
3. **Opción C:** Agregar rojo (#e74c3c) a theme.json

**Recomendación:** Opción A - Usar Secondary color

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.faq-accordion__question {
  color: #333;
}

.faq-accordion__icon {
  color: #e74c3c;
}

.faq-accordion__question:focus {
  outline: 2px solid #3498db;
}

/* DESPUÉS */
.faq-accordion__question {
  color: var(--wp--preset--color--contrast);
}

.faq-accordion__icon {
  color: var(--wp--preset--color--secondary);
}

.faq-accordion__question:focus {
  outline: 2px solid var(--wp--preset--color--primary);
}
```

### Variables locales necesarias:

```css
.faq-accordion {
  /* Colors */
  --faq-text-secondary: var(--wp--preset--color--gray);
  --faq-border-color: #e0e0e0;
  --faq-hover-bg: #f8f9fa;

  /* Spacing */
  --faq-spacing-xs: 0.5rem;   /* 8px */
  --faq-spacing-sm: 1rem;     /* 16px */
  --faq-spacing-md: 1.5rem;   /* 24px */
  --faq-spacing-lg: 2rem;     /* 32px */
  --faq-spacing-xl: 2.5rem;   /* 40px */
  --faq-spacing-2xl: 3rem;    /* 48px */

  /* Transitions */
  --faq-transition-fast: 0.2s ease;
  --faq-transition-normal: 0.3s ease;

  /* Border radius */
  --faq-border-radius: 8px;

  /* Container */
  --faq-max-width: 900px;
  --faq-description-max-width: 700px;
}
```

### Mapeo de font-sizes a theme.json:

| Actual | Theme.json equivalente |
|--------|------------------------|
| `clamp(1.875rem, 3vw, 2.5rem)` | Usar fluid typography de theme.json |
| `1.125rem` (18px) | Crear variable o usar medium |
| `1rem` (16px) | `var(--wp--preset--font-size--regular)` |
| `0.9375rem` (15px) | Crear variable local |

---

## CSS Personalizado Necesario: **SÍ**

**Razón:**
- Variables locales para colores de UI (borders, hovers)
- Spacing específico del componente
- Transitions y animaciones
- Border radius consistente

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.faq-accordion__`, siguiendo metodología BEM. No hay conflictos globales.

---

## Accesibilidad: ✅ EXCELENTE

- ✅ Usa `<button>` para preguntas (keyboard accessible)
- ✅ Implementa estados `:hover` y `:focus`
- ✅ Usa `[hidden]` para contenido colapsado
- ✅ State class `.is-open` para acordeón abierto
- ✅ Estilos de impresión para mostrar todas las respuestas
- ❌ **FALTA:** No implementa `prefers-reduced-motion` para animaciones

### Mejora sugerida:

```css
@media (prefers-reduced-motion: reduce) {
  .faq-accordion__icon,
  .faq-accordion__icon-vertical,
  .faq-accordion__question {
    transition: none;
  }
}
```

---

## Responsive Design: ✅ BUENO

- ✅ Ajusta spacing en mobile (768px)
- ✅ Reduce font-sizes en mobile
- ✅ Mantiene estructura funcional

---

## Próximos Pasos

1. ❓ **Decidir color del icono:** Secondary, Primary, o agregar rojo a theme.json
2. Reemplazar colores hardcodeados por variables de theme.json
3. Crear variables locales para UI colors y spacing
4. Agregar `prefers-reduced-motion` para accesibilidad
5. Mapear font-sizes a theme.json
6. Testing en editor y frontend
7. Commit: `refactor(faq-accordion): use theme.json variables and improve a11y`
