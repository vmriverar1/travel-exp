# Travel Blocks - Configuration Classes

Centralized configuration classes for shared settings across blocks.

## ButtonStyles

**File:** `ButtonStyles.php`
**Namespace:** `Travel\Blocks\Config`
**Since:** v2.1.0

Provides centralized configuration for button and badge color variants, eliminating duplication across multiple blocks.

### Features

- Button color variant fields
- Badge color variant fields
- Text alignment fields
- Button alignment fields
- Consistent color schemes across all blocks

### Usage

```php
use Travel\Blocks\Config\ButtonStyles;

// In your block's register_fields() method:

// Get a button color field
$button_field = ButtonStyles::get_button_field(
    'field_my_button_color',     // key
    'button_color_variant',       // name
    'primary',                    // default
    true                          // include "Read More" variant
);

// Get a badge color field
$badge_field = ButtonStyles::get_badge_field(
    'field_my_badge_color',       // key
    'badge_color_variant',        // name
    'secondary'                   // default
);

// Get text alignment field
$text_align_field = ButtonStyles::get_text_alignment_field(
    'field_my_text_align',        // key
    'text_alignment',             // name
    'left'                        // default
);

// Get button alignment field
$button_align_field = ButtonStyles::get_button_alignment_field(
    'field_my_button_align',      // key
    'button_alignment',           // name
    'left'                        // default
);
```

### Available Color Variants

#### Buttons
- `primary` - Pink (#E78C85)
- `secondary` - Purple (#311A42)
- `white` - White with black text
- `gold` - Gold (#CEA02D)
- `dark` - Dark (#1A1A1A)
- `transparent` - Transparent with white border
- `read-more` - Text "Read More" (no background) [optional]

#### Badges
- `primary` - Pink (#E78C85)
- `secondary` - Purple (#311A42)
- `white` - White with black text
- `gold` - Gold (#CEA02D)
- `dark` - Dark (#1A1A1A)
- `transparent` - Transparent with white border

### Blocks Using ButtonStyles

The following blocks use ButtonStyles configuration:

1. **HeroCarousel** (via CarouselBlockBase)
2. **FlexibleGridCarousel** (via CarouselBlockBase)
3. **TeamCarousel** (potential future use)
4. **TaxonomyTabs** (potential future use)
5. **PostsCarousel** (potential future use)
6. **SideBySideCards** (potential future use)

### Benefits

✅ **Single Source of Truth** - Update color variants in one place
✅ **Consistency** - All blocks use identical color options
✅ **Maintainability** - Easier to add/remove color variants
✅ **DRY Principle** - Don't Repeat Yourself
✅ **Type Safety** - Centralized validation

### Example Integration

```php
// Before (duplicated in every block):
[
    'key' => 'field_button_color',
    'label' => 'Button Color',
    'name' => 'button_color_variant',
    'type' => 'select',
    'choices' => [
        'primary' => 'Primary - Pink',
        'secondary' => 'Secondary - Purple',
        // ... more choices
    ],
    'default_value' => 'primary',
]

// After (using ButtonStyles):
ButtonStyles::get_button_field('field_button_color')
```

### Adding New Color Variants

To add a new color variant:

1. Edit `ButtonStyles.php`
2. Add the new color to `get_button_choices()` or `get_badge_choices()`
3. Update CSS files to handle the new variant class
4. All blocks using ButtonStyles will automatically have the new option

### Version History

- **v2.1.0** - Initial release with FASE 4 consolidation
- Replaced ~200+ lines of duplicated ACF field definitions
- Integrated with CarouselBlockBase for automatic propagation
