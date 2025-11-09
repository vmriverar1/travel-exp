<?php
namespace ACF\FECP\Admin;
if (!defined('ABSPATH')) exit;
class Menu {
  public function register(){
    add_action('admin_menu', function(){
      add_menu_page('Leads (ACF Forms)','Leads (ACF Forms)','manage_options','fecp-leads',[new LeadsPage(),'render'],'dashicons-feedback',56);
    });
  }
}
