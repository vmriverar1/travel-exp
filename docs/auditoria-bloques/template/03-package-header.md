# Auditoría: PackageHeader (Template)

**Fecha:** 2025-11-09
**Bloque:** 3/? Template
**Tiempo:** 50 minutos

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `package-header`
- **Namespace:** `travel-blocks/package-header`
- **ACF Field Keys:**
  - `subtitle`
  - `description` (overview)
  - `duration`
  - `departure`
  - `physical_difficulty`
  - `service_type`
- **Clases CSS críticas:**
  - `package-header`
  - `package-header__container`
  - `package-header__subtitle`
  - `package-header__overview`
  - `package-header__metadata`
  - `package-header__metadata-list`
  - `package-header__metadata-item`
  - `metadata-icon`
  - `metadata-label`
  - `metadata-value`
- **Trait dependency:** `PreviewDataTrait`
- **Base class:** `TemplateBlockBase`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- El bloque extiende `TemplateBlockBase` - cambios en base afectan a todos los bloques Template
- `PreviewDataTrait::get_preview_package_data()` debe existir y retornar array con keys específicas
- Template usa SVG icons inline - no depende de IconHelper
- Los field keys de ACF (`subtitle`, `description`, etc.) están en uso en contenido existente
- Template usa `wpautop()` para overview - mantener formato de párrafos

---

## 1. Información General

**Ubicación:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/src/Blocks/Template/PackageHeader.php`
**Namespace:** `Travel\Blocks\Blocks\Template`
**Template:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/templates/template/package-header.php`
**Assets:**
- CSS: `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/template/package-header.css` (263 líneas)
- JS: N/A (no usa JavaScript)

**Tipo:** [X] Template Block (extiende TemplateBlockBase)

**Líneas de código:**
- Clase PHP: 93 líneas
- Template PHP: 82 líneas
- CSS: 263 líneas
- JavaScript: 0 líneas
- **TOTAL: 438 líneas**

---

## 2. Propósito y Funcionalidad

**Descripción:**
Bloque de cabecera para páginas de paquetes turísticos que muestra el subtítulo del paquete, una sección de overview/descripción y metadatos clave (duración, punto de partida, dificultad física, tipo de servicio). Es un bloque puramente presentacional sin interactividad.

**Inputs (ACF Fields):**
- `subtitle` - Subtítulo del paquete (opcional)
- `description` - Descripción/overview del paquete (texto largo)
- `duration` - Duración del paquete (ej: "5 días")
- `departure` - Punto de partida (ej: "Quito")
- `physical_difficulty` - Nivel de dificultad física (ej: "Moderado")
- `service_type` - Tipo de servicio (ej: "Privado", "Grupo pequeño")

**Preview Data (PreviewDataTrait):**
- Usa `get_preview_package_data()` que retorna datos de ejemplo
- Mapea fields a keys específicas (ej: `physical_difficulty` → `difficulty`)

**Outputs:**
- HTML renderizado con estructura semántica
- Sección de subtítulo (si existe)
- Sección de overview con h2 + párrafos formateados
- Lista de metadatos con iconos SVG inline

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `TemplateBlockBase`
- Implementa: Ninguna
- Traits: `PreviewDataTrait`

**Propiedades:**
```php
protected string $name = 'package-header';                    (línea 22)
protected string $title = 'Package Header';                   (línea 23)
protected string $description = '...';                         (línea 24)
protected string $icon = 'heading';                            (línea 25)
protected array $keywords = [...];                             (línea 26)
```

**Métodos Públicos:**
```
__construct(): void                                 (líneas 20-27) - 8 líneas
enqueue_assets(): void                              (líneas 79-91) - 13 líneas
```

**Métodos Protected:**
```
render_preview(array $attributes): string           (líneas 29-46) - 18 líneas
render_live(int $post_id, array $attributes): string (líneas 48-58) - 11 líneas
```

**Métodos Private:**
```
get_package_metadata(int $post_id): array           (líneas 66-74) - 9 líneas
```

**Método más largo:** `render_preview()` con 18 líneas (muy manejable)

---

## 4. Registro del Bloque

**Método:** Heredado de `TemplateBlockBase`

**Configuración (vía constructor):**
- name: `package-header`
- title: "Package Header"
- description: "Package title, overview, and metadata"
- icon: `heading`
- keywords: `['header', 'title', 'overview', 'metadata', 'package']`

**Propiedades heredadas:**
- La base class maneja el registro completo
- `render_preview()` y `render_live()` son métodos abstractos implementados aquí

---

## 5. Campos ACF (si aplica)

**Definición:** N/A - No define campos ACF propios

**Campos utilizados:**
Este bloque NO define campos ACF. Lee campos existentes del post type Package:
- `subtitle` - Campo de texto
- `description` - Campo de área de texto (overview)
- `duration` - Campo de texto
- `departure` - Campo de texto
- `physical_difficulty` - Campo de texto/select
- `service_type` - Campo de texto/select

**Trait usado:**
- `PreviewDataTrait` proporciona `get_preview_package_data()` con datos de ejemplo

---

## 6. Flujo de Renderizado

**Preparación:**
1. `TemplateBlockBase` llama a `render_preview()` o `render_live()` según contexto
2. **Preview mode:**
   - Llama `get_preview_package_data()` del trait
   - Mapea datos a estructura esperada por template
   - Construye array `$data` con `subtitle`, `overview`, `metadata`, `is_preview`
3. **Live mode:**
   - Recibe `$post_id` como parámetro
   - Lee campos ACF con `get_field()`
   - Llama `get_package_metadata($post_id)` para obtener metadatos
   - Construye array `$data` similar al preview
4. Llama `load_template('package-header', $data)` (heredado de base class)
5. Template usa las variables directamente (NO usa extract)
6. Retorna HTML renderizado

**Variables al Template:**
```php
$subtitle         // string: Subtítulo del paquete
$overview         // string: Descripción/overview
$metadata         // array: ['duration', 'departure', 'difficulty', 'service_type']
$is_preview       // bool: Si está en modo preview
```

**Template processing:**
- NO usa `extract()` ✅
- Accede a variables directamente desde scope
- Usa `esc_html()` para texto simple (líneas 18, 40, 51, 61, 74)
- Usa `wp_kses_post()` + `wpautop()` para overview (línea 25)
- SVG icons inline (no depende de helpers externos)
- Filtra metadata con `array_filter()` antes de renderizar (línea 30)
- Condicionales para mostrar solo campos con valores

---

## 7. Funcionalidades Adicionales

**AJAX:** ❌ No

**JavaScript:** ❌ No
- Bloque completamente estático
- No requiere interactividad client-side

**REST API:** ❌ No

**Hooks Propios:**
- Ninguno (usa hook de enqueue heredado)

**Dependencias externas:**
- `TemplateBlockBase` - Clase base abstracta
- `PreviewDataTrait` - Trait para datos de preview
- `get_field()` - ACF function
- Constants: `TRAVEL_BLOCKS_PATH`, `TRAVEL_BLOCKS_URL`, `TRAVEL_BLOCKS_VERSION`

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP (Single Responsibility Principle):** ⚠️ **VIOLACIÓN LEVE-MEDIA**
- La clase hace:
  - Configuración del bloque (constructor) ✓
  - Rendering de preview ✓
  - Rendering de live ✓
  - Obtención de datos (get_package_metadata) ✓
  - Enqueue de assets ✓
- **Comparado con bloques sin base class:** Mejor (lógica de registro delegada)
- **Debería separarse:** DataProvider podría ser independiente
- **Mitigación:** La base class reduce responsabilidades vs bloques standalone

**OCP (Open/Closed Principle):** ✅ **BUENO**
- Extiende `TemplateBlockBase` - arquitectura extensible
- Usa trait para preview data - reutilizable
- `get_package_metadata()` podría sobrescribirse si se hereda
- Mejor que bloques monolíticos

**LSP (Liskov Substitution Principle):** ✅ **CUMPLE**
- Implementa correctamente métodos abstractos de `TemplateBlockBase`
- Cumple contrato esperado por la base class
- Puede sustituirse por otros bloques Template

**ISP (Interface Segregation Principle):** ✅ **N/A**
- No implementa interfaces explícitas
- Base class podría definir interfaz pero no es crítico

**DIP (Dependency Inversion Principle):** ❌ **VIOLACIÓN MEDIA**
- Depende directamente de:
  - `get_field()` - ACF function (static/global)
  - `load_template()` - método de base class (OK)
  - `get_preview_package_data()` - trait (OK)
- **NO usa inyección de dependencias para ACF**
- Mejor que bloques sin base class pero aún acoplado a ACF

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ Métodos muy cortos (<20 líneas)
- ✅ Método más largo: 18 líneas (render_preview)
- ✅ Lógica simple y directa
- ✅ No hay complejidad ciclomática alta

**Anidación:**
- ✅ Máximo 2 niveles de anidación
- ✅ Muy fácil de leer

**Duplicación:**
- ✅ Casi nula duplicación
- ✅ Patrón de `get_field()` se repite 6 veces pero es inevitable
- ✅ Bien encapsulado en `get_package_metadata()`

**Nombres:**
- ✅ Nombres muy descriptivos y claros
- ✅ `$metadata` es apropiado en este contexto
- ✅ `get_package_metadata()` es explícito
- ✅ Variables de template bien nombradas

**Código Sin Uso:**
- ✅ No hay código muerto
- ✅ Todos los métodos se utilizan
- ⚠️ CSS tiene estilos para `.package-header__title` y `.package-header__rating` que NO están en el template (líneas 26-108 CSS)

**Otros problemas:**
- ✅ NO usa `extract()` - **EXCELENTE**
- ✅ NO usa `uniqid()` - no necesita IDs únicos
- ✅ Código muy limpio y profesional

### 8.3 Problemas de Seguridad

**Sanitización:** ❌ **CRÍTICO**
- `render_live()` NO sanitiza valores de `get_field()`
- `get_package_metadata()` NO sanitiza (líneas 69-72)
- Datos van directamente al template sin sanitización previa
- Misma vulnerabilidad que otros bloques ACF

**Escapado:** ✅ **EXCELENTE**
- Template usa correctamente:
  - `esc_html()` para todos los campos de texto (líneas 18, 40, 51, 61, 74)
  - `wp_kses_post()` para overview que puede tener HTML (línea 25)
  - `wpautop()` para formatear párrafos
  - SVG inline con contenido estático (seguro)

**Nonces:** ✅ **N/A**
- No hay formularios ni AJAX
- No aplica verificación de nonce

**Capabilities:** ✅ **N/A**
- Bloque de solo lectura
- No modifica datos
- Hereda control de acceso de TemplateBlockBase

**SQL:** ✅ **N/A**
- No hay queries SQL directas
- Usa `get_field()` que está protegido por ACF

**Validación de Input:**
- ❌ NO valida `$post_id` antes de usarlo (líneas 51-53)
- ❌ NO valida que `$post_id` sea válido antes de `get_field()`
- ✅ Usa `??` operator para fallbacks (líneas 51-52)
- ✅ Usa `array_filter()` para limpiar metadata vacíos (template línea 30)

**XSS Potencial:**
- ✅ **BAJO RIESGO** - Todo escapado correctamente
- ✅ SVG inline es estático (no acepta input del usuario)
- ✅ `wp_kses_post()` filtra HTML peligroso en overview

### 8.4 Problemas de Arquitectura

**Namespace:** ✅ **CORRECTO**
- `Travel\Blocks\Blocks\Template` - apropiado y consistente

**Separación MVC:** ✅ **BUENO**
- **Model:** ⚠️ `get_package_metadata()` actúa como mini-model
- **View:** ✅ Template completamente separado
- **Controller:** ✅ Clase coordina entre model y view
- Mejor separación que bloques monolíticos

**Acoplamiento:** **MEDIO**
- Acoplado a `TemplateBlockBase` (inheritance - aceptable)
- Acoplado a `PreviewDataTrait` (composition - bueno)
- Acoplado a `get_field()` (ACF dependency - medio)
- Acoplado a estructura de fields ACF específica (alto)
- **Mejor que bloques standalone** - base class reduce acoplamiento global

**Cohesión:** ✅ **MUY ALTA**
- Todos los métodos relacionados directamente
- Funcionalidad única y bien definida
- Excelente cohesión

**Otros problemas:**
- ⚠️ Assets se cargan globalmente si el archivo existe (línea 83-89)
- ⚠️ NO usa `has_block()` para cargar condicionalmente
- ✅ Usa herencia apropiadamente (no viola LSP)
- ✅ Template es reutilizable y testeable
- ⚠️ CSS tiene estilos no usados (rating, title section)

**Problemas de Assets:**
- Assets se cargan en TODAS las páginas si el archivo existe
- No verifica si el bloque está presente en la página
- CSS: 263 líneas cargadas globalmente
- Aunque no tiene JS, el patrón es inconsistente con mejores prácticas

---

## 9. Recomendaciones de Refactorización

### Prioridad Alta

**1. Sanitizar datos en get_package_metadata() y render_live()**
- **Acción:** Agregar `sanitize_text_field()` a todos los `get_field()`
- **Razón:** Prevenir XSS y garantizar integridad de datos
- **Riesgo:** **ALTO** - Vulnerabilidad de seguridad
- **Precauciones:**
  - Usar `sanitize_text_field()` para textos cortos (duration, departure, etc.)
  - Usar `sanitize_textarea_field()` para overview antes de escapar
  - Mantener fallbacks con `??`
- **Esfuerzo:** 20 minutos
- **Código:**
```php
'subtitle' => sanitize_text_field(get_field('subtitle', $post_id) ?? ''),
'overview' => sanitize_textarea_field(get_field('description', $post_id) ?? ''),
'duration' => sanitize_text_field(get_field('duration', $post_id) ?? ''),
```

**2. Validar $post_id antes de usar**
- **Acción:** En `render_live()` y `get_package_metadata()` validar `$post_id`
- **Razón:** Prevenir errores con IDs inválidos o null
- **Riesgo:** **MEDIO** - Puede causar errores silenciosos
- **Precauciones:**
  - Verificar `!$post_id` o `!get_post($post_id)`
  - Retornar array vacío o valores por defecto
- **Esfuerzo:** 15 minutos
- **Código:**
```php
protected function render_live(int $post_id, array $attributes): string
{
    if (!$post_id || !get_post($post_id)) {
        return $this->render_preview($attributes);
    }
    // ... resto del código
}
```

**3. Cargar assets condicionalmente**
- **Acción:** Usar `has_block()` para cargar CSS solo cuando el bloque está presente
- **Razón:** Performance - no cargar 263 líneas de CSS innecesariamente
- **Riesgo:** **MEDIO** - Puede afectar carga en editors
- **Precauciones:**
  - Verificar que funcione en Gutenberg editor
  - Considerar bloques reutilizables
  - Cache busting apropiado
- **Esfuerzo:** 45 minutos
- **Código:**
```php
public function enqueue_assets(): void
{
    global $post;

    if (is_admin()) {
        // Cargar en editor
        $this->enqueue_css();
        return;
    }

    if (has_block('travel-blocks/package-header', $post)) {
        $this->enqueue_css();
    }
}

private function enqueue_css(): void
{
    $css_path = TRAVEL_BLOCKS_PATH . 'assets/blocks/template/package-header.css';

    if (file_exists($css_path)) {
        wp_enqueue_style(
            'travel-blocks-package-header',
            TRAVEL_BLOCKS_URL . 'assets/blocks/template/package-header.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );
    }
}
```

### Prioridad Media

**4. Limpiar CSS no usado**
- **Acción:** Eliminar estilos para `.package-header__title`, `.package-header__rating`, `.star` que NO están en el template
- **Razón:** Reducir tamaño de CSS, evitar confusión
- **Riesgo:** **BAJO-MEDIO** - Puede afectar otros templates si se reutilizan estilos
- **Precauciones:**
  - Verificar que no se usen en otros lugares
  - Buscar en toda la codebase antes de eliminar
  - Puede ser intencional para futuras features
- **Esfuerzo:** 30 minutos

**5. Mover get_package_metadata() a DataProvider dedicado**
- **Acción:** Crear `PackageDataProvider` class con método `get_metadata(int $post_id)`
- **Razón:** Mejor separación de responsabilidades, reutilizable en otros bloques
- **Riesgo:** **MEDIO** - Refactor que afecta arquitectura
- **Precauciones:**
  - Mantener backward compatibility
  - Inyectar via constructor
  - Usar en todos los bloques Template/Package
- **Esfuerzo:** 2-3 horas (para implementar sistema completo)

**6. Agregar type hints estrictos**
- **Acción:** Agregar `declare(strict_types=1);` al inicio del archivo
- **Razón:** Mejor type safety, prevenir bugs sutiles
- **Riesgo:** **BAJO** - Código ya usa type hints correctamente
- **Precauciones:** Testing exhaustivo
- **Esfuerzo:** 5 minutos

**7. Documentar campos ACF requeridos**
- **Acción:** Agregar DocBlock con lista de campos ACF que el bloque espera
- **Razón:** Claridad para desarrolladores, evitar errores de configuración
- **Riesgo:** **NINGUNO** - Solo documentación
- **Precauciones:** Mantener actualizado
- **Esfuerzo:** 15 minutos
- **Código:**
```php
/**
 * Package Header Template Block
 *
 * Displays package title, overview, and key metadata
 *
 * @package Travel\Blocks\Blocks\Template
 * @since 2.0.0
 *
 * ACF Fields Required:
 * - subtitle (text) - Optional package subtitle
 * - description (textarea) - Package overview/description
 * - duration (text) - Package duration (e.g., "5 días")
 * - departure (text) - Departure location (e.g., "Quito")
 * - physical_difficulty (text) - Difficulty level (e.g., "Moderado")
 * - service_type (text) - Service type (e.g., "Privado")
 */
```

### Prioridad Baja

**8. Crear constantes para field keys**
- **Acción:** Definir `private const FIELD_SUBTITLE = 'subtitle';` etc.
- **Razón:** Evitar typos, facilitar cambios futuros, autocomplete
- **Riesgo:** **BAJO** - Refactor cosmético
- **Precauciones:** Ninguna
- **Esfuerzo:** 20 minutos

**9. Extraer SVG icons a componentes reutilizables**
- **Acción:** Crear helper method o clase para SVG icons (clock, location, lightning, users)
- **Razón:** Reutilización en otros bloques, consistencia
- **Riesgo:** **BAJO** - Puede complicar template
- **Precauciones:** Mantener performance (no cargar helper innecesariamente)
- **Esfuerzo:** 1-2 horas

**10. Implementar schema.org markup**
- **Acción:** Agregar JSON-LD para Product/TouristTrip con metadatos
- **Razón:** Mejor SEO, rich snippets en Google
- **Riesgo:** **BAJO** - Mejora adicional
- **Precauciones:** Validar con Google Rich Results Test
- **Esfuerzo:** 1-2 horas

**11. Agregar Unit Tests**
- **Acción:** Crear tests para `get_package_metadata()`, `render_preview()`, `render_live()`
- **Razón:** Garantizar funcionalidad, prevenir regresiones
- **Riesgo:** **NINGUNO** - Solo testing
- **Precauciones:** Mock ACF functions y base class
- **Esfuerzo:** 3-4 horas

**12. Optimizar CSS con custom properties**
- **Acción:** Usar más variables CSS para colores, spacing, font-sizes
- **Razón:** Facilitar theming, consistencia
- **Riesgo:** **BAJO** - Puede afectar especificidad
- **Precauciones:** Testing visual exhaustivo
- **Esfuerzo:** 1 hora

---

## 10. Plan de Acción

**Fase 1: Seguridad Crítica** (Inmediato - 1 hora)
1. ✅ **Sanitizar get_field() calls** - Vulnerabilidad de seguridad
2. ✅ **Validar $post_id** - Prevenir errores
3. ✅ **Documentar campos ACF** - Claridad

**Fase 2: Performance** (Corto plazo - 1 día)
4. ✅ **Cargar assets condicionalmente** - Mejora performance
5. ✅ **Limpiar CSS no usado** - Reducir tamaño

**Fase 3: Arquitectura** (Mediano plazo - 1 semana)
6. ⚠️ **PackageDataProvider** - Separación de responsabilidades
7. ⚠️ **Type hints estrictos** - Type safety
8. ⚠️ **Constantes para fields** - Mejor práctica

**Fase 4: Calidad** (Largo plazo - 1 mes)
9. ⚠️ **SVG icons reutilizables** - DRY principle
10. ⚠️ **Schema.org markup** - SEO
11. ⚠️ **Unit Tests** - Testing
12. ⚠️ **Optimizar CSS** - Theming

**Precauciones Generales:**
- ⛔ **NO cambiar** field keys de ACF - rompe contenido existente
- ⛔ **NO cambiar** clases CSS críticas - rompe estilos
- ⛔ **NO cambiar** nombre del bloque - rompe contenido
- ⛔ **NO cambiar** firma de métodos abstractos - rompe herencia
- ✅ **Testing exhaustivo** en editor Y frontend
- ✅ **Verificar** con otros bloques Template que usan la misma base
- ✅ **Coordinar** cambios en TemplateBlockBase con todo el sistema

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] El bloque se renderiza correctamente en frontend
- [ ] Preview data aparece en editor Gutenberg
- [ ] Post data aparece correctamente con fields ACF
- [ ] Subtítulo se muestra (si existe)
- [ ] Overview se renderiza con párrafos formateados
- [ ] Metadata solo muestra items con valores
- [ ] SVG icons se renderizan correctamente
- [ ] Responsive funciona en móvil/tablet/desktop
- [ ] No hay warnings/notices en PHP error log
- [ ] No hay errores en browser console

### Arquitectura
- [ ] Assets se cargan solo cuando el bloque está presente
- [ ] Datos se sanitizan en render_live()
- [ ] $post_id se valida antes de usar
- [ ] No hay violación de LSP con TemplateBlockBase
- [ ] PreviewDataTrait funciona correctamente
- [ ] Template no usa extract() (ya cumplido ✅)
- [ ] Herencia es apropiada y no rompe otros bloques

### Seguridad
- [ ] Todos los get_field() sanitizados
- [ ] Todos los outputs escapados en template
- [ ] No hay SQL injection posible
- [ ] No hay XSS posible
- [ ] SVG inline no acepta input del usuario
- [ ] wpautop() no introduce vulnerabilidades

### Performance
- [ ] CSS no se carga en páginas sin el bloque
- [ ] No hay CSS no usado cargado
- [ ] SVG inline no afecta performance (son pequeños)
- [ ] No hay requests innecesarios

### Compatibilidad
- [ ] Funciona en Gutenberg editor
- [ ] Funciona en frontend
- [ ] Funciona en diferentes themes
- [ ] Responsive en móvil/tablet
- [ ] Funciona con bloques reutilizables
- [ ] Compatible con Full Site Editing
- [ ] Funciona sin ACF (fallback a preview?)

### Regresión
- [ ] Bloques existentes siguen funcionando
- [ ] ACF fields se leen correctamente
- [ ] No rompe otros bloques Template
- [ ] TemplateBlockBase sigue funcionando
- [ ] PreviewDataTrait no está roto
- [ ] CSS no afecta otros bloques

### Accesibilidad
- [ ] Estructura semántica correcta (header, h2, ul)
- [ ] SVG tienen roles/aria apropiados si necesario
- [ ] Contraste de colores es suficiente
- [ ] Funciona con screen readers
- [ ] Responsive no rompe usabilidad
- [ ] High contrast mode funciona (ya tiene CSS)

---

## 📊 Resumen Ejecutivo

### Estado Actual

**El bloque PackageHeader es un bloque bien diseñado arquitecturalmente que aprovecha herencia y traits para reducir código duplicado.** Es uno de los bloques mejor estructurados del plugin, con código limpio, métodos cortos, template sin extract(), y uso apropiado de patrones de diseño. Sin embargo, comparte el problema común de falta de sanitización en get_field() y carga assets globalmente.

**Hallazgos principales:**
- ✅ **Excelente arquitectura** - Extiende TemplateBlockBase, usa PreviewDataTrait
- ✅ **Código muy limpio** - Métodos <20 líneas, sin complejidad
- ✅ **Template sin extract()** - Mejor práctica vs otros bloques
- ✅ **Buen escapado** - Uso correcto de esc_html(), wp_kses_post()
- ✅ **SVG inline** - No depende de helpers externos
- ❌ **Sanitización faltante** - get_field() sin sanitize
- ❌ **Assets globales** - CSS cargado en todas las páginas
- ⚠️ **CSS no usado** - Estilos para rating/title que no están en template
- ⚠️ **Sin validación $post_id** - Puede fallar con IDs inválidos

### Puntuación: 8.0/10

**Desglose:**
- Funcionalidad: 9/10 (completa, sin features faltantes)
- Seguridad: 7/10 (buen escapado, falta sanitización)
- Arquitectura: 9/10 (excelente uso de herencia y traits)
- Clean Code: 9/10 (muy legible, sin extract(), métodos cortos)
- Performance: 6/10 (assets globales, CSS no usado)
- Mantenibilidad: 8/10 (bien estructurado, extensible)

**Fortalezas:**
1. ✅ **Arquitectura ejemplar** - Mejor uso de herencia/traits del plugin
2. ✅ **Código muy limpio** - Métodos cortos (8-18 líneas), cero complejidad
3. ✅ **Template sin extract()** - Acceso directo a variables (mejor práctica)
4. ✅ **Separación de responsabilidades** - Preview/live bien separados
5. ✅ **Escapado consistente y correcto** - esc_html(), wp_kses_post(), wpautop()
6. ✅ **SVG inline** - No depende de IconHelper, reduce acoplamiento
7. ✅ **Trait reutilizable** - PreviewDataTrait compartido con otros bloques
8. ✅ **Responsive design** - Mobile-first, media queries apropiadas
9. ✅ **Accesibilidad considerada** - High contrast mode, semantic HTML
10. ✅ **Internacionalización** - Strings traducibles

**Debilidades:**
1. ❌ **Sin sanitización** - get_field() sin sanitize_text_field() (TODAS las llamadas)
2. ❌ **Assets globales** - CSS cargado en todas las páginas (263 líneas)
3. ⚠️ **CSS no usado** - Estilos para rating/title/stars no están en template (líneas 22-108)
4. ⚠️ **Sin validación $post_id** - No verifica que sea válido antes de get_field()
5. ⚠️ **Violación SRP leve** - Hace rendering + data + enqueue (mitigado por base class)
6. ⚠️ **Acoplado a ACF** - Depende directamente de get_field() (sin abstracción)
7. ⚠️ **Sin constantes** - Field keys hardcoded (typo risk)
8. ⚠️ **Sin tests unitarios** - No hay garantía de no-regresión
9. ⚠️ **Sin documentación de fields** - No documenta qué ACF fields espera
10. ⚠️ **Sin schema.org** - Pierde oportunidad de SEO con structured data

**Comparación con bloques auditados:**
- **Mejor que:** ContactPlannerForm (no tiene AJAX faltante, usa base class)
- **Mejor que:** Bloques ACF sin TemplateBlockBase (mejor arquitectura)
- **Similar a:** Otros bloques Template bien estructurados
- **Peor que:** (ninguno auditado hasta ahora - este es el mejor)

**Recomendación:**

**REFACTORIZAR CON PRIORIDAD MEDIA.** Este es uno de los bloques mejor escritos del plugin y sirve como **modelo a seguir** para otros bloques. Las correcciones necesarias son principalmente de seguridad (sanitización) y performance (assets condicionales), ambas rápidas de implementar. No requiere refactor arquitectónico mayor.

**Ruta recomendada:**
1. **Inmediato (2 horas):** Sanitizar get_field() + validar $post_id + documentar fields
2. **Corto plazo (1 día):** Assets condicionales + limpiar CSS no usado
3. **Mediano plazo (1 semana):** PackageDataProvider compartido + constantes
4. **Largo plazo (1 mes):** Schema.org + tests unitarios + SVG components

**El bloque puede ser 9.5/10 con sanitización + assets condicionales (2-3 horas de trabajo).**

**Este bloque debe usarse como referencia arquitectónica para refactorizar otros bloques del plugin.**

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Pendiente - Prioridad Media
**Próximo bloque:** 4/? Template

**Notas especiales:**
- Este bloque demuestra el valor de TemplateBlockBase - considerar migrar bloques legacy a esta arquitectura
- PreviewDataTrait es reutilizable - otros bloques deberían adoptarlo
- Template sin extract() es mejor práctica - aplicar a todos los bloques nuevos
- SVG inline reduce dependencias - considerar para otros bloques
