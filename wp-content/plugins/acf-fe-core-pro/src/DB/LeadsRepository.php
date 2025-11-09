<?php
namespace ACF\FECP\DB;

if (!defined('ABSPATH')) exit;

class LeadsRepository {

  private string $table;

  /**
   * @param int $type_lead  0 = B2C, 1 = B2B
   */
  public function __construct(int $type_lead = 0) {
    global $wpdb;
    $this->table = $wpdb->prefix . ($type_lead === 1 ? 'acf_fecp_leads_b2b' : 'acf_fecp_leads_b2c');
  }

  /**
   * Inserta un nuevo lead
   */
  public function insert(array $data): int {
    global $wpdb;
    $wpdb->insert($this->table, $data);
    return (int)$wpdb->insert_id;
  }

  /**
   * Actualiza un lead existente
   */
  public function update(int $id, array $data): void {
    global $wpdb;
    $wpdb->update($this->table, $data, ['id' => $id]);
  }

  /**
   * Elimina un lead por ID
   */
  public function delete(int $id): void {
    global $wpdb;
    $wpdb->delete($this->table, ['id' => $id]);
  }

  /**
   * Lista leads con paginación
   * @return array{rows:array,total:int,per:int,paged:int}
   */
  public function list(int $paged = 1, int $per = 20): array {
    global $wpdb;
    $offset = max(0, ($paged - 1) * $per);
    $rows = $wpdb->get_results(
      $wpdb->prepare("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d OFFSET %d", $per, $offset),
      ARRAY_A
    );
    $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    return [
      'rows'  => $rows,
      'total' => $total,
      'per'   => $per,
      'paged' => $paged,
    ];
  }

  /**
   * Marca un lead como enviado exitosamente
   */
  public function mark_sent(int $id, string $body = ''): void {
    global $wpdb;
    $wpdb->update($this->table, [
      'sent_to_api' => 1,
      'api_code' => 200,
      'api_response' => $body,
      'updated_at' => current_time('mysql'),
    ], ['id' => $id]);
  }

  /**
   * Marca un lead con error al enviar
   */
  public function mark_error(int $id, int $code = 0, string $body = ''): void {
    global $wpdb;
    $wpdb->update($this->table, [
      'sent_to_api' => 0,
      'api_code' => $code,
      'api_response' => $body,
      'updated_at' => current_time('mysql'),
    ], ['id' => $id]);
  }

  /**
   * Devuelve todas las columnas de la tabla
   */
  public function get_columns(): array {
    global $wpdb;
    $columns = $wpdb->get_col("DESC {$this->table}", 0);
    return is_array($columns) ? $columns : [];
  }
}
