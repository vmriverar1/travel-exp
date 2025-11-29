# Posts Carousel - Native CSS Implementation

**Bloque de carousel nativo sin dependencias externas**. Usa CSS `scroll-snap` y JavaScript vanilla puro.

---

## 🎯 Características

- ✅ **Sin dependencias**: Cero librerías externas (no Swiper, no jQuery)
- ✅ **CSS Scroll-Snap**: Navegación nativa del navegador
- ✅ **JavaScript Vanilla**: ~200 líneas de JS puro
- ✅ **SSR Optimizado**: Contenido renderizado desde el servidor (SEO)
- ✅ **Skeleton Loader**: Shimmer effect mientras carga
- ✅ **IntersectionObserver**: Actualización automática de estados
- ✅ **Autoplay**: Con pausa en hover/focus
- ✅ **Keyboard Navigation**: Flechas izquierda/derecha
- ✅ **Accessible**: ARIA labels y focus management
- ✅ **Responsive**: Desktop grid + Mobile carousel

---

## 📦 Archivos

```
PostsCarousel/
├── PostsCarousel.php           # Clase principal + ACF fields
├── templates/
│   └── editorial-carousel.php  # Template SSR
└── assets/
    ├── style.css               # CSS con scroll-snap
    └── carousel.js             # JavaScript vanilla
```

---

## 🎨 ACF Fields (Configuración)

| Field | Type | Default | Description |
|-------|------|---------|-------------|
| **Posts to Display** | Number | 6 | Cantidad de posts (1-20) |
| **Show Navigation Arrows** | True/False | ✅ | Flechas prev/next |
| **Show Pagination Dots** | True/False | ✅ | Dots de navegación |
| **Enable Autoplay** | True/False | ❌ | Avance automático |
| **Autoplay Delay** | Number | 5000 | Delay en milisegundos (1000-30000) |

---

## 🔧 Tecnologías Usadas

### 1. CSS Scroll-Snap

```css
.pc-slides {
  display: flex;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scroll-behavior: smooth;
}

.pc-slide {
  scroll-snap-align: center;
  scroll-snap-stop: always;
}
```

**Beneficios:**
- Navegación suave nativa del navegador
- Hardware-accelerated
- Funciona en iOS/Android sin polyfills

---

### 2. IntersectionObserver API

```javascript
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      // Actualizar currentIndex automáticamente
      this.currentIndex = index;
      this.updateActiveStates();
    }
  });
}, { threshold: 0.5 });
```

**Beneficios:**
- Detecta qué slide está visible sin scroll events
- Performance optimizado
- Actualiza dots automáticamente

---

### 3. Skeleton Loader

```css
.pc-skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
  background-size: 400% 100%;
  animation: pc-shimmer 1.6s infinite linear;
}
```

**Efecto shimmer** que previene CLS (Cumulative Layout Shift).

---

## 🚀 Cómo Funciona

### Flujo de Renderizado

```
1. PHP (SSR) → Renderiza posts en HTML
                ↓
2. Skeleton visible → Usuario ve placeholder animado
                ↓
3. JavaScript inicia → Oculta loader, muestra carousel
                ↓
4. Fade-in suave → Transición opacity 0 → 1
                ↓
5. IntersectionObserver → Monitorea slides visibles
                ↓
6. Navegación activa → Arrows, dots, keyboard, autoplay
```

---

## 📊 Comparación: Swiper vs Nativo

| Feature | Swiper | PostsCarousel (Nativo) |
|---------|--------|------------------------|
| **Tamaño JS** | ~50KB min | ~6KB |
| **CSS** | ~15KB | ~8KB |
| **Dependencias** | Swiper library | Cero |
| **Scroll-snap** | No nativo | ✅ Nativo |
| **Performance** | Bueno | Excelente |
| **Complejidad** | Media | Baja |
| **Mantenibilidad** | Depende de updates | 100% control |

---

## 🎯 Uso en Editor

1. Agregar bloque "Posts Carousel (Native CSS)"
2. Configurar en sidebar:
   - Número de posts
   - Mostrar/ocultar arrows
   - Mostrar/ocultar dots
   - Activar autoplay (opcional)
   - Delay de autoplay (si está activo)
3. Publicar

---

## 🎨 Personalización

### Cambiar Colores

**Category Badge:**
```css
.pc-slide__category {
  background: #FF6B6B; /* ← Cambiar aquí */
}
```

**Read More Button:**
```css
.pc-slide__readmore {
  color: #FFE66D; /* ← Amarillo */
  border-color: #FFE66D;
}

.pc-slide__readmore:hover {
  background: #FFE66D;
  color: #111;
}
```

**Navigation Arrows:**
```css
.pc-nav {
  background: rgba(255, 255, 255, 0.95);
  color: #111;
}
```

**Active Dot:**
```css
.pc-dot.is-active {
  background: #FF6B6B; /* ← Color del dot activo */
}
```

---

### Cambiar Velocidades

**Autoplay:**
```javascript
// En ACF field o directamente en JS
this.autoplayDelay = 3000; // 3 segundos
```

**Scroll Smoothness:**
```css
.pc-slides {
  scroll-behavior: smooth; /* auto, smooth */
}
```

---

## ♿ Accesibilidad

### ARIA Labels
- Arrows tienen `aria-label="Previous/Next slide"`
- Dots tienen `aria-label="Go to slide X"`
- Active dot tiene `aria-current="true"`

### Navegación con Teclado
- `Arrow Left` → Slide anterior
- `Arrow Right` → Slide siguiente

### Reduced Motion
```css
@media (prefers-reduced-motion: reduce) {
  .pc-slides {
    scroll-behavior: auto; /* Sin animaciones */
  }
}
```

---

## 🐛 Debugging

### Ver currentIndex en consola

```javascript
// En carousel.js, agregar:
console.log('Current index:', this.currentIndex);
```

### Verificar IntersectionObserver

```javascript
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    console.log('Slide visible:', entry.target, entry.isIntersecting);
  });
});
```

---

## 📱 Responsive Behavior

### Desktop (> 1024px)
- Slides visibles: 1 por viewport
- Navigation: Arrows fuera del carousel
- Hover effects activos

### Tablet (768px - 1024px)
- Slides visibles: 1 por viewport
- Navigation: Arrows dentro del carousel
- Hover effects activos

### Mobile (< 768px)
- Slides visibles: 1 por viewport (100% width)
- Navigation: Arrows pequeños
- Swipe nativo del navegador

---

## 🚀 Performance Tips

### Lazy Load Images
```php
// En template, agregar loading="lazy"
<img src="<?php echo $thumbnail; ?>" loading="lazy">
```

### Reducir Posts
```php
// Limitar a 6-8 posts para mejor performance
$posts_per_page = 6;
```

### Preload First Image
```php
// Primera imagen con priority alta
<?php if ($index === 0): ?>
  <link rel="preload" as="image" href="<?php echo $thumbnail; ?>">
<?php endif; ?>
```

---

## ✅ Browser Support

| Browser | Version | Soporte |
|---------|---------|---------|
| Chrome | 69+ | ✅ Full |
| Firefox | 68+ | ✅ Full |
| Safari | 14.1+ | ✅ Full |
| Edge | 79+ | ✅ Full |
| iOS Safari | 14.5+ | ✅ Full |
| Chrome Android | 69+ | ✅ Full |

**CSS Scroll-Snap:** [Can I Use](https://caniuse.com/css-snappoints) - 96%+ global support

---

## 📝 TODOs Futuros

- [ ] Soporte para Custom Post Types (no solo 'post')
- [ ] Filtro por categoría/tag desde ACF
- [ ] Modo vertical (scroll-y)
- [ ] Infinite loop (primera → última sin break)
- [ ] Lazy load images con placeholder blur
- [ ] Swipe touch gestures mejorados
- [ ] Soporte para videos en slides

---

**Creado:** 2025-10-08
**Versión:** 1.0.0
**Plugin:** ACF + Gutenberg + REST Blocks v5
**Sin dependencias externas** ✨
