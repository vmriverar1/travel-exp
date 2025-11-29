# Aurora ACF Kit

**Version:** 1.0.0
**Author:** Rogger Palomino Gamboa
**Description:** ACF Field Groups programmatically registered with JSON sync.

---

## 📋 Overview

Aurora ACF Kit registers all Advanced Custom Fields field groups for the travel website. Supports JSON synchronization for version control.

---

## 🎯 Features

- ✅ **5 Field Groups**: Global Options, Home Page, Tour Single, About Page, Home Hero
- ✅ **JSON Sync**: `/acf-json/` for Git versioning
- ✅ **OOP Architecture**: Extensible base classes
- ✅ **Dynamic Location Rules**: By page template, CPT, or page ID
- ✅ **PSR-4 Autoloading**

---

## 📦 Field Groups

| Field Group | Key | Location | Fields |
|-------------|-----|----------|--------|
| **Global Options** | `group_global_options` | Options Page | Footer, Social, Contact |
| **Home Page** | `group_home_page` | Page Template | Hero Section |
| **Tour Single** | `group_tour_single` | CPT Tour | Price, Duration, Gallery |
| **About Page** | `group_about_page` | Page | Team, Timeline |
| **Home Hero** | `group_home_hero` | Page | Hero Configuration |

---

## 🚀 Installation

1. Upload to `/wp-content/plugins/aurora-acf-kit/`
2. Activate via WordPress admin
3. **Requires:** Advanced Custom Fields Pro

---

## 🔧 Usage

### Get Field Value

```php
$price = get_field('price'); // From Tour Single
$footer_text = get_field('footer_text', 'option'); // From Global Options
```

### Conditional Display

```php
if (get_field('show_hero')) {
    echo '<div class="hero">';
    echo '<h1>' . get_field('hero_title') . '</h1>';
    echo '</div>';
}
```

---

## 📁 Structure

```
aurora-acf-kit/
├── aurora-acf-kit.php
├── README.md
├── acf-json/              # JSON sync (Git tracked)
│   └── group_*.json
└── src/
    ├── Core/              # Base classes
    ├── Integration/       # JSON sync
    └── FieldGroups/       # Field group definitions
```

---

## ➕ Adding New Field Group

1. Create `src/FieldGroups/MyGroup.php`
2. Extend `FieldGroupBase`
3. Define fields in `register()` method
4. Add to `$services` array
5. Export JSON via ACF admin

See: `/docs/guias/agregar-acf.md`

---

## 🔗 Dependencies

- WordPress 6.0+
- **Advanced Custom Fields Pro** 6.0+
- PHP 7.4+

---

## 📝 Changelog

### 1.0.0 (2025-10-05)
- 5 field groups
- JSON sync enabled
- PSR-4 autoloading

---

**Developer:** Rogger Palomino Gamboa
