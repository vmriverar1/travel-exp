# Auditoría: Package Map

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/package-map.css`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa valores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | Línea | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|-------|------------------------|--------------------------------|
| `#f5f5f5` | Figure background | 13 | ❌ No existe | Crear variable local |
| `#f0f0f0` | Caption background | 28 | ❌ No existe | Crear variable local |
| `#4CAF50` | Caption border (green) | 29 | ❌ No existe | Crear variable local para success/info |
| `#666` | Caption text | 31 | ✅ Similar | Usar `var(--wp--preset--color--gray)` (#666666) |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Border-radius | `8px`, `4px` | Figure borders |
| Height | `330px` | Image height |
| Spacing | `12px` | Margin, padding |
| Border | `3px solid` | Caption left border |
| Font-size | `13px` | Caption text |

---

## Análisis

### ✅ Bloque Simple

Este es el bloque **MÁS SIMPLE** auditado hasta ahora:
- Solo **46 líneas** de CSS
- Principalmente un contenedor de imagen
- Estilos mínimos
- Caption solo visible en preview mode

### Problemas Identificados

1. **NO usa variables CSS:** Todo está hardcodeado
2. **Green border:** Usa #4CAF50 (Material Design green) que no está en theme.json
3. **Grays:** Usa #f5f5f5, #f0f0f0, #666 que no están definidos en theme.json
4. **Border-radius:** No usa variables para border-radius

### Impacto

- **BAJO:** Bloque muy simple, fácil de refactorizar
- **BAJO:** No usa colores de brand (coral/purple)
- **MÍNIMO:** El caption solo se muestra en preview mode del editor

---

## Plan de Refactorización

### Variables locales necesarias

```css
.package-map-wrapper {
    /* Colors */
    --pkgmap-bg: #F5F5F5;
    --pkgmap-caption-bg: #F0F0F0;
    --pkgmap-caption-border: #4CAF50; /* Green success color */
    --pkgmap-caption-text: var(--wp--preset--color--gray); /* #666666 */

    /* Sizing */
    --pkgmap-height: 330px;

    /* Border radius */
    --pkgmap-radius: 8px;
    --pkgmap-radius-sm: 4px;

    /* Spacing */
    --pkgmap-spacing: 12px;

    /* Border */
    --pkgmap-border-width: 3px;
}
```

### Código refactorizado

```css
/* ANTES */
.package-map-figure {
    border-radius: 8px;
    background: #f5f5f5;
}

.package-map-image {
    height: 330px;
}

.package-map-preview .package-map-caption {
    margin-top: 12px;
    padding: 12px;
    background: #f0f0f0;
    border-left: 3px solid #4CAF50;
    font-size: 13px;
    color: #666;
    border-radius: 4px;
}

/* DESPUÉS */
.package-map-figure {
    border-radius: var(--pkgmap-radius);
    background: var(--pkgmap-bg);
}

.package-map-image {
    height: var(--pkgmap-height);
}

.package-map-preview .package-map-caption {
    margin-top: var(--pkgmap-spacing);
    padding: var(--pkgmap-spacing);
    background: var(--pkgmap-caption-bg);
    border-left: var(--pkgmap-border-width) solid var(--pkgmap-caption-border);
    font-size: 13px; /* Keep as is or create variable */
    color: var(--pkgmap-caption-text);
    border-radius: var(--pkgmap-radius-sm);
}
```

---

## CSS Personalizado Necesario: **SÍ (MÍNIMO)**

**Razones:**
1. Necesita variables locales para colores de fondo
2. Necesita green color para caption border (no está en theme.json)
3. Necesita border-radius values
4. Necesita height específico para imagen
5. Caption solo visible en modo preview

**Complejidad:** MUY BAJA - Bloque extremadamente simple

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.package-map`, no hay conflictos globales.

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. Crear variables locales con prefijo `--pkgmap-`
3. Reemplazar colores hardcodeados por variables
4. Usar `--wp--preset--color--gray` para caption text
5. Testing en editor (preview mode) y frontend
6. Considerar si el green border debería usar otro color de theme.json
7. Commit: `refactor(package-map): migrate to CSS variables`

---

## Notas Adicionales

**Buenas prácticas encontradas:**
- ✅ Selectores bien scoped con prefijo `.package-map`
- ✅ Responsive design (mobile border-radius adjustment)
- ✅ Preview mode separado del frontend
- ✅ Object-fit: cover para imágenes

**Características del bloque:**
- **Muy simple:** Solo muestra una imagen de mapa
- **Fixed height:** 330px de altura
- **Caption preview:** Solo visible en el editor
- **Green border:** Indica que es información/preview

**Mejoras recomendadas:**
- Considerar si el caption green border debería existir o usar otro color
- Evaluar si la altura fija (330px) debería ser configurable
- El green (#4CAF50) es Material Design green - verificar si es apropiado para el tema
- Considerar usar aspect-ratio en lugar de height fijo

**Prioridad de refactorización:** BAJA
- Es un bloque muy simple
- No usa colores de brand problemáticos (coral/purple)
- El único color no-standard es el green del caption que solo se ve en preview
