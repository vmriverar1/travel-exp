# Resumen: Auditoría CSS Bloques Package (Parte 1)

**Fecha:** 2025-11-09
**Bloques auditados:** 10
**Categoría:** Bloques Package

---

## Bloques Auditados

| # | Bloque | Archivo CSS | Líneas | Estado | Prioridad |
|---|--------|-------------|--------|--------|-----------|
| 1 | ContactPlannerForm | ✅ contact-planner-form.css | 299 | Usa coral | ALTA |
| 2 | CTABanner | ✅ cta-banner.css | 164 | Usa purple | MEDIA |
| 3 | DatesAndPrices | ✅ dates-and-prices.css | 756 | **Usa coral + green palette, variables en :root** | **CRÍTICA** |
| 4 | FAQAccordion | ✅ faq-accordion.css | 200 | Usa red/blue (compartido ACF/Package) | MEDIA |
| 5 | ImpactSection | ✅ impact-section.css | 215 | Usa coral | ALTA |
| 6 | InclusionsExclusions | ✅ inclusions-exclusions.css | 337 | Usa coral | ALTA |
| 7 | ItineraryDayByDay | ✅ itinerary-day-by-day.css | 469 | Usa coral, pink tints, teal | ALTA |
| 8 | MetadataLine | ✅ metadata-line.css | 128 | **Usa coral + purple con nomenclatura confusa** | **ALTA** |
| 9 | PackageMap | ✅ package-map.css | 46 | Usa green (Material), simple | BAJA |
| 10 | PackagesByLocation | ❌ **NO EXISTE** | 0 | **Solo estilos inline en PHP** | **CRÍTICA** |

**Total líneas CSS:** 2,614 líneas (sin contar PackagesByLocation que no tiene CSS)

---

## Problemas Principales Encontrados

### 🔴 CRÍTICO: Paleta Coral/Purple Legacy

**9 de 10 bloques** usan colores de la paleta legacy que **NO existen en theme.json**:

| Color Legacy | Hex | Usos | Existe en theme.json? | Reemplazo Sugerido |
|--------------|-----|------|----------------------|-------------------|
| **Coral** | #E78C85 | 7 bloques | ❌ NO | `--wp--preset--color--secondary` (#C66E65) |
| **Coral Dark** | #D97369, #d97a74, #D97C76 | 3 bloques | ❌ NO | Derivar de secondary |
| **Coral Light** | #FFF0EF | 1 bloque | ❌ NO | Derivar de secondary |
| **Purple** | #311A42 | 2 bloques | ❌ NO | `--wp--preset--color--primary` (#17565C) |
| **Purple Light** | #4A2B5E | 1 bloque | ❌ NO | Derivar de primary |

**Bloques que usan Coral:**
1. ContactPlannerForm - var(--color-coral, #E78C85)
2. DatesAndPrices - --rose: #E78C85 (CTA principal)
3. ImpactSection - var(--color-coral, #E78C85)
4. InclusionsExclusions - #E78C85 hardcoded
5. ItineraryDayByDay - var(--color-coral, #E78C85)
6. MetadataLine - var(--color-coral, #E78C85)
7. PackagesByLocation - NO (usa blue #0073aa)

**Bloques que usan Purple:**
1. CTABanner - var(--color-purple, #311A42)
2. MetadataLine - var(--color-purple, #311A42)

### 🟡 Variables en :root (Contaminación Global)

**1 bloque** define variables en `:root` contaminando el scope global:

- **DatesAndPrices** - Define 26+ variables en :root (líneas 12-53)
  - Paleta completa: rose, green-strong, green-soft, green-dark
  - Grays: gray-100, gray-300
  - Booking colors completos
  - **ACCIÓN REQUERIDA:** Mover todas a `.booking` scope local

### 🟠 Sin Archivo CSS Dedicado

**1 bloque** NO tiene archivo CSS:

- **PackagesByLocation** - Solo usa estilos inline en PHP
  - Blue #0073aa hardcoded
  - Múltiples grays hardcoded
  - No reutilizable, no cacheable
  - **ACCIÓN REQUERIDA:** Crear packages-by-location.css

### 🟢 Otros Colores No-Theme

Colores encontrados que NO están en theme.json pero NO son legacy:

| Color | Hex | Uso | Bloques |
|-------|-----|-----|---------|
| Red | #e74c3c | Icon | FAQAccordion |
| Blue | #3498db | Focus outline | FAQAccordion |
| Blue WP | #0073aa | CTA, price | PackagesByLocation |
| Green Material | #4CAF50 | Caption border, success | PackageMap, ContactPlannerForm, InclusionsExclusions |

---

## Estadísticas

### Por Tipo de Problema

| Problema | Bloques Afectados | % |
|----------|-------------------|---|
| Usa colores legacy (coral/purple) | 9/10 | 90% |
| NO usa variables CSS | 3/10 | 30% |
| Usa variables pero NO de theme.json | 7/10 | 70% |
| Variables en :root global | 1/10 | 10% |
| Sin archivo CSS dedicado | 1/10 | 10% |
| Usa colores hardcoded (sin variables) | 4/10 | 40% |

### Por Complejidad

| Complejidad | Bloques | Líneas Promedio |
|-------------|---------|-----------------|
| Muy Alta | 2 (DatesAndPrices, ItineraryDayByDay) | 612 |
| Alta | 3 (ContactPlannerForm, InclusionsExclusions, ImpactSection) | 284 |
| Media | 3 (CTABanner, FAQAccordion, MetadataLine) | 164 |
| Baja | 1 (PackageMap) | 46 |
| Sin CSS | 1 (PackagesByLocation) | 0 |

### Variables CSS Usadas

| Variable | Bloques | Existe en theme.json? |
|----------|---------|----------------------|
| `--color-coral` | 6 | ❌ NO |
| `--color-purple` | 2 | ❌ NO |
| `--color-gray-900` | 5 | ❌ No exacto (vs #111111) |
| `--color-gray-700` | 4 | ❌ NO |
| `--color-gray-600` | 4 | ✅ Similar a gray (#666666) |
| `--border-radius-md` | 6 | ❌ NO |
| `--border-radius-lg` | 3 | ❌ NO |
| `--border-radius-sm` | 3 | ❌ NO |

---

## Análisis por Bloque

### 🔴 Prioridad CRÍTICA

**1. DatesAndPrices (756 líneas)**
- ❌ Define paleta completa en :root (26+ variables)
- ❌ Usa rose/coral (#E78C85) para CTA
- ❌ Usa green palette personalizada (3 verdes)
- ❌ Completamente desacoplado de theme.json
- ⚠️ Archivo más grande y complejo
- 📋 Plan: Migrar variables a scope local, mapear a theme.json

**2. PackagesByLocation (0 líneas CSS)**
- ❌ NO tiene archivo CSS dedicado
- ❌ Solo estilos inline en PHP
- ❌ Usa blue #0073aa que no está en theme.json
- ⚠️ No reutilizable, no cacheable
- 📋 Plan: Crear packages-by-location.css completo

### 🟠 Prioridad ALTA

**3. ContactPlannerForm (299 líneas)**
- ❌ Usa coral (#E78C85) para CTA y highlights
- ❌ Escala de grises completa (gray-300 a gray-900)
- ✅ Usa variables CSS con fallbacks
- 📋 Plan: Migrar coral → secondary, crear variables locales

**4. ImpactSection (215 líneas)**
- ❌ Usa coral (#E78C85) para botón CTA
- ❌ RGBA hardcoded con valores de coral en shadows
- ✅ Estructura simple
- 📋 Plan: Migrar coral → secondary, actualizar shadows

**5. InclusionsExclusions (337 líneas)**
- ❌ Usa coral #E78C85 hardcoded (no usa variable)
- ❌ 3 layouts x 3 estilos = 9 variantes
- ❌ Success/error colors personalizados
- 📋 Plan: Reemplazar hardcoded coral → secondary

**6. ItineraryDayByDay (469 líneas)**
- ❌ Usa coral en 4 lugares (bullets, pagination, focus)
- ❌ Pink backgrounds (#FFF6F5, #FFE8E5) derivados de coral
- ⚠️ Swiper.js con estilos específicos
- 📋 Plan: Migrar coral → secondary, derivar pinks

**7. MetadataLine (128 líneas)**
- ❌ Usa coral + purple con nomenclatura confusa
- ⚠️ "Primary" = coral, "Secondary" = purple (invertido vs theme.json)
- ⚠️ Breaking change potencial
- 📋 Plan: Remapear nomenclatura O actualizar colores manteniendo nombres

### 🟡 Prioridad MEDIA

**8. CTABanner (164 líneas)**
- ❌ Usa purple (#311A42) para texto
- ✅ Estructura simple
- 📋 Plan: Migrar purple → primary (teal)

**9. FAQAccordion (200 líneas)**
- ⚠️ Compartido entre ACF y Package
- ❌ Usa red (#e74c3c) y blue (#3498db)
- ✅ NO usa colores legacy (coral/purple)
- 📋 Plan: Migrar red → secondary, mantener blue para accesibilidad

### 🟢 Prioridad BAJA

**10. PackageMap (46 líneas)**
- ✅ NO usa colores legacy
- ⚠️ Usa green Material (#4CAF50)
- ✅ Muy simple, solo 46 líneas
- 📋 Plan: Crear variables locales simples

---

## Decisiones Requeridas

### 1. Paleta de Colores Global

**Opción A:** Actualizar theme.json para incluir coral/purple
```json
{
  "coral": "#E78C85",
  "purple": "#311A42"
}
```
- ✅ Menos cambios en CSS
- ❌ Mantiene colores legacy

**Opción B:** Migrar todo a Primary/Secondary de theme.json (RECOMENDADO)
```css
coral (#E78C85) → secondary (#C66E65)
purple (#311A42) → primary (#17565C)
```
- ✅ Alineación con theme.json
- ✅ Colores más modernos
- ❌ Más cambios necesarios
- ⚠️ Verificar contraste

### 2. Variables en :root

**DatesAndPrices debe:**
- Mover TODAS las variables de :root a .booking
- No contaminar scope global
- Usar variables de theme.json donde sea posible

### 3. Nomenclatura en MetadataLine

**Opción A:** Remapear completamente
- "Primary" variant → usar Secondary color
- "Secondary" variant → usar Primary color

**Opción B:** Mantener nombres, cambiar colores
- Menos confusión para usuarios
- Técnicamente incorrecto

---

## Plan de Acción Recomendado

### Fase 1: Crítico (Sprint 1)

1. **PackagesByLocation**
   - Crear archivo CSS dedicado
   - Eliminar estilos inline
   - Usar secondary para CTA

2. **DatesAndPrices**
   - Mover variables de :root a .booking
   - Migrar coral → secondary
   - Refactorizar green palette

### Fase 2: Alta Prioridad (Sprint 2)

3. **ContactPlannerForm** - Migrar coral → secondary
4. **ImpactSection** - Migrar coral → secondary
5. **InclusionsExclusions** - Reemplazar hardcoded coral
6. **ItineraryDayByDay** - Migrar coral → secondary, derivar pinks

### Fase 3: Media/Baja Prioridad (Sprint 3)

7. **MetadataLine** - Decidir estrategia de nomenclatura
8. **CTABanner** - Migrar purple → primary
9. **FAQAccordion** - Migrar red → secondary
10. **PackageMap** - Variables locales simples

---

## Métricas de Refactorización

| Métrica | Valor |
|---------|-------|
| Total de archivos CSS a refactorizar | 9 |
| Archivos CSS a crear | 1 |
| Variables en :root a mover | 26+ |
| Colores hardcoded a reemplazar | ~50+ instancias |
| Líneas de CSS afectadas | ~2,614 |
| Bloques con breaking changes potenciales | 2 (MetadataLine, DatesAndPrices) |

---

## Archivos de Auditoría Creados

Todos los reportes están en `/home/user/travel-exp/docs/auditoria-css/`:

1. ✅ contact-planner-form.md
2. ✅ cta-banner.md
3. ✅ dates-and-prices.md
4. ✅ faq-accordion.md (actualizado para indicar que es compartido)
5. ✅ impact-section.md
6. ✅ inclusions-exclusions.md
7. ✅ itinerary-day-by-day.md
8. ✅ metadata-line.md
9. ✅ package-map.md
10. ✅ packages-by-location.md

---

## Próximos Pasos

1. ✅ Auditoría Parte 1 completada (10 bloques Package)
2. 📋 Revisar y validar decisiones de color con equipo de diseño
3. 📋 Priorizar bloques para refactorización
4. 📋 Continuar con Parte 2: Auditar bloques restantes
5. 📋 Crear plan de migración detallado
6. 📋 Comenzar refactorización por fases

---

## Conclusiones

### Hallazgos Principales

1. **90% de los bloques** usan la paleta Coral/Purple legacy que no existe en theme.json
2. **DatesAndPrices** es el bloque más problemático (variables en :root, paleta completa personalizada)
3. **PackagesByLocation** necesita archivo CSS urgentemente (actualmente solo inline styles)
4. **MetadataLine** tiene nomenclatura confusa que puede causar breaking changes
5. **FAQAccordion** es compartido entre ACF y Package, cualquier cambio afecta ambos

### Recomendaciones

1. **Migrar colores a theme.json** (Opción B): coral → secondary, purple → primary
2. **Crear packages-by-location.css** como primera prioridad
3. **Refactorizar DatesAndPrices** para eliminar :root variables
4. **Definir convención de naming** para variables locales (prefijos específicos por bloque)
5. **Validar contraste** después de migración de colores
6. **Testing extensivo** después de cada cambio

### Impacto Estimado

- **Alto:** DatesAndPrices, PackagesByLocation, MetadataLine
- **Medio:** ContactPlannerForm, InclusionsExclusions, ItineraryDayByDay
- **Bajo:** CTABanner, ImpactSection, FAQAccordion, PackageMap

**Tiempo estimado:** 2-3 sprints para completar refactorización de los 10 bloques
