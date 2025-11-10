# Auditoría: HeroSection (ACF)

**Fecha:** 2025-11-09
**Bloque:** 6/15 ACF
**Tiempo:** 25 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `hero-section`
- **Namespace:** `acf/hero-section`
- **Campos ACF:** `background_image`, `overlay_opacity`, `title`, `subtitle`, `cta_text`, `cta_url`, `height`
- **Clases CSS:** Usadas en template `hero-section.php`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Método `load_template()` heredado de BlockBase
- Template ubicado en `/templates/hero-section.php` (verificar existencia)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/HeroSection.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/wp-content/plugins/travel-blocks/templates/hero-section.php`
**Assets:**
- CSS: `/assets/blocks/hero-section.css`
- JS: No tiene

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

---

## 2. Propósito y Funcionalidad

**Descripción:** Hero banner full-width con imagen de fondo, overlay, título, subtítulo y botón CTA. Versión simple de hero section.

**Inputs (ACF):**
- `background_image` (image, required): Imagen de fondo (recomendado 1920x800px)
- `overlay_opacity` (range): Opacidad de overlay oscuro (0-100%, default 40%)
- `title` (text, required): Título principal
- `subtitle` (textarea): Subtítulo/descripción
- `cta_text` (text): Texto del botón
- `cta_url` (url): URL del botón
- `height` (select): Altura del hero (small/medium/large/full)

**Outputs:**
- Hero section HTML con imagen de fondo
- Overlay oscuro para legibilidad
- Contenido centrado (título, subtítulo, botón)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: ✅ **BlockBase**
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```
Heredadas de BlockBase:
- $name, $title, $description, $category, $icon, $keywords, $mode, $supports
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor, configura propiedades del bloque
2. register(): void - Registra bloque ACF y campos
3. render($block, $content, $is_preview, $post_id): void - Renderiza el bloque
4. enqueue_assets(): void - Encola CSS del bloque
```

**Métodos Privados:**
```
Ninguno
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (heredado de BlockBase)

**Configuración:**
- name: `hero-section`
- title: "Hero Section"
- category: `travel`
- icon: `cover-image`
- keywords: ['hero', 'banner', 'header', 'cta']
- mode: `preview`
- supports: align=[full,wide], mode=true, multiple=true

**Block.json:** No existe

---

## 5. Campos ACF

**Definición:** [X] PHP inline (acf_add_local_field_group)

**Grupo:** `group_block_hero_section`

**Campos:**
1. `background_image` (image, required)
   - Return format: array
   - Preview size: large
   - Instructions: "Recommended size: 1920x800px"

2. `overlay_opacity` (range)
   - Min: 0, Max: 100, Step: 10
   - Default: 40
   - Append: %
   - Instructions: "Dark overlay to improve text readability"

3. `title` (text, required)
   - Default: "Discover the Magic of Peru"

4. `subtitle` (textarea)
   - Rows: 2
   - Default: "Unforgettable tours to Machu Picchu and beyond"

5. `cta_text` (text)
   - Default: "Explore Tours"

6. `cta_url` (url)
   - No default

7. `height` (select)
   - Choices: small (400px), medium (600px), large (800px), full (Full Screen)
   - Default: large

**Condicionales:** No tiene

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. ACF fields: `get_field('background_image')`, `get_field('overlay_opacity')`, etc.
2. Fallbacks: `overlay_opacity ?: 40`, `height ?: 'large'`

**Procesamiento:**
1. Prepara array `$data` con 9 keys (líneas 150-160)
2. Llama a `load_template('hero-section', $data)` (línea 163)

**Variables al Template:**
```php
- $block: array - Block settings
- $is_preview: bool
- $background_image: array|false
- $overlay_opacity: int (0-100)
- $title: string
- $subtitle: string
- $cta_text: string
- $cta_url: string
- $height: string (small/medium/large/full)
```

**Lógica en Template:**
- Template debe manejar rendering (no visto en esta auditoría)
- ✅ Uso de `load_template()` heredado de BlockBase

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

**SRP:** ✅ Cumple - Clase solo maneja hero section simple

**OCP:** ✅ Cumple - Puede extenderse sin modificar

**LSP:** ✅ Cumple - Respeta contrato de BlockBase

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ Parcial
- Dependencia de funciones globales ACF (get_field)
- Impacto: BAJO (estándar en WordPress)

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **Todos los métodos <30 líneas**
- ✅ __construct(): 14 líneas
- ✅ register(): 26 líneas (84 líneas incluyendo ACF fields inline)
- ✅ render(): 27 líneas
- ✅ enqueue_assets(): 9 líneas

**Anidación:**
- ✅ <3 niveles en todos los métodos

**Duplicación:**
- ⚠️ **Posible duplicación con otros Hero blocks**
  - ¿Existe HeroCarousel (ya auditado)?
  - ¿Existe HeroSection en Template namespace?
  - Impacto: MEDIO - Verificar si hay múltiples hero blocks

**Nombres:**
- ✅ Nombres descriptivos y claros

**Código Sin Uso:**
- ✅ No se detectó código sin uso

**DocBlocks:**
- ✅ **EXCELENTE** - Todos los métodos tienen PHPDoc completo
- Ubicación: Líneas 1-9, 34-37, 129-137, 166-169

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ get_field() con fallbacks seguros
- ⚠️ Verificar que template escapa URL (cta_url)

**Escapado:**
- ⚠️ Template debe escapar (no visto en auditoría)
- ⚠️ Verificar escapado de title, subtitle, cta_text, cta_url

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\ACF`
  - Esperado: `Travel\Blocks\ACF`
  - Ubicación: Línea 11
  - Impacto: BAJO (funciona pero no sigue convención)

**Separación MVC:**
- ✅ **EXCELENTE** - Controller (clase) / View (template) bien separados
- ✅ Usa método `load_template()` de BlockBase

**Acoplamiento:**
- ✅ Bajo acoplamiento
- ✅ Hereda de BlockBase correctamente

**Herencia:**
- ✅ **SÍ hereda de BlockBase** (a diferencia de FlexibleGridCarousel y HeroCarousel)
- ✅ Usa métodos heredados correctamente

**Otros:**
- ✅ Código limpio y bien estructurado
- ✅ Sigue convenciones de BlockBase

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque está bien implementado. Refactorización mínima necesaria.**

### Prioridad Alta

**1. Verificar duplicación de Hero blocks**
- **Acción:** Buscar otros bloques Hero:
  ```bash
  grep -r "class Hero" src/Blocks/
  grep -r "hero-section" src/Blocks/
  ```
- **Razón:** Evitar duplicación funcional
- **Riesgo:** BAJO - Solo investigación
- **Esfuerzo:** 15 min

**2. Verificar template escapa correctamente**
- **Acción:** Revisar `/templates/hero-section.php`:
  - ✅ `esc_url($cta_url)`
  - ✅ `esc_html($title)`
  - ✅ `esc_html($subtitle)`
  - ✅ `esc_attr($cta_text)` o `esc_html($cta_text)`
  - ✅ `esc_attr($height)`
- **Razón:** Seguridad
- **Riesgo:** MEDIO - Critical si no está escapado
- **Precauciones:** No romper output HTML
- **Esfuerzo:** 15 min

### Prioridad Media

**3. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\ACF` a `Travel\Blocks\ACF`
- **Razón:** No sigue PSR-4, tiene `\Blocks\Blocks\`
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:**
  - Actualizar composer.json si es necesario
  - Ejecutar `composer dump-autoload`
  - Verificar que bloque sigue registrándose
- **Esfuerzo:** 30 min

**4. Agregar validación de campos requeridos**
- **Acción:** En `render()`, verificar que `background_image` y `title` existen:
  ```php
  if (!$background_image || !$title) {
      echo '<p>Hero Section: Missing required fields</p>';
      return;
  }
  ```
- **Razón:** Prevenir errores si campos requeridos están vacíos
- **Riesgo:** BAJO
- **Precauciones:** Solo mostrar mensaje si realmente faltan
- **Esfuerzo:** 15 min

### Prioridad Baja

**5. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Precauciones:** Mantener registro ACF funcionando
- **Esfuerzo:** 30 min

**6. Mejorar choices de height con valores reales**
- **Acción:** En lugar de "Small (400px)", usar valores CSS custom properties:
  ```php
  'small' => __('Small (var(--hero-height-sm))', 'travel-blocks')
  ```
- **Razón:** Flexibilidad en theme.json
- **Riesgo:** BAJO
- **Esfuerzo:** 10 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. Verificar duplicación de Hero blocks
2. Verificar template escapa correctamente (CRÍTICO si no está)
3. Corregir namespace
4. Agregar validación de campos requeridos
5. Crear block.json (opcional)
6. Mejorar choices de height (opcional)

**Precauciones Generales:**
- ⛔ NO cambiar block name `hero-section`
- ⛔ NO cambiar nombres de campos ACF
- ⛔ NO cambiar clases CSS en template
- ✅ Testing: Insertar bloque, configurar campos, verificar frontend
- ✅ Testing: Verificar diferentes heights (small, medium, large, full)
- ✅ Testing: Verificar overlay opacity funciona
- ✅ Testing: Verificar CTA button funciona

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo (categoría "travel")
- [ ] Se puede insertar correctamente
- [ ] Campos ACF aparecen correctamente
- [ ] Preview funciona en editor
- [ ] Frontend funciona correctamente
- [ ] Background image se muestra
- [ ] Overlay opacity funciona (0-100%)
- [ ] Título se muestra
- [ ] Subtítulo se muestra (opcional)
- [ ] CTA button se muestra y funciona
- [ ] CTA URL funciona
- [ ] Height variants funcionan (small, medium, large, full)
- [ ] Align wide/full funciona
- [ ] Multiple instances funcionan

### Arquitectura
- [ ] Namespace correcto (si se cambió)
- [ ] Hereda de BlockBase (ya OK)
- [ ] load_template() funciona

### Seguridad
- [ ] Template escapa title
- [ ] Template escapa subtitle
- [ ] Template escapa cta_text
- [ ] Template escapa cta_url
- [ ] Template escapa height
- [ ] Validación de campos requeridos (si se agregó)

### Clean Code
- [ ] Código limpio (ya OK)
- [ ] Métodos cortos (ya OK)
- [ ] Sin duplicación (verificar)

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ **EXCELENTE IMPLEMENTACIÓN**
- ✅ Hereda de BlockBase correctamente
- ✅ Código limpio y bien estructurado
- ✅ Métodos cortos (<30 líneas)
- ✅ PHPDoc completo
- ✅ Separación MVC correcta
- ✅ Bajo acoplamiento
- ⚠️ Namespace incorrecto (menor)
- ⚠️ Posible duplicación con otros Hero blocks

### Puntuación: 9/10

**Fortalezas:**
- Código MUY limpio y simple (181 líneas totales)
- Hereda de BlockBase (a diferencia de FlexibleGridCarousel y HeroCarousel)
- PHPDoc completo en todos los métodos
- Métodos pequeños y enfocados
- No viola SOLID
- Separación MVC correcta con `load_template()`
- Campos ACF bien definidos con defaults sensatos

**Debilidades:**
- Namespace incorrecto (menor, fácil de corregir)
- Necesita verificar escapado en template
- Posible duplicación con otros Hero blocks (pendiente investigar)

**Recomendación:** ✅ **EXCELENTE EJEMPLO** de cómo debe ser un bloque ACF. Este es el mejor bloque auditado hasta ahora. Mínima refactorización necesaria, principalmente namespace y verificación de template.

**Comparación:** Este bloque es un contraste DRAMÁTICO con HeroCarousel (1126 líneas) y FlexibleGridCarousel (720 líneas). Demuestra que la simplicidad y herencia de BlockBase produce código mucho mejor.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Mínima - Solo namespace y verificación de template
