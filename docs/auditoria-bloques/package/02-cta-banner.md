# Auditoría: CTA Banner (Package)

**Fecha:** 2025-11-09
**Bloque:** 2/X Package
**Tiempo:** 45 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `cta-banner`
- **Namespace:** `travel-blocks/cta-banner` (Gutenberg nativo)
- **Post meta keys:** `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_url`, `cta_background_image`, `cta_background_color`
- **Clases CSS:** `.cta-banner`, `.cta-banner__inner`, `.cta-banner__content`, etc.

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- ⚠️ **Template variables:** Incompatibilidad CRÍTICA entre PHP y template
- ⚠️ **Método load_template():** Usa `extract()` con EXTR_SKIP
- ⚠️ **Post meta:** Puede estar en uso en producción

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Package/CTABanner.php`
**Namespace:** `Travel\Blocks\Blocks\Package`
**Template:** `/wp-content/plugins/travel-blocks/templates/cta-banner.php`
**Assets:**
- CSS: `/assets/blocks/cta-banner.css`
- JS: `/assets/blocks/cta-banner.js` (prácticamente vacío)

**Tipo:** [ ] ACF  [X] Gutenberg Nativo

---

## 2. Propósito y Funcionalidad

**Descripción:** Banner call-to-action con fondo de imagen/color, título, subtítulo y botones para incentivar acciones del usuario.

**Inputs:**
- Post meta:
  - `cta_title`: Título del CTA
  - `cta_subtitle`: Subtítulo opcional
  - `cta_button_text`: Texto del botón
  - `cta_button_url`: URL del botón
  - `cta_background_image`: ID de imagen de fondo
  - `cta_background_color`: Color de fondo (fallback)

**Outputs:**
- HTML del banner con estilos inline
- Fallback a featured image si no hay background_image

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: Ninguna (⚠️ debería extender BlockBase)
- Implementa: Ninguna
- Traits: Ninguno

**Métodos Públicos:**
```
1. register(): void - Registra bloque y hook de assets
2. enqueue_assets(): void - Encola CSS/JS (solo frontend)
3. render($attributes, $content, $block): string
```

**Métodos Privados:**
```
1. get_preview_data(): array - Datos de preview
2. get_post_data(int $post_id): array - Obtiene datos de post meta
3. load_template(string $template_name, array $data = []): void
```

**Líneas de Código:**
- Total clase: 124 líneas
- get_post_data(): 40 líneas (71-111) ⚠️ LARGO
- load_template(): 10 líneas
- render(): 22 líneas

---

## 4. Registro del Bloque

**Método:** `register_block_type` (Gutenberg nativo)

**Configuración:**
- name: `travel-blocks/cta-banner`
- category: `travel`
- icon: `megaphone`
- keywords: cta, banner, call-to-action, button
- supports: anchor=true, html=false
- api_version: 2

**Fields:** No usa ACF - lee de post meta directamente

---

## 5. Campos ACF

**Definición:** [ ] JSON  [ ] Inline  [X] No usa ACF

**⚠️ PROBLEMA:** Bloque espera post meta pero NO registra los campos. Posiblemente:
- Los campos están registrados en otro lugar
- Se esperaba usar ACF pero no se implementó
- Es un bloque legacy en transición

---

## 6. Flujo de Renderizado

**Flujo Normal:**
1. `render()` detecta si es preview con `EditorHelper::is_editor_mode()`
2. Si preview → `get_preview_data()`
3. Si post → `get_post_data($post_id)` (lee 6 post metas)
4. Si no hay título → return '' (bloque vacío)
5. Prepara datos con block_id único y className
6. `load_template()` carga template con `extract()` + `include`
7. Retorna HTML capturado con ob_start()

**Error Handling:** ✅ Try-catch con mensaje en WP_DEBUG

---

## 7. Funcionalidades Adicionales

**AJAX:** No

**JavaScript:** ✅ SÍ - Pero prácticamente vacío
- Archivo: `cta-banner.js`
- Solo inicializa y marca como initialized
- No hace nada funcional

**Hooks Propios:** No define

**Dependencias Externas:**
- `EditorHelper::is_editor_mode()` (helper interno)
- `IconHelper::get_icon_svg()` (usado en template)

---

## 8. Análisis de Problemas

### 🚨 8.1 BUG CRÍTICO: Incompatibilidad PHP ↔ Template

**Severidad:** CRÍTICA - El bloque NO puede funcionar correctamente

**Problema:**
- **PHP envía:** `$data['banner']` con array de datos
- **Template espera:** Variables individuales `$cta_title`, `$cta_subtitle`, etc.

**Evidencia:**
```php
// CTABanner.php línea 47
$data = [
    'banner' => $banner_data,  // ❌ Envía nested array
];

// cta-banner.php línea 39
<h2><?php echo esc_html($cta_title); ?></h2>  // ❌ Variable inexistente
```

**Consecuencia:**
- Template no puede acceder a datos
- Variables undefined
- Bloque se renderiza vacío o con errores PHP

**Solución:**
- Opción A: Enviar datos "flat" en lugar de nested
- Opción B: Template acceder a `$banner['title']`
- Opción C: Usar extract() correctamente

### 8.2 Violaciones SOLID

**SRP:** ❌ **VIOLA MODERADAMENTE**
- Clase hace: registro, renderizado, carga de assets, manejo de template
- Ubicación: Toda la clase
- Impacto: MEDIO - Responsabilidades mezcladas
- **Comparación:** Peor que bloques ACF que extienden BlockBase

**OCP:** ❌ **VIOLA**
- No extensible - propiedades private, no usa interfaces
- Ubicación: Líneas 8-10
- Impacto: MEDIO

**DIP:** ⚠️ **VIOLA**
- Dependencias directas sin abstracción:
  - `get_post_meta()` hardcodeado
  - `wp_get_attachment_image_url()` directo
  - `get_the_post_thumbnail_url()` directo
- Ubicación: Método get_post_data()
- Impacto: MEDIO - No testeable sin WordPress

### 8.3 Problemas Clean Code

**Complejidad:**
- ⚠️ **get_post_data(): 40 líneas** (71-111)
  - Impacto: MEDIO
  - Debería dividirse en métodos:
    - `get_cta_meta_fields()`
    - `get_background_image()`
    - `get_fallback_values()`

**Anidación:**
- ✅ Anidación aceptable (<3 niveles)
- Template tiene buen nivel de anidación

**Duplicación:**
- ⚠️ **get_post_meta() llamado 6 veces** (líneas 74-109)
  - Podría optimizarse con array de campos + loop

**Código Sin Uso:**
- ⚠️ **JavaScript vacío** - archivo cargado innecesariamente
  - Solo hace flag de initialized
  - No tiene lógica funcional
  - Desperdicia request HTTP

**Métodos muy largos:**
- get_post_data(): 40 líneas (límite sugerido: 30)

### 8.4 Problemas de Seguridad

**Sanitización:**
- ❌ **className NO sanitizado**
  ```php
  // Línea 46 - RIESGO XSS
  'class_name' => 'cta-banner' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
  ```
  - Debería usar: `sanitize_html_class()`
  - Impacto: MEDIO - XSS posible vía atributo de bloque

- ✅ Post meta: WordPress lo sanitiza al guardarlo
- ❌ **Post ID no validado** (línea 38)
  - No verifica si $post_id es válido antes de usarlo

**Escapado:**
- ✅ **Template EXCELENTE** - Usa correctamente:
  - `esc_attr()` para atributos HTML
  - `esc_html()` para contenido de texto
  - `esc_url()` para URLs
  - Líneas 28-58 del template

**Nonces:**
- ✅ No aplica (no hay formularios)

**Validación:**
- ⚠️ **Validación mínima**
  - Solo verifica si title está vacío (línea 42)
  - No valida tipos de datos
  - No valida formato de URLs

**SQL:**
- ✅ No usa queries directas (usa get_post_meta)

### 8.5 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace inconsistente**
  - Actual: `Travel\Blocks\Blocks\Package`
  - Debería ser: `Travel\Blocks\Package` (sin doble "Blocks")
  - Comparar con ACF: `Travel\Blocks\Blocks\ACF` (también incorrecto)

**Separación MVC:**
- ✅ Template separado (buen patrón)
- ⚠️ Pero usa `extract()` que puede causar conflictos

**Acoplamiento:**
- ❌ **Alto acoplamiento con WordPress**
  - No hay capa de abstracción
  - Dificulta testing unitario

**Inconsistencias:**
- ❌ **No usa BlockBase** (otros bloques ACF sí)
  - Debería extender clase base para consistencia
  - Duplica lógica de load_template()

**Patrones:**
- ⚠️ **extract() con EXTR_SKIP** (línea 120)
  - Puede causar conflictos de variables
  - Dificulta debugging
  - Anti-patrón en código moderno

### 8.6 Problemas Específicos del Template

**Variables incompatibles:**
Template espera variables que NO existen:
- `$cta_title` → Debería ser `$banner['title']`
- `$cta_subtitle` → Debería ser `$banner['subtitle']`
- `$cta_description` → NO existe en PHP (⚠️ variable fantasma)
- `$primary_button_text` → Debería ser `$banner['button_text']`
- `$primary_button_url` → Debería ser `$banner['button_url']`
- `$primary_button_icon` → NO existe en PHP (⚠️ variable fantasma)
- `$show_secondary_button` → NO existe en PHP (⚠️ variable fantasma)
- `$secondary_button_text` → NO existe en PHP
- `$secondary_button_url` → NO existe en PHP
- `$banner_style` → NO existe en PHP
- `$background_styles` → NO existe en PHP
- `$text_color` → NO existe en PHP
- `$content_alignment` → NO existe en PHP

**Consecuencia:**
- ⚠️ La mayoría del template NO funcionará
- Variables undefined
- Funcionalidades no implementadas (botón secundario, iconos, estilos)

---

## 9. Comparación con Mejores Prácticas

**Comparado con ContactForm (ACF):**
- ❌ No extiende BlockBase
- ❌ No usa ACF (pero debería para consistencia)
- ❌ Peor manejo de errores
- ✅ Similar seguridad en template
- ❌ BUG crítico de variables

**Comparado con Breadcrumb (ACF):**
- ❌ No extiende BlockBase
- ✅ Similar estructura de métodos
- ⚠️ Menos funcionalidad

**Diferencias clave:**
- Package blocks no usan ACF
- Package blocks no extienden BlockBase
- Arquitectura inconsistente con resto del proyecto

---

## 10. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**⛔ BLOQUE TIENE BUG CRÍTICO - Arreglar antes de usar en producción**

### Prioridad CRÍTICA

**1. 🚨 ARREGLAR incompatibilidad PHP ↔ Template**
- **Acción:** Opción A (recomendada):
  ```php
  $data = [
      'block_id' => 'cta-banner-' . uniqid(),
      'class_name' => 'cta-banner ' . sanitize_html_class($attributes['className'] ?? ''),
      'cta_title' => $banner_data['title'],
      'cta_subtitle' => $banner_data['subtitle'],
      'primary_button_text' => $banner_data['button_text'],
      'primary_button_url' => $banner_data['button_url'],
      'background_image' => $banner_data['background_image'],
      'background_color' => $banner_data['background_color'],
      'is_preview' => $is_preview,
  ];
  ```
- **Razón:** BUG CRÍTICO - bloque no funciona
- **Riesgo:** BAJO - Solo reorganiza datos existentes
- **Esfuerzo:** 30 min
- **Testing:** ⚠️ Verificar que template se renderiza correctamente

**2. Sanitizar className**
- **Acción:**
  ```php
  $class_name = 'cta-banner';
  if (!empty($attributes['className'])) {
      $class_name .= ' ' . sanitize_html_class($attributes['className']);
  }
  ```
- **Razón:** Seguridad - prevenir XSS
- **Riesgo:** BAJO
- **Esfuerzo:** 5 min

**3. Validar post_id**
- **Acción:**
  ```php
  $post_id = get_the_ID();
  if (!$post_id || !is_numeric($post_id)) {
      return $is_preview ? $this->get_preview_data() : '';
  }
  ```
- **Razón:** Prevenir errores con post_id inválido
- **Riesgo:** BAJO
- **Esfuerzo:** 5 min

### Prioridad Alta

**4. Decidir: ¿Eliminar JavaScript vacío o implementarlo?**
- **Opción A:** Eliminar archivo (recomendado)
  - Archivo no hace nada útil
  - Ahorra request HTTP
  - Limpia código
- **Opción B:** Implementar funcionalidad real
  - Solo si se necesita interactividad
- **Razón:** Performance - no cargar JS innecesario
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min (eliminar) o X horas (implementar)

**5. Dividir get_post_data()**
- **Acción:** Extraer a métodos:
  ```php
  private function get_cta_meta_fields(int $post_id): array
  private function get_background_image_url(int $post_id): string
  private function apply_fallback_values(array $data): array
  ```
- **Razón:** KISS - Método muy largo (40 líneas)
- **Riesgo:** BAJO
- **Esfuerzo:** 1h

**6. Implementar o documentar variables faltantes del template**
- **Decisión:** ¿Se necesitan estas features?
  - Botón secundario
  - Iconos en botones
  - Campo description
  - Estilos de banner (split, overlay)
  - Alineación de contenido
  - Background styles inline
- **Acción A:** Eliminar del template si no se usan
- **Acción B:** Implementar en PHP si se necesitan
- **Razón:** Claridad - template actual confunde
- **Riesgo:** MEDIO
- **Esfuerzo:** 2-4h (si se implementa)

### Prioridad Media

**7. Extender BlockBase**
- **Acción:**
  ```php
  class CTABanner extends BlockBase
  ```
- **Razón:** Consistencia con bloques ACF, reutilizar load_template()
- **Riesgo:** BAJO
- **Precauciones:** Verificar que BlockBase existe
- **Esfuerzo:** 30 min

**8. Optimizar get_post_meta() calls**
- **Acción:**
  ```php
  $meta_keys = ['cta_title', 'cta_subtitle', ...];
  $meta_data = array_map(fn($key) => get_post_meta($post_id, $key, true),
                         array_flip($meta_keys));
  ```
- **Razón:** DRY - reducir duplicación
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

**9. Corregir Namespace**
- **Acción:** Cambiar a `Travel\Blocks\Package`
- **Razón:** PSR-4, consistencia
- **Riesgo:** MEDIO
- **Precauciones:**
  - Actualizar autoload
  - Buscar usos del namespace
- **Esfuerzo:** 30 min

**10. Agregar type hints estrictos**
- **Acción:**
  ```php
  private function get_post_data(int $post_id): array
  protected function load_template(string $template_name, array $data = []): void
  ```
- **Razón:** Type safety (ya está parcialmente implementado)
- **Riesgo:** BAJO
- **Esfuerzo:** 15 min

### Prioridad Baja

**11. Migrar a ACF para consistencia**
- **Acción:** Crear ACF field group para CTA settings
- **Razón:** Consistencia con resto de bloques
- **Riesgo:** ALTO - Requiere migración de datos
- **Precauciones:**
  - ⛔ NO hacer si hay datos en producción
  - Migración de post meta a ACF fields
- **Esfuerzo:** 3-4h

**12. Agregar validación de URL**
- **Acción:**
  ```php
  if ($cta_button_url && !filter_var($cta_button_url, FILTER_VALIDATE_URL)) {
      $cta_button_url = '#';
  }
  ```
- **Razón:** Seguridad adicional
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min

**13. Reemplazar extract() con acceso directo**
- **Acción:**
  ```php
  // En template: usar $data['key'] en lugar de $key
  ```
- **Razón:** Mejores prácticas modernas
- **Riesgo:** MEDIO - requiere actualizar template
- **Esfuerzo:** 1h

---

## 11. Plan de Acción

**Orden Recomendado:**

**FASE 1: Arreglar BUG crítico (1h)**
1. Arreglar incompatibilidad PHP ↔ Template
2. Sanitizar className
3. Validar post_id
4. Testing completo del bloque

**FASE 2: Cleanup (1.5h)**
5. Decidir sobre JavaScript (eliminar recomendado)
6. Dividir get_post_data()
7. Corregir namespace

**FASE 3: Arquitectura (2h)**
8. Extender BlockBase
9. Optimizar get_post_meta() calls
10. Decidir sobre variables faltantes del template

**FASE 4: Mejoras opcionales (4h)**
11. Implementar features faltantes (si se necesitan)
12. Migrar a ACF (solo si no hay datos en prod)
13. Otras mejoras de baja prioridad

**Precauciones CRÍTICAS:**
- ⛔ PROBAR después de arreglar bug de variables
- ⛔ NO migrar a ACF si hay post meta en producción
- ⚠️ Verificar que no hay otros bloques usando este como referencia
- ✅ Testing: Renderizar en preview y en post real
- ✅ Testing: Verificar todas las variables en template

---

## 12. Checklist Post-Refactorización

### Funcionalidad CRÍTICA
- [ ] Bloque se muestra en editor sin errores
- [ ] Preview muestra datos correctos
- [ ] Post real carga datos de post meta
- [ ] Título se muestra correctamente
- [ ] Subtítulo se muestra (si existe)
- [ ] Botón tiene texto correcto
- [ ] Botón tiene URL correcta
- [ ] Background image funciona
- [ ] Fallback a featured image funciona
- [ ] Background color funciona como fallback
- [ ] No hay PHP warnings/notices
- [ ] No hay errores en consola JS

### Seguridad
- [ ] className está sanitizado (test con código malicioso)
- [ ] Template escapa todas las salidas
- [ ] URLs son válidas y escapadas
- [ ] No hay XSS posible

### Performance
- [ ] JavaScript eliminado o implementado correctamente
- [ ] CSS se carga solo en frontend
- [ ] No hay queries N+1
- [ ] Assets se cargan con versión correcta

### Arquitectura
- [ ] Extiende BlockBase (si se implementó)
- [ ] Métodos <30 líneas
- [ ] Namespace correcto
- [ ] Variables de template coinciden con PHP

---

## 📊 Resumen Ejecutivo

### Estado Actual

**🚨 BLOQUE NO FUNCIONAL - BUG CRÍTICO**

- ❌ **BUG CRÍTICO:** Incompatibilidad total entre PHP y template
- ❌ **XSS:** className no sanitizado
- ⚠️ **JavaScript vacío:** Carga archivo innecesariamente
- ⚠️ **Arquitectura:** No extiende BlockBase (inconsistente)
- ⚠️ **Método largo:** get_post_data() (40 líneas)
- ✅ **Template:** Buen escapado de salidas
- ✅ **Error handling:** Try-catch implementado

### Puntuación: 3.5/10

**⚠️ NOTA:** Puntuación baja por BUG crítico que impide funcionamiento

**Fortalezas:**
1. Template bien escapado (esc_attr, esc_html, esc_url)
2. Error handling con try-catch
3. Separación de template y lógica
4. CSS bien estructurado con variables
5. Código relativamente limpio (excepto bug)

**Debilidades CRÍTICAS:**
1. 🚨 BUG: Variables de template no coinciden con datos enviados
2. ❌ XSS: className no sanitizado
3. ⚠️ JavaScript vacío cargado innecesariamente
4. ❌ Template espera 13+ variables que no existen
5. ❌ No extiende BlockBase (inconsistencia arquitectónica)

**Debilidades MODERADAS:**
6. ⚠️ Método get_post_data() muy largo (40 líneas)
7. ⚠️ Namespace incorrecto (doble "Blocks")
8. ⚠️ No valida post_id
9. ⚠️ Usa extract() (anti-patrón)
10. ⚠️ 6 llamadas a get_post_meta() sin optimizar

**Comparación con ContactForm (6.5/10):**
- ContactForm: Funciona pero tiene métodos gigantes
- CTABanner: Código más limpio pero NO funciona por bug

**Recomendación:**
1. **CRÍTICO:** Arreglar bug de variables ANTES de usar en producción
2. **URGENTE:** Sanitizar className (XSS)
3. **ALTO:** Decidir sobre JavaScript vacío
4. **MEDIO:** Refactorizar get_post_data()
5. **BAJO:** Extender BlockBase para consistencia

**Impacto del BUG:**
- El bloque actualmente NO puede renderizarse correctamente
- Probablemente nadie lo está usando (o hay errores silenciosos)
- Debe arreglarse antes de documentar o promover su uso

**Estimación de refactorización total:** 5-8 horas
- FASE 1 (arreglar bug): 1h ⚠️ CRÍTICO
- FASE 2 (cleanup): 1.5h
- FASE 3 (arquitectura): 2h
- FASE 4 (opcional): 4h

---

## 📈 Métricas de Código

**Líneas Totales:** 124 líneas (PHP)

**Métodos por tamaño:**
1. get_post_data(): 40 líneas ⚠️ LARGO
2. render(): 22 líneas ✅ BIEN
3. load_template(): 10 líneas ✅ BIEN
4. get_preview_data(): 9 líneas ✅ BIEN
5. enqueue_assets(): 5 líneas ✅ BIEN
6. register(): 12 líneas ✅ BIEN

**Complejidad Ciclomática Estimada:**
- get_post_data(): ~8 (múltiples ifs, fallbacks)
- render(): ~4
- load_template(): ~3

**Cobertura de Tests:** 0% (no hay tests)

**Deuda Técnica Estimada:** ALTA
- BUG crítico: 1h
- Refactoring completo: 5-8h
- Total: 6-9h

---

**Auditoría completada:** 2025-11-09
**Refactorización:** ⚠️ CRÍTICA - ARREGLAR BUG antes de usar en producción
**Prioridad:** ALTA - Bloque actualmente no funcional
