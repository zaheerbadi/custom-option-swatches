# Vendor CustomOptionSwatches Module

## Overview
This Magento 2 module converts simple product custom option dropdown values into clickable color swatches on the product page. The original select element is preserved for submission and validation while displaying an attractive swatch UI below it.

## Features
- **Color Swatches**: Converts dropdown options into visual color swatches
- **Flexible Configuration**: Built-in color map for common color names
- **Hex Color Support**: Supports custom hex color codes (#RRGGBB)
- **Multiple Options Support**: Handles multiple custom dropdown options on the same page
- **Responsive Design**: Mobile-friendly swatch layout
- **Form Validation**: Original select element retained for proper form submission
- **Best Practices**: Follows Magento 2 architectural patterns

## Installation

### 1. Copy Module Files
Copy the `Vendor/CustomOptionSwatches` directory to:
```
app/code/Vendor/CustomOptionSwatches/
```

### 2. Enable Module
```bash
php bin/magento setup:upgrade
php bin/magento module:enable Vendor_CustomOptionSwatches
```

### 3. Compile and Deploy Static Files
```bash
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

### 4. Clear Cache
```bash
php bin/magento cache:clean
```

## Configuration

### Color Map
The default color map is defined in `Model/SwatchConfig.php`:

```php
const DEFAULT_COLOR_MAP = [
    'red' => '#FF0000',
    'blue' => '#0000FF',
    'green' => '#00FF00',
    'black' => '#000000',
    'white' => '#FFFFFF',
    'yellow' => '#FFFF00',
    'orange' => '#FFA500',
    'purple' => '#800080',
    'pink' => '#FFC0CB',
    'brown' => '#A52A2A',
    'gray' => '#808080',
    'navy' => '#000080',
];
```

To extend the color map, extend the `SwatchConfig` class and override the `getColorMap()` method.

### Custom Colors
You can use hex color codes directly as option values:
- Example: `#3498DB` will render as a swatch with that color

## Usage

### For Product Custom Options
1. Go to Catalog > Products
2. Edit a simple product
3. Add or edit custom options of type "Dropdown"
4. Use color names from the map or hex codes as option values
5. Save the product
6. View the product on the frontend - swatches will automatically render

### Color Value Examples
- Color names (case-insensitive): `Red`, `red`, `RED`
- Hex codes: `#FF0000`, `#3498DB`, `#27AE60`
- Unmapped values will not display swatches (dropdown only)

## File Structure

```
Vendor/CustomOptionSwatches/
├── registration.php                                    # Module registration
├── etc/
│   ├── module.xml                                      # Module configuration
│   ├── frontend/
│   │   ├── di.xml                                      # Dependency injection
│   │   └── layout/
│   │       └── catalog_product_view.xml               # Layout configuration
├── Model/
│   ├── SwatchConfig.php                               # Swatch configuration and color map
│   ├── SwatchConfigFactory.php                        # Factory for SwatchConfig
│   └── SwatchConfigProvider.php                       # Service for accessing config
├── Plugin/
│   └── OptionsPlugin.php                              # Plugin to enrich options data
├── Block/
│   └── SwatchOptions.php                              # Block for template data
└── view/frontend/
    ├── templates/
    │   └── product/view/
    │       └── options.phtml                          # Options template with swatch rendering
    └── web/
        ├── js/
        │   └── product-swatch.js                      # RequireJS component for interactivity
        └── css/
            └── product-swatch.less                    # Swatch styling
```

## Architecture

### Components

#### Model Layer
- **SwatchConfig**: Contains color map and logic to retrieve colors for values
- **SwatchConfigFactory**: Creates SwatchConfig instances
- **SwatchConfigProvider**: Service class providing access to SwatchConfig

#### Plugin Layer
- **OptionsPlugin**: Intercepts product options block rendering and enriches it with swatch data

#### Frontend Layer
- **options.phtml**: Template that renders options and swatches
- **product-swatch.js**: RequireJS component that handles swatch interactivity
- **product-swatch.less**: LESS stylesheet for visual styling

### Data Flow
1. Product page loads with custom options
2. OptionsPlugin plugin intercepts the options block
3. Plugin builds swatch data from product option values
4. Template receives swatch data and renders swatches below select
5. JavaScript component initializes swatch click handlers
6. Clicking a swatch updates the hidden select element

## Styling

### Customization
The LESS file (`view/frontend/web/css/product-swatch.less`) contains:
- `.swatch-container`: Container for swatches
- `.swatch`: Individual swatch styling
- Hover and active states
- Responsive breakpoints for mobile devices

### Custom Styles
To override styles, create a custom theme and include:
```less
@import 'Vendor_CustomOptionSwatches::css/product-swatch';
```

## JavaScript

### Component Initialization
The JavaScript component uses x-magento-init for initialization:
```html
<script type="text/x-magento-init">
{
    "[data-role=swatch-container]": {
        "Vendor_CustomOptionSwatches/js/product-swatch": {
            "swatchConfig": <?php echo $swatchConfig; ?>
        }
    }
}
</script>
```

### Event Handling
- Clicking a swatch updates the select element and triggers change event
- Changing the select updates the active swatch indicator
- All form validation uses the original select element

## Troubleshooting

### Swatches Not Displaying
1. Check that product custom options are of type "Dropdown"
2. Verify option values match color names in the map (case-insensitive)
3. Clear browser cache and Magento cache
4. Run `php bin/magento setup:static-content:deploy`

### Module Not Recognized
1. Verify file is in correct location: `app/code/Vendor/CustomOptionSwatches/`
2. Run `php bin/magento setup:upgrade`
3. Check `var/log/system.log` for errors

### Static Content Issues
1. Run: `php bin/magento setup:static-content:deploy -f`
2. Clear `pub/static` directory if necessary
3. Run: `php bin/magento cache:clean`

## Deployment Checklist

- [ ] Copy module to `app/code/Vendor/CustomOptionSwatches/`
- [ ] Run `php bin/magento setup:upgrade`
- [ ] Run `php bin/magento module:enable Vendor_CustomOptionSwatches`
- [ ] Run `php bin/magento setup:di:compile`
- [ ] Run `php bin/magento setup:static-content:deploy`
- [ ] Run `php bin/magento cache:clean`
- [ ] Test on product page with dropdown custom options
- [ ] Verify swatches display and are clickable
- [ ] Test form submission
- [ ] Verify mobile responsiveness

## Extension Points

### Adding More Colors
Edit `Model/SwatchConfig.php` and add to `DEFAULT_COLOR_MAP`:
```php
const DEFAULT_COLOR_MAP = [
    // ... existing colors
    'custom_color' => '#ABC123',
];
```

### Custom Color Resolution
Extend `SwatchConfig::getColorForValue()` to implement custom color resolution logic.

### Template Customization
Create a theme override for `templates/product/view/options.phtml` to customize rendering.


## Support
For issues or questions, contact the development team.
