# Auditoría: PostsListAdvanced

**Ruta:** `wp-content/plugins/travel-blocks/assets/blocks/PostsListAdvanced/style.css`
**Categoría:** Bloque ACF
**Fecha:** 2025-11-09

---

## Variables CSS Usadas

**NO usa variables CSS** - Usa colores hardcodeados directamente.

### Colores hardcodeados encontrados:

| Color Hardcodeado | Dónde se usa | ¿Existe en theme.json? | Variable theme.json equivalente |
|-------------------|--------------|------------------------|--------------------------------|
| `#fff` | `.pla-card`, `.pla-title a`, múltiples | ⚠️ Usar semantic | Usar `var(--wp--preset--color--base)` |
| `#ffb400` | `.pla-category`, `.pla-readmore` | ❌ No existe | Crear variable o usar `--wp--preset--color--gold` si existe |
| `#111` | `.pla-category` | ⚠️ Similar | Usar `var(--wp--preset--color--contrast)` (#111111) |
| `#eee` | `.pla-excerpt` | ❌ No existe | Crear variable local o usar gray con opacity |
| `#ffe082` | `.pla-readmore:hover` | ❌ No existe | Variante de gold, crear variable |
| `#E78C85` | `.swiper-button-prev`, `.swiper-button-next` (mobile) | ❌ No existe | **PROBLEMA:** Color coral no está en theme.json |
| `#d7706b` | `.swiper-button-prev:hover` | ❌ No existe | **PROBLEMA:** Color coral dark no está en theme.json |
| `#ccc` | `.swiper-pagination-bullet` | ❌ No existe | Usar gray con opacity |
| `#f3f3f3`, `#e6e6e6` | `.pla-skeleton` shimmer | ❌ No existe | Usar grays de theme.json |

### Otros valores hardcodeados:

| Tipo | Valor | Uso |
|------|-------|-----|
| Font-size | `20px`, `14px`, `13px` | Title, excerpt, category |
| Spacing | `32px`, `24px`, `20px`, `16px`, `8px`, `4px` | Gap, padding, margin |
| Border-radius | `16px`, `8px` | Cards, category badge |
| Transition | `0.5s ease`, `0.3s ease`, `0.6s ease-out` | Hover effects, animations |
| Box-shadow | `0 2px 8px rgba(0,0,0,0.08)`, `8px 8px 24px rgba(0,0,0,0.25)` | Cards elevation |
| Min-height | `420px`, `380px`, `320px`, `180px` | Cards height |

---

## Análisis

### ⚠️ Problema Principal

El bloque usa **color coral (#E78C85)** en los botones de navegación mobile (Swiper), pero este color **NO existe en theme.json**.

**theme.json tiene:**
- Primary: #17565C (teal)
- Secondary: #C66E65 (salmon/terracota)

**PostsListAdvanced usa:**
- Coral: #E78C85 (en navegación mobile)
- Gold: #ffb400 (en badges y readmore)

### Otros Problemas

1. **Múltiples archivos CSS:** El bloque tiene varios archivos CSS (`style.css`, `base.css`, `card.css`, `minimal.css`) que pueden causar conflictos
2. **Colores específicos no documentados:** #ffb400 (gold) no está en la paleta oficial
3. **No usa spacing scale:** Valores como 32px, 24px, 20px deberían usar `--wp--preset--spacing--*`
4. **No usa font-size scale:** 20px, 14px, 13px deberían usar `--wp--preset--font-size--*`

### Decisión Requerida

1. **Opción A:** Actualizar theme.json para incluir coral (#E78C85) y gold (#ffb400)
2. **Opción B:** Cambiar navegación mobile para usar Secondary (#C66E65) de theme.json
3. **Opción C:** Crear variables locales dentro del bloque para coral/gold

**Recomendación:** Opción B - Alinear con theme.json

---

## Plan de Refactorización

### Cambios a realizar:

```css
/* ANTES */
.swiper-button-prev,
.swiper-button-next {
  background: #E78C85;
}

.swiper-button-prev:hover,
.swiper-button-next:hover {
  background: #d7706b;
}

/* DESPUÉS */
.swiper-button-prev,
.swiper-button-next {
  background: var(--wp--preset--color--secondary); /* #C66E65 */
}

.swiper-button-prev:hover,
.swiper-button-next:hover {
  background: color-mix(in srgb, var(--wp--preset--color--secondary) 85%, black);
}
```

### Variables locales necesarias:

```css
.pla-grid {
  /* Spacing local */
  --pla-gap-lg: var(--wp--preset--spacing--60, 2rem);
  --pla-gap-md: var(--wp--preset--spacing--50, 1.5rem);
  --pla-gap-sm: var(--wp--preset--spacing--40, 1rem);

  /* Transitions */
  --pla-transition-fast: 0.3s ease;
  --pla-transition-slow: 0.5s ease;

  /* Colors locales si no se agregan a theme.json */
  --pla-gold: #ffb400;
  --pla-gold-light: #ffe082;
}
```

---

## CSS Personalizado Necesario: **SÍ**

**Razón:** Necesita variables locales para spacing, transitions y posiblemente colores (gold) que theme.json no soporta o no tiene.

---

## Selectores Específicos: ✅ OK

Todos los selectores usan el prefijo `.pla-*`, no hay conflictos globales.

---

## Próximos Pasos

1. ❓ **Decidir paleta de colores** (coral vs theme.json)
2. Consolidar archivos CSS en uno solo si es posible
3. Reemplazar colores hardcodeados por variables de theme.json
4. Implementar spacing y font-size scales de theme.json
5. Crear variables locales para transitions y valores específicos
6. Testing en editor y frontend
7. Commit: `refactor(posts-list-advanced): use theme.json variables`
