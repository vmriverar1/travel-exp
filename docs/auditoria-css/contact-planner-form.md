# Auditoría: Contact Planner Form

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/contact-planner-form.css`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS con fallbacks**, pero las variables principales NO existen en theme.json.

### Variables encontradas:

| Variable CSS | Fallback | Líneas | ¿Existe en theme.json? | Variable theme.json equivalente |
|--------------|----------|---------|------------------------|--------------------------------|
| `--color-coral` | `#E78C85` | 71, 137, 189 | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json, usar `--wp--preset--color--secondary` (#C66E65) |
| `--color-coral-dark` | `#D97369` | 202 | ❌ No existe | **PROBLEMA:** Color coral dark no está en theme.json |
| `--color-gray-900` | `#212121` | 65, 129 | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `--color-gray-800` | `#424242` | 117 | ❌ No existe | Crear variable local o usar contrast con opacity |
| `--color-gray-700` | `#616161` | 176 | ❌ No existe | Crear variable local |
| `--color-gray-600` | `#757575` | 76 | ❌ No existe | Usar `var(--wp--preset--color--gray)` (#666666) |
| `--color-gray-500` | `#9E9E9E` | 142 | ❌ No existe | Crear variable local |
| `--color-gray-400` | `#BDBDBD` | 212 | ❌ No existe | Crear variable local |
| `--color-gray-300` | `#E0E0E0` | 126 | ❌ No existe | Crear variable local |
| `--color-success` | `#4CAF50` | 237, 238 | ❌ No existe | Crear variable local |
| `--color-success-light` | `#E8F5E9` | 236 | ❌ No existe | Crear variable local |
| `--color-error` | `#F44336` | 243, 244 | ❌ No existe | Crear variable local |
| `--color-error-light` | `#FFEBEE` | 242 | ❌ No existe | Crear variable local |
| `--border-radius-lg` | `12px` | 42 | ❌ No existe | Crear variable local |
| `--border-radius-md` | `6px` | 192, 230 | ❌ No existe | Crear variable local |
| `--border-radius-sm` | `4px` | 127 | ❌ No existe | Crear variable local |

### Valores hardcodeados (sin variables):

| Tipo | Valor | Uso | Línea |
|------|-------|-----|-------|
| Color | `white` | Panel background, button text | 41, 190, 262 |
| Color | `rgba(0, 0, 0, 0.2)` | Box shadow | 43 |
| Color | `rgba(231, 140, 133, 0.1)` | Focus shadow (coral) | 138 |
| Color | `rgba(231, 140, 133, 0.3)` | Hover shadow (coral) | 204 |
| Color | `transparent` | Border, loading text | 263, 251 |
| Spacing | `600px`, `500px` | Min-height | 15, 276 |
| Spacing | `4rem`, `3rem`, `2.5rem`, `2rem`, `1.5rem`, `1rem` | Padding, margin, gap | Multiple |
| Font-size | `2rem`, `1.125rem`, `0.875rem`, `0.9375rem`, `1rem` | Text sizes | Multiple |
| Transition | `0.3s`, `0.3s ease`, `0.8s linear infinite` | Transitions, animations | 130, 197, 265 |
| Box-shadow | `0 20px 40px`, `0 8px 16px`, `0 0 0 3px` | Shadows | 43, 138, 204 |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa la **paleta Coral** de variables personalizadas, pero estos colores **NO existen en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**Contact Planner Form usa:**
- Coral: #E78C85
- Coral Dark: #D97369

### Problemas Adicionales

1. **Escala de grises personalizada:** Usa una escala de grises completa (`gray-300` a `gray-900`) que no existe en theme.json
2. **Colores de estado:** Necesita success/error que theme.json no provee
3. **Border radius:** Usa tres tamaños de border-radius que no están definidos globalmente
4. **Valores RGBA con coral:** Usa rgba con valores hardcodeados del coral en sombras

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85) y la escala de grises completa
2. **Opción B:** Cambiar formulario para usar Secondary (#C66E65) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque para todos los colores personalizados

**Recomendación:** Opción C + B combinadas:
- Usar `--wp--preset--color--secondary` para el color principal (coral → secondary)
- Crear variables locales para grises, success/error, y border-radius
- Usar variables CSS custom properties en la raíz del bloque

---

## Plan de Refactorización

### Paso 1: Reemplazar color coral por secondary

```css
/* ANTES */
.contact-planner-form__title .highlight {
    color: var(--color-coral, #E78C85);
}

/* DESPUÉS */
.contact-planner-form__title .highlight {
    color: var(--wp--preset--color--secondary); /* #C66E65 */
}
```

### Paso 2: Variables locales necesarias

```css
.contact-planner-form {
    /* Colors */
    --cpf-color-primary: var(--wp--preset--color--secondary);
    --cpf-color-primary-dark: #B35D54; /* Versión oscura de secondary */

    /* Grays - derivados de theme.json donde sea posible */
    --cpf-gray-900: var(--wp--preset--color--contrast); /* #111111 */
    --cpf-gray-800: #424242;
    --cpf-gray-700: #616161;
    --cpf-gray-600: var(--wp--preset--color--gray); /* #666666 */
    --cpf-gray-500: #9E9E9E;
    --cpf-gray-400: #BDBDBD;
    --cpf-gray-300: #E0E0E0;

    /* Status colors */
    --cpf-success: #4CAF50;
    --cpf-success-light: #E8F5E9;
    --cpf-error: #F44336;
    --cpf-error-light: #FFEBEE;

    /* Border radius */
    --cpf-radius-lg: 12px;
    --cpf-radius-md: 6px;
    --cpf-radius-sm: 4px;

    /* Spacing */
    --cpf-spacing-xs: 0.5rem;
    --cpf-spacing-sm: 1rem;
    --cpf-spacing-md: 1.5rem;
    --cpf-spacing-lg: 2rem;
    --cpf-spacing-xl: 2.5rem;
    --cpf-spacing-2xl: 3rem;
    --cpf-spacing-3xl: 4rem;

    /* Transitions */
    --cpf-transition-fast: 0.3s;
    --cpf-transition-slow: 0.8s linear;
}
```

### Paso 3: Actualizar shadows con RGBA usando secondary

```css
/* ANTES */
.contact-planner-form__field input:focus {
    box-shadow: 0 0 0 3px rgba(231, 140, 133, 0.1);
}

/* DESPUÉS */
.contact-planner-form__field input:focus {
    box-shadow: 0 0 0 3px rgba(198, 110, 101, 0.1); /* Secondary con alpha */
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razones:**
1. Necesita variables locales para la escala de grises completa
2. Necesita colores de estado (success/error) para mensajes de formulario
3. Necesita múltiples valores de border-radius
4. Necesita valores de spacing específicos no disponibles en theme.json
5. Necesita transiciones personalizadas

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.contact-planner-form`, no hay conflictos globales.

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. Crear variables locales con prefijo `--cpf-`
3. Reemplazar `--color-coral` por `--wp--preset--color--secondary`
4. Actualizar valores RGBA en shadows para usar secondary (#C66E65)
5. Reemplazar todos los usos de las variables antiguas por las nuevas
6. Testing en editor y frontend
7. Commit: `refactor(contact-planner-form): migrate to theme.json colors and local variables`

---

## Notas Adicionales

**Buenas prácticas encontradas:**
- ✅ Usa variables CSS con fallbacks
- ✅ Selectores bien scoped con prefijo `.contact-planner-form`
- ✅ Responsive design con media queries
- ✅ Estados de accesibilidad (focus, hover, active, disabled)
- ✅ Print styles para ocultar en impresión
- ✅ Loading state con animación

**Mejoras recomendadas:**
- Considerar usar `color-mix()` para generar variantes de colores en lugar de hardcodear
- Usar calc() para spacing basado en una escala base
