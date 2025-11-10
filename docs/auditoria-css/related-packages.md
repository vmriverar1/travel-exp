# Auditoría: RelatedPackages

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/related-packages.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**USA variables CSS locales** definidas en el mismo archivo.

### Variables locales definidas:

```css
.related-packages {
  --transition-speed: 0.3s;
  --elevation-1 a --elevation-4: Box shadows
  --card-gap: 24px;
  --color-primary: #E78C85;
  --color-secondary: #311A42;
  --color-gold: #CEA02D;
  --color-dark: #212121;
  --color-white: #ffffff;
}
```

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#E78C85` | --color-primary (coral) | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#311A42` | --color-secondary (purple) | ❌ No existe | **PROBLEMA:** Color purple no está |
| `#CEA02D` | --color-gold | ❌ No existe | Color custom para badges |
| `#F3CE72` | Badge horizontal (gold) | ❌ No existe | Variante de gold |
| `#212121` | Textos oscuros | ❌ No exacto | Similar a contrast |
| `#dc7b74`, `#d97670`, `#402753`, `#451f5c`, `#b88f28` | Hover variants | ❌ No existen | Derivados de primary/secondary |
| Múltiples colores en horizontal variant | Textos, badges | ❌ Mezcla | Distintos colores para variante horizontal |

### Imports externos:

```css
@import url('https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
```

**⚠️ PROBLEMA:** Importa Google Fonts directamente (pueden afectar performance)

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | Muchos valores px | Varios tamaños de texto |
| Spacing | Múltiples rem/px | Padding, margin, gap |
| Border-radius | `12px`, `10px`, `8px`, `24px` | Cards, badges, buttons |
| Min-height | `350px`, `480px`, `460px`, `440px`, `180px`, `160px`, `140px` | Card heights |
| Box-shadow | Múltiples elevations | Card shadows |
| Font-family | 'Satoshi', 'Saira Condensed', 'Inter' | Tipografías custom |

---

## Análisis

### ⚠️ Problemas Principales

1. **Paleta de colores incompatible**: Usa coral/purple que NO existen en theme.json
2. **Google Fonts imports**: Afectan performance y GDPR
3. **Tipografías custom**: Usa Satoshi, Saira Condensed, Inter (no documentadas)
4. **Archivo muy largo**: 1158 líneas (demasiado complejo)
5. **Dos variantes**: Vertical y Horizontal con estilos muy diferentes

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon)

**RelatedPackages usa:**
- Primary: #E78C85 (coral)
- Secondary: #311A42 (purple)
- Gold: #CEA02D, #F3CE72

### Hallazgos Positivos

✅ Ya usa sistema de variables CSS locales
✅ Buenos selectores con prefijo `.related-packages`, `.rp-`
✅ Responsive design completo
✅ Accessibility (prefers-reduced-motion, focus-visible)
✅ Loading states
✅ Múltiples variantes de botones y badges

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir todas las paletas
2. **Opción B:** Mapear a theme.json (coral → secondary, purple → primary)
3. **Opción C:** Mantener como bloque independiente con su propia paleta
4. **Opción D:** Dividir en dos bloques separados (vertical y horizontal)

**Recomendación:** Opción B + D - Mapear colores Y considerar dividir el bloque

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.related-packages {
  --color-primary: #E78C85;
  --color-secondary: #311A42;
}

/* DESPUÉS */
.related-packages {
  --color-primary: var(--wp--preset--color--secondary); /* #C66E65 salmon */
  --color-secondary: var(--wp--preset--color--primary); /* #17565C teal */
  --color-gold: #CEA02D; /* Mantener como custom */
  --color-dark: var(--wp--preset--color--contrast);
  --color-white: var(--wp--preset--color--base);
}
```

### Google Fonts:

**Eliminar imports** y usar fuentes del sistema o theme.json:

```css
/* Opción 1: Sistema */
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', ...

/* Opción 2: Theme.json */
font-family: var(--wp--preset--font-family--body);
```

### Variables locales necesarias (muchas ya existen):

El bloque ya tiene buen sistema de variables locales. Solo necesita:
1. Mapear colores a theme.json
2. Eliminar/reemplazar Google Fonts
3. Simplificar si es posible

---

## CSS Personalizado Necesario: **SÍ (MUCHO)**

**Razón:** Bloque muy complejo con:
- Sistema de cards con overlay gradient
- Swiper/slider mobile
- Dos variantes completamente diferentes (vertical/horizontal)
- Múltiples variantes de colores (badges, buttons)
- Sistema de navegación y dots
- Estados de loading

---

## Selectores Específicos: ✅ OK

Todos los selectores usan prefijos `.related-packages`, `.rp-card`, `.rp-slider__`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir estrategia**: ¿Dividir en dos bloques o mantener uno?
2. ❓ **Decidir sobre Google Fonts**: ¿Eliminar o cargar desde theme.json?
3. Mapear `--color-primary` → `--wp--preset--color--secondary`
4. Mapear `--color-secondary` → `--wp--preset--color--primary`
5. Reemplazar Google Fonts por sistema o theme.json fonts
6. Testing exhaustivo (muchas variantes)
7. Commit: `refactor(related-packages): migrate to theme.json, optimize fonts`

---

## Notas Adicionales

Este es el bloque **MÁS COMPLEJO** de todos los auditados. Considerar:
- **Dividir responsabilidades**: Crear `related-packages-vertical` y `related-packages-horizontal`
- **Optimizar assets**: Lazy load de estilos según variante
- **Documentar variantes**: Crear guía de uso para cada variante
