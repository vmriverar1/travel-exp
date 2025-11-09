# Travel Child Theme

Child theme for client customizations. Inherits all functionality from the parent theme **Travel Content Kit**.

## Purpose

This is an empty child theme ready for client-specific customizations. All core functionality, components, and features are provided by the parent theme.

## What You Can Customize Here

### 1. Styles (style.css)
Add custom CSS to override parent theme styles:
```css
/* Override colors */
:root {
  --wp--preset--color--primary: #your-color;
}

/* Custom styles */
.custom-class {
  property: value;
}
```

### 2. Functions (functions.php)
Add custom PHP functions, hooks, and filters:
```php
// Custom function
function my_custom_function() {
    // Your code
}

// Hook into WordPress
add_action('wp_footer', 'my_footer_content');
```

### 3. Template Overrides
Copy any template file from the parent theme to override it:
```
parent: travel-content-kit/header.php
child:  travel-child-theme/header.php (your custom version)
```

### 4. Theme Configuration (theme.json)
Override parent theme.json settings:
```json
{
  "$schema": "https://schemas.wp.org/wp/6.7/theme.json",
  "version": 3,
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "primary",
          "color": "#your-color",
          "name": "Primary"
        }
      ]
    }
  }
}
```

### 5. ACF Options
Add client-specific ACF field groups in:
```
travel-child-theme/inc/acf-client-options.php
```

### 6. Custom Assets
```
travel-child-theme/
├── assets/
│   ├── css/          # Additional stylesheets
│   ├── js/           # Custom JavaScript
│   └── images/       # Client images/logos
```

## File Structure

```
travel-child-theme/
├── style.css         # Theme metadata + custom styles
├── functions.php     # Custom functions
├── README.md         # This file
└── screenshot.png    # Theme thumbnail (optional)
```

## Parent Theme Features

The parent theme (Travel Content Kit) provides:

- ✅ Atomic Design structure (Atoms, Molecules, Organisms)
- ✅ ACF integration (header/footer options)
- ✅ 11 navigation menu locations
- ✅ Performance monitoring
- ✅ WebP image conversion
- ✅ Block performance tracking
- ✅ Responsive header/footer
- ✅ Mobile navigation
- ✅ Social media integrations

## How to Use

1. **Activate this child theme** in WordPress admin
2. **All parent functionality works automatically**
3. **Add customizations as needed** in this child theme
4. **Parent theme updates** won't affect your customizations

## Important Notes

- ⚠️ **Don't modify parent theme files** - Always customize in this child theme
- ✅ **All parent features are inherited** - No need to recreate them
- ✅ **Safe updates** - Parent theme can be updated without losing customizations
- ✅ **Override selectively** - Only copy/modify what you need to change

## Documentation

Parent theme documentation: `/themes/travel-content-kit/README.md`

## Support

For questions or issues with:
- **Parent theme features**: Contact parent theme developers
- **Child theme customizations**: Contact your development team

---

**Version**: 1.0.0
**Parent Theme**: Travel Content Kit
**WordPress**: 6.4+
**PHP**: 7.4+
