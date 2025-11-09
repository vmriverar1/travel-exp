# Auditoría: StaticCTA

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/static-cta.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#000` | `.static-cta__overlay` | ⚠️ Similar | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#fff` | `.static-cta__title`, `.static-cta__subtitle`, botones | ⚠️ Usar semantic | Usar `var(--wp--preset--color--base)` |
| `#e74c3c` | `.btn-primary` | ❌ No existe | **PROBLEMA:** Color rojo no está en theme.json |
| `#c0392b` | `.btn-primary:hover` | ❌ No existe | **PROBLEMA:** Color rojo dark no está en theme.json |
| `#3498db` | `.btn-secondary` | ❌ No existe | **PROBLEMA:** Color azul no está en theme.json |
| `#2980b9` | `.btn-secondary:hover` | ❌ No existe | **PROBLEMA:** Color azul dark no está en theme.json |
| `#333` | `.btn-outline:hover` | ⚠️ Similar | Usar `var(--wp--preset--color--contrast)` con opacity |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `clamp(2rem, 4vw, 3.5rem)`, `clamp(1rem, 2vw, 1.375rem)`, `1.125rem` | Title, subtitle, button |
| Spacing | `4rem 2rem`, `3rem 1.5rem`, `1rem 2.5rem`, `1rem`, `2.5rem` | Padding, margin, gap |
| Border-radius | `4px` | Buttons |
| Transition | `0.3s ease` | Hover effects |
| Box-shadow | `0 4px 6px rgba(0, 0, 0, 0.2)`, `0 6px 12px rgba(0, 0, 0, 0.3)` | Buttons elevation |
| Text-shadow | `0 2px 4px rgba(0, 0, 0, 0.3)`, `0 1px 3px rgba(0, 0, 0, 0.3)` | Text elevation |
| Min-height | `400px`, `350px` | CTA height |
| Max-width | `900px`, `700px` | Content width |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **colores completamente diferentes** a theme.json:

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**StaticCTA usa:**
- Primary: #e74c3c (rojo brillante) ❌
- Secondary: #3498db (azul brillante) ❌

Estos colores parecen ser **genéricos de placeholder** y no están alineados con la identidad visual del sitio.

### Otros Problemas

1. **No usa sistema de colores del tema:** Los colores rojo/azul no coinciden con ninguna paleta del proyecto
2. **Usa `clamp()` para responsive fonts:** Esto está bien, pero debería considerar usar `--wp--preset--font-size--*` como base
3. **Botones genéricos:** Los nombres `.btn-primary`, `.btn-secondary`, `.btn-outline` pueden colisionar con otros estilos globales

### Decisión Requerida

1. **Opción A:** Reemplazar completamente los colores para usar theme.json
2. **Opción B:** Crear variables locales que mapeen a theme.json
3. **Opción C:** Permitir colores customizables vía inline styles

**Recomendación:** Opción A - Reemplazar con theme.json (cambio crítico)

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.static-cta__button.btn-primary {
  background-color: #e74c3c;
  color: #fff;
}

.static-cta__button.btn-primary:hover {
  background-color: #c0392b;
}

.static-cta__button.btn-secondary {
  background-color: #3498db;
  color: #fff;
}

/* DESPUÉS */
.static-cta__button.btn-primary {
  background-color: var(--wp--preset--color--primary); /* #17565C */
  color: var(--wp--preset--color--base);
}

.static-cta__button.btn-primary:hover {
  background-color: color-mix(in srgb, var(--wp--preset--color--primary) 85%, black);
}

.static-cta__button.btn-secondary {
  background-color: var(--wp--preset--color--secondary); /* #C66E65 */
  color: var(--wp--preset--color--base);
}
```

### Variables locales necesarias:

```css
.static-cta {
  /* Spacing */
  --cta-padding-y: var(--wp--preset--spacing--80, 4rem);
  --cta-padding-x: var(--wp--preset--spacing--60, 2rem);
  --cta-spacing-md: var(--wp--preset--spacing--50, 1.5rem);
  --cta-spacing-lg: var(--wp--preset--spacing--60, 2.5rem);

  /* Typography */
  --cta-title-size: clamp(2rem, 4vw, 3.5rem);
  --cta-subtitle-size: clamp(1rem, 2vw, 1.375rem);

  /* Effects */
  --cta-transition: 0.3s ease;
  --cta-overlay-opacity: 0.4;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:**
- Necesita overlay con opacity personalizada
- Parallax background effect
- Responsive clamp() typography
- Text shadows para legibilidad sobre imágenes
- Box shadows en botones

---

## Selectores Específicos: ⚠️ CUIDADO

- `.static-cta` ✅ OK
- `.btn-primary`, `.btn-secondary`, `.btn-outline` ⚠️ **RIESGO**: Nombres muy genéricos, pueden colisionar con otros estilos

**Recomendación:** Usar `.static-cta__button--primary` en lugar de `.btn-primary`

---

## Próximos Pasos

1. ⚠️ **CRÍTICO:** Reemplazar colores rojo/azul por Primary/Secondary de theme.json
2. Renombrar clases de botones para evitar colisiones (`.btn-*` → `.static-cta__button--*`)
3. Implementar spacing scale de theme.json
4. Considerar agregar más variantes de color si se necesitan
5. Testing de contraste de texto sobre imágenes
6. Testing de parallax en diferentes navegadores
7. Commit: `refactor(static-cta): align with theme.json colors and fix button classes`
