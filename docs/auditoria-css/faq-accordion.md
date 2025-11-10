# Auditoría: FAQ Accordion

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/faq-accordion.css`
**Categoría:** Bloque ACF / Package (compartido)
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores y valores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#333` | `.faq-accordion__title`, `.faq-accordion__question` | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#666` | `.faq-accordion__description` | ✅ Sí | Usar `var(--wp--preset--color--gray)` (#666666) |
| `#e0e0e0` | `.faq-accordion__item` border | ❌ No existe | Crear variable local o usar gray con opacity |
| `#fff` | `.faq-accordion__item` background | ✅ Sí | Usar `var(--wp--preset--color--base)` (#FFFFFF) |
| `#f8f9fa` | `.faq-accordion__question:hover` background | ❌ No existe | Crear variable local o usar base con tint |
| `#3498db` | `.faq-accordion__question:focus` outline | ❌ No existe | **PROBLEMA:** Color azul no está en theme.json |
| `#e74c3c` | `.faq-accordion__icon` color | ❌ No existe | **PROBLEMA:** Color rojo no está en theme.json |
| `#555` | `.faq-accordion__answer-inner` color | ❌ No existe | Usar gray o crear variable local |
| `#999` | `.faq-accordion__empty` color | ❌ No existe | Usar gray con opacity |
| `rgba(0, 0, 0, 0.08)` | Hover box-shadow | ❌ N/A | Crear variable local |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `clamp(1.875rem, 3vw, 2.5rem)`, `1.125rem`, `1rem`, `0.9375rem` | Tamaños de texto |
| Spacing | `3rem`, `2.5rem`, `2rem`, `1.5rem`, `1.25rem`, `1rem`, `0.5rem`, `8px` | Margin, padding, gap |
| Border-radius | `8px` | Redondeo de bordes |
| Transition | `0.3s ease`, `0.2s ease` | Efectos de transición |
| Max-width | `900px`, `700px` | Anchos máximos |
| Line-height | `1.2`, `1.5`, `1.6`, `1.7` | Alturas de línea |
| Transforms | `rotate(45deg)` | Rotación del icono |

---

## Análisis

### ⚠️ Problemas Principales

1. **Colores de acento no alineados**: El bloque usa:
   - **Azul** (#3498db) para focus states - **NO existe en theme.json**
   - **Rojo** (#e74c3c) para el icono - **NO existe en theme.json**

2. **No integración con theme.json**: No usa ninguna variable de theme.json.

3. **Colores genéricos**: Usa colores genéricos (#333, #666, #555, #999) que tienen equivalentes en theme.json.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)
- Gray: #666666
- Contrast: #111111
- Base: #FFFFFF

**FAQAccordion usa:**
- Azul #3498db (focus outline)
- Rojo #e74c3c (icon)
- Grises varios (#333, #666, #555, #999)

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir los colores de acento (azul, rojo)
2. **Opción B:** Cambiar FAQAccordion para usar Primary/Secondary de theme.json:
   - Icon color: usar Secondary (#C66E65)
   - Focus outline: usar Primary (#17565C)
3. **Opción C:** Crear variables locales dentro del bloque

**Recomendación:** Opción B - Alinear con theme.json usando Secondary para icon y Primary para focus.

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.faq-accordion__title {
  color: #333;
}

.faq-accordion__description {
  color: #666;
}

.faq-accordion__question:focus {
  outline: 2px solid #3498db;
}

.faq-accordion__icon {
  color: #e74c3c;
}

/* DESPUÉS */
.faq-accordion__title {
  color: var(--wp--preset--color--contrast);
}

.faq-accordion__description {
  color: var(--wp--preset--color--gray);
}

.faq-accordion__question:focus {
  outline: 2px solid var(--wp--preset--color--primary);
}

.faq-accordion__icon {
  color: var(--wp--preset--color--secondary);
}
```

### Variables locales necesarias:

```css
.faq-accordion {
  /* Colores de theme.json */
  --faq-title-color: var(--wp--preset--color--contrast);
  --faq-text-color: var(--wp--preset--color--gray);
  --faq-bg: var(--wp--preset--color--base);
  --faq-icon-color: var(--wp--preset--color--secondary);
  --faq-focus-color: var(--wp--preset--color--primary);

  /* Colores locales (no en theme.json) */
  --faq-border-color: #e0e0e0;
  --faq-hover-bg: #f8f9fa;
  --faq-answer-color: #555;
  --faq-empty-color: #999;

  /* Spacing */
  --faq-spacing-xs: var(--wp--preset--spacing--20, 0.25rem);
  --faq-spacing-sm: var(--wp--preset--spacing--30, 0.5rem);
  --faq-spacing-md: var(--wp--preset--spacing--50, 1rem);
  --faq-spacing-lg: 2rem;
  --faq-spacing-xl: 3rem;

  /* Transitions */
  --faq-transition-fast: 0.2s ease;
  --faq-transition-normal: 0.3s ease;

  /* Border radius */
  --faq-radius: 8px;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Necesita variables locales para:
1. Spacing específico del accordion (padding de items, gaps)
2. Transitions para animaciones del icono y contenido
3. Border radius consistente
4. Colores de estados hover y empty que no están en theme.json

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.faq-accordion`, no hay conflictos globales.

---

## Próximos Pasos

1. Reemplazar colores hardcodeados por variables de theme.json donde sea posible
2. Cambiar icon color (#e74c3c) a Secondary (#C66E65)
3. Cambiar focus outline (#3498db) a Primary (#17565C)
4. Crear variables locales para spacing, transitions y border-radius
5. Crear variables locales para colores que no están en theme.json (border, hover-bg, etc.)
6. Testing en editor y frontend
7. Commit: `refactor(faq-accordion): use theme.json colors, add scoped CSS variables`
