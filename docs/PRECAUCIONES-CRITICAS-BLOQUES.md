# ⚠️ PRECAUCIONES CRÍTICAS: No Romper Bloques en Producción

## 🚨 Problema Principal

**WordPress NO guarda bloques como código**. Los bloques se guardan en la base de datos como:
- **HTML comments** con metadatos en `post_content`
- **Referencias por nombre** del bloque
- **Atributos serializados** en formato JSON

**Ejemplo de cómo se guarda un bloque en la DB:**
```html
<!-- wp:acf/hero-carousel {"id":"block_abc123","name":"acf/hero-carousel","data":{"field_123":"valor"},"align":"full"} -->
<div class="wp-block-acf-hero-carousel">...</div>
<!-- /wp:acf/hero-carousel -->
```

## ❌ CAMBIOS PROHIBIDOS (Rompen Producción)

### 1. ⛔ NUNCA Cambiar el Block Name

**Block Name**: El identificador único del bloque registrado en WordPress.

**Ubicación típica en código:**
```php
acf_register_block_type([
    'name' => 'hero-carousel',  // ← ESTE ES EL BLOCK NAME
    'title' => __('Hero Carousel', 'travel-blocks'),
    // ...
]);
```

**Cómo se guarda en DB:**
```html
<!-- wp:acf/hero-carousel -->
```

**❌ SI CAMBIAS ESTO:**
```php
'name' => 'carousel-hero',  // ← Cambio de nombre
```

**💥 RESULTADO:**
- WordPress no encuentra el bloque registrado
- Todos los bloques existentes se muestran como "Este bloque contiene contenido inesperado"
- El usuario ve un error en el editor
- El frontend puede mostrar HTML sin estilos o vacío

**✅ NUNCA CAMBIAR:**
- El `name` del bloque en `acf_register_block_type()`
- El `name` del bloque en `register_block_type()`
- El slug del bloque

**✅ SÍ PUEDES CAMBIAR:**
- El `title` (título visible en el editor)
- El `description`
- El `icon`
- La clase PHP (siempre que el callback siga funcionando)

---

### 2. ⛔ NUNCA Cambiar el Namespace del Bloque (ACF)

**Para bloques ACF:**
```php
acf_register_block_type([
    'name' => 'hero-carousel',
    // WordPress genera automáticamente: acf/hero-carousel
]);
```

**Cómo se guarda en DB:**
```html
<!-- wp:acf/hero-carousel -->
```

**❌ SI CAMBIAS a Gutenberg Nativo:**
```php
register_block_type('travel-blocks/hero-carousel', [...]);
// Genera: travel-blocks/hero-carousel
```

**💥 RESULTADO:**
- WordPress busca `acf/hero-carousel` pero ahora es `travel-blocks/hero-carousel`
- Todos los bloques ACF existentes dejan de funcionar
- Miles de bloques rotos en producción

**✅ REGLA:**
- **ACF blocks** SIEMPRE tienen namespace `acf/{name}`
- **Gutenberg blocks** tienen namespace `{plugin-namespace}/{name}`
- **NO convertir ACF a Gutenberg** sin migración de contenido

---

### 3. ⛔ CUIDADO al Cambiar Nombres de Campos ACF

**Campos ACF se guardan por `name` (key):**
```php
[
    'name' => 'hero_images',  // ← Este es el key
    'label' => 'Hero Images',
    'type' => 'gallery',
]
```

**Cómo se guarda en DB (post_content):**
```json
{
  "data": {
    "hero_images": ["123", "456", "789"],
    "hero_title": "Welcome"
  }
}
```

**❌ SI CAMBIAS EL NAME:**
```php
'name' => 'carousel_images',  // ← Cambio de nombre
```

**💥 RESULTADO:**
- El bloque busca `carousel_images` pero los datos están en `hero_images`
- El bloque no encuentra los datos guardados
- Se muestra vacío o con placeholders
- El usuario pierde el contenido configurado

**✅ REGLA:**
- **NUNCA cambiar** el `name` de campos ACF
- **SÍ puedes cambiar** el `label` (título visible)
- Si NECESITAS cambiar el name, crear migración de datos

---

### 4. ⛔ NUNCA Eliminar Dependencias Activas

**Librerías comúnmente usadas:**
- Swiper.js (carousels, sliders)
- Masonry (grids)
- Lightbox/Fancybox (galerías)
- Leaflet/Google Maps (mapas)

**Ejemplo en código:**
```php
public function enqueue_assets() {
    wp_enqueue_script(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
        [],
        '8.0.0',
        true
    );
}
```

**❌ SI ELIMINAS ESTO:**
```php
// Comentado o eliminado el enqueue de Swiper
```

**💥 RESULTADO:**
- JavaScript del bloque busca `new Swiper()` pero no existe
- Error en consola: `Uncaught ReferenceError: Swiper is not defined`
- El carousel no funciona, se muestra estático
- Puede romper todo el JavaScript de la página

**✅ REGLA:**
- **NUNCA eliminar** dependencias sin verificar que NO se usan
- Buscar en templates: `grep -r "Swiper" templates/`
- Buscar en JS: `grep -r "new Swiper" assets/`
- Si se usa, mantener o reemplazar con equivalente

---

### 5. ⛔ CUIDADO al Cambiar la Categoría del Bloque

**Categoría del bloque:**
```php
acf_register_block_type([
    'name' => 'hero-carousel',
    'category' => 'travel',  // ← Categoría personalizada
]);
```

**❌ SI CAMBIAS A:**
```php
'category' => 'widgets',  // ← Nueva categoría
```

**💥 RESULTADO (menos crítico pero molesto):**
- El bloque cambia de lugar en el inserter
- Los usuarios no lo encuentran donde esperan
- Puede causar confusión
- **NO rompe bloques existentes** (menos crítico)

**✅ REGLA:**
- Cambiar categoría es relativamente seguro
- Pero mantener consistencia ayuda a usuarios
- Documentar si cambias categoría

---

### 6. ⛔ NUNCA Cambiar el Render Callback Sin Cuidado

**Render callback:**
```php
acf_register_block_type([
    'name' => 'hero-carousel',
    'render_callback' => [$this, 'render'],  // ← Método que renderiza
]);
```

**❌ SI CAMBIAS MÉTODO O FIRMA:**
```php
// Antes
public function render($block, $content = '', $is_preview = false) {
    // ...
}

// Después (firma diferente)
public function render($block) {  // ← Faltan parámetros
    // Código que usa $is_preview... ERROR
}
```

**💥 RESULTADO:**
- PHP Fatal Error si el código usa parámetros eliminados
- El bloque no renderiza
- Página blanca o error 500

**✅ REGLA:**
- **Mantener firma** del método render
- Puedes refactorizar el contenido INTERNO
- NO cambies parámetros que WordPress pasa

---

### 7. ⛔ CUIDADO con Cambios en Estructura de Datos

**Ejemplo: Bloque espera array específico**
```php
// Template espera estructura:
$dates = [
    'departure' => '2024-01-15',
    'return' => '2024-01-22',
    'price' => 1500
];
```

**❌ SI CAMBIAS A:**
```php
// Ahora es objeto
$dates = (object)[
    'start' => '2024-01-15',  // ← Cambio de key
    'end' => '2024-01-22',
    'amount' => 1500
];
```

**💥 RESULTADO:**
- Template busca `$dates['departure']` pero ahora es `$dates->start`
- PHP Notice: Undefined index 'departure'
- Datos no se muestran

**✅ REGLA:**
- Mantener estructura de datos esperada por templates
- Si cambias estructura, actualizar templates simultáneamente
- Usar transformadores para compatibilidad

---

### 8. ⛔ NUNCA Cambiar Namespace de Clase PHP Sin Actualizar Autoload

**Namespace actual:**
```php
namespace Travel\Blocks\ACF;

class HeroCarousel {
    // ...
}
```

**Ubicación:** `/src/Blocks/ACF/HeroCarousel.php`

**❌ SI CAMBIAS NAMESPACE:**
```php
namespace Travel\ACFBlocks;  // ← Cambio de namespace
```

**Pero NO actualizas ubicación del archivo O composer autoload**

**💥 RESULTADO:**
- Autoloader no encuentra la clase
- PHP Fatal Error: Class 'Travel\Blocks\ACF\HeroCarousel' not found
- Plugin no se carga

**✅ REGLA:**
- Namespace debe reflejar estructura de carpetas (PSR-4)
- Si cambias namespace, mover archivo a carpeta correcta
- Ejecutar `composer dump-autoload` después de cambios
- Actualizar todas las referencias en código

---

### 9. ⛔ NUNCA Eliminar Métodos Públicos Usados Externamente

**Método público:**
```php
public function get_departure_dates() {
    // Lógica para obtener fechas
}
```

**Usado en:**
- Otros bloques
- Templates
- Hooks externos
- Plugins de terceros

**❌ SI ELIMINAS:**
```php
// Método eliminado
```

**💥 RESULTADO:**
- PHP Fatal Error: Call to undefined method
- Código que dependía del método se rompe

**✅ REGLA:**
- **NUNCA eliminar** métodos públicos sin verificar uso
- Buscar en todo el proyecto: `grep -r "get_departure_dates" .`
- Si no se usa, marcar como `@deprecated` antes de eliminar
- Mantener por al menos una versión mayor

---

### 10. ⛔ CUIDADO con CSS/JS que Usa Selectores Específicos

**Template genera HTML con clases:**
```php
<div class="hero-carousel">
    <div class="hero-carousel__slide">
        ...
    </div>
</div>
```

**CSS depende de estas clases:**
```css
.hero-carousel__slide {
    width: 100%;
}
```

**JavaScript depende de estas clases:**
```js
const slides = document.querySelectorAll('.hero-carousel__slide');
```

**❌ SI CAMBIAS CLASES:**
```php
<div class="carousel-hero">  <!-- ← Cambio de clase -->
    <div class="carousel-hero__item">  <!-- ← Cambio de clase -->
        ...
    </div>
</div>
```

**💥 RESULTADO:**
- CSS no se aplica (bloques sin estilos)
- JavaScript no funciona (selectors no encuentran elementos)
- El bloque se ve roto

**✅ REGLA:**
- **Mantener clases CSS** existentes (BEM u otras)
- Puedes AGREGAR clases nuevas
- Si cambias clases, actualizar CSS y JS simultáneamente
- Buscar en assets: `grep -r "hero-carousel__slide" assets/`

---

## ✅ CAMBIOS SEGUROS (No Rompen Producción)

### ✅ Puedes Cambiar SIN Riesgo:

1. **Título y Descripción del Bloque**
   ```php
   'title' => __('Hero Carousel v2', 'travel-blocks'),  // ✅ Seguro
   'description' => __('Nueva descripción', 'travel-blocks'),  // ✅ Seguro
   ```

2. **Icono del Bloque**
   ```php
   'icon' => 'slides',  // ✅ Seguro cambiar
   ```

3. **Keywords**
   ```php
   'keywords' => ['carousel', 'hero', 'slider'],  // ✅ Seguro cambiar
   ```

4. **Lógica INTERNA de Métodos Privados**
   ```php
   private function process_data($data) {
       // ✅ Puedes refactorizar completamente
       // Mientras la salida sea la misma
   }
   ```

5. **Optimización de Queries**
   ```php
   // ✅ Puedes optimizar la query
   // Mientras retorne los mismos datos
   $query = new WP_Query([
       'post_type' => 'package',
       'posts_per_page' => 10,
       'meta_key' => 'featured',  // ✅ Agregar meta_key para optimizar
   ]);
   ```

6. **Agregar Nuevas Propiedades a la Clase**
   ```php
   private $new_property;  // ✅ Seguro agregar
   ```

7. **Agregar Nuevos Métodos Privados/Protected**
   ```php
   private function new_helper_method() {  // ✅ Seguro agregar
       // ...
   }
   ```

8. **Mejorar Documentación (Docblocks)**
   ```php
   /**
    * Nueva documentación mejorada
    * ✅ Seguro cambiar
    */
   ```

9. **Agregar Sanitización/Escapado**
   ```php
   echo esc_html($title);  // ✅ Seguro agregar
   ```

10. **Extraer Código a Servicios/Helpers**
    ```php
    // Antes
    public function render() {
        $data = $this->get_data();
        // 50 líneas de procesamiento...
    }

    // Después
    public function render() {
        $data = $this->get_data();
        $processed = $this->dataProcessor->process($data);  // ✅ Seguro extraer
    }
    ```

---

## 🔍 CHECKLIST PRE-REFACTORIZACIÓN (Por Bloque)

**Antes de modificar CUALQUIER bloque, ejecutar este checklist:**

### 1. Identificar el Block Name Actual
```bash
# Buscar en el código de registro
grep -A 10 "acf_register_block_type\|register_block_type" src/Blocks/{Categoria}/{NombreBloque}.php
```

**Anotar:**
- ✅ Block name: `_________________`
- ✅ Namespace: `acf/` o `travel-blocks/` o `_________________`

### 2. Verificar Uso en Producción
```sql
-- Buscar en base de datos cuántas veces se usa este bloque
SELECT
    ID,
    post_title,
    post_type,
    post_status
FROM wp_posts
WHERE post_content LIKE '%wp:acf/hero-carousel%'  -- ← Ajustar al block name
  AND post_status IN ('publish', 'draft', 'pending');
```

**Anotar:**
- ✅ Bloques en publicados: `_____ posts`
- ✅ Bloques en borradores: `_____ posts`
- ✅ Total: `_____ instancias`

**⚠️ Si hay > 0 instancias: MÁXIMA PRECAUCIÓN**

### 3. Identificar Dependencias de Librerías
```bash
# Buscar enqueues de scripts/styles
grep -A 5 "wp_enqueue_script\|wp_enqueue_style" src/Blocks/{Categoria}/{NombreBloque}.php

# Buscar uso en templates
grep -r "Swiper\|Masonry\|Fancybox\|Leaflet" templates/{nombre-del-template}.php
```

**Anotar librerías usadas:**
- ✅ `_________________`
- ✅ `_________________`

**⚠️ NO eliminar estas librerías durante refactorización**

### 4. Identificar Campos ACF (si aplica)
```bash
# Buscar definición de campos
grep -A 20 "acf_add_local_field_group\|'fields'" src/Blocks/{Categoria}/{NombleBloque}.php
```

**Anotar nombres de campos (field names):**
- ✅ `_________________`
- ✅ `_________________`

**⚠️ NO cambiar estos nombres durante refactorización**

### 5. Identificar Métodos Públicos
```bash
# Buscar métodos públicos
grep "public function" src/Blocks/{Categoria}/{NombreBloque}.php
```

**Anotar métodos públicos:**
- ✅ `_________________`
- ✅ `_________________`

**⚠️ NO cambiar firma de estos métodos**

### 6. Identificar Clases CSS Usadas
```bash
# En template
grep -o "class=\"[^\"]*\"" templates/{nombre-del-template}.php | sort -u

# En CSS
grep -o "\.[a-zA-Z0-9_-]*" assets/blocks/{nombre-del-bloque}.css | sort -u
```

**Anotar clases principales:**
- ✅ `_________________`
- ✅ `_________________`

**⚠️ NO cambiar estas clases durante refactorización**

### 7. Verificar Render Callback
```bash
# Buscar render callback
grep "'render_callback'" src/Blocks/{Categoria}/{NombreBloque}.php
```

**Anotar:**
- ✅ Método: `_________________`
- ✅ Firma: `function (______, ______, ______)`

**⚠️ Mantener firma exacta**

### 8. Buscar Usos Externos del Bloque
```bash
# Buscar si otros archivos usan esta clase
grep -r "Travel\\Blocks\\{Categoria}\\{NombreBloque}" wp-content/ --exclude-dir=vendor

# Buscar si otros bloques usan métodos de este bloque
grep -r "{NombreBloque}::" wp-content/plugins/travel-blocks/
```

**Anotar dependencias:**
- ✅ `_________________`
- ✅ `_________________`

**⚠️ Verificar que refactorización no rompe estas dependencias**

---

## 🧪 CHECKLIST POST-REFACTORIZACIÓN (Por Bloque)

**Después de refactorizar, ANTES de commit:**

### 1. Verificación de Registro
```bash
# Ejecutar WP-CLI (si disponible)
wp block list --allow-root | grep {block-name}
```

**Verificar:**
- ✅ Bloque aparece en listado
- ✅ Namespace es correcto
- ✅ Título es correcto

### 2. Verificación en Editor (Testing Manual)

**Paso a paso:**
1. ✅ Ir al editor de WordPress
2. ✅ Crear nuevo post/página de prueba
3. ✅ Buscar el bloque en el inserter
4. ✅ Verificar que aparece en la categoría correcta
5. ✅ Insertar el bloque
6. ✅ Verificar que campos ACF aparecen (si aplica)
7. ✅ Configurar el bloque con datos de prueba
8. ✅ Verificar preview en editor
9. ✅ Guardar borrador
10. ✅ Previsualizar en frontend
11. ✅ Verificar que renderiza correctamente
12. ✅ Verificar consola del navegador (sin errores JS)
13. ✅ Verificar Network tab (sin errores 404 en assets)

### 3. Verificación de Bloque Existente

**Si el bloque YA existe en producción:**

1. ✅ Ir a un post/página que USE este bloque
2. ✅ Abrir en editor
3. ✅ Verificar que NO muestra "Este bloque contiene contenido inesperado"
4. ✅ Verificar que datos guardados se muestran correctamente
5. ✅ Hacer un cambio menor y guardar
6. ✅ Verificar que sigue funcionando
7. ✅ Previsualizar en frontend
8. ✅ Verificar que sigue renderizando correctamente

### 4. Verificación de CSS/JS

```bash
# Verificar que assets se cargan
curl -I https://tu-sitio.com/wp-content/plugins/travel-blocks/assets/blocks/{nombre}.css
# Debe retornar 200 OK

curl -I https://tu-sitio.com/wp-content/plugins/travel-blocks/assets/blocks/{nombre}.js
# Debe retornar 200 OK (si tiene JS)
```

**Verificar en navegador:**
- ✅ CSS se aplica correctamente
- ✅ JavaScript funciona (si aplica)
- ✅ Sin errores en consola
- ✅ Sin warnings en consola

### 5. Verificación de Autoload (si cambiaste namespace)

```bash
# Regenerar autoload
composer dump-autoload

# Verificar que clase se carga
wp eval 'var_dump(class_exists("Travel\\Blocks\\ACF\\HeroCarousel"));' --allow-root
# Debe retornar: bool(true)
```

### 6. Verificación de PHP Errors

```bash
# Verificar logs de PHP
tail -f /var/log/php/error.log

# O en WordPress
tail -f wp-content/debug.log
```

**Verificar:**
- ✅ Sin PHP Fatal Errors
- ✅ Sin PHP Warnings
- ✅ Sin PHP Notices (idealmente)

---

## 🚨 PLAN DE CONTINGENCIA

### Si Algo Se Rompe en Producción

**1. Identificar el Problema**
```bash
# Ver logs
tail -100 wp-content/debug.log

# Ver errores JS en navegador
# Console → buscar errores rojos
```

**2. Rollback Inmediato**
```bash
# Revertir commit
git revert HEAD

# O restaurar archivo específico
git checkout HEAD~1 -- src/Blocks/{Categoria}/{NombreBloque}.php
```

**3. Notificar Usuario**
- ✅ Informar que hay un problema
- ✅ Explicar qué se rompió
- ✅ Estimar tiempo de corrección
- ✅ Ofrecer rollback si es crítico

**4. Analizar Causa Raíz**
- ✅ ¿Qué cambió?
- ✅ ¿Por qué se rompió?
- ✅ ¿Qué no se verificó?

**5. Corregir y Re-testear**
- ✅ Aplicar fix
- ✅ Ejecutar checklist completo
- ✅ Re-deployar

---

## 📚 DOCUMENTACIÓN REQUERIDA POR BLOQUE

**Para cada bloque refactorizado, documentar:**

### 1. Cambios Realizados
```markdown
## Bloque: Hero Carousel

### Cambios Aplicados
- ✅ Extraída lógica de procesamiento a `CarouselDataProcessor`
- ✅ Movido enqueue de assets a `AssetManager`
- ✅ Agregado escapado en template líneas 15, 23, 45
- ✅ Agregada sanitización en método `process_images()`

### Cambios NO Realizados (Por Seguridad)
- ❌ NO se cambió block name (mantiene `hero-carousel`)
- ❌ NO se cambió namespace (mantiene `acf/hero-carousel`)
- ❌ NO se eliminó dependencia de Swiper.js
- ❌ NO se cambiaron nombres de campos ACF
```

### 2. Testing Realizado
```markdown
### Tests Ejecutados
- ✅ Editor: Inserción de bloque funciona
- ✅ Editor: Preview se muestra correctamente
- ✅ Editor: Configuración de campos ACF funciona
- ✅ Frontend: Renderizado correcto
- ✅ Frontend: Swiper se inicializa correctamente
- ✅ Frontend: Sin errores en consola
- ✅ Bloque existente: Datos guardados se mantienen
- ✅ Bloque existente: Edición funciona
```

### 3. Riesgos Identificados
```markdown
### Riesgos Residuales
- ⚠️ Dependencia de Swiper.js v8 (versión específica)
  - Si se actualiza Swiper, verificar compatibilidad
- ⚠️ Método público `get_slides()` usado en template externo
  - NO eliminar este método sin migración
```

---

## 🎯 REGLAS DE ORO

### 1. **NUNCA** cambies el block name
### 2. **NUNCA** cambies el namespace (ACF → Gutenberg)
### 3. **NUNCA** cambies nombres de campos ACF
### 4. **NUNCA** elimines dependencias activas (Swiper, etc.)
### 5. **NUNCA** cambies firma de métodos públicos
### 6. **NUNCA** cambies clases CSS usadas en assets
### 7. **SIEMPRE** verifica uso en DB antes de modificar
### 8. **SIEMPRE** ejecuta checklist pre y post refactorización
### 9. **SIEMPRE** haz commit por bloque (facilita rollback)
### 10. **SIEMPRE** documenta cambios y riesgos

---

**Preparado por:** Claude
**Fecha:** 2025-11-09
**Propósito:** Guía de precauciones críticas para auditoría de bloques sin romper producción
