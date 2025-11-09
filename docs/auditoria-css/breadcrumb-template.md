# Auditoría: Breadcrumb (Template)

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/template/breadcrumb.css`
**Categoría:** Bloque Template
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#666` | `.breadcrumb-item`, `.breadcrumb-item a` | ✅ Sí | `var(--wp--preset--color--gray)` (#666666) |
| `#E78C85` | `.breadcrumb-item a:hover` | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#333` | `span[aria-current="page"]` | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#999` | `.breadcrumb-item:not(:last-child)::after` | ❌ No existe | Crear variable local o usar gray con opacity |
| `#f9f9f9` | `.editor-styles-wrapper .breadcrumb-navigation` (editor) | ❌ No existe | Variable local para backgrounds |
| `#e0e0e0` | Border en editor | ❌ No existe | Variable local para borders |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `14px`, `13px`, `12px`, `11px` | Tamaños de texto responsive |
| Spacing | `12px`, `16px`, `8px`, `6px` | Padding, margin, gaps |
| Transition | `0.2s ease` | Hover effect |
| Font-weight | `500` | Current page weight |
| Border-radius | `4px` | Editor preview |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **#E78C85 (coral)** para hover, pero este color **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota) - Similar al coral
- Contrast-4: #311A42 (purple)

**Breadcrumb usa:**
- Hover: #E78C85 (coral) ← No en theme.json

### Selectores

✅ **BUENOS:** Todos los selectores usan `.breadcrumb-` como prefijo, excepto algunos específicos que necesitan revisión.

⚠️ **OBSERVACIÓN:** Usa selectores basados en atributos ARIA (`span[aria-current="page"]`), lo cual es bueno para accesibilidad pero podría complementarse con clase.

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.breadcrumb-item a {
  color: #666;
}

.breadcrumb-item a:hover {
  color: #E78C85;
}

/* DESPUÉS */
.breadcrumb-item a {
  color: var(--wp--preset--color--gray);
}

.breadcrumb-item a:hover {
  color: var(--wp--preset--color--secondary); /* #C66E65 - Similar al coral */
}
```

### Variables locales necesarias:

```css
.breadcrumb-navigation {
  /* Spacing local */
  --breadcrumb-spacing-xs: 0.375rem; /* 6px */
  --breadcrumb-spacing-sm: 0.5rem;   /* 8px */
  --breadcrumb-spacing-md: 0.75rem;  /* 12px */
  --breadcrumb-spacing-lg: 1rem;     /* 16px */

  /* Transitions */
  --breadcrumb-transition: 0.2s ease;

  /* Editor backgrounds */
  --breadcrumb-editor-bg: #f9f9f9;
  --breadcrumb-editor-border: #e0e0e0;
}
```

### Mapeo de font-sizes a theme.json:

| Actual | Theme.json equivalente |
|--------|------------------------|
| `14px` | `var(--wp--preset--font-size--small)` (0.875rem = 14px) |
| `13px` | Crear variable local `--breadcrumb-font-size-mobile: 0.8125rem` |
| `12px` | `var(--wp--preset--font-size--tiny)` (0.75rem = 12px) |
| `11px` | Crear variable local `--breadcrumb-font-size-xs: 0.6875rem` |

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Necesita variables locales para spacing específicos, transitions, y font-sizes intermedios que theme.json no cubre.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.breadcrumb`, no hay conflictos globales.

---

## Accesibilidad: ✅ EXCELENTE

- ✅ Usa `aria-current="page"` para página actual
- ✅ Implementa `prefers-reduced-motion` para transitions
- ✅ Estructura semántica con `<nav>` y lista

---

## Próximos Pasos

1. ❓ **Decidir color hover:** Usar Secondary (#C66E65) o agregar coral a theme.json
2. Reemplazar colores hardcodeados por variables de theme.json
3. Crear variables locales para spacing y transitions
4. Mapear font-sizes a theme.json o crear variables locales
5. Testing en editor y frontend
6. Commit: `refactor(breadcrumb-template): use theme.json variables`
