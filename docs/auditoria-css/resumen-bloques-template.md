# Resumen: Auditoría CSS Bloques Template

**Fecha:** 2025-11-09
**Bloques auditados:** 6 bloques Template

---

## Bloques Auditados

| # | Bloque | Archivo CSS | Estado |
|---|--------|-------------|--------|
| 1 | Breadcrumb (Template) | `template/breadcrumb.css` | ✅ Auditado |
| 2 | FAQAccordion (Template) | `faq-accordion.css` | ✅ Auditado |
| 3 | HeroMediaGrid | `template/hero-media-grid.css` | ✅ Auditado |
| 4 | PackageHeader | `template/package-header.css` | ✅ Auditado |
| 5 | PromoCards | `template/promo-cards.css` | ✅ Auditado |
| 6 | TaxonomyArchiveHero | **No tiene CSS propio** | ⚠️ Usa HeroCarousel |

---

## Problemas Principales Encontrados

### 🎨 1. Color Coral (#E78C85) - CRÍTICO

**Bloques afectados:** Breadcrumb, FAQAccordion, HeroMediaGrid, PackageHeader

**Problema:** Todos los bloques usan `#E78C85` (coral/pink) que **NO existe en theme.json**.

**theme.json tiene:**
- Primary: `#17565C` (teal)
- Secondary: `#C66E65` (salmon/terracota) ← Similar al coral

**Decisión requerida:**
- **Opción A:** Cambiar todos los bloques a usar Secondary (#C66E65)
- **Opción B:** Agregar Coral (#E78C85) a theme.json como color oficial
- **Opción C:** Mantener coral como variable local en cada bloque

**Recomendación:** Opción A - Usar Secondary para alinear con theme.json

---

### 🔧 2. Variables CSS Customizadas No Alineadas

**Bloque afectado:** PackageHeader

**Problema:** Usa variables custom que NO coinciden con WordPress:

```css
var(--color-gray-900, #212121)  /* ❌ No es estándar WP */
var(--color-gray-700, #616161)  /* ❌ No es estándar WP */
var(--color-coral, #E78C85)     /* ❌ No existe */
```

**Solución:** Reemplazar por variables de theme.json o variables locales con prefijo del bloque.

---

### 📏 3. Font-sizes No Mapeados

**Bloques afectados:** Todos

**Problema:** Usan valores hardcoded (`14px`, `13px`, `12px`, etc.) en lugar de theme.json.

**theme.json tiene:**
```json
"tiny": "0.75rem" (12px)
"small": "0.875rem" (14px)
"regular": "1rem" (16px)
```

**Solución:** Mapear font-sizes existentes a theme.json o crear variables locales.

---

### 🎯 4. Uso Excesivo de !important

**Bloque afectado:** PromoCards

**Problema:**
```css
border-radius: 24px !important;
min-height: 250px !important;
max-height: 400px !important;
```

**Solución:** Investigar y resolver conflictos de especificidad sin usar `!important`.

---

### 🔗 5. Conflictos entre Bloques

**Bloque afectado:** PromoCards

**Problema:** Tiene que ocultar estilos de `pdf-download-modal.css`:

```css
.promo-card--pdf-enabled::before { display: none !important; }
.promo-card--pdf-enabled::after { display: none !important; }
```

**Solución:** Refactorizar `pdf-download-modal.css` para usar selectores más específicos.

---

### 🌈 6. Sistema de Grises Inconsistente

**Bloques afectados:** Breadcrumb, FAQAccordion, PackageHeader

**Problema:** Cada bloque usa tonos de gris diferentes:
- `#333`, `#555`, `#666`, `#999`
- `#212121`, `#616161`, `#757575`

**theme.json solo tiene:**
- Gray: `#666666`
- Contrast: `#111111`

**Solución:** Crear sistema de grises consistente con variables locales o agregar a theme.json.

---

### ♿ 7. Accesibilidad Inconsistente

| Bloque | `prefers-reduced-motion` | `prefers-contrast` | `forced-colors` |
|--------|-------------------------|-------------------|-----------------|
| Breadcrumb | ✅ Sí | ❌ No | ❌ No |
| FAQAccordion | ❌ No | ❌ No | ❌ No |
| HeroMediaGrid | ❌ No | ❌ No | ❌ No |
| PackageHeader | ❌ No | ✅ Sí | ✅ Sí |
| PromoCards | ❌ No | ❌ No | ❌ No |

**Observación:** Solo **PackageHeader** implementa `prefers-contrast` y `forced-colors` (EJEMPLAR).

**Recomendación:** Agregar media queries de accesibilidad a todos los bloques.

---

## Aspectos Positivos

### ✅ Metodología BEM Consistente

Todos los bloques usan nomenclatura BEM:
- `.breadcrumb-navigation`, `.breadcrumb-item`
- `.faq-accordion__question`, `.faq-accordion__icon`
- `.hero-media-grid__container`, `.hero-gallery__carousel`
- `.package-header__title`, `.metadata-icon`
- `.promo-card`, `.promo-cards__container`

### ✅ Responsive Design Bien Implementado

Todos los bloques tienen breakpoints apropiados:
- Mobile: 480px - 768px
- Tablet: 768px - 1024px
- Desktop: 1024px+

### ✅ CSS Modular y Organizado

Los archivos CSS están bien estructurados con:
- Comentarios de sección
- Agrupación lógica de estilos
- Separación de responsive, editor, accesibilidad

---

## Recomendaciones Generales

### 1. Crear Sistema de Variables Compartidas

Crear un archivo `template-variables.css` con variables comunes:

```css
/* Template Blocks Shared Variables */
:root {
  /* Colors from theme.json */
  --template-primary: var(--wp--preset--color--primary);
  --template-secondary: var(--wp--preset--color--secondary);
  --template-contrast: var(--wp--preset--color--contrast);
  --template-gray: var(--wp--preset--color--gray);

  /* Extended grays */
  --template-gray-light: #999;
  --template-gray-medium: #666;
  --template-gray-dark: #333;

  /* UI elements */
  --template-border-color: #e0e0e0;
  --template-hover-bg: #f8f9fa;
  --template-placeholder-bg: #f5f5f5;

  /* Transitions */
  --template-transition-fast: 0.2s ease;
  --template-transition-normal: 0.3s ease;

  /* Shadows */
  --template-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
  --template-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.3);
}
```

### 2. Estandarizar Accesibilidad

Agregar snippet común a todos los bloques:

```css
/* Accessibility - Common for all template blocks */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

@media (prefers-contrast: high) {
  /* Aumentar font-weights y contraste */
}

@media (forced-colors: active) {
  /* Adaptar a modo alto contraste del sistema */
}
```

### 3. Resolver Decisión de Colores

**Prioridad ALTA:** Decidir qué hacer con el color Coral (#E78C85).

**Reunión requerida con:**
- Diseñador/UX
- Product owner
- Lead developer

**Opciones:**
1. Actualizar theme.json para incluir Coral
2. Migrar todos los bloques a Secondary (#C66E65)
3. Deprecar gradualmente el uso de Coral

### 4. Crear Guía de Estilos

Documentar decisiones de diseño:
- Paleta de colores oficial
- Sistema de spacing
- Sistema de typography
- Border-radius estándar (8px vs 12px vs 24px)
- Sistema de shadows

---

## Estadísticas de Auditoría

| Métrica | Valor |
|---------|-------|
| Bloques auditados | 6 |
| Archivos CSS únicos | 5 |
| Líneas de CSS total | ~700 |
| Colores hardcoded únicos | 15+ |
| Variables CSS usadas | Solo en PackageHeader |
| Uso de !important | 5 instancias (PromoCards) |
| Media queries de a11y | Solo 3 bloques |

---

## Priorización de Tareas

### 🔴 Prioridad ALTA

1. **Decidir color Coral** - Afecta 4 bloques
2. **Resolver conflicto PromoCards/PDF Modal** - Usa múltiples !important
3. **Crear variables compartidas** - Reduce duplicación

### 🟡 Prioridad MEDIA

4. Mapear font-sizes a theme.json
5. Agregar `prefers-reduced-motion` a todos los bloques
6. Estandarizar sistema de grises

### 🟢 Prioridad BAJA

7. Mejorar comentarios de código
8. Crear guía de estilos
9. Considerar fluid typography con `clamp()`

---

## Próximos Pasos

1. ✅ **Completado:** Auditoría de 6 bloques Template
2. 📋 **Pendiente:** Auditar `HeroCarousel/style.css` (usado por TaxonomyArchiveHero)
3. 🔧 **Pendiente:** Crear plan de refactorización unificado
4. 📝 **Pendiente:** Documentar decisiones en CLAUDE.md
5. 🧪 **Pendiente:** Testing después de cambios

---

## Archivos Generados

Reportes individuales creados en `/docs/auditoria-css/`:

1. `breadcrumb-template.md`
2. `faq-accordion-template.md`
3. `hero-media-grid.md`
4. `package-header.md`
5. `promo-cards.md`
6. `taxonomy-archive-hero.md`
7. `resumen-bloques-template.md` (este archivo)

---

**Auditoría completada:** 2025-11-09
**Tiempo estimado de refactorización:** 12-16 horas
**Complejidad:** Media-Alta (debido a decisiones de diseño pendientes)
