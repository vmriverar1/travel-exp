# Resumen de Auditoría CSS - Bloques ACF

**Fecha:** 2025-11-09
**Bloques auditados:** 4 de 7 solicitados
**Auditor:** Claude Code

---

## Bloques Auditados

| Bloque | Archivo CSS | Estado | Complejidad |
|--------|-------------|--------|-------------|
| ✅ ContactForm | `contact-form.css` | Auditado | Alta |
| ✅ FAQAccordion | `faq-accordion.css` | Auditado | Media |
| ✅ HeroSection | `hero-section.css` | Auditado | Baja |
| ✅ PostsCarousel | `posts-carousel.css` | Auditado | Muy Alta |
| ❌ FlexibleGridCarousel | No encontrado | - | - |
| ❌ HeroCarousel | No encontrado | - | - |
| ❌ PostsCarouselNative | No encontrado | - | - |

**Nota:** 3 bloques no tienen archivos CSS correspondientes en `/wp-content/plugins/travel-blocks/assets/blocks/`

---

## Problemas Críticos Encontrados

### 🚨 1. Paleta de Colores No Alineada con theme.json

**PROBLEMA MÁS GRAVE:** Todos los bloques auditados usan colores que **NO existen en theme.json**:

#### Colores Coral/Rose (#E78C85)
- **Usado en:** ContactForm, PostsCarousel
- **No existe en theme.json**
- **Equivalente sugerido:** Secondary (#C66E65)

#### Color Purple (#311A42)
- **Usado en:** PostsCarousel
- **No existe en theme.json**
- **Equivalente sugerido:** Primary (#17565C)

#### Color Rojo (#e74c3c)
- **Usado en:** FAQAccordion, HeroSection
- **No existe en theme.json**
- **Equivalente sugerido:** Secondary (#C66E65)

#### Color Azul (#3498db, #1976d2)
- **Usado en:** FAQAccordion, PostsCarousel
- **No existe en theme.json**
- **Equivalente sugerido:** Primary (#17565C)

#### Color Dorado (#CEA02D)
- **Usado en:** PostsCarousel
- **No existe en theme.json**
- **Acción:** Decidir si mantener como variante opcional

### Comparación de Paletas

| theme.json | Bloques ACF |
|------------|-------------|
| Primary: #17565C (teal) | Coral: #E78C85 |
| Secondary: #C66E65 (salmon) | Purple: #311A42 |
| Gray: #666666 | Red: #e74c3c |
| Contrast: #111111 | Blue: #3498db |
| Base: #FFFFFF | Gold: #CEA02D |

**Conclusión:** Existe un **conflicto fundamental de identidad de marca** entre theme.json y los bloques ACF.

---

## 🔴 2. Variables CSS en :root (Conflictos Globales)

**ContactForm** define variables globales en `:root`:
```css
:root {
    --rose: #E78C85;
    --green-dark: #0A797E;
    --text-dark: #1F2937;
    /* ... 20+ variables más */
}
```

**Problema:** Estas variables contaminan el scope global y pueden causar conflictos con:
- Otros bloques
- El tema principal
- Plugins de terceros

**Acción:** Mover todas las variables a `.hero-form { ... }` para scope local.

---

## 🟡 3. No Uso de Variables de theme.json

| Bloque | Usa variables de theme.json | Define variables propias |
|--------|----------------------------|--------------------------|
| ContactForm | ❌ No | ✅ Sí (en :root) |
| FAQAccordion | ❌ No | ❌ No |
| HeroSection | ❌ No | ❌ No |
| PostsCarousel | ❌ No | ✅ Sí (locales) |

**Ningún bloque auditado** usa variables CSS de theme.json como:
- `var(--wp--preset--color--primary)`
- `var(--wp--preset--color--secondary)`
- `var(--wp--preset--spacing--*)`
- `var(--wp--preset--font-size--*)`

---

## 🟡 4. Valores Hardcodeados

### Font-sizes hardcodeados (px)
| Bloque | Font-sizes encontrados |
|--------|------------------------|
| ContactForm | `22px`, `14px`, `15px`, `11px`, `19px`, `26px` |
| FAQAccordion | `clamp(...)`, `1.125rem`, `1rem`, `0.9375rem` (mejor) |
| HeroSection | `clamp(...)`, `1.125rem`, `1rem` (mejor) |
| PostsCarousel | `22px`, `14px`, `13px`, `18px`, `20px`, `16px`, `15px`, `11px`, `12px` (muy variado) |

**Observación:** FAQAccordion y HeroSection usan `rem` y `clamp()` (mejor práctica), pero ContactForm y PostsCarousel usan `px` (menos flexible).

### Spacing hardcodeado (px)
Todos los bloques usan valores de spacing hardcodeados en `px`:
- `8px`, `12px`, `16px`, `20px`, `24px`, `30px`, `40px`, `60px`

**theme.json tiene:**
- `--wp--preset--spacing--20` (0.25rem)
- `--wp--preset--spacing--30` (0.5rem)
- `--wp--preset--spacing--50` (1rem)

**Acción:** Usar variables de spacing de theme.json donde sea posible.

### Transitions hardcodeadas
Todos los bloques definen transitions inline:
- `0.2s ease`, `0.3s ease`, `0.4s ease`, `all 0.3s ease`

**Acción:** Crear variables locales para transitions consistentes.

---

## 🟢 5. Selectores Específicos

**✅ TODOS los bloques usan selectores específicos:**
- `.hero-form` (ContactForm)
- `.faq-accordion` (FAQAccordion)
- `.hero-section` (HeroSection)
- `.posts-carousel`, `.pc-*` (PostsCarousel)

**No hay conflictos de selectores globales** (excepto las variables en :root de ContactForm).

---

## Estadísticas de Complejidad

| Bloque | Líneas CSS | Variables definidas | Colores únicos | Responsive breakpoints |
|--------|-----------|---------------------|----------------|------------------------|
| ContactForm | 576 | 9 (en :root) | 15+ | 4 |
| FAQAccordion | 200 | 0 | 10 | 2 |
| HeroSection | 138 | 0 | 6 | 1 |
| PostsCarousel | 1589 | 7 (locales) | 25+ | 4 |

**PostsCarousel** es el bloque MÁS complejo con:
- 1589 líneas de CSS
- 3 variantes de card (default, vertical, overlay-split)
- 7 efectos hover (zoom, squeeze, lift, glow, tilt, fade, slide)
- 3 variantes de arrows mobile
- 8 variantes de color para botones/badges
- Sistema Material Design de elevations

---

## Observaciones Adicionales

### 1. Google Fonts en PostsCarousel
```css
@import url('https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
```

**Problema:**
- Las fuentes importadas (Saira Condensed, Inter) no se usan en el archivo
- El archivo referencia `'Satoshi'` que no está importado
- Los @import en CSS de bloque afectan performance

**Acción:** Eliminar imports, verificar fuentes necesarias, mover a theme.json.

### 2. Consistencia de Naming
| Bloque | Prefijo CSS | Consistencia |
|--------|-------------|--------------|
| ContactForm | `.hero-form` | ✅ Consistente |
| FAQAccordion | `.faq-accordion` | ✅ Consistente |
| HeroSection | `.hero-section` | ✅ Consistente |
| PostsCarousel | `.posts-carousel`, `.pc-*` | ✅ Consistente (dos prefijos) |

---

## Decisiones Requeridas

### ❓ Decisión 1: Paleta de Colores

**Opción A:** Actualizar theme.json para incluir coral (#E78C85), purple (#311A42), gold (#CEA02D)
- ✅ Mantiene diseño actual de los bloques
- ❌ Agrega complejidad a theme.json
- ❌ Conflicto con la paleta actual (teal/salmon)

**Opción B:** Cambiar todos los bloques para usar Primary/Secondary de theme.json
- ✅ Consistencia total con theme.json
- ✅ Diseño system más cohesivo
- ❌ Cambio visual significativo en el sitio
- ❌ Requiere testing extensivo

**Opción C:** Variables locales en cada bloque para colores personalizados
- ✅ Flexibilidad por bloque
- ✅ No contamina theme.json
- ❌ Menos consistencia entre bloques
- ❌ Más mantenimiento

**Recomendación:** **Opción B con variables locales** - Usar Primary/Secondary de theme.json como base, permitir variantes locales donde sea necesario (ej: gold, transparent en PostsCarousel).

### ❓ Decisión 2: Manejo de ContactForm Variables

Las variables en `:root` de ContactForm deben moverse a scope local:

**Cambio requerido:**
```css
/* ANTES */
:root {
    --rose: #E78C85;
    /* ... */
}

/* DESPUÉS */
.hero-form {
    --hero-form-primary: var(--wp--preset--color--secondary);
    /* ... */
}
```

---

## Prioridades de Refactorización

### 🔴 Prioridad ALTA
1. **Mover variables de ContactForm** de `:root` a `.hero-form` (riesgo de conflictos)
2. **Decidir paleta de colores** (impacta todo el sitio)
3. **Actualizar PostsCarousel** (bloque más usado, mayor impacto visual)

### 🟡 Prioridad MEDIA
4. Reemplazar colores hardcodeados por variables de theme.json
5. Crear sistemas de variables locales (spacing, transitions, shadows)
6. Eliminar Google Fonts imports de PostsCarousel

### 🟢 Prioridad BAJA
7. Convertir font-sizes de `px` a `rem` (ContactForm, PostsCarousel)
8. Usar spacing variables de theme.json donde sea posible
9. Documentar variantes de PostsCarousel

---

## Próximos Pasos Inmediatos

1. ✅ **Reportes individuales creados** para cada bloque
2. ❓ **Reunión de decisión** sobre paleta de colores (Opción A/B/C)
3. ⏳ **Refactorización de ContactForm** (mover variables de :root)
4. ⏳ **Refactorización de PostsCarousel** (mayor impacto)
5. ⏳ **Testing visual** de cambios de color
6. ⏳ **Documentación** de sistema de variables final

---

## Archivos de Auditoría Generados

- ✅ `/home/user/travel-exp/docs/auditoria-css/breadcrumb.md` (ejemplo existente)
- ✅ `/home/user/travel-exp/docs/auditoria-css/contact-form.md`
- ✅ `/home/user/travel-exp/docs/auditoria-css/faq-accordion.md`
- ✅ `/home/user/travel-exp/docs/auditoria-css/hero-section.md`
- ✅ `/home/user/travel-exp/docs/auditoria-css/posts-carousel.md`
- ✅ `/home/user/travel-exp/docs/auditoria-css/resumen-auditoria.md` (este archivo)

---

## Conclusión

La auditoría revela un **conflicto fundamental entre la paleta de colores de theme.json y los bloques ACF**. Los bloques usan consistentemente coral (#E78C85) y purple (#311A42), mientras que theme.json define teal (#17565C) y salmon (#C66E65).

**Antes de continuar con la refactorización técnica, es crítico decidir qué paleta de colores será la estándar del sitio.**

Todos los bloques necesitan refactorización para:
1. Integrar variables de theme.json
2. Eliminar colores hardcodeados
3. Crear sistemas de variables locales
4. Mejorar mantenibilidad y consistencia

**Impacto estimado:** Alto - Los cambios de color serán visibles en todo el sitio. Se requiere testing visual exhaustivo después de la refactorización.
