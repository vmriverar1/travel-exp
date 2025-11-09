# Aurora Content Kit

**Version:** 1.0.0
**Author:** Rogger Palomino Gamboa
**Description:** Custom Post Types and Taxonomies for travel website.

---

## 📋 Overview

Aurora Content Kit registers all Custom Post Types (CPTs) and Taxonomies for the Machu Picchu Peru travel website. Built with PSR-4 autoloading and OOP principles.

---

## 🎯 Features

- ✅ **5 Custom Post Types**: Tour, Destination, Deal, Review, Guide
- ✅ **5 Custom Taxonomies**: Tour Category, Difficulty, Duration, Region, Tour Type
- ✅ **REST API Ready**: All CPTs exposed to `/wp-json/wp/v2/`
- ✅ **Gutenberg Compatible**
- ✅ **Automatic Rewrite Flush** on activation/deactivation
- ✅ **PSR-4 Autoloading**

---

## 📦 Custom Post Types

| CPT | Slug | Archive | REST API |
|-----|------|---------|----------|
| **Tour** | `tour` | `/tours/` | ✅ |
| **Destination** | `destination` | `/destinations/` | ✅ |
| **Deal** | `deal` | `/deals/` | ✅ |
| **Review** | `review` | No | ✅ |
| **Guide** | `guide` | No | ✅ |

---

## 🏷️ Custom Taxonomies

| Taxonomy | Type | Post Types | REST API |
|----------|------|------------|----------|
| **Tour Category** | Hierarchical | tour | ✅ |
| **Difficulty** | Tags | tour | ✅ |
| **Duration** | Tags | tour | ✅ |
| **Region** | Hierarchical | tour, destination | ✅ |
| **Tour Type** | Tags | tour | ✅ |

---

## 🚀 Installation

1. Upload to `/wp-content/plugins/aurora-content-kit/`
2. Activate via WordPress admin
3. Rewrite rules flush automatically

---

## 🔧 Usage

### Querying Tours

```php
$tours = new WP_Query([
    'post_type' => 'tour',
    'posts_per_page' => 12,
    'tax_query' => [[
        'taxonomy' => 'difficulty',
        'field' => 'slug',
        'terms' => 'easy',
    ]],
]);
```

### REST API

```
GET /wp-json/wp/v2/tour
GET /wp-json/wp/v2/destination
GET /wp-json/wp/v2/tour_category
```

---

## 📁 Structure

```
aurora-content-kit/
├── aurora-content-kit.php
├── README.md
└── src/
    ├── Core/              # Base classes
    ├── PostTypes/         # CPT definitions
    └── Taxonomies/        # Taxonomy definitions
```

---

## ➕ Adding New CPT

1. Create `src/PostTypes/MyCPT.php`
2. Extend `CustomPostTypeBase`
3. Add to `$services` array in main plugin file
4. Deactivate/reactivate plugin

See: `/docs/guias/agregar-cpt.md`

---

## 🔗 Dependencies

- WordPress 6.0+
- PHP 7.4+

**Recommended:**
- Aurora ACF Kit (custom fields)
- ACF Gutenberg Blocks

---

## 📝 Changelog

### 1.0.0 (2025-10-05)
- 5 CPTs + 5 Taxonomies
- REST API support
- PSR-4 autoloading

---

**Developer:** Rogger Palomino Gamboa
