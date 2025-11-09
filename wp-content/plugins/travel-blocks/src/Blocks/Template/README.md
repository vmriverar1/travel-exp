# Template Blocks

Esta carpeta contiene los bloques de la categoría **"Template Blocks"** diseñados específicamente para ser usados en templates de WordPress (como `single-package.html`).

## Características

✅ **Datos de preview automáticos** - Muestran contenido de ejemplo en el Site Editor
✅ **Datos reales en frontend** - Obtienen datos del CPT Package en el frontend
✅ **EditorHelper integrado** - Detección automática de modo editor
✅ **Código limpio** - Extienden `TemplateBlockBase` con convenciones claras

## Estructura

Todos los bloques Template siguen esta estructura:

```php
namespace Travel\Blocks\Blocks\Template;

use Travel\Blocks\Core\TemplateBlockBase;
use Travel\Blocks\Core\PreviewDataTrait;

class MiBloque extends TemplateBlockBase
{
    use PreviewDataTrait;

    public function __construct()
    {
        $this->name = 'mi-bloque';
        $this->title = 'Mi Bloque';
        $this->description = 'Descripción del bloque';
        $this->icon = 'admin-generic';
        $this->keywords = ['keyword1', 'keyword2'];
    }

    protected function render_preview(array $attributes): string
    {
        $data = [
            'package' => $this->get_preview_package_data(),
            'is_preview' => true,
        ];

        return $this->load_template('mi-bloque', $data);
    }

    protected function render_live(int $post_id, array $attributes): string
    {
        $data = [
            'package' => $this->get_package_data($post_id),
            'is_preview' => false,
        ];

        return $this->load_template('mi-bloque', $data);
    }

    private function get_package_data(int $post_id): array
    {
        // Obtener datos reales del post
        return [
            'title' => get_the_title($post_id),
            // ... más datos
        ];
    }
}
```

## Templates

Los templates PHP se ubican en: `/templates/template/`

Ejemplo: `/templates/template/mi-bloque.php`

## Bloques Disponibles

Ver `/docs/plan-migracion-template-blocks.md` para la lista completa de bloques a crear.

## Convenciones

1. **Nombres de archivo**: PascalCase (ej: `HeroMediaGrid.php`)
2. **Nombres de bloque**: kebab-case (ej: `hero-media-grid`)
3. **Categoría**: Siempre `template-blocks`
4. **Templates**: Siempre en `/templates/template/`
5. **Preview Data**: Usar `PreviewDataTrait` cuando sea posible

## Fecha

Creado: 2025-10-25
