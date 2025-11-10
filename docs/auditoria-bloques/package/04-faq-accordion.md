# Auditoría: FAQ Accordion (Package)

**Fecha:** 2025-11-09
**Bloque:** 4/15 Package
**Tiempo:** 35 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `faq-accordion-package`
- **Namespace:** `travel-blocks/faq-accordion-package`
- **Post meta fields:** `faqs`, `faq_section_title`, `faq_section_description`
- **Estructura meta `faqs`:** Array de arrays con `question` y `answer`
- **Assets compartidos:** Reutiliza CSS/JS de ACF/FAQAccordion
- **Template compartido:** `templates/faq-accordion.php`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Compatibilidad con template compartido (usado por ACF y Package)
- JavaScript accordion sigue funcionando
- Schema.org JSON-LD válido
- Post meta structure en CPT Package

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/FAQAccordion.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** `/wp-content/plugins/travel-blocks/templates/faq-accordion.php` (COMPARTIDO)
**Assets:**
- CSS: `/assets/blocks/faq-accordion.css` (COMPARTIDO con ACF)
- JS: `/assets/blocks/faq-accordion.js` (COMPARTIDO con ACF)

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

**Líneas de código:** 207

---

## 2. Propósito y Funcionalidad

**Descripción:** Acordeón de preguntas frecuentes específico para CPT Package. Lee datos desde post meta (NO ACF) y genera Schema.org markup.

**Diferencia con ACF/FAQAccordion:**
- **ACF/FAQAccordion:** Campos ACF dentro del bloque (propósito general)
- **Package/FAQAccordion:** Post meta del CPT Package (específico para paquetes)
- **Template/FAQAccordion:** Taxonomía FAQ (términos reutilizables)

**Inputs (Post Meta):**
- `faqs` (array): Array de FAQs con estructura:
  ```php
  [
    ['question' => '...', 'answer' => '...'],
    ['question' => '...', 'answer' => '...'],
  ]
  ```
- `faq_section_title` (string): Título de sección
- `faq_section_description` (string): Descripción opcional

**Outputs:**
- HTML de acordeón interactivo (mismo template que ACF)
- JSON-LD con markup Schema.org FAQPage

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: Ninguna (clase standalone)
- Implementa: Ninguna
- Traits: Ninguno

**Métodos Públicos:**
```
1. register(): void - Registra bloque nativo
2. enqueue_assets(): void - Encola CSS/JS compartidos
3. render($attributes, $content, $block): string
```

**Métodos Privados:**
```
1. get_preview_data(): array - Datos para editor
2. get_post_data(int $post_id): array - Lee post meta
3. generate_faq_schema(array $faq_items): string - Schema.org
4. load_template(string $template_name, array $data): void
```

---

## 4. Registro del Bloque

**Método:** `register_block_type()` (WordPress nativo)

**Configuración:**
- name: `travel-blocks/faq-accordion-package`
- api_version: 2
- title: 'FAQ Accordion (Package)'
- description: 'Frequently asked questions with accordion and SEO schema - NO ACF'
- category: `travel`
- icon: `editor-help`
- keywords: `['faq', 'questions', 'accordion', 'help', 'package']`

**Supports:**
- anchor: true
- html: false

---

## 5. Fuente de Datos

**Tipo:** Post Meta (nativo WordPress, NO ACF)

**Preview Mode:**
- Detecta con `EditorHelper::is_editor_mode($post_id)`
- Retorna 4 preguntas de ejemplo

**Live Mode:**
- Lee `get_post_meta($post_id, 'faqs', true)`
- Transforma array al formato esperado
- Agrega `open_default => true` al primer item

**Transformación de datos:**
```php
foreach ($faqs as $index => $faq) {
    if (is_array($faq) && !empty($faq['question']) && !empty($faq['answer'])) {
        $faq_items[] = [
            'question' => $faq['question'],
            'answer' => $faq['answer'],
            'open_default' => $index === 0,
        ];
    }
}
```

---

## 6. Flujo de Renderizado

**Preparación:**
1. get_the_ID()
2. Check si es preview con EditorHelper
3. get_post_data() o get_preview_data()
4. Validar que `faq_items` no esté vacío
5. generate_faq_schema()
6. Preparar $data array
7. load_template('faq-accordion', $data)

**Schema.org:**
- Tipo: FAQPage
- mainEntity: Array de Question/Answer
- Sanitiza con wp_strip_all_tags()

**Error Handling:**
- Try-catch en método render()
- WP_DEBUG muestra mensaje de error
- Sin WP_DEBUG retorna string vacío

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa
**JavaScript:** ✅ SÍ - Accordion interactivo (compartido)
**Schema.org:** ✅ SÍ - FAQPage markup
**Hooks:** No define

**Reutilización de código:**
- ✅ CSS compartido con ACF/FAQAccordion
- ✅ JS compartido con ACF/FAQAccordion
- ✅ Template compartido con ACF/FAQAccordion

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ Cumple - Solo maneja FAQ de Package CPT
**OCP:** ✅ Cumple
**LSP:** ✅ N/A
**ISP:** ✅ N/A
**DIP:** ⚠️ Acoplamiento medio
- Depende de EditorHelper (aceptable)
- Depende directamente de template específico

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ render(): 26 líneas
- ✅ get_post_data(): 25 líneas
- ✅ generate_faq_schema(): 28 líneas
- ✅ get_preview_data(): 28 líneas
- ✅ Todos <30 líneas

**Anidación:**
- ✅ <3 niveles en todos los métodos

**Duplicación:**
- ✅ Reutiliza assets (CSS/JS) de ACF - EXCELENTE
- ✅ Reutiliza template - EXCELENTE
- ⚠️ Método generate_faq_schema() duplicado de ACF/FAQAccordion
  - Impacto: BAJO - Es idéntico, podría extraerse a utility class
- ⚠️ Método load_template() duplicado
  - Impacto: MEDIO - Cada bloque reimplementa lo mismo

**Nombres:**
- ✅ Descriptivos y claros

**Código Sin Uso:**
- ✅ Todo el código se usa

### 8.3 Problemas de Seguridad

**Sanitización:**
- ⚠️ **CRÍTICO:** Post meta NO sanitizado antes de usar
  - `get_post_meta($post_id, 'faqs', true)` se usa directo
  - No valida que sea array
  - No valida estructura de cada FAQ
  - Líneas 133-149
- ✅ Schema usa wp_strip_all_tags()
- ✅ Template escapa con esc_html() y wp_kses_post()

**Validación:**
- ⚠️ No valida que $post_id sea de tipo 'package'
- ⚠️ No valida estructura de $faqs antes de iterar
- ✅ Valida `!empty($faq['question']) && !empty($faq['answer'])`

**Escapado:**
- ✅ Template maneja escapado correctamente

**Nonces:** ✅ N/A
**Capabilities:** ✅ N/A
**SQL:** ✅ N/A - usa get_post_meta()

### 8.4 Problemas de Arquitectura

**Namespace:**
- ⚠️ **Incorrecto** (igual que ACF)
  - Actual: `Travel\Blocks\Blocks\Package`
  - Esperado: `Travel\Blocks\Package`

**Compatibilidad con Template:**
- ⚠️ **IMPORTANTE:** Template espera variable `$block` pero recibe `$data`
  - Línea 88: `load_template('faq-accordion', $data)`
  - Template línea 16: `$block_id = 'faq-' . $block['id'];`
  - **POSIBLE BUG:** Template podría romper si no recibe `$block`
  - Verificar si template funciona con ambos bloques

**Separación de responsabilidades:**
- ⚠️ load_template() dentro de clase (debería ser utility)
- ⚠️ generate_faq_schema() duplicado (debería ser service)

**Acoplamiento:**
- ✅ Bajo acoplamiento general
- ⚠️ Acoplado a template específico

### 8.5 Comparación con ACF/FAQAccordion

**ACF/FAQAccordion (8.5/10):**
- Usa campos ACF dentro del bloque
- Propósito general (cualquier página)
- Hereda de BlockBase
- Código casi idéntico en lógica

**Package/FAQAccordion (este):**
- Usa post meta nativo
- Específico para CPT Package
- Clase standalone
- Reutiliza assets de ACF (✅ EXCELENTE)

**¿Hay duplicación?**
- ❌ NO hay duplicación funcional
- ✅ Tienen propósitos DIFERENTES:
  - ACF: Bloque manual para cualquier contenido
  - Package: Automático desde datos del paquete
- ✅ Reutilización de assets es CORRECTA (DRY)
- ⚠️ Sí hay duplicación de código (generate_faq_schema, load_template)

---

## 9. Recomendaciones de Refactorización

### Prioridad Alta

**1. Validar y sanitizar post meta**
- **Acción:** Validar estructura de `faqs` antes de usar
  ```php
  private function validate_faqs_meta($faqs): array {
      if (!is_array($faqs)) return [];

      return array_filter($faqs, function($faq) {
          return is_array($faq)
              && isset($faq['question'], $faq['answer'])
              && is_string($faq['question'])
              && is_string($faq['answer']);
      });
  }
  ```
- **Razón:** Seguridad - post meta puede contener cualquier cosa
- **Riesgo:** BAJO
- **Esfuerzo:** 20 min

**2. Verificar compatibilidad con template**
- **Acción:** Revisar que template funcione con variable `$data`
- **Razón:** Línea 16 del template usa `$block['id']` pero recibe `$data`
- **Riesgo:** ALTO - Posible bug en producción
- **Esfuerzo:** 30 min
- **Precaución:** Testear con ambos bloques (ACF y Package)

### Prioridad Media

**3. Validar post type**
- **Acción:** Verificar que `$post_id` sea de tipo 'package'
  ```php
  if (get_post_type($post_id) !== 'package') {
      return '';
  }
  ```
- **Razón:** Bloque solo debe usarse en packages
- **Riesgo:** BAJO
- **Esfuerzo:** 5 min

**4. Extraer generate_faq_schema() a service class**
- **Acción:** Crear `SchemaGenerator` service
- **Razón:** Duplicado en ACF/Package/Template
- **Riesgo:** MEDIO
- **Esfuerzo:** 45 min

**5. Corregir Namespace**
- **Acción:** Cambiar a `Travel\Blocks\Package`
- **Razón:** PSR-4 compliance
- **Riesgo:** MEDIO
- **Precauciones:** Actualizar autoload, registros
- **Esfuerzo:** 30 min

### Prioridad Baja

**6. Extraer load_template() a TemplateLoader**
- **Acción:** Crear utility class TemplateLoader
- **Razón:** Duplicado en múltiples bloques
- **Riesgo:** BAJO
- **Esfuerzo:** 60 min (afecta múltiples bloques)

---

## 10. Plan de Acción

**Orden sugerido:**
1. Verificar compatibilidad con template (CRÍTICO)
2. Validar y sanitizar post meta (seguridad)
3. Validar post type
4. Corregir namespace
5. Extraer generate_faq_schema() a service
6. Extraer load_template() a utility

**Precauciones:**
- ⛔ NO cambiar block name (`faq-accordion-package`)
- ⛔ NO cambiar estructura de post meta `faqs`
- ⛔ NO romper compatibilidad con template compartido
- ⚠️ TESTEAR con CPT Package real
- ⚠️ TESTEAR que acordeón funcione (JS compartido)
- ⚠️ VALIDAR Schema.org (Google Rich Results Test)

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque se inserta correctamente en Package CPT
- [ ] Preview data aparece en editor
- [ ] FAQs se leen correctamente de post meta
- [ ] Acordeón funciona (expand/collapse)
- [ ] Primer item abre por defecto
- [ ] Schema.org aparece en source code
- [ ] Schema válido en Google Rich Results Test
- [ ] Template funciona con variable `$data` correctamente

### Datos
- [ ] Post meta `faqs` se valida antes de usar
- [ ] Estructura de cada FAQ se valida
- [ ] Maneja correctamente post meta vacío o inválido
- [ ] No rompe con post meta corrupto

### Arquitectura
- [ ] Namespace correcto (si se cambió)
- [ ] SchemaGenerator funciona (si se extrajo)
- [ ] TemplateLoader funciona (si se extrajo)

### Compatibilidad
- [ ] ACF/FAQAccordion sigue funcionando
- [ ] Template/FAQAccordion sigue funcionando
- [ ] Assets compartidos funcionan para todos
- [ ] No hay conflictos de CSS/JS

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Excelente reutilización de assets (CSS/JS compartidos)
- ✅ Excelente reutilización de template
- ✅ Schema.org bien implementado
- ✅ Métodos cortos y enfocados
- ✅ Try-catch para error handling
- ⚠️ **CRÍTICO:** Posible incompatibilidad con template (variable `$block` vs `$data`)
- ⚠️ Post meta no validado (seguridad)
- ⚠️ Namespace incorrecto
- ⚠️ Duplicación de código (generate_faq_schema, load_template)

### Puntuación: 7.5/10

**Fortalezas:**
- **EXCELENTE** reutilización de código (assets, template)
- Schema.org para SEO
- Código limpio y métodos cortos
- Error handling con try-catch
- Propósito bien diferenciado de ACF/FAQAccordion (NO es duplicación)

**Debilidades:**
- **CRÍTICO:** Posible bug con template (variable `$block`)
- Falta validación de post meta (seguridad)
- No valida post type
- Namespace incorrecto
- Código duplicado (schemas, templates)

### Comparación con ACF/FAQAccordion (8.5/10)

**¿Hay duplicación funcional?**
- ❌ **NO** - Tienen propósitos diferentes:
  - **ACF:** Bloque manual de propósito general
  - **Package:** Bloque automático para CPT Package
  - **Template:** Bloque basado en taxonomía FAQ

**¿Comparten código?**
- ✅ **SÍ** - Y es CORRECTO (DRY):
  - CSS compartido
  - JS compartido
  - Template compartido
- ⚠️ También duplican código (schemas, load_template) - MAL

**Recomendación:** ✅ Propósitos diferentes justifican bloques separados. Refactorización MEDIA necesaria (seguridad y compatibilidad con template).

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Media - Validación de datos y verificar template
**Siguiente:** Verificar Template/FAQAccordion para completar el análisis de los 3 FAQ blocks
