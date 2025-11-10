# Auditoría: Contact Form (ACF)

**Fecha:** 2025-11-09
**Bloque:** 2/15 ACF
**Tiempo:** 40 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `contact-form`
- **Namespace:** `acf/contact-form` (ACF block)
- **AJAX action:** `travel_hero_form_submit` (rompe envío de formularios)
- **Nonce action:** `travel_contact_form` (rompe seguridad)
- **Campos JSON:** Dependencia externa `/travel-acf-fields/acf-json/group_contact_form_hero.json`
- **Clases CSS:** `.hero-form`, `.hero-form__field`, etc.

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- ⚠️ **Envío de emails:** Sistema crítico en producción
- ⚠️ **JavaScript:** `contact-form.js` hace AJAX al action
- ⚠️ **Método handle_form_submit():** Puede ser llamado desde JS externo

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/ContactForm.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/wp-content/plugins/travel-blocks/templates/contact-form.php`
**Assets:**
- CSS: `/assets/blocks/contact-form.css`
- JS: `/assets/blocks/contact-form.js` ⚠️ CRÍTICO - maneja AJAX

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

---

## 2. Propósito y Funcionalidad

**Descripción:** Formulario de contacto tipo "hero" con imagen de fondo, campos personalizables y envío por email.

**Inputs (ACF - desde JSON):**
- Campos definidos en archivo JSON externo
- JSON location: `/wp-content/plugins/travel-acf-fields/acf-json/group_contact_form_hero.json`

**Outputs:**
- Formulario HTML con validación
- Envío AJAX a servidor
- Email HTML formateado al administrador

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `BlockBase`
- Implementa: Ninguna
- Traits: Ninguno

**Métodos Públicos:**
```
1. __construct(): void
2. register(): void - Registra bloque ACF y handlers AJAX
3. enqueue_assets(): void - Encola CSS/JS, crea nonce
4. render($block, $content, $is_preview, $post_id): void
5. handle_form_submit(): void - Handler AJAX (CRÍTICO)
```

**Métodos Privados:**
```
1. build_email_template($data): string - Genera HTML del email
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type`

**Configuración:**
- name: `contact-form`
- category: `template-blocks`
- supports: align=[wide,full], anchor=true

**ACF Fields:** Cargados desde JSON externo (no inline)

---

## 5. Campos ACF

**Definición:** [X] JSON (archivo externo)

**Ubicación:** `/wp-content/plugins/travel-acf-fields/acf-json/group_contact_form_hero.json`

**⚠️ RIESGO:** Dependencia externa - si JSON no existe, campos no se cargan

---

## 6. Flujo de Renderizado

**AJAX Handler:**
1. Usuario llena formulario
2. JavaScript envía AJAX a `travel_hero_form_submit`
3. `handle_form_submit()` valida y sanitiza
4. `build_email_template()` genera HTML
5. `wp_mail()` envía email
6. Respuesta JSON al frontend

**Sanitización:** ✅ Completa (líneas 165-172)
**Validación:** ✅ Completa (líneas 174-204)
**Nonce:** ✅ Verificado (línea 158)

---

## 7. Funcionalidades Adicionales

**AJAX:** ✅ SÍ - CRÍTICO
- Action: `travel_hero_form_submit`
- Nonce: `travel_contact_form`
- Capability: Público (nopriv)

**JavaScript:** ✅ SÍ
- Archivo: `contact-form.js`
- Maneja envío AJAX
- Recibe nonce vía wp_localize_script

**Hooks Propios:** No define

**Dependencias Externas:**
- ⚠️ JSON file de ACF fields
- ⚠️ Sistema de email (wp_mail)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ❌ **VIOLA**
- Clase hace: registro, renderizado, AJAX, validación, email, HTML template
- Ubicación: Toda la clase
- Impacto: ALTO - Múltiples responsabilidades

**DIP:** ⚠️ **VIOLA**
- Dependencia directa de wp_mail sin abstracción
- Ubicación: Línea 245
- Impacto: MEDIO

### 8.2 Problemas Clean Code

**Complejidad:**
- ❌ **handle_form_submit(): 101 líneas** (155-256)
  - Impacto: ALTO
- ❌ **build_email_template(): 198 líneas** (261-458)
  - Impacto: CRÍTICO - Método ENORME

**Anidación:**
- ✅ Anidación aceptable (<3 niveles)

**Duplicación:**
- ✅ No detectada

**Código Sin Uso:**
- ✅ Ninguno

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ **EXCELENTE** - Todas las entradas sanitizadas:
  - `sanitize_text_field()` para nombres, teléfono, país
  - `sanitize_email()` para email
  - `absint()` para package_id
  - `sanitize_textarea_field()` para mensaje

**Escapado:**
- ✅ Template escapa correctamente
- ✅ Email template escapa con `esc_html()`

**Nonces:**
- ✅ **BIEN IMPLEMENTADO**
  - Generado: Línea 113
  - Verificado: Línea 158

**Validación:**
- ✅ **COMPLETA** - Valida campos requeridos y formato de email

**SQL:**
- ✅ No usa queries directas

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto** (igual que Breadcrumb)
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`

**Separación MVC:**
- ❌ **Viola MVC**
  - HTML de email hardcodeado en método privado (198 líneas)
  - Debería estar en template separado

**Acoplamiento:**
- ⚠️ Alto acoplamiento con wp_mail
- ⚠️ Dependencia de archivo JSON externo

**Otros:**
- ❌ **Template HTML de email en clase PHP** (anti-patrón)

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**⛔ BLOQUE CRÍTICO EN PRODUCCIÓN - NO romper envío de emails ni AJAX**

### Prioridad Alta

**1. Extraer Email Service**
- **Acción:** Crear `EmailService` con métodos:
  ```php
  EmailService::send_contact_form($data)
  EmailService::build_template($data)
  ```
- **Razón:** SRP - Separar responsabilidad de envío de emails
- **Riesgo:** MEDIO - Sistema crítico
- **Precauciones:**
  - ⛔ NO cambiar AJAX action name
  - ⛔ NO cambiar formato de datos
  - ✅ Testing exhaustivo de envío
- **Esfuerzo:** 2h

**2. Mover template de email a archivo separado**
- **Acción:** Crear `/templates/emails/contact-form.php`
- **Razón:** MVC - Separar vista de lógica
- **Riesgo:** BAJO - Solo mueve HTML
- **Precauciones:** Mantener mismo HTML
- **Esfuerzo:** 1h

**3. Dividir handle_form_submit()**
- **Acción:** Extraer a métodos:
  ```php
  private function validate_form_data($data)
  private function send_notification_email($data)
  ```
- **Razón:** KISS - Método muy largo (101 líneas)
- **Riesgo:** BAJO - Método privado
- **Precauciones:**
  - ✅ Mantener lógica exacta
  - ✅ Testing AJAX completo
- **Esfuerzo:** 1.5h

### Prioridad Media

**4. Verificar existencia de JSON file**
- **Acción:** Agregar warning si JSON no existe:
  ```php
  if (!file_exists($json_file)) {
      error_log('ContactForm: ACF JSON file not found');
  }
  ```
- **Razón:** Prevenir errores silenciosos
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

**5. Corregir Namespace**
- **Acción:** Cambiar a `Travel\Blocks\ACF`
- **Razón:** PSR-4
- **Riesgo:** MEDIO
- **Precauciones:** Actualizar autoload
- **Esfuerzo:** 30 min

### Prioridad Baja

**6. Agregar logging de errores de email**
- **Acción:** Log si wp_mail falla
- **Razón:** Debugging
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

---

## 10. Plan de Acción

**Orden:**
1. Mover template email a archivo separado
2. Dividir handle_form_submit()
3. Extraer EmailService
4. Verificar JSON file
5. Corregir namespace
6. Agregar logging

**Precauciones CRÍTICAS:**
- ⛔ NO cambiar `travel_hero_form_submit` (AJAX action)
- ⛔ NO cambiar `travel_contact_form` (nonce action)
- ⛔ NO romper envío de emails
- ✅ Testing: Enviar formulario real, verificar email llega
- ✅ Testing: Verificar AJAX en dev tools (Network tab)
- ✅ Testing: Verificar nonce se genera y verifica

---

## 11. Checklist Post-Refactorización

### Funcionalidad CRÍTICA
- [ ] Formulario se muestra correctamente
- [ ] Todos los campos son editables
- [ ] JavaScript se carga sin errores (console)
- [ ] Enviar formulario → AJAX funciona (Network tab)
- [ ] Validación muestra errores correctos
- [ ] **Email llega al administrador** ⚠️ CRÍTICO
- [ ] Email tiene formato HTML correcto
- [ ] Todos los datos aparecen en email
- [ ] Reply-To funciona
- [ ] Mensaje de éxito se muestra al usuario

### Seguridad
- [ ] Nonce se genera (ver source)
- [ ] Nonce se verifica (test envío)
- [ ] Sanitización funciona (test con XSS)
- [ ] Validación funciona (campos vacíos)

### Arquitectura
- [ ] Template de email en archivo separado (si se movió)
- [ ] EmailService funciona (si se creó)
- [ ] Métodos <30 líneas (si se dividió)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ **Seguridad:** EXCELENTE (sanitización, validación, nonce)
- ✅ Funciona correctamente
- ❌ Método build_email_template() ENORME (198 líneas)
- ❌ Clase con múltiples responsabilidades (SRP violado)
- ⚠️ Dependencia de JSON externo

### Puntuación: 6.5/10

**Fortalezas:**
- Seguridad impecable (mejor auditoría de seguridad hasta ahora)
- Sistema AJAX bien implementado
- Validación completa
- Nonce correcto

**Debilidades:**
- Método gigante de template email
- Viola SRP (hace demasiado)
- HTML hardcodeado en clase PHP
- Método handle_form_submit() muy largo

**Recomendación:** Refactorizar con MÁXIMA precaución - es sistema crítico de contacto.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Pendiente - REQUIERE TESTING EXHAUSTIVO
