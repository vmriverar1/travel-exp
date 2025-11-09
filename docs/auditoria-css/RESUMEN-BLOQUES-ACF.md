# Resumen: Auditoría CSS de Bloques ACF

**Fecha:** 2025-11-09
**Bloques auditados:** 7 bloques ACF
**Archivos generados:** 7 reportes individuales + este resumen

---

## Estado de Auditoría

| # | Bloque | Archivo CSS | Estado | Usa Coral/Purple | Problemas Críticos |
|---|--------|-------------|--------|------------------|-------------------|
| 1 | **PostsListAdvanced** | `PostsListAdvanced/style.css` | ✅ Auditado | ⚠️ SÍ (mobile) | Coral en navegación mobile |
| 2 | **SideBySideCards** | `side-by-side-cards.css` | ✅ Auditado | ❌ SÍ | Coral/Purple en toda la paleta |
| 3 | **StaticCTA** | `static-cta.css` | ✅ Auditado | ✅ NO | Colores rojo/azul genéricos |
| 4 | **StaticHero** | `StaticHero/style.css` | ✅ Auditado | ✅ NO | Ninguno (bloque minimal) |
| 5 | **StickySideMenu** | `sticky-side-menu.css` | ✅ Auditado | ❌ SÍ | Coral/Purple en botones y menú |
| 6 | **TaxonomyTabs** | `taxonomy-tabs.css` | ✅ Auditado | ❌ SÍ | Coral/Purple + variables no usadas |
| 7 | **TeamCarousel** | `TeamCarousel/style.css` | ✅ Auditado | ✅ NO | Color naranja #d46a3f (similar a Secondary) |

---

## Problema Principal: Paleta Coral/Purple

### ❌ Bloques con problema CRÍTICO (5 de 7)

Los siguientes bloques usan **#E78C85 (Coral)** y/o **#311A42 (Purple)** que **NO están en theme.json**:

1. **PostsListAdvanced** - Usa Coral (#E78C85) en navegación mobile (Swiper)
2. **SideBySideCards** - Usa Coral/Purple en badges, botones, dividers, locations, arrows
3. **StickySideMenu** - Usa Coral/Purple en botones CTA y enlaces del menú
4. **TaxonomyTabs** - Usa Coral/Purple extensivamente en badges, botones, tabs, arrows
5. **Breadcrumb** (ya auditado) - Usa Coral/Purple en enlaces y variantes

### theme.json tiene:

```json
{
  "primary": "#17565C",    // Teal
  "secondary": "#C66E65",  // Salmon/Terracota
  "gray": "#666666",
  "base": "#ffffff",
  "contrast": "#111111"
}
```

### Bloques usan:

```css
Primary (Coral):    #E78C85  ❌ NO existe
Secondary (Purple): #311A42  ❌ NO existe
Gold:               #CEA02D  ❌ NO existe
```

---

## Resumen por Bloque

### 1. PostsListAdvanced

**Archivo:** `/home/user/travel-exp/docs/auditoria-css/posts-list-advanced.md`

**Problemas encontrados:**
- ❌ Usa Coral (#E78C85) en botones Swiper mobile
- ❌ Usa Gold (#ffb400) en badges y readmore
- ⚠️ Múltiples archivos CSS (style.css, base.css, card.css, minimal.css)
- ⚠️ No usa spacing/font-size scales de theme.json

**Recomendación:** Reemplazar Coral → Secondary (#C66E65), consolidar archivos CSS

---

### 2. SideBySideCards

**Archivo:** `/home/user/travel-exp/docs/auditoria-css/side-by-side-cards.md`

**Problemas encontrados:**
- ❌ Usa Coral (#E78C85) en badges, botones, title hover, divider, location, arrows, dots
- ❌ Usa Purple (#311A42) en badge/botón secondary y price
- ❌ Usa Gold (#CEA02D) en badge/botón gold
- ⚠️ Define 6 variantes de color (primary, secondary, white, gold, dark, transparent)
- ⚠️ No usa spacing/font-size scales de theme.json

**Recomendación:** Cambiar primary → Secondary theme.json, secondary → Primary theme.json

---

### 3. StaticCTA

**Archivo:** `/home/user/travel-exp/docs/auditoria-css/static-cta.md`

**Problemas encontrados:**
- ❌ Usa colores genéricos: Rojo (#e74c3c) y Azul (#3498db)
- ⚠️ Botones con nombres genéricos `.btn-primary`, `.btn-secondary` pueden colisionar
- ⚠️ No usa colores de theme.json en absoluto

**Recomendación:** Reemplazar rojo/azul por Primary/Secondary de theme.json, renombrar clases

---

### 4. StaticHero ✅

**Archivo:** `/home/user/travel-exp/docs/auditoria-css/static-hero.md`

**Problemas encontrados:**
- ✅ Ningún problema crítico
- ⚠️ Overlay opacity hardcodeada (minor)
- ⚠️ No usa spacing scale (minor)

**Recomendación:** Prioridad baja, solo mejoras menores

---

### 5. StickySideMenu

**Archivo:** `/home/user/travel-exp/docs/auditoria-css/sticky-side-menu.md`

**Problemas encontrados:**
- ❌ Usa Coral (#E78C85) en botón primary y enlaces hover
- ❌ Usa Purple (#311A42) en botón secondary
- ❌ Usa Gold (#CEA02D) en botón gold
- ✅ Phone color (#154D52) es MUY similar a Primary (#17565C) - fácil de actualizar
- ⚠️ Define 6 variantes de color para botones CTA

**Recomendación:** Actualizar phone → Primary, primary → Secondary, secondary → Primary

---

### 6. TaxonomyTabs

**Archivo:** `/home/user/travel-exp/docs/auditoria-css/taxonomy-tabs.md`

**Problemas encontrados:**
- ❌ **CRÍTICO:** Define variables `:root` (`--tt-primary-color`, etc.) pero NO las usa
- ❌ Usa Coral (#E78C85) en badges, botones, arrows, dots
- ❌ Usa Purple (#311A42) en badges, botones, tabs activos
- ❌ Usa Gold (#CEA02D) en variantes
- ❌ Usa azules (#2563eb, #1976d2) en algunas partes
- ⚠️ Importa Google Fonts (Saira Condensed, Inter) sin verificar theme
- ⚠️ Define 7 variantes de color
- ⚠️ Multiple grays no documentados

**Recomendación:** Limpiar variables no usadas, alinear con theme.json, verificar fonts

---

### 7. TeamCarousel

**Archivo:** `/home/user/travel-exp/docs/auditoria-css/team-carousel.md`

**Problemas encontrados:**
- ⚠️ Usa naranja/terracota (#d46a3f) - MUY similar a Secondary (#C66E65)
- ✅ NO usa Coral/Purple problemático
- ⚠️ Usa grays no definidos (#888, #f0f0f0, #e0e0e0)

**Recomendación:** Reemplazar #d46a3f → Secondary theme.json

---

## Análisis de Colores Hardcodeados

### Coral (#E78C85) - PROBLEMA CRÍTICO

**Usado en 5 bloques:**
1. PostsListAdvanced (navegación mobile)
2. SideBySideCards (badges, botones, dividers, arrows, dots)
3. StickySideMenu (botón primary, enlaces hover)
4. TaxonomyTabs (badges, botones, tabs, arrows)
5. Breadcrumb (ya auditado)

**Frecuencia:** ⚠️⚠️⚠️⚠️⚠️ MUY ALTA

---

### Purple (#311A42) - PROBLEMA CRÍTICO

**Usado en 4 bloques:**
1. SideBySideCards (badge/botón secondary, price)
2. StickySideMenu (botón secondary)
3. TaxonomyTabs (badges, botones, tabs activos)
4. Breadcrumb (ya auditado)

**Frecuencia:** ⚠️⚠️⚠️⚠️ ALTA

---

### Gold (#CEA02D) - PROBLEMA MODERADO

**Usado en 3 bloques:**
1. SideBySideCards (badge/botón gold)
2. StickySideMenu (botón gold)
3. TaxonomyTabs (badge/botón gold)

**Frecuencia:** ⚠️⚠️⚠️ MODERADA

---

### Otros colores problemáticos

- **Rojo/Azul genéricos** (StaticCTA): #e74c3c, #3498db
- **Naranja** (TeamCarousel): #d46a3f (similar a Secondary)
- **Gold variant** (PostsListAdvanced): #ffb400

---

## Decisión Estratégica Requerida

### Opción A: Actualizar theme.json (MÁS FÁCIL)

Agregar los colores Coral/Purple/Gold a theme.json como paleta "legacy":

```json
{
  "palette": [
    {
      "slug": "primary",
      "color": "#17565C",
      "name": "Primary (Teal)"
    },
    {
      "slug": "secondary",
      "color": "#C66E65",
      "name": "Secondary (Salmon)"
    },
    {
      "slug": "coral-legacy",
      "color": "#E78C85",
      "name": "Coral (Legacy)"
    },
    {
      "slug": "purple-legacy",
      "color": "#311A42",
      "name": "Purple (Legacy)"
    },
    {
      "slug": "gold",
      "color": "#CEA02D",
      "name": "Gold"
    }
  ]
}
```

**Ventajas:**
- ✅ Cambio mínimo en CSS
- ✅ Mantiene colores actuales
- ✅ Rápido de implementar

**Desventajas:**
- ❌ Mantiene inconsistencia de marca
- ❌ Dos paletas de colores en el sitio

---

### Opción B: Refactorizar bloques (MÁS CORRECTO)

Cambiar todos los bloques para usar Primary/Secondary de theme.json:

- Coral (#E78C85) → Secondary (#C66E65)
- Purple (#311A42) → Primary (#17565C)
- Gold (#CEA02D) → Agregar a theme.json o eliminar

**Ventajas:**
- ✅ Consistencia de marca
- ✅ Una sola paleta de colores
- ✅ Más mantenible a largo plazo

**Desventajas:**
- ❌ Requiere actualizar múltiples bloques
- ❌ Testing extensivo necesario
- ❌ Posibles cambios visuales notables

---

### Opción C: Híbrida (RECOMENDADA)

1. Agregar Gold (#CEA02D) a theme.json (es útil y no genera conflicto)
2. Mapear Coral → Secondary en bloques
3. Mapear Purple → Primary en bloques
4. Crear documentación de migración

**Ventajas:**
- ✅ Balance entre esfuerzo y mejora
- ✅ Mantiene Gold (color útil)
- ✅ Elimina colores conflictivos (Coral/Purple)

---

## Recomendaciones Finales

### Prioridad ALTA (Crítico)

1. **Decidir estrategia de colores** (Opción A, B, o C)
2. **TaxonomyTabs:** Limpiar variables `:root` no usadas
3. **StaticCTA:** Reemplazar colores rojo/azul genéricos
4. **Todos los bloques:** Implementar spacing/font-size scales de theme.json

### Prioridad MEDIA

1. **PostsListAdvanced:** Consolidar archivos CSS
2. **StaticCTA:** Renombrar clases `.btn-*` para evitar colisiones
3. **TaxonomyTabs:** Verificar Google Fonts vs theme.json

### Prioridad BAJA

1. **StaticHero:** Mejoras menores (spacing scale)
2. **TeamCarousel:** Actualizar #d46a3f → Secondary
3. **Todos:** Crear variables locales para transitions

---

## Próximos Pasos

1. ❓ **DECISIÓN:** ¿Opción A, B, o C para colores?
2. Crear branch: `refactor/acf-blocks-css-alignment`
3. Implementar cambios según decisión
4. Testing exhaustivo en editor y frontend
5. Documentar cambios y crear guía de migración
6. Commit por bloque con mensajes descriptivos
7. Pull request con resumen de cambios

---

## Archivos Generados

- `/docs/auditoria-css/posts-list-advanced.md`
- `/docs/auditoria-css/side-by-side-cards.md`
- `/docs/auditoria-css/static-cta.md`
- `/docs/auditoria-css/static-hero.md`
- `/docs/auditoria-css/sticky-side-menu.md`
- `/docs/auditoria-css/taxonomy-tabs.md`
- `/docs/auditoria-css/team-carousel.md`
- `/docs/auditoria-css/RESUMEN-BLOQUES-ACF.md` (este archivo)
- `/docs/auditoria-css/breadcrumb.md` (ya existía - usado como plantilla)

---

**Total:** 8 archivos de auditoría (7 nuevos + 1 existente)
