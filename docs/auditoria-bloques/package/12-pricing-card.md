# Auditoría: PricingCard (Package)

**Fecha:** 2025-11-09
**Bloque:** 12/XX Package
**Tiempo:** 30 min
**⚠️ ESTADO:** MUY BUENO - Bloque sticky completo con cálculos inteligentes de comidas
**⚠️ NOTA IMPORTANTE:** Usa lógica compleja para contar comidas desde itinerario

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/pricing-card`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Campos ACF:** `price_offer`, `price_from`, `price_normal`, `days`, `accommodation`, `itinerary`
- **Taxonomía:** `type_service` (para detectar desayuno/almuerzo/cena)
- **Icon:** `money-alt`
- **Category:** `template-blocks`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **Usa template separado** ✅ (pricing-card.php)
- **Lógica compleja de conteo de comidas** ⚠️ (depende de términos de taxonomía)
- **ACF dependency:** Múltiples campos (prices, days, accommodation, itinerary)
- **Sticky positioning:** Manejado por CSS

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **EditorHelper:** ✅ Usa is_editor_mode() correctamente
- **ACF fields:** price_offer, price_from, price_normal, days, accommodation, itinerary
- **Taxonomía:** type_service (términos con nombres específicos)
- **Template:** pricing-card.php (166 líneas)
- **CSS:** pricing-card.css (335 líneas - sticky sidebar)

### ⚠️ IMPORTANTE - LÓGICA DE PRECIOS
**ACLARACIÓN CRÍTICA:** Este bloque tiene una **lógica de prioridad de precios**:
1. Prioridad 1: `price_offer` (precio con oferta)
2. Prioridad 2: `price_from` (precio desde)
3. Prioridad 3: `price_normal` (precio normal)

Si modificas esto, afectarás cómo se muestran los precios en TODA la web.

### ⚠️ IMPORTANTE - LÓGICA DE COMIDAS
**ACLARACIÓN CRÍTICA:** El bloque **recorre todo el itinerario** del paquete buscando servicios de tipo desayuno/almuerzo/cena y cuenta cuántos hay de cada uno. Esto depende de:
- Que el campo `itinerary` tenga estructura `[{active, items: [{type_service}]}]`
- Que los términos de `type_service` tengan nombres específicos (breakfast/desayuno, lunch/almuerzo, dinner/cena)
- Que los días inactivos se ignoren

Si modificas esto, se romperá el conteo de comidas en todas las tarjetas de precios.

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/PricingCard.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** ✅ `/templates/pricing-card.php` (166 líneas)
**Assets:**
- CSS: `/assets/blocks/pricing-card.css` (335 líneas - incluye sticky positioning)
- JS: ❌ NO tiene JavaScript

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- ✅ EditorHelper::is_editor_mode() (correctamente usado)
- ACF fields (price_offer, price_from, price_normal, days, accommodation, itinerary)
- Taxonomía type_service (para detectar tipo de comidas)
- WordPress term functions (get_term)

**Líneas de Código:**
- **Clase PHP:** 202 líneas
- **Template:** 166 líneas
- **JavaScript:** 0 líneas
- **CSS:** 335 líneas
- **TOTAL:** 703 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Tarjeta de precio sticky para sidebar que muestra duración, precio, CTA, y detalles del paquete (alojamiento y comidas incluidas). Se mantiene visible mientras el usuario hace scroll.

**Funcionalidad Principal:**
1. **Display de precio con prioridad:**
   - Prioridad 1: price_offer (precio oferta)
   - Prioridad 2: price_from (precio desde)
   - Prioridad 3: price_normal (precio normal)
   - Formato: "$450" (sin decimales)

2. **Cálculo de duración:**
   - Obtiene días del campo 'days'
   - Calcula noches: `$nights = $days - 1`
   - Formato: "4 days / 3 nights"

3. **Conteo inteligente de comidas:**
   - Recorre campo itinerary (array de días)
   - Ignora días inactivos (active: false)
   - Busca términos de type_service
   - Detecta breakfast/desayuno, lunch/almuerzo, dinner/cena
   - Cuenta total de cada tipo

4. **Preview mode:**
   - Muestra datos de ejemplo hardcoded
   - NO usa datos reales en editor

5. **Template rendering:**
   - Usa load_template() con extract()
   - Pasa todas las variables al template
   - Template maneja todo el HTML/SVG

**Inputs (ACF - NO registrado en código):**
- `price_offer` (float) - Precio con oferta
- `price_from` (float) - Precio desde
- `price_normal` (float) - Precio normal
- `days` (int) - Número de días del tour
- `accommodation` (string) - Texto descripción alojamiento
- `itinerary` (array) - Array de días con items y type_service

**Outputs:**
- Tarjeta sticky completa con:
  - Icono de duración (SVG)
  - Número de días y noches
  - Precio con símbolo de tag
  - Texto "From USD" y "Per person"
  - CTA button (anchor a #booking-form)
  - Sección "Is this tour for me?"
  - Grid con accommodation e included meals

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'pricing-card';
private string $title = 'Pricing Card';
private string $description = 'Tarjeta de precio sticky para sidebar con duración, precio, CTA, meses recomendados, inclusiones y garantías';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (18 líneas)
2. enqueue_assets(): void - Encola CSS (9 líneas)
3. render($attributes, $content, $block): string - Renderiza (35 líneas)
```

**Métodos Privados:**
```php
4. get_preview_data(): array - Datos de preview (13 líneas)
5. get_post_data(int $post_id): array - Datos reales del post (34 líneas)
6. count_meals_from_itinerary(int $post_id): array - Cuenta comidas del itinerario (36 líneas)
```

**Métodos Protegidos:**
```php
7. load_template(string $template_name, array $data = []): void - Carga template (16 líneas)
```

**Total:** 7 métodos, 202 líneas

**Métodos más largos:**
1. ✅ `count_meals_from_itinerary()` - **36 líneas** (aceptable pero complejo)
2. ✅ `render()` - **35 líneas** (aceptable)
3. ✅ `get_post_data()` - **34 líneas** (aceptable)
4. ✅ `register()` - **18 líneas** (excelente)

**Observación:** ✅ TODOS los métodos están bien dimensionados (<50 líneas)

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/pricing-card`
- api_version: 2
- category: `template-blocks`
- icon: `money-alt`
- keywords: ['pricing', 'price', 'card', 'sidebar', 'cta', 'booking', 'package']
- supports: anchor: true, html: false
- render_callback: `[$this, 'render']`
- show_in_rest: true

**Enqueue Assets:**
- CSS: `/assets/blocks/pricing-card.css` (sin condiciones)
- Hook: `enqueue_block_assets`
- ⚠️ **NO hay conditional loading** - CSS se carga siempre (incluso en páginas sin el bloque)

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Campos:** ❌ **NO REGISTRA CAMPOS** (asume que ACF fields existen)

---

## 5. Campos Meta

**Definición:** ❌ **NO REGISTRA CAMPOS EN CÓDIGO**

**Campos usados (asume que existen):**
- `price_offer` (float) - Precio con oferta
- `price_from` (float) - Precio desde
- `price_normal` (float) - Precio normal
- `days` (int) - Número de días
- `accommodation` (string) - Descripción de alojamiento
- `itinerary` (array) - Array de días con estructura:
  ```php
  [
      [
          'active' => true,
          'items' => [
              [
                  'type_service' => 123, // term_id de taxonomía type_service
              ]
          ]
      ]
  ]
  ```

**Taxonomía usada:**
- `type_service` - Para detectar tipo de servicio (breakfast, lunch, dinner)

**Problemas:**
- ❌ **NO registra campos** - Depende de que estén definidos en ACF externamente
- ❌ **NO documenta estructura esperada** de itinerary (muy complejo)
- ❌ **NO valida estructura** de itinerary antes de recorrer
- ⚠️ **Hardcoded strings** para detectar comidas (breakfast, desayuno, lunch, almuerzo, dinner, cena)
- ⚠️ **Depende de nombres específicos** en términos de type_service

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Try-catch wrapper (líneas 63-95)
2. Get post_id con get_the_ID() (línea 64)
3. Check preview mode con EditorHelper::is_editor_mode() (línea 65)
4. Si preview: get_preview_data() (línea 68)
5. Si NO preview: get_post_data($post_id) (línea 70)
6. Generate block_id con uniqid() (línea 73)
7. Append className si existe (líneas 76-78)
8. Add block_id, class_name, is_preview a $data (líneas 80-82)
9. Output con ob_start/load_template/ob_get_clean (líneas 84-86)
10. Catch exceptions con mensaje de error en WP_DEBUG (líneas 88-94)

**Flujo de Datos:**
```
render()
  → EditorHelper::is_editor_mode()?
    → YES: get_preview_data()
      → return hardcoded preview data
    → NO: get_post_data($post_id)
      → get price (offer > from > normal)
      → get days → calculate nights
      → get accommodation
      → count_meals_from_itinerary($post_id)
        → get_field('itinerary')
        → foreach day:
          → skip if !active
          → foreach item:
            → get_term(type_service)
            → strpos() name for breakfast/lunch/dinner
            → increment counter
        → return meals array
  → load_template('pricing-card', $data)
    → extract($data)
    → include template
```

**Variables al Template:**
```php
$block_id = 'pricing-card-abc123'; // string
$class_name = 'pricing-card custom-class'; // string
$is_preview = false; // bool
$price = 450; // float
$duration_number = '4'; // string
$duration_text = 'days / 3 nights'; // string
$accommodation = '2 Nights hotel, 2 Nights camping'; // string
$meals = [
    'breakfast' => 4,
    'lunch' => 3,
    'dinner' => 3,
]; // array
```

**Manejo de Errores:**
- ✅ Try-catch wrapper en render()
- ✅ WP_DEBUG check antes de mostrar error
- ✅ Escapado de error con esc_html()
- ✅ Return empty string si error y NO WP_DEBUG
- ✅ File exists check en load_template()
- ⚠️ NO valida estructura de itinerary (puede causar warnings)
- ⚠️ NO valida que term existe antes de usar (is_wp_error check presente)

---

## 7. Funcionalidades Adicionales

### 7.1 Lógica de Precios

**Método:** `get_post_data()` (líneas 116-119)

**Funcionalidad:**
```php
$price_offer = floatval(get_field('price_offer', $post_id));
$price_from = floatval(get_field('price_from', $post_id));
$price_normal = floatval(get_field('price_normal', $post_id));
$price = $price_offer ?: ($price_from ?: $price_normal);
```

**Prioridad:**
1. price_offer (si > 0)
2. price_from (si > 0)
3. price_normal

**Calidad:** 9/10 - Muy bien implementado, prioridad clara

**Observaciones:**
- ✅ floatval() convierte a número
- ✅ Operador ternario anidado (legible)
- ⚠️ NO hay fallback si TODOS están vacíos (price = 0)

### 7.2 Cálculo de Duración

**Método:** `get_post_data()` (líneas 122-132)

**Funcionalidad:**
```php
$days = intval(get_field('days', $post_id));
$nights = $days > 0 ? $days - 1 : 0;

$duration_number = $days > 0 ? (string)$days : '';
$duration_text = '';
if ($days > 0) {
    $duration_text = ($days === 1 ? 'day' : 'days');
    if ($nights > 0) {
        $duration_text .= ' / ' . $nights . ' ' . ($nights === 1 ? 'night' : 'nights');
    }
}
```

**Características:**
- ✅ Calcula nights automáticamente (days - 1)
- ✅ Pluralización correcta (day/days, night/nights)
- ✅ Formato: "4 days / 3 nights"
- ✅ Maneja caso singular (1 day, 1 night)
- ✅ Maneja caso 0 days (empty strings)

**Calidad:** 9/10 - Excelente lógica

### 7.3 Conteo de Comidas desde Itinerario

**Método:** `count_meals_from_itinerary()` (líneas 149-184)

**Funcionalidad:**
- Obtiene campo itinerary (array de días)
- Recorre cada día del array
- Ignora días inactivos (active: false)
- Recorre items de cada día
- Obtiene term de type_service
- Usa strpos() para detectar nombre de meal:
  - breakfast/desayuno → meals['breakfast']++
  - lunch/almuerzo → meals['lunch']++
  - dinner/cena → meals['dinner']++
- Retorna array con contadores

**Calidad:** 7/10 - Funciona pero frágil

**Problemas:**
- ❌ **Hardcoded strings** (breakfast, desayuno, lunch, almuerzo, dinner, cena)
- ❌ **strpos() case-sensitive** (pero usa strtolower, OK)
- ❌ **NO valida estructura** de itinerary antes de recorrer
- ⚠️ **Depende de nombres específicos** en términos
- ⚠️ **is_wp_error check** presente pero NO maneja error
- ⚠️ **36 líneas** - Es el método más largo (pero aceptable)
- ⚠️ **NO documenta estructura esperada** de itinerary
- ✅ Check is_array() presente
- ✅ Check isset($day['active']) presente
- ✅ Check !empty($day['items']) presente

**Observación:** Este método es **frágil** porque depende de convenciones de nombres en taxonomía. Si alguien crea un término "Desayuno continental" funcionará, pero "Continental breakfast" NO.

### 7.4 Preview Data

**Método:** `get_preview_data()` (líneas 98-111)

**Funcionalidad:**
- Retorna array con datos hardcoded de ejemplo
- price: 450
- duration: "4 days / 3 nights"
- accommodation: "2 Nights hotel, 2 Nights camping"
- meals: 4 breakfasts, 3 lunchs, 3 dinners

**Calidad:** 8/10 - Claro y útil

**Observación:** ⚠️ "Lunchs" debería ser "Lunches" (error ortográfico)

### 7.5 Template Loading

**Método:** `load_template()` (líneas 186-201)

**Funcionalidad:**
- Construye path: TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php'
- Check file_exists()
- Si NO existe: muestra warning en WP_DEBUG
- extract($data, EXTR_SKIP) → Convierte array keys a variables
- include $template_path

**Calidad:** 8/10 - Estándar de WordPress

**Problemas:**
- ⚠️ **extract() es peligroso** - Puede sobrescribir variables (usa EXTR_SKIP, mejor)
- ⚠️ **NO documenta** que usa extract
- ⚠️ **NO valida** que $data sea array
- ✅ File exists check presente
- ✅ WP_DEBUG check antes de warning
- ✅ Escapado con esc_html() en warning

### 7.6 JavaScript

**Archivo:** ❌ NO tiene JavaScript

**Razón:** El sticky positioning se maneja con CSS (position: sticky)

**Observación:** ✅ Correcto - No necesita JS para sticky

### 7.7 CSS

**Archivo:** `/assets/blocks/pricing-card.css` (335 líneas)

**Características:**
- ✅ Sticky positioning (top: 2rem)
- ✅ Layout complejo con grid y flexbox
- ✅ Responsive design (1024px, 768px, 480px breakpoints)
- ✅ CSS variables (var(--wp--preset--color--secondary))
- ✅ Hover effects en CTA button
- ✅ SVG inline en template (NO en CSS)
- ⚠️ **Algunos valores hardcoded** (#F9F9F9, #202C2E, etc.)
- ⚠️ **!important en width** (línea 206 - .pricing-card__details-grid)

**Organización:**
- Secciones claras (duration, white-box, price-section, CTA, etc.)
- Comentarios descriptivos
- Cascada lógica

**Calidad:** 8/10 - Completo y responsive

### 7.8 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.9 Dependencias Externas

- ACF get_field() (6 campos diferentes)
- WordPress get_term() (para type_service)
- WordPress get_the_ID()
- WordPress is_wp_error()
- EditorHelper::is_editor_mode() ✅

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ⚠️ **VIOLA LEVEMENTE**
- Clase hace VARIAS cosas:
  - Registrar bloque
  - Enqueue assets
  - Calcular precios
  - Calcular duración
  - **Contar comidas desde itinerario** ← Responsabilidad compleja
  - Cargar template
- **count_meals_from_itinerary()** debería estar en una clase separada (ItineraryService?)
- **Impacto:** MEDIO - El método de conteo es complejo

**OCP:** ⚠️ **VIOLA**
- No hereda de BlockBase → Difícil extender
- Hardcoded meal names (breakfast, lunch, dinner) → NO configurable
- **Impacto:** MEDIO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con mejores bloques
- **Impacto:** MEDIO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Acoplado directamente a:
  - ACF get_field()
  - WordPress get_term()
  - Taxonomía type_service
  - Nombres específicos de términos
- No hay abstracción/interfaces
- **Impacto:** MEDIO - El conteo de comidas es muy acoplado

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **TODOS los métodos <50 líneas** (EXCELENTE)
- ✅ Método más largo: count_meals_from_itinerary() 36 líneas
- ⚠️ count_meals_from_itinerary() tiene **complejidad ciclomática alta** (nested loops + conditionals)

**Anidación:**
- ⚠️ **count_meals_from_itinerary() tiene 4 niveles** de anidación
  - foreach days
    - if active
      - foreach items
        - if type_service
- ⚠️ Supera recomendación de 3 niveles

**Duplicación:**
- ⚠️ **Duplicación en detección de comidas** (6 strpos() similares)
- ⚠️ **Duplicación en pluralización** (day/days, night/nights)

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ⚠️ $service_name no es muy descriptivo (debería ser $service_type_name)

**Código Sin Uso:**
- ✅ No detectado

**DocBlocks:**
- ❌ **0/7 métodos documentados** (0%)
- ❌ Header de archivo tiene descripción básica pero incompleta
- ❌ **NO documenta estructura de itinerary** (CRÍTICO - es complejo)
- ❌ NO documenta params/return types
- **Impacto:** ALTO - La estructura de itinerary es compleja y NO está documentada

**Magic Values:**
- ⚠️ 'breakfast', 'desayuno', 'lunch', 'almuerzo', 'dinner', 'cena' hardcoded (deberían ser constantes)
- ⚠️ 'type_service' taxonomy hardcoded (debería ser constante)
- ⚠️ 'large' size en CSS (no configurable)
- ⚠️ Colores hardcoded en CSS

### 8.3 Problemas de Seguridad

**Sanitización:**
- ⚠️ **NO sanitiza precios** antes de usar (pero floatval() los convierte)
- ⚠️ **NO sanitiza days** antes de usar (pero intval() los convierte)
- ⚠️ **NO valida estructura de itinerary** antes de recorrer
- ✅ get_field() de ACF es seguro
- **Impacto:** BAJO - Conversiones de tipo protegen

**Escapado:**
- ✅ Template usa esc_attr() para block_id, class_name
- ✅ Template usa esc_html() para todos los outputs de texto
- ✅ Template usa esc_html_e() para traducciones
- ✅ number_format() + esc_html() para precio
- ✅ Escapado correcto en error messages
- **Impacto:** NINGUNO - Perfecto

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ✅ No hace queries directas

**XSS:**
- ✅ **TODO escapado correctamente** en template

**Price Manipulation:**
- ⚠️ **NO valida que precios sean positivos** (puede mostrar $0 o negativos)
- **Impacto:** BAJO - Es presentación, no transacción

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ✅ **Template separado** (pricing-card.php)
- ✅ Lógica de negocio en clase
- ✅ Presentación en template
- ✅ Estilos en CSS separado

**Acoplamiento:**
- ⚠️ **Alto acoplamiento** a:
  - ACF (6 campos)
  - Taxonomía type_service
  - Nombres específicos de términos
  - Estructura específica de itinerary
- **Impacto:** MEDIO

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
- ⚠️ **Método count_meals_from_itinerary()** debería estar en servicio separado

---

## 9. Recomendaciones de Refactorización

### Prioridad Alta

**1. Heredar de BlockBase**
- **Acción:** `class PricingCard extends BlockBase`
- **Razón:** Consistencia, funcionalidades compartidas
- **Riesgo:** MEDIO - Requiere refactorizar
- **Precauciones:**
  - Mover config a properties
  - Usar parent::register()
  - Adaptar enqueue_assets()
- **Esfuerzo:** 1 hora

**2. Extraer conteo de comidas a servicio separado**
- **Acción:**
  ```php
  // Crear: Travel\Blocks\Services\ItineraryMealCounter
  class ItineraryMealCounter
  {
      private const MEAL_TYPES = [
          'breakfast' => ['breakfast', 'desayuno'],
          'lunch' => ['lunch', 'almuerzo'],
          'dinner' => ['dinner', 'cena'],
      ];

      public function count(array $itinerary): array
      {
          // Mover lógica aquí
      }

      private function getMealType(string $serviceName): ?string
      {
          // Detectar tipo de comida
      }
  }

  // En PricingCard:
  $mealCounter = new ItineraryMealCounter();
  $meals = $mealCounter->count($itinerary);
  ```
- **Razón:** SRP, reduce complejidad, reusabilidad, testabilidad
- **Riesgo:** MEDIO
- **Esfuerzo:** 1.5 horas

**3. Validar estructura de itinerary**
- **Acción:**
  ```php
  private function count_meals_from_itinerary(int $post_id): array
  {
      $itinerary = get_field('itinerary', $post_id);
      $meals = ['breakfast' => 0, 'lunch' => 0, 'dinner' => 0];

      if (!is_array($itinerary) || empty($itinerary)) {
          return $meals;
      }

      foreach ($itinerary as $day) {
          // Validar estructura de $day
          if (!is_array($day) || !isset($day['items'])) {
              continue;
          }
          // ...
      }
  }
  ```
- **Razón:** Prevenir warnings/notices si estructura cambia
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**4. Documentar estructura de itinerary**
- **Acción:**
  ```php
  /**
   * Count meals from package itinerary
   *
   * Expected itinerary structure:
   * [
   *     [
   *         'active' => true,
   *         'items' => [
   *             [
   *                 'type_service' => 123, // term_id from type_service taxonomy
   *             ]
   *         ]
   *     ]
   * ]
   *
   * Detected meal types (case-insensitive):
   * - breakfast/desayuno
   * - lunch/almuerzo
   * - dinner/cena
   *
   * @param int $post_id Package post ID
   * @return array ['breakfast' => int, 'lunch' => int, 'dinner' => int]
   */
  private function count_meals_from_itinerary(int $post_id): array
  ```
- **Razón:** Documentación crítica de estructura compleja
- **Riesgo:** NINGUNO
- **Esfuerzo:** 30 min

### Prioridad Media

**5. Convertir hardcoded meal names a constantes**
- **Acción:**
  ```php
  private const MEAL_TYPES = [
      'breakfast' => ['breakfast', 'desayuno'],
      'lunch' => ['lunch', 'almuerzo'],
      'dinner' => ['dinner', 'cena'],
  ];

  private function detectMealType(string $serviceName): ?string
  {
      $serviceName = strtolower($serviceName);

      foreach (self::MEAL_TYPES as $mealType => $keywords) {
          foreach ($keywords as $keyword) {
              if (strpos($serviceName, $keyword) !== false) {
                  return $mealType;
              }
          }
      }

      return null;
  }
  ```
- **Razón:** Mantenibilidad, configurabilidad, DRY
- **Riesgo:** BAJO
- **Esfuerzo:** 45 min

**6. Agregar validación de precios positivos**
- **Acción:**
  ```php
  $price_offer = max(0, floatval(get_field('price_offer', $post_id)));
  $price_from = max(0, floatval(get_field('price_from', $post_id)));
  $price_normal = max(0, floatval(get_field('price_normal', $post_id)));

  $price = $price_offer ?: ($price_from ?: $price_normal);

  // Si no hay precio válido, usar 0
  if ($price <= 0) {
      $price = 0;
  }
  ```
- **Razón:** Prevenir precios negativos o inválidos
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**7. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con params, returns, description
- **Razón:** Documentación para mantenimiento
- **Riesgo:** NINGUNO
- **Esfuerzo:** 45 min

**8. Conditional CSS loading**
- **Acción:**
  ```php
  public function enqueue_assets(): void
  {
      if (!is_admin() && is_singular('package')) {
          wp_enqueue_style(
              'pricing-card-style',
              TRAVEL_BLOCKS_URL . 'assets/blocks/pricing-card.css',
              [],
              TRAVEL_BLOCKS_VERSION
          );
      }
  }
  ```
- **Razón:** Performance - Solo cargar CSS donde se necesita
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**9. Reducir anidación en count_meals_from_itinerary()**
- **Acción:**
  ```php
  private function count_meals_from_itinerary(int $post_id): array
  {
      $itinerary = get_field('itinerary', $post_id);
      $meals = ['breakfast' => 0, 'lunch' => 0, 'dinner' => 0];

      if (!is_array($itinerary)) {
          return $meals;
      }

      foreach ($itinerary as $day) {
          if (!$this->isDayActive($day)) {
              continue;
          }

          $this->countDayMeals($day, $meals);
      }

      return $meals;
  }

  private function isDayActive(array $day): bool
  {
      return isset($day['active']) && $day['active'];
  }

  private function countDayMeals(array $day, array &$meals): void
  {
      if (empty($day['items']) || !is_array($day['items'])) {
          return;
      }

      foreach ($day['items'] as $item) {
          $mealType = $this->getMealTypeFromItem($item);
          if ($mealType) {
              $meals[$mealType]++;
          }
      }
  }
  ```
- **Razón:** Reducir complejidad, mejorar legibilidad
- **Riesgo:** MEDIO
- **Esfuerzo:** 1 hora

### Prioridad Baja

**10. Crear block.json**
- **Acción:** Migrar de register_block_type() a block.json
- **Razón:** Gutenberg moderno, mejor performance
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**11. Agregar filtro para meal types**
- **Acción:**
  ```php
  $meal_types = apply_filters('travel_blocks_pricing_card_meal_types', [
      'breakfast' => ['breakfast', 'desayuno'],
      'lunch' => ['lunch', 'almuerzo'],
      'dinner' => ['dinner', 'cena'],
  ]);
  ```
- **Razón:** Permitir customización por temas/plugins
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**12. Corregir ortografía en preview data**
- **Acción:** Cambiar "Lunchs" a "Lunches"
- **Razón:** Ortografía correcta
- **Riesgo:** NINGUNO
- **Esfuerzo:** 5 min

---

## 10. Plan de Acción

### Fase 1 - Alta Prioridad (Esta semana)
1. Heredar de BlockBase (1 hora)
2. Extraer conteo de comidas a servicio separado (1.5 horas)
3. Validar estructura de itinerary (30 min)
4. Documentar estructura de itinerary (30 min)

**Total Fase 1:** 3.5 horas

### Fase 2 - Media Prioridad (Próximas 2 semanas)
5. Convertir meal names a constantes (45 min)
6. Validación de precios positivos (15 min)
7. Agregar DocBlocks (45 min)
8. Conditional CSS loading (15 min)
9. Reducir anidación en count_meals (1 hora)

**Total Fase 2:** 3 horas

### Fase 3 - Baja Prioridad (Cuando haya tiempo)
10. Crear block.json (30 min)
11. Filtro para meal types (20 min)
12. Corregir ortografía preview (5 min)

**Total Fase 3:** 55 min

**Total Refactorización Completa:** ~7 horas 25 min

**Precauciones Generales:**
- ⚠️ **MUY IMPORTANTE:** El conteo de comidas es CRÍTICO - Probar exhaustivamente
- ⚠️ **NO cambiar lógica de prioridad de precios** sin consultar
- ⚠️ **NO cambiar nombres de meal types** sin verificar que términos existentes siguen funcionando
- ✅ SIEMPRE probar con paquetes que tienen itinerarios reales
- ✅ SIEMPRE probar días activos/inactivos
- ✅ Validar que CSS sticky funciona después de cambios

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra datos hardcoded)
- [ ] Frontend funciona (muestra datos reales)
- [ ] Sticky positioning funciona

### Precios
- [ ] price_offer tiene prioridad
- [ ] price_from es fallback de offer
- [ ] price_normal es fallback final
- [ ] Precio se muestra sin decimales (number_format)
- [ ] Precio 0 se maneja correctamente
- [ ] Escapado de precio correcto

### Duración
- [ ] Campo 'days' se obtiene correctamente
- [ ] Nights se calcula (days - 1)
- [ ] Pluralización funciona (day/days, night/nights)
- [ ] Formato "4 days / 3 nights" correcto
- [ ] Caso 1 día funciona (1 day / 0 nights)
- [ ] Caso 0 días funciona (empty strings)

### Conteo de Comidas (CRÍTICO)
- [ ] Itinerary se obtiene correctamente
- [ ] Días activos se cuentan
- [ ] Días inactivos se ignoran
- [ ] Breakfast/desayuno se detecta
- [ ] Lunch/almuerzo se detecta
- [ ] Dinner/cena se detecta
- [ ] Contadores se incrementan correctamente
- [ ] Estructura inválida NO causa warnings
- [ ] Terms de type_service se obtienen correctamente
- [ ] is_wp_error check funciona

### Template
- [ ] load_template() carga correctamente
- [ ] extract() crea variables correctamente
- [ ] Todas las variables están disponibles en template
- [ ] SVGs se muestran correctamente
- [ ] Grid de detalles se muestra
- [ ] CTA button funciona (#booking-form anchor)
- [ ] "Is this tour for me?" se muestra

### CSS
- [ ] Sticky positioning funciona (top: 2rem)
- [ ] Estilos se aplican correctamente
- [ ] Responsive funciona (1024px, 768px, 480px)
- [ ] CTA hover effects funcionan
- [ ] Grid layout funciona
- [ ] SVG sizing correcto
- [ ] Conditional loading funciona (si se agregó)

### Seguridad
- [ ] esc_attr() en block_id, class_name ✅
- [ ] esc_html() en todos los outputs de texto ✅
- [ ] number_format() + esc_html() en precio ✅
- [ ] Validación de estructura itinerary (si se agregó)
- [ ] Validación de precios positivos (si se agregó)

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] ItineraryMealCounter existe (si se creó)
- [ ] Servicio funciona correctamente (si se creó)
- [ ] Constantes definidas (si se agregaron)
- [ ] block.json (si se creó)
- [ ] Filtros funcionan (si se agregaron)

### Clean Code
- [ ] Métodos <50 líneas ✅ (ya cumple)
- [ ] Anidación <3 niveles (si se redujo)
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] No magic values (si se convirtieron a constantes)
- [ ] No duplicación (si se eliminó)

### Performance
- [ ] CSS solo se carga donde se necesita (si se agregó conditional)
- [ ] NO hay queries N+1
- [ ] get_term() se llama eficientemente

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Template separado (pricing-card.php - 166 líneas)
- ✅ CSS completo y responsive (335 líneas)
- ✅ Usa EditorHelper correctamente
- ✅ Try-catch wrapper en render()
- ✅ Escapado de seguridad correcto
- ✅ Lógica de precios con prioridad clara
- ✅ Cálculo inteligente de duración (days/nights)
- ✅ Preview data útil
- ✅ Sticky positioning con CSS
- ⚠️ **count_meals_from_itinerary() es complejo** (36 líneas, 4 niveles anidación)
- ⚠️ **Alto acoplamiento a taxonomía** type_service
- ⚠️ **Hardcoded meal names** (breakfast, lunch, dinner en strings)
- ⚠️ **NO valida estructura de itinerary**
- ⚠️ **NO documenta estructura de itinerary** (CRÍTICO)
- ❌ NO hereda de BlockBase
- ❌ NO tiene DocBlocks (0/7 métodos)
- ❌ Método de conteo debería estar en servicio separado

### Puntuación: 7.5/10

**Razones para la puntuación:**
- ➕ Template bien separado (+1)
- ➕ Lógica de precios clara (+0.5)
- ➕ Cálculo de duración inteligente (+0.5)
- ➕ Usa EditorHelper (+0.5)
- ➕ Try-catch wrapper (+0.5)
- ➕ Escapado correcto (+1)
- ➕ CSS completo y sticky (+1)
- ➕ Preview mode útil (+0.5)
- ➕ Métodos bien dimensionados (+0.5)
- ➕ Responsive design (+0.5)
- ➖ count_meals muy complejo (-0.5)
- ➖ Alto acoplamiento a taxonomía (-0.5)
- ➖ NO valida estructura itinerary (-0.5)
- ➖ NO documenta estructura (-0.5)
- ➖ NO hereda BlockBase (-0.5)
- ➖ Sin DocBlocks (-0.5)
- ➖ Hardcoded meal names (-0.5)

### Fortalezas
1. **Template separado:** Buena separación de concerns (166 líneas de HTML)
2. **Lógica de precios clara:** Prioridad offer > from > normal bien implementada
3. **Cálculo inteligente de duración:** Days → nights con pluralización
4. **Conteo de comidas desde itinerary:** Funcionalidad compleja pero útil
5. **EditorHelper usado correctamente:** is_editor_mode() para preview
6. **Try-catch wrapper:** Manejo de errores robusto
7. **Escapado perfecto:** esc_html(), esc_attr(), esc_html_e() everywhere
8. **CSS completo:** Sticky positioning, responsive, hover effects
9. **Preview data útil:** Datos de ejemplo claros
10. **Sticky positioning:** Implementado con CSS (no JS)

### Debilidades
1. ❌ **count_meals_from_itinerary() demasiado complejo** - 36 líneas, 4 niveles anidación, alto acoplamiento
2. ❌ **NO hereda de BlockBase** - Inconsistente
3. ❌ **NO documenta estructura de itinerary** - CRÍTICO (estructura compleja NO documentada)
4. ⚠️ **NO valida estructura de itinerary** - Puede causar warnings
5. ⚠️ **Hardcoded meal names** - Deberían ser constantes configurables
6. ⚠️ **Alto acoplamiento a taxonomía** type_service con nombres específicos
7. ⚠️ **strpos() detection frágil** - Depende de convenciones de nombres
8. ⚠️ **Método de conteo debería ser servicio separado** - Viola SRP
9. ❌ **NO tiene DocBlocks** (0/7 métodos)
10. ⚠️ **NO conditional CSS loading** - CSS se carga en todas las páginas

### Recomendación Principal

**Este es un BLOQUE MUY BUENO pero necesita refactorización en el método de conteo de comidas.**

**PROBLEMA CRÍTICO:** El método `count_meals_from_itinerary()` es **demasiado complejo** (36 líneas, 4 niveles de anidación, alto acoplamiento a taxonomía). Debería extraerse a un servicio separado (`ItineraryMealCounter`).

**Prioridad Alta (Esta semana - 3.5 horas):**
1. Heredar de BlockBase (1 hora) - Consistencia arquitectónica
2. **Extraer conteo de comidas a servicio separado (1.5 horas)** - CRÍTICO para SRP
3. Validar estructura de itinerary (30 min) - Prevenir warnings
4. **Documentar estructura de itinerary (30 min)** - CRÍTICO para mantenimiento

**Prioridad Media (2 semanas - 3 horas):**
5. Convertir meal names a constantes (45 min) - Configurabilidad
6. Validación de precios positivos (15 min) - Robustez
7. DocBlocks completos (45 min) - Documentación
8. Conditional CSS loading (15 min) - Performance
9. Reducir anidación en count_meals (1 hora) - Clean Code

**Prioridad Baja (Cuando haya tiempo - 55 min):**
10. block.json (30 min)
11. Filtro para meal types (20 min)
12. Corregir "Lunchs" → "Lunches" (5 min)

**Esfuerzo total:** ~7 horas 25 min de refactorización

**Veredicto:** Este bloque es **muy bueno** en funcionalidad pero tiene un **punto crítico de complejidad** en el método de conteo de comidas. La lógica de precios, duración y template están muy bien. El método `count_meals_from_itinerary()` necesita **refactorización urgente** para:
- Extraer a servicio separado (SRP)
- Reducir anidación (Clean Code)
- Documentar estructura esperada (Mantenibilidad)
- Validar estructura antes de recorrer (Robustez)

**PRIORIDAD: Refactorización MEDIA-ALTA - El bloque funciona pero el método de conteo necesita mejoras urgentes.**

### Dependencias Identificadas

**ACF:**
- `price_offer` (float) - Precio con oferta
- `price_from` (float) - Precio desde
- `price_normal` (float) - Precio normal
- `days` (int) - Número de días
- `accommodation` (string) - Descripción de alojamiento
- `itinerary` (array) - **Estructura compleja NO documentada**

**Taxonomía:**
- `type_service` - Para detectar tipo de comidas
- **Nombres específicos requeridos:** breakfast/desayuno, lunch/almuerzo, dinner/cena

**WordPress:**
- get_term() - Obtener término de taxonomía
- get_the_ID() - Obtener post ID
- is_wp_error() - Check de errores

**Helpers:**
- EditorHelper::is_editor_mode() ✅

**JavaScript:**
- ❌ **NO tiene JavaScript** (sticky con CSS)

**CSS:**
- pricing-card.css (335 líneas)
- Sticky positioning
- Responsive design
- Hover effects

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** MEDIA-ALTA - Refactorizar método count_meals_from_itinerary urgentemente
**Próxima revisión:** Después de refactorización Fase 1
