# Auditoría: Metadata Line

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/metadata-line.css`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS con fallbacks**, pero las variables principales NO existen en theme.json.

### Variables encontradas:

| Variable CSS | Fallback | Líneas | ¿Existe en theme.json? | Variable theme.json equivalente |
|--------------|----------|---------|------------------------|--------------------------------|
| `--color-gray-700` | `#616161` | 53 | ❌ No existe | Crear variable local |
| `--color-gray-600` | `#757575` | 57 | ❌ No existe | Usar `var(--wp--preset--color--gray)` (#666666) |
| `--color-coral` | `#E78C85` | 62, 66 | ❌ No existe | **PROBLEMA:** Usar `--wp--preset--color--secondary` (#C66E65) |
| `--color-coral-light` | `#FFF0EF` | 70 | ❌ No existe | **PROBLEMA:** Derivar de secondary |
| `--color-purple` | `#311A42` | 75 | ❌ No existe | **PROBLEMA:** Usar `--wp--preset--color--primary` (#17565C) |
| `--color-purple-light` | `#4A2B5E` | 79, 83 | ❌ No existe | **PROBLEMA:** Derivar de primary |

### Valores hardcodeados:

**NO hay colores hardcodeados** - Todos usan variables con fallbacks ✅

### Spacing y sizing:

| Tipo | Valores |
|------|---------|
| Padding | `1.5rem`, `1rem` |
| Gap | `1rem`, `1.5rem`, `1.25rem`, `0.875rem`, `0.75rem`, `0.5rem` |
| Font-size | `0.875rem`, `0.9375rem` |
| Font-weight | `500`, `600`, `700` |
| Grid columns | `repeat(1, 1fr)`, `repeat(2, 1fr)`, `1fr` |

---

## Análisis

### ⚠️ PROBLEMA CRÍTICO

El bloque define **3 variantes de color** que usan la **paleta Coral/Purple** legacy que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**Metadata Line usa:**
- **Primary variant:** Coral #E78C85 + Coral Light #FFF0EF
- **Secondary variant:** Purple #311A42 + Purple Light #4A2B5E
- **Default variant:** Grays #616161, #757575

### Sistema de Variantes

El bloque tiene 3 variantes de color configurables:

1. **Default (Gray):**
   - Text: #616161 (gray-700)
   - Icons: #757575 (gray-600)

2. **Primary (Coral):**
   - Text & Icons: #E78C85 (coral)
   - Separator: #FFF0EF (coral-light)

3. **Secondary (Purple):**
   - Text: #311A42 (purple)
   - Icons: #4A2B5E (purple-light)
   - Separator: #4A2B5E (purple-light)

### Problemas Identificados

1. **Nomenclatura confusa:** El bloque llama "primary" al coral y "secondary" al purple, pero en theme.json primary es teal y secondary es salmon
2. **Paleta legacy completa:** Usa coral (#E78C85) y purple (#311A42) que no existen en theme.json
3. **Light variants:** Usa variantes light (#FFF0EF, #4A2B5E) que tampoco existen
4. **Sin default en theme.json:** No hay una variante "default" gray en theme.json

### Impacto

- **ALTO:** Las variantes de color son configurables por el usuario en el editor
- **ALTO:** Cambiar los colores afectará la apariencia visual elegida por los usuarios
- **MEDIO:** Es un bloque simple (128 líneas) pero con 3 variantes de color

---

## Plan de Refactorización

### Opción A: Remapear completamente a theme.json (Recomendada)

Cambiar la nomenclatura y colores para alinear con theme.json:

| Variante Actual | Nuevo Nombre | Nuevo Color |
|-----------------|--------------|-------------|
| Default (Gray) | Default | Mantener grays |
| Primary (Coral) | Primary | Usar Secondary #C66E65 (salmon) |
| Secondary (Purple) | Secondary | Usar Primary #17565C (teal) |

**Nota:** Esto invierte la nomenclatura actual pero alinea con theme.json

### Opción B: Mantener nomenclatura, actualizar colores

Mantener los nombres actuales pero usar colores de theme.json:

| Variante Actual | Color Actual | Nuevo Color |
|-----------------|--------------|-------------|
| Primary | Coral #E78C85 | Secondary #C66E65 |
| Secondary | Purple #311A42 | Primary #17565C |

### Variables locales necesarias (Opción A)

```css
.metadata-line {
    /* Default variant (gray) */
    --metadata-default-text: var(--metadata-gray-700);
    --metadata-default-icon: var(--wp--preset--color--gray); /* #666666 */

    /* Primary variant (salmon - from theme.json secondary) */
    --metadata-primary-color: var(--wp--preset--color--secondary); /* #C66E65 */
    --metadata-primary-light: #FFF3F2; /* Derivado de secondary */

    /* Secondary variant (teal - from theme.json primary) */
    --metadata-secondary-color: var(--wp--preset--color--primary); /* #17565C */
    --metadata-secondary-light: #E0F2F1; /* Derivado de primary */

    /* Local grays */
    --metadata-gray-700: #616161;
    --metadata-gray-600: #757575;
}
```

### Código refactorizado

```css
/* ANTES */
.metadata-line__content--primary {
    color: var(--color-coral, #E78C85);
}

.metadata-line__content--primary .metadata-line__item svg {
    color: var(--color-coral, #E78C85);
}

.metadata-line__content--primary .metadata-line__separator {
    color: var(--color-coral-light, #FFF0EF);
}

/* DESPUÉS */
.metadata-line__content--primary {
    color: var(--metadata-primary-color);
}

.metadata-line__content--primary .metadata-line__item svg {
    color: var(--metadata-primary-color);
}

.metadata-line__content--primary .metadata-line__separator {
    color: var(--metadata-primary-light);
}
```

```css
/* ANTES */
.metadata-line__content--secondary {
    color: var(--color-purple, #311A42);
}

.metadata-line__content--secondary .metadata-line__item svg {
    color: var(--color-purple-light, #4A2B5E);
}

/* DESPUÉS */
.metadata-line__content--secondary {
    color: var(--metadata-secondary-color);
}

.metadata-line__content--secondary .metadata-line__item svg {
    color: var(--metadata-secondary-light);
}
```

---

## CSS Personalizado Necesario: **SÍ (MÍNIMO)**

**Razones:**
1. Necesita variables locales para las 3 variantes de color
2. Necesita derivar colores light para separadores
3. Necesita grays para variante default
4. Sistema de grid responsive simple

**Complejidad:** BAJA - Es un bloque simple de metadata con iconos

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.metadata-line`, no hay conflictos globales.

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. **DECISIÓN CRÍTICA:** Elegir Opción A o B (remapear vs mantener nomenclatura)
3. Crear variables locales con prefijo `--metadata-`
4. Reemplazar `--color-coral` por `--wp--preset--color--secondary`
5. Reemplazar `--color-purple` por `--wp--preset--color--primary`
6. Derivar colores light con opacity o color-mix()
7. Testing de las 3 variantes (default, primary, secondary)
8. Testing responsive (3 breakpoints)
9. Commit: `refactor(metadata-line): migrate color variants to theme.json`

---

## Notas Adicionales

**Buenas prácticas encontradas:**
- ✅ Usa variables CSS con fallbacks consistentemente
- ✅ NO tiene colores hardcodeados (todos usan variables)
- ✅ Selectores bien scoped con prefijo `.metadata-line`
- ✅ Responsive design con 3 breakpoints
- ✅ Grid layout flexible
- ✅ Accessibility: high contrast mode support
- ✅ Print styles

**Características del bloque:**
- **Simple y clean:** Solo 128 líneas
- **3 variantes de color:** Default, Primary, Secondary
- **Iconos:** Map pin, backpack, clock, users
- **Responsive grid:** 1 columna mobile, 2 tablet, variado en desktop
- **Accessibility:** Soporte para modo de alto contraste

**Consideraciones importantes:**
- ⚠️ **Decisión de diseño necesaria:** La inversión de nomenclatura (Opción A) es más correcta técnicamente pero puede confundir a usuarios que ya eligieron colores
- ⚠️ **Breaking change potencial:** Cambiar los colores afectará el diseño de páginas existentes
- ✅ **Migración simple:** Es uno de los bloques más simples de migrar (solo 6 variables de color)

**Recomendación:**
1. **Si no hay contenido en producción:** Usar Opción A (remapear completamente)
2. **Si hay contenido en producción:** Considerar crear un script de migración para actualizar las clases de variantes en posts existentes
3. Documentar el cambio en changelog para usuarios

**Mejoras adicionales:**
- Considerar usar `color-mix()` para derivar los colores light automáticamente
- Los separadores actualmente no se muestran (display: none en línea 46), evaluar si son necesarios
