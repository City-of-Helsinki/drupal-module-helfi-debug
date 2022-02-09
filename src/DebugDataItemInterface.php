<?php

declare(strict_types = 1);

namespace Drupal\helfi_debug;

/**
 * Interface for debug_data_item plugins.
 */
interface DebugDataItemInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label() : string;

  /**
   * Collects the data.
   *
   * @return array
   *   The data.
   */
  public function collect() : array;

}
