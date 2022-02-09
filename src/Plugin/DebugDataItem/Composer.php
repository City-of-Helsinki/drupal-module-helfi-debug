<?php

declare(strict_types = 1);

namespace Drupal\helfi_debug\Plugin\DebugDataItem;

use ComposerLockParser\ComposerInfo;
use ComposerLockParser\Package;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\helfi_debug\DebugDataItemPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the debug_data_item.
 *
 * @DebugDataItem(
 *   id = "composer",
 *   label = @Translation("Composer"),
 *   description = @Translation("Composer")
 * )
 */
class Composer extends DebugDataItemPluginBase implements ContainerFactoryPluginInterface {

  private string $root;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->root = $container->getParameter('app.root');
    return $instance;
  }

  private function getComposerInfo() : ComposerInfo {
    $path = sprintf('%s/../composer.lock', $this->root);
    if (!file_exists($path)) {
      throw new \InvalidArgumentException('Composer.lock not found.');
    }
    return new ComposerInfo($path);
  }

  /**
   * Checks if package should be included in collection or not.
   *
   * @param \ComposerLockParser\Package $package
   *   The package.
   *
   * @return bool
   *   TRUE if package should be included.
   */
  private function includePackage(Package $package) : bool {
    return match(TRUE) {
      str_starts_with($package->getName(), 'drupal/helfi_') => TRUE,
      str_starts_with($package->getName(), 'drupal/hdbt') => TRUE,
      default => FALSE,
    };
  }

  public function collect(): array {
    /** @var \ComposerLockParser\Package[] $packages */
    $packages = $this->getComposerInfo()->getPackages();

    $data = [];
    foreach ($packages as $package) {
      if (!$this->includePackage($package)) {
        continue;
      }
      $data['packages'][] = [
        'name' => $package->getName(),
        'version' => $package->getVersion(),
        'type' => $package->getType(),
        'require' => $package->getRequire(),
        'requireDev' => $package->getRequireDev(),
        'time' => $package->getTime()->format('c'),
      ];
    }
    return $data;
  }

}
