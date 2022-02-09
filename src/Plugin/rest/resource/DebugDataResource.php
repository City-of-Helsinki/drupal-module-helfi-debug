<?php

declare(strict_types = 1);

namespace Drupal\helfi_debug\Plugin\rest\resource;

use Drupal\Component\Plugin\DependentPluginInterface;
use Drupal\helfi_debug\DebugDataItemPluginManager;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Represents Debug records as resources.
 *
 * @RestResource (
 *   id = "helfi_debug_data",
 *   label = @Translation("Debug data"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/debug"
 *   }
 * )
 */
final class DebugDataResource extends ResourceBase implements DependentPluginInterface {

  /**
   * The debug data plugin manager.
   *
   * @var \Drupal\helfi_debug\DebugDataItemPluginManager
   */
  private DebugDataItemPluginManager $manager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) : DebugDataResource {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest')
    );
    $instance->manager = $container->get('plugin.manager.debug_data_item');

    return $instance;
  }

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function get() {
    $data = [];
    foreach ($this->manager->getDefinitions() as $definition) {
      /** @var \Drupal\helfi_debug\DebugDataItemInterface $instance */
      $instance = $this->manager->createInstance($definition['id']);
      $data[$definition['id']] = [
        'label' => $instance->label(),
        'data' => $instance->collect(),
      ];
    }
    return new ResourceResponse($data);
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

}
