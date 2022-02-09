<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_debug\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests Composer debug data plugin.
 *
 * @group helfi_debug
 */
class ComposerDebugDataItemTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'helfi_debug',
  ];

  /**
   * Tests that composer plugin collects data properly.
   */
  public function testCompile() : void {
    /** @var \Drupal\helfi_debug\DebugDataItemPluginManager $manager */
    $manager = $this->container->get('plugin.manager.debug_data_item');
    /** @var \Drupal\helfi_debug\Plugin\DebugDataItem\Composer $plugin */
    $plugin = $manager->createInstance('composer');
    // Make sure we have at least one package.
    $this->assertNotEmpty($plugin->collect()['packages'][0]['name']);
  }

}
