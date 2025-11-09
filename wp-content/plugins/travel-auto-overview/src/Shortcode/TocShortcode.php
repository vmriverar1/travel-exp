<?php
namespace Travel\Overview\Shortcode;

if (!defined('ABSPATH')) exit;

class TocShortcode {

  public function register() {
    add_shortcode('travel_toc', [$this, 'render']);
  }

  /**
   * Render shortcode.
   * Usage: [travel_toc title="Quick Overview"]
   */
  public function render($atts = []) {
    global $post;
    if (!$post) return '';

    $atts = shortcode_atts([
      'title' => 'Quick Overview',
    ], $atts, 'travel_toc');

    $content = $post->post_content;
    if (empty($content)) return '';

    // ✅ Buscar H2..H7 (aunque contengan etiquetas internas)
    if (!preg_match_all('/<(h[2-7])([^>]*)>(.*?)<\/\1>/is', $content, $matches, PREG_SET_ORDER)) {
      return '';
    }

    $found_any_h2 = false;
    $items = [];
    $id_counts = [];

    foreach ($matches as $m) {
      $tag = strtolower($m[1]);
      $level = intval(substr($tag, 1));
      $inner_html = trim($m[3]);
      $title = wp_strip_all_tags($inner_html); // ✅ limpia <strong>, <em>, etc.

      if ($level < 2 || $level > 7) continue;
      if (!$found_any_h2) {
        if ($level !== 2) continue;
        $found_any_h2 = true;
      }

      $base_id = sanitize_title($title);
      if ($base_id === '') $base_id = 'section-' . $level;
      if (!isset($id_counts[$base_id])) $id_counts[$base_id] = 0;
      $id_counts[$base_id]++;
      $id = $base_id;
      if ($id_counts[$base_id] > 1) $id .= '-' . $id_counts[$base_id];

      $items[] = [
        'level' => $level,
        'title' => $title,
        'id'    => $id,
        'raw'   => $m[0],
      ];
    }

    if (empty($items)) return '';

    $tree = $this->build_tree($items);

    $html  = '<div class="travel-toc-box">';
    $html .= '<div class="travel-toc-header"><span class="travel-toc-title">' . esc_html($atts['title']) . '</span></div>';
    $html .= $this->render_list($tree);
    $html .= '</div>';

    // ✅ Insertar IDs post-render (incluso con <strong>, <span>, etc.)
    add_filter('the_content', function($html_content) use ($items) {
      foreach ($items as $it) {
        $tag = 'h' . $it['level'];

        // Saltar si ya tiene id
        $regex_has_id = '/<' . $tag . '[^>]*id=["\']([^"\']+)["\']/i';
        if (preg_match($regex_has_id, $html_content)) continue;

        // Permitir contenido interno variable dentro del heading
        $pattern = '/<' . $tag . '([^>]*)>(.*?)' . preg_quote($it['title'], '/') . '(.*?)<\/' . $tag . '>/is';
        $replacement = '<' . $tag . '$1 id="' . esc_attr($it['id']) . '">$2' . $it['title'] . '$3</' . $tag . '>';
        $html_content = preg_replace($pattern, $replacement, $html_content, 1);
      }
      return $html_content;
    }, 20);

    wp_enqueue_script('travel-toc-scroll');
    wp_enqueue_style('travel-toc-style');

    return $html;
  }

  /** Construye la jerarquía **/
  private function build_tree($items) {
    $root = [];
    $stack = [['level' => 1, 'children' => &$root]];

    foreach ($items as $it) {
      $node = [
        'title' => $it['title'],
        'id'    => $it['id'],
        'level' => $it['level'],
        'children' => []
      ];

      while (!empty($stack) && end($stack)['level'] >= $it['level']) {
        array_pop($stack);
      }

      $parent_index = count($stack) - 1;
      $stack[$parent_index]['children'][] = $node;

      $last_index = count($stack[$parent_index]['children']) - 1;
      $ref = &$stack[$parent_index]['children'][$last_index];
      $stack[] = ['level' => $it['level'], 'children' => &$ref['children']];
    }

    return $root;
  }

  /** Render recursivo **/
  private function render_list($nodes) {
    if (empty($nodes)) return '';
    $html = '<ul class="travel-toc-list">';
    foreach ($nodes as $n) {
      $html .= '<li>';
      $html .= '<a href="#' . esc_attr($n['id']) . '">' . esc_html($n['title']) . '</a>';
      if (!empty($n['children'])) {
        $html .= $this->render_list($n['children']);
      }
      $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
  }
}
