# Plan de Eliminación: global.css

## Objetivo

Eliminar completamente `wp-content/themes/travel-content-kit/assets/css/global.css` y hacer que cada bloque sea independiente, usando únicamente lo que ya existe en `theme.json` sin modificarlo.

---

## Principios de Trabajo

1. **NO modificar `theme.json`** - Solo usar variables que ya existen
2. **Independencia de bloques** - Cada bloque autosuficiente con su propio CSS
3. **Selectores específicos** - Usar clases específicas del bloque en el DOM para evitar conflictos
4. **Git maneja historial** - No crear archivos de backup

---

## Problema Actual

El archivo `global.css` contiene variables CSS que se usan como dependencia en TODOS los archivos del tema y plugins. Esto crea conflictos porque:

- Los bloques no son independientes
- Cambios en `global.css` afectan a todos los componentes
- Hay colisión de selectores genéricos
- Dificulta el mantenimiento y testing individual

---

## Variables Disponibles en theme.json

El `theme.json` ya contiene las siguientes variables que PUEDEN usarse:

### Colores
- `var(--wp--preset--color--primary)`
- `var(--wp--preset--color--secondary)`
- `var(--wp--preset--color--tertiary)`
- `var(--wp--preset--color--base)` (blanco)
- `var(--wp--preset--color--contrast)` (negro)
- `var(--wp--preset--color--gray)`
- Y todas las variantes de complementarios

### Tipografía
- `var(--wp--preset--font-size--tiny)` (0.75rem)
- `var(--wp--preset--font-size--small)` (0.875rem)
- `var(--wp--preset--font-size--regular)` (1rem)
- `var(--wp--preset--font-size--medium)` (1.25rem)
- `var(--wp--preset--font-size--large)` (1.75rem)
- Y demás tamaños

### Espaciado
- `var(--wp--preset--spacing--20)` (0.25rem)
- `var(--wp--preset--spacing--30)` (0.5rem)
- `var(--wp--preset--spacing--50)` (1rem)
- `var(--wp--preset--spacing--60)` (1.5rem)
- `var(--wp--preset--spacing--70)` (1.75rem)
- Y demás espaciados

### Familia tipográfica
- `var(--wp--preset--font-family--satoshi)`

### Sombras
- `var(--wp--preset--shadow--sombra-sm)`

---

## FASE 1: Auditoría Inicial

### Contexto
Esta fase consiste en identificar qué variables de `global.css` está usando cada bloque actualmente. No se modifica ningún archivo, solo se documenta.

### Acciones

1. **Crear estructura de documentación**
   - Crear carpeta `/docs/auditoria-css/`
   - Por cada bloque crear un archivo markdown de auditoría

2. **Por cada bloque ejecutar**
   - Abrir el archivo CSS del bloque
   - Buscar todas las referencias `var(--`
   - Listar cada variable encontrada
   - Verificar si existe equivalente en `theme.json`
   - Documentar en archivo de auditoría

3. **Formato del reporte de auditoría**
   Archivo: `/docs/auditoria-css/[nombre-bloque].md`

   Contenido:
   - Nombre del bloque
   - Ruta del archivo CSS
   - Lista de variables de `global.css` usadas
   - Para cada variable: ¿existe en theme.json?
   - Decisión: ¿Necesita CSS personalizado? Sí/No

### Checklist de Bloques

#### Bloques ACF (15)
- [ ] Breadcrumb
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] ContactForm
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] FAQAccordion
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] FlexibleGridCarousel
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] HeroCarousel
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] HeroSection
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PostsCarouselNative
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PostsCarousel
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PostsListAdvanced
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] SideBySideCards
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] StaticCTA
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] StaticHero
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] StickySideMenu
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] TaxonomyTabs
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] TeamCarousel
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

#### Bloques de Package (21)
- [ ] ContactPlannerForm
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] CTABanner
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] DatesAndPrices
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] FAQAccordion (Package)
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] ImpactSection
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] InclusionsExclusions
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] ItineraryDayByDay
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] MetadataLine
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PackageMap
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PackagesByLocation
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PackageVideo
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PricingCard
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] ProductGalleryHero
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] ProductMetadata
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PromoCard
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] QuickFacts
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] RelatedPackages
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] RelatedPostsGrid
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] ReviewsCarousel
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] TravelerReviews
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] TrustBadges
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

#### Bloques de Deal (3)
- [ ] DealInfoCard
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] DealPackagesGrid
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] DealsSlider
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

#### Bloques de Template (6)
- [ ] Breadcrumb (Template)
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] FAQAccordion (Template)
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] HeroMediaGrid
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PackageHeader
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] PromoCards
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

- [ ] TaxonomyArchiveHero
  - [ ] Auditoría completada
  - [ ] CSS personalizado necesario: Sí / No

### Verificación de Fase 1
- [ ] Todos los bloques tienen archivo de auditoría
- [ ] Todas las variables están documentadas
- [ ] Se identificaron variables que NO existen en theme.json
- [ ] Se identificaron bloques que SÍ pueden usar solo theme.json
- [ ] Resumen general creado con estadísticas

---

## FASE 2: Refactorización de Bloques ACF

### Contexto
Los bloques ACF son componentes generales reutilizables. Se refactorizan primero porque son independientes del contexto de Package. En esta fase se modifica el CSS de cada bloque para eliminar dependencias de `global.css`.

### Acciones

1. **Por cada bloque ACF**
   - Abrir archivo CSS del bloque
   - Identificar variables de `global.css` que se usan
   - Reemplazar por equivalentes de `theme.json` cuando existan
   - Para variables sin equivalente: crear variables locales dentro del selector del bloque
   - Asegurar que todos los selectores CSS sean específicos del bloque
   - Probar el bloque en editor Gutenberg
   - Probar el bloque en frontend
   - Verificar responsive en mobile/tablet/desktop
   - Commit individual por bloque

2. **Criterio de selectores específicos**
   - Todos los selectores deben iniciar con la clase del bloque
   - Evitar selectores genéricos como `.card`, `.button`, etc.
   - Usar metodología BEM o clases prefijadas con nombre del bloque

3. **Creación de variables locales**
   - Las variables locales deben definirse dentro del selector raíz del bloque
   - Usar nomenclatura clara: `--[bloque]-[propiedad]`
   - Solo crear variables que NO existen en theme.json

### Checklist de Bloques ACF

- [ ] Breadcrumb
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(breadcrumb): remove global.css dependency`

- [ ] ContactForm
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(contact-form): remove global.css dependency`

- [ ] FAQAccordion
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(faq-accordion): remove global.css dependency`

- [ ] FlexibleGridCarousel
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(flexible-grid-carousel): remove global.css dependency`

- [ ] HeroCarousel
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(hero-carousel): remove global.css dependency`

- [ ] HeroSection
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(hero-section): remove global.css dependency`

- [ ] PostsCarouselNative
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(posts-carousel-native): remove global.css dependency`

- [ ] PostsCarousel
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(posts-carousel): remove global.css dependency`

- [ ] PostsListAdvanced
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(posts-list-advanced): remove global.css dependency`

- [ ] SideBySideCards
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(side-by-side-cards): remove global.css dependency`

- [ ] StaticCTA
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(static-cta): remove global.css dependency`

- [ ] StaticHero
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(static-hero): remove global.css dependency`

- [ ] StickySideMenu
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(sticky-side-menu): remove global.css dependency`

- [ ] TaxonomyTabs
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(taxonomy-tabs): remove global.css dependency`

- [ ] TeamCarousel
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(team-carousel): remove global.css dependency`

### Verificación de Fase 2
- [ ] Todos los 15 bloques ACF refactorizados
- [ ] No hay referencias a variables de `global.css`
- [ ] Todos los bloques probados individualmente
- [ ] No hay conflictos CSS entre bloques
- [ ] Commits individuales realizados

---

## FASE 3: Refactorización de Bloques de Package

### Contexto
Los bloques de Package son específicos para el post type "package". Estos bloques pueden tener dependencias entre sí y deben ser consistentes visualmente. Se trabajan después de los ACF porque son más complejos y específicos del dominio.

### Acciones

Igual que en Fase 2, pero considerando:
- Posibles dependencias entre bloques de Package
- Consistencia visual entre todos los bloques de Package
- Verificar que funcionan correctamente en single-package template

### Checklist de Bloques de Package

- [ ] ContactPlannerForm
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(contact-planner-form): remove global.css dependency`

- [ ] CTABanner
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(cta-banner): remove global.css dependency`

- [ ] DatesAndPrices
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(dates-and-prices): remove global.css dependency`

- [ ] FAQAccordion (Package)
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(package-faq-accordion): remove global.css dependency`

- [ ] ImpactSection
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(impact-section): remove global.css dependency`

- [ ] InclusionsExclusions
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(inclusions-exclusions): remove global.css dependency`

- [ ] ItineraryDayByDay
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(itinerary-day-by-day): remove global.css dependency`

- [ ] MetadataLine
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(metadata-line): remove global.css dependency`

- [ ] PackageMap
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(package-map): remove global.css dependency`

- [ ] PackagesByLocation
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(packages-by-location): remove global.css dependency`

- [ ] PackageVideo
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(package-video): remove global.css dependency`

- [ ] PricingCard
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(pricing-card): remove global.css dependency`

- [ ] ProductGalleryHero
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(product-gallery-hero): remove global.css dependency`

- [ ] ProductMetadata
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(product-metadata): remove global.css dependency`

- [ ] PromoCard
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(promo-card): remove global.css dependency`

- [ ] QuickFacts
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(quick-facts): remove global.css dependency`

- [ ] RelatedPackages
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(related-packages): remove global.css dependency`

- [ ] RelatedPostsGrid
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(related-posts-grid): remove global.css dependency`

- [ ] ReviewsCarousel
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(reviews-carousel): remove global.css dependency`

- [ ] TravelerReviews
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(traveler-reviews): remove global.css dependency`

- [ ] TrustBadges
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (single package)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(trust-badges): remove global.css dependency`

### Verificación de Fase 3
- [ ] Todos los 21 bloques de Package refactorizados
- [ ] Consistencia visual entre bloques de Package
- [ ] No hay referencias a variables de `global.css`
- [ ] Probado en página de single package completa
- [ ] Commits individuales realizados

---

## FASE 4: Refactorización de Bloques de Deal

### Contexto
Los bloques de Deal son específicos para promociones y ofertas. Son pocos pero críticos para la funcionalidad de ventas. Se trabajan por separado porque tienen lógica de negocio específica.

### Acciones

Mismo proceso que Fases 2 y 3, verificando:
- Funcionamiento correcto de promociones
- Integración con sistema de deals
- Pruebas en páginas de deals/promociones

### Checklist de Bloques de Deal

- [ ] DealInfoCard
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (deal pages)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(deal-info-card): remove global.css dependency`

- [ ] DealPackagesGrid
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (deal pages)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(deal-packages-grid): remove global.css dependency`

- [ ] DealsSlider
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en editor
  - [ ] Probado en frontend (deal pages)
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(deals-slider): remove global.css dependency`

### Verificación de Fase 4
- [ ] Todos los 3 bloques de Deal refactorizados
- [ ] Sistema de promociones funciona correctamente
- [ ] No hay referencias a variables de `global.css`
- [ ] Commits individuales realizados

---

## FASE 5: Refactorización de Bloques de Template

### Contexto
Los bloques de Template son componentes estructurales que se usan en templates específicos de WordPress (archive, single, etc.). Son críticos para la estructura general del sitio.

### Acciones

Mismo proceso que fases anteriores, con énfasis en:
- Verificar funcionamiento en diferentes templates
- Asegurar compatibilidad con estructura del tema
- Probar en diferentes tipos de páginas (archive, single, taxonomy)

### Checklist de Bloques de Template

- [ ] Breadcrumb (Template)
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en múltiples templates
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(template-breadcrumb): remove global.css dependency`

- [ ] FAQAccordion (Template)
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en múltiples templates
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(template-faq-accordion): remove global.css dependency`

- [ ] HeroMediaGrid
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en múltiples templates
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(hero-media-grid): remove global.css dependency`

- [ ] PackageHeader
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en múltiples templates
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(package-header): remove global.css dependency`

- [ ] PromoCards
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en múltiples templates
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(promo-cards): remove global.css dependency`

- [ ] TaxonomyArchiveHero
  - [ ] CSS refactorizado
  - [ ] Variables locales creadas (si aplica)
  - [ ] Selectores específicos verificados
  - [ ] Probado en múltiples templates
  - [ ] Probado en frontend
  - [ ] Responsive verificado
  - [ ] Commit: `refactor(taxonomy-archive-hero): remove global.css dependency`

### Verificación de Fase 5
- [ ] Todos los 6 bloques de Template refactorizados
- [ ] Funcionan en todos los templates donde se usan
- [ ] No hay referencias a variables de `global.css`
- [ ] Commits individuales realizados

---

## FASE 6: Refactorización de Componentes del Tema

### Contexto
El tema tiene sus propios componentes (atoms, molecules, organisms) que también dependen de `global.css`. Estos componentes son usados por el tema base y pueden aparecer fuera del contexto de bloques de Gutenberg.

### Acciones

1. **Identificar componentes del tema**
   - Listar todos los archivos en `/wp-content/themes/travel-content-kit/assets/css/atoms/`
   - Listar todos los archivos en `/wp-content/themes/travel-content-kit/assets/css/molecules/`
   - Listar todos los archivos en `/wp-content/themes/travel-content-kit/assets/css/organisms/`
   - Listar todos los archivos en `/wp-content/themes/travel-content-kit/assets/css/templates/`

2. **Por cada componente**
   - Refactorizar CSS eliminando dependencias de `global.css`
   - Usar variables de `theme.json` cuando sea posible
   - Crear variables locales cuando no existan en `theme.json`
   - Verificar selectores específicos
   - Probar en páginas que usan el componente
   - Commit por grupo (atoms, molecules, organisms, templates)

### Checklist de Componentes del Tema

#### Atoms
- [ ] Auditoría de atoms completada
- [ ] Refactorización de atoms completada
- [ ] Testing de atoms completado
- [ ] Commit: `refactor(theme-atoms): remove global.css dependency`

#### Molecules
- [ ] Auditoría de molecules completada
- [ ] Refactorización de molecules completada
- [ ] Testing de molecules completado
- [ ] Commit: `refactor(theme-molecules): remove global.css dependency`

#### Organisms
- [ ] Auditoría de organisms completada
- [ ] Refactorización de organisms completada
- [ ] Testing de organisms completado
- [ ] Commit: `refactor(theme-organisms): remove global.css dependency`

#### Templates
- [ ] Auditoría de templates completada
- [ ] Refactorización de templates completada
- [ ] Testing de templates completado
- [ ] Commit: `refactor(theme-templates): remove global.css dependency`

### Verificación de Fase 6
- [ ] Todos los componentes del tema refactorizados
- [ ] No hay referencias a variables de `global.css` en el tema
- [ ] Header y Footer funcionan correctamente
- [ ] Navigation funciona correctamente
- [ ] Commits por grupo realizados

---

## FASE 7: Eliminación de global.css

### Contexto
Con todos los bloques y componentes refactorizados, se procede a eliminar completamente `global.css` del sistema. Esta es la fase final y crítica.

### Acciones

1. **Actualizar functions.php**
   - Abrir `/wp-content/themes/travel-content-kit/functions.php`
   - Localizar líneas 64-69 donde se encola `travel-global`
   - Eliminar ese bloque de código
   - Verificar que no hay otras referencias a `travel-global` en el archivo

2. **Eliminar dependencias**
   - Buscar en `functions.php` todas las dependencias `['travel-global']`
   - Eliminar esa dependencia de los arrays de dependencias
   - Si un archivo CSS no tiene otras dependencias, dejar array vacío `[]`

3. **Eliminar archivo**
   - Eliminar `/wp-content/themes/travel-content-kit/assets/css/global.css`

4. **Limpiar cachés**
   - Limpiar caché de WordPress
   - Limpiar caché de Redis
   - Recargar PHP-FPM

5. **Verificación global**
   - Buscar en todo el proyecto referencias a `global.css`
   - Buscar variables con formato `var(--color-*`, `var(--spacing-*`, etc. de global.css
   - Verificar que no hay referencias

### Checklist de Eliminación

- [ ] functions.php actualizado (líneas 64-69 eliminadas)
- [ ] Todas las dependencias `['travel-global']` eliminadas
- [ ] Archivo global.css eliminado
- [ ] No hay referencias a global.css en todo el proyecto
- [ ] No hay variables de global.css sin reemplazar
- [ ] Cachés limpiados
- [ ] Commit: `refactor: remove global.css completely`

### Verificación de Fase 7
- [ ] Búsqueda en todo el proyecto no encuentra `global.css`
- [ ] Búsqueda en todo el proyecto no encuentra variables antiguas
- [ ] Sitio funciona sin errores de CSS
- [ ] Todas las páginas se ven correctamente

---

## FASE 8: Testing Final y Documentación

### Contexto
Con `global.css` eliminado, se realiza testing exhaustivo de todo el sitio para asegurar que no hay regresiones visuales ni funcionales. Además, se documenta el nuevo sistema para futuros desarrolladores.

### Acciones - Testing

1. **Testing por tipo de página**
   - Homepage
   - Single Package (múltiples packages)
   - Archive Package
   - Single Post
   - Archive Post
   - Deal Pages
   - Taxonomy Pages
   - Search Results
   - 404 Page

2. **Testing responsive**
   - Mobile (375px, 390px, 414px)
   - Tablet (768px, 820px)
   - Desktop (1280px, 1440px)
   - Large Desktop (1920px)

3. **Testing cross-browser**
   - Chrome
   - Firefox
   - Safari
   - Edge

4. **Testing de bloques en editor**
   - Insertar cada tipo de bloque en editor
   - Verificar preview correcto
   - Verificar configuraciones del bloque
   - Verificar que no hay errores de consola

### Acciones - Documentación

1. **Crear guía de desarrollo**
   - Archivo: `/docs/guia-css-independiente.md`
   - Explicar nuevo sistema sin global.css
   - Explicar cómo usar variables de theme.json
   - Explicar cómo crear variables locales en bloques
   - Ejemplos prácticos

2. **Actualizar README**
   - Documentar estructura de CSS
   - Documentar convenciones de nomenclatura
   - Documentar proceso para nuevos bloques

### Checklist de Testing Final

#### Testing de Páginas
- [ ] Homepage testeada (desktop/tablet/mobile)
- [ ] Single Package testeada (al menos 3 packages diferentes)
- [ ] Archive Package testeado
- [ ] Single Post testeado
- [ ] Archive Post testeado
- [ ] Deal Pages testeadas
- [ ] Taxonomy Pages testeadas
- [ ] Search Results testeado
- [ ] 404 Page testeada

#### Testing Responsive
- [ ] Mobile 375px testeado
- [ ] Mobile 414px testeado
- [ ] Tablet 768px testeado
- [ ] Desktop 1280px testeado
- [ ] Desktop 1920px testeado

#### Testing Cross-browser
- [ ] Chrome testeado
- [ ] Firefox testeado
- [ ] Safari testeado
- [ ] Edge testeado

#### Testing de Editor
- [ ] Todos los bloques ACF testeados en editor
- [ ] Todos los bloques Package testeados en editor
- [ ] Todos los bloques Deal testeados en editor
- [ ] Todos los bloques Template testeados en editor
- [ ] No hay errores de consola al insertar bloques

#### Documentación
- [ ] Guía de desarrollo creada
- [ ] README actualizado
- [ ] Ejemplos incluidos
- [ ] Commit: `docs: add guide for CSS without global.css`

### Verificación de Fase 8
- [ ] Testing completo sin errores visuales
- [ ] Sin errores de consola
- [ ] Performance no afectado
- [ ] Documentación completa y clara
- [ ] Proyecto listo para producción

---

## Resumen de Estimación

| Fase | Descripción | Tiempo Estimado |
|------|-------------|-----------------|
| Fase 1 | Auditoría Inicial (45 bloques) | 4-5 horas |
| Fase 2 | Bloques ACF (15 bloques × 20 min) | 5-6 horas |
| Fase 3 | Bloques Package (21 bloques × 20 min) | 7-8 horas |
| Fase 4 | Bloques Deal (3 bloques × 20 min) | 1 hora |
| Fase 5 | Bloques Template (6 bloques × 20 min) | 2 horas |
| Fase 6 | Componentes del Tema | 3-4 horas |
| Fase 7 | Eliminación global.css | 1 hora |
| Fase 8 | Testing Final y Documentación | 4-5 horas |
| **TOTAL** | | **27-36 horas** |

---

## Notas Importantes

1. **Orden de ejecución**: Las fases deben ejecutarse en orden secuencial
2. **Testing continuo**: Cada bloque debe probarse inmediatamente después de refactorizar
3. **Commits frecuentes**: Commit por cada bloque refactorizado para facilitar rollback
4. **No skip de fases**: No saltar fases ni bloques sin completar checklist
5. **Comunicación**: Reportar problemas inmediatamente si un bloque no puede refactorizarse

---

## Criterio de Éxito

El proyecto se considera exitoso cuando:
- ✅ global.css eliminado completamente
- ✅ Todos los 45 bloques funcionan independientemente
- ✅ Solo se usan variables de theme.json o variables locales
- ✅ No hay conflictos CSS entre bloques
- ✅ Testing completo sin regresiones
- ✅ Documentación actualizada
