# Auditoría: Packages by Location

**Ruta:** `wp-content/plugins/travel-blocks/src/Blocks/Package/PackagesByLocation.php`
**Categoría:** Bloque Package
**Fecha:** 2025-11-09

---

## ⚠️ NO TIENE ARCHIVO CSS DEDICADO

Este bloque **NO tiene archivo CSS** (`packages-by-location.css` no existe). En su lugar, utiliza **estilos inline** directamente en el archivo PHP.

---

## Estilos Inline Encontrados

### Colores hardcodeados en estilos inline:

| Color Hardcodeado | Dónde se usa | Línea | Problema |
|-------------------|--------------|-------|----------|
| `#f0f0f0` | Preview background | 239 | No usa theme.json |
| `#666` | Description text, metadata | 294, 329 | Similar a gray pero hardcoded |
| `#fff` | Card background | 308 | Debería usar variable |
| `rgba(0,0,0,0.1)` | Card shadow | 308 | Hardcoded |
| `#333` | Title link color | 318 | Similar a contrast pero hardcoded |
| `#555` | Excerpt text | 340 | Hardcoded |
| `#0073aa` | Price color, button background | 346, 351 | **PROBLEMA:** Blue no está en theme.json |
| `#f9f9f9` | Empty state background | 377 | Hardcoded |

### Otros valores inline hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Padding | `3rem 0`, `2rem`, `1.5rem`, `0.75rem`, `1rem` | Multiple |
| Border-radius | `12px`, `8px`, `4px` | Card, image, button |
| Box-shadow | `0 2px 8px rgba(0,0,0,0.1)` | Card shadow |
| Grid columns | `repeat({$columns}, 1fr)` | Grid layout |
| Gap | `2rem`, `1rem` | Grid, flex gaps |
| Font-size | `2rem`, `1.5rem`, `1.25rem`, `1.125rem`, `0.9rem`, `0.875rem` | Text sizes |
| Height | `250px` | Thumbnail height |
| Max-width | `1200px` | Container width |
| Transition | `0.3s` | Card hover |

---

## Análisis

### ❌ PROBLEMA CRÍTICO: ESTILOS INLINE

El bloque utiliza **estilos inline** en lugar de un archivo CSS dedicado. Esto tiene múltiples problemas:

1. **No reutilizable:** Los estilos no se pueden compartir o sobrescribir fácilmente
2. **Rendimiento:** Los estilos se repiten en cada instancia del bloque
3. **Mantenibilidad:** Difícil de actualizar y mantener
4. **No usa theme.json:** Completamente desacoplado del sistema de diseño
5. **Sin variables CSS:** No puede aprovechar variables CSS para temas
6. **Sin responsive:** Los estilos inline son más difíciles de hacer responsive
7. **Sin caching:** Los estilos inline no se cachean por separado

### Colores Problemáticos

- **Blue (#0073aa):** Usa blue de WordPress default que NO está en theme.json
- **Grays varios:** #f0f0f0, #666, #555, #f9f9f9 que no están en theme.json
- **Black variants:** #333 similar a contrast pero hardcoded

### Arquitectura del Bloque

El bloque muestra un grid de package cards con:
- **Featured image** (opcional)
- **Title**
- **Duration** (opcional)
- **Rating** (opcional)
- **Excerpt** (opcional)
- **Price** (opcional)
- **CTA button**
- **Pagination** (opcional)

---

## Plan de Refactorización

### ⚠️ ACCIÓN REQUERIDA: Crear archivo CSS dedicado

Este bloque **NECESITA** un archivo CSS dedicado: `packages-by-location.css`

### Paso 1: Crear archivo CSS

Crear `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/packages-by-location.css`

### Paso 2: Diseño del archivo CSS

```css
/**
 * Packages by Location Block Styles
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

/* ===== VARIABLES ===== */

.packages-by-location-block {
    /* Colors from theme.json */
    --pbl-text: var(--wp--preset--color--contrast);
    --pbl-meta: var(--wp--preset--color--gray);
    --pbl-cta: var(--wp--preset--color--secondary); /* En lugar de blue #0073aa */

    /* Local colors */
    --pbl-bg: #FFFFFF;
    --pbl-preview-bg: #F0F0F0;
    --pbl-empty-bg: #F9F9F9;
    --pbl-excerpt: #555555;

    /* Shadows */
    --pbl-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

    /* Border radius */
    --pbl-radius-lg: 12px;
    --pbl-radius-md: 8px;
    --pbl-radius-sm: 4px;

    /* Spacing */
    --pbl-spacing-xs: 0.75rem;
    --pbl-spacing-sm: 1rem;
    --pbl-spacing-md: 1.5rem;
    --pbl-spacing-lg: 2rem;
    --pbl-spacing-xl: 3rem;

    /* Container */
    --pbl-max-width: 1200px;

    /* Card */
    --pbl-card-height: 250px;

    /* Transitions */
    --pbl-transition: 0.3s ease;
}

/* ===== CONTAINER ===== */

.packages-by-location-block {
    padding: var(--pbl-spacing-xl) 0;
}

/* ===== HEADER ===== */

.pbl-header {
    max-width: var(--pbl-max-width);
    margin: 0 auto var(--pbl-spacing-lg);
    padding: 0 var(--pbl-spacing-sm);
}

.pbl-header__title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--pbl-text);
}

.pbl-header__count {
    color: var(--pbl-meta);
}

/* ===== GRID ===== */

.packages-grid {
    max-width: var(--pbl-max-width);
    margin: 0 auto;
    padding: 0 var(--pbl-spacing-sm);
    display: grid;
    gap: var(--pbl-spacing-lg);
    margin-bottom: var(--pbl-spacing-lg);
}

.packages-grid--2col {
    grid-template-columns: repeat(2, 1fr);
}

.packages-grid--3col {
    grid-template-columns: repeat(3, 1fr);
}

.packages-grid--4col {
    grid-template-columns: repeat(4, 1fr);
}

/* ===== PACKAGE CARD ===== */

.package-card {
    background: var(--pbl-bg);
    border-radius: var(--pbl-radius-lg);
    overflow: hidden;
    box-shadow: var(--pbl-shadow);
    transition: transform var(--pbl-transition);
}

.package-card:hover {
    transform: translateY(-4px);
}

.package-card__image {
    width: 100%;
    height: var(--pbl-card-height);
    object-fit: cover;
}

.package-card__content {
    padding: var(--pbl-spacing-md);
}

.package-card__title {
    margin-bottom: var(--pbl-spacing-xs);
    font-size: 1.25rem;
    line-height: 1.3;
}

.package-card__title a {
    text-decoration: none;
    color: var(--pbl-text);
}

.package-card__meta {
    display: flex;
    gap: var(--pbl-spacing-sm);
    flex-wrap: wrap;
    margin-bottom: var(--pbl-spacing-xs);
    font-size: 0.875rem;
    color: var(--pbl-meta);
}

.package-card__excerpt {
    margin-bottom: var(--pbl-spacing-sm);
    font-size: 0.9rem;
    color: var(--pbl-excerpt);
    line-height: 1.5;
}

.package-card__price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--pbl-cta);
    margin-bottom: var(--pbl-spacing-sm);
}

.package-card__button {
    display: block;
    text-align: center;
    padding: var(--pbl-spacing-xs) var(--pbl-spacing-md);
    background: var(--pbl-cta);
    color: white;
    text-decoration: none;
    border-radius: var(--pbl-radius-sm);
    transition: all var(--pbl-transition);
}

.package-card__button:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

/* ===== EMPTY STATE ===== */

.pbl-empty {
    text-align: center;
    padding: var(--pbl-spacing-xl);
    background: var(--pbl-empty-bg);
    border-radius: var(--pbl-radius-md);
}

.pbl-empty__text {
    font-size: 1.125rem;
    color: var(--pbl-meta);
}

/* ===== PREVIEW MODE ===== */

.pbl-preview {
    padding: var(--pbl-spacing-lg);
    background: var(--pbl-preview-bg);
    text-align: center;
}

/* ===== PAGINATION ===== */

.pbl-pagination {
    text-align: center;
    margin-top: var(--pbl-spacing-lg);
}

/* ===== RESPONSIVE ===== */

@media (max-width: 1023px) {
    .packages-grid--4col {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 767px) {
    .packages-grid--3col,
    .packages-grid--4col {
        grid-template-columns: repeat(2, 1fr);
    }

    .pbl-header__title {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .packages-grid {
        grid-template-columns: 1fr;
    }
}
```

### Paso 3: Actualizar PHP para usar clases

Eliminar todos los estilos inline del PHP y usar las clases CSS:

```php
// ANTES (línea 308)
<div class="package-card" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);transition:transform 0.3s">

// DESPUÉS
<div class="package-card">
```

### Paso 4: Registrar el CSS en PHP

Agregar el registro del archivo CSS en el método de registro del bloque.

---

## CSS Personalizado Necesario: **SÍ (CRÍTICO)**

**Razones:**
1. **Actualmente NO tiene CSS:** Todo es inline
2. Grid layout con 2-4 columnas configurables
3. Card design completo (image, content, button)
4. Responsive design necesario
5. Hover states y transitions
6. Empty state
7. Pagination styling
8. Preview mode para editor

---

## Próximos Pasos

1. ✅ **Auditoría completada**
2. **CRÍTICO:** Crear archivo `packages-by-location.css`
3. Definir variables locales con prefijo `--pbl-`
4. Usar `--wp--preset--color--secondary` para CTA (en lugar de blue)
5. Usar `--wp--preset--color--contrast` para textos
6. Usar `--wp--preset--color--gray` para metadata
7. Eliminar todos los estilos inline del PHP
8. Agregar clases CSS semánticas al markup
9. Registrar CSS en el block registration
10. Testing del grid responsive (2, 3, 4 columnas)
11. Testing de todas las opciones toggleables
12. Testing de paginación
13. Commit: `refactor(packages-by-location): create dedicated CSS file, remove inline styles`

---

## Notas Adicionales

**Problemas encontrados:**
- ❌ NO tiene archivo CSS dedicado
- ❌ Usa estilos inline exclusivamente
- ❌ Blue (#0073aa) hardcoded que no está en theme.json
- ❌ Múltiples grays hardcoded
- ❌ No usa variables CSS de ningún tipo
- ❌ Markup PHP con estilos inline mezclados
- ❌ Difícil de mantener y personalizar

**Características del bloque:**
- **Grid configurable:** 2, 3, o 4 columnas
- **Auto/Manual mode:** Detecta location automáticamente o permite selección manual
- **Opciones de card:** Imagen, precio, duración, rating, excerpt (todos toggleables)
- **Paginación:** Opcional con posts_per_page configurable
- **Query dinámico:** Filtra packages por location usando ACF field

**Prioridad de refactorización:** ALTA
- Este bloque necesita refactorización URGENTE
- Pasar de inline styles a CSS dedicado es crítico
- Es uno de los bloques más importantes (muestra grid de packages)
- El blue (#0073aa) debería cambiarse a secondary (#C66E65)

**Recomendación Final:**
Este es el ÚNICO bloque de los 10 auditados que **NO tiene archivo CSS**. Debería ser prioridad crear el archivo CSS dedicado antes de hacer cualquier otra refactorización de bloques.
