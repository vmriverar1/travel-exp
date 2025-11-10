# Auditoría Integral de Arquitectura - Travel Blocks

## Objetivo Principal

Eliminar la dependencia de `wp-content/themes/travel-content-kit/assets/css/global.css` y establecer una arquitectura de bloques independientes que:

1. Utilicen únicamente variables de `theme.json` para estilos globales
2. Implementen CSS específico por bloque cuando sea necesario
3. Eviten conflictos de selectores entre bloques
4. Mantengan la funcionalidad actual en producción
5. Apliquen principios de arquitectura limpia y escalable

---

## Parte 1: Arquitectura Correcta para Plugins de WordPress

### 1.1 Estructura de Archivos y Organización (PSR-4)

**Principio**: Cada plugin debe seguir el estándar PSR-4 para autoloading de clases.

**Estructura recomendada**:
```
plugin-name/
├── src/
│   ├── Blocks/           # Clases de bloques organizadas por dominio
│   │   ├── ACF/         # Bloques ACF generales
│   │   ├── Package/     # Bloques específicos de paquetes
│   │   ├── Deal/        # Bloques de promociones
│   │   └── Template/    # Bloques de plantillas
│   ├── Core/            # Clases base y utilidades
│   ├── Services/        # Servicios (AJAX, API, etc.)
│   └── Helpers/         # Funciones helper
├── templates/           # Archivos de plantilla PHP
├── assets/
│   ├── blocks/         # CSS/JS por bloque
│   │   ├── block-name.css
│   │   └── block-name.js
│   └── shared/         # Recursos compartidos (iconos, imágenes)
├── languages/          # Archivos de traducción
├── vendor/             # Dependencias de Composer
├── composer.json       # Definición de dependencias
└── plugin-name.php     # Archivo principal
```

**Namespace recomendado**: `Travel\PluginName\Categoria\Clase`

Ejemplo: `Travel\Blocks\Package\DatesAndPrices`

### 1.2 Patrones de Diseño Aplicables

#### 1.2.1 Singleton Pattern
**Uso**: Clases principales del plugin que deben tener una única instancia.

**Aplicación**:
- Clase principal del plugin
- Gestor de bloques
- Gestor de assets
- Gestor de AJAX

**Beneficios**:
- Control centralizado de la instancia
- Prevención de duplicación de recursos
- Punto único de acceso

#### 1.2.2 Factory Pattern
**Uso**: Creación de instancias de bloques.

**Aplicación**:
- BlockFactory para instanciar bloques según tipo
- Creación dinámica basada en configuración
- Registro de bloques en WordPress/ACF

**Beneficios**:
- Desacoplamiento de la lógica de creación
- Flexibilidad para agregar nuevos tipos
- Código más mantenible

#### 1.2.3 Observer Pattern (Hooks de WordPress)
**Uso**: Sistema de acciones y filtros de WordPress.

**Aplicación**:
- Hooks para enqueue de assets
- Hooks para registro de bloques
- Hooks para modificación de comportamiento

**Beneficios**:
- Extensibilidad sin modificar código core
- Desacoplamiento de funcionalidad
- Compatibilidad con otros plugins

#### 1.2.4 MVC Pattern
**Uso**: Separación de lógica, presentación y datos.

**Aplicación**:
- Model: Clases que manejan datos (ACF fields, post meta)
- View: Templates PHP para renderizado
- Controller: Clases de bloques que coordinan Model y View

**Beneficios**:
- Separación clara de responsabilidades
- Testabilidad mejorada
- Reutilización de componentes

#### 1.2.5 Service Container / Dependency Injection
**Uso**: Gestión de dependencias entre clases.

**Aplicación**:
- Inyección de servicios en bloques
- Registro de servicios centralizados
- Resolución automática de dependencias

**Beneficios**:
- Bajo acoplamiento
- Alta testabilidad
- Flexibilidad en configuración

### 1.3 Principios SOLID

#### S - Single Responsibility Principle (SRP)
**Definición**: Una clase debe tener una única razón para cambiar.

**Aplicación en bloques**:
- Clase de bloque: solo registro y configuración
- Clase de renderizado: solo lógica de presentación
- Clase de datos: solo obtención y procesamiento de datos
- Clase de assets: solo enqueue de CSS/JS

**Anti-patrón a evitar**: Bloques que mezclan registro, renderizado, lógica de negocio y gestión de assets en un solo archivo.

#### O - Open/Closed Principle (OCP)
**Definición**: Abierto para extensión, cerrado para modificación.

**Aplicación en bloques**:
- Clase base `BlockBase` que define interfaz común
- Bloques heredan y extienden sin modificar la base
- Hooks y filtros para permitir extensión externa

**Beneficio**: Agregar nuevos bloques sin modificar código existente.

#### L - Liskov Substitution Principle (LSP)
**Definición**: Subclases deben ser sustituibles por sus clases base.

**Aplicación en bloques**:
- Todos los bloques que heredan de `BlockBase` deben implementar los mismos métodos
- Comportamiento consistente en métodos heredados
- No romper contratos de la clase base

#### I - Interface Segregation Principle (ISP)
**Definición**: No forzar implementación de métodos no utilizados.

**Aplicación en bloques**:
- Interfaces específicas por funcionalidad (Renderable, HasAssets, HasACF, HasAjax)
- Bloques implementan solo las interfaces necesarias
- Evitar interfaces monolíticas

#### D - Dependency Inversion Principle (DIP)
**Definición**: Depender de abstracciones, no de concreciones.

**Aplicación en bloques**:
- Inyectar interfaces en lugar de clases concretas
- Usar Service Container para resolver dependencias
- Evitar dependencias hardcodeadas

### 1.4 Principios de Clean Code

#### KISS (Keep It Simple, Stupid)
**Aplicación**:
- Métodos cortos y enfocados (máximo 20-30 líneas)
- Evitar complejidad innecesaria
- Soluciones directas y claras

**Anti-patrón**: Lógica anidada con múltiples niveles de condicionales.

#### DRY (Don't Repeat Yourself)
**Aplicación**:
- Métodos helper para lógica repetida
- Clase base para funcionalidad común de bloques
- Utilidades compartidas en carpeta Helpers

**Anti-patrón**: Copiar y pegar código entre bloques.

#### YAGNI (You Aren't Gonna Need It)
**Aplicación**:
- Implementar solo lo necesario actualmente
- Evitar funcionalidad "por si acaso"
- Refactorizar cuando se necesite, no antes

**Anti-patrón**: Métodos o clases que no tienen uso real.

#### Nombres Descriptivos
**Aplicación**:
- Variables: `$package_id`, `$departure_date`, `$sold_out_status`
- Métodos: `get_package_departures()`, `render_pricing_table()`, `is_package_sold_out()`
- Clases: `DatesAndPrices`, `PackageMetadata`, `BookingWizard`

**Anti-patrón**: `$data`, `$temp`, `process()`, `handle()`.

#### Bajo Acoplamiento
**Aplicación**:
- Bloques no deben conocer implementación de otros bloques
- Comunicación mediante hooks y eventos
- Dependencias inyectadas, no instanciadas internamente

### 1.5 Seguridad en WordPress

#### Sanitización de Entrada
**Aplicación**:
- `sanitize_text_field()` para campos de texto
- `absint()` para IDs numéricos
- `sanitize_email()` para emails
- `wp_kses_post()` para HTML permitido

**Dónde aplicar**: Todo input de usuario (forms, AJAX, query params).

#### Escapado de Salida
**Aplicación**:
- `esc_html()` para texto plano
- `esc_attr()` para atributos HTML
- `esc_url()` para URLs
- `wp_kses_post()` para contenido HTML confiable

**Dónde aplicar**: Todo output en templates.

#### Verificación de Nonce
**Aplicación**:
- `wp_create_nonce()` al crear formularios/AJAX
- `wp_verify_nonce()` al procesar solicitudes
- Nonces únicos por acción

**Dónde aplicar**: Todos los endpoints AJAX y formularios.

#### Verificación de Capabilities
**Aplicación**:
- `current_user_can()` antes de operaciones sensibles
- Capabilities apropiadas según acción (edit_posts, manage_options, etc.)

**Dónde aplicar**: Operaciones que modifican datos o acceden a información restringida.

#### Consultas Preparadas
**Aplicación**:
- `$wpdb->prepare()` para queries personalizadas
- Placeholders `%s`, `%d`, `%f` según tipo de dato
- Nunca concatenar variables directamente en SQL

**Dónde aplicar**: Todas las consultas personalizadas a la base de datos.

### 1.6 WordPress Coding Standards

#### WPCS (WordPress Coding Standards)
**Herramienta**: PHP_CodeSniffer con ruleset de WordPress

**Aspectos clave**:
- Indentación con tabs
- Espacios alrededor de operadores
- Llaves en nueva línea para funciones/clases
- Llaves en misma línea para estructuras de control
- Nombres de funciones en snake_case
- Nombres de clases en PascalCase
- Constantes en UPPERCASE

#### PSR-12 Compatibility
**Aplicación**:
- Declaración de tipos en parámetros
- Return types cuando sea posible
- Visibilidad explícita (public, private, protected)
- Declaración de propiedades con tipos

#### Namespacing
**Aplicación**:
- Un namespace por archivo
- Namespace refleja estructura de carpetas
- Use statements al inicio del archivo
- Alias cuando hay conflictos de nombres

#### Hooks Naming
**Convención**: `{plugin_prefix}_{hook_type}_{context}`

**Ejemplos**:
- `travel_blocks_before_render_{block_name}`
- `travel_blocks_enqueue_assets_{block_name}`
- `travel_blocks_filter_data_{block_name}`

### 1.7 Compatibilidad e Internacionalización

#### i18n (Internationalization)
**Aplicación**:
- Text domain único por plugin
- Usar funciones de traducción: `__()`, `_e()`, `_n()`, `_x()`
- Cargar traducciones en init hook
- Nunca concatenar traducciones

**Estructura**:
```php
__('Text to translate', 'travel-blocks')
_e('Text to output', 'travel-blocks')
_n('Singular', 'Plural', $count, 'travel-blocks')
```

#### l10n (Localization)
**Aplicación**:
- Archivos .pot para strings extraídos
- Archivos .po/.mo por idioma
- Formato de fechas con `date_i18n()`
- Formato de números con `number_format_i18n()`

#### Backward Compatibility
**Aplicación**:
- Deprecation notices antes de eliminar funcionalidad
- Mantener compatibilidad con versión anterior de WordPress (mínimo 2 versiones)
- Versionado semántico (MAJOR.MINOR.PATCH)

### 1.8 Herramientas Recomendadas

#### Composer
**Uso**: Gestión de dependencias PHP

**Aplicación**:
- Autoloading PSR-4
- Dependencias de desarrollo (PHPCS, PHPStan)
- Librerías externas si son necesarias

#### PHPCS (PHP_CodeSniffer)
**Uso**: Validación de coding standards

**Configuración**:
```json
{
  "phpcs.standard": "WordPress",
  "phpcs.ignore": ["vendor/", "node_modules/"]
}
```

#### PHPStan
**Uso**: Análisis estático de código

**Nivel recomendado**: 5-6 para balance entre rigor y practicidad

**Aplicación**:
- Detección de errores de tipos
- Identificación de código muerto
- Validación de DocBlocks

#### WP-CLI
**Uso**: Operaciones de WordPress desde línea de comandos

**Aplicación**:
- Scaffold de bloques: `wp scaffold block`
- Limpieza de caché
- Importación/exportación de datos
- Testing automatizado

#### Query Monitor
**Uso**: Debugging en desarrollo

**Monitoreo**:
- Queries de base de datos
- Hooks ejecutados
- Tiempo de carga
- Errores PHP

### 1.9 Gestión de Assets (CSS/JS)

#### Principio de Independencia
**Aplicación**:
- Cada bloque tiene su propio archivo CSS en `assets/blocks/{block-name}.css`
- Enqueue condicional: solo cargar cuando el bloque está presente
- No depender de CSS global del tema

#### Uso de Variables de theme.json
**Variables disponibles**:
- Colores: `var(--wp--preset--color--{slug})`
- Tipografía: `var(--wp--preset--font-size--{slug})`
- Espaciado: `var(--wp--preset--spacing--{slug})`
- Familia de fuente: `var(--wp--preset--font-family--{slug})`

**Beneficio**: Consistencia visual sin dependencias hard-coded.

#### Selectores Específicos
**Patrón recomendado**:
```css
.wp-block-travel-{plugin}-{block-name} {
  /* Estilos del bloque */
}

.wp-block-travel-{plugin}-{block-name} .component-name {
  /* Estilos de componente interno */
}
```

**Evitar**: Selectores genéricos como `.button`, `.card`, `.container`.

#### Enqueue Condicional
**Método 1**: En la clase del bloque
```php
public function enqueue_assets() {
    if (has_block('travel-blocks/block-name')) {
        wp_enqueue_style(
            'travel-blocks-block-name',
            plugins_url('assets/blocks/block-name.css', __FILE__),
            [],
            filemtime(plugin_dir_path(__FILE__) . 'assets/blocks/block-name.css')
        );
    }
}
```

**Método 2**: Block.json (recomendado para bloques Gutenberg)
```json
{
  "style": "file:./assets/blocks/block-name.css",
  "editorStyle": "file:./assets/blocks/block-name-editor.css"
}
```

### 1.10 Renderizado de Bloques

#### Template Pattern
**Estructura**:
1. Clase de bloque obtiene datos
2. Clase prepara datos para template
3. Template recibe datos y renderiza HTML
4. Lógica mínima en template (solo loops y condicionales simples)

#### Separación de Responsabilidades
**Controller (Clase de Bloque)**:
- Obtener datos de ACF/post meta
- Procesar y validar datos
- Preparar array de datos para template

**View (Template PHP)**:
- Recibir datos preparados
- Renderizar HTML
- Escapar output
- No contener lógica de negocio

#### ACF Integration
**Registro de campos**:
- PHP (recomendado): Mejor para versionado y deployment
- JSON: Opción alternativa, exportar desde UI

**Obtención de valores**:
```php
$value = get_field('field_name', $post_id);
```

**Validación**:
- Siempre verificar existencia antes de usar
- Valores por defecto cuando campo está vacío
- Type checking apropiado

---

## Parte 2: Metodología de Auditoría por Bloque

### 2.1 Proceso de Auditoría Individual

Para cada bloque se debe seguir este proceso sistemático:

#### Paso 1: Análisis de Funcionalidad Actual
**Acciones**:
1. Identificar el archivo principal del bloque
2. Leer la clase completa del bloque
3. Identificar qué hace el bloque (propósito y alcance)
4. Documentar inputs esperados (ACF fields, props, etc.)
5. Documentar outputs generados (HTML, datos procesados)

#### Paso 2: Mapeo de Conexiones
**Acciones**:
1. Identificar template asociado (si existe)
2. Listar todos los campos ACF utilizados
3. Identificar dependencias con otros plugins/servicios
4. Mapear hooks utilizados (actions y filters)
5. Identificar assets cargados (CSS, JS)

#### Paso 3: Análisis de Renderizado en Editor
**Acciones**:
1. Verificar cómo se registra el bloque (ACF vs Gutenberg nativo)
2. Identificar preview/render callback
3. Revisar configuración de block.json (si existe)
4. Documentar opciones disponibles en el editor
5. Identificar JavaScript específico del editor

#### Paso 4: Análisis de Renderizado en Frontend
**Acciones**:
1. Identificar template utilizado para frontend
2. Revisar lógica de preparación de datos
3. Documentar estructura HTML generada
4. Identificar CSS específico aplicado
5. Verificar JavaScript frontend (si existe)

#### Paso 5: Análisis de Dependencias CSS
**Acciones**:
1. Identificar si usa global.css
2. Listar variables CSS utilizadas
3. Verificar si usa variables de theme.json
4. Identificar selectores CSS propios del bloque
5. Detectar posibles conflictos con otros bloques

#### Paso 6: Identificación de Riesgos
**Acciones**:
1. Identificar código que depende de global.css
2. Detectar lógica compleja o difícil de mantener
3. Identificar funciones sin uso
4. Detectar problemas de seguridad (sanitización, escapado, nonces)
5. Identificar código duplicado con otros bloques

#### Paso 7: Recomendaciones de Mejora
**Acciones**:
1. Proponer refactorización de CSS (eliminar dependencia de global.css)
2. Sugerir mejoras de arquitectura (SOLID, Clean Code)
3. Identificar código a eliminar (funciones sin uso)
4. Proponer mejoras de seguridad
5. Sugerir optimizaciones de rendimiento

### 2.2 Template de Auditoría por Bloque

```markdown
### Bloque: [Nombre del Bloque]

#### Información General
- **Ubicación**: [Ruta del archivo principal]
- **Namespace**: [Namespace de la clase]
- **Template**: [Ruta del template si existe]
- **Tipo**: [ACF / Gutenberg Nativo]

#### Funcionalidad Actual
**Propósito**: [Descripción de qué hace el bloque]

**Inputs**:
- [Listar campos ACF, props, parámetros]

**Outputs**:
- [Descripción del HTML generado o datos procesados]

#### Conexiones y Dependencias

**Template**:
- Archivo: [Ruta]
- Datos recibidos: [Lista de variables pasadas al template]

**Campos ACF** (si aplica):
- [Campo 1]: [Tipo] - [Descripción]
- [Campo 2]: [Tipo] - [Descripción]
- ...

**Dependencias Externas**:
- [Plugins, servicios, APIs utilizados]

**Hooks Utilizados**:
- Actions: [Lista de actions]
- Filters: [Lista de filters]

**Assets**:
- CSS: [Archivos CSS cargados]
- JS: [Archivos JS cargados]

#### Renderizado en Editor

**Método de Registro**:
- [ACF register_block_type / registerBlockType de Gutenberg]

**Configuración**:
- [Opciones disponibles en inspector de bloque]
- [Preview mode: auto/edit/none]

**Block.json** (si existe):
- [Configuración relevante]

#### Renderizado en Frontend

**Template Utilizado**: [Ruta]

**Preparación de Datos**:
- [Describir proceso de obtención y procesamiento de datos]

**Estructura HTML**:
- [Descripción de la estructura generada]
- [Clases CSS principales]

**JavaScript Frontend**:
- [Funcionalidad JS si existe]

#### Análisis de CSS

**Dependencia de global.css**:
- [ ] Sí / [ ] No
- Variables utilizadas de global.css: [Lista]

**Variables de theme.json utilizadas**:
- [Lista de variables de theme.json si las hay]

**CSS Específico del Bloque**:
- Archivo: [Ruta si existe]
- Selectores principales: [Lista]

**Posibles Conflictos**:
- [Selectores genéricos que pueden conflictuar con otros bloques]

#### Identificación de Riesgos

**Riesgos de Funcionalidad**:
1. [Riesgo 1: Descripción y por qué es un riesgo]
2. [Riesgo 2: ...]

**Problemas de Arquitectura**:
1. [Problema 1: Violación de principio SOLID, código duplicado, etc.]
2. [Problema 2: ...]

**Problemas de Seguridad**:
1. [Problema 1: Falta sanitización, escapado, etc.]
2. [Problema 2: ...]

**Código Sin Uso**:
- [Métodos o funciones que no se utilizan]

**Complejidad Innecesaria**:
- [Lógica que se puede simplificar]

#### Recomendaciones de Mejora

**CSS**:
1. [Recomendación 1: Eliminar uso de variable X de global.css y usar Y de theme.json]
2. [Recomendación 2: Crear archivo CSS específico con selectores únicos]
3. ...

**Arquitectura**:
1. [Recomendación 1: Aplicar SRP separando lógica en métodos específicos]
2. [Recomendación 2: Eliminar método X que no se utiliza]
3. ...

**Seguridad**:
1. [Recomendación 1: Sanitizar input X con sanitize_text_field()]
2. [Recomendación 2: Escapar output Y con esc_html()]
3. ...

**Mantenibilidad**:
1. [Recomendación 1: Simplificar lógica de Z para mejorar legibilidad]
2. [Recomendación 2: Extraer lógica repetida a método helper]
3. ...

#### Plan de Acción

**Prioridad**: [Alta / Media / Baja]

**Orden de Implementación**:
1. [Acción 1]
2. [Acción 2]
3. ...

**Precauciones**:
- [Precaución 1: No modificar X porque afecta Y]
- [Precaución 2: Probar Z antes de eliminar A]

**Checklist de Verificación Post-Refactorización**:
- [ ] El bloque aparece en el catálogo de bloques del editor
- [ ] El bloque se puede insertar correctamente
- [ ] Los campos ACF se muestran en el inspector (si aplica)
- [ ] El preview en el editor funciona correctamente
- [ ] El bloque renderiza correctamente en frontend
- [ ] Los estilos se aplican correctamente
- [ ] No hay errores en consola de JavaScript
- [ ] No hay errores PHP en logs
- [ ] El bloque mantiene su funcionalidad original
- [ ] Los estilos no afectan a otros bloques
```

---

## Parte 3: Auditoría por Plugin

### Plugin 1: Travel Blocks ACF (Bloques Generales)

**Ubicación**: `/wp-content/plugins/travel-blocks/src/Blocks/ACF/`

**Total de Bloques**: 15

**Lista de Bloques a Auditar**:

1. Breadcrumb
2. ContactForm
3. FAQAccordion
4. FlexibleGridCarousel
5. HeroCarousel
6. HeroSection
7. PostsCarouselNative
8. PostsCarousel
9. PostsListAdvanced
10. SideBySideCards
11. StaticCTA
12. StaticHero
13. StickySideMenu
14. TaxonomyTabs
15. TeamCarousel

**Proceso**:
- Auditar cada bloque individualmente siguiendo el template del apartado 2.2
- Documentar hallazgos específicos por bloque
- Identificar patrones comunes de problemas
- Proponer soluciones específicas y generales

---

### Plugin 2: Travel Blocks Package

**Ubicación**: `/wp-content/plugins/travel-blocks/src/Blocks/Package/`

**Total de Bloques**: 21

**Lista de Bloques a Auditar**:

1. ContactPlannerForm
2. CTABanner
3. DatesAndPrices
4. FAQAccordion
5. ImpactSection
6. InclusionsExclusions
7. ItineraryDayByDay
8. MetadataLine
9. PackageMap
10. PackagesByLocation
11. PackageVideo
12. PricingCard
13. ProductGalleryHero
14. ProductMetadata
15. PromoCard
16. QuickFacts
17. RelatedPackages
18. RelatedPostsGrid
19. ReviewsCarousel
20. TravelerReviews
21. TrustBadges

**Proceso**:
- Auditar cada bloque individualmente siguiendo el template del apartado 2.2
- Documentar hallazgos específicos por bloque
- Identificar patrones comunes de problemas
- Proponer soluciones específicas y generales

---

### Plugin 3: Travel Blocks Deal

**Ubicación**: `/wp-content/plugins/travel-blocks/src/Blocks/Deal/`

**Total de Bloques**: 3

**Lista de Bloques a Auditar**:

1. DealInfoCard
2. DealPackagesGrid
3. DealsSlider

**Proceso**:
- Auditar cada bloque individualmente siguiendo el template del apartado 2.2
- Documentar hallazgos específicos por bloque
- Identificar patrones comunes de problemas
- Proponer soluciones específicas y generales

---

### Plugin 4: Travel Blocks Template

**Ubicación**: `/wp-content/plugins/travel-blocks/src/Blocks/Template/`

**Total de Bloques**: 6

**Lista de Bloques a Auditar**:

1. Breadcrumb
2. FAQAccordion
3. HeroMediaGrid
4. PackageHeader
5. PromoCards
6. TaxonomyArchiveHero

**Proceso**:
- Auditar cada bloque individualmente siguiendo el template del apartado 2.2
- Documentar hallazgos específicos por bloque
- Identificar patrones comunes de problemas
- Proponer soluciones específicas y generales

---

## Parte 4: Checklist de Verificación Final por Plugin

### Travel Blocks ACF

**Funcionalidad**:
- [ ] Todos los bloques aparecen en el catálogo de bloques
- [ ] Todos los bloques se pueden insertar correctamente
- [ ] Los campos ACF se muestran en el inspector para cada bloque
- [ ] El preview funciona correctamente en todos los bloques
- [ ] Todos los bloques renderizan correctamente en frontend
- [ ] No hay errores PHP en logs relacionados con estos bloques
- [ ] No hay errores JavaScript en consola

**Estilos**:
- [ ] Ningún bloque depende de global.css
- [ ] Todos los bloques usan variables de theme.json apropiadamente
- [ ] Los bloques que requieren CSS específico tienen su propio archivo
- [ ] No hay conflictos de selectores entre bloques
- [ ] Los estilos se cargan solo cuando el bloque está presente

**Arquitectura**:
- [ ] Las clases siguen el namespace correcto
- [ ] Se aplican principios SOLID
- [ ] No hay código duplicado entre bloques
- [ ] No hay funciones sin uso
- [ ] La lógica es clara y mantenible

**Seguridad**:
- [ ] Todos los inputs están sanitizados
- [ ] Todos los outputs están escapados correctamente
- [ ] Los nonces se verifican en operaciones AJAX/formularios
- [ ] Las capabilities se verifican apropiadamente

**Templates**:
- [ ] Los templates están en la ubicación correcta
- [ ] Los templates reciben datos preparados (no lógica de negocio)
- [ ] Los templates son reutilizables y claros

---

### Travel Blocks Package

**Funcionalidad**:
- [ ] Todos los bloques aparecen en el catálogo de bloques
- [ ] Todos los bloques se pueden insertar correctamente
- [ ] Los campos ACF se muestran en el inspector para cada bloque
- [ ] El preview funciona correctamente en todos los bloques
- [ ] Todos los bloques renderizan correctamente en frontend
- [ ] No hay errores PHP en logs relacionados con estos bloques
- [ ] No hay errores JavaScript en consola
- [ ] La funcionalidad de booking wizard se mantiene intacta
- [ ] La visualización de fechas y precios funciona correctamente
- [ ] Los estados SOLD OUT se muestran apropiadamente

**Estilos**:
- [ ] Ningún bloque depende de global.css
- [ ] Todos los bloques usan variables de theme.json apropiadamente
- [ ] Los bloques que requieren CSS específico tienen su propio archivo
- [ ] No hay conflictos de selectores entre bloques
- [ ] Los estilos se cargan solo cuando el bloque está presente
- [ ] Los estilos del booking wizard funcionan correctamente

**Arquitectura**:
- [ ] Las clases siguen el namespace correcto
- [ ] Se aplican principios SOLID
- [ ] No hay código duplicado entre bloques
- [ ] No hay funciones sin uso
- [ ] La lógica es clara y mantenible
- [ ] La integración con ACF es consistente

**Seguridad**:
- [ ] Todos los inputs están sanitizados
- [ ] Todos los outputs están escapados correctamente
- [ ] Los nonces se verifican en operaciones AJAX/formularios
- [ ] Las capabilities se verifican apropiadamente

**Templates**:
- [ ] Los templates están en la ubicación correcta
- [ ] Los templates reciben datos preparados (no lógica de negocio)
- [ ] Los templates son reutilizables y claros
- [ ] La estructura HTML mantiene compatibilidad con funcionalidad actual

---

### Travel Blocks Deal

**Funcionalidad**:
- [ ] Todos los bloques aparecen en el catálogo de bloques
- [ ] Todos los bloques se pueden insertar correctamente
- [ ] Los campos ACF se muestran en el inspector para cada bloque
- [ ] El preview funciona correctamente en todos los bloques
- [ ] Todos los bloques renderizan correctamente en frontend
- [ ] No hay errores PHP en logs relacionados con estos bloques
- [ ] No hay errores JavaScript en consola
- [ ] La funcionalidad de promociones se mantiene intacta
- [ ] Los sliders funcionan correctamente

**Estilos**:
- [ ] Ningún bloque depende de global.css
- [ ] Todos los bloques usan variables de theme.json apropiadamente
- [ ] Los bloques que requieren CSS específico tienen su propio archivo
- [ ] No hay conflictos de selectores entre bloques
- [ ] Los estilos se cargan solo cuando el bloque está presente

**Arquitectura**:
- [ ] Las clases siguen el namespace correcto
- [ ] Se aplican principios SOLID
- [ ] No hay código duplicado entre bloques
- [ ] No hay funciones sin uso
- [ ] La lógica es clara y mantenible

**Seguridad**:
- [ ] Todos los inputs están sanitizados
- [ ] Todos los outputs están escapados correctamente
- [ ] Los nonces se verifican en operaciones AJAX/formularios
- [ ] Las capabilities se verifican apropiadamente

**Templates**:
- [ ] Los templates están en la ubicación correcta
- [ ] Los templates reciben datos preparados (no lógica de negocio)
- [ ] Los templates son reutilizables y claros

---

### Travel Blocks Template

**Funcionalidad**:
- [ ] Todos los bloques aparecen en el catálogo de bloques
- [ ] Todos los bloques se pueden insertar correctamente
- [ ] Los campos ACF se muestran en el inspector para cada bloque (si aplica)
- [ ] El preview funciona correctamente en todos los bloques
- [ ] Todos los bloques renderizan correctamente en frontend
- [ ] No hay errores PHP en logs relacionados con estos bloques
- [ ] No hay errores JavaScript en consola

**Estilos**:
- [ ] Ningún bloque depende de global.css
- [ ] Todos los bloques usan variables de theme.json apropiadamente
- [ ] Los bloques que requieren CSS específico tienen su propio archivo
- [ ] No hay conflictos de selectores entre bloques
- [ ] Los estilos se cargan solo cuando el bloque está presente

**Arquitectura**:
- [ ] Las clases siguen el namespace correcto
- [ ] Se aplican principios SOLID
- [ ] No hay código duplicado entre bloques
- [ ] No hay funciones sin uso
- [ ] La lógica es clara y mantenible

**Seguridad**:
- [ ] Todos los inputs están sanitizados
- [ ] Todos los outputs están escapados correctamente
- [ ] Los nonces se verifican en operaciones AJAX/formularios (si aplica)
- [ ] Las capabilities se verifican apropiadamente

**Templates**:
- [ ] Los templates están en la ubicación correcta
- [ ] Los templates reciben datos preparados (no lógica de negocio)
- [ ] Los templates son reutilizables y claros

---

## Parte 5: Fases de Implementación

### Fase 1: Auditoría Inicial (Análisis sin modificaciones)

**Duración estimada**: 20-25 horas

**Objetivo**: Completar el análisis de todos los bloques sin realizar modificaciones al código.

**Actividades**:

1. **Auditoría de Travel Blocks ACF** (15 bloques)
   - Aplicar template de auditoría a cada bloque
   - Documentar hallazgos en archivo individual por bloque
   - Identificar patrones comunes
   - Tiempo estimado: 6-8 horas

2. **Auditoría de Travel Blocks Package** (21 bloques)
   - Aplicar template de auditoría a cada bloque
   - Documentar hallazgos en archivo individual por bloque
   - Identificar patrones comunes
   - Tiempo estimado: 8-10 horas

3. **Auditoría de Travel Blocks Deal** (3 bloques)
   - Aplicar template de auditoría a cada bloque
   - Documentar hallazgos en archivo individual por bloque
   - Identificar patrones comunes
   - Tiempo estimado: 2-3 horas

4. **Auditoría de Travel Blocks Template** (6 bloques)
   - Aplicar template de auditoría a cada bloque
   - Documentar hallazgos en archivo individual por bloque
   - Identificar patrones comunes
   - Tiempo estimado: 3-4 horas

5. **Consolidación de hallazgos**
   - Crear documento resumen con problemas comunes
   - Identificar mejoras globales aplicables
   - Priorizar bloques según criticidad
   - Tiempo estimado: 1-2 horas

**Entregable**:
- Documento de auditoría por cada uno de los 45 bloques
- Documento resumen con hallazgos globales
- Plan de priorización de refactorización

---

### Fase 2: Refactorización de Travel Blocks ACF

**Duración estimada**: 8-10 horas

**Objetivo**: Implementar mejoras en los 15 bloques ACF generales.

**Actividades por bloque**:

1. **Revisión de auditoría del bloque**
   - Leer documento de auditoría
   - Identificar acciones específicas a realizar

2. **Refactorización de CSS**
   - Eliminar dependencias de global.css
   - Migrar a variables de theme.json
   - Crear archivo CSS específico si es necesario
   - Aplicar selectores únicos

3. **Refactorización de arquitectura**
   - Aplicar principios SOLID
   - Eliminar código sin uso
   - Simplificar lógica compleja
   - Separar responsabilidades

4. **Mejoras de seguridad**
   - Implementar sanitización faltante
   - Implementar escapado faltante
   - Verificar nonces y capabilities

5. **Documentación de cambios**
   - Actualizar documento de auditoría con cambios realizados
   - Marcar items del checklist de verificación

**Orden sugerido** (de menor a mayor complejidad):
1. Breadcrumb
2. StaticCTA
3. StickySideMenu
4. SideBySideCards
5. ContactForm
6. FAQAccordion
7. TaxonomyTabs
8. StaticHero
9. HeroSection
10. TeamCarousel
11. PostsListAdvanced
12. PostsCarousel
13. PostsCarouselNative
14. FlexibleGridCarousel
15. HeroCarousel

---

### Fase 3: Refactorización de Travel Blocks Package

**Duración estimada**: 12-15 horas

**Objetivo**: Implementar mejoras en los 21 bloques de Package.

**Actividades por bloque**:

1. **Revisión de auditoría del bloque**
   - Leer documento de auditoría
   - Identificar acciones específicas a realizar

2. **Refactorización de CSS**
   - Eliminar dependencias de global.css
   - Migrar a variables de theme.json
   - Crear archivo CSS específico si es necesario
   - Aplicar selectores únicos

3. **Refactorización de arquitectura**
   - Aplicar principios SOLID
   - Eliminar código sin uso
   - Simplificar lógica compleja
   - Separar responsabilidades

4. **Mejoras de seguridad**
   - Implementar sanitización faltante
   - Implementar escapado faltante
   - Verificar nonces y capabilities

5. **Documentación de cambios**
   - Actualizar documento de auditoría con cambios realizados
   - Marcar items del checklist de verificación

**Orden sugerido** (de menor a mayor complejidad):
1. MetadataLine
2. ProductMetadata
3. QuickFacts
4. TrustBadges
5. PromoCard
6. CTABanner
7. ImpactSection
8. PackageVideo
9. ContactPlannerForm
10. RelatedPostsGrid
11. RelatedPackages
12. PricingCard
13. PackagesByLocation
14. FAQAccordion
15. ReviewsCarousel
16. TravelerReviews
17. InclusionsExclusions
18. PackageMap
19. ProductGalleryHero
20. ItineraryDayByDay
21. DatesAndPrices

**Precaución especial**:
- DatesAndPrices: mantener funcionalidad del booking wizard
- ItineraryDayByDay: lógica compleja de acordeón
- ProductGalleryHero: integración con galería de medios

---

### Fase 4: Refactorización de Travel Blocks Deal

**Duración estimada**: 2-3 horas

**Objetivo**: Implementar mejoras en los 3 bloques de Deal.

**Actividades por bloque**:

1. **Revisión de auditoría del bloque**
   - Leer documento de auditoría
   - Identificar acciones específicas a realizar

2. **Refactorización de CSS**
   - Eliminar dependencias de global.css
   - Migrar a variables de theme.json
   - Crear archivo CSS específico si es necesario
   - Aplicar selectores únicos

3. **Refactorización de arquitectura**
   - Aplicar principios SOLID
   - Eliminar código sin uso
   - Simplificar lógica compleja
   - Separar responsabilidades

4. **Mejoras de seguridad**
   - Implementar sanitización faltante
   - Implementar escapado faltante
   - Verificar nonces y capabilities

5. **Documentación de cambios**
   - Actualizar documento de auditoría con cambios realizados
   - Marcar items del checklist de verificación

**Orden sugerido**:
1. DealInfoCard
2. DealPackagesGrid
3. DealsSlider

---

### Fase 5: Refactorización de Travel Blocks Template

**Duración estimada**: 4-5 horas

**Objetivo**: Implementar mejoras en los 6 bloques de Template.

**Actividades por bloque**:

1. **Revisión de auditoría del bloque**
   - Leer documento de auditoría
   - Identificar acciones específicas a realizar

2. **Refactorización de CSS**
   - Eliminar dependencias de global.css
   - Migrar a variables de theme.json
   - Crear archivo CSS específico si es necesario
   - Aplicar selectores únicos

3. **Refactorización de arquitectura**
   - Aplicar principios SOLID
   - Eliminar código sin uso
   - Simplificar lógica compleja
   - Separar responsabilidades

4. **Mejoras de seguridad**
   - Implementar sanitización faltante
   - Implementar escapado faltante
   - Verificar nonces y capabilities

5. **Documentación de cambios**
   - Actualizar documento de auditoría con cambios realizados
   - Marcar items del checklist de verificación

**Orden sugerido**:
1. Breadcrumb
2. PromoCards
3. FAQAccordion
4. PackageHeader
5. TaxonomyArchiveHero
6. HeroMediaGrid

---

### Fase 6: Revisión de Componentes del Tema

**Duración estimada**: 3-4 horas

**Objetivo**: Verificar que componentes del tema no dependen de global.css.

**Actividades**:

1. **Auditoría de functions.php**
   - Revisar enqueue de estilos
   - Identificar dependencias de global.css
   - Verificar orden de carga de assets

2. **Auditoría de templates del tema**
   - Header
   - Footer
   - Sidebar
   - Post Meta
   - Otros template parts

3. **Auditoría de patterns y templates personalizados**
   - Verificar si usan variables de global.css
   - Migrar a theme.json si es necesario

4. **Documentación de cambios necesarios**
   - Listar componentes que requieren ajustes
   - Proponer soluciones específicas

---

### Fase 7: Eliminación de global.css

**Duración estimada**: 1-2 horas

**Objetivo**: Eliminar completamente global.css del tema.

**Actividades**:

1. **Verificación final**
   - Confirmar que ningún bloque depende de global.css
   - Confirmar que ningún componente del tema depende de global.css
   - Buscar referencias en código (grep/search)

2. **Eliminación del archivo**
   - Comentar el enqueue en functions.php (no eliminar inmediatamente)
   - Verificar que no hay errores visuales
   - Si todo funciona, eliminar archivo global.css
   - Eliminar línea de enqueue en functions.php

3. **Limpieza de referencias**
   - Buscar y eliminar comentarios o código relacionado con global.css
   - Actualizar documentación

---

### Fase 8: Verificación y Documentación Final

**Duración estimada**: 4-5 horas

**Objetivo**: Verificar que todo funciona correctamente y documentar el estado final.

**Actividades**:

1. **Verificación por plugin**
   - Ejecutar checklist completo de Travel Blocks ACF
   - Ejecutar checklist completo de Travel Blocks Package
   - Ejecutar checklist completo de Travel Blocks Deal
   - Ejecutar checklist completo de Travel Blocks Template

2. **Verificación de tema**
   - Verificar que header/footer renderizan correctamente
   - Verificar que templates del tema funcionan
   - Verificar que no hay errores PHP en logs
   - Verificar que no hay errores JavaScript en consola

3. **Documentación final**
   - Crear documento resumen de cambios realizados
   - Actualizar README de plugins si es necesario
   - Documentar nuevas convenciones de CSS
   - Documentar mejoras de arquitectura implementadas

4. **Entrega**
   - Documento final de auditoría consolidado
   - Documento de cambios implementados
   - Checklist de verificación completado
   - Recomendaciones para mantenimiento futuro

---

## Parte 6: Anexos

### Anexo A: Variables Disponibles en theme.json

**Colores**:
- `--wp--preset--color--primary` (#17565C)
- `--wp--preset--color--primary-80` (#17565CCC)
- `--wp--preset--color--primary-60` (#17565C99)
- `--wp--preset--color--primary-40` (#17565C66)
- `--wp--preset--color--primary-20` (#17565C33)
- `--wp--preset--color--secondary` (#C66E65)
- `--wp--preset--color--secondary-80` (#C66E65CC)
- `--wp--preset--color--secondary-60` (#C66E6599)
- `--wp--preset--color--secondary-40` (#C66E6566)
- `--wp--preset--color--secondary-20` (#C66E6533)
- `--wp--preset--color--tertiary` (#202C2E)
- `--wp--preset--color--pink-pastel` (#FFF6F5)
- `--wp--preset--color--gray` (#666666)
- `--wp--preset--color--base` (#FFFFFF)
- `--wp--preset--color--contrast` (#111111)
- `--wp--preset--color--divider` (#FF0090)
- `--wp--preset--color--complementary-1` (#F3CE72)
- `--wp--preset--color--contrast-1` (#CEA02D)
- `--wp--preset--color--complementary-2` (#9FCC87)
- `--wp--preset--color--contrast-2` (#567943)
- `--wp--preset--color--complementary-3` (#7BC5CC)
- `--wp--preset--color--contrast-3` (#17565C)
- `--wp--preset--color--complementary-4` (#B18BCC)
- `--wp--preset--color--contrast-4` (#311A42)

**Espaciado**:
- `--wp--preset--spacing--20` (0.25rem / 4px)
- `--wp--preset--spacing--30` (0.5rem / 8px)
- `--wp--preset--spacing--40` (0.75rem / 12px)
- `--wp--preset--spacing--50` (1rem / 16px)
- `--wp--preset--spacing--60` (clamp 1.25rem - 1.5rem)
- `--wp--preset--spacing--70` (clamp 1.25rem - 1.75rem)
- `--wp--preset--spacing--80` (clamp 1.5rem - 2rem)
- `--wp--preset--spacing--90` (clamp 2rem - 3rem)
- `--wp--preset--spacing--100` (clamp 2.5rem - 4rem)
- `--wp--preset--spacing--110` (clamp 3rem - 5rem)
- `--wp--preset--spacing--120` (clamp 3.5rem - 6rem)
- `--wp--preset--spacing--130` (clamp 3.5rem - 7rem)

**Tipografía - Tamaños**:
- `--wp--preset--font-size--tiny` (0.75rem)
- `--wp--preset--font-size--small` (0.875rem)
- `--wp--preset--font-size--regular` (1rem)
- `--wp--preset--font-size--medium` (1.25rem fluid)
- `--wp--preset--font-size--extra-medium` (1.5rem fluid)
- `--wp--preset--font-size--large` (1.75rem fluid)
- `--wp--preset--font-size--x-large` (2.25rem fluid)
- `--wp--preset--font-size--xx-large` (2.625rem fluid)
- `--wp--preset--font-size--huge` (3rem fluid)

**Tipografía - Familia**:
- `--wp--preset--font-family--satoshi` (Satoshi, sans-serif)

**Sombras**:
- `--wp--preset--shadow--sombra-sm` (0px 4px 4px 0px rgba(0, 0, 0, 0.25))

### Anexo B: Convención de Nombres de Archivos CSS por Bloque

**Patrón**: `{block-name}.css`

**Ubicación**: `/wp-content/plugins/{plugin-name}/assets/blocks/`

**Ejemplos**:
- `dates-and-prices.css`
- `hero-carousel.css`
- `contact-form.css`

**Selectores base**:
```css
.wp-block-travel-blocks-{block-name} {
  /* Estilos raíz del bloque */
}

.wp-block-travel-blocks-{block-name}__element {
  /* Estilos de elemento hijo (BEM) */
}
```

### Anexo C: Herramientas de Auditoría Recomendadas

**Búsqueda de referencias a global.css**:
```bash
grep -r "global.css" wp-content/plugins/travel-blocks/
grep -r "var(--color-" wp-content/plugins/travel-blocks/
grep -r "var(--font-size-" wp-content/plugins/travel-blocks/
grep -r "var(--spacing-" wp-content/plugins/travel-blocks/
```

**Búsqueda de código sin uso**:
- PHPStan con nivel 5-6
- PHP Code Sniffer con ruleset WordPress
- IDE con análisis estático (PHPStorm)

**Verificación de seguridad**:
```bash
grep -r "echo \$" wp-content/plugins/travel-blocks/templates/
grep -r "\$_POST\[" wp-content/plugins/travel-blocks/
grep -r "\$_GET\[" wp-content/plugins/travel-blocks/
```

**Verificación de enqueue de assets**:
```bash
grep -r "wp_enqueue_style" wp-content/plugins/travel-blocks/
grep -r "wp_enqueue_script" wp-content/plugins/travel-blocks/
```

---

## Notas Finales

### Consideraciones Importantes

1. **No realizar pruebas durante la auditoría**: El usuario realizará todas las pruebas. La auditoría debe enfocarse en análisis y documentación.

2. **No sacar conclusiones sin análisis**: Cada recomendación debe estar basada en hallazgos específicos documentados durante la auditoría.

3. **Mantener funcionalidad en producción**: Todas las refactorizaciones deben preservar la funcionalidad actual. No implementar mejoras que cambien comportamiento visible.

4. **Auditar individualmente, no en masa**: Cada bloque tiene sus particularidades y debe ser analizado de forma específica.

5. **Documentar todo**: La documentación es crítica para entender el estado actual y los cambios propuestos.

### Próximos Pasos

1. Comenzar con la Fase 1: Auditoría Inicial
2. Auditar primer bloque siguiendo el template del apartado 2.2
3. Documentar hallazgos en archivo individual
4. Continuar con los siguientes bloques de forma sistemática
5. Al completar la auditoría de todos los bloques, consolidar hallazgos globales
6. Presentar plan de priorización para refactorización

---

**Documento creado**: 2025-11-09
**Versión**: 1.0
**Total de bloques a auditar**: 45 (15 ACF + 21 Package + 3 Deal + 6 Template)
