# Auditoría de Arquitectura, Lógica y Funcionalidad - Travel Plugins

## Objetivo Principal

Mejorar la arquitectura, lógica y funcionalidad de los plugins de bloques, asegurando:

1. Código limpio, mantenible y escalable
2. Arquitectura sólida basada en principios OOP y SOLID
3. Eliminación de código sin uso o innecesario
4. Simplificación de lógica compleja
5. Mantener funcionalidad actual en producción sin romper nada

---

## Parte 1: Arquitectura Correcta para Plugins de WordPress

### 1.1 Estructura de Archivos y Organización

#### PSR-4 Autoloading Standard

**Principio**: Organización de clases que permite autoloading automático basado en namespaces.

**Estructura recomendada**:
```
plugin-name/
├── src/
│   ├── Blocks/           # Clases de bloques organizadas por dominio
│   │   ├── ACF/         # Bloques ACF generales
│   │   ├── Package/     # Bloques específicos de paquetes
│   │   ├── Deal/        # Bloques de promociones
│   │   └── Template/    # Bloques de plantillas
│   ├── Core/            # Clases base y abstracciones
│   │   ├── BlockBase.php
│   │   ├── Interfaces/
│   │   └── Traits/
│   ├── Services/        # Servicios reutilizables
│   │   ├── AjaxHandler.php
│   │   ├── DataProcessor.php
│   │   └── AssetManager.php
│   ├── Helpers/         # Funciones helper estáticas
│   │   ├── ArrayHelper.php
│   │   └── StringHelper.php
│   └── Repositories/    # Acceso a datos
│       ├── PackageRepository.php
│       └── DealRepository.php
├── templates/           # Archivos de vista PHP
├── assets/
│   ├── blocks/         # CSS/JS por bloque
│   └── shared/         # Recursos compartidos
├── languages/          # Archivos de traducción
├── vendor/             # Dependencias de Composer
├── composer.json       # Definición de autoload y dependencias
└── plugin-name.php     # Archivo principal (bootstrap)
```

**Beneficios**:
- Autoloading automático sin `require` manual
- Organización clara por responsabilidades
- Fácil navegación y mantenimiento
- Escalabilidad al agregar nuevas funcionalidades

**Namespace Convention**: `Travel\PluginName\Category\Class`

Ejemplo: `Travel\Blocks\Package\DatesAndPrices`

#### Separación de Responsabilidades

**Principio**: Cada carpeta tiene una responsabilidad clara.

**src/Blocks/**: Solo clases de bloques (registro y configuración)
**src/Core/**: Clases base, interfaces y traits compartidos
**src/Services/**: Lógica de negocio reutilizable
**src/Helpers/**: Funciones utilitarias sin estado
**src/Repositories/**: Acceso y manipulación de datos
**templates/**: Solo presentación (HTML con datos escapados)

---

### 1.2 Patrones de Diseño en WordPress Plugins

#### 1.2.1 Singleton Pattern

**Definición**: Garantizar que una clase tenga solo una instancia con punto de acceso global.

**Cuándo usar**:
- Clase principal del plugin
- Gestor de bloques
- Gestor de assets
- Servicios que deben ser únicos (Logger, Config)

**Cuándo NO usar**:
- Clases de bloques individuales (deben instanciarse por separado)
- Clases de datos (models)
- Helpers sin estado

**Estructura típica**:
- Constructor privado
- Propiedad estática `$instance`
- Método estático `instance()` que retorna la única instancia
- Prevenir clonación (`__clone` privado)
- Prevenir deserialización (`__wakeup` privado)

**Beneficios**:
- Control centralizado
- Prevención de duplicación de recursos
- Punto único de inicialización

**Riesgos**:
- Puede dificultar testing si se abusa
- Crea acoplamiento global si no se usa correctamente

#### 1.2.2 Factory Pattern

**Definición**: Delegación de la creación de objetos a una clase factory.

**Cuándo usar**:
- Crear instancias de bloques dinámicamente
- Cuando la lógica de creación es compleja
- Cuando se necesita crear diferentes tipos basados en configuración

**Estructura típica**:
- Método `create($type, $config)` que retorna instancia apropiada
- Switch/strategy pattern para determinar qué clase instanciar
- Configuración centralizada

**Beneficios**:
- Desacoplamiento de código cliente de clases concretas
- Facilita agregar nuevos tipos sin modificar código existente
- Centraliza lógica de creación

**Aplicación en bloques**:
- BlockFactory para crear bloques según tipo ACF vs Gutenberg
- Registro masivo de bloques desde configuración

#### 1.2.3 Observer Pattern (WordPress Hooks)

**Definición**: Sistema de notificación donde objetos observan y reaccionan a eventos.

**Implementación en WordPress**:
- **Actions** (do_action): Ejecutar código en puntos específicos
- **Filters** (apply_filters): Modificar datos antes de usarlos

**Cuándo usar**:
- Extensibilidad sin modificar código core
- Permitir que otros plugins modifiquen comportamiento
- Desacoplar componentes

**Convención de nombres**:
```
{plugin_prefix}_{action_type}_{context}_{specific_detail}
```

Ejemplos:
- `travel_blocks_before_render_dates_and_prices`
- `travel_blocks_after_save_package_data`
- `travel_blocks_filter_departure_dates`

**Beneficios**:
- Extensibilidad nativa de WordPress
- Desacoplamiento total
- Compatibilidad con ecosistema WordPress

**Consideraciones**:
- Documentar todos los hooks disponibles
- Usar prioridades apropiadas
- Pasar datos relevantes como parámetros

#### 1.2.4 MVC Pattern (Model-View-Controller)

**Definición**: Separación de datos, presentación y lógica de control.

**Aplicación en bloques WordPress**:

**Model** (Datos):
- Clases Repository para acceso a datos
- Métodos para obtener posts, ACF fields, taxonomías
- Validación de datos
- NO contiene lógica de presentación

**View** (Presentación):
- Templates PHP en carpeta `templates/`
- Reciben datos ya preparados
- Solo estructuras de control simples (if, foreach)
- Toda salida debe estar escapada
- NO contienen lógica de negocio

**Controller** (Coordinación):
- Clases de bloques en `src/Blocks/`
- Obtienen datos del modelo
- Procesan y preparan datos para la vista
- Deciden qué vista renderizar
- Manejan lógica de negocio

**Beneficios**:
- Separación clara de responsabilidades
- Testabilidad mejorada
- Reutilización de componentes
- Mantenibilidad a largo plazo

**Anti-patrón común en WordPress**:
- Templates con queries directas a DB
- Bloques con HTML hardcodeado en PHP
- Lógica de negocio mezclada con presentación

#### 1.2.5 Service Container / Dependency Injection

**Definición**: Contenedor que gestiona creación y resolución de dependencias.

**Cuándo usar**:
- Bloques que necesitan servicios (AJAX handler, logger, cache)
- Testing con mock objects
- Facilitar cambio de implementaciones

**Dependency Injection Manual**:
```
Constructor recibe dependencias como parámetros
No instanciar dependencias internamente
Permite inyectar mocks en tests
```

**Service Container**:
```
Registro centralizado de servicios
Resolución automática de dependencias
Lazy loading de servicios
```

**Beneficios**:
- Bajo acoplamiento
- Alta testabilidad
- Flexibilidad en configuración
- Cumple DIP (Dependency Inversion Principle)

**Aplicación en bloques**:
- Inyectar AssetManager en lugar de llamar wp_enqueue_* directamente
- Inyectar DataProcessor en lugar de lógica inline
- Inyectar AjaxHandler para manejo de peticiones AJAX

#### 1.2.6 Repository Pattern

**Definición**: Abstracción de acceso a datos que encapsula queries.

**Cuándo usar**:
- Acceso a custom post types
- Queries complejas repetitivas
- Cuando se necesita cambiar fuente de datos

**Estructura típica**:
- Métodos descriptivos: `findById()`, `findByLocation()`, `getActive()`
- Encapsula WP_Query, get_posts, get_field
- Retorna objetos de dominio o arrays estructurados
- NO retorna objetos WP directamente

**Beneficios**:
- Queries centralizadas y reutilizables
- Fácil de testear (mockear repository)
- Cambiar implementación sin afectar consumidores
- Cacheo centralizado

**Aplicación en Travel Plugins**:
- PackageRepository: Obtener paquetes por ubicación, fecha, categoría
- DealRepository: Obtener promociones activas
- ReviewRepository: Obtener reseñas de viajeros

---

### 1.3 Principios SOLID

#### S - Single Responsibility Principle (Responsabilidad Única)

**Definición**: Una clase debe tener una única razón para cambiar.

**Aplicación correcta**:
- **Clase de Bloque**: Solo registro, configuración y orquestación
- **Clase Repository**: Solo acceso a datos
- **Clase Service**: Solo una funcionalidad de negocio específica
- **Clase Helper**: Solo utilidades relacionadas

**Violaciones comunes a evitar**:
- Bloque que registra, renderiza, procesa AJAX, y gestiona assets en un solo archivo
- Clase que hace queries, procesa datos, y genera HTML
- Método que valida, sanitiza, guarda, y envía email

**Cómo identificar violación**:
- Clase con más de 300-400 líneas
- Clase que usa diferentes APIs de WordPress (posts, users, options, transients)
- Métodos con múltiples responsabilidades

**Refactorización**:
- Extraer responsabilidades a clases separadas
- Inyectar servicios en lugar de implementar inline
- Usar composition en lugar de herencia masiva

#### O - Open/Closed Principle (Abierto/Cerrado)

**Definición**: Abierto para extensión, cerrado para modificación.

**Aplicación correcta**:
- **Clase base abstracta** que define interfaz común
- **Hooks estratégicos** que permiten modificar comportamiento
- **Configuración externa** en lugar de hardcodear valores
- **Strategy pattern** para comportamientos variables

**Violaciones comunes a evitar**:
- Modificar clase base cada vez que se agrega nuevo tipo de bloque
- Switch/case que crece con cada nueva funcionalidad
- Constantes hardcodeadas que requieren editar código

**Cómo cumplir en bloques**:
- BlockBase abstracto con métodos que heredan bloques
- Filtros antes/después de renderizado
- Configuración en arrays o archivos separados
- Interfaces para comportamientos opcionales

**Beneficios**:
- Agregar funcionalidad sin tocar código existente
- Menor riesgo de romper lo que ya funciona
- Facilita testing de nuevas features

#### L - Liskov Substitution Principle (Sustitución de Liskov)

**Definición**: Objetos de subclases deben poder reemplazar objetos de la clase base sin alterar comportamiento.

**Aplicación correcta**:
- Todos los bloques que heredan de BlockBase deben implementar métodos base
- Métodos heredados deben mantener contratos (tipos de parámetros, retorno)
- No romper precondiciones o poscondiciones de clase padre

**Violaciones comunes a evitar**:
- Sobrescribir método padre y cambiar signature
- Método heredado que lanza excepción cuando padre no lo hace
- Subclase que ignora comportamiento de método padre

**Cómo cumplir**:
- Definir contratos claros en clase base (abstract methods, docblocks)
- Type hints para parámetros y return types
- Respetar expectativas de métodos heredados

**Indicador de violación**:
- Necesidad de `instanceof` checks para decidir comportamiento
- Casos especiales para subclases específicas

#### I - Interface Segregation Principle (Segregación de Interfaces)

**Definición**: No forzar implementación de métodos que no se usan.

**Aplicación correcta**:
- **Interfaces pequeñas y específicas** en lugar de monolíticas
- **Composición de interfaces** para funcionalidad compleja
- **Interfaces opcionales** según capacidades del bloque

**Ejemplos de interfaces segregadas**:
- `Renderable`: Solo método `render()`
- `HasAssets`: Solo métodos `enqueue_styles()`, `enqueue_scripts()`
- `HasACF`: Solo métodos `get_field_config()`, `register_fields()`
- `Ajaxable`: Solo métodos `register_ajax_handlers()`, `handle_ajax()`

**Violaciones comunes a evitar**:
- Interfaz `BlockInterface` con 15 métodos que no todos los bloques necesitan
- Forzar implementación de `has_ajax()` en bloques sin AJAX
- Métodos vacíos o que retornan null porque no aplican

**Cómo cumplir**:
- Bloques implementan solo interfaces que necesitan
- Interfaces con 1-3 métodos relacionados
- Usar composition: Un bloque puede implementar múltiples interfaces pequeñas

**Beneficios**:
- Clases más simples y enfocadas
- Cambios en una interfaz no afectan clases que no la usan
- Mejor documentación de capacidades

#### D - Dependency Inversion Principle (Inversión de Dependencias)

**Definición**: Depender de abstracciones, no de implementaciones concretas.

**Aplicación correcta**:
- **Depender de interfaces** en lugar de clases concretas
- **Inyectar dependencias** en lugar de instanciar internamente
- **Usar abstracciones** de WordPress cuando sea posible

**Violaciones comunes a evitar**:
```
Instanciar clases concretas con new dentro de métodos
Llamar funciones globales directamente sin abstracción
Hardcodear nombres de clases específicas
```

**Cómo cumplir**:
```
Constructor recibe interfaces, no clases concretas
Usar Service Container para resolver dependencias
Abstraer funciones de WordPress en wrappers cuando convenga
```

**Beneficios**:
- Facilita testing (inyectar mocks)
- Flexibilidad para cambiar implementaciones
- Bajo acoplamiento

**Ejemplo de refactorización**:
```
Mal: Bloque instancia directamente PackageRepository
Bien: Bloque recibe RepositoryInterface en constructor
```

---

### 1.4 Principios de Clean Code

#### KISS (Keep It Simple, Stupid)

**Definición**: Mantener soluciones simples y directas.

**Aplicación**:
- Métodos cortos (máximo 20-30 líneas)
- Una tarea por método
- Lógica clara sin trucos complejos
- Evitar optimizaciones prematuras

**Indicadores de complejidad innecesaria**:
- Anidación de más de 3 niveles
- Expresiones booleanas complejas
- Lógica que requiere comentarios extensos para entender
- Nombres de variables crípticos

**Cómo simplificar**:
- Extraer condicionales complejas a métodos con nombres descriptivos
- Early returns en lugar de else anidados
- Guard clauses al inicio de métodos
- Descomponer métodos grandes en varios pequeños

#### DRY (Don't Repeat Yourself)

**Definición**: Evitar duplicación de lógica.

**Aplicación**:
- Extraer código repetido a métodos helper
- Usar herencia o traits para compartir funcionalidad
- Centralizar lógica común en clases de servicio
- Configuración en arrays en lugar de código repetitivo

**Tipos de duplicación a evitar**:
- **Código literal**: Mismo bloque copiado en múltiples lugares
- **Lógica similar**: Variaciones del mismo algoritmo
- **Estructuras**: Patrones repetitivos

**Cómo eliminar duplicación**:
- Identificar patrones comunes
- Crear métodos o clases reutilizables
- Parametrizar diferencias
- Usar composición

**Balance con abstracción**:
- No crear abstracciones prematuras
- Duplicar 2 veces está bien, a la tercera refactorizar
- Abstraer solo cuando el patrón es claro

#### YAGNI (You Aren't Gonna Need It)

**Definición**: No implementar funcionalidad que no se necesita ahora.

**Aplicación**:
- Implementar solo requisitos actuales
- No agregar métodos "por si acaso"
- No crear infraestructura para features futuras
- Refactorizar cuando realmente se necesite

**Indicadores de violación**:
- Métodos que nunca se llaman
- Parámetros que nunca se usan
- Configuraciones para casos que no existen
- Abstracciones para extensiones que no se han solicitado

**Cómo evitar**:
- Revisar código sin uso periódicamente
- Herramientas de análisis estático (PHPStan)
- Code review enfocado en necesidad real
- Eliminar código comentado (git lo guarda)

**Balance**:
- Diseño extensible está bien
- Anticipar cambios obvios está bien
- Crear infraestructura especulativa NO está bien

#### Descriptive Naming (Nombres Descriptivos)

**Definición**: Nombres que revelan intención sin necesidad de comentarios.

**Convenciones**:
- **Variables**: `$package_id`, `$departure_date`, `$is_sold_out`
- **Métodos**: `get_active_departures()`, `calculate_discount()`, `is_date_available()`
- **Clases**: `DatesAndPrices`, `PackageRepository`, `BookingWizardService`
- **Constantes**: `MAX_DEPARTURES`, `DEFAULT_CURRENCY`

**Anti-patrones**:
- `$data`, `$temp`, `$arr`, `$x`, `$flag`
- `process()`, `handle()`, `do_stuff()`, `run()`
- Nombres genéricos sin contexto

**Reglas**:
- Nombres pronunciables
- Nombres buscables (evitar single-letter)
- Un concepto por palabra
- Contexto relevante
- Evitar prefijos innecesarios (no `$thePackage`)

#### Code Cohesion and Low Coupling

**Cohesión**: Grado en que elementos de un módulo pertenecen juntos.

**Alta cohesión** (deseable):
- Métodos de una clase trabajan con mismos datos
- Funcionalidad relacionada agrupada
- Clase con propósito claro y único

**Baja cohesión** (evitar):
- Clase "cajón de sastre" con funcionalidad no relacionada
- Métodos que no usan propiedades de la clase
- Responsabilidades mezcladas

**Acoplamiento**: Grado de dependencia entre módulos.

**Bajo acoplamiento** (deseable):
- Módulos independientes
- Interfaces claras
- Dependencias inyectadas
- Comunicación mediante eventos/hooks

**Alto acoplamiento** (evitar):
- Acceso directo a propiedades de otras clases
- Dependencias hardcodeadas
- Conocimiento de implementación interna de otras clases

**Cómo mejorar**:
- Interfaces para definir contratos
- Dependency Injection
- Hooks para comunicación
- Encapsulación (propiedades privadas)

---

### 1.5 Seguridad en WordPress

#### Data Sanitization (Sanitización de Entrada)

**Definición**: Limpiar datos de entrada antes de procesarlos o guardarlos.

**Funciones por tipo de dato**:
- `sanitize_text_field()`: Texto plano simple
- `sanitize_textarea_field()`: Texto multi-línea
- `sanitize_email()`: Direcciones de email
- `sanitize_url()` / `esc_url_raw()`: URLs
- `sanitize_key()`: Claves (slugs, IDs alfanuméricos)
- `absint()`: Enteros positivos (IDs)
- `intval()`: Enteros (pueden ser negativos)
- `floatval()`: Números decimales
- `sanitize_hex_color()`: Códigos de color
- `wp_kses_post()`: HTML permitido (posts)
- `wp_kses()`: HTML con tags específicos permitidos

**Dónde sanitizar**:
- **Siempre** al recibir `$_POST`, `$_GET`, `$_REQUEST`
- Al guardar en post meta / options
- Al procesar formularios
- Al manejar AJAX requests
- Al procesar query parameters

**Anti-patrón**:
```
Usar datos de entrada directamente sin sanitizar
Asumir que datos de admin son seguros
Sanitizar solo en save, no en display
```

#### Output Escaping (Escapado de Salida)

**Definición**: Convertir caracteres especiales para prevenir XSS.

**Funciones por contexto**:
- `esc_html()`: Texto en HTML
- `esc_attr()`: Atributos HTML
- `esc_url()`: URLs en href/src
- `esc_js()`: Strings en JavaScript inline
- `esc_textarea()`: Contenido de textarea
- `wp_kses_post()`: HTML de posts (permite tags seguros)
- `wp_json_encode()`: JSON seguro

**Dónde escapar**:
- **Siempre** al hacer echo/print de variables
- En templates antes de output
- En atributos HTML
- En URLs
- En JavaScript inline

**Regla de oro**: Escapar lo más tarde posible (en el punto de salida).

**Anti-patrón**:
```
Echo directo de variables
Asumir que datos de ACF son seguros
Escapar en save en lugar de output
```

#### Nonce Verification (Verificación de Nonce)

**Definición**: Tokens de un solo uso para verificar intención de usuario.

**Creación**:
- `wp_create_nonce('action_name')`: Generar nonce
- `wp_nonce_field('action_name', 'nonce_field')`: Input hidden en forms
- `wp_nonce_url($url, 'action_name')`: URL con nonce

**Verificación**:
- `wp_verify_nonce($nonce, 'action_name')`: Verificar nonce
- `check_admin_referer('action_name')`: Verificar y morir si falla
- `check_ajax_referer('action_name')`: Para AJAX requests

**Dónde usar**:
- Todos los formularios que modifican datos
- Todas las peticiones AJAX
- URLs de acciones (delete, activate, etc.)
- Operaciones sensibles

**Estructura típica**:
```
Form: wp_nonce_field('save_package', 'package_nonce')
Handler: wp_verify_nonce($_POST['package_nonce'], 'save_package')
```

#### Capability Checks (Verificación de Permisos)

**Definición**: Verificar que usuario tiene permisos para realizar acción.

**Función principal**: `current_user_can($capability)`

**Capabilities comunes**:
- `edit_posts`: Editar posts
- `edit_pages`: Editar páginas
- `edit_others_posts`: Editar posts de otros
- `publish_posts`: Publicar posts
- `manage_options`: Acceso a opciones (admin)
- `edit_{post_type}`: Para custom post types

**Dónde verificar**:
- Antes de guardar datos
- Antes de eliminar datos
- En AJAX handlers
- En REST API endpoints
- En admin pages

**Estructura típica**:
```
if (!current_user_can('edit_posts')) {
    wp_die('No tienes permisos');
}
```

#### Prepared SQL Statements

**Definición**: Usar placeholders en queries para prevenir SQL injection.

**Uso de $wpdb->prepare()**:
```
Placeholders: %s (string), %d (integer), %f (float)
Siempre usar prepare() para queries con variables
Never concatenar variables directamente en SQL
```

**Cuándo usar**:
- Queries personalizadas con $wpdb
- Queries complejas que WP_Query no soporta
- Acceso a tablas personalizadas

**Cuándo NO es necesario**:
- WP_Query (ya prepara internamente)
- get_posts() con parámetros
- get_post_meta(), update_post_meta() (ya son seguros)

**Anti-patrón**:
```
Concatenar variables: "WHERE id = $id"
No usar prepare con datos de usuario
Confiar en sanitize como única protección
```

---

### 1.6 WordPress Coding Standards

#### WordPress PHP Coding Standards (WPCS)

**Indentación**:
- Tabs para indentación
- Spaces para alineación

**Llaves**:
- Funciones/clases: Llave en nueva línea
- Estructuras de control: Llave en misma línea

**Espaciado**:
- Espacios alrededor de operadores: `$a = $b + $c`
- No espacios en paréntesis: `function_name( $param )`
- Espacio después de comas: `array( 'a', 'b', 'c' )`

**Nombres**:
- Funciones: `snake_case`
- Clases: `PascalCase`
- Constantes: `UPPERCASE`
- Variables: `$snake_case`

**Arrays**:
- Short syntax permitido: `[]` o `array()`
- Consistencia en todo el proyecto

**Control Structures**:
- Siempre usar llaves, incluso para single-line
- Else/elseif en misma línea que llave de cierre

#### PSR-12 Compatibility

**Type Declarations**:
- Type hints en parámetros cuando sea posible
- Return type declarations
- Strict types opcional pero recomendado

**Visibility**:
- Siempre declarar visibilidad: `public`, `private`, `protected`
- No usar `var` para propiedades

**Namespace & Use**:
- Un namespace por archivo
- Use statements después de namespace
- Una clase por archivo

**Imports**:
- Ordenar alfabéticamente
- Agrupar por vendor (WordPress, PHP, custom)

#### Namespace Prefixing

**Convención**: `Vendor\Package\Category\Class`

**Para Travel Plugins**: `Travel\Blocks\{Category}\{BlockName}`

**Ejemplos**:
- `Travel\Blocks\ACF\HeroCarousel`
- `Travel\Blocks\Package\DatesAndPrices`
- `Travel\Blocks\Core\BlockBase`
- `Travel\Services\AjaxHandler`

**Reglas**:
- Un namespace por archivo
- Namespace refleja estructura de carpetas
- Use statements para clases externas
- Alias cuando hay conflictos

#### Hooks Naming Conventions

**Patrón**: `{prefix}_{type}_{context}_{detail}`

**Prefix**: `travel_blocks`

**Type**: `action`, `filter` (implícito por uso)

**Ejemplos**:
- `travel_blocks_before_block_render`
- `travel_blocks_after_save_meta`
- `travel_blocks_filter_package_data`
- `travel_blocks_modify_query_args`

**Documentación**:
- Documentar cada hook con @action o @filter
- Especificar parámetros y tipos
- Describir propósito y cuándo se ejecuta

---

### 1.7 Internacionalización y Localización

#### i18n Functions

**Funciones principales**:
- `__($text, $domain)`: Traducir y retornar
- `_e($text, $domain)`: Traducir y hacer echo
- `_x($text, $context, $domain)`: Traducir con contexto
- `_n($single, $plural, $number, $domain)`: Plural
- `_nx($single, $plural, $number, $context, $domain)`: Plural con contexto
- `esc_html__()`, `esc_html_e()`: Traducir y escapar
- `esc_attr__()`, `esc_attr_e()`: Traducir y escapar para atributos

**Text Domain**:
- Único por plugin
- Definido en plugin header
- Consistente en todas las llamadas
- Para Travel Blocks: `travel-blocks`

**Reglas**:
- Todo texto visible al usuario debe ser traducible
- No concatenar traducciones
- Usar placeholders: `sprintf(__('Hello %s', 'domain'), $name)`
- Contexto cuando palabra puede significar varias cosas

#### Text Domain Loading

**Método correcto**:
```
Hook: plugins_loaded o init
Función: load_plugin_textdomain()
Path: languages/ dentro del plugin
```

**Archivos**:
- `.pot`: Template con todos los strings
- `.po`: Traducción editable
- `.mo`: Traducción compilada

**Generación**:
- WP-CLI: `wp i18n make-pot`
- Herramienta: Poedit, Loco Translate

---

### 1.8 Herramientas y Validación

#### Composer

**Uso**:
- Autoloading PSR-4
- Gestión de dependencias
- Scripts para tareas comunes

**composer.json básico**:
```json
{
  "autoload": {
    "psr-4": {
      "Travel\\Blocks\\": "src/"
    }
  },
  "require-dev": {
    "phpstan/phpstan": "^1.0",
    "squizlabs/php_codesniffer": "^3.7",
    "wp-coding-standards/wpcs": "^3.0"
  }
}
```

#### PHPCS (PHP CodeSniffer)

**Uso**: Validar coding standards

**Instalación**:
```bash
composer require --dev squizlabs/php_codesniffer
composer require --dev wp-coding-standards/wpcs
```

**Configuración** (phpcs.xml):
```xml
<?xml version="1.0"?>
<ruleset name="Travel Blocks">
    <rule ref="WordPress"/>
    <exclude-pattern>vendor/</exclude-pattern>
    <exclude-pattern>node_modules/</exclude-pattern>
</ruleset>
```

**Comandos**:
```bash
phpcs --standard=WordPress src/
phpcbf --standard=WordPress src/  # Auto-fix
```

#### PHPStan / Psalm

**Uso**: Análisis estático, detección de errores de tipos

**Nivel recomendado**: 5-6 (balance entre rigor y practicidad)

**Configuración** (phpstan.neon):
```neon
parameters:
    level: 6
    paths:
        - src
    excludePaths:
        - vendor
```

**Beneficios**:
- Detecta bugs antes de ejecutar código
- Valida tipos y retornos
- Identifica código muerto
- Mejora calidad general

#### WP-CLI

**Uso**: Comandos WordPress desde terminal

**Comandos útiles**:
```bash
wp scaffold block          # Generar estructura de bloque
wp plugin list             # Listar plugins
wp cache flush             # Limpiar cache
wp post list               # Listar posts
wp db query                # Ejecutar queries
wp i18n make-pot           # Generar archivo .pot
```

#### Query Monitor

**Uso**: Debugging en desarrollo

**Monitorea**:
- Queries de base de datos (cantidad, tiempo, duplicadas)
- Hooks ejecutados y orden
- Errores PHP y notices
- Tiempo de carga por componente
- HTTP requests
- Scripts y estilos cargados

**Beneficios**:
- Identificar queries lentas o duplicadas
- Detectar hooks que se ejecutan múltiples veces
- Ver errores que no aparecen con WP_DEBUG

---

## Parte 2: Metodología de Auditoría por Bloque

### 2.1 Proceso Sistemático de Auditoría

Para cada bloque se debe seguir este proceso de 8 pasos:

#### Paso 1: Análisis de Estructura de Archivos

**Acciones**:
1. Identificar archivo principal de la clase del bloque
2. Identificar template asociado (si existe)
3. Identificar assets (CSS, JS) del bloque
4. Verificar ubicación correcta según estructura PSR-4
5. Verificar namespace correcto

**Documentar**:
- Ruta de clase principal
- Ruta de template
- Rutas de assets
- Namespace actual
- Desviaciones de estructura esperada

#### Paso 2: Análisis de Clase del Bloque

**Acciones**:
1. Leer clase completa del bloque
2. Identificar propósito principal del bloque
3. Listar todas las propiedades de la clase
4. Listar todos los métodos públicos
5. Listar todos los métodos privados/protected
6. Identificar herencia (extiende clase base?)
7. Identificar traits usados
8. Identificar interfaces implementadas

**Documentar**:
- Propósito del bloque
- Responsabilidades actuales de la clase
- Métodos y su propósito
- Dependencias (otras clases instanciadas o usadas)

#### Paso 3: Análisis de Registro del Bloque

**Acciones**:
1. Identificar método de registro (ACF `acf_register_block_type` vs Gutenberg `register_block_type`)
2. Revisar configuración del bloque (name, title, category, etc.)
3. Identificar render callback
4. Identificar supports (anchor, align, etc.)
5. Verificar si usa block.json

**Documentar**:
- Tipo de bloque (ACF vs Gutenberg nativo)
- Configuración completa
- Método de renderizado
- Características soportadas

#### Paso 4: Análisis de Campos ACF (si aplica)

**Acciones**:
1. Identificar si el bloque usa campos ACF
2. Listar todos los campos ACF definidos
3. Identificar método de definición (PHP vs JSON)
4. Revisar grupos de campos
5. Verificar validación y condicionales

**Documentar**:
- Lista completa de campos ACF
- Tipo de cada campo
- Relaciones y condicionales
- Valores por defecto
- Validaciones

#### Paso 5: Análisis de Lógica de Renderizado

**Acciones**:
1. Identificar método que prepara datos
2. Analizar obtención de datos (ACF, post meta, queries)
3. Identificar procesamiento de datos
4. Revisar lógica de negocio
5. Verificar qué se pasa al template
6. Revisar template y su lógica

**Documentar**:
- Flujo de obtención de datos
- Transformaciones aplicadas
- Lógica compleja identificada
- Separación entre controller y view
- Uso de funciones WordPress

#### Paso 6: Análisis de Funcionalidad Especial

**Acciones**:
1. Identificar si tiene AJAX
2. Identificar si tiene JavaScript frontend
3. Identificar si tiene REST API
4. Identificar hooks propios (actions/filters)
5. Identificar integraciones con otros plugins/servicios

**Documentar**:
- Funcionalidades AJAX y sus acciones
- JavaScript y su propósito
- Hooks registrados
- Dependencias externas

#### Paso 7: Identificación de Problemas

**Categorías a revisar**:

**A. Violaciones de SOLID**:
- SRP: Clase con múltiples responsabilidades
- OCP: Necesidad de modificar clase para extender
- LSP: Herencia que rompe contratos
- ISP: Implementación de métodos no usados
- DIP: Dependencias hardcodeadas

**B. Problemas de Clean Code**:
- Métodos muy largos (>30 líneas)
- Lógica compleja (anidación >3 niveles)
- Código duplicado
- Nombres no descriptivos
- Código comentado sin razón
- Funciones sin uso

**C. Problemas de Seguridad**:
- Falta de sanitización en inputs
- Falta de escapado en outputs
- Nonces faltantes en AJAX/forms
- Capabilities no verificadas
- Queries sin prepare

**D. Problemas de Arquitectura**:
- No usa namespace correcto
- No sigue PSR-4
- Lógica en template en lugar de controller
- Mezcla de responsabilidades
- Acoplamiento alto

**E. Código Sin Uso**:
- Métodos nunca llamados
- Propiedades nunca usadas
- Parámetros nunca usados
- Imports innecesarios

**Documentar cada problema encontrado con**:
- Tipo de problema
- Ubicación (archivo:línea)
- Descripción del problema
- Impacto (crítico, alto, medio, bajo)

#### Paso 8: Recomendaciones de Refactorización

**Para cada problema identificado**:

**Documentar**:
1. **Acción**: Qué hacer específicamente
2. **Razón**: Por qué es necesario
3. **Riesgo**: Qué puede romperse
4. **Precauciones**: Cómo mitigar el riesgo
5. **Prioridad**: Alta, Media, Baja
6. **Esfuerzo estimado**: Horas

**Categorías de recomendaciones**:
- **Refactorización**: Mejorar estructura sin cambiar funcionalidad
- **Eliminación**: Remover código sin uso
- **Extracción**: Separar responsabilidades
- **Seguridad**: Agregar sanitización/escapado/nonces
- **Simplificación**: Reducir complejidad

---

### 2.2 Template de Auditoría Individual

```markdown
## Bloque: [Nombre del Bloque]

### 1. Información General

**Ubicación**: `[ruta/de/la/clase.php]`
**Namespace**: `[Namespace\Completo]`
**Template**: `[ruta/del/template.php]` (si aplica)
**Assets**:
- CSS: `[ruta/del/css]`
- JS: `[ruta/del/js]`

**Tipo de Bloque**: [ ] ACF  [ ] Gutenberg Nativo

### 2. Propósito y Funcionalidad

**Descripción**: [Qué hace este bloque en términos de usuario final]

**Inputs**:
- [Campo/dato 1]: [Tipo] - [Descripción]
- [Campo/dato 2]: [Tipo] - [Descripción]
...

**Outputs**:
- [Descripción del HTML/contenido generado]

### 3. Estructura de la Clase

**Herencia**:
- Extiende: [Clase padre o "Ninguna"]
- Implementa: [Interfaces o "Ninguna"]
- Usa Traits: [Traits o "Ninguno"]

**Propiedades**:
```
- $propiedad1: [tipo] - [propósito]
- $propiedad2: [tipo] - [propósito]
...
```

**Métodos Públicos**:
```
1. nombre_metodo($param1, $param2): [return type]
   Propósito: [descripción]

2. otro_metodo(): [return type]
   Propósito: [descripción]
...
```

**Métodos Privados/Protected**:
```
1. metodo_privado($param): [return type]
   Propósito: [descripción]
...
```

### 4. Registro del Bloque

**Método de Registro**: [acf_register_block_type / register_block_type]

**Configuración**:
```
- name: [nombre del bloque]
- title: [título visible]
- description: [descripción]
- category: [categoría]
- icon: [icono]
- keywords: [array de palabras clave]
- render_callback: [método o template]
- supports: [array de features soportadas]
```

**Block.json**: [ ] Existe  [ ] No existe
(Si existe, describir configuración relevante)

### 5. Campos ACF (si aplica)

**Definición**: [ ] PHP  [ ] JSON  [ ] No aplica

**Grupos de Campos**:

**Grupo 1**: [nombre del grupo]
- Campo: `[field_name]`
  - Tipo: [text, textarea, select, etc.]
  - Label: [etiqueta visible]
  - Required: [sí/no]
  - Default: [valor por defecto si existe]

**Grupo 2**: [nombre del grupo]
- ...

**Campos Condicionales**: [Si hay lógica condicional, describirla]

### 6. Flujo de Renderizado

**Método de Preparación de Datos**: [nombre del método si existe]

**Obtención de Datos**:
1. [Fuente 1]: [Cómo se obtiene - get_field, WP_Query, etc.]
2. [Fuente 2]: [Cómo se obtiene]
...

**Procesamiento de Datos**:
1. [Transformación 1]: [Descripción del procesamiento]
2. [Transformación 2]: [Descripción]
...

**Variables Pasadas al Template**:
```
- $variable1: [tipo] - [contenido]
- $variable2: [tipo] - [contenido]
...
```

**Lógica en Template**:
- [Describir si hay lógica compleja en el template]
- [Identificar si debería moverse al controller]

### 7. Funcionalidades Adicionales

**AJAX**:
- [ ] No usa AJAX
- [ ] Usa AJAX:
  - Acción: `[nombre_accion_ajax]`
  - Propósito: [descripción]
  - Nonce: [ ] Sí  [ ] No
  - Capability check: [ ] Sí  [ ] No

**JavaScript Frontend**:
- [ ] No usa JS
- [ ] Usa JS:
  - Archivo: `[ruta]`
  - Propósito: [descripción]
  - Dependencias: [jQuery, etc.]

**REST API**:
- [ ] No usa REST API
- [ ] Usa REST API:
  - Endpoint: `[ruta]`
  - Propósito: [descripción]

**Hooks Propios**:
- Actions:
  - `[nombre_hook]`: [cuándo se ejecuta] - [parámetros]
- Filters:
  - `[nombre_hook]`: [qué filtra] - [parámetros]

**Dependencias Externas**:
- [Plugin/servicio 1]: [para qué se usa]
- [Plugin/servicio 2]: [para qué se usa]

### 8. Análisis de Problemas

#### 8.1 Violaciones de SOLID

**SRP (Single Responsibility)**:
- [ ] Cumple
- [ ] Viola: [Descripción del problema]
  - Ubicación: [archivo:línea]
  - Impacto: [Crítico/Alto/Medio/Bajo]

**OCP (Open/Closed)**:
- [ ] Cumple
- [ ] Viola: [Descripción del problema]
  - Ubicación: [archivo:línea]
  - Impacto: [Crítico/Alto/Medio/Bajo]

**LSP (Liskov Substitution)**:
- [ ] Cumple / [ ] No aplica
- [ ] Viola: [Descripción del problema]

**ISP (Interface Segregation)**:
- [ ] Cumple / [ ] No aplica
- [ ] Viola: [Descripción del problema]

**DIP (Dependency Inversion)**:
- [ ] Cumple
- [ ] Viola: [Descripción del problema]
  - Ubicación: [archivo:línea]
  - Impacto: [Crítico/Alto/Medio/Bajo]

#### 8.2 Problemas de Clean Code

**Complejidad**:
- [ ] Métodos con >30 líneas: [listar métodos]
- [ ] Anidación >3 niveles: [ubicación]
- [ ] Lógica compleja: [descripción]

**Duplicación**:
- [ ] Código duplicado detectado: [descripción y ubicaciones]

**Nombres**:
- [ ] Nombres no descriptivos: [listar variables/métodos]

**Código Sin Uso**:
- [ ] Métodos sin uso: [listar]
- [ ] Propiedades sin uso: [listar]
- [ ] Imports sin uso: [listar]
- [ ] Código comentado: [ubicaciones]

#### 8.3 Problemas de Seguridad

**Sanitización**:
- [ ] Inputs sin sanitizar: [ubicación y tipo de input]

**Escapado**:
- [ ] Outputs sin escapar: [ubicación en template]

**Nonces**:
- [ ] AJAX sin nonce: [acción]
- [ ] Formulario sin nonce: [ubicación]

**Capabilities**:
- [ ] Operación sin capability check: [ubicación]

**SQL**:
- [ ] Query sin prepare: [ubicación]

#### 8.4 Problemas de Arquitectura

**Namespace/PSR-4**:
- [ ] Namespace incorrecto: [actual vs esperado]
- [ ] Ubicación incorrecta: [actual vs esperada]

**Separación MVC**:
- [ ] Lógica en template que debe estar en controller: [descripción]
- [ ] Queries directas en template: [ubicación]

**Acoplamiento**:
- [ ] Dependencias hardcodeadas: [descripción]
- [ ] Instanciación directa de clases: [ubicación]

**Otros**:
- [Cualquier otro problema arquitectónico identificado]

### 9. Recomendaciones de Refactorización

#### Prioridad Alta

**Recomendación 1**: [Título breve]
- **Acción**: [Descripción detallada de qué hacer]
- **Razón**: [Por qué es necesario]
- **Problema que resuelve**: [Referencia a problema identificado en sección 8]
- **Riesgo**: [Qué puede romperse]
- **Precauciones**: [Cómo mitigar el riesgo]
- **Esfuerzo estimado**: [X horas]

**Recomendación 2**: ...

#### Prioridad Media

**Recomendación X**: [Mismo formato]

#### Prioridad Baja

**Recomendación Y**: [Mismo formato]

### 10. Plan de Acción

**Orden de Implementación**:
1. [Acción 1 - Prioridad Alta]
2. [Acción 2 - Prioridad Alta]
3. [Acción 3 - Prioridad Media]
...

**Precauciones Generales**:
- [Precaución 1: No modificar X porque afecta Y]
- [Precaución 2: Probar Z después de cada cambio]
- [Precaución 3: Backup de datos antes de modificar queries]

### 11. Checklist de Verificación Post-Refactorización

#### Funcionalidad
- [ ] El bloque aparece en el catálogo de bloques del editor
- [ ] El bloque se puede insertar en una página/post
- [ ] Los campos ACF aparecen correctamente en el inspector (si aplica)
- [ ] El bloque se puede configurar desde el editor
- [ ] El preview en editor muestra correctamente
- [ ] El bloque renderiza correctamente en frontend
- [ ] Los datos guardados se mantienen correctos
- [ ] No hay errores PHP en logs
- [ ] No hay errores JavaScript en consola
- [ ] AJAX funciona correctamente (si aplica)

#### Arquitectura
- [ ] Namespace correcto según PSR-4
- [ ] Ubicación de archivo correcta
- [ ] Clase cumple SRP
- [ ] Dependencias inyectadas (no hardcodeadas)
- [ ] Lógica separada de presentación (MVC)
- [ ] No hay código duplicado
- [ ] No hay código sin uso

#### Seguridad
- [ ] Todos los inputs están sanitizados
- [ ] Todos los outputs están escapados
- [ ] Nonces implementados (si aplica)
- [ ] Capabilities verificadas (si aplica)
- [ ] Queries usan prepare (si aplica)

#### Clean Code
- [ ] Métodos cortos (<30 líneas)
- [ ] Nombres descriptivos
- [ ] Baja complejidad (anidación <3 niveles)
- [ ] Código comentado eliminado
- [ ] Documentación actualizada (docblocks)

---

**Auditoría realizada**: [Fecha]
**Refactorización completada**: [Fecha] (por completar)
```

---

## Parte 3: Inventario de Bloques a Auditar

### Plugin: Travel Blocks

**Total de Bloques**: 45

#### 3.1 Bloques ACF (Generales)
**Ubicación**: `/wp-content/plugins/travel-blocks/src/Blocks/ACF/`
**Total**: 15 bloques

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

#### 3.2 Bloques de Package
**Ubicación**: `/wp-content/plugins/travel-blocks/src/Blocks/Package/`
**Total**: 21 bloques

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

#### 3.3 Bloques de Deal/Promociones
**Ubicación**: `/wp-content/plugins/travel-blocks/src/Blocks/Deal/`
**Total**: 3 bloques

1. DealInfoCard
2. DealPackagesGrid
3. DealsSlider

#### 3.4 Bloques de Template
**Ubicación**: `/wp-content/plugins/travel-blocks/src/Blocks/Template/`
**Total**: 6 bloques

1. Breadcrumb
2. FAQAccordion
3. HeroMediaGrid
4. PackageHeader
5. PromoCards
6. TaxonomyArchiveHero

---

## Parte 4: Checklist de Verificación Final por Categoría

### 4.1 Bloques ACF (Generales)

#### Funcionalidad General
- [ ] Los 15 bloques aparecen en el catálogo del editor
- [ ] Todos los bloques se pueden insertar correctamente
- [ ] Los campos ACF se muestran apropiadamente en cada bloque
- [ ] El preview funciona en todos los bloques
- [ ] Renderizado frontend correcto en todos los bloques
- [ ] No hay errores PHP en logs
- [ ] No hay errores JavaScript en consola

#### Arquitectura
- [ ] Todos los bloques usan namespace `Travel\Blocks\ACF\`
- [ ] Todos los archivos están en `/src/Blocks/ACF/`
- [ ] Todos heredan de clase base común (si existe)
- [ ] No hay dependencias hardcodeadas
- [ ] Separación MVC implementada correctamente
- [ ] No hay código duplicado entre bloques
- [ ] No hay funciones sin uso en ningún bloque

#### Seguridad
- [ ] Sanitización implementada en todos los inputs
- [ ] Escapado implementado en todos los templates
- [ ] Nonces en todos los formularios y AJAX
- [ ] Capabilities verificadas donde corresponde
- [ ] Queries preparadas correctamente

#### Clean Code
- [ ] No hay métodos >30 líneas
- [ ] Nombres descriptivos en todos los bloques
- [ ] Complejidad baja (anidación <3 niveles)
- [ ] Código comentado eliminado
- [ ] Documentación actualizada

#### Templates
- [ ] Templates solo contienen lógica de presentación
- [ ] No hay queries directas en templates
- [ ] Todos los outputs están escapados
- [ ] Variables recibidas están bien documentadas

---

### 4.2 Bloques de Package

#### Funcionalidad General
- [ ] Los 21 bloques aparecen en el catálogo del editor
- [ ] Todos los bloques se pueden insertar correctamente
- [ ] Los campos ACF se muestran apropiadamente en cada bloque
- [ ] El preview funciona en todos los bloques
- [ ] Renderizado frontend correcto en todos los bloques
- [ ] No hay errores PHP en logs
- [ ] No hay errores JavaScript en consola

#### Funcionalidad Específica de Package
- [ ] DatesAndPrices muestra fechas correctamente
- [ ] DatesAndPrices maneja SOLD OUT apropiadamente
- [ ] Booking wizard se abre correctamente desde DatesAndPrices
- [ ] ItineraryDayByDay despliega acordeón correctamente
- [ ] PackageMap muestra ubicaciones correctamente
- [ ] ReviewsCarousel funciona correctamente
- [ ] ProductGalleryHero integra con galería de medios

#### Arquitectura
- [ ] Todos los bloques usan namespace `Travel\Blocks\Package\`
- [ ] Todos los archivos están en `/src/Blocks/Package/`
- [ ] Todos heredan de clase base común (si existe)
- [ ] No hay dependencias hardcodeadas
- [ ] Separación MVC implementada correctamente
- [ ] No hay código duplicado entre bloques
- [ ] No hay funciones sin uso en ningún bloque

#### Seguridad
- [ ] Sanitización implementada en todos los inputs
- [ ] Escapado implementado en todos los templates
- [ ] Nonces en todos los formularios y AJAX
- [ ] Capabilities verificadas donde corresponde
- [ ] Queries preparadas correctamente
- [ ] ContactPlannerForm valida y sanitiza correctamente

#### Clean Code
- [ ] No hay métodos >30 líneas
- [ ] Nombres descriptivos en todos los bloques
- [ ] Complejidad baja (anidación <3 niveles)
- [ ] Código comentado eliminado
- [ ] Documentación actualizada

#### Templates
- [ ] Templates solo contienen lógica de presentación
- [ ] No hay queries directas en templates
- [ ] Todos los outputs están escapados
- [ ] Variables recibidas están bien documentadas

---

### 4.3 Bloques de Deal/Promociones

#### Funcionalidad General
- [ ] Los 3 bloques aparecen en el catálogo del editor
- [ ] Todos los bloques se pueden insertar correctamente
- [ ] Los campos ACF se muestran apropiadamente en cada bloque
- [ ] El preview funciona en todos los bloques
- [ ] Renderizado frontend correcto en todos los bloques
- [ ] No hay errores PHP en logs
- [ ] No hay errores JavaScript en consola

#### Funcionalidad Específica de Deal
- [ ] DealInfoCard muestra información de promoción correctamente
- [ ] DealPackagesGrid lista paquetes en promoción
- [ ] DealsSlider funciona correctamente (navegación, autoplay)
- [ ] Filtros de promociones activas funcionan
- [ ] Cálculo de descuentos es correcto

#### Arquitectura
- [ ] Todos los bloques usan namespace `Travel\Blocks\Deal\`
- [ ] Todos los archivos están en `/src/Blocks/Deal/`
- [ ] Todos heredan de clase base común (si existe)
- [ ] No hay dependencias hardcodeadas
- [ ] Separación MVC implementada correctamente
- [ ] No hay código duplicado entre bloques
- [ ] No hay funciones sin uso en ningún bloque

#### Seguridad
- [ ] Sanitización implementada en todos los inputs
- [ ] Escapado implementado en todos los templates
- [ ] Nonces en AJAX (si aplica)
- [ ] Queries preparadas correctamente

#### Clean Code
- [ ] No hay métodos >30 líneas
- [ ] Nombres descriptivos en todos los bloques
- [ ] Complejidad baja (anidación <3 niveles)
- [ ] Código comentado eliminado
- [ ] Documentación actualizada

#### Templates
- [ ] Templates solo contienen lógica de presentación
- [ ] No hay queries directas en templates
- [ ] Todos los outputs están escapados
- [ ] Variables recibidas están bien documentadas

---

### 4.4 Bloques de Template

#### Funcionalidad General
- [ ] Los 6 bloques aparecen en el catálogo del editor
- [ ] Todos los bloques se pueden insertar correctamente
- [ ] Los campos ACF se muestran apropiadamente (si aplica)
- [ ] El preview funciona en todos los bloques
- [ ] Renderizado frontend correcto en todos los bloques
- [ ] No hay errores PHP en logs
- [ ] No hay errores JavaScript en consola

#### Funcionalidad Específica de Template
- [ ] Breadcrumb genera ruta correctamente
- [ ] FAQAccordion expande/colapsa correctamente
- [ ] HeroMediaGrid muestra grid de medios correctamente
- [ ] PackageHeader muestra información de encabezado
- [ ] PromoCards muestra tarjetas promocionales
- [ ] TaxonomyArchiveHero funciona en archivos de taxonomía

#### Arquitectura
- [ ] Todos los bloques usan namespace `Travel\Blocks\Template\`
- [ ] Todos los archivos están en `/src/Blocks/Template/`
- [ ] Todos heredan de clase base común (si existe)
- [ ] No hay dependencias hardcodeadas
- [ ] Separación MVC implementada correctamente
- [ ] No hay código duplicado entre bloques
- [ ] No hay funciones sin uso en ningún bloque

#### Seguridad
- [ ] Sanitización implementada en todos los inputs
- [ ] Escapado implementado en todos los templates
- [ ] Queries preparadas correctamente

#### Clean Code
- [ ] No hay métodos >30 líneas
- [ ] Nombres descriptivos en todos los bloques
- [ ] Complejidad baja (anidación <3 niveles)
- [ ] Código comentado eliminado
- [ ] Documentación actualizada

#### Templates
- [ ] Templates solo contienen lógica de presentación
- [ ] No hay queries directas en templates
- [ ] Todos los outputs están escapados
- [ ] Variables recibidas están bien documentadas

---

## Parte 5: Fases de Implementación

### Fase 1: Auditoría Inicial Completa

**Objetivo**: Analizar todos los bloques sin realizar modificaciones

**Duración estimada**: 30-40 horas

**Actividades**:

#### 1.1 Auditoría de Bloques ACF (15 bloques)
- Aplicar template de auditoría a cada uno de los 15 bloques
- Documentar hallazgos en archivo individual por bloque
- Identificar patrones comunes de problemas
- Crear documento resumen de hallazgos ACF
- **Tiempo estimado**: 10-12 horas (40-50 min por bloque)

#### 1.2 Auditoría de Bloques Package (21 bloques)
- Aplicar template de auditoría a cada uno de los 21 bloques
- Documentar hallazgos en archivo individual por bloque
- Identificar patrones comunes de problemas
- Crear documento resumen de hallazgos Package
- **Tiempo estimado**: 14-16 horas (40-50 min por bloque)

#### 1.3 Auditoría de Bloques Deal (3 bloques)
- Aplicar template de auditoría a cada uno de los 3 bloques
- Documentar hallazgos en archivo individual por bloque
- Identificar patrones comunes de problemas
- Crear documento resumen de hallazgos Deal
- **Tiempo estimado**: 2-3 horas

#### 1.4 Auditoría de Bloques Template (6 bloques)
- Aplicar template de auditoría a cada uno de los 6 bloques
- Documentar hallazgos en archivo individual por bloque
- Identificar patrones comunes de problemas
- Crear documento resumen de hallazgos Template
- **Tiempo estimado**: 3-4 horas

#### 1.5 Consolidación y Priorización
- Consolidar todos los hallazgos en documento general
- Identificar mejoras globales aplicables a múltiples bloques
- Crear plan de priorización de refactorización
- Identificar bloques críticos vs no críticos
- Estimar esfuerzo total de refactorización
- **Tiempo estimado**: 2-3 horas

**Entregables**:
- 45 documentos de auditoría individual (uno por bloque)
- 4 documentos resumen (uno por categoría)
- 1 documento de consolidación general
- 1 plan de priorización de refactorización

---

### Fase 2: Refactorización de Bloques ACF

**Objetivo**: Implementar mejoras en los 15 bloques ACF

**Duración estimada**: 20-25 horas

**Prerrequisito**: Fase 1 completada

**Metodología por bloque**:

1. **Revisión de auditoría** (5 min)
   - Leer documento de auditoría del bloque
   - Revisar plan de acción definido

2. **Refactorización de código** (variable según complejidad)
   - Aplicar principios SOLID
   - Simplificar lógica compleja
   - Eliminar código sin uso
   - Separar responsabilidades
   - Inyectar dependencias

3. **Mejoras de seguridad** (10-15 min)
   - Agregar sanitización faltante
   - Agregar escapado faltante
   - Implementar nonces (si aplica)
   - Verificar capabilities (si aplica)

4. **Documentación** (5 min)
   - Actualizar docblocks
   - Actualizar documento de auditoría con cambios realizados
   - Marcar items del checklist

**Orden sugerido** (menor a mayor complejidad):

1. Breadcrumb 
2. StaticCTA 
3. StickySideMenu 
4. SideBySideCards 
5. StaticHero 
6. HeroSection 
7. ContactForm 
8. FAQAccordion 
9. TaxonomyTabs 
10. TeamCarousel 
11. PostsListAdvanced 
12. PostsCarousel 
13. PostsCarouselNative 
14. FlexibleGridCarousel 
15. HeroCarousel 

**Precauciones**:
- No modificar contratos de métodos públicos si son usados externamente
- Probar cada bloque después de refactorización (usuario hace testing)
- Commit por bloque para facilitar rollback si es necesario

---

### Fase 3: Refactorización de Bloques Package

**Objetivo**: Implementar mejoras en los 21 bloques de Package
**Metodología**: Igual que Fase 2, mientras en cada auditoria respetes las precauciones no tienes porque ser muy conservador y puedes crear cosas, renombrar, eliminar codigo que nose usa, deja las librerias sin tocar, puedes traer los archibos que esten funcionando fuera del plugin para que funcionen desde dentro y hacer mejoras significartivas que mejoren los resultados en la auditoria

**Orden sugerido** (menor a mayor complejidad):

1. MetadataLine 
2. ProductMetadata 
3. QuickFacts 
4. TrustBadges 
5. PromoCard 
6. CTABanner 
7. ImpactSection 
8. PackageVideo 
9. RelatedPostsGrid 
10. RelatedPackages 
11. ContactPlannerForm 
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

**Precauciones especiales**:
- **DatesAndPrices**: Mantener funcionalidad del booking wizard intacta
- **ItineraryDayByDay**: Mantener lógica de acordeón y estructura de días
- **ProductGalleryHero**: No romper integración con galería de medios
- **ContactPlannerForm**: Mantener integración con sistema de formularios
- **PackageMap**: Mantener integración con API de mapas

---

### Fase 4: Refactorización de Bloques Deal

**Objetivo**: Implementar mejoras en los 3 bloques de Deal

**Duración estimada**: 4-5 horas

**Prerrequisito**: Fase 1 completada

**Metodología**: Igual que Fase 2

**Orden sugerido**:

1. DealInfoCard (1.5h)
2. DealPackagesGrid (1.5h)
3. DealsSlider (2h)

**Precauciones especiales**:
- Mantener lógica de cálculo de descuentos
- No romper filtros de promociones activas
- Mantener funcionalidad de slider (navegación, autoplay)

---

### Fase 5: Refactorización de Bloques Template

**Objetivo**: Implementar mejoras en los 6 bloques de Template

**Duración estimada**: 8-10 horas

**Prerrequisito**: Fase 1 completada

**Metodología**: Igual que Fase 2

**Orden sugerido**:

1. Breadcrumb (1.5h)
2. PromoCards (1.5h)
3. FAQAccordion (1.5h)
4. PackageHeader (1.5h)
5. HeroMediaGrid (2h)
6. TaxonomyArchiveHero (2h)

**Precauciones especiales**:
- **Breadcrumb**: Mantener lógica de generación de ruta
- **TaxonomyArchiveHero**: Verificar que funciona en todos los archivos de taxonomía

---

### Fase 6: Mejoras Globales y Clase Base

**Objetivo**: Implementar mejoras que benefician a todos los bloques

**Duración estimada**: 6-8 horas

**Prerrequisito**: Fases 2, 3, 4 y 5 completadas

**Actividades**:

#### 6.1 Análisis de Patrones Comunes (1h)
- Revisar todos los bloques refactorizados
- Identificar código duplicado que quedó
- Identificar oportunidades de abstracción

#### 6.2 Creación/Mejora de Clase Base (2-3h)
- Revisar o crear `BlockBase` abstracta
- Mover funcionalidad común a clase base
- Definir métodos abstractos que bloques deben implementar
- Implementar interfaces según capacidades (HasAssets, HasACF, etc.)

#### 6.3 Creación de Servicios Compartidos (2-3h)
- Crear `AssetManager` si no existe
- Crear helpers comunes
- Crear repositories compartidos
- Implementar service container si aplica

#### 6.4 Actualización de Autoloading (30 min)
- Verificar composer.json
- Verificar PSR-4 correctamente configurado
- Regenerar autoload: `composer dump-autoload`

#### 6.5 Documentación Global (1h)
- Actualizar README del plugin
- Documentar arquitectura general
- Documentar convenciones de código
- Documentar hooks disponibles

---

### Fase 7: Validación y Herramientas

**Objetivo**: Implementar herramientas de validación automática

**Duración estimada**: 4-5 horas

**Prerrequisito**: Todas las fases anteriores completadas

**Actividades**:

#### 7.1 Configuración de PHPCS (1h)
- Instalar PHPCS vía Composer
- Instalar WordPress Coding Standards
- Crear archivo `phpcs.xml` con reglas
- Ejecutar PHPCS en todo el código
- Corregir violaciones automáticamente con phpcbf
- Documentar issues que requieren corrección manual

#### 7.2 Configuración de PHPStan (1h)
- Instalar PHPStan vía Composer
- Crear archivo `phpstan.neon` con configuración
- Ejecutar PHPStan nivel 5-6
- Documentar errores encontrados
- Corregir errores críticos

#### 7.3 Generación de Archivos de Traducción (1h)
- Instalar WP-CLI si no está disponible
- Generar archivo .pot: `wp i18n make-pot`
- Verificar que todos los strings tienen text domain correcto
- Documentar strings que necesitan contexto

#### 7.4 Configuración de Git Hooks (1h)
- Crear pre-commit hook para ejecutar PHPCS
- Crear pre-commit hook para ejecutar PHPStan
- Documentar proceso para desarrolladores

#### 7.5 Documentación de Herramientas (30 min)
- Documentar cómo ejecutar cada herramienta
- Documentar cómo interpretar resultados
- Crear guía de contribución con estándares

