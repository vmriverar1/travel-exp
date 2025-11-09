# Auditoría: Breadcrumb (ACF)

**Fecha:** 2025-11-09
**Bloque:** 1/15 ACF
**Tiempo:** 40 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `breadcrumb`
- **Namespace:** `acf/breadcrumb` (ACF block)
- **Campos ACF:** `show_home`, `separator`, `text_color`
- **Clases CSS:** `.breadcrumb`, `.breadcrumb__list`, `.breadcrumb__item`, `.breadcrumb__link`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Método público `get_breadcrumb_items()` - puede ser usado externamente
- Lógica de detección de contexto (is_singular, is_archive, etc.)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/Breadcrumb.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/wp-content/plugins/travel-blocks/templates/breadcrumb.php`
**Assets:**
- CSS: `/assets/blocks/breadcrumb.css` (133 líneas)
- JS: No tiene

**Tipo:** [X] ACF  [ ] Gutenberg Nativo

---

## 2. Propósito y Funcionalidad

**Descripción:** Genera automáticamente migas de pan (breadcrumb) según el contexto de la página actual.

**Inputs (ACF):**
- `show_home` (true_false): Mostrar/ocultar enlace a inicio
- `separator` (select): Símbolo separador (>, /, →, », ·)
- `text_color` (select): Color del breadcrumb (default, primary, secondary, dark)

**Outputs:**
- HTML semántico `<nav>` con lista de navegación
- Ruta contextual automática basada en tipo de página

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `BlockBase`
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```
Heredadas de BlockBase (name, title, description, category, icon, keywords, mode, supports)
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor, configura propiedades del bloque
2. enqueue_assets(): void - Encola CSS del bloque
3. register(): void - Registra bloque ACF y campos
4. render($block, $content, $is_preview, $post_id): void - Renderiza el bloque
```

**Métodos Privados:**
```
1. get_breadcrumb_items($show_home): array - Genera items del breadcrumb según contexto
```

---

## 4. Registro del Bloque

**Método:** `acf_register_block_type` (heredado de BlockBase)

**Configuración:**
- name: `breadcrumb`
- title: "Breadcrumb (Migas de Pan)"
- category: `travel`
- icon: `admin-home`
- keywords: ['breadcrumb', 'migas', 'navegacion', 'ruta']
- render_callback: `$this->render()`
- supports: align=false, mode=false, multiple=true, anchor=false

**Block.json:** No existe

---

## 5. Campos ACF

**Definición:** [X] PHP (acf_add_local_field_group)

**Grupo:** `group_block_breadcrumb`

**Campos:**
1. `show_home` (true_false)
   - Label: "Mostrar Inicio"
   - Default: 1 (true)
   - UI: Yes

2. `separator` (select)
   - Label: "Separador"
   - Choices: >, /, →, », ·
   - Default: >

3. `text_color` (select)
   - Label: "Color del Texto"
   - Choices: default, primary, secondary, dark
   - Default: default

**Condicionales:** No tiene

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. ACF fields: `get_field('show_home')`, `get_field('separator')`, `get_field('text_color')`
2. Breadcrumb items: `$this->get_breadcrumb_items($show_home)` - detecta contexto automáticamente

**Procesamiento:**
1. Detecta contexto (singular, archive, search, 404)
2. Construye array de items según contexto
3. Cada item tiene: title, url, current (bool)

**Variables al Template:**
```php
- $block_id: string - ID único del bloque
- $show_home: bool - Mostrar inicio
- $separator: string - Símbolo separador
- $text_color: string - Color variant
- $items: array - Items del breadcrumb
- $is_preview: bool - Modo preview
```

**Lógica en Template:**
- Simple: foreach de items, condicionales para current/link
- ✅ Bien separado MVC
- ✅ Todo escapado correctamente

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

**SRP:** ✅ Cumple - Clase solo maneja breadcrumb

**OCP:** ✅ Cumple - Puede extenderse sin modificar

**LSP:** ✅ Cumple - Respeta contrato de BlockBase

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA** - Instancia directa de funciones globales WP sin abstracción
- Ubicación: Líneas 159-263
- Impacto: Medio (dificulta testing)

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ Métodos <30 líneas: Todos OK
- ❌ **get_breadcrumb_items() tiene 105 líneas** (159-263)
  - Ubicación: Línea 159
  - Impacto: Alto - Método muy largo y complejo

**Anidación:**
- ⚠️ **Anidación de 4 niveles** en get_breadcrumb_items()
  - Ubicación: Líneas 203-219 (taxonomies loop)
  - Impacto: Medio

**Duplicación:**
- ⚠️ **Código duplicado con Template\Breadcrumb**
  - Ubicación: Dos bloques hacen lo mismo
  - Impacto: Alto - Mantenimiento doble

**Nombres:**
- ✅ Nombres descriptivos en general

**Código Sin Uso:**
- ✅ No se detectó código sin uso

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ get_field() con fallbacks seguros

**Escapado:**
- ✅ Template escapa todo correctamente (esc_attr, esc_url, esc_html)

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
  - Ubicación: Línea 12
  - Impacto: Bajo (funciona pero no sigue convención)

**Separación MVC:**
- ✅ Bien separado - Controller (clase) / View (template)

**Acoplamiento:**
- ⚠️ Alto acoplamiento con funciones globales WP
- ⚠️ Lógica compleja en método privado

**Otros:**
- ❌ **DUPLICACIÓN CRÍTICA: Existen 2 bloques Breadcrumb**
  - `ACF\Breadcrumb` (este)
  - `Template\Breadcrumb` (similar funcionalidad)
  - Impacto: ALTO - Confusión, mantenimiento doble

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque está en uso en producción. NO cambiar block name, ACF fields, ni clases CSS.**

### Prioridad Alta

**1. Decidir: ¿Consolidar o Mantener 2 Breadcrumbs?**
- **Acción:** Usuario debe decidir estrategia:
  - Opción A: Mantener ambos (ACF para páginas generales, Template para packages)
  - Opción B: Consolidar en uno solo
- **Razón:** Duplicación de código y funcionalidad
- **Riesgo:** Si se consolida, migrar contenido existente
- **Precauciones:** Verificar uso en DB de ambos bloques
- **Esfuerzo:** 4-6h (si se consolida)

**2. Refactorizar get_breadcrumb_items() - Dividir método largo**
- **Acción:** Extraer lógica por contexto a métodos separados:
  ```php
  private function get_singular_breadcrumbs()
  private function get_archive_breadcrumbs()
  private function get_search_breadcrumbs()
  private function get_404_breadcrumbs()
  ```
- **Razón:** Método de 105 líneas viola KISS, difícil de mantener
- **Riesgo:** BAJO - Es método privado, no afecta API pública
- **Precauciones:** Mantener output exacto (mismo array structure)
- **Esfuerzo:** 1h

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

**4. Reducir anidación en taxonomies loop**
- **Acción:** Early returns, guard clauses
- **Razón:** Anidación de 4 niveles dificulta lectura
- **Riesgo:** BAJO - Lógica interna
- **Precauciones:** Mantener lógica exacta
- **Esfuerzo:** 30 min

### Prioridad Baja

**5. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Precauciones:** Mantener registro ACF funcionando
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **PRIMERO:** Usuario decide estrategia de consolidación (ACF vs Template)
2. Refactorizar get_breadcrumb_items() (dividir en métodos)
3. Corregir namespace
4. Reducir anidación
5. Crear block.json (opcional)

**Precauciones Generales:**
- ⛔ NO cambiar block name `breadcrumb`
- ⛔ NO cambiar nombres de campos ACF
- ⛔ NO cambiar clases CSS en template
- ✅ Testing: Insertar bloque, configurar, verificar frontend en diferentes contextos
- ✅ Testing: Verificar en singular post, singular package, category, search, 404

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en catálogo (categoría "travel")
- [ ] Se puede insertar correctamente
- [ ] Campos ACF aparecen (show_home, separator, text_color)
- [ ] Preview funciona en editor
- [ ] Frontend funciona en página singular
- [ ] Frontend funciona en archivo de categoría
- [ ] Frontend funciona en archivo de custom post type
- [ ] Frontend funciona en búsqueda
- [ ] Frontend funciona en 404
- [ ] Separadores se muestran correctamente
- [ ] Colores variant funcionan

### Arquitectura
- [ ] Namespace correcto (si se cambió)
- [ ] Métodos <30 líneas (si se refactorizó)
- [ ] Anidación <3 niveles (si se refactorizó)
- [ ] No hay duplicación (si se consolidó con Template)

### Seguridad
- [ ] Escapado en template (ya OK)
- [ ] Sanitización de ACF (ya OK)

### Clean Code
- [ ] Código claro y legible
- [ ] Métodos pequeños y enfocados
- [ ] Sin duplicación

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funciona correctamente
- ✅ Seguridad OK (escapado completo)
- ✅ Separación MVC correcta
- ⚠️ Método muy largo (105 líneas)
- ⚠️ Duplicación con Template\Breadcrumb
- ⚠️ Namespace incorrecto

### Puntuación: 7/10

**Fortalezas:**
- Código funcional y seguro
- Template bien escapado
- Lógica de breadcrumb completa (muchos contextos)

**Debilidades:**
- Método get_breadcrumb_items() demasiado largo
- Duplicación crítica con otro bloque
- Namespace no sigue convención

**Recomendación:** Refactorizar método largo y decidir estrategia de consolidación.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** Pendiente (depende decisión usuario)
