# Mapeo de Colores - Migración a theme.json

**Fecha:** 2025-11-09
**Decisión:** Usar colores existentes en theme.json

---

## 🎨 Mapeo Oficial de Colores

### Colores Legacy → theme.json

| Color Legacy | Hex | Bloques Afectados | → theme.json | Variable CSS |
|--------------|-----|-------------------|--------------|--------------|
| **Coral** | `#E78C85` | 28 bloques | **Secondary** `#C66E65` | `var(--wp--preset--color--secondary)` |
| **Purple** | `#311A42` | 15 bloques | **Contrast 4** `#311A42` | `var(--wp--preset--color--contrast-4)` |
| **Gold** | `#CEA02D` | 5 bloques | **Contrast 1** `#CEA02D` | `var(--wp--preset--color--contrast-1)` |

### Notas de Mapeo

**Coral → Secondary:**
- Legacy: `#E78C85` (tono más claro, más rosado)
- theme.json: `#C66E65` (tono más oscuro, más terracota)
- **Diferencia visual:** Ligera, cambio de tono pero similar familia
- **Impacto:** BAJO - Los usuarios probablemente no notarán el cambio

**Purple → Contrast 4:**
- Legacy: `#311A42`
- theme.json: `#311A42` (¡EXACTO!)
- **Diferencia visual:** NINGUNA
- **Impacto:** NULO

**Gold → Contrast 1:**
- Legacy: `#CEA02D`
- theme.json: `#CEA02D` (¡EXACTO!)
- **Diferencia visual:** NINGUNA
- **Impacto:** NULO

---

## 📝 Estrategia de Refactorización

### Reemplazos Directos

```css
/* ANTES */
color: #E78C85;

/* DESPUÉS */
color: var(--wp--preset--color--secondary);
```

```css
/* ANTES */
color: #311A42;

/* DESPUÉS */
color: var(--wp--preset--color--contrast-4);
```

```css
/* ANTES */
color: #CEA02D;

/* DESPUÉS */
color: var(--wp--preset--color--contrast-1);
```

### Variables Locales (cuando se necesite variante)

Si un bloque necesita una variante más clara/oscura:

```css
.bloque {
  /* Variable local derivada de theme.json */
  --bloque-color-hover: color-mix(in srgb, var(--wp--preset--color--secondary) 80%, white);

  color: var(--wp--preset--color--secondary);
}

.bloque:hover {
  color: var(--bloque-color-hover);
}
```

---

## 🎯 Priorización de Bloques

### CRÍTICO (refactorizar PRIMERO)

1. **DatesAndPrices** - Variables en `:root` (26 variables)
2. **ContactForm** - Variables en `:root` (9 variables)
3. **Eliminar Google Fonts** - 3 bloques

### ALTO (muchos usos de colores legacy)

4. **PostsCarousel** (1,589 líneas)
5. **RelatedPackages** (1,158 líneas)
6. **DealsSlider** (806 líneas)

### MEDIO (refactorización estándar)

7-30. Bloques de 200-400 líneas

### BAJO (bloques simples)

31-41. Bloques < 150 líneas

---

## ✅ Decisiones Tomadas

- [x] **Coral → Secondary** (#C66E65)
- [x] **Purple → Contrast 4** (#311A42 - exacto)
- [x] **Gold → Contrast 1** (#CEA02D - exacto)
- [x] **Sistema de grises:** Usar Gray (#666666) y Contrast (#111111) de theme.json, crear variantes locales si se necesita
- [x] **Deal blocks:** Unificar con Primary/Secondary según contexto
- [x] **PackagesByLocation:** Crear CSS dedicado

---

## 📋 Próximos Pasos

1. ✅ Mapeo de colores definido
2. → Refactorizar DatesAndPrices (mover variables de :root)
3. → Refactorizar ContactForm (mover variables de :root)
4. → Eliminar Google Fonts
5. → Refactorizar bloques por orden de prioridad
6. → Eliminar global.css y common-variables.css
7. → Testing completo
