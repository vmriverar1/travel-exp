# Auditoría: TaxonomyArchiveHero

**Ruta:** `wp-content/plugins/travel-blocks/src/Blocks/Template/TaxonomyArchiveHero.php`
**Categoría:** Bloque Template
**Fecha:** 2025-11-09

---

## ⚠️ NOTA IMPORTANTE

**Este bloque NO tiene archivo CSS propio.**

Según el archivo PHP (línea 99-104), TaxonomyArchiveHero **reutiliza los estilos de HeroCarousel:**

```php
public function enqueue_assets() {
    wp_enqueue_style(
        'taxonomy-archive-hero-style',
        TRAVEL_BLOCKS_URL . 'assets/blocks/HeroCarousel/style.css',
        [],
        TRAVEL_BLOCKS_VERSION
    );
    // ...
}
```

---

## Análisis

### Arquitectura del Bloque

**TaxonomyArchiveHero** es un **wrapper inteligente** sobre HeroCarousel que:

1. **Reutiliza estilos:** Usa `HeroCarousel/style.css`
2. **Reutiliza templates:** Usa `HeroCarousel/templates/*.php`
3. **Reutiliza JavaScript:** Usa `HeroCarousel/carousel.js` y `HeroCarousel/editor.js`

### Funcionalidad Única

Lo que hace diferente a TaxonomyArchiveHero:

1. **Imagen automática con fallback:**
   - Intenta obtener imagen de taxonomy term actual
   - Si no hay, busca package aleatorio de esa taxonomy
   - Si no hay, busca cualquier package aleatorio
   - Si no hay packages, busca imagen aleatoria de media library
   - Fallback final: Picsum placeholder

2. **Contenido dinámico:**
   - Integra con `ContentQueryHelper` para packages, posts, deals
   - Puede mostrar contenido de taxonomías automáticamente

### Variables y Campos ACF

El bloque tiene **muchos campos ACF** para personalización:

- Layout variations (bottom, top, side_left, side_right)
- Proporción content/cards
- Heights responsivos (mobile, tablet, desktop)
- Negative margins complejos
- Carousel settings (arrows, dots, autoplay)
- Button y badge color variants

**Colores usados en ACF choices (líneas 655-661, 674-680):**
- Primary - Pink (#E78C85) ← **PROBLEMA:** No es el primary de theme.json
- Secondary - Purple (#311A42) ← Coincide con contrast-4
- Gold (#CEA02D) ← Coincide con contrast-1
- Dark (#1A1A1A)
- White, Transparent

---

## Problemas Identificados

### 1. Color Confusion en ACF

Los choices de ACF describen incorrectamente los colores:

```php
'primary' => __('Primary - Pink (#E78C85)', 'acf-gutenberg-rest-blocks'),
'secondary' => __('Secondary - Purple (#311A42)', 'acf-gutenberg-rest-blocks'),
```

**theme.json real:**
- Primary: #17565C (teal) - NO pink
- Secondary: #C66E65 (salmon) - NO purple
- Contrast-4: #311A42 (purple)

**Acción requerida:** Actualizar las descripciones de los campos ACF para reflejar theme.json real.

### 2. Dependencia de HeroCarousel CSS

Cualquier refactorización de `HeroCarousel/style.css` afectará a:
- TaxonomyArchiveHero (Template)
- HeroCarousel (ACF)

**Recomendación:** Auditar `HeroCarousel/style.css` como parte de esta auditoría.

---

## Archivos Relacionados que Necesitan Auditoría

1. **`/assets/blocks/HeroCarousel/style.css`** - Estilos compartidos
2. **`/assets/blocks/HeroCarousel/carousel.js`** - JavaScript del carousel
3. **`/assets/blocks/HeroCarousel/editor.js`** - Editor scripts
4. **`/assets/blocks/HeroCarousel/templates/*.php`** - Templates (bottom.php, top.php, side_left.php, side_right.php)

---

## Variables CSS Esperadas

Como este bloque usa HeroCarousel CSS, esperamos encontrar en `style.css`:

- Variables de color para buttons (primary, secondary, gold, dark)
- Variables de color para badges
- Layout proportions
- Heights responsivos
- Negative margins system
- Carousel controls styling

**TODO:** Crear auditoría de `HeroCarousel/style.css` para confirmar.

---

## Accesibilidad Heredada

Este bloque hereda toda la accesibilidad de HeroCarousel, incluyendo:

- InnerBlocks para contenido flexible
- Soporte para anchor y customClassName
- Soporte para spacing (margin, padding, blockGap)
- Soporte para color (background, text, gradients)
- Soporte para typography

---

## Recomendaciones

### 1. Actualizar ACF Field Descriptions

```php
// ANTES
'primary' => __('Primary - Pink (#E78C85)', 'acf-gutenberg-rest-blocks'),
'secondary' => __('Secondary - Purple (#311A42)', 'acf-gutenberg-rest-blocks'),

// DESPUÉS
'primary' => __('Primary - Teal (#17565C)', 'acf-gutenberg-rest-blocks'),
'secondary' => __('Secondary - Salmon (#C66E65)', 'acf-gutenberg-rest-blocks'),
'contrast-4' => __('Purple (#311A42)', 'acf-gutenberg-rest-blocks'),
```

### 2. Considerar CSS Separado

Si TaxonomyArchiveHero tiene estilos específicos únicos, considerar:

- Crear `taxonomy-archive-hero.css` para estilos específicos
- Mantener `HeroCarousel/style.css` para estilos compartidos
- Evitar duplicación de código

### 3. Documentar Dependencias

Crear una nota en la documentación que explique la relación:

```
TaxonomyArchiveHero (Template)
  └── Depende de HeroCarousel (ACF)
      ├── style.css (compartido)
      ├── carousel.js (compartido)
      └── templates/ (compartidos)
```

---

## Próximos Pasos

1. 📋 **Auditar `HeroCarousel/style.css`** - Es crítico
2. 🔧 Actualizar descripciones ACF para reflejar theme.json real
3. 📝 Documentar arquitectura de bloques compartidos
4. 🧪 Testing de fallback de imágenes (taxonomy → package → media → picsum)
5. 🔍 Verificar que color variants en ACF coincidan con implementación CSS
6. Commit: `docs(taxonomy-archive-hero): document shared architecture with HeroCarousel`

---

## Conclusión

**TaxonomyArchiveHero NO necesita auditoría CSS propia**, pero requiere:

1. Auditoría de `HeroCarousel/style.css` (archivo compartido)
2. Corrección de descripciones ACF
3. Documentación de arquitectura compartida

Este bloque es un **buen ejemplo de reutilización de código**, pero necesita mejor documentación y alineación con theme.json.
