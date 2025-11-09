# Auditoría: DealsSlider

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/deals-slider.css`
**Categoría:** Bloque ACF - Deal
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS locales** definidas en el mismo archivo.

### Variables locales definidas:

```css
.deals-slider {
  --color-primary-green-dark: #0a797e;
  --color-primary-green-medium: #1a8a8f;
  --color-accent-yellow: #FFE500;
  --color-accent-pink: #e78c85;
  --color-text-dark: #1F2937;
  --color-text-gray: #6B7280;
  --color-border-light: #E5E7EB;
  --color-white: #FFFFFF;

  --countdown-bar-height: 80px;
  --card-border-radius: 16px;
  --card-padding: 20px;
  --card-min-height: 280px;
}
```

### Imports externos:

```css
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
```

**⚠️ PROBLEMA:** Importa Google Fonts directamente (puede afectar performance)

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#0a797e`, `#1a8a8f` | Green primary/medium | ❌ No existe | **NUEVA PALETA** teal green |
| `#FFE500` | Yellow accent (countdown) | ❌ No existe | Amarillo brillante |
| `#e78c85` | Pink/coral accent (ribbon, stars) | ❌ No existe | Coral (de otros bloques) |
| `#1F2937`, `#6B7280` | Text colors | ❌ No existen | Grays nuevos |
| `#E5E7EB` | Borders | ❌ No existe | Gray light |
| `#17565C99` | Price text (teal con opacity) | ✅ Similar | Primary de theme.json con alpha |
| `#17565C` | Price label | ✅ Sí | **¡USA Primary de theme.json!** |
| `#C66E65` | Pagination active | ✅ Sí | **¡USA Secondary de theme.json!** |
| `#E78C85` | Arrow prev | ❌ No existe | Coral |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | Múltiples valores | Countdown, titles, features, buttons |
| Spacing | Múltiples valores | Padding, margin, gap |
| Font-family | 'Poppins' | Custom font |
| Heights | `80px`, `280px`, `240px`, etc. | Countdown bar, cards, images |
| Border-radius | `16px`, `12px`, `9999px`, `8px` | Cards, buttons, pills |
| Min-width | `64px`, `56px`, `44px` | Countdown pills, arrows |
| Box-shadow | Múltiples | Cards, buttons, arrows |
| Transform | `rotate(-35deg)`, `scale()`, `translateY()`, `translateX()` | Ribbon, hover effects |

---

## Análisis

### ⚠️ Problemas Principales

1. **PALETA MIXTA:** Combina Green/Yellow/Pink con colores de theme.json
2. **Google Fonts import:** Poppins cargado directamente
3. **Archivo muy largo:** 806 líneas (complejo)
4. **Variables locales inconsistentes:** Algunas usan theme.json, otras no

**Paletas mezcladas:**
- **Deal Blue** (DealInfoCard/Grid): #2563eb
- **Teal Green** (DealsSlider): #0a797e, #1a8a8f
- **Theme.json Primary/Secondary:** #17565C (usa parcialmente), #C66E65 (usa parcialmente)
- **Yellow:** #FFE500 (nuevo)
- **Coral/Pink:** #e78c85 (de otros bloques)

### Hallazgos Positivos

✅ Ya usa algunas variables locales
✅ **USA Primary y Secondary de theme.json** (en price y pagination)
✅ Buenos selectores con prefijo `.deals-slider`
✅ Responsive design completo
✅ Countdown timer
✅ Swiper slider
✅ Accessibility (focus-visible, screen reader)
✅ Grid layout complejo (42% image, 58% content)

### Problema de Identidad Visual

Este bloque mezcla **3 paletas diferentes**:
1. Teal Green (#0a797e, #1a8a8f) - Botones
2. Theme.json Primary/Secondary (#17565C, #C66E65) - Price, pagination
3. Yellow/Pink (#FFE500, #e78c85) - Countdown, ribbon, stars

**INCONSISTENCIA:** Los otros bloques Deal usan Blue (#2563eb), este usa Teal Green.

### Decisión Requerida

1. **Opción A:** Unificar todos los bloques Deal con paleta Blue
2. **Opción B:** Mantener DealsSlider con paleta Green única
3. **Opción C:** Usar SOLO colores de theme.json (Primary/Secondary)
4. **Opción D:** Crear Deal Theme completo con todas las variantes

**Recomendación:** Opción A - Unificar con paleta Blue para consistencia

---

## Plan de Refactorización

### Opción A - Unificar con Deal Blue:

```css
/* ANTES */
.deals-slider {
  --color-primary-green-dark: #0a797e;
  --color-primary-green-medium: #1a8a8f;
  --color-accent-yellow: #FFE500;
  --color-accent-pink: #e78c85;
}

/* DESPUÉS */
.deals-slider {
  --color-primary: var(--wp--preset--color--deal-primary); /* #2563eb */
  --color-primary-dark: var(--wp--preset--color--deal-dark); /* #1d4ed8 */
  --color-accent-yellow: #FFE500; /* Mantener como unique */
  --color-accent: var(--wp--preset--color--secondary); /* #C66E65 en lugar de pink */
  --color-text-dark: var(--wp--preset--color--contrast);
  --color-white: var(--wp--preset--color--base);
}
```

### Google Fonts:

**Eliminar import** y opciones:
1. Usar fuente del sistema
2. Cargar desde theme.json
3. Usar self-hosted fonts

### Variables locales necesarias (muchas ya existen):

```css
.deals-slider {
  /* Ya definidas - mantener y mejorar */
  --countdown-bar-height: 80px;
  --card-border-radius: 16px;
  --card-padding: 20px;
  --card-min-height: 280px;

  /* Agregar */
  --slider-spacing-sm: 0.5rem;
  --slider-spacing-md: 1rem;
  --slider-spacing-lg: 1.5rem;
  --slider-spacing-xl: 2rem;

  /* Typography (sin Poppins) */
  --slider-font-family: var(--wp--preset--font-family--body);

  /* Transitions */
  --slider-transition: all 0.2s;
}
```

---

## CSS Personalizado Necesario: **SÍ (MUCHO)**

**Razón:** Bloque extremadamente complejo con:
- Background images responsive
- Countdown timer con pills
- Swiper slider
- Grid layout complejo (42%/58%)
- Ribbon diagonal
- Stars rating
- Features grid (2x2)
- Navigation arrows + dots
- Responsive breakpoints complejos

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.deals-slider__`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **DECISIÓN CRÍTICA:** ¿Unificar paleta Deal (Blue vs Green)?
2. ❓ **Decidir sobre Google Fonts Poppins**
3. Coordinar con DealInfoCard y DealPackagesGrid
4. Si unificar: Reemplazar green → blue, pink → secondary
5. Eliminar/reemplazar Google Fonts import
6. Mantener yellow como accent único del slider
7. Testing exhaustivo (countdown, slider, responsive)
8. Commit: `refactor(deals-slider): unify Deal color palette, optimize fonts`

---

## Notas Adicionales

**IMPORTANTE:** Este es el bloque Deal **MÁS COMPLEJO** y tiene identidad visual diferente a los otros bloques Deal.

**Recomendaciones:**
1. **Coordinar con equipo de diseño:** ¿Por qué Green en lugar de Blue?
2. **Documentar decisión:** Si se mantiene Green, documentar por qué
3. **Deal Design System:** Crear guía unificada para todos los bloques Deal
4. **Performance:** Optimizar imports de fonts y background images
5. **Testing:** Verificar countdown timer con diferentes timezones

**Bloques Deal a coordinar:**
- DealInfoCard (Blue)
- DealPackagesGrid (Blue)
- DealsSlider (Green) ← Este

¿Deben todos usar la misma paleta o cada uno puede tener su identidad?
