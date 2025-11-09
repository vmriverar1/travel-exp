# Auditoría: Deal Info Card

**Fecha:** 2025-11-09
**Bloque:** 2 Deal
**Tiempo:** 45 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `deal-info-card`
- **Namespace:** `travel-blocks/deal-info-card` (Gutenberg nativo)
- **Clases CSS:** `.deal-info-card`, `.deal-info-card__discount`, `.deal-info-card__validity`, `.deal-info-card__button`, etc.
- **Meta keys:** `active`, `start_date`, `end_date`, `discount_percentage`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Método público `render()` - es callback de WordPress
- Método público `enqueue_assets()` - es hook de WordPress
- Lógica de cálculo de estados (active, scheduled, expired)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Deal/DealInfoCard.php`
**Namespace:** `Travel\Blocks\Blocks\Deal`
**Template:** `/wp-content/plugins/travel-blocks/templates/deal-info-card.php`
**Assets:**
- CSS: `/assets/blocks/deal-info-card.css` (199 líneas)
- JS: No tiene

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

---

## 2. Propósito y Funcionalidad

**Descripción:** Tarjeta sidebar sticky que muestra información clave de un deal: descuento, fechas de validez, estado, CTA de reserva y beneficios.

**Inputs (Post Meta):**
- `active` (bool): Deal activo/inactivo
- `start_date` (datetime): Fecha inicio del deal
- `end_date` (datetime): Fecha fin del deal
- `discount_percentage` (int): Porcentaje de descuento

**Outputs:**
- Badge de descuento con porcentaje
- Fechas de validez formateadas
- Estado del deal (active/scheduled/expired)
- Botón CTA "View Packages"
- Información de contacto
- Lista de beneficios

**Contexto:** Solo funciona en posts de tipo `deal`. Muestra preview con datos ficticios en editor.

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: Ninguna
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```
- $name: string = 'deal-info-card'
- $title: string = 'Deal Info Card'
- $description: string = 'Displays deal discount percentage, validity dates, and booking CTA'
```

**Métodos Públicos:**
```
1. register(): void - Registra el bloque y hook de assets
2. enqueue_assets(): void - Encola CSS en frontend
3. render($attributes, $content, $block): string - Renderiza el bloque
```

**Métodos Privados:**
```
1. get_deal_data(int $post_id): array - Obtiene y procesa datos del deal
2. render_preview_fallback(): string - Renderiza preview con datos ficticios
3. get_template(string $template_name, array $data): string - Carga template PHP
```

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/deal-info-card`
- api_version: 2
- title: "Deal Info Card"
- category: `travel`
- icon: `tag`
- keywords: ['deal', 'discount', 'offer', 'promo']
- render_callback: `$this->render()`
- supports: anchor=true, html=false

**Block.json:** ❌ No existe (debería existir para bloques nativos)

---

## 5. Campos ACF

**N/A** - No es bloque ACF, obtiene datos de post meta directamente.

**Post Meta Usado:**
1. `active` (true_false)
2. `start_date` (datetime)
3. `end_date` (datetime)
4. `discount_percentage` (number)

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. Post ID: `get_the_ID()`
2. Validación: Verifica que sea post type `deal`
3. Deal data: `$this->get_deal_data($post_id)`
4. Fallback: `render_preview_fallback()` si no es deal

**Procesamiento:**
1. Obtiene meta fields del post
2. Calcula estado basado en fechas actuales:
   - `scheduled`: Antes de start_date
   - `active`: Entre start_date y end_date
   - `expired`: Después de end_date
3. Formatea fechas con `date_i18n()`
4. Retorna array con datos procesados

**Variables al Template:**
```php
- $discount_percentage: int - Porcentaje de descuento
- $start_date: string - Fecha inicio raw
- $end_date: string - Fecha fin raw
- $start_date_formatted: string - Fecha inicio formateada
- $end_date_formatted: string - Fecha fin formateada
- $is_active: bool - Deal activo ahora
- $status: string - Estado (active/scheduled/expired)
```

**Lógica en Template:**
- Media: Condicionales múltiples para estados
- ⚠️ Lógica de presentación mezclada (wrapper_class)
- ✅ Todo escapado correctamente
- ❌ Email hardcodeado: `info@travel.com`
- ❌ Beneficios hardcodeados (no configurables)
- ❌ Link CTA hardcodeado: `#packages`

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:** No usa

**REST API:** No usa

**Hooks Propios:** No define

**Dependencias Externas:** No tiene

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ Cumple - Clase solo maneja tarjeta de info del deal

**OCP:** ✅ Cumple - Puede extenderse sin modificar

**LSP:** ✅ N/A - No hereda de ninguna clase

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA** - Dependencia directa de funciones globales WP
- Ubicación: Líneas 63-65, 83-86, 89-91
- Funciones: `get_the_ID()`, `get_post_type()`, `get_post_meta()`, `current_time()`, `strtotime()`
- Impacto: Medio (dificulta testing unitario)

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ Métodos <40 líneas: Todos OK
- ✅ Método más largo: `get_deal_data()` 36 líneas (aceptable)
- ✅ Código legible en general

**Anidación:**
- ✅ Máximo 2 niveles (aceptable)

**Duplicación:**
- ✅ No se detectó duplicación significativa

**Nombres:**
- ✅ Nombres descriptivos y consistentes

**Código Sin Uso:**
- ⚠️ Variable `$active` obtenida pero solo usada en una condición (línea 96)

**Malas Prácticas:**
- ❌ **Uso de extract() en get_template()**
  - Ubicación: Línea 147
  - Problema: `extract($data, EXTR_SKIP)` es inseguro y dificulta depuración
  - Impacto: Medio (riesgo de colisión de variables)

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ `intval()` usado para discount_percentage (línea 108)
- ⚠️ Meta fields no sanitizados (líneas 83-86)
- ⚠️ Fechas no validadas antes de `strtotime()`

**Escapado:**
- ✅ Template escapa todo correctamente
- ✅ `esc_attr()`, `esc_html()` usados consistentemente

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas

**Otros:**
- ⚠️ `extract()` puede ser riesgo de seguridad si `$data` no está controlado

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\Deal`
  - Esperado: `Travel\Blocks\Deal`
  - Ubicación: Línea 11
  - Impacto: Bajo (funciona pero no sigue convención)

**Separación MVC:**
- ✅ Bien separado - Controller (clase) / View (template)
- ⚠️ Template tiene lógica de presentación (líneas 14-17)

**Acoplamiento:**
- ⚠️ Alto acoplamiento con funciones globales WP
- ⚠️ Acoplamiento con tipo de post `deal` (hardcoded línea 65)
- ❌ **Email hardcodeado en template** (línea 86)
- ❌ **Beneficios hardcodeados en template** (líneas 91-100)
- ❌ **Link CTA hardcodeado** (línea 73: `#packages`)

**Configurabilidad:**
- ❌ **CRÍTICO: Email no configurable**
- ❌ **CRÍTICO: Beneficios no configurables**
- ❌ **CTA link no configurable**

**Otros:**
- ❌ **No usa block.json** (recomendado para bloques nativos)
- ⚠️ No usa constantes para magic strings (`'deal'`, `'active'`, etc.)

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque está en uso en producción. NO cambiar block name ni clases CSS.**

### Prioridad Alta

**1. Hacer Email y CTA Configurables**
- **Acción:** Agregar attributes al bloque:
  ```php
  'attributes' => [
      'contactEmail' => ['type' => 'string', 'default' => 'info@travel.com'],
      'ctaUrl' => ['type' => 'string', 'default' => '#packages'],
      'ctaText' => ['type' => 'string', 'default' => 'View Packages'],
  ]
  ```
- **Razón:** Email y CTA hardcodeados no son reutilizables
- **Riesgo:** BAJO - Son nuevos campos
- **Precauciones:** Mantener defaults actuales
- **Esfuerzo:** 2h (incluye UI en editor)

**2. Hacer Beneficios Configurables**
- **Acción:** Agregar attribute `benefits` como array:
  ```php
  'benefits' => [
      'type' => 'array',
      'default' => [
          'Best Price Guarantee',
          'Free Cancellation',
          '24/7 Customer Support',
          'Secure Payment',
          'Instant Confirmation'
      ]
  ]
  ```
- **Razón:** Lista hardcodeada no se adapta a diferentes deals
- **Riesgo:** BAJO - Son nuevos campos
- **Precauciones:** Mantener defaults actuales
- **Esfuerzo:** 2h (incluye UI en editor)

**3. Eliminar extract() en get_template()**
- **Acción:** Pasar array `$data` al template y acceder explícitamente:
  ```php
  // En get_template()
  include $template_path;

  // En template
  <?php echo esc_html($data['discount_percentage']); ?>
  ```
- **Razón:** `extract()` es inseguro y dificulta depuración
- **Riesgo:** MEDIO - Requiere cambiar template
- **Precauciones:**
  - Actualizar template para usar `$data` array
  - Verificar todas las variables usadas
  - Testing exhaustivo
- **Esfuerzo:** 1h

### Prioridad Media

**4. Crear block.json**
- **Acción:** Crear `block.json` con toda la configuración del bloque
- **Razón:** WordPress recomienda block.json para bloques nativos
- **Riesgo:** MEDIO - Requiere ajustar registro
- **Precauciones:**
  - Mantener registro funcionando
  - Migrar configuración completa
- **Esfuerzo:** 1.5h

**5. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\Deal` a `Travel\Blocks\Deal`
- **Razón:** No sigue PSR-4, tiene `\Blocks\Blocks\`
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:**
  - Actualizar composer.json si es necesario
  - Ejecutar `composer dump-autoload`
  - Verificar que bloque sigue registrándose
- **Esfuerzo:** 30 min

**6. Validar y Sanitizar Fechas**
- **Acción:** Validar fechas antes de `strtotime()`:
  ```php
  if ($start_date && preg_match('/^\d{4}-\d{2}-\d{2}/', $start_date)) {
      $start_timestamp = strtotime($start_date);
  }
  ```
- **Razón:** Prevenir errores con fechas inválidas
- **Riesgo:** BAJO - Mejora defensiva
- **Precauciones:** Mantener lógica actual
- **Esfuerzo:** 30 min

### Prioridad Baja

**7. Extraer Constantes para Magic Strings**
- **Acción:** Crear constantes de clase:
  ```php
  private const POST_TYPE = 'deal';
  private const STATUS_ACTIVE = 'active';
  private const STATUS_SCHEDULED = 'scheduled';
  private const STATUS_EXPIRED = 'expired';
  ```
- **Razón:** Mejor mantenibilidad
- **Riesgo:** BAJO
- **Precauciones:** Reemplazar todos los strings
- **Esfuerzo:** 30 min

**8. Agregar Filtro para Datos del Deal**
- **Acción:** Agregar hook para modificar datos:
  ```php
  return apply_filters('travel_blocks_deal_info_card_data', $data, $post_id);
  ```
- **Razón:** Permitir extensibilidad
- **Riesgo:** BAJO
- **Precauciones:** Documentar filtro
- **Esfuerzo:** 15 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. Eliminar extract() en get_template() (seguridad)
2. Validar y sanitizar fechas (seguridad)
3. Hacer email y CTA configurables (funcionalidad)
4. Hacer beneficios configurables (funcionalidad)
5. Crear block.json (arquitectura)
6. Corregir namespace (arquitectura)
7. Extraer constantes para magic strings (código limpio)
8. Agregar filtro para datos del deal (extensibilidad)

**Precauciones Generales:**
- ⛔ NO cambiar block name `deal-info-card`
- ⛔ NO cambiar clases CSS en template
- ⛔ NO cambiar meta keys existentes
- ✅ Testing: Insertar bloque, configurar, verificar frontend
- ✅ Testing: Verificar en deal activo, scheduled, expired
- ✅ Testing: Verificar sticky behavior en desktop/mobile

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo (categoría "travel")
- [ ] Se puede insertar correctamente
- [ ] Preview funciona en editor con datos ficticios
- [ ] Frontend funciona en post tipo `deal`
- [ ] No renderiza en otros post types (o muestra preview)
- [ ] Descuento se muestra correctamente
- [ ] Fechas se muestran formateadas
- [ ] Estado active/scheduled/expired funciona
- [ ] CTA solo aparece en deals activos
- [ ] Email configurable (si se implementó)
- [ ] CTA configurable (si se implementó)
- [ ] Beneficios configurables (si se implementó)

### Arquitectura
- [ ] Namespace correcto (si se cambió)
- [ ] block.json existe (si se creó)
- [ ] No usa extract() (si se refactorizó)
- [ ] Fechas validadas (si se implementó)
- [ ] Constantes definidas (si se implementó)

### Seguridad
- [ ] Escapado en template (ya OK)
- [ ] Sanitización de meta fields (mejorado)
- [ ] Validación de fechas (mejorado)

### CSS
- [ ] Position sticky funciona en desktop
- [ ] Position static en mobile/tablet
- [ ] Variables CSS funcionan correctamente
- [ ] Responsive funciona en todos los breakpoints

### Clean Code
- [ ] Código claro y legible
- [ ] Métodos pequeños y enfocados
- [ ] Sin duplicación

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funciona correctamente
- ✅ Código limpio y organizado
- ✅ Separación MVC correcta
- ✅ CSS bien estructurado con variables
- ❌ Email y beneficios hardcodeados (no configurables)
- ❌ Uso inseguro de extract()
- ⚠️ No usa block.json (debería para bloques nativos)
- ⚠️ Namespace incorrecto

### Puntuación: 7/10

**Fortalezas:**
- Código limpio y legible
- Métodos pequeños y enfocados (máx 36 líneas)
- Template bien escapado
- Buena lógica de estados (active/scheduled/expired)
- CSS responsive y con variables CSS
- Sticky behavior bien implementado

**Debilidades:**
- Email hardcodeado en template (no configurable)
- Beneficios hardcodeados (no configurables)
- CTA link hardcodeado
- Uso inseguro de extract()
- No usa block.json
- Namespace incorrecto

**Métricas:**
- **LOC Totales:** 154 líneas (PHP) + 103 líneas (template) + 199 líneas (CSS) = **456 líneas**
- **Método más largo:** `get_deal_data()` - 36 líneas
- **Métodos públicos:** 3
- **Métodos privados:** 3

**Recomendación:** Hacer configurables el email, CTA y beneficios. Eliminar extract() por seguridad. Crear block.json para seguir estándares WordPress.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Pendiente
