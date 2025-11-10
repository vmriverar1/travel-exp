# Análisis de Conflictos de Variables CSS

## Fecha: 2025-11-09
## Auditoría Realizada por: Claude

---

## Resumen Ejecutivo

Se han identificado **TRES sistemas de variables CSS diferentes** en el proyecto:

1. **global.css** (Tema)
2. **common-variables.css** (Plugin travel-blocks)
3. **theme.json** (Tema)

Esto representa un conflicto importante que debe resolverse antes de continuar con la migración.

---

## 1. Sistema: global.css (Tema)

**Ubicación:** `/wp-content/themes/travel-content-kit/assets/css/global.css`

### Paleta de Colores
```css
--color-primary: #d46a3f;        /* Terracota */
--color-secondary: #5a8f7b;      /* Green */
--color-accent: #f4c430;         /* Gold */
```

### Uso
- **316 referencias totales** en archivos CSS
  - **61 ocurrencias** en tema (16 archivos)
  - **255 ocurrencias** en bloques (19 archivos)

### Dependencias
- TODOS los archivos CSS del tema dependen de `travel-global` en functions.php:64-69
- Atoms, molecules, organisms, utilities, package-layout

---

## 2. Sistema: common-variables.css (Plugin)

**Ubicación:** `/wp-content/plugins/travel-blocks/assets/css/common-variables.css`

### Paleta de Colores (DIFERENTE)
```css
--color-coral: #E78C85;
--color-coral-dark: #d97a74;
--color-coral-light: #FFF0EF;

--color-purple: #311A42;
--color-teal: #4A90A4;
```

### Características
- Sistema completo de diseño (brand colors, status colors, neutral colors)
- Tipografía, spacing, borders, shadows, z-index, transitions
- Utility classes (.text-coral, .bg-purple, etc.)
- Soporte para dark mode (no implementado aún)

### ⚠️ CONFLICTO IMPORTANTE
Los bloques del plugin usan variables de `common-variables.css`, NO de `global.css`. Esto significa que hay dos paletas de colores diferentes en uso.

---

## 3. Sistema: theme.json (Tema)

**Ubicación:** `/wp-content/themes/travel-content-kit/theme.json`

### Paleta de Colores (TERCERA PALETA DIFERENTE)
```json
"Primary": "#17565C"
"Secondary": "#C66E65"
```

### Características
- Paleta completa con variaciones de opacidad (80%, 60%, 40%, 20%)
- Sistema de espaciado con clamp() para responsive
- Tipografía con fluid sizing
- Solo una sombra definida: "sombra-sm"
- Layout: contentSize y wideSize de 1200px

---

## Estadísticas de Uso

### Referencias a var(--color-)
- **Tema:** 61 ocurrencias en 16 archivos
- **Bloques:** 255 ocurrencias en 19 archivos
- **Total:** 316 referencias a variables de color

### Referencias a var(--spacing-)
- **Tema:** 80 ocurrencias en 16 archivos
- **Bloques:** 0 ocurrencias (usan spacing del common-variables.css)

### Referencias totales a var(--)
- **Tema:** 231 ocurrencias en 22 archivos

---

## Archivos Críticos con Más Referencias

### Tema
1. `global.css` - 2 referencias (definiciones)
2. `molecules/nav-aside.css` - 67 referencias
3. `molecules/contact-info.css` - 25 referencias
4. `organisms/footer-main.css` - 5 referencias

### Bloques
1. `common-variables.css` - 31 referencias a color
2. `deals-slider.css` - 30 referencias
3. `inclusions-exclusions.css` - 21 referencias
4. `traveler-reviews.css` - 22 referencias

---

## Análisis de Conflictos por Tipo

### 1. Colores
**Problema:** Tres paletas diferentes compitiendo
- global.css: Terracota/Green/Gold
- common-variables.css: Coral/Purple/Teal
- theme.json: #17565C / #C66E65

**Impacto:** Alto - Afecta consistencia visual del sitio

**Decisión requerida:** ¿Cuál es la paleta oficial?

### 2. Tipografía
**Problema:** Redundancia
- global.css: Define tamaños xs, sm, base, lg, xl, 2xl, 3xl
- common-variables.css: Define xs, sm, base, lg, xl, 2xl, 3xl, 4xl, 5xl
- theme.json: Define tiny, small, regular, medium, extra-medium, large, x-large, xx-large, huge

**Impacto:** Medio - Diferentes nomenclaturas

### 3. Espaciado
**Problema:** Nomenclatura diferente
- global.css: xs (0.5rem), sm (1rem), md (1.5rem), lg (2rem)
- common-variables.css: xs (0.25rem), sm (0.5rem), md (1rem), lg (1.5rem)
- theme.json: 20 (0.25rem), 30 (0.5rem), 50 (1rem), 60 (1.5rem clamp)

**Impacto:** Alto - ⚠️ VALORES DIFERENTES para mismos nombres

### 4. Sombras
**Problema:** Redundancia con valores idénticos
- Todos definen sm, md, lg, xl con mismos valores
- theme.json solo tiene "sombra-sm"

**Impacto:** Bajo - Valores consistentes

---

## Recomendaciones

### Opción 1: Migración Completa a theme.json (Original Plan)
**Pros:**
- WordPress nativo
- Editor de bloques integrado
- Futuro-proof

**Contras:**
- No soporta todas las variables (z-index, transitions, layout)
- Requiere mantener common-variables.css del plugin
- Dos sistemas diferentes (tema vs plugin)

### Opción 2: Unificar en common-variables.css
**Pros:**
- Sistema único para todo el proyecto
- Soporta todas las variables necesarias
- Ya tiene estructura completa

**Contras:**
- Plugin mantiene el control
- theme.json queda subutilizado
- No aprovecha features de WordPress

### Opción 3: Sistema Híbrido (RECOMENDADO)
**Pros:**
- theme.json para colores, tipografía, espaciado (WordPress features)
- custom-properties.css para variables no soportadas (z-index, transitions, layout)
- common-variables.css se mantiene para bloques del plugin

**Contras:**
- Más complejo de mantener
- Requiere documentación clara

---

## Próximos Pasos Recomendados

1. **DECISIÓN CRÍTICA:** Definir paleta de colores oficial con stakeholder/diseñador
2. **MAPEO:** Crear tabla de equivalencias entre los tres sistemas
3. **ESTRATEGIA:** Decidir si migrar plugin también o solo tema
4. **DOCUMENTACIÓN:** Crear guía de uso para desarrolladores
5. **MIGRACIÓN:** Ejecutar plan fase por fase

---

## Notas Adicionales

- El plugin travel-blocks parece ser independiente y tener su propio sistema de diseño
- La migración debería considerar si el plugin es de terceros o desarrollo interno
- Si es desarrollo interno, podría migrarse también a usar theme.json
- Se requiere reunión con equipo de diseño para resolver conflicto de colores
