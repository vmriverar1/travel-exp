<?php
namespace ACF\FECP\DB;
if (!defined('ABSPATH')) exit;

class Installer {
  public function install() {
    SchemaB2C::create_table();
    SchemaB2B::create_table();
  }
}
