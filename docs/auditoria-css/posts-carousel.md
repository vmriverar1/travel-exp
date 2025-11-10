# Auditoría: PostsCarousel

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/posts-carousel.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**Define algunas variables locales** - Pero la mayoría de valores están hardcodeados.

### Variables propias definidas:

| Variable | Valor | ¿Existe en theme.json? | Observaciones |
|----------|-------|------------------------|---------------|
| `--card-gap` | 24px | ❌ No | Spacing local - OK |
| `--transition-speed` | 0.3s | ❌ N/A | Transition - OK |
| `--elevation-1` | 0 2px 4px rgba(...) | ❌ N/A | Shadow - OK |
| `--elevation-2` | 0 4px 8px rgba(...) | ❌ N/A | Shadow - OK |
| `--elevation-3` | 0 8px 16px rgba(...) | ❌ N/A | Shadow - OK |
| `--elevation-4` | 0 12px 24px rgba(...) | ❌ N/A | Shadow - OK |
| `--card-height` | 450px | ❌ N/A | Height override - OK |

### Colores hardcodeados encontrados (MUY ABUNDANTES):

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#311A42` | `.pc-card__badge`, badge secondary | ❌ No existe | **PROBLEMA:** Color purple no está en theme.json |
| `#402753` | Badge hover | ❌ No existe | **PROBLEMA:** Purple hover no está en theme.json |
| `#E78C85` | Arrows, buttons, badges, dots, favorite hover | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#dc7b74` | Hover states (arrows, buttons) | ❌ No existe | **PROBLEMA:** Coral hover no está en theme.json |
| `#e74c3c` | Favorite button hover, alternative red | ❌ No existe | **PROBLEMA:** Color rojo no está en theme.json |
| `#333` | Text colors, meta items | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#666` | Text muted, vertical card | ✅ Sí | Usar `var(--wp--preset--color--gray)` (#666666) |
| `#1a1a1a` | Title vertical card | ❌ No exacto | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#fff` | Text on overlay, backgrounds | ✅ Sí | Usar `var(--wp--preset--color--base)` (#FFFFFF) |
| `#000` | Badge dark variant | ✅ Sí (conceptualmente) | Usar `var(--wp--preset--color--contrast)` |
| `#1976d2`, `#1565c0` | Read more button (blue) | ❌ No existe | **PROBLEMA:** Color azul no está en theme.json |
| `#CEA02D`, `#b88f25` | Gold button/badge variants | ❌ No existe | **PROBLEMA:** Color dorado no está en theme.json |
| `#bdbdbd` | Dots border | ❌ No existe | Usar gray con opacity |
| `#e0e0e0` | Divider vertical | ❌ No existe | Usar gray con opacity |
| `#f0f0f0`, `#e0e0e0` | Loading shimmer | ❌ N/A | Crear variable local |

### Overlays y gradientes:

| Valor | Dónde se usa |
|-------|--------------|
| `linear-gradient(to top, rgba(0,0,0,0.9) 0%, ... transparent 100%)` | Card overlay default |
| `linear-gradient(to top, rgba(0,0,0,0.95) 0%, ... transparent 100%)` | Overlay-split variant |
| `rgba(255, 255, 255, 0.9)`, `0.95` | Favorite button background |
| Múltiples rgba() para shadows | Todo el archivo |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `22px`, `14px`, `13px`, `18px`, `20px`, `16px`, `15px`, `11px`, `12px`, `1.25rem`, `0.95rem`, `1.1rem` | Tamaños de texto |
| Spacing | `24px`, `20px`, `16px`, `12px`, `8px`, `6px`, `40px`, `60px`, `30px`, `10px`, `4px`, `2px` | Padding, margin, gap |
| Border-radius | `12px`, `20px`, `24px`, `50%`, `4px`, `9999px` | Redondeo de elementos |
| Transition | `0.3s`, `0.2s`, `0.4s`, `cubic-bezier(0.4, 0, 0.2, 1)` | Animaciones |
| Heights | `450px`, `400px`, `380px`, `220px`, `280px`, `500px`, `44px`, `48px`, `40px`, `36px` | Alturas |
| Widths | `70%`, `100%`, `44px`, `48px`, `24px`, `16px`, `14px`, `22px`, `8px`, `50%` | Anchos |
| Max-width | `1400px`, `800px` | Contenedor |

---

## Análisis

### ⚠️ Problemas Principales

1. **Paleta Coral/Purple predominante**: El bloque usa INTENSAMENTE:
   - **Coral** (#E78C85) - usado en arrows, buttons, badges, dots, favorite hover - **NO existe en theme.json**
   - **Purple** (#311A42) - usado en badges - **NO existe en theme.json**
   - Estas son las **mismas colores** que Breadcrumb

2. **Múltiples paletas de colores alternativas**: El bloque define variaciones de color para botones y badges:
   - Primary (coral #E78C85)
   - Secondary (purple #311A42)
   - White (#fff)
   - Gold (#CEA02D) - **NO existe en theme.json**
   - Dark (#1A1A1A)
   - Transparent
   - Read More (blue #1976d2) - **NO existe en theme.json**
   - Line Arrow (coral #E78C85)

3. **Archivo muy extenso y complejo**: 1589 líneas con múltiples variantes de diseño:
   - Default overlay card
   - Vertical card
   - Overlay-split card
   - Desktop grid con 6 efectos hover diferentes (zoom, squeeze, lift, glow, tilt, fade, slide)
   - Mobile slider con 3 variantes de arrows
   - Múltiples responsive breakpoints

4. **Importaciones de Google Fonts**: Líneas 7-9 importan Saira Condensed e Inter.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**PostsCarousel usa:**
- Primary: #E78C85 (coral)
- Secondary: #311A42 (purple)
- Gold: #CEA02D
- Blue: #1976d2
- Red: #e74c3c

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85), purple (#311A42), gold (#CEA02D), y blue (#1976d2)
2. **Opción B:** Cambiar PostsCarousel para usar Primary/Secondary de theme.json:
   - Coral (#E78C85) → Secondary (#C66E65)
   - Purple (#311A42) → Primary (#17565C)
   - Eliminar variantes gold y blue, o usar Primary/Secondary
3. **Opción C:** Crear variables locales dentro del bloque para toda la paleta personalizada

**Recomendación:** Opción B con variables locales - Usar Secondary de theme.json como color principal, crear variables locales para las variantes de color adicionales que el usuario puede elegir.

---

## Plan de Refactorización

### Fase 1: Cambios principales

```css
/* ANTES */
.pc-card__badge {
  background: #311A42;
}

.arrows-sides .pc-arrow--prev,
.pc-card__button,
.pc-dot.is-active {
  background: #E78C85;
}

/* DESPUÉS */
.posts-carousel {
  /* Colores principales de theme.json */
  --carousel-primary: var(--wp--preset--color--secondary, #C66E65);
  --carousel-secondary: var(--wp--preset--color--primary, #17565C);

  /* Colores de texto */
  --carousel-text-light: var(--wp--preset--color--base, #FFFFFF);
  --carousel-text-dark: var(--wp--preset--color--contrast, #111111);
  --carousel-text-muted: var(--wp--preset--color--gray, #666666);
}

.pc-card__badge {
  background: var(--carousel-secondary);
}

.arrows-sides .pc-arrow--prev,
.pc-card__button,
.pc-dot.is-active {
  background: var(--carousel-primary);
}
```

### Fase 2: Variables locales completas

```css
.posts-carousel {
  /* === COLORES DE THEME.JSON === */
  --carousel-primary: var(--wp--preset--color--secondary);
  --carousel-primary-hover: #b55e54; /* Secondary oscurecido */
  --carousel-secondary: var(--wp--preset--color--primary);
  --carousel-secondary-hover: #0f3f43; /* Primary oscurecido */

  --carousel-text-light: var(--wp--preset--color--base);
  --carousel-text-dark: var(--wp--preset--color--contrast);
  --carousel-text-muted: var(--wp--preset--color--gray);

  /* === COLORES ADICIONALES (variantes seleccionables) === */
  --carousel-white: #FFFFFF;
  --carousel-dark: #1A1A1A;
  --carousel-gold: #CEA02D;
  --carousel-gold-hover: #b88f25;
  --carousel-blue: #1976d2;
  --carousel-blue-hover: #1565c0;

  /* === COLORES UTILITARIOS === */
  --carousel-border-light: #e0e0e0;
  --carousel-border-dots: #bdbdbd;
  --carousel-bg-hover: #f9f9f9;
  --carousel-bg-card: #fff;

  /* === SPACING === */
  --carousel-gap: 24px;
  --carousel-spacing-xs: 6px;
  --carousel-spacing-sm: 12px;
  --carousel-spacing-md: 20px;
  --carousel-spacing-lg: 24px;
  --carousel-spacing-xl: 40px;
  --carousel-spacing-xxl: 60px;

  /* === TRANSITIONS === */
  --carousel-transition-fast: 0.2s ease;
  --carousel-transition-normal: 0.3s ease;
  --carousel-transition-slow: 0.4s ease;
  --carousel-transition-cubic: cubic-bezier(0.4, 0, 0.2, 1);

  /* === ELEVATIONS (Material Design) === */
  --elevation-1: 0 2px 4px rgba(0, 0, 0, 0.1);
  --elevation-2: 0 4px 8px rgba(0, 0, 0, 0.12);
  --elevation-3: 0 8px 16px rgba(0, 0, 0, 0.15);
  --elevation-4: 0 12px 24px rgba(0, 0, 0, 0.18);

  /* === BORDER RADIUS === */
  --carousel-radius-sm: 4px;
  --carousel-radius-md: 12px;
  --carousel-radius-lg: 20px;
  --carousel-radius-xl: 24px;
  --carousel-radius-full: 9999px;

  /* === HEIGHTS === */
  --carousel-card-height: 450px;
  --carousel-card-height-mobile: 400px;
  --carousel-card-height-small: 380px;
  --carousel-arrow-size: 48px;
  --carousel-arrow-size-small: 36px;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Bloque altamente personalizado que requiere:
1. Sistema completo de variables para colores, spacing, transitions, shadows
2. Múltiples variantes de diseño (default, vertical, overlay-split)
3. Sistema de efectos hover (7 variantes)
4. Sistema de arrows para mobile (3 variantes)
5. Material Design elevations
6. Variantes de color para botones y badges (8 opciones)
7. Grid layout complejo con filas y squeeze effects
8. Loading states con shimmer animation
9. Responsive design completo (desktop, tablet, mobile)

**Este es uno de los bloques MÁS complejos del sistema.**

---

## Selectores Específicos: ✅ OK

Todos los selectores usan los prefijos `.posts-carousel` y `.pc-*`, no hay conflictos globales.

---

## Observaciones Adicionales

### 1. Google Fonts

El archivo importa fuentes de Google (líneas 7-9):
```css
@import url('https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
```

**Problema:** Estas importaciones deberían estar en theme.json o en el head del sitio, no en un archivo CSS de bloque.

**Referencias a fuentes:**
- Líneas 187, 196, 1399, 1409: `font-family: 'Satoshi', sans-serif;` (pero Satoshi no está importado, probablemente error)
- Las fuentes importadas (Saira Condensed, Inter) no se usan en el archivo

**Acción:** Eliminar @import statements, verificar si las fuentes se necesitan.

### 2. Complejidad del archivo

Con 1589 líneas, este es un archivo muy complejo que podría beneficiarse de:
- Separación en archivos parciales (base, variants, responsive)
- Sistema de variables más organizado
- Documentación de las variantes disponibles

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral/purple vs theme.json primary/secondary)
2. Eliminar @import de Google Fonts
3. Verificar uso de fuentes y corregir 'Satoshi' no importado
4. Reemplazar #E78C85 (coral) por Secondary de theme.json
5. Reemplazar #311A42 (purple) por Primary de theme.json
6. Crear sistema completo de variables locales (colores, spacing, transitions, etc.)
7. Considerar refactorizar en archivos parciales si es necesario
8. Testing extensivo de todas las variantes (3 card styles × 7 hover effects × 3 arrow variants)
9. Commit: `refactor(posts-carousel): use theme.json colors, add comprehensive CSS variables system`

---

## Impacto

**ALTO** - Este bloque es usado intensivamente y tiene muchas variantes. Los cambios de color (especialmente coral → secondary) serán muy visibles en todo el sitio donde se use este carousel.

**Requiere:** Testing visual exhaustivo después de la refactorización.
