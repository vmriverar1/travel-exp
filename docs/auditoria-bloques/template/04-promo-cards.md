# Auditoría: Promo Cards (Template)

**Fecha:** 2025-11-09
**Bloque:** 4/X Template
**Tiempo:** 45 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `promo-cards`
- **Namespace:** `acf/promo-cards` (ACF block)
- **Clases CSS:** `.promo-cards`, `.promo-cards__container`, `.promo-card`, `.promo-card__image`
- **Campos ACF:** `card_1_image`, `card_1_height`, `card_2_image`, `card_2_height`, `card_2_link`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Funcionalidad PDF en card_1 - hardcoded para packages
- Template usa tag dinámico (`<a>` vs `<div>`)
- Heights editables (200-800px)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Template/PromoCards.php`
**Namespace:** `Travel\Blocks\Blocks\Template`
**Template:** `/wp-content/plugins/travel-blocks/templates/template/promo-cards.php` (52 líneas)
**Assets:**
- CSS: `/assets/blocks/template/promo-cards.css` (79 líneas)
- JS: No tiene

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

---

## 2. Propósito y Funcionalidad

**Descripción:** Dos tarjetas de imagen lado a lado con alturas editables. La primera tarjeta tiene funcionalidad PDF para packages, la segunda tiene link opcional.

**Inputs:**
- `card_1_image` (image) - Imagen para tarjeta izquierda
- `card_1_height` (number) - Altura en px (default: 400, min: 200, max: 800)
- `card_2_image` (image) - Imagen para tarjeta derecha
- `card_2_height` (number) - Altura en px (default: 400, min: 200, max: 800)
- `card_2_link` (url) - Link opcional para tarjeta derecha

**Outputs:**
- HTML con 2 tarjetas en grid
- Card 1: Puede activar modal PDF si está en package
- Card 2: Puede ser link clickable

**Contextos soportados:**
- Cualquier página (bloque ACF insertable)
- Funcionalidad PDF solo activa en single packages

**Comportamiento especial:**
- Card 1 detecta automáticamente si está en package y habilita PDF download
- Card 2 renderiza como `<a>` si tiene link, `<div>` si no
- Heights son editables individualmente
- Imágenes preview de Unsplash por defecto

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `BlockBase`
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```
Heredadas de BlockBase:
- name, title, description, category, icon, keywords, mode, supports
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor, configura propiedades del bloque
2. register(): void - Registra bloque y campos ACF
3. enqueue_assets(): void - Encola CSS del bloque
4. render(array $block, string $content, bool $is_preview, int $post_id): void - Renderiza bloque
```

**Métodos Privados:**
```
Ninguno
```

---

## 4. Registro del Bloque

**Método:** `register()` - Llama parent::register() + acf_add_local_field_group()

**Configuración:**
- name: `promo-cards`
- title: "Promo Cards"
- description: "Two image cards with editable heights"
- category: `template-blocks`
- icon: `slides`
- keywords: ['promo', 'cards', 'images']
- mode: `preview`

**ACF Group:** `group_promo_cards`

**Supports:**
- align: false
- mode: false
- multiple: true
- anchor: false

---

## 5. Campos ACF

**Definición:** `register()` (líneas 44-118)

**Grupo:** `group_promo_cards` - "Promo Cards Settings"

**Campos:**
1. `card_1_image` (image)
   - Label: "Card 1 - Image"
   - Return format: array
   - Preview size: medium
   - Wrapper width: 50%

2. `card_1_height` (number)
   - Label: "Card 1 - Height (px)"
   - Default: 400
   - Min: 200, Max: 800, Step: 10
   - Wrapper width: 50%

3. `card_2_image` (image)
   - Label: "Card 2 - Image"
   - Return format: array
   - Preview size: medium
   - Wrapper width: 50%

4. `card_2_height` (number)
   - Label: "Card 2 - Height (px)"
   - Default: 400
   - Min: 200, Max: 800, Step: 10
   - Wrapper width: 50%

5. `card_2_link` (url)
   - Label: "Card 2 - Link URL"
   - Instructions: "Optional: Add a link to redirect when clicking the second card"
   - Required: false
   - Placeholder: https://example.com

**Validaciones:** Solo ranges numéricos (200-800)

**Notas:**
- ⚠️ DUPLICACIÓN: card_1 y card_2 son 95% idénticos (solo diffieren en name/label)
- ⚠️ ASIMETRÍA: card_1 NO tiene link, card_2 NO tiene PDF - confuso
- ✅ Wrapper widths: 50% para layout side-by-side en ACF

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()` (líneas 142-189)

**Obtención de Datos:**
1. Obtiene campos ACF con `get_field()`
2. Aplica defaults con operador `?:` (400 para heights, '' para link)
3. Detecta post type actual con `get_post_type(get_the_ID())`
4. Habilita PDF solo si `$current_post_type === 'package'`

**Procesamiento:**
```php
// Card 1 - PDF enabled solo en packages
$enable_pdf = ($current_post_type === 'package');
$package_id = $enable_pdf ? $current_post_id : null;

// Debug log (PROBLEMA - ver sección 8)
error_log('PromoCards Debug: post_type=' . $current_post_type . ', enable_pdf=' . var_export($enable_pdf, true) . ', package_id=' . var_export($package_id, true));

// Default preview images
$default_img_1 = 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800&h=600&fit=crop';
$default_img_2 = 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&h=600&fit=crop';

// Array de cards
$cards = [
    // Card 1: PDF enabled si es package
    [
        'image' => is_array($card_1_image) ? $card_1_image['url'] : $default_img_1,
        'alt' => is_array($card_1_image) ? $card_1_image['alt'] : 'Promo Card 1',
        'height' => $card_1_height,
        'enable_pdf' => $enable_pdf,
        'package_id' => $package_id,
    ],
    // Card 2: Link opcional
    [
        'image' => is_array($card_2_image) ? $card_2_image['url'] : $default_img_2,
        'alt' => is_array($card_2_image) ? $card_2_image['alt'] : 'Promo Card 2',
        'height' => $card_2_height,
        'enable_pdf' => false,
        'package_id' => null,
        'link' => $card_2_link,
    ],
];
```

**Variables al Template:**
```php
- $cards: array - Array de 2 cards con image, alt, height, enable_pdf, package_id, link
```

**Lógica en Template:**
- Loop `foreach ($cards as $card)`
- Construye clases dinámicas:
  - `.promo-card--pdf-enabled` si tiene PDF
  - `.promo-card--clickable` si tiene link
- Tag dinámico: `<a>` si tiene link, `<div>` si no
- Atributo `data-package-id` si tiene PDF
- Atributo `href` si tiene link
- Inline style para height
- ✅ Todo escapado correctamente (esc_attr, esc_url)

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:** No usa (asume que existe modal PDF global)

**REST API:** No usa

**Hooks Propios:** No define

**Dependencias Externas:**
- `BlockBase` (core framework)
- ACF (acf_add_local_field_group, get_field)
- ⚠️ **Modal PDF global** (no está documentado en el código)
  - Asume que hay JS global que detecta `data-package-id`
  - CSS oculta `.promo-card--pdf-enabled::before` y `::after`

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ⚠️ **VIOLA PARCIALMENTE**
- Ubicación: `register()` (líneas 38-120)
- Problema: Método hace 2 cosas: registrar bloque + registrar campos ACF
- Impacto: MEDIO - Método muy largo (82 líneas)
- Recomendación: Extraer campos ACF a método `register_fields()`

**OCP:** ✅ Cumple - Puede extenderse sin modificar

**LSP:** ✅ Cumple - Respeta contrato de BlockBase

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA**
- Ubicación: render() - líneas 145-161
- Problema: Acoplamiento directo a funciones globales WP/ACF sin abstracción
- Impacto: MEDIO (dificulta testing)
- Funciones: get_field, get_the_ID, get_post_type, error_log

### 8.2 Problemas Clean Code

**Complejidad:**
- ⚠️ **register() DEMASIADO LARGO** (82 líneas, 44-120)
  - Problema: Registra bloque + define 5 campos ACF inline
  - Debería estar <50 líneas
  - Recomendación: Extraer a `get_acf_fields(): array`

- ⚠️ **render() con lógica de negocio** (48 líneas, 142-189)
  - Problema: Mezcla obtención de datos + lógica de PDF + construcción de array
  - Recomendación: Extraer a método `prepare_cards_data()`

**Anidación:**
- ✅ Máximo 2 niveles - Aceptable

**Duplicación:**
- ❌ **DUPLICACIÓN ALTA en campos ACF**
  - Ubicación: Líneas 48-72 (card_1) vs 74-98 (card_2)
  - Problema: 95% del código es idéntico, solo cambia name/label
  - Impacto: ALTO - 50 líneas duplicadas
  - Recomendación: Crear método `get_card_field_group(int $card_number): array`

- ⚠️ **Lógica de image fallback duplicada**
  - Ubicación: Líneas 169-170 vs 176-177
  - Patrón: `is_array($card_X_image) ? $card_X_image['url'] : $default_img_X`
  - Recomendación: Método helper `get_image_url($field_value, $default)`

**Nombres:**
- ✅ Nombres descriptivos y claros
- ✅ Variables bien nombradas ($enable_pdf, $card_1_image)

**Código Sin Uso:**
- ✅ No se detectó código muerto

**Otros Problemas:**
- ❌ **CRÍTICO: error_log() en producción**
  - Ubicación: Línea 161
  - Problema: Debug log activo en código de producción
  - Impacto: ALTO - Contamina logs, puede revelar información
  - Solución: Eliminar o usar sistema de logging con niveles

- ⚠️ **Números mágicos**
  - Ubicación: Líneas 67-69, 93-95 (400, 200, 800)
  - Recomendación: Constantes de clase
  ```php
  private const DEFAULT_HEIGHT = 400;
  private const MIN_HEIGHT = 200;
  private const MAX_HEIGHT = 800;
  ```

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF sanitiza automáticamente (image, number, url)
- ✅ Heights tienen validación de range (200-800)
- ✅ URL field valida formato
- ⚠️ error_log() expone datos en logs (post_type, IDs)

**Escapado:**
- ✅ Template escapa todo correctamente:
  - esc_attr() para clases, heights, IDs
  - esc_url() para images y links
- ✅ Data pasada al template es limpia (strings ACF)

**Nonces:**
- ✅ N/A - ACF maneja nonces automáticamente

**Capabilities:**
- ✅ N/A - Es bloque de contenido

**SQL:**
- ✅ No usa queries directas, solo funciones WP

**Otros:**
- ⚠️ **Imágenes default de Unsplash**
  - Ubicación: Líneas 164-165
  - Problema: URLs externas hardcoded
  - Impacto: BAJO - Si Unsplash cae, preview se rompe
  - Recomendación: Placeholder.com o imagen local

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\Template`
  - Esperado: `Travel\Blocks\Template`
  - Ubicación: Línea 11
  - Impacto: Bajo (funciona pero no sigue PSR-4)
  - **NOTA:** Mismo problema que otros bloques Template

**Separación MVC:**
- ✅ Bien separado - Controller (clase) / View (template)
- ⚠️ Lógica de negocio en render() - debería estar en método separado

**Acoplamiento:**
- ⚠️ **Acoplamiento fuerte con funcionalidad PDF no documentada**
  - Ubicación: Líneas 156-158, 172-173
  - Problema: Asume que existe modal PDF global pero no está documentado
  - CSS oculta `::before` y `::after` (líneas 35-42 del CSS)
  - Impacto: MEDIO - Funcionalidad no autocontenida
  - Recomendación: Documentar dependencia o hacer auto-suficiente

**CSS - Uso de !important:**
- ⚠️ **4 usos de !important**
  - Ubicación: Líneas 22, 31, 65, 66
  - Problema: Indica problemas de especificidad
  ```css
  border-radius: 24px !important; /* L22 */
  cursor: pointer !important; /* L31 */
  min-height: 250px !important; /* L65 */
  max-height: 400px !important; /* L66 */
  ```
  - Impacto: MEDIO - Dificulta override, indica CSS pollution
  - Recomendación: Aumentar especificidad sin !important

**Otros:**
- ⚠️ **Asimetría funcional entre cards**
  - Card 1: Tiene PDF, NO tiene link
  - Card 2: Tiene link, NO tiene PDF
  - Problema: Inconsistente, confuso para usuarios
  - Recomendación: Ambas cards deberían tener opción de link + PDF configurable

- ⚠️ **Lógica de negocio hardcoded**
  - Ubicación: Línea 157 - `($current_post_type === 'package')`
  - Problema: PDF solo funciona en packages, no es configurable
  - Recomendación: Campo ACF "Enable PDF Download" en card_1

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque está en uso en producción. NO cambiar block name, campos ACF ni clases CSS públicas.**

### Prioridad CRÍTICA

**1. ❌ ELIMINAR error_log() de producción**
- **Acción:** Eliminar línea 161 o usar sistema de logging condicional
- **Razón:** Contamina logs de producción con debug info
- **Riesgo:** BAJO - Es solo logging
- **Precauciones:** Si se necesita debug, usar WP_DEBUG condicional
- **Esfuerzo:** 5 min
```php
// EN VEZ DE:
error_log('PromoCards Debug: post_type=' . $current_post_type . ', enable_pdf=' . var_export($enable_pdf, true) . ', package_id=' . var_export($package_id, true));

// USAR:
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log(sprintf('PromoCards: post_type=%s, enable_pdf=%s, package_id=%s',
        $current_post_type,
        var_export($enable_pdf, true),
        var_export($package_id, true)
    ));
}
```

### Prioridad Alta

**2. Extraer campos ACF duplicados a método helper**
- **Acción:** Crear método `get_card_field_group(int $card_number, array $config): array`
- **Razón:** 95% del código de campos ACF está duplicado (50 líneas)
- **Riesgo:** BAJO - Solo refactor interno
- **Precauciones:** Mantener keys exactas
- **Esfuerzo:** 1h
```php
private function get_card_field_group(int $card_number, array $config = []): array
{
    $defaults = [
        'has_link' => false,
    ];
    $config = array_merge($defaults, $config);

    $fields = [
        [
            'key' => "field_promo_card_{$card_number}_image",
            'label' => sprintf(__('Card %d - Image', 'travel-blocks'), $card_number),
            'name' => "card_{$card_number}_image",
            'type' => 'image',
            // ... resto de config
        ],
        [
            'key' => "field_promo_card_{$card_number}_height",
            // ...
        ],
    ];

    if ($config['has_link']) {
        $fields[] = [
            'key' => "field_promo_card_{$card_number}_link",
            // ...
        ];
    }

    return $fields;
}

// Uso:
'fields' => array_merge(
    $this->get_card_field_group(1),
    $this->get_card_field_group(2, ['has_link' => true])
),
```

**3. Extraer lógica de preparación de datos**
- **Acción:** Crear método `prepare_cards_data(): array`
- **Razón:** render() mezcla obtención de datos + lógica de negocio
- **Riesgo:** BAJO - Solo refactor interno
- **Precauciones:** Mantener output exacto
- **Esfuerzo:** 1h
```php
private function prepare_cards_data(): array
{
    $card_1_image = get_field('card_1_image');
    $card_1_height = get_field('card_1_height') ?: self::DEFAULT_HEIGHT;

    $card_2_image = get_field('card_2_image');
    $card_2_height = get_field('card_2_height') ?: self::DEFAULT_HEIGHT;
    $card_2_link = get_field('card_2_link') ?: '';

    $current_post_id = get_the_ID();
    $current_post_type = get_post_type($current_post_id);

    $enable_pdf = ($current_post_type === 'package');
    $package_id = $enable_pdf ? $current_post_id : null;

    return [
        [
            'image' => $this->get_image_url($card_1_image, self::DEFAULT_IMAGE_1),
            'alt' => $this->get_image_alt($card_1_image, 'Promo Card 1'),
            'height' => $card_1_height,
            'enable_pdf' => $enable_pdf,
            'package_id' => $package_id,
        ],
        [
            'image' => $this->get_image_url($card_2_image, self::DEFAULT_IMAGE_2),
            'alt' => $this->get_image_alt($card_2_image, 'Promo Card 2'),
            'height' => $card_2_height,
            'enable_pdf' => false,
            'package_id' => null,
            'link' => $card_2_link,
        ],
    ];
}
```

**4. Eliminar !important del CSS**
- **Acción:** Aumentar especificidad sin !important
- **Razón:** !important indica problemas de especificidad
- **Riesgo:** MEDIO - Puede afectar estilos visuales
- **Precauciones:** Testing visual exhaustivo
- **Esfuerzo:** 30 min - 1h
```css
/* EN VEZ DE: */
.promo-card {
    border-radius: 24px !important;
}

/* USAR: */
.promo-cards .promo-card {
    border-radius: 24px;
}
```

### Prioridad Media

**5. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\Template` a `Travel\Blocks\Template`
- **Razón:** No sigue PSR-4
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:** Mismo que otros bloques Template
- **Esfuerzo:** 30 min (coordinado con otros bloques)

**6. Mover números mágicos a constantes**
- **Acción:** Crear constantes de clase
- **Razón:** Heights hardcoded (400, 200, 800)
- **Riesgo:** BAJO
- **Precauciones:** Mantener valores exactos
- **Esfuerzo:** 15 min
```php
private const DEFAULT_HEIGHT = 400;
private const MIN_HEIGHT = 200;
private const MAX_HEIGHT = 800;
private const DEFAULT_IMAGE_1 = 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800&h=600&fit=crop';
private const DEFAULT_IMAGE_2 = 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&h=600&fit=crop';
```

**7. Documentar dependencia de modal PDF**
- **Acción:** Agregar PHPDoc y comentario sobre modal PDF global
- **Razón:** Funcionalidad no autocontenida ni documentada
- **Riesgo:** BAJO - Solo documentación
- **Precauciones:** Ninguna
- **Esfuerzo:** 15 min

### Prioridad Baja

**8. Hacer ambas cards simétricas**
- **Acción:** Agregar opción de link a card_1 y PDF a card_2
- **Razón:** Asimetría confusa
- **Riesgo:** ALTO - Cambia comportamiento
- **Precauciones:** Mantener retrocompatibilidad
- **Esfuerzo:** 2-3h

**9. Hacer PDF configurable (no solo packages)**
- **Acción:** Campo ACF "Enable PDF Download" en lugar de auto-detectar
- **Razón:** Lógica hardcoded limita uso
- **Riesgo:** ALTO - Cambia comportamiento
- **Precauciones:** Backward compatibility
- **Esfuerzo:** 2h

**10. Usar placeholder local en vez de Unsplash**
- **Acción:** Imagen local o placeholder.com
- **Razón:** URLs externas pueden fallar
- **Riesgo:** BAJO
- **Precauciones:** Ninguna
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **CRÍTICO:** Eliminar error_log() de producción
2. **ALTO:** Extraer campos ACF duplicados a método helper
3. **ALTO:** Extraer lógica de preparación de datos
4. **ALTO:** Eliminar !important del CSS
5. Corregir namespace (coordinado con otros bloques)
6. Mover números mágicos a constantes
7. Documentar dependencia de modal PDF
8. (Opcional) Hacer cards simétricas
9. (Opcional) Hacer PDF configurable

**Precauciones Generales:**
- ⛔ NO cambiar block name `promo-cards`
- ⛔ NO cambiar nombres de campos ACF (card_1_image, etc.)
- ⛔ NO cambiar clases CSS públicas
- ✅ Testing: Verificar preview en editor
- ✅ Testing: Verificar en package pages (funcionalidad PDF)
- ✅ Testing: Verificar en otras páginas (sin PDF)
- ✅ Testing: Verificar card 2 con y sin link
- ✅ Testing: Verificar responsive (mobile)

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en inserter
- [ ] Se puede insertar en cualquier página
- [ ] Preview funciona con imágenes default
- [ ] Card 1 muestra PDF en package pages
- [ ] Card 1 NO muestra PDF en otras páginas
- [ ] Card 2 funciona como link cuando tiene URL
- [ ] Card 2 funciona como div cuando NO tiene URL
- [ ] Heights son editables (200-800px)
- [ ] Imágenes se muestran correctamente
- [ ] Responsive funciona (1 columna en mobile)

### Arquitectura
- [ ] error_log() eliminado o condicional
- [ ] Campos ACF no duplicados (si se refactorizó)
- [ ] Lógica de datos separada de render() (si se refactorizó)
- [ ] !important eliminado del CSS (si se refactorizó)
- [ ] Namespace correcto (si se cambió)
- [ ] Constantes para números mágicos (si se implementó)

### Seguridad
- [ ] Escapado en template (ya OK)
- [ ] ACF sanitiza inputs (ya OK)
- [ ] No hay debug logs en producción

### Clean Code
- [ ] Métodos <60 líneas (después de extracciones)
- [ ] No hay duplicación innecesaria
- [ ] Código claro y legible

---

## 12. Métricas de Código

**Total líneas:** 191 (PromoCards.php) + 52 (template) + 79 (CSS) = **322 líneas**

**Métodos y tamaño:**
```
1. __construct()       : 15 líneas  (18-33)   ✅ OK
2. register()          : 82 líneas  (38-120)  ❌ DEMASIADO LARGO
3. enqueue_assets()    : 13 líneas  (125-137) ✅ OK
4. render()            : 48 líneas  (142-189) ⚠️ MEJORABLE
```

**Método más largo:** `register()` con 82 líneas

**Complejidad ciclomática:**
- __construct(): 1 (simple)
- register(): 2 (if function_exists)
- enqueue_assets(): 2 (if file_exists)
- render(): 5 (múltiples ternarios + is_array checks)

**Duplicación:** ~50 líneas de campos ACF duplicadas

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ❌ **error_log() en producción** (CRÍTICO)
- ⚠️ **Duplicación alta** en campos ACF (50 líneas)
- ⚠️ **Método register() muy largo** (82 líneas)
- ⚠️ **4 usos de !important** en CSS
- ⚠️ Namespace incorrecto (PSR-4)
- ⚠️ Asimetría funcional entre cards
- ✅ Seguridad OK (escapado completo)
- ✅ Template limpio
- ✅ Separación MVC correcta

### Puntuación: 6.5/10

**Fortalezas:**
- Template bien estructurado y escapado
- Tag dinámico (`<a>` vs `<div>`) elegante
- Funcionalidad útil (heights editables, PDF, links)
- CSS responsive bien implementado
- Clases BEM correctas

**Debilidades:**
- **error_log() activo en producción** (CRÍTICO)
- Método register() demasiado largo (82 líneas)
- 95% de campos ACF duplicados
- 4 usos de !important en CSS
- Namespace incorrecto
- Lógica de negocio en render()
- Acoplamiento no documentado con modal PDF
- Asimetría confusa (card 1 = PDF, card 2 = link)

**Líneas totales:** 322 (191 PHP + 52 template + 79 CSS)

**Métodos más largos:**
1. register(): 82 líneas (38-120)
2. render(): 48 líneas (142-189)

**Recomendación:** Eliminar error_log() URGENTE, extraer duplicación ACF, limpiar CSS de !important.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** URGENTE (error_log) + Recomendada (duplicación ACF + !important)
