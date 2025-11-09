<?php

namespace ValenciaTravel\Theme;

use ValenciaTravel\Theme\App\Enqueue;

class Init {

  public function register() {
    // Instancia el manejador de assets
    (new Enqueue())->register_hooks();
  }
}
