# Auditoría: PackageHeader

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/template/package-header.css`
**Categoría:** Bloque Template
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**✅ USA VARIABLES CSS** - Pero con fallbacks hardcodeados.

### Variables encontradas:

```css
color: var(--color-gray-900, #212121);
color: var(--color-gray-700, #616161);
color: var(--color-gray-600, #757575);
color: var(--color-coral, #E78C85);
```

⚠️ **PROBLEMA:** Usa variables CSS que NO están definidas en theme.json. Son variables custom que probablemente venían de `common-variables.css` o similar.

### Colores encontrados:

| Variable CSS | Fallback | ¿Existe en theme.json? | Variable theme.json equivalente |
|--------------|----------|------------------------|--------------------------------|
| `var(--color-gray-900, #212121)` | `#212121` | ❌ No exacto | `var(--wp--preset--color--contrast)` (#111111) |
| `var(--color-gray-700, #616161)` | `#616161` | ❌ No existe | Crear variable o usar gray con opacity |
| `var(--color-gray-600, #757575)` | `#757575` | ❌ No existe | Crear variable o usar gray con opacity |
| `var(--color-coral, #E78C85)` | `#E78C85` | ❌ No existe | **PROBLEMA:** Coral no está en theme.json |
| `#FFB800` | Estrellas (hardcoded) | ❌ No existe | **PROBLEMA:** Color oro/amarillo no está |
| `#E0E0E0` | Estrellas vacías | ❌ No existe | Variable local para UI elements |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `2.5rem`, `2rem`, `1.75rem`, `1.6rem`, `1rem`, `0.9375rem`, `0.875rem` | Títulos y texto |
| Spacing | `2rem`, `1.5rem`, `1rem`, `0.75rem`, `0.5rem`, `0.25rem` | Margins, paddings, gaps |
| Font-weight | `700`, `600`, `500`, `400` | Text weights |
| Line-height | `1.2`, `1.3`, `1.5`, `1.6` | Line heights |
| Icon sizes | `20px`, `18px` | SVG dimensions |
| Border | `1px solid #e0e0e0` | Editor preview |
| Border-radius | `4px` | Editor preview |

---

## Análisis

### ⚠️ Problemas Principales

1. **Variables CSS no alineadas con theme.json:**
   - Usa `--color-gray-900`, `--color-gray-700`, etc. que NO existen en WP
   - Usa `--color-coral` que NO está en theme.json

2. **Sistema de grises no coincide:**
   - theme.json solo tiene `gray` (#666666)
   - PackageHeader necesita 3 tonos: 900, 700, 600

3. **Color de estrellas hardcoded:**
   - Usa `#FFB800` (amarillo) que no existe en theme.json
   - theme.json tiene `contrast-1` (#CEA02D - oro) como alternativa

4. **Font-sizes no mapeados:**
   - Usa valores específicos en rem que no coinciden con theme.json

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)
- Contrast: #111111 (negro)
- Gray: #666666
- Contrast-1: #CEA02D (oro) ← Podría usarse para estrellas

**PackageHeader necesita:**
- Gray-900: #212121 ← No existe
- Gray-700: #616161 ← No existe
- Gray-600: #757575 ← No existe
- Coral: #E78C85 ← No existe
- Amarillo estrellas: #FFB800 ← No existe (usar #CEA02D?)

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.package-header__title {
  color: var(--color-gray-900, #212121);
}

.package-header__subtitle {
  color: var(--color-gray-700, #616161);
}

.metadata-icon {
  color: var(--color-coral, #E78C85);
}

.star--full {
  color: #FFB800;
}

/* DESPUÉS */
.package-header__title {
  color: var(--wp--preset--color--contrast);
}

.package-header__subtitle {
  color: var(--package-header-text-secondary);
}

.metadata-icon {
  color: var(--wp--preset--color--secondary);
}

.star--full {
  color: var(--wp--preset--color--contrast-1); /* #CEA02D - oro */
}
```

### Variables locales necesarias:

```css
.package-header {
  /* Text colors */
  --package-header-text-primary: var(--wp--preset--color--contrast);
  --package-header-text-secondary: #616161;
  --package-header-text-tertiary: #757575;

  /* Icon color */
  --package-header-icon-color: var(--wp--preset--color--secondary);

  /* Stars */
  --package-header-star-filled: var(--wp--preset--color--contrast-1);
  --package-header-star-empty: #E0E0E0;

  /* Spacing */
  --package-header-spacing-xs: 0.25rem;
  --package-header-spacing-sm: 0.5rem;
  --package-header-spacing-md: 0.75rem;
  --package-header-spacing-lg: 1rem;
  --package-header-spacing-xl: 1.5rem;
  --package-header-spacing-2xl: 2rem;

  /* Typography scale */
  --package-header-title-desktop: 2.5rem;
  --package-header-title-tablet: 2rem;
  --package-header-title-mobile: 1.75rem;

  /* Icon sizes */
  --package-header-icon-size: 20px;
  --package-header-icon-size-mobile: 18px;

  /* Editor */
  --package-header-editor-border: #e0e0e0;
  --package-header-editor-radius: 4px;
}
```

### Mapeo de font-sizes a theme.json:

| Actual | Theme.json equivalente | Notas |
|--------|------------------------|-------|
| `2.5rem` | `var(--wp--preset--font-size--huge)` (3rem) | Muy grande, crear variable local |
| `2rem` | `var(--wp--preset--font-size--x-large)` (2.25rem) | Casi coincide |
| `1.75rem` | `var(--wp--preset--font-size--large)` (1.75rem) | ✅ Coincide |
| `1.6rem` | `var(--wp--preset--font-size--extra-medium)` (1.5rem) | Crear variable local |
| `1rem` | `var(--wp--preset--font-size--regular)` (1rem) | ✅ Coincide |
| `0.9375rem` | Crear variable local | No existe en theme.json |
| `0.875rem` | `var(--wp--preset--font-size--small)` (0.875rem) | ✅ Coincide |

---

## CSS Personalizado Necesario: **SÍ**

**Razón:**
- Sistema de grises intermedios no disponible en theme.json
- Font-sizes específicos del componente
- Spacing system propio
- Metadata grid layout

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.package-header__` y `.metadata-`. Sigue metodología BEM.

---

## Accesibilidad: ✅ EXCELENTE

- ✅ `@media (prefers-contrast: high)` implementado para aumentar font-weights
- ✅ `@media (forced-colors: active)` para modo alto contraste
- ✅ Structure semántica con heading levels
- ✅ Icon colors adaptables en forced-colors mode

**Esto es EJEMPLAR.** Uno de los pocos bloques que implementa `prefers-contrast` y `forced-colors`.

---

## Responsive Design: ✅ BUENO

- ✅ Font-sizes escalables con breakpoints (768px, 640px, 480px)
- ✅ Grid de metadata adapta a 1 columna en mobile
- ✅ Icon sizes reducidos en mobile
- ❌ **FALTA:** Considerar `clamp()` para font-sizes fluidos

---

## Grid Layout

⚠️ **OBSERVACIÓN:** Usa `grid-template-columns: repeat(1, 1fr)` que es redundante. Podría simplificarse a `grid-template-columns: 1fr`.

---

## Próximos Pasos

1. ✅ **Mantener accesibilidad:** No tocar `prefers-contrast` y `forced-colors`
2. Eliminar variables CSS custom (`--color-gray-900`, etc.)
3. Crear variables locales para grises intermedios
4. Cambiar coral icon color a Secondary
5. Cambiar estrellas a `contrast-1` (oro de theme.json)
6. Mapear font-sizes a theme.json donde sea posible
7. Considerar fluid typography con `clamp()`
8. Simplificar grid syntax
9. Testing en diferentes modos de accesibilidad
10. Commit: `refactor(package-header): align with theme.json, preserve a11y features`
