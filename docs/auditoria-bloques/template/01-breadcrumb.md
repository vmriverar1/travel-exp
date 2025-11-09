# Auditoría: Breadcrumb (Template)

**Fecha:** 2025-11-09
**Bloque:** 1/X Template
**Tiempo:** 35 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `breadcrumb`
- **Namespace:** `travel/breadcrumb` (Template block)
- **Método público:** `get_package_breadcrumbs()` - puede ser usado por otros bloques
- **Clases CSS:** `.breadcrumb`, `.breadcrumb__list`, `.breadcrumb__item`, `.breadcrumb__link`

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- Herencia de `TemplateBlockBase` - es para usar en Query Loop
- Método `render_live()` recibe `$post_id` como parámetro

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/Template/Breadcrumb.php`
**Namespace:** `Travel\Blocks\Blocks\Template`
**Template:** `/wp-content/plugins/travel-blocks/templates/breadcrumb.php` (compartido con ACF)
**Assets:**
- CSS: `/assets/blocks/template/breadcrumb.css` (88 líneas)
- JS: No tiene

**Tipo:** [ ] ACF  [X] Gutenberg Nativo (Template Block)

---

## 2. Propósito y Funcionalidad

**Descripción:** Genera breadcrumb navigation específico para packages en Query Loop templates.

**Diferencia con ACF/Breadcrumb:**
- **Template/Breadcrumb:** Para usar en templates de packages (Query Loop), recibe post_id específico
- **ACF/Breadcrumb:** Para insertar en cualquier página, detecta contexto automáticamente

**Inputs:**
- Ninguno (sin campos ACF, bloque nativo)
- Recibe `$post_id` del contexto de Query Loop

**Outputs:**
- HTML semántico `<nav>` con breadcrumbs
- Estructura: Home > Tours > [Destinations jerárquicos] > Package Title

**Contextos soportados:**
- Solo packages individuales (no multicontexto como ACF)

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `TemplateBlockBase`
- Implementa: Ninguna
- Traits: `PreviewDataTrait`

**Propiedades:**
```
Heredadas de TemplateBlockBase (name, title, description, icon, keywords)
```

**Métodos Públicos:**
```
1. __construct(): void - Constructor, configura propiedades del bloque
2. render_preview(array $attributes): string - Renderiza preview con datos de ejemplo
3. render_live(int $post_id, array $attributes): string - Renderiza breadcrumb real
4. enqueue_assets(): void - Encola CSS del bloque
```

**Métodos Privados:**
```
1. get_preview_breadcrumbs(): array - Retorna breadcrumbs de ejemplo para preview
2. get_package_breadcrumbs(int $post_id): array - Genera breadcrumbs reales para package
```

---

## 4. Registro del Bloque

**Método:** Heredado de `TemplateBlockBase`

**Configuración:**
- name: `breadcrumb`
- title: "Breadcrumb Navigation"
- description: "Hierarchical breadcrumb navigation for packages"
- icon: `arrow-right-alt`
- keywords: ['breadcrumb', 'navigation', 'hierarchy', 'path']

**Block.json:** No existe

**Categoría:** Heredada de TemplateBlockBase (probablemente 'travel-template' o similar)

---

## 5. Campos ACF

**Definición:** No aplica - Es bloque Gutenberg nativo sin campos ACF

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render_preview()` o `render_live()` según contexto

**Obtención de Datos:**

**Preview:**
1. Llama `get_preview_breadcrumbs()` - retorna array hardcoded de ejemplo
2. Ejemplo: Home > Tours > Peru > Cusco > 4-Day Inca Trail Trek to Machu Picchu

**Live:**
1. Recibe `$post_id` del contexto de Query Loop
2. Llama `get_package_breadcrumbs($post_id)`
3. Construye breadcrumbs basado en:
   - Home (hardcoded)
   - Tours archive (desde post type object)
   - Destinations jerárquicos (taxonomía 'destination')
   - Package title (current page, sin link)

**Procesamiento:**
1. Detecta post type 'package' y su archive
2. Obtiene términos de taxonomía 'destination'
3. Si hay jerarquía, obtiene ancestors con `get_ancestors()`
4. Construye array con title + url

**Variables al Template:**
```php
- $breadcrumbs: array - Items del breadcrumb
- $is_preview: bool - Modo preview
```

**Lógica en Template:**
- Template esperaría `$data['breadcrumbs']` pero recibe `$items`
- Template compartido con ACF (estructura de datos debe ser compatible)
- ✅ Todo escapado correctamente

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:** No usa

**REST API:** No usa

**Hooks Propios:** No define

**Dependencias Externas:**
- `TemplateBlockBase` (core framework)
- `PreviewDataTrait` (core framework)

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ Cumple - Clase solo maneja breadcrumb para packages

**OCP:** ✅ Cumple - Puede extenderse sin modificar

**LSP:** ✅ Cumple - Respeta contrato de TemplateBlockBase

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ⚠️ **VIOLA** - Instancia directa de funciones globales WP sin abstracción
- Ubicación: get_package_breadcrumbs() (líneas 71-125)
- Impacto: Medio (dificulta testing)
- Funciones: get_post_type_object, wp_get_post_terms, get_ancestors, get_term, get_term_link

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ Métodos cortos: Todos <60 líneas
- ✅ get_package_breadcrumbs() tiene 55 líneas (71-125) - Aceptable
- ✅ Lógica clara y fácil de seguir

**Anidación:**
- ✅ Máximo 3 niveles - Aceptable
- Líneas 96-109: Anidación de 3 niveles (if parent > foreach ancestors > if ancestor)

**Duplicación:**
- ⚠️ **Lógica de breadcrumbs duplicada con ACF/Breadcrumb**
  - Ubicación: get_package_breadcrumbs() vs ACF\Breadcrumb::get_breadcrumb_items()
  - Impacto: MEDIO - Ambos construyen breadcrumbs de packages similar
  - Diferencia: Template usa solo packages, ACF es multicontexto

**Nombres:**
- ✅ Nombres descriptivos y claros
- ✅ get_preview_breadcrumbs, get_package_breadcrumbs muy explícitos

**Código Sin Uso:**
- ✅ No se detectó código sin uso

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ `$post_id` es int (type hint)
- ✅ Funciones WP (get_post_type_object, wp_get_post_terms) ya sanitizan

**Escapado:**
- ✅ Template escapa todo correctamente (esc_attr, esc_url, esc_html)
- ✅ Data pasada al template es limpia (strings de WP)

**Nonces:**
- ✅ N/A - No tiene formularios ni AJAX

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas, solo funciones WP

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ⚠️ **Namespace incorrecto**
  - Actual: `Travel\Blocks\Blocks\Template`
  - Esperado: `Travel\Blocks\Template`
  - Ubicación: Línea 11
  - Impacto: Bajo (funciona pero no sigue convención PSR-4)
  - **NOTA:** Mismo problema que ACF/Breadcrumb

**Separación MVC:**
- ✅ Bien separado - Controller (clase) / View (template compartido)

**Acoplamiento:**
- ✅ Bajo acoplamiento - Usa TemplateBlockBase y PreviewDataTrait
- ⚠️ Acoplamiento con funciones globales WP (normal en WordPress)

**Otros:**
- ❌ **CRÍTICO: CSS NO COINCIDE CON TEMPLATE**
  - CSS define: `.breadcrumb-navigation`, `.breadcrumb-list`, `.breadcrumb-item`
  - Template usa: `.breadcrumb`, `.breadcrumb__list`, `.breadcrumb__item`
  - Ubicación: breadcrumb.css vs breadcrumb.php
  - Impacto: ALTO - CSS no se aplica al bloque
  - **El bloque Template está usando el CSS equivocado**

- ⚠️ **Comparte template con ACF/Breadcrumb**
  - Ambos usan: `/templates/breadcrumb.php`
  - Impacto: MEDIO - Cambios en template afectan ambos bloques
  - Ventaja: Consistencia visual
  - Desventaja: Variables deben ser compatibles

- ⚠️ **Sin block.json**
  - WordPress recomienda block.json para bloques nativos
  - Impacto: Bajo (funciona sin él)

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**Este bloque está en uso en producción como Template Block. NO cambiar block name ni métodos públicos.**

### Prioridad CRÍTICA

**1. ⚠️ ARREGLAR CSS - No coincide con template**
- **Acción:** Decidir estrategia:
  - Opción A: Cambiar clases en CSS para que coincidan con template (.breadcrumb, .breadcrumb__list)
  - Opción B: Crear CSS específico para este bloque
  - Opción C: Compartir CSS con ACF/Breadcrumb (si es intencional)
- **Razón:** CSS actual NO se aplica al bloque, está roto
- **Riesgo:** ALTO - El bloque no tiene estilos funcionando
- **Precauciones:**
  - Verificar si el CSS correcto está siendo encolado
  - Verificar que el template usa las clases correctas
  - Testing visual después del fix
- **Esfuerzo:** 30 min - 1h (dependiendo de opción elegida)

### Prioridad Alta

**2. Decidir: ¿Consolidar lógica con ACF/Breadcrumb?**
- **Acción:** Extraer lógica común a un servicio compartido:
  ```php
  class BreadcrumbService {
      public function get_package_breadcrumbs(int $post_id): array
      public function get_contextual_breadcrumbs(): array
  }
  ```
- **Razón:** Ambos bloques generan breadcrumbs de packages de forma similar
- **Diferencia:** Template/Breadcrumb es específico para packages, ACF es multicontexto
- **Riesgo:** MEDIO - Refactor arquitectural
- **Precauciones:**
  - Mantener ambos bloques funcionando
  - NO consolidar bloques (tienen propósitos diferentes)
  - Solo compartir lógica interna
- **Esfuerzo:** 2-3h

**3. Corregir Namespace**
- **Acción:** Cambiar de `Travel\Blocks\Blocks\Template` a `Travel\Blocks\Template`
- **Razón:** No sigue PSR-4, tiene `\Blocks\Blocks\`
- **Riesgo:** MEDIO - Requiere actualizar autoload
- **Precauciones:**
  - Actualizar composer.json si es necesario
  - Ejecutar `composer dump-autoload`
  - Verificar que bloque sigue registrándose
- **Esfuerzo:** 30 min

### Prioridad Media

**4. Crear block.json**
- **Acción:** Crear block.json con metadata del bloque
- **Razón:** WordPress recomienda block.json para bloques nativos
- **Riesgo:** BAJO
- **Precauciones:**
  - Mantener compatibilidad con registro PHP actual
  - Verificar que bloque sigue apareciendo en editor
- **Esfuerzo:** 1h

**5. Extraer lógica de destinations a método separado**
- **Acción:** Extraer líneas 90-116 a método privado:
  ```php
  private function get_destination_breadcrumbs(int $post_id): array
  ```
- **Razón:** Separar responsabilidad, facilitar testing
- **Riesgo:** BAJO - Es método privado
- **Precauciones:** Mantener output exacto
- **Esfuerzo:** 30 min

### Prioridad Baja

**6. Agregar filtros para extender breadcrumbs**
- **Acción:** Agregar hooks:
  ```php
  apply_filters('travel_blocks/template/breadcrumb/items', $breadcrumbs, $post_id)
  ```
- **Razón:** Permitir customización sin modificar código
- **Riesgo:** BAJO
- **Precauciones:** Documentar filtros
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **CRÍTICO:** Arreglar CSS - No coincide con template
2. **ALTO:** Decidir estrategia de servicio compartido con ACF/Breadcrumb
3. Corregir namespace
4. Crear block.json
5. Extraer lógica de destinations
6. Agregar filtros de extensión

**Precauciones Generales:**
- ⛔ NO cambiar block name `breadcrumb`
- ⛔ NO cambiar métodos públicos (render_preview, render_live, enqueue_assets)
- ⛔ NO cambiar estructura de datos pasada al template (compartido con ACF)
- ✅ Testing: Verificar en Query Loop de packages
- ✅ Testing: Verificar preview en editor
- ✅ Testing: Verificar estilos CSS después del fix

---

## 11. Checklist Post-Refactorización

### Funcionalidad
- [ ] Bloque aparece en inserter (Template blocks)
- [ ] Se puede insertar en Query Loop template
- [ ] Preview funciona en editor con datos de ejemplo
- [ ] Frontend funciona en páginas de package
- [ ] Breadcrumbs muestran jerarquía correcta (Home > Tours > Destinations > Package)
- [ ] Destinations jerárquicos se muestran en orden correcto
- [ ] CSS se aplica correctamente (después del fix)

### Arquitectura
- [ ] CSS coincide con clases del template (CRÍTICO)
- [ ] Namespace correcto (si se cambió)
- [ ] block.json creado (si se implementó)
- [ ] Servicio compartido con ACF (si se implementó)

### Seguridad
- [ ] Escapado en template (ya OK)
- [ ] Type hints correctos (ya OK)

### Clean Code
- [ ] Código claro y legible
- [ ] Métodos enfocados y pequeños
- [ ] Sin duplicación innecesaria

---

## 12. Comparación con ACF/Breadcrumb

### Similitudes
- ✅ Ambos generan breadcrumbs
- ✅ Comparten el mismo template PHP (`breadcrumb.php`)
- ✅ Estructura de datos similar (array de items con title, url)
- ✅ Lógica de packages similar (destinations jerárquicos)
- ✅ Mismo problema de namespace incorrecto

### Diferencias Críticas

| Aspecto | ACF/Breadcrumb | Template/Breadcrumb |
|---------|----------------|---------------------|
| **Propósito** | Bloque general multicontexto | Solo para packages en Query Loop |
| **Tipo** | ACF Block | Gutenberg Native (Template) |
| **Campos** | 3 campos ACF (show_home, separator, text_color) | Sin campos |
| **Contextos** | Singular, Archive, Search, 404, Packages | Solo Packages |
| **Herencia** | BlockBase | TemplateBlockBase + PreviewDataTrait |
| **CSS** | `/assets/blocks/breadcrumb.css` (133 líneas) | `/assets/blocks/template/breadcrumb.css` (88 líneas) |
| **Clases CSS** | `.breadcrumb`, `.breadcrumb__list` | ❌ CSS define `.breadcrumb-navigation` (roto) |
| **Código** | 286 líneas | 143 líneas |
| **Complejidad** | Método largo (105 líneas) | Métodos cortos (<60 líneas) |
| **Puntuación** | 7/10 | 8/10 (pero CSS roto baja a 6/10) |

### ¿Hay Duplicación?

**Respuesta:** NO hay duplicación funcional real, pero SÍ hay duplicación de lógica.

**Propósitos diferentes:**
- **ACF/Breadcrumb:** Para insertar manualmente en cualquier página, detecta contexto automático
- **Template/Breadcrumb:** Para usar en templates de Query Loop de packages

**Duplicación de lógica:**
- Ambos tienen código similar para generar breadcrumbs de packages
- Candidatos a compartir un servicio común `BreadcrumbService`

**Recomendación:**
1. **Mantener ambos bloques** - Tienen propósitos diferentes
2. **Extraer lógica común** a servicio compartido
3. **Arreglar CSS** de Template/Breadcrumb (CRÍTICO)
4. **Clarificar documentación** sobre cuándo usar cada uno

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ⚠️ **CSS ROTO** - No coincide con template (CRÍTICO)
- ✅ Código limpio y bien estructurado
- ✅ Separación MVC correcta
- ✅ Seguridad OK (escapado completo)
- ⚠️ Namespace incorrecto (mismo problema que ACF)
- ⚠️ Duplicación de lógica con ACF/Breadcrumb

### Puntuación: 6/10
**Nota:** Sería 8/10 pero CSS roto lo baja a 6/10

**Fortalezas:**
- Código muy limpio y corto (143 líneas)
- Métodos bien divididos (<60 líneas cada uno)
- Usa arquitectura correcta (TemplateBlockBase)
- Preview bien implementado
- Sin anidación excesiva

**Debilidades:**
- **CSS NO FUNCIONA** - Clases no coinciden (CRÍTICO)
- Namespace incorrecto (PSR-4)
- Duplicación de lógica con ACF/Breadcrumb
- Sin block.json (recomendado para bloques nativos)

**Recomendación:** Arreglar CSS URGENTE, luego extraer lógica común con ACF/Breadcrumb a servicio compartido.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** URGENTE (CSS roto) + Pendiente (servicio compartido)
