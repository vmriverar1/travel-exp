# Auditoría: Breadcrumb

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/breadcrumb.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#666` | `.breadcrumb__link` | ❌ No exacto | Usar `var(--wp--preset--color--gray)` (#666666) |
| `#E78C85` | `.breadcrumb__link:hover`, color variants | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#d97a74` | `.breadcrumb--color-primary:hover` | ❌ No existe | **PROBLEMA:** Color coral dark no está en theme.json |
| `#333` | `.breadcrumb__text` | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) o crear variable local |
| `#311A42` | `.breadcrumb--color-secondary` | ❌ No existe | **PROBLEMA:** Color purple no está en theme.json |
| `#999` | `.breadcrumb__separator` | ❌ No existe | Crear variable local o usar gray con opacity |
| `#1A1A1A` | `.breadcrumb--color-dark` | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `14px`, `13px`, `12px`, `11px` | Tamaños de texto |
| Spacing | `30px`, `12px`, `24px`, `8px`, `16px` | Padding, margin, gap |
| Transition | `0.3s ease` | Hover effect |
| Font-weight | `500`, `600` | Text weight |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa la **paleta Coral/Purple** de `common-variables.css`, pero estos colores **NO existen en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**Breadcrumb usa:**
- Primary: #E78C85 (coral)
- Secondary: #311A42 (purple)

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85) y purple (#311A42)
2. **Opción B:** Cambiar breadcrumb para usar Primary (#17565C) y Secondary (#C66E65) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque para coral/purple

**Recomendación:** Opción B - Alinear con theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.breadcrumb__link {
  color: #666;
}

.breadcrumb__link:hover {
  color: #E78C85;
}

/* DESPUÉS */
.breadcrumb__link {
  color: var(--wp--preset--color--gray);
}

.breadcrumb__link:hover {
  color: var(--wp--preset--color--secondary); /* #C66E65 */
}
```

### Variables locales necesarias (si no se actualiza theme.json):

```css
.breadcrumb {
  /* Spacing local */
  --breadcrumb-spacing-sm: 0.5rem; /* 8px */
  --breadcrumb-spacing-md: 1rem;   /* 16px */
  --breadcrumb-spacing-lg: 1.875rem; /* 30px */

  /* Transitions */
  --breadcrumb-transition: 0.3s ease;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Necesita variables locales para spacing y transitions que theme.json no soporta.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.breadcrumb`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral vs theme.json)
2. Reemplazar colores hardcodeados por variables de theme.json
3. Crear variables locales para spacing y transitions
4. Testing en editor y frontend
5. Commit: `refactor(breadcrumb): remove color dependencies, use theme.json`
