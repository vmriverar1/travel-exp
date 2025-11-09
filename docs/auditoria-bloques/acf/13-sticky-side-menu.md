# Auditoría: StickySideMenu (ACF)

**Fecha:** 2025-11-09
**Bloque:** 13/15 ACF
**Tiempo:** 40 min

---

## 🚨 PRECAUCIONES CRÍTICAS

### ⛔ NUNCA CAMBIAR
- **Block name:** `sticky-side-menu`
- **Namespace:** `acf/sticky-side-menu`
- **Campos ACF:** `show_phone`, `phone_number`, `show_cta`, `cta_url`, `show_hamburger`, `offset_value`, etc.
- **JavaScript:** `sticky-side-menu.js` - maneja sticky behavior y hamburger menu trigger
- **Clases CSS:** `.sticky-side-menu`, `.is-visible` - usadas en JavaScript para sticky behavior
- **Hamburger Menu Integration:** Integra con aside menu del header (#aside-menu)

### ⚠️ VERIFICAR ANTES DE MODIFICAR
- **Sticky behavior:** JavaScript calcula offset dinámico (vh, px, %) - NO cambiar lógica
- **Hamburger trigger:** Busca función global `window.asideMenuToggle` - NO romper integración
- **CSS Variables:** `--offset-top`, `--shadow-blur`, `--shadow-alpha` - usadas por JS
- **Responsive:** Hide mobile feature (campo `hide_mobile`)

---

## 1. Información General

**Ubicación:** `/wp-content/plugins/travel-blocks/src/Blocks/ACF/StickySideMenu.php`
**Namespace:** `Travel\Blocks\Blocks\ACF`
**Template:** `/wp-content/plugins/travel-blocks/templates/sticky-side-menu.php`

**Assets:**
- CSS: `/assets/blocks/sticky-side-menu.css`
- JS: `/assets/blocks/sticky-side-menu.js` (CRÍTICO - sticky behavior + hamburger trigger)

**Tipo:** [X] ACF  [ ] Gutenberg Nativo  [ ] Usa InnerBlocks

---

## 2. Propósito y Funcionalidad

**Descripción:** Menú lateral sticky que flota a la derecha de la pantalla. Aparece después de un scroll offset configurable. Contiene: teléfono, botón CTA y hamburguesa para abrir menú aside.

**Inputs (ACF):**
- **Teléfono:** `show_phone`, `phone_number`, `phone_icon`
- **Botón CTA:** `show_cta`, `cta_text`, `cta_url`, `cta_style` (6 variantes de color)
- **Hamburguesa:** `show_hamburger` (abre aside menu del header)
- **Posicionamiento:** `offset_value`, `offset_unit` (vh/px/%)
- **Estilos:** `shadow_intensity` (1-10), `hide_mobile`

**Outputs:**
- Menú fixed flotante a la derecha (top: 0, right: 0)
- Aparece con animación slide-in cuando scroll > offset
- Integración con menú aside del header (hamburguesa)
- Responsive: Oculto en mobile si `hide_mobile` = true

---

## 3. Estructura de la Clase

**Herencia:**
- Extiende: `BlockBase` ✅
- Implementa: Ninguna
- Traits: Ninguno

**Propiedades:**
```php
protected $name = 'sticky-side-menu'
protected $title, $description, $category, $icon, $keywords
protected $mode = 'preview'
protected $supports (align=false, mode, multiple, anchor)
```

**Métodos Públicos:**
```
1. __construct(): void - Define propiedades del bloque
2. enqueue_assets(): void - Encola CSS y JS
3. register(): void - Registra bloque (hereda de BlockBase) + campos ACF
4. render($block, $content, $is_preview, $post_id): void - Renderiza bloque
```

**Métodos Privados:**
```
1. get_menu_choices(): array - Obtiene menús WP (NO USADO en el bloque actual)
```

---

## 4. Registro del Bloque

**Método:** Hereda `parent::register()` de `BlockBase` (línea 68)

**Configuración:**
- name: `sticky-side-menu`
- category: `travel`
- icon: `menu-alt`
- keywords: ['sticky', 'side', 'menu', 'hamburger', 'cta', 'phone']
- supports: align=false (no necesita alineación, siempre flotante), anchor, multiple

**Block.json:** No existe

---

## 5. Campos ACF

**Definición:** [X] PHP inline (295 líneas de campos)

**Grupo:** `group_block_sticky_side_menu`

**Estructura por Tabs:**
1. **📞 Teléfono:** show_phone, phone_number, phone_icon
2. **🔘 Botón CTA:** show_cta, cta_text, cta_url, cta_style (6 opciones)
3. **🍔 Menú Hamburguesa:** show_hamburger
4. **📍 Posicionamiento:** offset_value (0-1000), offset_unit (vh/px/%)
5. **🎨 Estilos:** shadow_intensity (1-10), hide_mobile

**Campos Complejos:**
- `cta_style`: 6 variantes (primary, secondary, white, gold, dark, transparent)
- `offset_unit`: select con 3 opciones (vh, px, %)
- `shadow_intensity`: range 1-10
- Condicionales: phone_number y phone_icon solo si show_phone = true

---

## 6. Flujo de Renderizado

**Método de Preparación:** `render()`

**Obtención de Datos:**
1. ACF fields: show_phone, phone_number, cta_text, cta_url, cta_style, etc. (líneas 335-350)
2. Defaults: phone_number = '+51 999 999 999', cta_text = 'Contactar'
3. Block ID generation: 'ssm-' + $block['id']
4. Prepara array $data con 13 variables

**Procesamiento:**
- Sin procesamiento complejo
- Solo obtiene valores ACF y aplica defaults
- No hay lógica de negocio adicional

**Variables al Template:**
```php
- block_id, show_phone, phone_number, phone_icon
- show_cta, cta_text, cta_url, cta_style
- show_hamburger, menu_location (NO USADO)
- offset_value, offset_unit, shadow_intensity, hide_mobile
- is_preview
```

**Template Loading:**
- Usa `$this->load_template('sticky-side-menu', $data)` (heredado de BlockBase)
- Template en `/templates/sticky-side-menu.php`

---

## 7. Funcionalidades Adicionales

**AJAX:** No usa

**JavaScript:** ✅ SÍ - CRÍTICO
- Archivo: `sticky-side-menu.js` (140 líneas)
- **Sticky behavior:** Calcula offset dinámico y muestra/oculta menú con clase `.is-visible`
- **Hamburger trigger:** Busca `window.asideMenuToggle()` o manipula `#aside-menu` directamente
- **Throttle:** Usa `requestAnimationFrame` para performance
- **Editor preview:** Detecta editor y siempre muestra menú
- **Responsive:** Calcula offset en vh, px o % según configuración

**Dependencias Externas:**
- ⚠️ **Aside Menu del Header** - El hamburger trigger depende de:
  - `window.asideMenuToggle` (función global)
  - `#aside-menu` (elemento del header)
  - Clases: `.is-open`, `.no-scroll`, `.btn-hamburger`

**Integración Header:**
- Líneas 85-120 del JS: Fallback si no existe `window.asideMenuToggle`
- Manipula directamente `#aside-menu` y body classes
- ⚠️ CRÍTICO: NO cambiar lógica de integración

---

## 8. Análisis de Problemas

### 8.1 Violaciones SOLID

**SRP:** ✅ OK
- Responsabilidad clara: Menú sticky flotante
- Separación adecuada entre clase, template y JS

**OCP:** ✅ OK
- Extensible mediante herencia de BlockBase
- 6 estilos de botones configurables

**LSP:** ✅ OK
- Hereda correctamente de BlockBase

**ISP:** ✅ N/A - No usa interfaces

**DIP:** ✅ OK
- No tiene dependencias directas de otros bloques
- ⚠️ Depende de aside menu del header (pero es acoplamiento esperado)

### 8.2 Problemas Clean Code

**Complejidad:**
- ✅ **render(): 47 líneas** (329-376) - OK
- ✅ **register_fields(): 233 líneas** (66-306) - Largo pero aceptable para ACF fields
- ✅ Métodos pequeños y enfocados

**Anidación:**
- ✅ **Anidación baja** (1-2 niveles máximo)

**Duplicación:**
- ⚠️ **Estilos de botones duplicados**
  - 6 variantes de botones (primary, secondary, white, gold, dark, transparent)
  - Mismo sistema que PostsCarousel y otros bloques
  - Candidato para sistema de design tokens compartido

**Nombres:**
- ✅ Nombres descriptivos y claros
- ✅ Prefijo `ssm` en campos ACF (consistente)

**Código Sin Uso:**
- ❌ **get_menu_choices()** (líneas 314-324)
  - Método privado que obtiene menús WP
  - Campo `menu_location` definido (línea 345) pero NO usado en template
  - ⚠️ Probablemente feature incompleta (menú dentro del aside)
  - **Impacto:** BAJO - No causa errores, solo código muerto

### 8.3 Problemas de Seguridad

**Sanitización:**
- ✅ ACF fields sanitizados por ACF
- ✅ phone_number: tipo 'text' (ACF sanitiza)
- ✅ cta_url: tipo 'url' (ACF valida URLs)

**Escapado:**
- ⚠️ Template debe escapar outputs (verificar template)
- ⚠️ CSS variables (--offset-top) generadas desde ACF

**Nonces:**
- ✅ N/A - No tiene formularios

**Capabilities:**
- ✅ N/A - Es bloque de lectura

**SQL:**
- ✅ No usa queries directas

### 8.4 Problemas de Arquitectura

**Namespace/PSR-4:**
- ✅ **Namespace correcto:** `Travel\Blocks\Blocks\ACF`
- ✅ Ubicación: `/src/Blocks/ACF/StickySideMenu.php`

**Separación MVC:**
- ✅ Controller (clase) / View (template) bien separados
- ✅ JavaScript separado en archivo propio

**Acoplamiento:**
- ⚠️ **Acoplamiento con Header Aside Menu**
  - Depende de `window.asideMenuToggle` o `#aside-menu`
  - JavaScript tiene fallback si no existe
  - **Impacto:** MEDIO - Funcional pero acoplado a estructura del header
  - **Recomendación:** Documentar dependencia en header

**Herencia:**
- ✅ **Hereda correctamente de BlockBase**
- ✅ Usa `parent::register()` y `load_template()`

**Otros:**
- ⚠️ **Align support = false** - Correcto para menú flotante
- ⚠️ **Multiple support = true** - Permite múltiples instancias (¿necesario?)

---

## 9. Recomendaciones de Refactorización

### ⚠️ PRECAUCIÓN GENERAL
**BLOQUE FUNCIONAL Y BIEN ESTRUCTURADO. Refactorizaciones menores.**

### Prioridad Media

**1. Eliminar código sin uso: get_menu_choices() y menu_location**
- **Acción:** Eliminar método `get_menu_choices()` (líneas 314-324)
- **Acción:** Eliminar referencia a `menu_location` en render() (línea 345)
- **Razón:** Código muerto, feature incompleta
- **Riesgo:** BAJO - No se usa en ningún lado
- **Precauciones:**
  - ✅ Verificar que no se use en template
  - ✅ Eliminar solo si confirmamos que no es feature futura
- **Esfuerzo:** 10 min

**2. Consolidar sistema de estilos de botones**
- **Acción:** Crear config/helper compartido para estilos de botones
- **Razón:** Duplicación con PostsCarousel, TaxonomyTabs, etc.
- **Riesgo:** MEDIO - Requiere refactorización cross-bloque
- **Precauciones:**
  - ⛔ NO cambiar nombres de campos ACF
  - ✅ Mantener compatibilidad con bloques existentes
- **Esfuerzo:** 2-3h (si se hace consolidación general)

**3. Documentar dependencia con Header Aside Menu**
- **Acción:** Agregar comentarios en código sobre dependencia
- **Razón:** Acoplamiento no obvio con header
- **Riesgo:** BAJO - Solo documentación
- **Esfuerzo:** 15 min

### Prioridad Baja

**4. Evaluar necesidad de multiple support**
- **Acción:** ¿Es necesario permitir múltiples sticky menus?
- **Razón:** Un solo sticky menu por página es más común
- **Riesgo:** BAJO - Solo UX
- **Esfuerzo:** 5 min (decisión) + testing

**5. Crear block.json**
- **Acción:** Migrar configuración a block.json
- **Razón:** WordPress recomienda block.json
- **Riesgo:** BAJO
- **Esfuerzo:** 30 min

---

## 10. Plan de Acción

**Orden de Implementación:**
1. **PRIMERO:** Eliminar get_menu_choices() y menu_location (código muerto)
2. Documentar dependencia con Header Aside Menu
3. Evaluar multiple support
4. Consolidar sistema de estilos de botones (en refactorización general)
5. Crear block.json (opcional)

**Precauciones Generales:**
- ⛔ NO cambiar block name `sticky-side-menu`
- ⛔ NO cambiar nombres de campos ACF (field_ssm_*)
- ⛔ NO romper integración con aside menu del header
- ⛔ NO cambiar lógica de sticky behavior en JS
- ⛔ NO cambiar clases CSS usadas en JS (.sticky-side-menu, .is-visible)
- ✅ Testing: Sticky behavior, hamburger menu trigger, responsive, hide mobile
- ✅ Testing: Offset en vh/px/%, shadow intensity, estilos de botones

---

## 11. Checklist Post-Refactorización

### Funcionalidad CRÍTICA
- [ ] Bloque aparece en catálogo
- [ ] Menú aparece después de scroll offset configurado
- [ ] Sticky behavior funciona (fixed top: 0, right: 0)
- [ ] Animación slide-in funciona (.is-visible)
- [ ] Teléfono se muestra/oculta correctamente
- [ ] Botón CTA funciona (URL, texto, estilos)
- [ ] 6 estilos de botones funcionan correctamente
- [ ] Hamburguesa abre aside menu del header
- [ ] Integración con window.asideMenuToggle funciona
- [ ] Fallback de hamburger funciona si no existe función global
- [ ] Offset en vh funciona correctamente
- [ ] Offset en px funciona correctamente
- [ ] Offset en % funciona correctamente
- [ ] Shadow intensity funciona (CSS variable)
- [ ] Hide mobile funciona (display: none < 768px)
- [ ] Responsive comportamiento correcto
- [ ] Editor preview muestra menú siempre

### Arquitectura
- [ ] Hereda de BlockBase correctamente
- [ ] Namespace correcto
- [ ] Código sin uso eliminado (get_menu_choices)
- [ ] Dependencia con header documentada

### Seguridad
- [ ] Template escapa outputs correctamente
- [ ] URLs validadas por ACF

### Clean Code
- [ ] Sin código duplicado innecesario
- [ ] Sin código sin uso

---

## 📊 Resumen Ejecutivo

### Estado Actual
- ✅ Funciona correctamente
- ✅ Bien estructurado (hereda de BlockBase)
- ✅ JavaScript robusto con fallback
- ✅ Sticky behavior profesional
- ✅ Integración con header aside menu
- ⚠️ Código sin uso: get_menu_choices()
- ⚠️ Acoplamiento con header (esperado pero no documentado)
- ⚠️ Duplicación de estilos de botones (común a varios bloques)

### Puntuación: 8/10

**Fortalezas:**
- Bloque funcional y profesional
- JavaScript bien implementado (throttle, fallback, responsive)
- Sticky behavior dinámico (vh/px/%)
- Integración robusta con header aside menu
- Hereda correctamente de BlockBase
- Campos ACF bien organizados (tabs)
- 6 variantes de estilos de botones
- Responsive y mobile-friendly

**Debilidades:**
- Código sin uso: get_menu_choices() y menu_location
- Duplicación de estilos de botones con otros bloques
- Acoplamiento con header no documentado
- No tiene block.json (pero no es crítico)

**Recomendación:** **MANTENIMIENTO MENOR** - Bloque bien hecho, solo limpiar código sin uso y documentar dependencias. Considerar consolidación de estilos de botones en refactorización general.

---

**Auditoría completada:** 2025-11-09
**Refactorización:** **BAJA PRIORIDAD** - Solo limpieza menor
