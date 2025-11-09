# Auditoría: PromoCard (Package)

**Fecha:** 2025-11-09
**Bloque:** 15/XX Package
**Tiempo:** 30 min
**⚠️ ESTADO:** REGULAR - Bloque con inconsistencias graves entre PHP y template
**⚠️ NOTA IMPORTANTE:** Template espera variables que NO se pasan desde PHP

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/promo-card`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Campos ACF:** `promo_title`, `promo_subtitle`, `promo_description`, `promo_image`, `promo_badge_text`, `promo_badge_color`, `promo_button_text`, `promo_button_url`, `discount_percentage`
- **Icon:** `format-image`
- **Category:** `travel`
- **Keywords:** promo, card, promotional, offer

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **Usa template separado** ✅ (promo-card.php)
- **⚠️ INCONSISTENCIA CRÍTICA:** Template espera variables diferentes a las que pasa PHP
- **ACF dependency:** Múltiples campos (9 campos)
- **Fallback a discount_percentage:** Si no hay título/badge, usa descuento

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** ✅ Usa is_editor_mode() correctamente
- **ACF fields:** promo_title, promo_subtitle, promo_description, promo_image, promo_badge_text, promo_badge_color, promo_button_text, promo_button_url, discount_percentage
- **Template:** promo-card.php (47 líneas)
- **CSS:** promo-card.css (204 líneas - imagen circular, múltiples variantes)

### ⚠️ IMPORTANTE - INCONSISTENCIA TEMPLATE
**ACLARACIÓN CRÍTICA:** El bloque tiene una **inconsistencia grave** entre PHP y template:

**PHP pasa al template:**
```php
$promo = [
    'title' => '...',
    'subtitle' => '...',
    'description' => '...',
    'image' => '...', // URL string
    'badge_text' => '...',
    'badge_color' => '...',
    'button_text' => '...',
    'button_url' => '...',
];
```

**Template espera:**
```php
$image // Array con 'sizes', 'url', 'alt'
$title // String directo (NO $promo['title'])
$description // String directo
$button_text // String directo
$button_url // String directo
$button_style // NO se pasa desde PHP
$button_target // NO se pasa desde PHP
$background_color // NO se pasa desde PHP
$text_color // NO se pasa desde PHP
```

**RESULTADO:** ⛔ **El template NO va a funcionar correctamente** con el código PHP actual.

### ⚠️ IMPORTANTE - FALLBACK A DISCOUNT
**ACLARACIÓN CRÍTICA:** El bloque tiene lógica de fallback a discount_percentage:
1. Si NO hay promo_title → Usa discount_percentage para generar "XX% Off Early Bird Special"
2. Si NO hay promo_badge_text → Usa discount_percentage para generar "XX% OFF"

Esto significa que el bloque está diseñado para promociones de descuento por defecto.

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/PromoCard.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** ✅ `/templates/promo-card.php` (47 líneas - ⚠️ INCONSISTENTE con PHP)
**Assets:**
- CSS: `/assets/blocks/promo-card.css` (204 líneas - incluye variantes de imagen circular)
- JS: ❌ NO tiene JavaScript

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- ✅ EditorHelper::is_editor_mode() (correctamente usado)
- ACF fields (9 campos diferentes)
- WordPress media functions (wp_get_attachment_image_url, get_the_post_thumbnail_url)

**Líneas de Código:**
- **Clase PHP:** 129 líneas
- **Template:** 47 líneas
- **JavaScript:** 0 líneas
- **CSS:** 204 líneas
- **TOTAL:** 380 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Tarjeta promocional con imagen circular, badge, título, descripción y CTA. Diseñada para ofertas y promociones especiales de paquetes.

**Funcionalidad Principal:**
1. **Display de datos promocionales:**
   - Título de la promo (con fallback a discount)
   - Subtítulo opcional
   - Descripción de la oferta
   - Imagen circular (promo_image o featured image)
   - Badge con color personalizable
   - Botón CTA con texto/URL customizable

2. **Fallback inteligente a discount_percentage:**
   - Si NO hay promo_title → "XX% Off Early Bird Special"
   - Si NO hay promo_badge_text → "XX% OFF"
   - Permite usar el bloque sin configurar todos los campos

3. **Imagen con fallback:**
   - Prioridad 1: promo_image (custom field)
   - Prioridad 2: featured image del post
   - Formato: URL de imagen (size 'medium')

4. **Preview mode:**
   - Muestra datos de ejemplo hardcoded
   - NO usa datos reales en editor

5. **Template rendering:**
   - Usa load_template() con extract()
   - ⚠️ **PROBLEMA:** Variables pasadas NO coinciden con las esperadas

**Inputs (ACF - NO registrado en código):**
- `promo_title` (string) - Título de la promoción
- `promo_subtitle` (string) - Subtítulo opcional
- `promo_description` (string) - Descripción de la oferta
- `promo_image` (attachment_id) - Imagen de la promoción
- `promo_badge_text` (string) - Texto del badge
- `promo_badge_color` (color) - Color del badge
- `promo_button_text` (string) - Texto del botón
- `promo_button_url` (url) - URL del botón
- `discount_percentage` (int) - Porcentaje de descuento (fallback)

**Outputs:**
- Tarjeta promocional con:
  - Imagen circular (150px × 150px default, con variantes)
  - Badge opcional con color custom
  - Título, subtítulo y descripción
  - Botón CTA
  - Variantes de estilo (flat, elevated, bordered)
  - Alineación configurable (left, center, right)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'promo-card';
private string $title = 'Promo Card';
private string $description = 'Promotional card with circular image';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (14 líneas)
2. enqueue_assets(): void - Encola CSS (6 líneas)
3. render($attributes, $content, $block): string - Renderiza (23 líneas)
```

**Métodos Privados:**
```php
4. get_preview_data(): array - Datos de preview (12 líneas)
5. get_post_data(int $post_id): array - Datos reales del post (45 líneas)
```

**Métodos Protegidos:**
```php
6. load_template(string $template_name, array $data = []): void - Carga template (11 líneas)
```

**Total:** 6 métodos, 129 líneas

**Métodos más largos:**
1. ✅ `get_post_data()` - **45 líneas** (aceptable)
2. ✅ `render()` - **23 líneas** (excelente)
3. ✅ `register()` - **14 líneas** (excelente)
4. ✅ `get_preview_data()` - **12 líneas** (excelente)

**Observación:** ✅ TODOS los métodos están bien dimensionados (<50 líneas)

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/promo-card`
- api_version: 2
- category: `travel`
- icon: `format-image`
- keywords: ['promo', 'card', 'promotional', 'offer']
- supports: anchor: true, html: false
- render_callback: `[$this, 'render']`

**Enqueue Assets:**
- CSS: `/assets/blocks/promo-card.css` (sin condiciones)
- Hook: `enqueue_block_assets`
- ⚠️ **NO hay conditional loading** - CSS se carga siempre (incluso en páginas sin el bloque)

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Campos:** ❌ **NO REGISTRA CAMPOS** (asume que ACF fields existen)

---

## 5. Campos Meta

**Definición:** ❌ **NO REGISTRA CAMPOS EN CÓDIGO**

**Campos usados (asume que existen):**
- `promo_title` (string) - Título de la promoción
- `promo_subtitle` (string) - Subtítulo opcional
- `promo_description` (string) - Descripción de la oferta
- `promo_image` (attachment_id) - ID de imagen
- `promo_badge_text` (string) - Texto del badge
- `promo_badge_color` (color) - Color del badge (default: #ff5722)
- `promo_button_text` (string) - Texto del botón (default: "Learn More")
- `promo_button_url` (url) - URL del botón (default: "#pricing-card")
- `discount_percentage` (int) - Porcentaje de descuento (para fallback)

**Problemas:**
- ❌ **NO registra campos** - Depende de que estén definidos en ACF externamente
- ❌ **NO documenta qué campos son required vs optional**
- ⚠️ **Hardcoded defaults** para button (#pricing-card, "Learn More")
- ⚠️ **Hardcoded default color** (#ff5722)
- ✅ Tiene fallbacks a discount_percentage (buen diseño)

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Try-catch wrapper (líneas 36-55)
2. Get post_id con get_the_ID() (línea 37)
3. Check preview mode con EditorHelper::is_editor_mode() (línea 38)
4. Si preview: get_preview_data() (línea 40)
5. Si NO preview: get_post_data($post_id) (línea 40)
6. Early return si NO hay título (línea 41)
7. Generate block_id con uniqid() (línea 44)
8. Append className si existe (línea 45)
9. Build $data array (líneas 43-48)
10. Output con ob_start/load_template/ob_get_clean (líneas 50-52)
11. Catch exceptions con mensaje de error en WP_DEBUG (líneas 53-55)

**Flujo de Datos:**
```
render()
  → EditorHelper::is_editor_mode()?
    → YES: get_preview_data()
      → return hardcoded preview data
    → NO: get_post_data($post_id)
      → get promo_title (fallback a discount)
      → get promo_subtitle
      → get promo_description
      → get promo_image (fallback a featured)
      → get promo_badge_text (fallback a discount)
      → get promo_badge_color (default #ff5722)
      → get promo_button_text (default "Learn More")
      → get promo_button_url (default "#pricing-card")
      → return promo array
  → empty check on promo['title']
  → load_template('promo-card', $data)
    → extract($data)
    → include template
```

**Variables al Template:**
```php
$block_id = 'promo-card-abc123'; // string
$class_name = 'promo-card custom-class'; // string
$promo = [
    'title' => 'Special Offer',
    'subtitle' => 'Limited Time Only',
    'description' => '...',
    'image' => 'https://...jpg', // ⚠️ String URL, NO array
    'badge_text' => '20% OFF',
    'badge_color' => '#ff5722',
    'button_text' => 'Claim Offer',
    'button_url' => '#pricing',
]; // array
$is_preview = false; // bool
```

**⚠️ PROBLEMA CRÍTICO:** El template espera `$image` (array), `$title` (string), etc. pero recibe `$promo` (array). Esto causará errores.

**Manejo de Errores:**
- ✅ Try-catch wrapper en render()
- ✅ WP_DEBUG check antes de mostrar error
- ✅ Escapado de error con esc_html()
- ✅ Return empty string si error y NO WP_DEBUG
- ✅ File exists check en load_template()
- ✅ Empty check en promo['title'] antes de renderizar

---

## 7. Funcionalidades Adicionales

### 7.1 Fallback a Discount Percentage

**Método:** `get_post_data()` (líneas 74-85, 98-104)

**Funcionalidad para Título:**
```php
$promo_title = get_post_meta($post_id, 'promo_title', true);

if (empty($promo_title)) {
    $discount = get_post_meta($post_id, 'discount_percentage', true);
    if ($discount) {
        $promo_title = sprintf(__('%s%% Off Early Bird Special', 'travel-blocks'), $discount);
    }
}
```

**Funcionalidad para Badge:**
```php
$badge_text = get_post_meta($post_id, 'promo_badge_text', true);
if (empty($badge_text)) {
    $discount = get_post_meta($post_id, 'discount_percentage', true);
    if ($discount) {
        $badge_text = $discount . '% OFF';
    }
}
```

**Calidad:** 8/10 - Muy buena lógica de fallback

**Observaciones:**
- ✅ Usa sprintf() con traducción para título
- ✅ Doble check empty() y if ($discount)
- ⚠️ **Duplicación:** get_post_meta('discount_percentage') se llama 2 veces (debería cachear)
- ⚠️ Badge NO usa traducción (inconsistente con título)

### 7.2 Imagen con Fallback

**Método:** `get_post_data()` (líneas 87-96)

**Funcionalidad:**
```php
$promo_image = '';
$promo_image_id = get_post_meta($post_id, 'promo_image', true);
if ($promo_image_id) {
    $promo_image = wp_get_attachment_image_url($promo_image_id, 'medium');
} else {
    $featured_id = get_post_thumbnail_id($post_id);
    if ($featured_id) {
        $promo_image = get_the_post_thumbnail_url($post_id, 'medium');
    }
}
```

**Características:**
- ✅ Prioridad clara: promo_image → featured image
- ✅ Usa size 'medium' (optimización)
- ✅ Verifica que ID exista antes de obtener URL
- ⚠️ **Retorna string vacío si NO hay imagen** (el bloque sigue funcionando)
- ⚠️ **Template espera array con sizes/url/alt** pero recibe string URL

**Calidad:** 7/10 - Buena lógica pero inconsistente con template

### 7.3 Defaults para Botón

**Método:** `get_post_data()` (líneas 112-114)

**Funcionalidad:**
```php
'badge_color' => get_post_meta($post_id, 'promo_badge_color', true) ?: '#ff5722',
'button_text' => $promo_button_text ?: __('Learn More', 'travel-blocks'),
'button_url' => $promo_button_url ?: '#pricing-card',
```

**Características:**
- ✅ Operador ternario para defaults
- ✅ button_text usa traducción
- ✅ badge_color tiene default naranja (#ff5722)
- ⚠️ **Hardcoded anchor** (#pricing-card) - Debería ser configurable
- ⚠️ **Hardcoded color** (#ff5722) - Debería ser constante

**Calidad:** 7/10 - Funcional pero con hardcoded values

### 7.4 Preview Data

**Método:** `get_preview_data()` (líneas 58-70)

**Funcionalidad:**
- Retorna array con datos hardcoded de ejemplo
- Simula una oferta del 20% OFF
- Incluye todos los campos necesarios
- badge_color: #ff5722 (naranja)
- button_url: #pricing

**Calidad:** 9/10 - Claro, completo y útil

**Observaciones:**
- ✅ Datos realistas y representativos
- ✅ Incluye todos los campos
- ✅ Color consistente con defaults
- ✅ Textos en inglés (consistente con traducciones)

### 7.5 Template Loading

**Método:** `load_template()` (líneas 118-128)

**Funcionalidad:**
- Construye path: TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php'
- Check file_exists()
- Si NO existe: muestra warning en WP_DEBUG
- extract($data, EXTR_SKIP) → Convierte array keys a variables
- include $template_path

**Calidad:** 7/10 - Estándar pero con inconsistencias

**Problemas:**
- ⚠️ **extract() es peligroso** - Puede sobrescribir variables (usa EXTR_SKIP, mejor)
- ⚠️ **NO documenta** que usa extract
- ⚠️ **NO valida** que $data sea array
- ⚠️ **INCONSISTENCIA:** Variables extraídas NO coinciden con las esperadas en template
- ✅ File exists check presente
- ✅ WP_DEBUG check antes de warning
- ✅ Escapado con esc_html() en warning

### 7.6 JavaScript

**Archivo:** ❌ NO tiene JavaScript

**Razón:** El bloque es puramente presentacional, no necesita interacción

**Observación:** ✅ Correcto - No necesita JS

### 7.7 CSS

**Archivo:** `/assets/blocks/promo-card.css` (204 líneas)

**Características:**
- ✅ Imagen circular con border-radius
- ✅ Múltiples variantes:
  - Card styles: flat, elevated, bordered
  - Image sizes: small (100px), medium (150px), large (200px)
  - Text alignment: left, center, right
  - Button styles: primary, secondary, outline
- ✅ Responsive design (767px breakpoint)
- ✅ Hover effects en card y button
- ✅ CSS variables (var(--border-radius-lg), var(--wp--preset--color--secondary))
- ⚠️ **Algunos valores hardcoded** (colores, tamaños)

**Organización:**
- Secciones claras (card, image, content, alignment, button)
- Comentarios descriptivos
- Cascada lógica

**Calidad:** 8/10 - Completo y flexible

### 7.8 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.9 Dependencias Externas

- ACF get_post_meta() (9 campos diferentes)
- WordPress wp_get_attachment_image_url() (para imagen)
- WordPress get_the_post_thumbnail_url() (para featured image)
- WordPress get_post_thumbnail_id() (para featured image)
- WordPress get_the_ID()
- EditorHelper::is_editor_mode() ✅

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ **CUMPLE**
- Clase tiene una responsabilidad clara: renderizar promo card
- Métodos bien enfocados
- NO hay complejidad excesiva
- **Impacto:** NINGUNO

**OCP:** ⚠️ **VIOLA**
- No hereda de BlockBase → Difícil extender
- Hardcoded defaults (#pricing-card, #ff5722) → NO configurable
- **Impacto:** MEDIO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con mejores bloques
- **Impacto:** MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Acoplado directamente a:
  - ACF get_post_meta()
  - WordPress media functions
  - Estructura específica de campos
- No hay abstracción/interfaces
- **Impacto:** BAJO - Acoplamiento normal para WordPress

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **TODOS los métodos <50 líneas** (EXCELENTE)
- ✅ Método más largo: get_post_data() 45 líneas (aceptable)
- ✅ Complejidad ciclomática baja

**Anidación:**
- ✅ **Máximo 2 niveles** de anidación (excelente)
- ✅ Código muy legible

**Duplicación:**
- ⚠️ **get_post_meta('discount_percentage')** se llama 2 veces (líneas 81, 100)
- ⚠️ **Patrón de fallback duplicado** (título y badge)

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ✅ Propiedades claras

**Código Sin Uso:**
- ✅ No detectado

**DocBlocks:**
- ❌ **0/6 métodos documentados** (0%)
- ❌ Header de archivo básico
- ❌ NO documenta params/return types
- **Impacto:** MEDIO

**Magic Values:**
- ⚠️ '#ff5722' hardcoded (debería ser constante)
- ⚠️ '#pricing-card' hardcoded (debería ser configurable)
- ⚠️ 'medium' image size hardcoded (debería ser configurable)
- ⚠️ '%s%% Off Early Bird Special' string hardcoded (OK porque usa traducción)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ get_post_meta() de WordPress es seguro
- ✅ wp_get_attachment_image_url() es seguro
- ✅ NO hay inputs de usuario directos
- **Impacto:** NINGUNO - Perfecto

**Escapado:**
- ⚠️ **Template usa escapado** pero variables NO coinciden
- ✅ Escapado en error messages
- **Impacto:** ALTO - Template probablemente tiene errores

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ✅ No hace queries directas

**XSS:**
- ⚠️ **NO podemos verificar escapado** porque template tiene variables incorrectas

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ✅ **Template separado** (promo-card.php)
- ⚠️ **Template inconsistente** con datos de la clase
- ✅ Lógica de negocio en clase
- ✅ Estilos en CSS separado

**Acoplamiento:**
- ✅ **Bajo acoplamiento** - Solo ACF fields
- ✅ NO hay dependencias complejas
- **Impacto:** NINGUNO

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Pierde funcionalidades compartidas
- **Impacto:** MEDIO

**Caché:**
- ✅ N/A - No necesita caché (data de ACF)

**Otros:**
- ❌ **NO usa block.json** (debería para Gutenberg moderno)
- ✅ **Usa EditorHelper** correctamente
- ⚠️ **Duplicación de get_post_meta('discount_percentage')**

---

## 9. Recomendaciones de Refactorización

### Prioridad CRÍTICA

**1. ⛔ ARREGLAR INCONSISTENCIA PHP ↔ TEMPLATE**
- **Acción:**
  ```php
  // OPCIÓN A: Cambiar template para usar $promo array
  // En template (línea 16):
  <?php if (!empty($promo['image'])): ?>
      <img src="<?php echo esc_url($promo['image']); ?>" ... />
  <?php endif; ?>
  <h3><?php echo esc_html($promo['title']); ?></h3>
  ...

  // OPCIÓN B: Cambiar PHP para pasar variables individuales
  // En render():
  $data = [
      'block_id' => 'promo-card-' . uniqid(),
      'class_name' => 'promo-card' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
      'image' => $promo['image'],
      'title' => $promo['title'],
      'subtitle' => $promo['subtitle'],
      'description' => $promo['description'],
      'badge_text' => $promo['badge_text'],
      'badge_color' => $promo['badge_color'],
      'button_text' => $promo['button_text'],
      'button_url' => $promo['button_url'],
      'button_style' => $attributes['buttonStyle'] ?? 'primary',
      'button_target' => $attributes['buttonTarget'] ?? '_self',
      'background_color' => $attributes['backgroundColor'] ?? 'transparent',
      'text_color' => $attributes['textColor'] ?? 'inherit',
      'is_preview' => $is_preview,
  ];
  ```
- **Razón:** ⛔ **CRÍTICO** - El bloque NO funciona correctamente ahora
- **Riesgo:** ALTO - Puede romper frontend
- **Precauciones:**
  - Verificar qué versión está en producción (template o PHP)
  - Probar exhaustivamente después de cambio
  - Revisar todos los paquetes que usan este bloque
- **Esfuerzo:** 2 horas (incluye testing)

### Prioridad Alta

**2. Heredar de BlockBase**
- **Acción:** `class PromoCard extends BlockBase`
- **Razón:** Consistencia, funcionalidades compartidas
- **Riesgo:** MEDIO - Requiere refactorizar
- **Esfuerzo:** 1 hora

**3. Cachear discount_percentage**
- **Acción:**
  ```php
  private function get_post_data(int $post_id): array
  {
      $promo_title = get_post_meta($post_id, 'promo_title', true);
      $discount = get_post_meta($post_id, 'discount_percentage', true); // Cachear aquí

      if (empty($promo_title) && $discount) {
          $promo_title = sprintf(__('%s%% Off Early Bird Special', 'travel-blocks'), $discount);
      }

      // ...

      $badge_text = get_post_meta($post_id, 'promo_badge_text', true);
      if (empty($badge_text) && $discount) { // Reusar variable
          $badge_text = $discount . '% OFF';
      }
  }
  ```
- **Razón:** DRY, performance (evita query duplicada)
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**4. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con params, returns, description
- **Razón:** Documentación para mantenimiento
- **Riesgo:** NINGUNO
- **Esfuerzo:** 30 min

### Prioridad Media

**5. Convertir hardcoded values a constantes**
- **Acción:**
  ```php
  private const DEFAULT_BADGE_COLOR = '#ff5722';
  private const DEFAULT_BUTTON_TEXT = 'Learn More';
  private const DEFAULT_BUTTON_URL = '#pricing-card';
  private const IMAGE_SIZE = 'medium';

  // Uso:
  'badge_color' => get_post_meta($post_id, 'promo_badge_color', true) ?: self::DEFAULT_BADGE_COLOR,
  ```
- **Razón:** Mantenibilidad, configurabilidad
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**6. Hacer defaults configurables**
- **Acción:**
  ```php
  'button_url' => $promo_button_url ?: apply_filters('travel_blocks_promo_card_default_button_url', '#pricing-card'),
  ```
- **Razón:** Flexibilidad para temas/plugins
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**7. Conditional CSS loading**
- **Acción:**
  ```php
  public function enqueue_assets(): void
  {
      if (!is_admin() && (is_singular('package') || has_block('travel-blocks/promo-card'))) {
          wp_enqueue_style(...);
      }
  }
  ```
- **Razón:** Performance - Solo cargar CSS donde se necesita
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

### Prioridad Baja

**8. Crear block.json**
- **Acción:** Migrar de register_block_type() a block.json
- **Razón:** Gutenberg moderno, mejor performance
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**9. Mejorar estructura de imagen**
- **Acción:**
  ```php
  // Retornar array completo en lugar de solo URL
  $promo_image = [
      'url' => wp_get_attachment_image_url($promo_image_id, 'medium'),
      'sizes' => [
          'medium' => wp_get_attachment_image_url($promo_image_id, 'medium'),
          'large' => wp_get_attachment_image_url($promo_image_id, 'large'),
      ],
      'alt' => get_post_meta($promo_image_id, '_wp_attachment_image_alt', true),
  ];
  ```
- **Razón:** Consistencia con expectativas del template
- **Riesgo:** MEDIO - Requiere actualizar template
- **Esfuerzo:** 30 min

**10. Agregar traducción a badge fallback**
- **Acción:**
  ```php
  if (empty($badge_text) && $discount) {
      $badge_text = sprintf(__('%s%% OFF', 'travel-blocks'), $discount);
  }
  ```
- **Razón:** Consistencia con título (que sí usa traducción)
- **Riesgo:** BAJO
- **Esfuerzo:** 5 min

---

## 10. Plan de Acción

### Fase 0 - CRÍTICO (URGENTE - Hoy)
1. ⛔ **Arreglar inconsistencia PHP ↔ Template** (2 horas) - BLOQUEA TODO

**Total Fase 0:** 2 horas

### Fase 1 - Alta Prioridad (Esta semana)
2. Heredar de BlockBase (1 hora)
3. Cachear discount_percentage (15 min)
4. Agregar DocBlocks (30 min)

**Total Fase 1:** 1.75 horas

### Fase 2 - Media Prioridad (Próximas 2 semanas)
5. Convertir hardcoded a constantes (20 min)
6. Defaults configurables (20 min)
7. Conditional CSS loading (15 min)

**Total Fase 2:** 55 min

### Fase 3 - Baja Prioridad (Cuando haya tiempo)
8. Crear block.json (30 min)
9. Mejorar estructura de imagen (30 min)
10. Traducción badge fallback (5 min)

**Total Fase 3:** 1 hora 5 min

**Total Refactorización Completa:** ~5 horas 50 min

**Precauciones Generales:**
- ⛔ **MUY IMPORTANTE:** Primero resolver inconsistencia template antes de cualquier otra cosa
- ⚠️ **Verificar** qué versión está en producción (template o PHP)
- ⚠️ **NO cambiar** lógica de fallback a discount sin consultar
- ✅ SIEMPRE probar con paquetes reales después de cambios
- ✅ Verificar que imagen circular se muestra correctamente

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra datos hardcoded)
- [ ] Frontend funciona (muestra datos reales)
- [ ] ⛔ **Variables del template coinciden con las del PHP**

### Datos Promocionales
- [ ] promo_title se muestra correctamente
- [ ] promo_subtitle se muestra si existe
- [ ] promo_description se muestra correctamente
- [ ] Fallback a discount_percentage funciona (título)
- [ ] Fallback a discount_percentage funciona (badge)
- [ ] Escapado correcto en todos los outputs

### Imagen
- [ ] promo_image se muestra si existe
- [ ] Fallback a featured image funciona
- [ ] Imagen es circular (border-radius)
- [ ] Size 'medium' se usa correctamente
- [ ] Alt text existe
- [ ] ⛔ **Estructura de $image consistente con template**

### Badge y Botón
- [ ] Badge se muestra con color correcto
- [ ] Badge color default (#ff5722) funciona
- [ ] Botón se muestra con texto correcto
- [ ] Botón URL es correcta
- [ ] Botón default "Learn More" funciona
- [ ] Botón default URL "#pricing-card" funciona

### Template
- [ ] load_template() carga correctamente
- [ ] extract() crea variables correctamente
- [ ] ⛔ **Todas las variables esperadas están disponibles**
- [ ] background_color se aplica (si se agregó)
- [ ] text_color se aplica (si se agregó)
- [ ] button_style funciona (si se agregó)
- [ ] button_target funciona (si se agregó)

### CSS
- [ ] Estilos se aplican correctamente
- [ ] Imagen circular funciona
- [ ] Variantes de card funcionan (flat, elevated, bordered)
- [ ] Variantes de tamaño funcionan (small, medium, large)
- [ ] Alineación funciona (left, center, right)
- [ ] Button styles funcionan (primary, secondary, outline)
- [ ] Responsive funciona (767px)
- [ ] Hover effects funcionan
- [ ] Conditional loading funciona (si se agregó)

### Seguridad
- [ ] ⛔ **esc_html() en todos los outputs de texto**
- [ ] ⛔ **esc_url() en imagen y botón URL**
- [ ] ⛔ **esc_attr() en atributos HTML**
- [ ] get_post_meta() se usa correctamente

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] Constantes definidas (si se agregaron)
- [ ] block.json (si se creó)
- [ ] Filtros funcionan (si se agregaron)

### Clean Code
- [ ] Métodos <50 líneas ✅ (ya cumple)
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] No duplicación de discount_percentage (si se fijó)
- [ ] Constantes en lugar de magic values (si se cambiaron)

### Performance
- [ ] CSS solo se carga donde se necesita (si se agregó conditional)
- [ ] NO hay queries duplicadas (discount_percentage)
- [ ] Imagen size 'medium' optimiza carga

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Código PHP bien estructurado (129 líneas)
- ✅ Lógica de fallback a discount inteligente
- ✅ Imagen con fallback a featured
- ✅ Defaults para botón
- ✅ Preview data completo
- ✅ CSS completo con variantes (204 líneas)
- ✅ Usa EditorHelper correctamente
- ✅ Try-catch wrapper en render()
- ⛔ **INCONSISTENCIA CRÍTICA: Template NO coincide con PHP**
- ⚠️ Duplicación de get_post_meta('discount_percentage')
- ⚠️ Hardcoded values (#ff5722, #pricing-card)
- ❌ NO hereda de BlockBase
- ❌ NO tiene DocBlocks (0/6 métodos)

### Puntuación: 5.5/10

**Razones para la puntuación:**
- ➕ Lógica de fallback inteligente (+1)
- ➕ Imagen con fallback (+0.5)
- ➕ Defaults bien pensados (+0.5)
- ➕ Preview mode completo (+0.5)
- ➕ Try-catch wrapper (+0.5)
- ➕ CSS completo con variantes (+1)
- ➕ Código bien estructurado (+0.5)
- ➖ ⛔ **Template NO coincide con PHP** (-3) ← CRÍTICO
- ➖ Duplicación discount query (-0.5)
- ➖ Hardcoded values (-0.5)
- ➖ NO hereda BlockBase (-0.5)
- ➖ Sin DocBlocks (-0.5)

### Fortalezas
1. **Lógica de fallback a discount:** Muy bien pensada (título y badge)
2. **Imagen con prioridades:** promo_image → featured image
3. **Defaults sensatos:** Learn More, #pricing-card, #ff5722
4. **Preview data completo:** Datos realistas y útiles
5. **CSS flexible:** Múltiples variantes (card, image, alignment, button)
6. **Código limpio:** Métodos cortos, buena legibilidad
7. **Usa EditorHelper:** Correctamente implementado
8. **Try-catch wrapper:** Manejo de errores robusto
9. **Early return:** Si NO hay título, NO renderiza (eficiente)
10. **Responsive design:** Breakpoint en 767px

### Debilidades
1. ⛔ **INCONSISTENCIA CRÍTICA PHP ↔ TEMPLATE** - Variables NO coinciden, bloque probablemente NO funciona
2. ⚠️ **Duplicación de query** - get_post_meta('discount_percentage') se llama 2 veces
3. ⚠️ **Hardcoded values** - #ff5722, #pricing-card, 'medium' deberían ser constantes
4. ❌ **NO hereda de BlockBase** - Inconsistente con arquitectura
5. ❌ **NO tiene DocBlocks** (0/6 métodos)
6. ⚠️ **NO conditional CSS loading** - CSS se carga siempre
7. ⚠️ **Template espera array de imagen** pero recibe string URL
8. ⚠️ **Template espera variables que NO se pasan** (button_style, button_target, background_color, text_color)
9. ⚠️ **NO documenta campos required vs optional**
10. ⚠️ **Badge fallback NO usa traducción** (inconsistente con título)

### Recomendación Principal

**Este bloque tiene un PROBLEMA CRÍTICO que debe resolverse INMEDIATAMENTE.**

**PROBLEMA CRÍTICO:** ⛔ El template espera variables que NO se pasan desde PHP. Esto significa que el bloque probablemente **NO funciona correctamente** en frontend.

**Prioridad 0 - CRÍTICO (Hoy - 2 horas):**
1. ⛔ **Arreglar inconsistencia PHP ↔ Template** (2 horas) - BLOQUEA TODO
   - OPCIÓN A: Actualizar template para usar `$promo` array
   - OPCIÓN B: Actualizar PHP para pasar variables individuales
   - Decidir qué versión está en producción
   - Probar exhaustivamente

**Prioridad 1 - Alta (Esta semana - 1.75 horas):**
2. Heredar de BlockBase (1 hora)
3. Cachear discount_percentage (15 min)
4. Agregar DocBlocks (30 min)

**Prioridad 2 - Media (2 semanas - 55 min):**
5. Constantes para hardcoded values (20 min)
6. Defaults configurables con filtros (20 min)
7. Conditional CSS loading (15 min)

**Prioridad 3 - Baja (Cuando haya tiempo - 1h 5min):**
8. block.json (30 min)
9. Mejorar estructura imagen (30 min)
10. Traducción badge fallback (5 min)

**Esfuerzo total:** ~5 horas 50 min

**Veredicto:** Este bloque tiene **código PHP de buena calidad** con lógica de fallback inteligente, pero sufre de una **inconsistencia crítica** entre el código PHP y el template. Esto sugiere que:
- O el template es de una versión antigua
- O el PHP es de una versión antigua
- O nunca se probó correctamente

**ACCIÓN URGENTE:** Antes de cualquier otra refactorización, DEBE resolverse la inconsistencia template/PHP. Sin esto, el bloque NO funciona.

**PRIORIDAD: CRÍTICA - El bloque NO funciona correctamente hasta que se resuelva la inconsistencia.**

### Dependencias Identificadas

**ACF:**
- `promo_title` (string) - Título de la promoción
- `promo_subtitle` (string) - Subtítulo opcional
- `promo_description` (string) - Descripción de la oferta
- `promo_image` (attachment_id) - ID de imagen
- `promo_badge_text` (string) - Texto del badge
- `promo_badge_color` (color) - Color del badge
- `promo_button_text` (string) - Texto del botón
- `promo_button_url` (url) - URL del botón
- `discount_percentage` (int) - Porcentaje de descuento (fallback)

**WordPress:**
- wp_get_attachment_image_url() - Obtener URL de imagen
- get_the_post_thumbnail_url() - Obtener URL de featured image
- get_post_thumbnail_id() - Obtener ID de featured image
- get_the_ID() - Obtener post ID

**Helpers:**
- EditorHelper::is_editor_mode() ✅

**JavaScript:**
- ❌ **NO tiene JavaScript**

**CSS:**
- promo-card.css (204 líneas)
- Imagen circular
- Variantes múltiples
- Responsive design

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** ⛔ **CRÍTICA** - Resolver inconsistencia template/PHP INMEDIATAMENTE
**Próxima revisión:** Después de resolver inconsistencia crítica
