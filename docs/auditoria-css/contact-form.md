# Auditoría: ContactForm

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/contact-form.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**Define sus propias variables en :root** - No usa variables de theme.json.

### Variables propias definidas:

| Variable | Valor | ¿Existe en theme.json? | Variable theme.json equivalente |
|----------|-------|------------------------|--------------------------------|
| `--rose` | #E78C85 | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `--green-dark` | #0A797E | ❌ No existe | Similar a Primary (#17565C) pero diferente |
| `--green-strong` | #A8F04C | ❌ No existe | **PROBLEMA:** Color verde fuerte no está en theme.json |
| `--green-soft` | #EBFED3 | ❌ No existe | **PROBLEMA:** Color verde suave no está en theme.json |
| `--text-dark` | #1F2937 | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `--text-gray` | #6B7280 | ❌ No exacto | Usar `var(--wp--preset--color--gray)` (#666666) |
| `--placeholder` | #94A3B8 | ❌ No existe | Crear variable local o usar gray con opacity |
| `--legal-text` | #CBD5E1 | ❌ No existe | Crear variable local |
| `--border-light` | #E5E7EB | ❌ No existe | Crear variable local |

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `rgba(206, 212, 218, 0.85)` | `.hero-form__card` background | ❌ No existe | Crear variable local |
| `#e78c85` | `.btn-cta` background | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#e07a73` | `.btn-cta:hover` | ❌ No existe | **PROBLEMA:** Color coral hover no está en theme.json |
| `rgba(10, 121, 126, 0.1)` | Input focus shadow | ❌ No existe | Derivado de --green-dark |
| `rgba(231, 140, 133, 0.3)` | Button shadow | ❌ No existe | Derivado de --rose |
| `rgba(0, 0, 0, 0.15)`, etc. | Múltiples shadows | ❌ N/A | Shadows - variables locales |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `22px`, `14px`, `15px`, `11px`, `19px`, `26px` | Tamaños de texto |
| Spacing | `30px`, `40px`, `60px`, `20px`, `12px`, `16px`, `14px`, `10px`, `6px`, `8px` | Padding, margin, gap |
| Border-radius | `26px`, `10px`, `9999px` | Redondeo de bordes |
| Transition | `0.2s ease`, `0.3s ease`, `all 0.3s ease` | Efectos de transición |
| Box-shadow | Múltiples valores | Sombras de elementos |
| Heights | `700px`, `600px`, `500px`, `40px`, `42px`, `50px`, `60px` | Alturas de elementos |

---

## Análisis

### ⚠️ Problemas Principales

1. **Paleta de colores personalizada**: El bloque define su propia paleta de colores que **NO existe en theme.json**:
   - Coral/Rose (#E78C85) - usado para botones y acentos
   - Verde oscuro (#0A797E) - usado para focus states
   - Verde fuerte (#A8F04C) y Verde suave (#EBFED3) - definidos pero no usados visiblemente

2. **No integración con theme.json**: No usa ninguna variable de theme.json para colores, spacing o font-sizes.

3. **Variables en :root**: Define variables CSS globales en `:root` que podrían causar conflictos con otros bloques o el tema.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**ContactForm usa:**
- Rose: #E78C85 (coral)
- Green-dark: #0A797E (teal diferente)

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir los colores del formulario (coral #E78C85, green-dark #0A797E, etc.)
2. **Opción B:** Cambiar ContactForm para usar Primary (#17565C) y Secondary (#C66E65) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque (`.hero-form { --color-primary: ... }`)

**Recomendación:** Opción C - Usar variables locales en el selector del bloque para evitar conflictos globales.

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
:root {
    --rose: #E78C85;
    --green-dark: #0A797E;
    /* ... */
}

/* DESPUÉS */
.hero-form {
    /* Variables locales del bloque */
    --hero-form-primary: var(--wp--preset--color--secondary, #C66E65);
    --hero-form-text: var(--wp--preset--color--contrast, #111111);
    --hero-form-text-muted: var(--wp--preset--color--gray, #666666);

    /* Variables locales específicas que no están en theme.json */
    --hero-form-bg-card: rgba(206, 212, 218, 0.85);
    --hero-form-border-light: #E5E7EB;
    --hero-form-placeholder: #94A3B8;

    /* Spacing */
    --hero-form-spacing-xs: var(--wp--preset--spacing--20, 0.25rem);
    --hero-form-spacing-sm: var(--wp--preset--spacing--30, 0.5rem);
    --hero-form-spacing-md: var(--wp--preset--spacing--50, 1rem);

    /* Transitions */
    --hero-form-transition-fast: 0.2s ease;
    --hero-form-transition-normal: 0.3s ease;
}
```

### Variables locales necesarias:

```css
.hero-form {
    /* Colores - Intentar usar theme.json donde sea posible */
    --hero-form-primary: var(--wp--preset--color--secondary);
    --hero-form-text-dark: var(--wp--preset--color--contrast);
    --hero-form-text-gray: var(--wp--preset--color--gray);

    /* Colores específicos del formulario (no en theme.json) */
    --hero-form-bg-card: rgba(206, 212, 218, 0.85);
    --hero-form-border: #E5E7EB;
    --hero-form-placeholder: #94A3B8;

    /* Spacing */
    --hero-form-card-padding: 30px 40px;
    --hero-form-card-margin: 60px;
    --hero-form-field-gap: 12px;

    /* Transitions */
    --hero-form-transition: 0.2s ease;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** El bloque requiere:
1. Variables locales para colores específicos del formulario
2. Variables para spacing que theme.json no soporta adecuadamente
3. Variables para transitions, shadows y border-radius
4. Diseño complejo con overlays, glassmorphism y estados específicos

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.hero-form`, no hay conflictos globales.

**EXCEPCIÓN:** Las variables están definidas en `:root` (líneas 13-38), lo que crea riesgo de conflictos globales.

**Acción:** Mover todas las variables de `:root` a `.hero-form`.

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral vs theme.json secondary)
2. Mover variables de `:root` a `.hero-form` para evitar conflictos globales
3. Reemplazar colores hardcodeados por variables locales
4. Intentar usar variables de theme.json donde sea posible (text colors, spacing base)
5. Testing en editor y frontend
6. Commit: `refactor(contact-form): use scoped CSS variables, improve theme.json integration`
