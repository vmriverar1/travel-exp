# 📋 Precauciones Específicas por Fase y Bloque

## Extraído del Plan de Auditoría Original

Este documento consolida TODAS las precauciones mencionadas en el plan de auditoría para evitar romper funcionalidad en producción.

---

## 🔍 PRECAUCIONES GENERALES (Aplicables a Todas las Fases)

### Del Plan - Fase 2, 3, 4, 5

#### Metodología General

**Precauciones**:
1. ✅ **No modificar contratos de métodos públicos** si son usados externamente
2. ✅ **Probar cada bloque después de refactorización** (usuario hace testing)
3. ✅ **Commit por bloque** para facilitar rollback si es necesario

**Análisis de Cada Precaución:**

#### 1. No Modificar Contratos de Métodos Públicos

**¿Qué es un "contrato"?**
- Firma del método: nombre, parámetros, tipos, orden
- Tipo de retorno
- Comportamiento esperado

**¿Por qué NO modificar?**
- Otros bloques pueden llamar estos métodos
- Templates pueden usar estos métodos
- Código externo (tema, otros plugins) puede depender

**Ejemplo de violación:**
```php
// ANTES (producción)
public function get_departure_dates($package_id) {
    return $this->repository->get_dates($package_id);
}

// DESPUÉS (refactorización INCORRECTA)
public function get_departure_dates($package_id, $limit = 10) {  // ← Agregado parámetro
    return $this->repository->get_dates($package_id, $limit);
}
```

**Problema:**
- Código existente llama `get_departure_dates(123)` sin segundo parámetro
- Si el parámetro NO tiene valor por defecto, se rompe

**Solución correcta:**
```php
// Mantener método original
public function get_departure_dates($package_id) {
    return $this->get_departure_dates_with_limit($package_id, 10);
}

// Agregar nuevo método
public function get_departure_dates_with_limit($package_id, $limit = 10) {
    return $this->repository->get_dates($package_id, $limit);
}
```

**Checklist antes de modificar método público:**
- [ ] ¿Este método es llamado desde otros archivos?
  ```bash
  grep -r "->nombre_del_metodo(" wp-content/ --exclude-dir=vendor
  ```
- [ ] ¿Este método es llamado desde templates?
  ```bash
  grep -r "->nombre_del_metodo(" templates/
  ```
- [ ] ¿Este método está documentado en README/docs como API pública?
- [ ] Si se usa externamente: **NO modificar firma**, crear método nuevo

---

#### 2. Probar Cada Bloque Después de Refactorización

**Testing Manual Requerido:**

**En Editor de WordPress:**
1. [ ] Crear nuevo post/página de prueba
2. [ ] Insertar el bloque refactorizado
3. [ ] Configurar todos los campos ACF (si aplica)
4. [ ] Verificar preview en editor
5. [ ] Guardar borrador
6. [ ] Previsualizar en frontend
7. [ ] Verificar que renderiza correctamente
8. [ ] Abrir consola del navegador → verificar sin errores
9. [ ] Abrir Network tab → verificar que assets se cargan (200 OK)

**En Bloque Existente (si hay en producción):**
1. [ ] Ir a post/página que YA usa este bloque
2. [ ] Abrir en editor
3. [ ] Verificar que NO dice "Este bloque contiene contenido inesperado"
4. [ ] Verificar que datos se muestran correctamente
5. [ ] Hacer cambio menor y guardar
6. [ ] Previsualizar en frontend
7. [ ] Verificar que sigue funcionando

**En Frontend:**
1. [ ] Abrir página con el bloque
2. [ ] Verificar estilos aplicados
3. [ ] Verificar JavaScript funciona (carousels, acordeones, etc.)
4. [ ] Probar interacciones (clicks, hover, etc.)
5. [ ] Verificar responsive (móvil, tablet, desktop)

**En PHP Logs:**
```bash
tail -f wp-content/debug.log
# Debe estar vacío (sin errores, warnings, notices)
```

**En JavaScript Console:**
- Sin errores rojos
- Sin warnings (idealmente)
- Sin 404s en assets

---

#### 3. Commit por Bloque

**¿Por qué commit individual?**
- Facilita identificar QUÉ cambio causó problema
- Permite rollback quirúrgico (solo ese bloque)
- Mejor trazabilidad en git log
- Code review más fácil

**Estructura de commits:**

```bash
# MAL (todos los bloques juntos)
git add .
git commit -m "Refactor all ACF blocks"
# Si algo se rompe, difícil identificar cuál

# BIEN (un bloque a la vez)
git add src/Blocks/ACF/HeroCarousel.php templates/hero-carousel.php
git commit -m "refactor(ACF): HeroCarousel - extract data processing to service

- Extracted carousel data processing to CarouselDataProcessor service
- Added sanitization to all inputs
- Added escaping to template outputs
- Maintained block name 'hero-carousel'
- Maintained ACF field names
- Tested: Editor preview works, frontend renders correctly
"

# Siguiente bloque
git add src/Blocks/ACF/Breadcrumb.php templates/breadcrumb.php
git commit -m "refactor(ACF): Breadcrumb - simplify path generation

- Simplified breadcrumb path generation logic
- Reduced method complexity from 45 to 15 lines
- Added unit tests for path generation
- Maintained block name 'breadcrumb'
- Tested: Works correctly in all page types
"
```

**Beneficio en caso de problema:**
```bash
# Revertir solo el bloque que falló
git revert abc123  # Commit del bloque problemático

# O restaurar archivo específico
git checkout HEAD~1 -- src/Blocks/ACF/HeroCarousel.php
```

---

## 🔧 PRECAUCIONES ESPECÍFICAS POR BLOQUE

### Bloques ACF

#### ContactForm
**Del plan original:**
> ContactForm (2h)

**Precauciones específicas:**
1. ⚠️ **Formulario con envío de emails**
   - NO romper lógica de envío de correos
   - Mantener integración con sistema de email (wp_mail o servicio externo)
   - Verificar que emails llegan correctamente después de refactorización

2. ⚠️ **Validación y sanitización crítica**
   - Formularios son punto de entrada de usuarios
   - DEBE tener sanitización completa de todos los campos
   - DEBE tener nonce verification
   - DEBE tener CAPTCHA o anti-spam (si existe)

3. ⚠️ **Campos ACF del formulario**
   - NO cambiar nombres de campos que se mapean al email
   - Mantener estructura de datos que espera el handler de email

**Checklist adicional:**
- [ ] Probar envío de formulario en ambiente de desarrollo
- [ ] Verificar que email llega correctamente
- [ ] Verificar que datos se guardan en DB (si aplica)
- [ ] Verificar mensaje de éxito/error al usuario

---

#### PostsListAdvanced
**Del plan original:**
> PostsListAdvanced (2h)

**Precauciones específicas:**
1. ⚠️ **Queries complejas de posts**
   - Bloque probablemente tiene WP_Query con múltiples parámetros
   - NO romper lógica de filtrado (categorías, tags, meta queries)
   - Mantener paginación si existe

2. ⚠️ **Múltiples estilos/layouts**
   - El nombre "Advanced" sugiere múltiples opciones de display
   - Verificar que TODOS los layouts siguen funcionando

3. ⚠️ **AJAX para cargar más posts** (si existe)
   - Mantener funcionalidad de "Load More"
   - Verificar nonce en AJAX
   - Verificar que nuevos posts se cargan correctamente

**Checklist adicional:**
- [ ] Probar todos los layouts disponibles
- [ ] Probar filtros (si existen)
- [ ] Probar paginación / load more (si existe)
- [ ] Verificar performance de query (no debe ser más lenta)

---

#### FlexibleGridCarousel
**Del plan original:**
> FlexibleGridCarousel (2.5h)

**Precauciones específicas:**
1. ⚠️ **Dependencia de librería de carousel**
   - Probablemente usa Swiper.js o similar
   - NO eliminar enqueue de la librería
   - Verificar versión específica requerida

2. ⚠️ **Configuración flexible**
   - El nombre "Flexible" sugiere múltiples opciones de configuración
   - Verificar que TODAS las opciones siguen funcionando:
     - Número de slides
     - Autoplay
     - Navigation (arrows)
     - Pagination (dots)
     - Loop
     - Breakpoints (responsive)

3. ⚠️ **Grid + Carousel híbrido**
   - Puede tener modo grid y modo carousel
   - Verificar ambos modos

**Checklist adicional:**
- [ ] Verificar que librería de carousel se carga (Network tab)
- [ ] Probar navegación (arrows, dots)
- [ ] Probar autoplay (si existe)
- [ ] Probar responsive (diferentes breakpoints)
- [ ] Verificar que no hay error `Swiper is not defined` en consola

---

#### HeroCarousel
**Del plan original:**
> HeroCarousel (2.5h)

**Precauciones específicas:**
1. ⚠️ **Hero = Bloque crítico de homepage**
   - Este bloque probablemente está en la página principal
   - Cualquier error es MUY visible
   - Testing exhaustivo requerido

2. ⚠️ **Múltiples slides con contenido complejo**
   - Cada slide puede tener: imagen, título, descripción, CTA
   - Verificar que TODOS los campos se muestran
   - Verificar que imágenes se cargan correctamente

3. ⚠️ **Transiciones y efectos**
   - Puede tener efectos de fade, slide, etc.
   - Verificar que transiciones funcionan suavemente
   - Verificar que no hay glitches visuales

**Checklist adicional:**
- [ ] Probar con 1 slide, 3 slides, 10 slides
- [ ] Verificar que todas las imágenes cargan
- [ ] Verificar que CTAs son clickeables
- [ ] Verificar que autoplay funciona (si existe)
- [ ] Verificar en homepage real de producción

---

### Bloques Package

#### DatesAndPrices
**Del plan original:**
> DatesAndPrices (3h)
> **Precauciones especiales**: Mantener funcionalidad del booking wizard intacta

**Análisis de la precaución:**

**¿Qué es el "booking wizard"?**
- Sistema de reserva/booking integrado en el bloque
- Probablemente abre modal o sidebar con formulario
- Permite seleccionar fecha y proceder a reserva

**Riesgos de romperlo:**
1. Cambiar IDs o clases que JavaScript usa para abrir wizard
2. Eliminar enqueue de JavaScript del wizard
3. Cambiar estructura de datos de fechas que wizard espera
4. Romper AJAX endpoint que wizard usa
5. Cambiar nombres de campos ACF que wizard lee

**Precauciones específicas:**
1. ⚠️ **NO cambiar clase CSS del trigger**
   ```php
   // Template actual (ejemplo)
   <button class="dates-and-prices__book-now" data-departure-id="123">
       Book Now
   </button>
   ```
   - JavaScript busca `.dates-and-prices__book-now`
   - Si cambias esta clase, el wizard no se abre

2. ⚠️ **NO eliminar data attributes**
   ```html
   data-departure-id="123"
   data-price="1500"
   data-date="2024-01-15"
   ```
   - Wizard lee estos atributos para pre-llenar formulario

3. ⚠️ **NO cambiar estructura de JSON de fechas**
   ```php
   // Si el bloque pasa datos así a JavaScript:
   $dates_json = json_encode([
       'departures' => [...],
       'prices' => [...],
   ]);
   ```
   - Wizard espera esta estructura exacta

4. ⚠️ **Mantener AJAX action name**
   ```php
   wp_ajax_{action_name}
   wp_ajax_nopriv_{action_name}
   ```
   - Si cambias el action name, AJAX falla

**Checklist DatesAndPrices:**
- [ ] Verificar que calendario de fechas se muestra
- [ ] Verificar que precios se muestran correctamente
- [ ] Verificar etiquetas (SOLD OUT, BEST PRICE, etc.)
- [ ] Click en "Book Now" → wizard se abre
- [ ] Wizard pre-llena datos de fecha y precio seleccionados
- [ ] Formulario de wizard funciona (validación, envío)
- [ ] No hay errores en consola
- [ ] AJAX de wizard funciona (si aplica)

---

#### ItineraryDayByDay
**Del plan original:**
> ItineraryDayByDay (3h)
> **Precauciones especiales**: Mantener lógica de acordeón y estructura de días

**Análisis de la precaución:**

**¿Qué es la "lógica de acordeón"?**
- Sistema de expand/collapse para cada día del itinerario
- Click en día → se expande mostrando detalles
- Click en otro día → el anterior se colapsa (o no, depende del tipo)

**Riesgos de romperlo:**
1. Cambiar IDs o clases que JavaScript del acordeón usa
2. Eliminar JavaScript del acordeón
3. Cambiar estructura HTML que acordeón espera
4. Romper states (expanded/collapsed)

**Precauciones específicas:**
1. ⚠️ **Mantener estructura HTML de acordeón**
   ```html
   <div class="itinerary">
       <div class="itinerary__day">
           <div class="itinerary__day-header">  <!-- ← Clickeable -->
               Day 1: Lima Arrival
           </div>
           <div class="itinerary__day-content">  <!-- ← Expandible -->
               Details here...
           </div>
       </div>
   </div>
   ```
   - JavaScript busca `.itinerary__day-header` para hacer click
   - JavaScript muestra/oculta `.itinerary__day-content`

2. ⚠️ **Mantener atributos de estado**
   ```html
   <div class="itinerary__day" data-expanded="false">
   ```
   - JavaScript puede usar data attributes para tracking

3. ⚠️ **No romper iconos de expand/collapse**
   - Puede usar iconos (+ / -) o arrows (↓ / ↑)
   - Verificar que iconos cambian correctamente

**¿Qué es "estructura de días"?**
- Datos estructurados: Día 1, Día 2, ..., Día N
- Cada día tiene: título, descripción, actividades, meals, accommodation
- Puede usar ACF Repeater field

**Precauciones específicas:**
1. ⚠️ **NO cambiar nombre del campo ACF repeater**
   ```php
   'name' => 'itinerary_days',  // ← NO cambiar
   ```

2. ⚠️ **NO cambiar nombres de sub-campos**
   ```php
   'name' => 'day_number',      // ← NO cambiar
   'name' => 'day_title',       // ← NO cambiar
   'name' => 'day_description', // ← NO cambiar
   ```

3. ⚠️ **Mantener orden de días**
   - Si hay lógica que ordena por `day_number`, mantenerla

**Checklist ItineraryDayByDay:**
- [ ] Verificar que todos los días se muestran
- [ ] Click en día 1 → se expande
- [ ] Click en día 2 → se expande (y día 1 se colapsa, si es accordion tipo "solo uno abierto")
- [ ] Verificar que contenido de cada día es correcto
- [ ] Verificar iconos de expand/collapse
- [ ] Verificar animaciones de transición (smooth)
- [ ] No hay errores en consola

---

#### ProductGalleryHero
**Del plan original:**
> ProductGalleryHero (2.5h)
> **Precauciones especiales**: No romper integración con galería de medios

**Análisis de la precaución:**

**¿Qué es "integración con galería de medios"?**
- Selector de WordPress Media Library para elegir imágenes
- Puede tener lightbox para ver imágenes en grande
- Puede tener thumbnail navigation

**Riesgos de romperlo:**
1. Cambiar campo ACF de tipo Gallery
2. Romper JavaScript de lightbox
3. Cambiar IDs de imágenes que lightbox usa

**Precauciones específicas:**
1. ⚠️ **Campo ACF Gallery**
   ```php
   'type' => 'gallery',
   'name' => 'product_gallery',  // ← NO cambiar name
   ```

2. ⚠️ **NO eliminar librería de lightbox**
   - Puede usar Fancybox, Lightbox2, GLightbox, etc.
   - Verificar qué librería usa:
   ```bash
   grep -r "Fancybox\|Lightbox\|GLightbox" assets/blocks/product-gallery-hero.js
   ```

3. ⚠️ **Mantener data attributes en imágenes**
   ```html
   <img src="..." data-fancybox="gallery" data-caption="...">
   ```

**Checklist ProductGalleryHero:**
- [ ] Verificar que todas las imágenes se muestran
- [ ] Click en imagen → lightbox se abre
- [ ] Navegación en lightbox funciona (prev/next)
- [ ] Cerrar lightbox funciona
- [ ] Thumbnails funcionan (si existen)
- [ ] Zoom funciona (si existe)
- [ ] No hay errores en consola

---

#### ContactPlannerForm
**Del plan original:**
> ContactPlannerForm (2h)
> **Precauciones especiales**: Mantener integración con sistema de formularios

**Análisis de la precaución:**

**¿Qué es "integración con sistema de formularios"?**
- Puede usar Contact Form 7, Gravity Forms, WPForms, etc.
- O sistema personalizado de envío de emails
- Puede guardar submissions en DB

**Riesgos de romperlo:**
1. Cambiar shortcode del formulario
2. Romper JavaScript de validación
3. Romper envío de email
4. Romper guardado en DB

**Precauciones específicas:**
1. ⚠️ **Si usa plugin de formularios (CF7, etc.)**
   ```php
   // Template puede tener:
   echo do_shortcode('[contact-form-7 id="123"]');
   ```
   - NO cambiar este shortcode
   - NO cambiar ID del formulario

2. ⚠️ **Si usa formulario custom**
   - Mantener name de campos
   - Mantener action del form
   - Mantener nonce verification
   - Mantener AJAX action (si aplica)

3. ⚠️ **Envío de emails**
   ```php
   wp_mail($to, $subject, $message, $headers);
   ```
   - Verificar que sigue enviando correctamente

**Checklist ContactPlannerForm:**
- [ ] Formulario se muestra correctamente
- [ ] Todos los campos son editables
- [ ] Validación funciona (campos requeridos, email format, etc.)
- [ ] Enviar formulario → mensaje de éxito
- [ ] Email llega al destinatario
- [ ] Datos se guardan en DB (si aplica)
- [ ] No hay errores en consola

---

#### PackageMap
**Del plan original:**
> PackageMap (2.5h)
> **Precauciones especiales**: Mantener integración con API de mapas

**Análisis de la precaución:**

**¿Qué es "integración con API de mapas"?**
- Puede usar Google Maps API
- Puede usar Leaflet (OpenStreetMap)
- Puede usar Mapbox
- Puede ser imagen estática de mapa

**Riesgos de romperlo:**
1. Eliminar enqueue de librería de mapas
2. Cambiar API key
3. Romper inicialización del mapa
4. Cambiar coordenadas o estructura de datos

**Precauciones específicas:**

**Si usa Google Maps:**
1. ⚠️ **API Key**
   ```php
   wp_enqueue_script(
       'google-maps',
       'https://maps.googleapis.com/maps/api/js?key=API_KEY_HERE',
       [],
       null,
       true
   );
   ```
   - NO eliminar este enqueue
   - NO cambiar API key (puede estar en options)

2. ⚠️ **Inicialización del mapa**
   ```js
   const map = new google.maps.Map(element, options);
   ```
   - Mantener selector del elemento
   - Mantener estructura de options

**Si usa Leaflet:**
1. ⚠️ **CSS y JS de Leaflet**
   ```php
   wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
   wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
   ```
   - NO eliminar estos enqueues

2. ⚠️ **Inicialización**
   ```js
   const map = L.map('map').setView([lat, lng], zoom);
   ```

**Estructura de datos de ubicaciones:**
```php
// Si pasa datos de ubicaciones a JavaScript
$locations = [
    ['name' => 'Lima', 'lat' => -12.0464, 'lng' => -77.0428],
    ['name' => 'Cusco', 'lat' => -13.5319, 'lng' => -71.9675],
];
```
- NO cambiar estructura (keys: name, lat, lng)

**Checklist PackageMap:**
- [ ] Mapa se carga y muestra correctamente
- [ ] Marcadores/pins se muestran en ubicaciones correctas
- [ ] Zoom funciona
- [ ] Pan (arrastrar mapa) funciona
- [ ] Popups/tooltips funcionan (si existen)
- [ ] Ruta se muestra (si existe)
- [ ] No hay errores en consola (especialmente sobre API key)
- [ ] No hay warnings sobre billing (Google Maps)

---

### Bloques Deal

#### DealsSlider
**Del plan original:**
> DealsSlider (2h)
> **Precauciones especiales**: Mantener funcionalidad de slider (navegación, autoplay)

**Análisis de la precaución:**

**Funcionalidades críticas del slider:**
1. **Navegación**: Arrows para prev/next
2. **Autoplay**: Slides cambian automáticamente
3. **Pagination**: Dots para ir a slide específico
4. **Loop**: Volver al inicio después del último slide
5. **Responsive**: Diferentes configuraciones por breakpoint

**Precauciones específicas:**
1. ⚠️ **Librería de slider**
   - Probablemente Swiper.js
   - NO eliminar enqueue

2. ⚠️ **Configuración del slider**
   ```js
   new Swiper('.deals-slider', {
       slidesPerView: 3,
       spaceBetween: 30,
       navigation: {
           nextEl: '.swiper-button-next',
           prevEl: '.swiper-button-prev',
       },
       pagination: {
           el: '.swiper-pagination',
           clickable: true,
       },
       autoplay: {
           delay: 5000,
       },
       loop: true,
       breakpoints: {
           640: { slidesPerView: 1 },
           768: { slidesPerView: 2 },
           1024: { slidesPerView: 3 },
       }
   });
   ```
   - NO cambiar selectores (`.swiper-button-next`, etc.)
   - Verificar todas las opciones siguen funcionando

3. ⚠️ **HTML structure requerida**
   ```html
   <div class="swiper deals-slider">
       <div class="swiper-wrapper">
           <div class="swiper-slide">Deal 1</div>
           <div class="swiper-slide">Deal 2</div>
       </div>
       <div class="swiper-button-next"></div>
       <div class="swiper-button-prev"></div>
       <div class="swiper-pagination"></div>
   </div>
   ```
   - NO cambiar esta estructura (Swiper la requiere)

**Checklist DealsSlider:**
- [ ] Slider se inicializa correctamente
- [ ] Navigation arrows funcionan (prev/next)
- [ ] Pagination dots funcionan
- [ ] Autoplay funciona (slides cambian solos)
- [ ] Loop funciona (vuelve al inicio)
- [ ] Responsive funciona (diferentes slides por breakpoint)
- [ ] No hay errores en consola

---

### Bloques Template

#### TaxonomyArchiveHero
**Del plan original:**
> TaxonomyArchiveHero (2h)
> **Precauciones especiales**: Verificar que funciona en todos los archivos de taxonomía

**Análisis de la precaución:**

**¿Qué son "archivos de taxonomía"?**
- Páginas de archivo de categorías
- Páginas de archivo de tags
- Páginas de archivo de custom taxonomies (destination, activity, etc.)

**El bloque debe funcionar en:**
- `category.php` (categorías)
- `tag.php` (etiquetas)
- `taxonomy-{taxonomy}.php` (taxonomías personalizadas)
- `archive.php` (archivo genérico)

**Precauciones específicas:**
1. ⚠️ **Detectar contexto correcto**
   ```php
   if (is_category()) {
       $term = get_queried_object();
       $title = $term->name;
   } elseif (is_tag()) {
       $term = get_queried_object();
       $title = $term->name;
   } elseif (is_tax()) {
       $term = get_queried_object();
       $title = $term->name;
   }
   ```
   - El bloque debe detectar correctamente el tipo de archivo
   - Debe obtener datos del término correcto

2. ⚠️ **Imagen destacada de taxonomía**
   - Puede usar ACF field en taxonomía
   - Puede usar plugin de thumbnail de taxonomía
   - Verificar que imagen se obtiene correctamente

3. ⚠️ **Fallbacks**
   - Si término no tiene imagen, mostrar placeholder
   - Si término no tiene descripción, mostrar mensaje o ocultar sección

**Checklist TaxonomyArchiveHero:**
- [ ] Funciona en página de categoría
- [ ] Funciona en página de tag
- [ ] Funciona en página de taxonomía custom (destination, activity, etc.)
- [ ] Título del término se muestra correctamente
- [ ] Descripción se muestra correctamente
- [ ] Imagen se muestra correctamente
- [ ] Fallback funciona si no hay imagen
- [ ] Breadcrumb funciona (si existe)

---

#### Breadcrumb (Template)
**Del plan original:**
> Breadcrumb (1.5h)
> **Precauciones especiales**: Mantener lógica de generación de ruta

**Análisis de la precaución:**

**¿Qué es "lógica de generación de ruta"?**
- Algoritmo que genera la ruta de breadcrumb según el contexto
- Home > Category > Post
- Home > Destination > Package
- Home > Search Results

**Contextos diferentes:**
- Homepage: No breadcrumb
- Single post: Home > Category > Post Title
- Single page: Home > Parent Page > Current Page
- Category archive: Home > Category
- Custom post type: Home > Post Type Archive > Post Title
- Search results: Home > Search Results for "query"
- 404: Home > Page Not Found

**Precauciones específicas:**
1. ⚠️ **NO romper detección de contexto**
   ```php
   if (is_home()) {
       // No breadcrumb
   } elseif (is_single()) {
       // Single post breadcrumb
   } elseif (is_page()) {
       // Page breadcrumb (con parents)
   } elseif (is_category()) {
       // Category breadcrumb
   } elseif (is_search()) {
       // Search results breadcrumb
   }
   ```

2. ⚠️ **Jerarquía de páginas**
   ```php
   $ancestors = get_post_ancestors($post_id);
   ```
   - Si página tiene parents, mostrarlos en orden

3. ⚠️ **Custom post types**
   - Verificar que breadcrumb funciona para post types custom (package, deal, etc.)

**Checklist Breadcrumb:**
- [ ] Funciona en homepage (no se muestra o muestra solo "Home")
- [ ] Funciona en single post (Home > Category > Post)
- [ ] Funciona en single page (Home > Parent > Child)
- [ ] Funciona en single package (Home > Packages > Package Name)
- [ ] Funciona en category archive (Home > Category)
- [ ] Funciona en search results (Home > Search Results)
- [ ] Funciona en 404 (Home > Page Not Found)
- [ ] Schema.org markup correcto (si existe)
- [ ] Separadores se muestran correctamente (> o / o →)

---

## 📊 MATRIZ DE RIESGOS POR TIPO DE CAMBIO

| Tipo de Cambio | Riesgo | Bloques Afectados | Precaución |
|----------------|--------|-------------------|------------|
| **Cambiar block name** | 🔴 CRÍTICO | Todos (45 bloques) | ⛔ NUNCA hacer |
| **Cambiar namespace ACF→Gutenberg** | 🔴 CRÍTICO | Todos ACF (15 bloques) | ⛔ NUNCA hacer |
| **Cambiar nombres de campos ACF** | 🔴 CRÍTICO | Todos con ACF | ⛔ NUNCA hacer |
| **Eliminar dependencias (Swiper, etc.)** | 🔴 CRÍTICO | Carousels, sliders | Verificar uso primero |
| **Cambiar firma de métodos públicos** | 🟠 ALTO | Todos | Verificar uso externo |
| **Cambiar clases CSS usadas en JS** | 🟠 ALTO | Con JavaScript | Verificar selectores |
| **Cambiar estructura de datos** | 🟠 ALTO | Todos | Actualizar templates |
| **Cambiar categoría del bloque** | 🟡 MEDIO | Todos | Documentar cambio |
| **Cambiar título/descripción** | 🟢 BAJO | Todos | Seguro |
| **Refactorizar métodos privados** | 🟢 BAJO | Todos | Seguro (mantener output) |
| **Agregar sanitización/escapado** | 🟢 BAJO | Todos | Seguro y recomendado |

---

## ✅ CHECKLIST GENERAL PRE-REFACTORIZACIÓN

**Ejecutar ANTES de modificar cualquier bloque:**

### 1. Investigación Inicial
- [ ] Identificar block name actual
- [ ] Buscar uso en DB: `SELECT * FROM wp_posts WHERE post_content LIKE '%wp:acf/block-name%'`
- [ ] Anotar número de instancias en producción
- [ ] Si > 0: MÁXIMA PRECAUCIÓN

### 2. Análisis de Dependencias
- [ ] Identificar librerías JavaScript usadas (Swiper, Leaflet, etc.)
- [ ] Identificar campos ACF (nombres, tipos)
- [ ] Identificar métodos públicos
- [ ] Identificar clases CSS usadas en JavaScript

### 3. Análisis de Uso Externo
- [ ] Buscar usos de la clase en otros archivos: `grep -r "NombreBloque" wp-content/`
- [ ] Buscar llamadas a métodos: `grep -r "->metodo(" wp-content/`
- [ ] Identificar dependencias externas

### 4. Plan de Acción
- [ ] Listar cambios a realizar
- [ ] Identificar cambios PROHIBIDOS (block name, ACF fields, etc.)
- [ ] Identificar cambios de RIESGO (métodos públicos, clases CSS)
- [ ] Identificar cambios SEGUROS (métodos privados, optimizaciones)

---

## ✅ CHECKLIST GENERAL POST-REFACTORIZACIÓN

**Ejecutar DESPUÉS de modificar cada bloque:**

### 1. Verificación de Código
- [ ] Sin errores PHP en código
- [ ] Sin warnings de linter
- [ ] Documentación actualizada (docblocks)

### 2. Testing en Editor
- [ ] Bloque aparece en inserter
- [ ] Bloque se puede insertar
- [ ] Campos ACF aparecen (si aplica)
- [ ] Preview funciona
- [ ] Guardar funciona

### 3. Testing en Frontend
- [ ] Bloque renderiza correctamente
- [ ] Estilos aplicados
- [ ] JavaScript funciona
- [ ] Sin errores en consola
- [ ] Sin 404 en Network tab

### 4. Testing de Bloque Existente (si aplica)
- [ ] Abrir post con bloque existente
- [ ] NO muestra error de "contenido inesperado"
- [ ] Datos guardados se mantienen
- [ ] Sigue funcionando después de editar

### 5. Documentación
- [ ] Cambios documentados
- [ ] Riesgos identificados documentados
- [ ] Testing realizado documentado

### 6. Git
- [ ] Commit individual del bloque
- [ ] Mensaje descriptivo
- [ ] Cambios verificados antes de push

---

**Preparado por:** Claude
**Fecha:** 2025-11-09
**Propósito:** Consolidación de todas las precauciones específicas mencionadas en el plan de auditoría
