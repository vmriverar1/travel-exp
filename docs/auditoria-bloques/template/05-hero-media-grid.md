# Auditoría: Hero Media Grid (Template)

**Fecha:** 2025-11-09
**Bloque:** 5/X Template
**Tiempo:** 45 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `hero-media-grid`
- **Namespace:** `travel/hero-media-grid` (Template block)
- **Métodos públicos:** Todos heredados de TemplateBlockBase
- **Clases CSS:** `.hero-media-grid`, `.hero-gallery`, `.hero-media-grid__container`, `.hero-media-grid__sidebar`
- **Campos ACF usados:** `gallery`, `map_image`, `video_url`, `price_normal`, `price_offer`, `physical_difficulty`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Herencia de `TemplateBlockBase` - es para usar en Query Loop
- Método `render_live()` recibe `$post_id` como parámetro
- Template contiene CSS y JS inline que deberían estar en archivos separados

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Template/HeroMediaGrid.php`
**Namespace:** `Travel\Blocks\Blocks\Template`
**Template:** `/wp-content/plugins/travel-blocks/templates/template/hero-media-grid.php`
**Assets:**
- CSS: `/assets/blocks/template/hero-media-grid.css` (403 líneas)
- JS: `/assets/blocks/template/hero-media-grid.js` (14 líneas - casi vacío)

**Tipo:** [ ] ACF  [X] Gutenberg Nativo (Template Block)

---

## 2. Propósito y Funcionalidad

**Descripción:** Bloque hero que combina una galería carousel (65% ancho) con un mapa y video apilados (35% ancho) en un layout de grid.

**Inputs:**
- Ninguno (sin campos ACF, bloque nativo)
- Recibe `$post_id` del contexto de Query Loop
- Obtiene datos de ACF fields del package: `gallery`, `map_image`, `video_url`, `price_normal`, `price_offer`, `physical_difficulty`

**Outputs:**
- Grid layout 65/35 con galería Swiper + mapa/video
- Discount badge rotado si hay precio oferta
- Activity level indicator con dots
- Lightbox para galería y mapa (GLightbox)
- Video embed (YouTube/Vimeo)

**Contextos soportados:**
- Solo packages individuales en Query Loop templates

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `TemplateBlockBase`
- Implementa: Ninguna
- Traits: `PreviewDataTrait`

**Propiedades:**
```
Heredadas de TemplateBlockBase (name, title, description, icon, keywords)
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor, configura propiedades del bloque
2. render_preview(array $attributes): string - Renderiza preview con datos de ejemplo
3. render_live(int $post_id, array $attributes): string - Renderiza hero grid real
4. enqueue_assets(): void - Encola CSS y JS del bloque
```

**Métodos Privados:**
```
1. get_package_gallery(int $post_id): array - Obtiene imágenes de galería del package
2. get_package_map_image(int $post_id): string - Obtiene imagen del mapa
3. get_package_video_url(int $post_id): string - Obtiene URL del video
4. get_package_discount(int $post_id): array - Calcula descuento y badge
```

---

## 4. Registro del Bloque

**Método:** Heredado de `TemplateBlockBase`

**Configuración:**
- name: `hero-media-grid`
- title: "Hero Media Grid"
- description: "Gallery carousel with map and video in split layout"
- icon: `format-gallery`
- keywords: ['hero', 'gallery', 'map', 'video', 'carousel', 'media']

**Block.json:** No existe

**Categoría:** Heredada de TemplateBlockBase

---

## 5. Campos ACF

**Definición:** No define campos - Consume campos existentes del post type `package`:
- `gallery` (Gallery ACF field)
- `map_image` (Image ACF field)
- `video_url` (URL ACF field)
- `price_normal` (Number ACF field)
- `price_offer` (Number ACF field)
- `physical_difficulty` (Select ACF field)

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render_preview()` o `render_live()` según contexto

**Obtención de Datos:**

**Preview:**
1. Llama `get_preview_images(6)` - retorna 6 imágenes de ejemplo
2. Genera datos hardcoded:
   - gallery: 6 imágenes de picsum.photos
   - map_image: Imagen de ejemplo
   - video_url: YouTube video de ejemplo
   - discount_badge: 15% Early Bird Discount

**Live:**
1. Recibe `$post_id` del contexto de Query Loop
2. Llama métodos privados para obtener cada dato:
   - `get_package_gallery($post_id)` - Procesa ACF gallery field
   - `get_package_map_image($post_id)` - Extrae URL de image field
   - `get_package_video_url($post_id)` - Obtiene video URL
   - `get_package_discount($post_id)` - Calcula % descuento

**Procesamiento:**

**get_package_gallery():**
1. Obtiene ACF gallery field
2. Valida que sea array no vacío
3. Itera y extrae url, alt, title de cada imagen
4. Retorna array estructurado

**get_package_discount():**
1. Obtiene price_normal y price_offer
2. Valida que offer sea menor que normal
3. Calcula porcentaje: `round((($normal - $offer) / $normal) * 100)`
4. Retorna array con show, percentage, text

**Variables al Template:**
```php
- $gallery: array - Imágenes de galería [{url, alt, title}, ...]
- $map_image: string - URL de imagen de mapa
- $video_url: string - URL de video (YouTube/Vimeo)
- $discount_badge: array - ['show' => bool, 'percentage' => int, 'text' => string]
- $is_preview: bool - Modo preview
```

**Lógica en Template:**

⚠️ **PROBLEMA CRÍTICO:** Template contiene demasiada lógica:

1. **CSS Inline (líneas 19-48):** CSS duplicado que ya existe en archivo .css
2. **JS Inline (líneas 215-280):** Inicialización de Swiper/GLightbox que debería estar en .js
3. **Lógica de physical_difficulty (líneas 88-116):** Mapeo de dificultad que debería estar en clase PHP
4. **Regex de video parsing (líneas 183-195):** Parsing de YouTube/Vimeo que debería estar en clase PHP

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:**
- ⚠️ **Archivo JS vacío** - hero-media-grid.js solo tiene placeholder
- ✅ JS real está inline en template (líneas 215-280)
- Inicializa Swiper carousel (loop, fade, autoplay)
- Inicializa GLightbox para galería
- Event listener para botón "View All Photos"

**REST API:** No usa

**Hooks Propios:** No define

**Dependencias Externas:**
- `TemplateBlockBase` (core framework)
- `PreviewDataTrait` (core framework)
- Swiper.js (frontend - cargado globalmente)
- GLightbox (frontend - cargado globalmente)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ⚠️ **VIOLA PARCIALMENTE**
- **Clase PHP:** ✅ Cumple - Solo maneja data preparation
- **Template:** ❌ VIOLA - Tiene responsabilidades de:
  - Presentación (HTML)
  - Estilos (CSS inline)
  - Comportamiento (JS inline)
  - Lógica de negocio (physical_difficulty mapping, video parsing)

**OCP:** ✅ Cumple - Puede extenderse sin modificar

**LSP:** ✅ Cumple - Respeta contrato de TemplateBlockBase

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA** - Dependencias concretas
- Ubicación: Métodos get_package_* (líneas 65-139)
- Impacto: Medio (dificulta testing)
- Funciones directas: get_field() sin abstracción
- Template: get_the_ID(), get_field() directamente (líneas 93-95)

### 8.2 Problemas Clean Code

**Complejidad:**

**Clase PHP:**
- ✅ Métodos muy cortos: Todos <25 líneas
- ✅ get_package_gallery() - 20 líneas (65-85)
- ✅ get_package_discount() - 18 líneas (122-139)
- ✅ Lógica clara y simple

**Template:**
- ❌ **ALTO:** Template demasiado complejo (282 líneas)
- ❌ Bloque de lógica PHP de 29 líneas (88-116) - Debería estar en clase
- ❌ Bloque JS de 66 líneas (215-280) - Debería estar en archivo .js
- ❌ Bloque CSS de 30 líneas (19-48) - Debería estar en archivo .css

**Anidación:**
- ✅ Clase PHP: Máximo 2 niveles - Excelente
- ⚠️ Template: Máximo 4 niveles (foreach > if > if) - Aceptable pero alto

**Duplicación:**

1. ⚠️ **CSS duplicado en template**
   - CSS inline (líneas 19-48) duplica reglas de hero-media-grid.css
   - Impacto: ALTO - Mantenimiento duplicado, peso innecesario

2. ⚠️ **Lógica de video parsing podría ser reutilizable**
   - Template líneas 183-195: Regex YouTube/Vimeo
   - No verificado si otros bloques tienen lógica similar
   - Impacto: MEDIO - Candidato a helper function

**Nombres:**
- ✅ Clase PHP: Nombres muy claros y descriptivos
- ✅ get_package_gallery, get_package_map_image muy explícitos
- ⚠️ Template: Variables genéricas ($video_embed, $video_id)

**Código Sin Uso:**
- ❌ **hero-media-grid.js está prácticamente vacío**
  - Ubicación: Lines 8-13
  - Contenido: Solo placeholder comment
  - Impacto: BAJO - Archivo innecesario, debería tener el código del template o eliminarse

### 8.3 Problemas de Seguridad

**Sanitización:**

✅ **Clase PHP bien sanitizada:**
- `$post_id` es int (type hint)
- `get_field()` retorna datos ya sanitizados por ACF
- Conversión a float con `(float)` para precios (líneas 124-125)

❌ **Template tiene problemas:**
- **CRÍTICO:** Línea 197 `echo $video_embed;` sin escapar
  - Contiene iframe construido con sprintf
  - Aunque usa esc_attr() para $video_id, el echo final no está escapado
  - Permite XSS si ACF field es manipulado
- ⚠️ Regex sin validación previa de $video_url (líneas 183-195)
  - No verifica que $video_url sea string válido antes de preg_match
  - Podría generar warnings

**Escapado:**

✅ **Mayoría bien escapado:**
- esc_url() para URLs (líneas 71, 73, 160, 162)
- esc_attr() para atributos (líneas 74, 163, 187, 193)
- esc_html() para texto (líneas 58, 60, 84, 137, 171, 204)

❌ **CRÍTICO - Sin escapar:**
- Línea 197: `echo $video_embed;` - Debería ser `echo wp_kses_post($video_embed);`

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas, solo funciones WP/ACF

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\Template`
  - Esperado: `Travel\Blocks\Template`
  - Ubicación: Línea 11
  - Impacto: Bajo (funciona pero no sigue convención PSR-4)
  - **NOTA:** Mismo problema que otros bloques Template

**Separación MVC:**
- ✅ Clase PHP: Controller bien separado
- ❌ **Template: VIOLA completamente MVC**
  - Mezcla View + Controller (lógica PHP) + Assets (CSS/JS)
  - CSS debería estar solo en .css file
  - JS debería estar solo en .js file
  - Lógica de physical_difficulty debería estar en clase PHP
  - Video parsing debería estar en clase PHP

**Acoplamiento:**
- ✅ Clase PHP: Bajo acoplamiento con base classes
- ❌ Template: Alto acoplamiento con:
  - get_field() directo (línea 95)
  - get_the_ID() directo (línea 94)
  - Swiper global (línea 237)
  - GLightbox global (línea 254)

**Otros:**

❌ **CRÍTICO: Lógica de negocio en template**
- **Physical difficulty mapping (líneas 88-116):**
  - 29 líneas de lógica PHP en template
  - Debería ser método privado en clase: `get_package_physical_difficulty()`
  - Mapeo hardcoded de values a labels y dots
  - Impacto: ALTO - Dificulta testing y mantenimiento

❌ **CRÍTICO: Assets inline en template**
- **CSS inline (líneas 19-48):**
  - 30 líneas de CSS que duplican hero-media-grid.css
  - Comentario dice "Force grid layout in frontend" - Mal enfoque
  - Impacto: ALTO - Duplicación, no se puede cachear, aumenta peso HTML

- **JS inline (líneas 215-280):**
  - 66 líneas de JavaScript que deberían estar en hero-media-grid.js
  - Impacto: ALTO - No se puede cachear, no se puede minificar separado, aumenta peso HTML

❌ **ALTO: Video parsing en template**
- **Regex YouTube/Vimeo (líneas 183-195):**
  - Debería ser método privado: `parse_video_embed_url(string $url): string`
  - Lógica reutilizable que podría ser helper
  - Impacto: MEDIO - Dificulta testing y reutilización

⚠️ **Sin block.json**
- WordPress recomienda block.json para bloques nativos
- Impacto: Bajo (funciona sin él)

⚠️ **Archivo JS vacío sin propósito**
- hero-media-grid.js tiene solo placeholder
- Debería contener el código del template o eliminarse
- Impacto: BAJO - Archivo innecesario cargado

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque está en uso en producción. NO cambiar block name ni estructura de datos.**

### Prioridad CRÍTICA

**1. ⚠️ ARREGLAR XSS - echo sin escapar**
- **Acción:** Cambiar línea 197 de `echo $video_embed;` a `echo wp_kses_post($video_embed);`
- **Razón:** Vulnerability XSS potencial
- **Riesgo:** CRÍTICO - Seguridad
- **Precauciones:**
  - Verificar que iframe sigue funcionando después
  - Testing con videos YouTube y Vimeo
- **Esfuerzo:** 5 min + testing 15 min

**2. ⚠️ EXTRAER lógica de template a clase PHP**

**2A. Mover physical_difficulty a método privado:**
```php
// En HeroMediaGrid.php
private function get_package_physical_difficulty(int $post_id): array
{
    $physical_difficulty = get_field('physical_difficulty', $post_id);

    $difficulty_map = [
        'easy' => ['label' => __('Easy', 'travel-blocks'), 'dots' => 1],
        'moderate' => ['label' => __('Moderate', 'travel-blocks'), 'dots' => 2],
        'moderate_demanding' => ['label' => __('Moderate - Demanding', 'travel-blocks'), 'dots' => 3],
        'difficult' => ['label' => __('Difficult', 'travel-blocks'), 'dots' => 4],
        'very_difficult' => ['label' => __('Very Difficult', 'travel-blocks'), 'dots' => 5],
    ];

    if (!empty($physical_difficulty) && isset($difficulty_map[$physical_difficulty])) {
        return $difficulty_map[$physical_difficulty];
    }

    return ['label' => '', 'dots' => 0];
}
```
- **Razón:** Lógica de negocio no debería estar en template
- **Riesgo:** BAJO - Es lógica nueva encapsulada
- **Precauciones:**
  - Pasar datos al template en render_live() y render_preview()
  - Actualizar template para recibir $activity_level variable
- **Esfuerzo:** 1h

**2B. Mover video parsing a método privado:**
```php
private function parse_video_embed_url(string $video_url): string
{
    if (empty($video_url)) {
        return '';
    }

    // YouTube
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $matches)) {
        $video_id = sanitize_text_field($matches[1]);
        return sprintf(
            '<iframe src="https://www.youtube.com/embed/%s" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            esc_attr($video_id)
        );
    }

    // Vimeo
    if (preg_match('/vimeo\.com\/([0-9]+)/', $video_url, $matches)) {
        $video_id = sanitize_text_field($matches[1]);
        return sprintf(
            '<iframe src="https://player.vimeo.com/video/%s" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>',
            esc_attr($video_id)
        );
    }

    return '';
}
```
- Luego usar en render_live(): `'video_embed' => $this->parse_video_embed_url($video_url)`
- **Razón:** Parsing de URLs es lógica que debería estar en controller
- **Riesgo:** BAJO - Encapsula lógica existente
- **Esfuerzo:** 45 min

### Prioridad Alta

**3. MOVER JS de template a archivo .js**
- **Acción:**
  1. Copiar código JS de líneas 215-280 a hero-media-grid.js
  2. Eliminar bloque `<script>` del template
  3. Verificar que hero-media-grid.js se encola correctamente
- **Razón:**
  - Assets no deberían estar inline en template
  - JS inline no se puede cachear ni minificar separadamente
  - Aumenta peso del HTML innecesariamente
- **Riesgo:** MEDIO - Requiere testing de Swiper y GLightbox
- **Precauciones:**
  - Verificar que Swiper y GLightbox cargan antes
  - Testing de carousel y lightbox
  - Testing de botón "View All Photos"
- **Esfuerzo:** 1h + testing 30 min

**4. REMOVER CSS inline del template**
- **Acción:**
  1. Eliminar bloque `<style>` líneas 19-48
  2. Verificar que hero-media-grid.css tiene todas esas reglas (ya las tiene)
  3. Verificar que CSS se aplica correctamente sin inline
- **Razón:**
  - CSS inline no se puede cachear
  - Duplica reglas que ya existen en .css
  - Comentario "Force grid" sugiere problema de especificidad, no necesidad de inline
- **Riesgo:** MEDIO - Podría haber problemas de especificidad
- **Precauciones:**
  - Verificar que grid se mantiene sin !important inline
  - Si hay conflicto, aumentar especificidad en .css, no usar inline
  - Testing visual completo en desktop/tablet/mobile
- **Esfuerzo:** 30 min + testing visual 1h

**5. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\Template` a `Travel\Blocks\Template`
- **Razón:** No sigue PSR-4, tiene `\Blocks\Blocks\`
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:**
  - Actualizar composer.json si es necesario
  - Ejecutar `composer dump-autoload`
  - Verificar que bloque sigue registrándose
- **Esfuerzo:** 30 min

### Prioridad Media

**6. Crear Helper Service para Video Parsing**
- **Acción:** Crear `VideoEmbedService` con método `parse_url()`
- **Razón:** Lógica reutilizable por otros bloques
- **Riesgo:** BAJO - Es nuevo servicio
- **Precauciones:** Documentar uso
- **Esfuerzo:** 1h (si otros bloques también lo necesitan)

**7. Crear block.json**
- **Acción:** Crear block.json con metadata del bloque
- **Razón:** WordPress recomienda block.json para bloques nativos
- **Riesgo:** BAJO
- **Precauciones:**
  - Mantener compatibilidad con registro PHP actual
  - Verificar que bloque sigue apareciendo en editor
- **Esfuerzo:** 1h

**8. Agregar validación de video_url antes de regex**
- **Acción:**
  ```php
  private function parse_video_embed_url(string $video_url): string
  {
      if (!is_string($video_url) || empty($video_url)) {
          return '';
      }
      // ... resto del código
  }
  ```
- **Razón:** Prevenir warnings de preg_match
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min

### Prioridad Baja

**9. Agregar filtros para extender**
- **Acción:** Agregar hooks:
  ```php
  apply_filters('travel_blocks/template/hero_media_grid/gallery', $gallery, $post_id)
  apply_filters('travel_blocks/template/hero_media_grid/difficulty_map', $difficulty_map)
  ```
- **Razón:** Permitir customización sin modificar código
- **Riesgo:** BAJO
- **Precauciones:** Documentar filtros
- **Esfuerzo:** 30 min

**10. Considerar eliminar archivo .js si no se va a usar**
- **Acción:** Si se decide mantener JS inline (no recomendado), eliminar hero-media-grid.js
- **Razón:** Evitar archivos innecesarios
- **Riesgo:** NINGUNO
- **Esfuerzo:** 2 min
- **NOTA:** Solo si se rechaza recomendación #3

---

## 10. Plan de Acción

**Orden de Implementación:**

**FASE 1 - Crítico (Seguridad):**
1. Arreglar XSS echo sin escapar

**FASE 2 - Crítico (Arquitectura):**
2. Extraer physical_difficulty a método privado
3. Extraer video parsing a método privado
4. Mover JS a archivo .js separado
5. Remover CSS inline del template

**FASE 3 - Refactor:**
6. Corregir namespace
7. Crear block.json
8. Validación de video_url

**FASE 4 - Mejoras:**
9. Agregar filtros
10. Crear VideoEmbedService si hay otros bloques que lo necesitan

**Precauciones Generales:**
- ⛔ NO cambiar block name `hero-media-grid`
- ⛔ NO cambiar estructura de datos esperada por template
- ⛔ NO cambiar clases CSS públicas
- ✅ Testing: Verificar Swiper carousel funciona
- ✅ Testing: Verificar GLightbox funciona
- ✅ Testing: Verificar grid layout 65/35 en desktop
- ✅ Testing: Verificar responsive en tablet/mobile
- ✅ Testing: Verificar videos YouTube y Vimeo embeds
- ✅ Testing: Verificar discount badge aparece correctamente
- ✅ Testing: Verificar activity level indicator

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en inserter (Template blocks)
- [ ] Se puede insertar en Query Loop template
- [ ] Preview funciona en editor con datos de ejemplo
- [ ] Frontend funciona en páginas de package
- [ ] Galería Swiper muestra carousel con fade effect
- [ ] Autoplay funciona (5 segundos)
- [ ] GLightbox abre al hacer click en "View All Photos"
- [ ] Mapa abre en GLightbox al hacer click
- [ ] Video YouTube embeds correctamente
- [ ] Video Vimeo embeds correctamente
- [ ] Discount badge aparece cuando hay precio oferta
- [ ] Porcentaje de descuento se calcula correctamente
- [ ] Activity level indicator muestra dots correctos según dificultad
- [ ] Grid 65/35 funciona en desktop
- [ ] Grid responsive funciona en tablet (1 columna)
- [ ] Sidebar 2 columnas en mobile

### Arquitectura
- [ ] XSS arreglado (echo escapado)
- [ ] Physical_difficulty en método privado (no en template)
- [ ] Video parsing en método privado (no en template)
- [ ] JS movido a archivo .js (no inline)
- [ ] CSS inline removido del template
- [ ] Namespace correcto (si se cambió)
- [ ] block.json creado (si se implementó)

### Seguridad
- [ ] echo $video_embed escapado con wp_kses_post()
- [ ] Todos los esc_url, esc_attr, esc_html correctos
- [ ] Validación de video_url antes de regex
- [ ] Type hints correctos en métodos

### Clean Code
- [ ] Template solo tiene presentación (HTML)
- [ ] Lógica de negocio en clase PHP
- [ ] Assets en archivos separados (.css, .js)
- [ ] Métodos pequeños y enfocados
- [ ] Sin duplicación de código

---

## 12. Análisis de Template vs Assets

### Líneas de Código

| Componente | Líneas | Observaciones |
|------------|--------|---------------|
| **HeroMediaGrid.php** | 168 | ✅ Código limpio y corto |
| **hero-media-grid.php (template)** | 282 | ❌ Demasiado largo por assets inline |
| **hero-media-grid.css** | 403 | ✅ CSS completo y bien organizado |
| **hero-media-grid.js** | 14 | ❌ Vacío (solo placeholder) |
| **Total** | **867** | |

### Métodos más largos

**Clase PHP (todos cortos):**
1. `get_package_gallery()` - 20 líneas (65-85) ✅
2. `get_package_discount()` - 18 líneas (122-139) ✅
3. `enqueue_assets()` - 24 líneas (144-167) ✅
4. `render_preview()` - 16 líneas (29-44) ✅

**Template (bloques problemáticos):**
1. Physical difficulty logic - 29 líneas (88-116) ❌
2. JS initialization - 66 líneas (215-280) ❌
3. CSS inline - 30 líneas (19-48) ❌
4. Video parsing - 18 líneas (183-198) ⚠️

### Distribución Lógica en Template

```
Template (282 líneas):
- CSS inline: 30 líneas (10.6%) ❌ Debería estar en .css
- HTML/PHP presentación: 168 líneas (59.6%) ✅ Correcto
- PHP lógica negocio: 47 líneas (16.7%) ❌ Debería estar en clase
- JS inline: 66 líneas (23.4%) ❌ Debería estar en .js
```

**Conclusión:** Solo ~60% del template es presentación pura, el resto es lógica/assets que no deberían estar ahí.

---

## 📊 Resumen Ejecutivo

### Estado Actual

**Problemas Críticos:**
- ❌ XSS vulnerability - echo sin escapar (línea 197)
- ❌ Lógica de negocio en template (physical_difficulty, video parsing)
- ❌ CSS inline duplicado (30 líneas)
- ❌ JS inline que debería estar en archivo (66 líneas)
- ❌ Archivo .js vacío sin uso real

**Problemas Moderados:**
- ⚠️ Namespace incorrecto (PSR-4)
- ⚠️ Sin block.json (recomendado)
- ⚠️ get_field() directo en template (línea 95)
- ⚠️ Template muy largo (282 líneas)

**Fortalezas:**
- ✅ Clase PHP limpia y bien estructurada (168 líneas)
- ✅ Métodos privados muy cortos (<25 líneas)
- ✅ Type hints correctos
- ✅ Preview bien implementado
- ✅ CSS file completo y organizado (403 líneas)
- ✅ Escapado correcto en mayoría del template
- ✅ Cálculo de descuento bien implementado
- ✅ Manejo de gallery field robusto

### Puntuación: 5/10

**Justificación:**
- Clase PHP sería 8/10 (muy limpia)
- Template baja puntuación a 5/10 por:
  - XSS vulnerability (-2 puntos)
  - Assets inline (-1 punto)
  - Lógica de negocio en vista (-1 punto)
  - Archivo JS vacío (-0.5 puntos)

**Desglose por Categoría:**

| Categoría | Puntuación | Observaciones |
|-----------|------------|---------------|
| **Clase PHP** | 8/10 | ✅ Muy limpia, métodos cortos, bien estructurada |
| **Template** | 3/10 | ❌ Viola MVC, assets inline, lógica negocio |
| **Seguridad** | 4/10 | ❌ XSS vulnerability crítica |
| **Arquitectura** | 5/10 | ⚠️ Separación MVC violada en template |
| **Clean Code** | 6/10 | ✅ Clase bien, ❌ Template complejo |
| **SOLID** | 6/10 | ⚠️ SRP violado en template |

### Líneas de Código Totales: 867 líneas

**Distribución:**
- Clase PHP: 168 líneas (19.4%)
- Template: 282 líneas (32.5%)
- CSS: 403 líneas (46.5%)
- JS: 14 líneas (1.6%)

### Métodos más largos:

**Clase PHP (todos OK):**
1. `enqueue_assets()` - 24 líneas ✅
2. `get_package_gallery()` - 20 líneas ✅
3. `get_package_discount()` - 18 líneas ✅

**Template (problemáticos):**
1. JS inline block - 66 líneas ❌
2. CSS inline block - 30 líneas ❌
3. Physical difficulty logic - 29 líneas ❌

### Principales Problemas Encontrados:

1. **XSS Vulnerability** - echo $video_embed sin escapar (CRÍTICO)
2. **Assets inline en template** - CSS y JS no están en archivos separados
3. **Lógica de negocio en template** - Physical difficulty y video parsing deberían estar en clase
4. **Archivo JS vacío** - hero-media-grid.js no tiene contenido útil
5. **Namespace incorrecto** - `Travel\Blocks\Blocks\Template` debería ser `Travel\Blocks\Template`

### Fortalezas Destacadas:

1. **Clase PHP muy limpia** - Solo 168 líneas, métodos cortos (<25 líneas)
2. **Separación clara en clase** - Cada método privado tiene responsabilidad única
3. **Preview bien implementado** - Datos de ejemplo completos y realistas
4. **Manejo robusto de ACF gallery** - Validación y estructura correcta
5. **Cálculo de descuento correcto** - Lógica matemática clara y precisa
6. **CSS completo y organizado** - 403 líneas con responsive bien estructurado

### Recomendación:

**URGENTE:** Arreglar XSS vulnerability (5 min)

**ALTA PRIORIDAD (1-2 días):**
1. Extraer lógica de template a clase PHP (physical_difficulty, video parsing)
2. Mover JS a archivo .js separado
3. Remover CSS inline

**MEDIA PRIORIDAD (siguiente sprint):**
4. Corregir namespace
5. Crear block.json

**Impacto Esperado Post-Refactor:** 8/10
- Template quedaría limpio (solo presentación)
- Seguridad OK (XSS arreglado)
- Assets separados correctamente
- Mantenibilidad mejorada significativamente

---

**Auditoría completada:** 2025-11-09
**Refactorización:** URGENTE (XSS) + Recomendada (arquitectura)
