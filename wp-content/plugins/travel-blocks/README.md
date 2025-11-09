# ACF Gutenberg Blocks

**Version:** 1.0.0
**Author:** Rogger Palomino Gamboa
**Description:** Custom Gutenberg blocks powered by ACF for travel website.

---

## 📋 Overview

Custom Gutenberg blocks for the Machu Picchu Peru website, fully powered by Advanced Custom Fields with modular assets loading.

---

## 🎯 Features

- ✅ **3 Blocks**: Hero Section, Static CTA, FAQ Accordion
- ✅ **ACF Powered**: All block options via ACF fields
- ✅ **Modular Assets**: CSS/JS only loaded when block is used
- ✅ **SEO Optimized**: FAQ Schema, meta tags integration
- ✅ **Responsive**: Mobile-first design
- ✅ **Accessible**: ARIA labels, keyboard navigation

---

## 📦 Blocks

| Block | Name | Type | Features |
|-------|------|------|----------|
| **Hero Section** | `acf/hero-section` | Static | Background image, title, CTA |
| **Static CTA** | `acf/static-cta` | Static | Flexible backgrounds, buttons |
| **FAQ Accordion** | `acf/faq-accordion` | Interactive | Accordion, FAQ Schema markup |

---

## 🚀 Installation

1. Upload to `/wp-content/plugins/acf-gutenberg-blocks/`
2. Activate via WordPress admin
3. **Requires:** Advanced Custom Fields Pro
4. Blocks appear in "Travel Blocks" category

---

## 🔧 Usage

### Adding a Block

1. Edit page/post in Gutenberg
2. Click "+" to add block
3. Search "Travel Blocks" category
4. Configure via sidebar ACF fields
5. Preview in real-time

### Example: FAQ Accordion

**ACF Fields:**
- Section Title
- Section Description
- FAQ Items (Repeater)
  - Question (Text)
  - Answer (WYSIWYG)
  - Open by Default (True/False)

**Output:**
- Interactive accordion with smooth animations
- FAQ JSON-LD schema for SEO
- Keyboard accessible (Enter/Space keys)

---

## 📁 Structure

```
acf-gutenberg-blocks/
├── acf-gutenberg-blocks.php
├── README.md
├── acf-json/           # Block field groups
└── src/
    ├── Core/           # BlockBase, AssetManager
    └── Blocks/
        ├── HeroSection/
        │   ├── HeroSection.php
        │   ├── template.php
        │   └── style.css
        ├── StaticCTA/
        │   └── ...
        └── FAQAccordion/
            ├── FAQAccordion.php
            ├── template.php
            ├── style.css
            └── script.js
```

---

## ➕ Adding New Block

1. Create `src/Blocks/MyBlock/MyBlock.php`
2. Extend `BlockBase`
3. Create `template.php`, `style.css`
4. Register in main plugin file
5. Create ACF field group in admin

See: `/docs/guias/agregar-bloque.md`

---

## 🔗 Dependencies

- WordPress 6.0+
- **Advanced Custom Fields Pro** 6.0+
- PHP 7.4+

**Recommended:**
- Aurora ACF Kit
- Travel Performance (for SEO schema)

---

## 📝 Changelog

### 1.0.0 (2025-10-05)
- 3 blocks: Hero, CTA, FAQ
- FAQ Schema markup
- Responsive design
- Accessibility features

---

**Developer:** Rogger Palomino Gamboa
