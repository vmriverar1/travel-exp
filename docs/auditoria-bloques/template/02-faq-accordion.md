# Auditoría: FAQAccordion (Template)

**Fecha:** 2025-11-09
**Bloque:** 2/? Template
**Tiempo:** 60 minutos

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `faq-accordion-template`
- **Namespace:** `travel-blocks/faq-accordion-template`
- **Taxonomy consultada:** `faq` (terms con ACF fields 'pregunta' y 'respuesta')
- **ACF Fields en taxonomy:**
  - `pregunta` - Texto de la pregunta (en term meta)
  - `respuesta` - Respuesta WYSIWYG (en term meta)
- **Clases CSS críticas:**
  - `faq-accordion`
  - `faq-accordion__container`
  - `faq-accordion__title`
  - `faq-accordion__list`
  - `faq-accordion__item`
  - `faq-accordion__question`
  - `faq-accordion__icon`
  - `faq-accordion__answer`
  - `faq-accordion__answer-content`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **COMPARTE ASSETS con ACF/FAQAccordion y Package/FAQAccordion:**
  - CSS: `/assets/blocks/faq-accordion.css` (199 líneas) - COMPARTIDO
  - JS: `/assets/blocks/faq-accordion.js` (114 líneas) - COMPARTIDO
- **TEMPLATE DIFERENTE:**
  - Template: `/templates/template/faq-accordion.php` (53 líneas) - ESPECÍFICO
  - ACF/Package usan: `/templates/faq-accordion.php` (82 líneas) - DIFERENTE
- **FUENTE DE DATOS ÚNICA:** Lee de taxonomy 'faq' con `get_the_terms()`
- Template usa `data-faq-toggle` (diferente de ACF/Package que usan `data-faq-trigger`)
- Template usa schema.org inline, NO JSON-LD separado
- NO usa `extract()` - recibe variables directamente

---

## 1. Información General

**Ubicación:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/src/Blocks/Template/FAQAccordion.php`
**Namespace:** `Travel\Blocks\Blocks\Template`
**Template:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/templates/template/faq-accordion.php`
**Assets (COMPARTIDOS):**
- CSS: `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/faq-accordion.css` (199 líneas)
- JS: `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/faq-accordion.js` (114 líneas)

**Tipo:** [X] Template Block (extiende TemplateBlockBase)

**Líneas de código:**
- Clase PHP: 147 líneas
- Template PHP: 53 líneas
- CSS: 199 líneas (COMPARTIDO con ACF y Package)
- JavaScript: 114 líneas (COMPARTIDO con ACF y Package)
- **TOTAL: 513 líneas** (313 si excluimos assets compartidos)

**Base Classes:**
- Extiende: `TemplateBlockBase`
- Usa trait: `PreviewDataTrait`

---

## 2. Propósito y Funcionalidad

**Descripción:**
Bloque que muestra FAQs (Preguntas Frecuentes) obtenidos desde una **taxonomy custom 'faq'** asignada al post actual. Cada term de la taxonomy tiene dos campos ACF: 'pregunta' y 'respuesta'. El bloque lee todos los terms asignados al post y los muestra en un accordion interactivo con markup Schema.org para SEO.

**DIFERENCIA CON OTROS FAQ BLOCKS:**
- **ACF/FAQAccordion:** Usuario ingresa FAQs manualmente en el bloque (ACF repeater)
- **Package/FAQAccordion:** Lee FAQs desde post meta 'faqs' del package
- **Template/FAQAccordion:** Lee FAQs desde taxonomy 'faq' asignada al post

**Inputs:**
- **Taxonomy terms:** Terms de taxonomy 'faq' asignados al post actual
- **Term ACF Fields:**
  - `pregunta` - Pregunta (texto)
  - `respuesta` - Respuesta (WYSIWYG HTML)

**Outputs:**
- Accordion HTML con título y lista de preguntas/respuestas
- Schema.org markup inline (FAQPage con Questions/Answers)
- Interactividad JavaScript (toggle accordion)
- Preview con datos de ejemplo en editor

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `TemplateBlockBase`
- Implementa: Ninguna
- Traits: `PreviewDataTrait`

**Propiedades:**
```php
protected string $name = 'faq-accordion-template';
protected string $title = 'FAQ Accordion (Template)';
protected string $description = 'Frequently Asked Questions accordion with schema markup for templates';
protected string $icon = 'editor-help';
protected array $keywords = ['faq', 'accordion', 'questions', 'answers', 'help', 'template'];
```

**Métodos Públicos:**
```
__construct(): void                                         (líneas 20-27)   - 8 líneas
register(): void                                            (líneas 32-57)   - 26 líneas
enqueue_assets(): void                                      (líneas 123-146) - 24 líneas
```

**Métodos Protected (heredados/implementados):**
```
render_preview(array $attributes): string                   (líneas 59-68)   - 10 líneas
render_live(int $post_id, array $attributes): string        (líneas 70-79)   - 10 líneas
```

**Métodos Privados:**
```
get_acf_faqs_data(): array                                  (líneas 88-118)  - 31 líneas
```

**Métodos Heredados de TemplateBlockBase:**
- `render()` - Maneja preview vs live mode (usa EditorHelper)
- `load_template()` - Carga template con extract() ⚠️
- `render_error()` - Muestra errores en WP_DEBUG

**Métodos Heredados de PreviewDataTrait:**
- `get_preview_faqs()` - Retorna 4 FAQs de ejemplo

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type()` - ACF Block (aunque NO usa ACF fields en el bloque)

⚠️ **PROBLEMA:** El bloque usa ACF para registro pero NO tiene campos ACF propios. Solo lee ACF fields de la taxonomy.

**Configuración:**
- name: `faq-accordion-template`
- title: "FAQ Accordion (Template)" (traducible)
- description: "Frequently Asked Questions accordion with schema markup for templates"
- category: `template-blocks` (heredado de TemplateBlockBase)
- icon: `editor-help`
- keywords: `['faq', 'accordion', 'questions', 'answers', 'help', 'template']`
- supports: (heredado)
  - anchor: true
  - align: false
  - html: false
- mode: `preview`
- render_callback: `[$this, 'render']` (heredado de TemplateBlockBase)
- example: Datos de preview con `get_preview_faqs()`

**Hook adicional:**
- `enqueue_block_assets` - registrado en línea 56

---

## 5. Campos ACF (si aplica)

**Definición:** ❌ NO - El bloque NO tiene campos ACF propios

**IMPORTANTE:**
- El bloque NO registra campos ACF
- En su lugar, DEPENDE de ACF fields en la **taxonomy 'faq'**
- Asume que cada term de taxonomy 'faq' tiene estos campos:
  - `pregunta` (texto)
  - `respuesta` (WYSIWYG)

**Estructura esperada:**
```
Taxonomy: faq
  └── Term: "best-time-to-visit" (ejemplo)
       ├── ACF Field: pregunta = "What is the best time to do the Inca Trail?"
       └── ACF Field: respuesta = "The best months are April to October..."
```

**Acceso a datos:**
```php
get_the_terms($post_id, 'faq')  // Obtiene terms asignados al post
get_field('pregunta', 'faq_' . $term->term_id)  // Lee ACF de term
get_field('respuesta', 'faq_' . $term->term_id) // Lee ACF de term
```

---

## 6. Flujo de Renderizado

**Preparación (TemplateBlockBase::render):**
1. Obtiene `$post_id` con `get_the_ID()`
2. Verifica si es preview con `EditorHelper::is_editor_mode($post_id)`
3. Si es preview → `render_preview($attributes)`
4. Si es live → `render_live($post_id, $attributes)`
5. Manejo de excepciones con `render_error()`

**Preview Mode (render_preview):**
1. Construye array `$data` con:
   - title: "Frequently Asked Questions"
   - faqs: `$this->get_preview_faqs()` (4 FAQs hardcoded del trait)
   - is_preview: true
2. Retorna `$this->load_template('faq-accordion', $data)`

**Live Mode (render_live):**
1. Obtiene título con `get_field('faq_title')` (⚠️ campo ACF que NO existe en el bloque)
2. Default a "Frequently Asked Questions" si vacío
3. Obtiene FAQs con `$this->get_acf_faqs_data()`
4. Construye array `$data` con title, faqs, is_preview: false
5. Retorna `$this->load_template('faq-accordion', $data)`

**get_acf_faqs_data() - Obtención de datos:**
1. Obtiene post ID actual con `get_the_ID()`
2. Obtiene terms de taxonomy 'faq' con `get_the_terms($post_id, 'faq')`
3. Valida que no sea WP_Error y no esté vacío
4. Loop por cada term:
   - Lee `pregunta` con `get_field('pregunta', 'faq_' . $term->term_id)`
   - Lee `respuesta` con `get_field('respuesta', 'faq_' . $term->term_id)`
   - Valida que ambos existan
   - Agrega a array: `['question' => $pregunta, 'answer' => $respuesta]`
5. Retorna array de FAQs

**Variables al Template:**
```php
$title     // string: Título de la sección
$faqs      // array: Lista de FAQs con 'question' y 'answer'
$is_preview // bool: Si es modo preview
```

**Estructura de cada $faq:**
```php
[
    'question' => string,  // Texto de la pregunta
    'answer'   => string,  // HTML de la respuesta (WYSIWYG)
]
```

**Template processing:**
- Template recibe variables directamente (NO usa extract - TemplateBlockBase lo hace)
- Loop sobre `$faqs` con foreach
- Escapado con `esc_html()`, `esc_attr()`, `wp_kses_post()`
- Schema.org markup INLINE en HTML (no JSON-LD separado)
- SVG inline para iconos
- `data-faq-toggle` en button (diferente de ACF/Package)
- `wpautop()` para formatear respuesta

---

## 7. Funcionalidades Adicionales

**AJAX:** ❌ No

**JavaScript:** ✅ Sí - `/assets/blocks/faq-accordion.js` (114 líneas)
- **COMPARTIDO** con ACF/FAQAccordion y Package/FAQAccordion
- Inicializa accordions con `initFAQAccordions()`
- Toggle con animación smooth (max-height transition)
- Keyboard accessibility (Enter, Space)
- Busca elementos con `[data-faq-item]`, `[data-faq-trigger]`, `[data-faq-content]`
- ⚠️ **PROBLEMA:** Template usa `data-faq-toggle` pero JS busca `data-faq-trigger` → JS NO FUNCIONA

**REST API:** ❌ No

**Hooks Propios:**
- Ninguno (solo usa `enqueue_block_assets`)

**Dependencias externas:**
- Constants: `TRAVEL_BLOCKS_URL`, `TRAVEL_BLOCKS_PATH`, `TRAVEL_BLOCKS_VERSION`
- WordPress functions: `get_the_ID()`, `get_the_terms()`, `get_field()`, `acf_register_block_type()`
- **Helper usado:** `EditorHelper::is_editor_mode()` (static call) ⚠️
- **Trait usado:** `PreviewDataTrait::get_preview_faqs()`
- **Base class:** `TemplateBlockBase`

**Schema.org Markup:**
- Implementado INLINE en template (no JSON-LD separado)
- Usa atributos `itemscope`, `itemprop`, `itemtype`
- FAQPage → Question → Answer
- Solo en frontend (no en preview)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP (Single Responsibility Principle):** ✅ **BUENO**
- Clase enfocada en una cosa: renderizar FAQs de taxonomy
- Responsabilidades claras:
  - Registro: método `register()`
  - Preview: `render_preview()`
  - Live: `render_live()`
  - Data: `get_acf_faqs_data()`
  - Assets: `enqueue_assets()`
- **Mejor que ACF/Package** porque TemplateBlockBase abstrae lógica común

**OCP (Open/Closed Principle):** ⚠️ **VIOLACIÓN LEVE**
- Taxonomía 'faq' hardcoded - no extensible a otras taxonomías
- ACF field names hardcoded ('pregunta', 'respuesta')
- No hay filtros/hooks para extender comportamiento
- No permite customizar data source sin modificar código

**LSP (Liskov Substitution Principle):** ✅ **BUENO**
- Extiende `TemplateBlockBase` correctamente
- Implementa métodos abstractos `render_preview()` y `render_live()`
- No rompe contrato de clase padre
- Puede sustituirse por cualquier TemplateBlock

**ISP (Interface Segregation Principle):** ✅ **N/A**
- No implementa interfaces (solo extiende clase abstracta)

**DIP (Dependency Inversion Principle):** ❌ **VIOLACIÓN CRÍTICA**
- Depende directamente de `get_field()` - función global ACF (no injectable)
- Depende de `get_the_terms()` - función global WordPress
- Depende de `get_the_ID()` - función global
- **Usa static helper:** `EditorHelper::is_editor_mode()` (herencia de TemplateBlockBase)
- **NO hay interfaces/abstracciones**
- **Peor que bloques sin helpers** (tiene acoplamiento a EditorHelper)

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ Métodos muy cortos (máximo 31 líneas)
- ✅ Lógica clara y directa
- ✅ No hay complejidad ciclomática alta
- ✅ Un solo nivel de anidación en loops

**Anidación:**
- ✅ Máximo 2 niveles de anidación
- ✅ Early returns en `get_acf_faqs_data()` (líneas 98-99)
- ✅ Validación clara de condiciones

**Duplicación:**
- ❌ **DUPLICACIÓN CRÍTICA:** Tres bloques FAQ haciendo lo mismo
  - ACF/FAQAccordion (203 líneas)
  - Package/FAQAccordion (207 líneas)
  - Template/FAQAccordion (147 líneas)
- ✅ Reutiliza assets (CSS/JS) - BUENO
- ⚠️ Templates diferentes pero salida similar
- ⚠️ Lógica de schema duplicada entre bloques

**Nombres:**
- ✅ Nombres descriptivos: `get_acf_faqs_data()`, `render_preview()`, etc.
- ⚠️ `faq_title` es confuso - campo ACF que NO se define
- ✅ Variables claras: `$pregunta`, `$respuesta`, `$faqs`
- ✅ Convención consistente

**Código Sin Uso:**
- ✅ No hay código muerto
- ⚠️ `get_field('faq_title')` busca campo que NO existe (línea 73)
- ✅ Todos los métodos se usan

**Otros problemas:**
- ⚠️ **Uso de `extract()`** en `TemplateBlockBase::load_template()` (heredado) - **MAL PRÁCTICA**
- ✅ No hay magic numbers
- ⚠️ Preview data viene de trait (mejor que hardcoded en método)
- ✅ Type hints consistentes

### 8.3 Problemas de Seguridad

**Sanitización:** ❌ **CRÍTICO**
- `get_field('pregunta', 'faq_' . $term->term_id)` - sin sanitización (línea 102)
- `get_field('respuesta', 'faq_' . $term->term_id)` - sin sanitización (línea 105)
- `get_field('faq_title')` - sin sanitización (línea 73)
- `$term->term_id` NO se sanitiza (debería ser `intval()`)
- **Riesgo:** XSS si admin malicioso crea terms con contenido peligroso
- Líneas críticas: 73, 102, 105

**Escapado:** ✅ **BUENO** (en template)
- Template usa correctamente:
  - `esc_html()` para título y pregunta (líneas 20, 32)
  - `esc_attr()` para atributos (línea 29)
  - `wp_kses_post(wpautop())` para respuesta HTML (línea 46)
- Schema.org inline - datos bien escapados
- SVG inline sin user input - seguro

**Nonces:** ✅ **N/A**
- No hay formularios ni AJAX

**Capabilities:** ⚠️ **PARCIAL**
- NO verifica permisos de usuario
- Cualquiera puede ver FAQs (probablemente OK - contenido público)
- NO hay validación de capabilities

**SQL:** ✅ **N/A**
- No hay queries SQL directas
- Usa `get_the_terms()` que está protegido

**Validación de Input:**
- ✅ `is_wp_error($faq_terms)` validado (línea 98) ✓
- ✅ `!empty($faq_terms)` validado (línea 98) ✓
- ✅ `!empty($pregunta) && !empty($respuesta)` validado (línea 108) ✓
- ❌ NO valida que `$term->term_id` sea entero
- ⚠️ NO sanitiza `$term->term_id` antes de concatenar

**XSS Potencial:**
- ✅ Pregunta escapada con `esc_html()`
- ⚠️ Respuesta usa `wp_kses_post()` - permite HTML (WYSIWYG)
- ⚠️ Título sin sanitizar antes de pasar a template
- ✅ Schema.org inline bien escapado

**Otros:**
- ✅ No hay `eval()`, `exec()`, `system()`
- ✅ Template path validado con `file_exists()` (heredado)
- ✅ No hay inclusión dinámica de archivos

### 8.4 Problemas de Arquitectura

**Namespace:** ✅ **CORRECTO**
- `Travel\Blocks\Blocks\Template` - apropiado y consistente

**Separación MVC:** ✅ **BUENO**
- **Model:** `get_acf_faqs_data()` - separado
- **View:** Template independiente en archivo separado
- **Controller:** `TemplateBlockBase::render()` - maneja lógica
- **Mejor que bloques sin base class**

**Acoplamiento:** **ALTO** ⚠️
- Acoplado a taxonomy 'faq' (nombre hardcoded)
- Acoplado a ACF fields 'pregunta', 'respuesta' (nombres hardcoded)
- Acoplado a ACF plugin (get_field, acf_register_block_type)
- Acoplado a `EditorHelper` (static call heredado)
- Acoplado a estructura específica de term meta
- **Más acoplado que bloques nativos de WordPress**

**Cohesión:** ✅ **ALTA**
- Métodos relacionados entre sí
- Funcionalidad clara: "mostrar FAQs de taxonomy"
- Todo gira alrededor de un propósito

**Otros problemas:**
- ❌ **DUPLICACIÓN:** Tres bloques FAQ con funcionalidad similar
- ⚠️ **Assets globales:** CSS/JS se cargan en TODAS las páginas
- ⚠️ **Template diferente** a ACF/Package pero salida similar
- ❌ **JavaScript NO funciona:** Template usa `data-faq-toggle`, JS busca `data-faq-trigger`
- ⚠️ Usa `acf_register_block_type()` pero podría usar `register_block_type()`
- ⚠️ `get_field('faq_title')` busca campo que NO está definido

**Dependencia de Taxonomy:**
- ❌ **RIESGO:** Totalmente dependiente de taxonomy 'faq' existente
- Si taxonomy 'faq' no existe → bloque no funciona
- ⚠️ No valida que taxonomy esté registrada
- ⚠️ Asume que terms tienen ACF fields específicos

**JavaScript Roto:**
- ❌ **CRÍTICO:** Template usa `data-faq-toggle` pero JS busca `data-faq-trigger`
- **Resultado:** Accordion NO funciona en Template/FAQAccordion
- ACF/Package usan `data-faq-trigger` (correcto)
- Template necesita cambiar a `data-faq-trigger` O crear JS separado

---

## 9. Comparación con ACF/FAQAccordion y Package/FAQAccordion

### 9.1 Tabla Comparativa

| Aspecto | ACF/FAQAccordion | Package/FAQAccordion | Template/FAQAccordion |
|---------|------------------|----------------------|----------------------|
| **Puntuación** | 8.5/10 | 7.5/10 | **6.5/10** |
| **Líneas PHP** | 203 | 207 | 147 |
| **Líneas Total** | 598 | 603 | 513 |
| **Base Class** | BlockBase | Ninguna | TemplateBlockBase |
| **Data Source** | ACF Repeater | Post Meta 'faqs' | Taxonomy 'faq' |
| **Template** | `/templates/faq-accordion.php` | `/templates/faq-accordion.php` | `/templates/template/faq-accordion.php` |
| **CSS** | `faq-accordion.css` (COMPARTIDO) | `faq-accordion.css` (COMPARTIDO) | `faq-accordion.css` (COMPARTIDO) |
| **JS** | `faq-accordion.js` (COMPARTIDO) | `faq-accordion.js` (COMPARTIDO) | `faq-accordion.js` (COMPARTIDO) |
| **JS Funciona** | ✅ Sí | ✅ Sí | ❌ NO (data attr diferente) |
| **Schema JSON-LD** | ✅ Sí (separado) | ✅ Sí (separado) | ❌ No (inline schema.org) |
| **Sanitización** | ⚠️ Parcial | ⚠️ Parcial | ❌ Ninguna |
| **Assets Condicionales** | ❌ No | ✅ Sí (!is_admin) | ❌ No |
| **EditorHelper** | ✅ Usa | ✅ Usa | ✅ Usa (heredado) |
| **extract()** | ✅ Usa | ✅ Usa | ✅ Usa (heredado) |
| **Preview Data** | ❌ Hardcoded | ❌ Hardcoded | ✅ Trait |
| **Registro** | `acf_register_block_type()` | `register_block_type()` | `acf_register_block_type()` |
| **ACF Fields** | ✅ Define propios | ❌ No | ❌ No (lee de taxonomy) |

### 9.2 Propósitos Diferentes o Duplicación?

**RESPUESTA: DUPLICACIÓN FUNCIONAL CON FUENTES DE DATOS DIFERENTES**

Los tres bloques tienen el **mismo propósito** (mostrar FAQs en accordion) pero con **fuentes de datos diferentes**:

1. **ACF/FAQAccordion (8.5/10):**
   - **Propósito:** FAQs manuales ingresadas en el bloque
   - **Caso de uso:** Editor quiere crear FAQs únicas para una página específica
   - **Ventaja:** Control total sobre contenido por página
   - **Desventaja:** Contenido no reutilizable

2. **Package/FAQAccordion (7.5/10):**
   - **Propósito:** FAQs asociadas a un package (post meta)
   - **Caso de uso:** FAQs almacenadas en datos del package
   - **Ventaja:** FAQs vinculadas al package, reutilizables
   - **Desventaja:** Solo funciona en contexto package

3. **Template/FAQAccordion (6.5/10):**
   - **Propósito:** FAQs desde taxonomy reutilizable
   - **Caso de uso:** FAQs globales asignables a múltiples posts via taxonomy
   - **Ventaja:** FAQs centralizadas y reutilizables entre posts
   - **Desventaja:** JavaScript NO funciona, menos flexible

### 9.3 Reutilización de Assets

**✅ BUENO:** Los tres bloques comparten CSS y JS
- `/assets/blocks/faq-accordion.css` (199 líneas) - COMPARTIDO
- `/assets/blocks/faq-accordion.js` (114 líneas) - COMPARTIDO

**⚠️ PROBLEMA:** Templates diferentes
- ACF/Package: `/templates/faq-accordion.php` (82 líneas)
- Template: `/templates/template/faq-accordion.php` (53 líneas)

**❌ CRÍTICO:** Template usa data attributes diferentes
- ACF/Package: `data-faq-trigger`, `data-faq-item`, `data-faq-content`
- Template: `data-faq-toggle` (solo en button) → **JS NO FUNCIONA**

### 9.4 Problemas de Duplicación

1. ❌ **Código duplicado:**
   - Método `generate_faq_schema()` duplicado en ACF y Package
   - Lógica de loop por FAQs duplicada en templates
   - Enqueue de assets duplicado en 3 clases

2. ❌ **Mantenimiento:**
   - Cambio en UI requiere modificar 2 templates
   - Bug en JS afecta a los 3 bloques
   - Cambio en CSS afecta a los 3 bloques

3. ⚠️ **Confusión:**
   - Usuario ve 3 bloques "FAQ Accordion" diferentes
   - Difícil saber cuál usar
   - Documentación necesaria para explicar diferencias

### 9.5 Recomendación

**REFACTORIZAR A UN SOLO BLOQUE CON FUENTES DE DATOS CONFIGURABLES**

Crear un único bloque `FAQAccordion` con selector de fuente:
- **Opción 1:** Manual (ACF repeater)
- **Opción 2:** Post Meta (desde package)
- **Opción 3:** Taxonomy (terms asignados)

**Beneficios:**
- Un solo código base
- Un solo template
- Un solo set de assets
- Más fácil mantener
- Menos confusión para usuarios

---

## 10. Recomendaciones de Refactorización

### Prioridad Alta

**1. Arreglar JavaScript - data attributes**
- **Acción:** Cambiar `data-faq-toggle` a `data-faq-trigger` en template
- **Razón:** **CRÍTICO** - JavaScript NO funciona actualmente
- **Riesgo:** **CRÍTICO** - Funcionalidad rota
- **Precauciones:**
  - Cambiar línea 30 template: `data-faq-toggle` → `data-faq-trigger`
  - Agregar `data-faq-item` al div.faq-accordion__item
  - Agregar `data-faq-content` al div.faq-accordion__answer
  - Verificar que JS funcione después
- **Esfuerzo:** 15 minutos
- **Código:**
```php
// Línea 25 template
<div class="faq-accordion__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" data-faq-item>

// Línea 30 template
data-faq-trigger

// Línea 38 template
<div class="faq-accordion__answer" id="faq-answer-<?php echo esc_attr($index); ?>" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" hidden data-faq-content>
```

**2. Sanitizar get_field() y term_id**
- **Acción:** Agregar `sanitize_text_field()` a todos los `get_field()` y `intval()` a term_id
- **Razón:** Prevenir XSS y garantizar integridad de datos
- **Riesgo:** **ALTO** - Vulnerabilidad de seguridad
- **Precauciones:**
  - `sanitize_text_field()` para pregunta
  - `wp_kses_post()` para respuesta (permite HTML)
  - `intval($term->term_id)` antes de usar
- **Esfuerzo:** 30 minutos
- **Código:**
```php
$term_id = intval($term->term_id);
$pregunta = sanitize_text_field(get_field('pregunta', 'faq_' . $term_id));
$respuesta = wp_kses_post(get_field('respuesta', 'faq_' . $term_id));
```

**3. Remover get_field('faq_title') o definir campo**
- **Acción:** Eliminar línea 73 o crear campo ACF 'faq_title' en el bloque
- **Razón:** Campo NO existe - genera warning
- **Riesgo:** **MEDIO** - Warning en logs, confusión
- **Precauciones:**
  - Si se elimina: usar título hardcoded "Frequently Asked Questions"
  - Si se crea: agregar campo al bloque (requiere ACF field group)
- **Esfuerzo:** 10 minutos
- **Código:**
```php
// Opción 1: Eliminar
$data = [
    'title' => 'Frequently Asked Questions',
    'faqs' => $this->get_acf_faqs_data(),
    'is_preview' => false,
];

// Opción 2: Usar atributo del bloque
$data = [
    'title' => $attributes['title'] ?? 'Frequently Asked Questions',
    'faqs' => $this->get_acf_faqs_data(),
    'is_preview' => false,
];
```

**4. Cargar assets condicionalmente**
- **Acción:** Usar `has_block()` para cargar CSS/JS solo cuando el bloque está presente
- **Razón:** Performance - no cargar 313 líneas de CSS/JS innecesariamente
- **Riesgo:** **MEDIO** - Puede afectar carga en editor
- **Precauciones:**
  - Verificar en Gutenberg editor
  - Verificar con bloques reutilizables
- **Esfuerzo:** 20 minutos
- **Código:**
```php
public function enqueue_assets(): void
{
    if (is_admin() || !has_block('travel-blocks/faq-accordion-template')) {
        return;
    }
    // ... enqueue logic
}
```

### Prioridad Media

**5. Validar que taxonomy 'faq' existe**
- **Acción:** En `register()` verificar `taxonomy_exists('faq')` antes de usar
- **Razón:** Prevenir errores si taxonomy no está registrada
- **Riesgo:** **MEDIO** - Bloque rompe sin taxonomy
- **Precauciones:** Log warning si no existe
- **Esfuerzo:** 15 minutos

**6. Consolidar 3 bloques FAQ en uno solo**
- **Acción:** Crear único bloque con selector de fuente de datos
- **Razón:** Eliminar duplicación, facilitar mantenimiento
- **Riesgo:** **ALTO** - Refactor mayor
- **Precauciones:**
  - Mantener retrocompatibilidad
  - Migración de contenido existente
  - Testing exhaustivo
- **Esfuerzo:** 8-10 horas

**7. Unificar templates**
- **Acción:** Usar un solo template para los 3 bloques
- **Razón:** Menos duplicación, más fácil mantener
- **Riesgo:** **MEDIO** - Puede romper diseños existentes
- **Precauciones:**
  - Probar con los 3 bloques
  - Verificar schema.org inline vs JSON-LD
- **Esfuerzo:** 2 horas

**8. Extraer lógica de schema a helper/trait**
- **Acción:** Crear `SchemaHelper::generate_faq_schema()` compartido
- **Razón:** Eliminar duplicación entre ACF/Package/Template
- **Riesgo:** **BAJO** - Mejora arquitectónica
- **Precauciones:** Usar mismo formato que bloques existentes
- **Esfuerzo:** 1 hora

**9. Usar register_block_type en lugar de acf_register_block_type**
- **Acción:** Cambiar a WordPress nativo ya que NO usa ACF fields propios
- **Razón:** Menos dependencia de ACF, más estándar
- **Riesgo:** **BAJO** - Cambio menor
- **Precauciones:**
  - Mantener funcionalidad preview
  - Verificar en editor
- **Esfuerzo:** 30 minutos

**10. Agregar JSON-LD schema separado**
- **Acción:** Cambiar schema.org inline a JSON-LD como ACF/Package
- **Razón:** Mejor práctica SEO, más flexible
- **Riesgo:** **BAJO** - Mejora SEO
- **Precauciones:** Reutilizar código de ACF/Package
- **Esfuerzo:** 1 hora

### Prioridad Baja

**11. Agregar DocBlocks completos**
- **Acción:** Documentar todos los métodos con @param, @return
- **Razón:** Mejor documentación, IDE autocomplete
- **Riesgo:** **NINGUNO** - Solo documentación
- **Esfuerzo:** 30 minutos

**12. Unit Tests**
- **Acción:** Crear tests para `get_acf_faqs_data()`, `render_preview()`, `render_live()`
- **Razón:** Garantizar funcionalidad, prevenir regresiones
- **Riesgo:** **NINGUNO** - Solo testing
- **Precauciones:** Mock WordPress functions y ACF
- **Esfuerzo:** 2-3 horas

**13. Optimizar CSS**
- **Acción:** Revisar si las 199 líneas son necesarias
- **Razón:** Performance, mantenibilidad
- **Riesgo:** **BAJO** - Puede romper estilos
- **Precauciones:** Testing visual exhaustivo
- **Esfuerzo:** 1 hora

---

## 11. Plan de Acción

**Fase 1: Arreglos Críticos** (Inmediato - 2 horas)
1. ✅ **Arreglar JavaScript data attributes** - CRÍTICO
2. ✅ **Sanitizar get_field() y term_id** - Seguridad
3. ✅ **Remover get_field('faq_title')** - Warning
4. ✅ **Cargar assets condicionalmente** - Performance

**Fase 2: Consolidación** (Corto plazo - 1 semana)
5. ⚠️ **Validar taxonomy 'faq' existe** - Prevención
6. ⚠️ **Consolidar 3 bloques FAQ** - Eliminar duplicación
7. ⚠️ **Unificar templates** - Mantenibilidad
8. ⚠️ **Extraer lógica schema** - DRY

**Fase 3: Mejoras** (Mediano plazo - 1 mes)
9. ⚠️ **Usar register_block_type nativo** - Menos dependencias
10. ⚠️ **Agregar JSON-LD schema** - SEO

**Fase 4: Calidad** (Largo plazo - 3 meses)
11. ⚠️ **DocBlocks** - Documentación
12. ⚠️ **Unit Tests** - Testing
13. ⚠️ **Optimizar CSS** - Performance

**Precauciones Generales:**
- ⛔ **NO cambiar** taxonomy 'faq' - rompe contenido
- ⛔ **NO cambiar** ACF field names 'pregunta', 'respuesta' - rompe contenido
- ⛔ **NO cambiar** clases CSS compartidas - rompe 3 bloques
- ⚔ **CRÍTICO:** Arreglar JS antes de lanzar a producción
- ✅ Testing exhaustivo en los 3 bloques FAQ después de cambios
- ✅ Considerar migración a bloque único consolidado

---

## 12. Checklist Post-Refactorización

### Funcionalidad
- [ ] JavaScript funciona correctamente (data attributes correctos)
- [ ] FAQs se renderizan en frontend
- [ ] Preview data aparece en editor
- [ ] Schema.org markup aparece en HTML
- [ ] Accordion toggle funciona (abrir/cerrar)
- [ ] Keyboard accessibility funciona (Enter, Space)
- [ ] Animación smooth funciona
- [ ] Se muestran FAQs de taxonomy 'faq'
- [ ] Solo FAQs con pregunta Y respuesta se muestran
- [ ] Mensaje vacío si no hay FAQs

### Arquitectura
- [ ] Assets se cargan condicionalmente
- [ ] get_field() está sanitizado
- [ ] term_id está validado con intval()
- [ ] faq_title resuelto (eliminado o definido)
- [ ] Taxonomy 'faq' existe antes de usar
- [ ] No hay warnings en logs
- [ ] Templates funcionan correctamente

### Seguridad
- [ ] Pregunta sanitizada con sanitize_text_field()
- [ ] Respuesta sanitizada con wp_kses_post()
- [ ] term_id validado como entero
- [ ] Todos los outputs escapados en template
- [ ] No hay XSS posible
- [ ] Schema.org bien escapado

### Performance
- [ ] CSS no se carga en páginas sin el bloque
- [ ] JS no se carga en páginas sin el bloque
- [ ] No hay errores en console
- [ ] Accordion smooth sin lag
- [ ] No hay queries N+1

### Compatibilidad
- [ ] Funciona en Gutenberg editor
- [ ] Funciona en frontend
- [ ] Preview funciona correctamente
- [ ] Responsive en móvil
- [ ] Funciona con bloques reutilizables
- [ ] Compatible con Full Site Editing
- [ ] Funciona con diferentes themes

### Regresión
- [ ] Posts con taxonomy 'faq' siguen mostrando FAQs
- [ ] Clases CSS no han cambiado
- [ ] Template sigue funcionando
- [ ] Schema.org markup sigue funcionando
- [ ] No rompe otros bloques FAQ (ACF/Package)

---

## 📊 Resumen Ejecutivo

### Estado Actual

**El bloque Template/FAQAccordion tiene un propósito válido (FAQs desde taxonomy reutilizable) pero está ROTO - el JavaScript NO funciona debido a data attributes incorrectos.** Es el tercero de tres bloques FAQ duplicados, comparte assets (CSS/JS) pero usa template diferente. Tiene una arquitectura más limpia que los otros dos (usa TemplateBlockBase) pero problemas de implementación críticos.

**Hallazgos principales:**
- ❌ **JavaScript ROTO** - usa `data-faq-toggle` en lugar de `data-faq-trigger`
- ❌ **Duplicación funcional** - tercer bloque FAQ con mismo propósito
- ✅ **Assets compartidos** - reutiliza CSS/JS de ACF/Package (bueno)
- ❌ **Template diferente** - salida similar pero código diferente
- ❌ **Sin sanitización** - get_field() sin sanitize
- ⚠️ **Campo fantasma** - get_field('faq_title') NO existe
- ✅ **Arquitectura limpia** - TemplateBlockBase + PreviewDataTrait
- ⚠️ **Assets globales** - se cargan en todas las páginas
- ⚠️ **Acoplamiento alto** - hardcoded a taxonomy 'faq'

### Puntuación: 6.5/10

**Desglose:**
- Funcionalidad: 4/10 (ROTO - JS no funciona)
- Seguridad: 5/10 (falta sanitización, buen escapado)
- Arquitectura: 8/10 (TemplateBlockBase es bueno)
- Clean Code: 7/10 (código limpio pero duplicado)
- Performance: 6/10 (assets globales)
- Mantenibilidad: 6/10 (duplicación entre 3 bloques)

**Comparación:**
- **ACF/FAQAccordion:** 8.5/10 (mejor, funciona correctamente)
- **Package/FAQAccordion:** 7.5/10 (funciona, assets condicionales)
- **Template/FAQAccordion:** 6.5/10 (ROTO, pero buena arquitectura)

**Fortalezas:**
1. ✅ **Arquitectura moderna** - TemplateBlockBase + PreviewDataTrait
2. ✅ **Assets compartidos** - reutiliza CSS/JS
3. ✅ **Código limpio** - métodos cortos, nombres claros
4. ✅ **Preview trait** - datos de ejemplo centralizados
5. ✅ **Propósito único** - FAQs reutilizables desde taxonomy
6. ✅ **Separación MVC** - modelo/vista/controller separados
7. ✅ **Type hints** - consistentes en toda la clase
8. ✅ **Early returns** - validaciones claras
9. ✅ **Template escapado** - buen uso de esc_html, esc_attr
10. ✅ **Schema.org inline** - SEO markup presente

**Debilidades:**
1. ❌ **JavaScript ROTO** - data attributes incorrectos (CRÍTICO)
2. ❌ **Duplicación crítica** - tercer bloque FAQ haciendo lo mismo
3. ❌ **Sin sanitización** - get_field() sin sanitize (SEGURIDAD)
4. ⚠️ **Campo fantasma** - get_field('faq_title') NO definido
5. ⚠️ **Assets globales** - CSS/JS en todas las páginas
6. ⚠️ **Template diferente** - duplica lógica de ACF/Package
7. ⚠️ **Acoplamiento alto** - hardcoded a 'faq' taxonomy y ACF fields
8. ⚠️ **Schema inline** - JSON-LD sería mejor (como ACF/Package)
9. ⚠️ **Sin validación taxonomy** - no verifica que 'faq' exista
10. ⚠️ **EditorHelper static** - acoplamiento heredado

**Comparación arquitectónica:**
- **Mejor que Package:** Usa base class abstracta, trait para preview
- **Peor que ACF:** JavaScript roto, funcionalidad no trabaja
- **Similar a Package:** Ambos leen de data externa (no manual)

**Recomendación:**

**REFACTORIZAR CON PRIORIDAD CRÍTICA.** El bloque está ROTO (JavaScript no funciona) y debe arreglarse antes de usarse en producción. Además, es el tercero de tres bloques FAQ duplicados - se debería consolidar en un solo bloque con fuentes de datos configurables.

**Ruta recomendada:**
1. **Inmediato (2 horas):** Arreglar JS + sanitización + faq_title
2. **Corto plazo (1 semana):** Consolidar 3 bloques FAQ en uno solo
3. **Mediano plazo (1 mes):** Unificar templates + JSON-LD schema
4. **Largo plazo (3 meses):** Tests + optimizaciones

**El bloque puede pasar de 6.5/10 a 9.0/10 si se consolidan los 3 bloques FAQ y se arreglan los problemas críticos.**

### Decisión sobre los 3 bloques FAQ

**RECOMENDACIÓN: CONSOLIDAR EN UN SOLO BLOQUE**

Crear `FAQAccordion` único con selector de fuente:
```
[ Data Source: ]
  ○ Manual Entry (ACF repeater)
  ○ Package Meta (post meta 'faqs')
  ○ Taxonomy Terms (taxonomy 'faq')
```

**Beneficios:**
- Un código base (147-207 líneas en lugar de 557 líneas totales)
- Un template (82 líneas en lugar de 135 líneas totales)
- Mismo CSS/JS (ya compartidos)
- Menos confusión para usuarios
- Más fácil mantener

**Alternativa:** Mantener separados pero:
- Arreglar JS en Template
- Unificar templates
- Compartir lógica schema
- Documentar diferencias claramente

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Pendiente - **PRIORIDAD CRÍTICA** (JavaScript roto)
**Próximo bloque:** 3/? Template (cuando se identifique)
**Nota especial:** Este bloque NO debe usarse en producción hasta arreglar JavaScript
