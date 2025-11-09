# Auditoría: DatesAndPrices (Package) - BLOQUE CRÍTICO

**Fecha:** 2025-11-09
**Bloque:** 03/03 Package
**Tiempo:** 45 min
**⚠️ ESTADO:** CRÍTICO - Maneja booking wizard y fechas/precios

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `travel-blocks/dates-and-prices`
- **Namespace:** `Travel\Blocks\Blocks\Package`
- **Template path:** `/templates/dates-and-prices.php`
- **Campos ACF:** `tour_id`, `months`, `fixed_departures`, `free_spot_start_day`, `days`, `default_spots`, `price_from`, `price_normal`, `departure_exceptions`, `promo`, `booking_anchor_id`
- **API Endpoint:** `https://cms.valenciatravelcusco.com/packages/tours/{tour_id}/calendar`
- **Data structure:** Cambiar estructura de datos puede romper booking wizard
- **Custom events:** `travelBlocksDateSelected`, `travelBlocksContactRequested`, `travelBlocksPurchaseRequested`
- **SessionStorage keys:** `selectedDepartureDate`, `selectedReturnDate`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **NO hereda de BlockBase** ❌ (inconsistente con mejores bloques)
- **API externa sin caché** ⚠️ (rendimiento)
- **SSL verify disabled** 🚨 **RIESGO DE SEGURIDAD CRÍTICO**
- **get_preview_data() tiene 493 líneas** ❌ (violación masiva Clean Code)
- **API integration compleja** con lógica de negocio (tipos: spots_api, fidex_week, fixed_dates, no_program)
- **Pricing logic crítica** - NUNCA cambiar cálculos sin pruebas exhaustivas
- **JavaScript embebido en template** (líneas 318-326) - Datos JSON para JS
- **file_get_contents como fallback** ⚠️ (línea 977)

### 🔒 DEPENDENCIAS CRÍTICAS EXTERNAS
- **API Externa:** `https://cms.valenciatravelcusco.com/packages/tours/{tour_id}/calendar`
- **EditorHelper:** Para detectar modo preview
- **ACF:** Para campos (no registra en código, asume que existen)
- **WordPress HTTP API:** wp_remote_get
- **JavaScript:** dates-and-prices.js (interactividad crítica)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/DatesAndPrices.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** `/templates/dates-and-prices.php` (339 líneas)
**Assets:**
- CSS: `/assets/blocks/dates-and-prices.css` (755 líneas)
- JS: `/assets/blocks/dates-and-prices.js` (554 líneas)

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Dependencias:**
- ❌ NO hereda de BlockBase (problema arquitectónico)
- EditorHelper (para detectar editor mode)
- ACF fields (NO los registra, asume que existen)
- API externa (opcional, con fallback a ACF)

**Líneas de Código:**
- **Clase PHP:** 1217 líneas
- **Template:** 339 líneas
- **JavaScript:** 554 líneas
- **CSS:** 755 líneas
- **TOTAL:** 2865 líneas

---

## 2. Propósito y Funcionalidad

**Descripción:** Bloque de booking wizard que muestra fechas de salida con precios, disponibilidad y sistema de navegación año/mes. Soporta generación automática de fechas, excepciones manuales e integración con API externa.

**Funcionalidad Principal:**
1. **Sistema de fechas flexible:**
   - Generación automática basada en meses + días de semana + día inicio
   - Excepciones manuales (departure_exceptions repeater)
   - Integración API externa (si tiene tour_id)
   - Preview data (datos hardcoded para editor)

2. **Sistema de precios:**
   - Precio base (price_from)
   - Precio normal/oferta (price_regular/price_offer)
   - Descuentos automáticos
   - Deals visuales

3. **Sistema de disponibilidad:**
   - available (>5 spots)
   - limited (≤5 spots)
   - sold_out (0 spots)

4. **API Integration (opcional):**
   - Fetches calendar data por tour_id, año, mes
   - Tipos: spots_api, fidex_week, fixed_dates, no_program
   - Lógica compleja de botones (BOOK NOW, CONTACT US, SOLD OUT)
   - Promociones condicionales

5. **Navegación interactiva:**
   - Year tabs (flotantes en borde superior)
   - Month navigation (prev/next)
   - Month select popover
   - Filtrado dinámico de tarjetas

**Inputs (ACF - NO registrados en código):**
- `tour_id` (number) - ID de tour para API
- `months` (array) - Meses activos
- `fixed_departures` (array) - Días de semana
- `free_spot_start_day` (number) - Día de inicio
- `days` (number) - Duración del paquete
- `default_spots` (number) - Espacios por defecto
- `price_from` (number) - Precio base
- `price_normal` (number) - Precio normal
- `departure_exceptions` (repeater) - Excepciones manuales
  - `date` (date)
  - `spots` (number)
  - `price_regular` (number)
  - `price_offer` (number)
- `promo` (boolean) - Promo activa (para API)
- `booking_anchor_id` (text) - Anchor para scroll

**Outputs:**
- Section con year tabs
- Month navigation
- Trip list (scrollable, max-height: 480px)
- Trip cards (dates, price, deal badge, CTA)
- Legend chips (floating en borde inferior)
- Booking alert (fuera del container)
- JSON data embebido para JavaScript

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ❌ **NO hereda de BlockBase** (problema arquitectónico)
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
private string $name = 'dates-and-prices';
private string $title = 'Dates and Prices';
private string $description = 'Display departure dates with pricing';
```

**Métodos Públicos:**
```php
1. register(): void - Registra bloque (15 líneas)
2. enqueue_assets(): void - Encola assets (10 líneas)
3. render($attributes, $content, $block): string - Renderiza (75 líneas)
```

**Métodos Privados:**
```php
4. get_preview_data(): array - Preview data (493 líneas) ❌ MASIVO
5. get_post_data(int $post_id): array - Obtiene fechas (71 líneas)
6. get_departure_dates(int $post_id): array - Excepciones (79 líneas)
7. calculate_availability(string $status, int $spots): string - Calcula disponibilidad (12 líneas)
8. generate_automatic_dates(array $months, array $weekdays, int $start_day, int $years_ahead = 3): array - Genera fechas (74 líneas)
9. group_dates_by_year_month(array $dates): array - Agrupa fechas (32 líneas)
10. fetch_api_calendar(int $tour_id, int $year, int $month): array - API call (78 líneas)
11. transform_api_data_to_dates(array $api_data, int $duration, bool $promo_active, string $anchor_id): array - Transforma API (136 líneas) ❌ LARGO
12. get_api_data(int $post_id, int $tour_id): array - Obtiene datos API (30 líneas)
13. load_template(string $template_name, array $data = []): void - Carga template (10 líneas)
```

**Total:** 13 métodos, 1217 líneas

**Métodos más largos:**
1. ❌ `get_preview_data()` - **493 líneas** (VIOLACIÓN MASIVA)
2. ❌ `transform_api_data_to_dates()` - **136 líneas** (VIOLACIÓN)
3. ⚠️ `get_departure_dates()` - **79 líneas** (límite)
4. ⚠️ `fetch_api_calendar()` - **78 líneas** (límite)
5. ⚠️ `render()` - **75 líneas** (límite)
6. ⚠️ `generate_automatic_dates()` - **74 líneas** (límite)

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/dates-and-prices`
- api_version: 2
- category: `template-blocks`
- icon: `calendar-alt`
- keywords: ['dates', 'prices', 'departures', 'calendar']
- supports: anchor, html: false
- render_callback: `[$this, 'render']`
- uses_context: postId, postType

**Enqueue Assets:**
- CSS: `/assets/blocks/dates-and-prices.css` (frontend + editor)
- JS: `/assets/blocks/dates-and-prices.js` (solo frontend, NO admin)
- Encolado en método separado `enqueue_assets()`
- Hook: `enqueue_block_assets`

**Block.json:** ❌ No existe (debería tenerlo para Gutenberg moderno)

**Campos ACF:** ❌ **NO REGISTRA CAMPOS** (asume que existen)

---

## 5. Campos ACF

**Definición:** ❌ **NO REGISTRA CAMPOS EN CÓDIGO**

**Campos usados (asume que existen):**
- `tour_id` - API integration
- `months` - Meses activos
- `fixed_departures` - Días de semana
- `free_spot_start_day` - Día inicio
- `days` - Duración
- `default_spots` - Espacios default
- `price_from` - Precio base
- `price_normal` - Precio normal
- `departure_exceptions` (repeater):
  - `date`
  - `spots`
  - `price_regular`
  - `price_offer`
- `promo` - Promo activa
- `booking_anchor_id` - Anchor ID

**Problemas:**
- ❌ **NO registra campos** - Depende de que estén definidos externamente
- ❌ **NO documenta campos** - No hay PHPDoc de estructura esperada
- ❌ **NO valida campos** - get_field() sin validación
- ❌ **NO sanitiza campos** - Usa valores directamente
- ⚠️ Asume estructura específica (e.g., repeater con keys exactas)

**Prefijos:**
- ❌ No hay consistencia (algunos campos sin prefijo)

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Get post_id de block context o current post (líneas 44-56)
2. Detecta preview mode con EditorHelper (línea 59)
3. Get dates: preview data vs post data vs API data (línea 62)
4. Si no hay dates: return empty state (líneas 65-75)
5. Group dates by year/month (línea 78)
6. Extract available years (línea 81)
7. Determine initial year/month (líneas 84-91)
8. Build $data array (líneas 93-107)
9. Load template con ob_start/ob_get_clean (líneas 109-111)
10. Try-catch con error display si WP_DEBUG (líneas 112-114)

**Flujo de Datos:**
```
render()
  → get_post_data()
    → check tour_id
      → SI: get_api_data()
        → fetch_api_calendar() (loop 2 años x 12 meses)
          → wp_remote_get() o file_get_contents
        → transform_api_data_to_dates()
      → NO: generate_automatic_dates() + get_departure_dates()
  → group_dates_by_year_month()
  → load_template()
```

**Variables al Template:**
```php
$data = [
    'block_id' => 'booking-' . uniqid(),
    'class_name' => 'dates-and-prices booking' . $attributes['className'],
    'grouped_dates' => $grouped_dates, // [year][month][dates]
    'all_dates' => $dates,
    'available_years' => $available_years,
    'current_year' => $current_year,
    'current_month' => $current_month,
    'currency_symbol' => 'USD $', // ⚠️ HARDCODED
    'button_text' => __('BOOK NOW', 'travel-blocks'),
    'alert_message' => __('Secure your spot...', 'travel-blocks'),
    'alert_emphasis' => __('Act quickly...', 'travel-blocks'),
    'is_preview' => $is_preview,
    'package_id' => $post_id,
];
```

**Manejo de Errores:**
- ✅ Try-catch en render()
- ✅ Error message si WP_DEBUG
- ✅ Empty state si no hay dates
- ⚠️ API errors solo logged, no mostrados
- ⚠️ NO valida estructura API response
- ⚠️ NO valida campos ACF antes de usar

---

## 7. Funcionalidades Adicionales

### 7.1 API Integration

**Endpoint:** `https://cms.valenciatravelcusco.com/packages/tours/{tour_id}/calendar?year={year}&month={month}`

**Método:** `fetch_api_calendar()`

**Problemas CRÍTICOS:**
- 🚨 **SSL verify disabled** (líneas 952, 972-973) - **RIESGO DE SEGURIDAD GRAVE**
  ```php
  'sslverify' => false,  // ❌ NUNCA hacer esto
  'ssl' => ['verify_peer' => false, 'verify_peer_name' => false] // ❌
  ```
- ❌ **NO cachea responses** - API call en cada render
- ❌ **file_get_contents como fallback** (línea 977) - No debería usarse
- ❌ **NO valida estructura response** - Solo check JSON válido
- ⚠️ **Timeout 15s** - Puede bloquear render
- ⚠️ **Fetches 24 months** (current year + next) - Muchos requests

**Lógica API por tipo:**
- `spots_api`: Días normales sin grupo asegurado
- `fidex_week`: Salidas fijas semanales (lógica compleja en líneas 1086-1122)
- `fixed_dates`: Fechas aseguradas con pasajeros (líneas 1123-1154)
- `no_program`: No programada → SOLD OUT

**Transform Logic:** 136 líneas en `transform_api_data_to_dates()` ❌

### 7.2 JavaScript

**Archivo:** `/assets/blocks/dates-and-prices.js` (554 líneas)

**Funcionalidades:**
- ✅ IIFE pattern (encapsulado)
- ✅ Public API expuesto (window.TravelBlocks.BookingDates)
- ✅ Year tabs navigation
- ✅ Month prev/next navigation
- ✅ Month select popover
- ✅ Visible dates filtering
- ✅ Booking button handlers
- ✅ Custom events:
  - `travelBlocksDateSelected`
  - `travelBlocksContactRequested`
  - `travelBlocksPurchaseRequested`
- ✅ SessionStorage para fechas seleccionadas
- ✅ Restore selected date on load
- ✅ Different button actions:
  - `default` (store + event)
  - `scroll_to_anchor` (scroll to form)
  - `contact` (contact event)
  - `open_purchase_aside` (purchase event)

**Calidad:** 8/10 - Bien estructurado, clean code

### 7.3 CSS

**Archivo:** `/assets/blocks/dates-and-prices.css` (755 líneas)

**Características:**
- ✅ CSS Variables (custom properties)
- ✅ Theme.json integration (--wp--preset--color--secondary)
- ✅ Responsive design (@media queries)
- ✅ Custom scrollbar
- ✅ Print styles
- ✅ Accessibility (sr-only)
- ✅ States (hover, disabled, active)
- ✅ API integration classes (booking-row--promo-fixed-week, etc.)

**Calidad:** 8/10 - Bien organizado, moderno

### 7.4 Hooks Propios

**Ninguno** - No usa hooks personalizados

### 7.5 Dependencias Externas

- EditorHelper (interno)
- ACF (asume campos existen)
- **API Externa:** `cms.valenciatravelcusco.com` 🚨 **DEPENDENCIA CRÍTICA**

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ❌ **VIOLA GRAVEMENTE**
- Clase hace DEMASIADAS cosas:
  - Render
  - API integration
  - Date generation
  - Data transformation
  - Pricing logic
  - Availability calculation
  - Template loading
- Debería dividirse en:
  - DatesAndPricesBlock (render)
  - DateGenerator (generate_automatic_dates)
  - ApiClient (fetch, transform)
  - PricingCalculator (pricing logic)
  - AvailabilityCalculator (availability)
- **Impacto:** CRÍTICO - 1217 líneas en una clase

**OCP:** ❌ **VIOLA**
- No hereda de BlockBase → Difícil extender
- Lógica API hardcoded → No se puede cambiar provider
- **Impacto:** ALTO

**LSP:** ❌ **VIOLA**
- NO hereda de BlockBase cuando debería
- Inconsistente con mejores bloques
- **Impacto:** ALTO

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ❌ **VIOLA GRAVEMENTE**
- Acoplado directamente a:
  - ACF (get_field hardcoded)
  - API externa específica (URL hardcoded)
  - WordPress HTTP API
  - EditorHelper
- No hay abstracción/interfaces
- **Impacto:** CRÍTICO - Imposible cambiar dependencias

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ **get_preview_data():** **493 líneas** - VIOLACIÓN MASIVA
  - Debería ser archivo JSON externo
  - Es solo data, no lógica
  - Impacto: CRÍTICO
- ❌ **transform_api_data_to_dates():** **136 líneas** - VIOLACIÓN GRAVE
  - Anidación >4 niveles
  - Multiple if/elseif chains
  - Debería dividirse en métodos por tipo
  - Impacto: ALTO
- ⚠️ **get_departure_dates():** 79 líneas (límite)
- ⚠️ **fetch_api_calendar():** 78 líneas (límite)
- ⚠️ **render():** 75 líneas (límite)
- ⚠️ **generate_automatic_dates():** 74 líneas (límite)

**Anidación:**
- ❌ transform_api_data_to_dates: >4 niveles (líneas 1066-1160)
- ⚠️ generate_automatic_dates: 4 niveles (líneas 852-886)
- ⚠️ get_departure_dates: 3 niveles

**Duplicación:**
- ⚠️ Lógica availability repetida (calculate_availability vs en template)
- ⚠️ Date parsing repetido
- ⚠️ Field access repetido (get_field múltiples veces)

**Nombres:**
- ✅ Buenos nombres de variables
- ✅ Métodos descriptivos
- ⚠️ `fidex_week` typo? (debería ser `fixed_week`)

**Código Sin Uso:**
- ✅ No detectado

**DocBlocks:**
- ❌ **0/13 métodos documentados** (0%)
- ⚠️ Solo algunos tienen comentarios inline
- ❌ NO documenta estructura API response
- ❌ NO documenta estructura date entry
- ❌ NO documenta params/return types
- **Impacto:** CRÍTICO - Código muy complejo sin docs

**Magic Numbers:**
- ⚠️ 50, 20, 10, 5 en transform_api_data_to_dates (deberían ser constantes)
- ⚠️ 480px max-height (debería ser configurable)
- ⚠️ 15s timeout (debería ser constante)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ❌ **NO sanitiza campos ACF** antes de usar
- ❌ **NO valida tour_id** (puede ser cualquier int)
- ❌ **NO valida API response structure**
- ⚠️ Asume que get_field() devuelve tipo correcto
- **Impacto:** MEDIO

**Escapado:**
- ✅ Template usa esc_html(), esc_attr(), esc_url() correctamente
- ✅ wp_json_encode() para JSON embebido

**SSL/TLS:**
- 🚨 **SSL verify disabled** (líneas 952, 972-973)
  ```php
  'sslverify' => false,  // ❌ CRÍTICO
  ```
- **Impacto:** CRÍTICO - Man-in-the-middle attacks
- **Recomendación:** ELIMINAR INMEDIATAMENTE

**Nonces:**
- ✅ N/A - No tiene formularios/AJAX

**Capabilities:**
- ✅ N/A - Bloque de lectura

**SQL:**
- ✅ No hace queries directas

**XSS:**
- ✅ Template escapa correctamente
- ✅ JSON data escapado con wp_json_encode()

**file_get_contents:**
- ❌ **Usado como fallback** (línea 977)
- No debería usarse para HTTP requests
- **Impacto:** MEDIO

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ Correcto: `Travel\Blocks\Blocks\Package`

**Separación MVC:**
- ⚠️ **Template tiene lógica** (cálculos, loops complejos)
  - Debería recibir datos ya procesados
  - Líneas 163-305: Loop complejo con lógica
- ⚠️ **JSON embebido en template** (líneas 318-326)
  - Debería estar en data attribute o variable JS
- **Impacto:** MEDIO

**Acoplamiento:**
- ❌ **Acoplamiento ALTO a API externa**
  - URL hardcoded (línea 942)
  - Estructura de response asumida
  - No hay abstracción
- ❌ **Acoplamiento a ACF**
  - get_field() hardcoded
  - Asume campos existen
- **Impacto:** CRÍTICO

**Herencia:**
- ❌ **NO hereda de BlockBase**
  - Inconsistente con mejores bloques
  - Duplica código (load_template, etc.)
- **Impacto:** ALTO

**Caché:**
- ❌ **NO cachea API responses**
  - Fetches 24 months en cada render
  - Sin transients/opciones
- **Impacto:** CRÍTICO - Rendimiento

**Otros:**
- ❌ **Preview data 493 líneas** en código (debería ser JSON)
- ❌ **Currency symbol hardcoded** 'USD $' (línea 101)
- ❌ **NO usa block.json** (debería para Gutenberg moderno)
- ⚠️ **Lógica de negocio compleja** mezclada con presentación
- ⚠️ **15s timeout puede bloquear** render

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este es un BLOQUE CRÍTICO que maneja booking/precios. NO cambiar sin pruebas exhaustivas.**

### Prioridad CRÍTICA (URGENTE)

**1. 🚨 ELIMINAR SSL verify disabled**
- **Acción:**
  ```php
  // Línea 952 - ELIMINAR sslverify: false
  $response = wp_remote_get($url, [
      'timeout' => 15,
      // 'sslverify' => false, // ❌ ELIMINAR ESTA LÍNEA
      'headers' => [...],
  ]);

  // Líneas 972-973 - ELIMINAR verify_peer: false
  $context = stream_context_create([
      'http' => [...],
      // 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false], // ❌ ELIMINAR
  ]);
  ```
- **Razón:** RIESGO DE SEGURIDAD CRÍTICO - MITM attacks
- **Riesgo:** BAJO - Solo eliminar líneas inseguras
- **Precauciones:**
  - Si API tiene SSL problems, arreglar en servidor API
  - NO deshabilitar SSL como "fix"
- **Esfuerzo:** 5 min
- **⚠️ HACER AHORA - NO ESPERAR**

**2. 🚨 Implementar caché para API responses**
- **Acción:**
  ```php
  private function fetch_api_calendar(int $tour_id, int $year, int $month): array
  {
      $cache_key = "tour_calendar_{$tour_id}_{$year}_{$month}";
      $cached = get_transient($cache_key);

      if ($cached !== false) {
          return $cached;
      }

      // ... existing fetch logic ...

      if (!empty($data)) {
          set_transient($cache_key, $data, 6 * HOUR_IN_SECONDS); // 6 horas
      }

      return $data;
  }
  ```
- **Razón:** Rendimiento - Fetches 24 API calls por render
- **Riesgo:** BAJO - Solo agrega caché
- **Precauciones:**
  - Invalidar caché cuando se actualice tour
  - TTL razonable (6h recomendado)
- **Esfuerzo:** 30 min

**3. 🚨 Extraer get_preview_data() a archivo JSON**
- **Acción:**
  ```php
  // Crear: /data/preview-dates.json
  // Mover array de líneas 119-608 a JSON

  private function get_preview_data(): array
  {
      $json_path = TRAVEL_BLOCKS_PATH . 'data/preview-dates.json';
      if (!file_exists($json_path)) {
          return [];
      }
      $json = file_get_contents($json_path);
      return json_decode($json, true) ?: [];
  }
  ```
- **Razón:** 493 líneas de data en código es violación masiva
- **Riesgo:** BAJO - Solo mueve data
- **Precauciones:** Verificar JSON válido
- **Esfuerzo:** 30 min

### Prioridad Alta

**4. Dividir transform_api_data_to_dates()**
- **Acción:** Crear métodos por tipo:
  ```php
  private function transform_api_data_to_dates(array $api_data, ...): array
  {
      $dates = [];
      foreach ($api_data as $date_str => $date_info) {
          $date_entry = $this->create_base_date_entry($date_str, $date_info, ...);
          $date_entry = $this->apply_type_logic($date_entry, $date_info, ...);
          $dates[] = $date_entry;
      }
      return $dates;
  }

  private function apply_type_logic(array $date_entry, array $date_info, ...): array
  {
      switch ($date_info['type']) {
          case 'spots_api':
              return $this->apply_spots_api_logic($date_entry, $date_info, ...);
          case 'fidex_week':
              return $this->apply_fidex_week_logic($date_entry, $date_info, ...);
          case 'fixed_dates':
              return $this->apply_fixed_dates_logic($date_entry, $date_info, ...);
          default:
              return $this->apply_no_program_logic($date_entry);
      }
  }
  ```
- **Razón:** 136 líneas, anidación >4 niveles
- **Riesgo:** MEDIO - Cambia estructura pero no lógica
- **Precauciones:**
  - Probar todos los tipos (spots_api, fidex_week, fixed_dates, no_program)
  - NO cambiar lógica, solo refactorizar
- **Esfuerzo:** 3 horas

**5. Crear clase ApiClient**
- **Acción:**
  ```php
  // Nuevo: /src/Services/TourApiClient.php
  class TourApiClient {
      private string $base_url;
      private int $cache_ttl = 6 * HOUR_IN_SECONDS;

      public function fetch_calendar(int $tour_id, int $year, int $month): array
      public function fetch_multiple_months(...): array
      private function cache_get(string $key)
      private function cache_set(string $key, $data)
  }

  // En DatesAndPrices:
  private TourApiClient $api_client;
  ```
- **Razón:** Separar responsabilidades, reducir acoplamiento
- **Riesgo:** ALTO - Requiere refactorizar flujo
- **Precauciones:** Probar integración completa
- **Esfuerzo:** 4 horas

**6. Validar API response structure**
- **Acción:**
  ```php
  private function validate_api_response(array $data): bool
  {
      foreach ($data as $date_str => $date_info) {
          if (!isset($date_info['type'], $date_info['price'], $date_info['spots'])) {
              return false;
          }
      }
      return true;
  }

  // En fetch_api_calendar, después de json_decode:
  if (!$this->validate_api_response($data)) {
      error_log('Invalid API response structure');
      return [];
  }
  ```
- **Razón:** Prevenir errores por cambios en API
- **Riesgo:** BAJO - Solo agrega validación
- **Esfuerzo:** 1 hora

**7. Heredar de BlockBase**
- **Acción:** `class DatesAndPrices extends BlockBase`
- **Razón:** Consistencia, evita duplicación
- **Riesgo:** ALTO - Requiere refactorizar
- **Precauciones:**
  - Mover config a properties
  - Usar parent::register()
  - Adaptar load_template()
- **Esfuerzo:** 4 horas

### Prioridad Media

**8. Eliminar file_get_contents fallback**
- **Acción:**
  ```php
  // ELIMINAR líneas 960-996 (fallback con file_get_contents)
  // Si wp_remote_get falla, return [] directamente
  ```
- **Razón:** No debería usarse para HTTP
- **Riesgo:** MEDIO - Puede afectar si wp_remote_get falla
- **Precauciones:** Arreglar causa de wp_remote_get failures
- **Esfuerzo:** 15 min

**9. Sanitizar campos ACF**
- **Acción:**
  ```php
  $tour_id = absint(get_field('tour_id', $post_id) ?: 0);
  $months = array_filter((array) get_field('months', $post_id));
  $price_from = floatval(get_field('price_from', $post_id) ?: 0);
  ```
- **Razón:** Seguridad, validación
- **Riesgo:** BAJO
- **Esfuerzo:** 1 hora

**10. Convertir magic numbers a constantes**
- **Acción:**
  ```php
  private const SPOTS_THRESHOLD_CONTACT = 50;
  private const SPOTS_THRESHOLD_LIMITED = 20;
  private const SPOTS_THRESHOLD_SOLDOUT = 5;
  private const DAYS_UNTIL_CLOSE = 10;
  private const DAYS_UNTIL_MEDIUM = 30;
  ```
- **Razón:** Mantenibilidad, claridad
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**11. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos:
  ```php
  /**
   * Fetch calendar data from API for specific tour, year, month
   *
   * @param int $tour_id Tour ID
   * @param int $year Year (YYYY)
   * @param int $month Month (1-12)
   * @return array API response data or empty array on error
   */
  private function fetch_api_calendar(int $tour_id, int $year, int $month): array
  ```
- **Razón:** Documentación crítica para código complejo
- **Riesgo:** NINGUNO
- **Esfuerzo:** 2 horas

**12. Crear block.json**
- **Acción:** Migrar de register_block_type() a block.json
- **Razón:** Gutenberg moderno, mejor performance
- **Riesgo:** MEDIO
- **Esfuerzo:** 1 hora

### Prioridad Baja

**13. Externalizar currency symbol**
- **Acción:** Hacer configurable via ACF o settings
- **Razón:** Hardcoded 'USD $'
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**14. Reducir timeout API**
- **Acción:** Cambiar de 15s a 5s
- **Razón:** 15s puede bloquear render
- **Riesgo:** MEDIO - Puede fallar si API es lenta
- **Esfuerzo:** 5 min

**15. Registrar campos ACF en código**
- **Acción:** Crear método register_fields()
- **Razón:** Actualmente asume que existen
- **Riesgo:** BAJO
- **Esfuerzo:** 2 horas

---

## 10. Plan de Acción

### Fase 1 - CRÍTICA (URGENTE - HOY)
1. 🚨 Eliminar SSL verify disabled (5 min)
2. 🚨 Implementar caché API (30 min)
3. 🚨 Extraer preview data a JSON (30 min)

**Total Fase 1:** 1 hora

### Fase 2 - Alta Prioridad (Esta semana)
4. Dividir transform_api_data_to_dates() (3 horas)
5. Crear clase ApiClient (4 horas)
6. Validar API response (1 hora)
7. Heredar de BlockBase (4 horas)

**Total Fase 2:** 12 horas

### Fase 3 - Media Prioridad (Próximas 2 semanas)
8. Eliminar file_get_contents (15 min)
9. Sanitizar campos ACF (1 hora)
10. Constantes para magic numbers (30 min)
11. Agregar DocBlocks (2 horas)
12. Crear block.json (1 hora)

**Total Fase 3:** 5 horas

### Fase 4 - Baja Prioridad (Cuando haya tiempo)
13. Externalizar currency (30 min)
14. Reducir timeout (5 min)
15. Registrar campos ACF (2 horas)

**Total Fase 4:** 2.5 horas

**Total Refactorización Completa:** ~20 horas

**Precauciones Generales:**
- ⛔ NUNCA cambiar lógica de pricing/availability sin tests
- ⛔ NUNCA cambiar estructura de datos sin migration plan
- ⛔ NUNCA deshabilitar SSL verify
- ⛔ NO cambiar API endpoint sin verificar
- ✅ SIEMPRE probar con diferentes tipos (spots_api, fidex_week, etc.)
- ✅ SIEMPRE verificar que booking wizard funciona
- ✅ SIEMPRE probar con y sin tour_id

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo
- [ ] Se puede insertar correctamente
- [ ] Preview mode funciona (muestra preview data)
- [ ] Frontend funciona (muestra fechas reales)
- [ ] Campos ACF funcionan

### Fechas
- [ ] Generación automática funciona (meses + días semana)
- [ ] Excepciones manuales funcionan
- [ ] API integration funciona (si tour_id)
- [ ] Agrupación por año/mes correcta
- [ ] Ordenamiento por fecha correcto

### API Integration
- [ ] SSL verify enabled (NO disabled) ✅ CRÍTICO
- [ ] API calls funcionan
- [ ] Caché funciona (no re-fetch innecesario)
- [ ] Timeout razonable (no bloquea)
- [ ] Error handling correcto
- [ ] Validación structure response
- [ ] Todos los tipos funcionan:
  - [ ] spots_api
  - [ ] fidex_week
  - [ ] fixed_dates
  - [ ] no_program

### Precios y Disponibilidad
- [ ] Precio base correcto
- [ ] Precio oferta correcto
- [ ] Descuentos calculados correctamente
- [ ] Availability states correctos:
  - [ ] available (>5 spots)
  - [ ] limited (≤5 spots)
  - [ ] sold_out (0 spots)
- [ ] Deals visuales funcionan

### JavaScript
- [ ] Year tabs funcionan
- [ ] Month navigation funciona
- [ ] Month select popover funciona
- [ ] Visible dates filtrado correcto
- [ ] Booking buttons funcionan
- [ ] Custom events se disparan:
  - [ ] travelBlocksDateSelected
  - [ ] travelBlocksContactRequested
  - [ ] travelBlocksPurchaseRequested
- [ ] SessionStorage funciona
- [ ] Restore selected date funciona
- [ ] Button actions funcionan:
  - [ ] default
  - [ ] scroll_to_anchor
  - [ ] contact
  - [ ] open_purchase_aside

### CSS
- [ ] Estilos se aplican correctamente
- [ ] Responsive funciona (móvil)
- [ ] Year tabs flotantes
- [ ] Legend chips flotantes
- [ ] Scrollbar custom funciona
- [ ] States funcionan (hover, disabled, active)
- [ ] Print styles funcionan

### Arquitectura (si se refactorizó)
- [ ] Hereda de BlockBase (si se cambió)
- [ ] ApiClient funciona (si se creó)
- [ ] Caché implementado
- [ ] Preview data en JSON (si se movió)
- [ ] Methods divididos (si se refactorizó)
- [ ] Constantes definidas
- [ ] block.json (si se creó)

### Seguridad
- [ ] SSL verify enabled ✅ CRÍTICO
- [ ] Campos ACF sanitizados
- [ ] API response validado
- [ ] Template escapa todo
- [ ] No file_get_contents (si se eliminó)

### Clean Code
- [ ] get_preview_data() <50 líneas (si se movió a JSON)
- [ ] transform_api_data_to_dates() <50 líneas (si se dividió)
- [ ] Métodos <50 líneas
- [ ] Anidación <3 niveles
- [ ] DocBlocks en todos los métodos (si se agregaron)
- [ ] No magic numbers (si se convirtieron a constantes)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funcionalidad compleja FUNCIONA
- ✅ Sistema flexible de fechas (auto + manual + API)
- ✅ JavaScript/CSS bien hechos
- ❌ 1217 líneas en una clase (demasiado)
- ❌ **get_preview_data() 493 líneas** (violación masiva)
- ❌ **transform_api_data_to_dates() 136 líneas** (violación)
- ❌ NO hereda de BlockBase
- 🚨 **SSL verify disabled** (CRÍTICO)
- ❌ NO cachea API responses
- ❌ NO tiene DocBlocks (0/13 métodos)
- ❌ Acoplamiento alto a API externa
- ⚠️ file_get_contents usado

### Puntuación: 4.5/10

**Razones para la puntuación:**
- ➕ Funciona bien (+2)
- ➕ JavaScript/CSS excelentes (+1.5)
- ➕ Sistema flexible (+1)
- ➖ SSL verify disabled (-2) 🚨
- ➖ get_preview_data 493 líneas (-1)
- ➖ NO hereda BlockBase (-1)
- ➖ Sin caché API (-0.5)
- ➖ Sin DocBlocks (-0.5)
- ➖ Violaciones SOLID (-1)

### Fortalezas
1. **Sistema flexible:** Auto-generación + excepciones + API
2. **JavaScript excelente:** Clean code, public API, custom events
3. **CSS moderno:** Variables, responsive, accessibility
4. **Preview data completo:** Editor experience bueno
5. **Multiple data sources:** API fallback a ACF
6. **Error handling:** Try-catch, empty states
7. **Semantic HTML:** Accessibility completo
8. **Custom events:** Fácil integración externa

### Debilidades CRÍTICAS
1. 🚨 **SSL verify disabled** - RIESGO DE SEGURIDAD GRAVE
2. ❌ **get_preview_data() 493 líneas** - Violación masiva
3. ❌ **transform_api_data_to_dates() 136 líneas** - Violación grave
4. ❌ **NO cachea API** - 24 requests por render
5. ❌ **NO hereda BlockBase** - Inconsistente
6. ❌ **NO documenta** - 0/13 métodos con DocBlocks
7. ❌ **Acoplamiento alto** a API externa
8. ❌ **NO valida** API response structure
9. ⚠️ **file_get_contents** usado
10. ⚠️ **Lógica compleja** mezclada

### Recomendación Principal

🚨 **ACCIÓN URGENTE REQUERIDA:**

1. **HOY (CRÍTICO):**
   - Eliminar SSL verify disabled (5 min) 🚨
   - Implementar caché API (30 min)
   - Extraer preview data a JSON (30 min)

2. **Esta semana (ALTO):**
   - Dividir métodos largos
   - Crear ApiClient
   - Validar API responses
   - Heredar de BlockBase

3. **Luego (MEDIO/BAJO):**
   - DocBlocks
   - Sanitización
   - block.json

**Esfuerzo total:** ~20 horas de refactorización

**Veredicto:** Este es un BLOQUE CRÍTICO que funciona pero tiene PROBLEMAS GRAVES de seguridad (SSL disabled), arquitectura (493 líneas en un método, no hereda BlockBase) y rendimiento (sin caché). La funcionalidad es excelente y el JavaScript/CSS están bien hechos, pero el código PHP necesita refactorización urgente. **PRIORIDAD: Fix SSL verify HOY, luego refactorización gradual.**

### Dependencias Críticas Identificadas

**API Externa:**
- Endpoint: `https://cms.valenciatravelcusco.com/packages/tours/{tour_id}/calendar`
- ⚠️ Dependencia CRÍTICA del negocio
- 🚨 SSL verify disabled (ARREGLAR URGENTE)
- ❌ Sin caché (rendimiento)
- ❌ Sin validación estructura

**JavaScript:**
- dates-and-prices.js (554 líneas)
- Custom events para booking wizard
- SessionStorage para persistencia

**ACF Fields (NO registrados):**
- tour_id, months, fixed_departures, free_spot_start_day, days, default_spots, price_from, price_normal, departure_exceptions, promo, booking_anchor_id

---

**Auditoría completada:** 2025-11-09
**Acción requerida:** CRÍTICA - Eliminar SSL verify disabled HOY, implementar caché esta semana
**Próxima revisión:** Después de Fase 1 (fixes críticos)
