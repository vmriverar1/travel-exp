# Travel Content Kit

A flexible WordPress theme designed for travel and tourism websites, built with Atomic Design principles and modern development practices.

## Features

- **Atomic Design Architecture**: Organized components (Atoms, Molecules, Organisms)
- **ACF Integration**: Advanced Custom Fields for flexible content management
- **Performance Optimized**: Efficient asset loading and caching strategies
- **Responsive Navigation**: Custom header and footer navigation systems
- **Block Performance Monitoring**: Built-in block rendering performance tracking
- **WebP Image Support**: Automatic WebP conversion for optimized images
- **Flexible Layouts**: Multiple page templates and customization options

## Theme Structure

```
travel-content-kit/
├── assets/
│   ├── css/
│   │   ├── atoms/          # Individual UI elements
│   │   ├── molecules/      # Component groups
│   │   ├── organisms/      # Complex components
│   │   ├── global.css      # CSS variables and base styles
│   │   └── utilities.css   # Utility classes
│   ├── js/
│   │   ├── molecules/      # Component scripts
│   │   └── organisms/      # Complex component scripts
│   └── fonts/
│       └── satoshi/        # Satoshi variable font
├── inc/
│   ├── acf-options.php           # Header ACF options
│   └── acf-footer-options.php    # Footer ACF options
├── parts/
│   ├── atoms/              # Reusable UI elements
│   ├── molecules/          # Component partials
│   └── organisms/          # Complex component partials
├── templates/              # Custom page templates
├── functions.php           # Theme setup and functionality
├── style.css              # Theme metadata
└── theme.json             # WordPress theme configuration
```

## Requirements

- WordPress 6.4 or higher
- PHP 7.4 or higher
- ACF (Advanced Custom Fields) plugin
- Recommended: Aurora ACF Kit plugin for additional ACF field groups

## Installation

1. Download or clone this theme into `/wp-content/themes/`
2. Install and activate required plugins (ACF, Aurora ACF Kit)
3. Go to **Appearance → Themes** and activate **Travel Content Kit**
4. Configure theme settings in **Settings → ACF Options**

## Navigation Menus

The theme registers the following navigation menus:

### Header
- Primary Menu (Desktop)
- Secondary Menu (Main Pages)
- Aside Menu (Mobile)
- Aside Secondary Links

### Footer
- Footer - Top Experiences
- Footer - Treks & Adventure
- Footer - Culture & History
- Footer - Destinations
- Footer - About Machu Picchu Peru
- Footer - Extra Information
- Footer - Legal Links

## ACF Options

Configure global theme settings through ACF Options pages:

- **Header Options**: Logo, contact information, social media links
- **Footer Options**: Company information, payment methods, review platform logos

## Performance Features

### Block Performance Monitoring
The theme includes built-in performance monitoring that logs slow-rendering blocks:
- Tracks render time for each Gutenberg block
- Logs blocks taking > 5ms to render
- Provides aggregate statistics on shutdown

### Asset Loading
- Selective CSS/JS loading based on page requirements
- Atomic Design methodology for modular stylesheets
- Deferred JavaScript execution
- Cache busting for development

### Image Optimization
- Automatic WebP conversion via `convert_to_webp_if_possible()` function
- Lazy loading for images
- Responsive image support

## Development

### Cache Control
The theme disables caching for logged-in users by default. For production:
1. Remove or modify the cache control headers in `functions.php`
2. Implement a proper caching strategy (Redis, Varnish, etc.)

### Customization
- All styles are enqueued through `functions.php`
- CSS organized by Atomic Design principles
- JavaScript components are modular and reusable
- Templates follow WordPress template hierarchy

## Atomic Design Components

### Atoms (Basic Elements)
- Buttons (close, hamburger)
- Logos (header, footer)
- Navigation links
- Payment icons
- Social media icons

### Molecules (Component Groups)
- Contact information blocks
- Footer company info
- Navigation groups (main, aside, footer)
- Payment methods display
- Social media bars

### Organisms (Complex Components)
- Header with full navigation
- Footer with multiple sections
- Complete navigation systems

## Support

For issues, questions, or contributions:
- GitHub: [your-org/travel-content-kit]
- Documentation: [your-docs-url]

## License

GNU General Public License v2 or later
http://www.gnu.org/licenses/gpl-2.0.html

## Credits

- **Design System**: Based on Atomic Design by Brad Frost
- **Typography**: Satoshi Variable Font
- **Based on**: Twenty Twenty-Four WordPress theme

---

Built with ❤️ by Travel Development Team
