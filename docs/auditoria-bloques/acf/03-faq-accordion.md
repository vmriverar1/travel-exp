# Auditoría: FAQ Accordion (ACF)

**Fecha:** 2025-11-09
**Bloque:** 3/15 ACF
**Tiempo:** 30 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `faq-accordion`
- **Namespace:** `acf/faq-accordion`
- **Campos ACF:** `section_title`, `section_description`, `faq_items` (repeater)
- **Sub-campos:** `question`, `answer`, `open_default`
- **Clases CSS/JS:** Usadas para acordeón (collapse/expand)

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- JavaScript `faq-accordion.js` maneja click events
- Schema.org JSON-LD (critical para SEO)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/FAQAccordion.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/wp-content/plugins/travel-blocks/templates/faq-accordion.php`
**Assets:**
- CSS: `/assets/blocks/faq-accordion.css`
- JS: `/assets/blocks/faq-accordion.js` (maneja acordeón)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

---

## 2. Propósito y Funcionalidad

**Descripción:** Acordeón de preguntas frecuentes (FAQ) con markup Schema.org para SEO.

**Inputs (ACF):**
- `section_title` (text): Título de sección
- `section_description` (textarea): Descripción opcional
- `faq_items` (repeater): Lista de FAQ
  - `question` (text, required)
  - `answer` (wysiwyg, required)
  - `open_default` (true_false): Abrir por defecto

**Outputs:**
- HTML de acordeón interactivo
- JSON-LD con markup Schema.org FAQPage

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `BlockBase`
- Implementa: Ninguna
- Traits: Ninguno

**Métodos Públicos:**
```
1. __construct(): void
2. register(): void - Registra bloque ACF
3. render($block, $content, $is_preview, $post_id): void
4. enqueue_assets(): void - CSS + JS
```

**Métodos Privados:**
```
1. generate_faq_schema($faq_items): string - Genera JSON-LD Schema.org
```

---

## 4-5. Registro y Campos ACF

**Configuración:**
- name: `faq-accordion`
- category: `travel`
- icon: `editor-help`

**Campos:** [X] PHP inline (bien definidos)

**Repeater Structure:**
- Min items: 1
- Layout: block
- Sub-fields: question, answer, open_default

---

## 6. Flujo de Renderizado

**Preparación:**
1. get_field('section_title')
2. get_field('faq_items')
3. generate_faq_schema() - crea JSON-LD
4. Pass data to template

**Schema.org:**
- Tipo: FAQPage
- Cada item: Question → Answer
- Sanitiza con wp_strip_all_tags()

**JavaScript:**
- Maneja click en preguntas para expand/collapse

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa
**JavaScript:** ✅ SÍ - Acordeón interactivo
**Schema.org:** ✅ SÍ - FAQPage markup
**Hooks:** No define

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ Cumple - Solo maneja FAQ accordion
**OCP:** ✅ Cumple
**LSP:** ✅ Cumple
**ISP:** ✅ N/A
**DIP:** ✅ Cumple

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ Todos los métodos <30 líneas
- ✅ render(): 19 líneas
- ✅ generate_faq_schema(): 29 líneas

**Anidación:**
- ✅ <3 niveles en todos los métodos

**Duplicación:**
- ⚠️ Posible duplicación con `Package\FAQAccordion` y `Template\FAQAccordion`
  - Impacto: MEDIO - Verificar si existen 3 FAQ blocks

**Nombres:**
- ✅ Descriptivos

**Código Sin Uso:**
- ✅ Ninguno

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ Schema usa wp_strip_all_tags()

**Escapado:**
- ✅ Template debe escapar (verificar)

**Nonces:** ✅ N/A
**Capabilities:** ✅ N/A
**SQL:** ✅ N/A

### 8.4 Problemas de Arquitectura

**Namespace:**
- ⚠️ Incorrecto (igual que anteriores)
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`

**Separación MVC:**
- ✅ Bien separado

**Acoplamiento:**
- ✅ Bajo acoplamiento

**Otros:**
- ⚠️ Posible duplicación con otros FAQ blocks

---

## 9. Recomendaciones de Refactorización

### Prioridad Alta

**1. Verificar duplicación de FAQ blocks**
- **Acción:** Buscar si existen otros FAQ blocks:
  ```bash
  grep -r "FAQAccordion" src/Blocks/
  ```
- **Razón:** Evitar duplicación funcional
- **Riesgo:** BAJO - Solo investigación
- **Esfuerzo:** 15 min

### Prioridad Media

**2. Corregir Namespace**
- **Acción:** Cambiar a `Travel\Blocks\ACF`
- **Razón:** PSR-4
- **Riesgo:** MEDIO
- **Precauciones:** Actualizar autoload
- **Esfuerzo:** 30 min

### Prioridad Baja

**3. Verificar template escapa correctamente**
- **Acción:** Revisar template que escape $answer (WYSIWYG)
- **Razón:** WYSIWYG puede tener HTML
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min

---

## 10. Plan de Acción

**Orden:**
1. Verificar duplicación FAQ blocks
2. Corregir namespace
3. Verificar escapado en template

**Precauciones:**
- ⛔ NO cambiar block name
- ⛔ NO cambiar ACF field names
- ⛔ NO romper JavaScript acordeón
- ✅ Testing: Acordeón expand/collapse funciona
- ✅ Testing: Schema.org markup válido (Google Rich Results Test)

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque se inserta correctamente
- [ ] Repeater funciona (agregar/quitar items)
- [ ] Acordeón funciona (click expand/collapse)
- [ ] Solo un item abierto a la vez (o múltiples, según config)
- [ ] Items con "open_default" abren automáticamente
- [ ] Schema.org aparece en source (view-source)
- [ ] Schema válido (test en Google Rich Results)

### Arquitectura
- [ ] Namespace correcto (si se cambió)
- [ ] Sin duplicación (si se consolidó)

### Seguridad
- [ ] Template escapa answer WYSIWYG

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Código limpio y simple
- ✅ Métodos cortos (<30 líneas)
- ✅ Schema.org bien implementado
- ✅ Bajo acoplamiento
- ⚠️ Namespace incorrecto
- ⚠️ Posible duplicación con otros FAQ

### Puntuación: 8.5/10

**Fortalezas:**
- Código muy limpio (mejor ejemplo hasta ahora)
- Schema.org para SEO
- Métodos pequeños y enfocados
- No viola SOLID

**Debilidades:**
- Namespace incorrecto (menor)
- Posible duplicación funcional

**Recomendación:** ✅ Buen ejemplo de cómo debe ser un bloque. Mínima refactorización necesaria.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Mínima - Solo namespace
