# Refactorización de Bloques Deal y Template

**Fecha:** 2025-11-09
**Tarea:** Reemplazo de colores legacy por variables de theme.json

## Resumen

Se han refactorizado **7 bloques CSS** reemplazando colores hardcodeados por variables CSS del theme.json.

---

## Bloques Refactorizados

### Bloques Deal (3)

#### 1. deal-info-card.css
**Archivo:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/deal-info-card.css`

**Cambios realizados:**
- `#2563eb` → `var(--wp--preset--color--primary)` (5 ocurrencias)
  - Línea 7: Border del card
  - Línea 32: Color del valor de descuento
  - Línea 40: Color del label de descuento
  - Línea 78: Color del icono de fecha
  - Línea 113: Background del botón CTA

**Elementos afectados:**
- `.deal-info-card` (border)
- `.deal-info-card__discount-value` (color)
- `.deal-info-card__discount-label` (color)
- `.deal-info-card__date-icon` (color)
- `.deal-info-card__button` (background)

---

#### 2. deal-packages-grid.css
**Archivo:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/deal-packages-grid.css`

**Cambios realizados:**
- `#2563eb` → `var(--wp--preset--color--primary)` (4 ocurrencias)
  - Línea 88: Background del badge promocional
  - Línea 121: Color hover del título
  - Línea 181: Color del precio
  - Línea 191: Background del botón

**Elementos afectados:**
- `.deal-package-card__badge` (background)
- `.deal-package-card__title a:hover` (color)
- `.deal-package-card__price-value` (color)
- `.deal-package-card__button` (background)

---

#### 3. deals-slider.css
**Archivo:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/deals-slider.css`

**Cambios realizados:**
- `#e78c85` → `var(--wp--preset--color--secondary)` (4 ocurrencias)
  - Línea 15: Variable local `--color-accent-pink`
  - Línea 492: Background de flecha prev
  - Línea 493: Border color de flecha prev

**Elementos afectados:**
- `.deals-slider` (variable CSS local)
- `.deals-slider__arrow--prev` (background y border-color)

**Nota:** Se mantuvo la variable local `--color-primary-green-dark: #0a797e` según instrucciones.

---

### Bloques Template (4)

#### 4. template/breadcrumb.css
**Archivo:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/template/breadcrumb.css`

**Cambios realizados:**
- `#E78C85` → `var(--wp--preset--color--secondary)` (1 ocurrencia)
  - Línea 36: Color hover de enlaces

**Elementos afectados:**
- `.breadcrumb-item a:hover` (color)

---

#### 5. template/hero-media-grid.css
**Archivo:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/template/hero-media-grid.css`

**Cambios realizados:**
- `#E78C85` → `var(--wp--preset--color--secondary)` (3 ocurrencias)
  - Línea 96: Background del badge de descuento
  - Línea 124: Background del botón "View All Photos"
  - Línea 204: Background del activity dot activo

**Elementos afectados:**
- `.hero-gallery__discount-badge` (background)
- `.hero-gallery__view-button` (background)
- `.activity-dot.active` (background)

---

#### 6. template/package-header.css
**Archivo:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/template/package-header.css`

**Cambios realizados:**
- `#E78C85` → `var(--wp--preset--color--secondary)` (1 ocurrencia)
  - Línea 172: Color de iconos de metadata (reemplazó `var(--color-coral, #E78C85)`)

**Elementos afectados:**
- `.metadata-icon` (color)

---

#### 7. template/promo-cards.css
**Archivo:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/template/promo-cards.css`

**Estado:** ✅ No requiere refactorización
- No contiene colores legacy del mapeo

---

### Bloques Verificados (sin cambios)

#### 8. faq-accordion.css
**Archivo:** `/home/user/travel-exp/wp-content/plugins/travel-blocks/assets/blocks/faq-accordion.css`

**Estado:** ✅ No requiere refactorización
- Contiene `#e74c3c` pero no está en la lista de colores legacy a refactorizar

---

#### 9. taxonomy-archive-hero
**Estado:** ❌ No encontrado
- No se encontró archivo CSS propio para este bloque

---

## Mapeo de Colores Aplicado

| Color Legacy | Variable Theme.json | Descripción |
|-------------|-------------------|-------------|
| `#E78C85` | `var(--wp--preset--color--secondary)` | Coral/Pink |
| `#2563eb` | `var(--wp--preset--color--primary)` | Azul Deal |
| `#0a797e` | Variable local | Green dark (mantenido) |

---

## Estadísticas

- **Bloques refactorizados:** 6 de 9
- **Bloques sin cambios:** 2 de 9
- **Bloques no encontrados:** 1 de 9
- **Total de reemplazos:** 17 ocurrencias
- **Colores legacy eliminados:** 2 tipos (`#E78C85`, `#2563eb`)

---

## Archivos Modificados

```
wp-content/plugins/travel-blocks/assets/blocks/
├── deal-info-card.css                    ✅ Refactorizado
├── deal-packages-grid.css                ✅ Refactorizado
├── deals-slider.css                      ✅ Refactorizado
├── faq-accordion.css                     ⚪ Sin cambios
└── template/
    ├── breadcrumb.css                    ✅ Refactorizado
    ├── hero-media-grid.css               ✅ Refactorizado
    ├── package-header.css                ✅ Refactorizado
    └── promo-cards.css                   ⚪ Sin cambios
```

---

## Próximos Pasos

1. ✅ Revisar visualmente los bloques en el frontend
2. ✅ Verificar que las variables CSS del theme.json están correctamente definidas
3. ✅ Probar responsividad en diferentes breakpoints
4. ⏳ Actualizar documentación de componentes si existe
5. ⏳ Crear commit con los cambios

---

## Notas Técnicas

- **Compatibilidad:** Se utilizan variables CSS nativas de WordPress (`--wp--preset--color--*`)
- **Fallbacks:** Los colores de hover hardcodeados se mantuvieron para transiciones suaves
- **Variables locales:** Se respetó `--color-primary-green-dark` según instrucciones
- **Formato:** Se mantuvieron indentación y estructura original de cada archivo
