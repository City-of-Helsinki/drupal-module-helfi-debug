<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_debug\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\helfi_debug\DebugDataItemInterface;
use Drupal\helfi_debug\DebugDataItemPluginManager;
use Drupal\helfi_debug\Plugin\rest\resource\DebugDataResource;
use Drupal\Tests\UnitTestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests DebugDataResource dependency map.
 *
 * @coversDefaultClass \Drupal\helfi_debug\Plugin\rest\resource\DebugDataResource
 * @group helfi_debug
 */
class DebugDataResourceDependencyTest extends UnitTestCase {

  /**
   * Constructs a fake data item plugin.
   *
   * @param array $dependencies
   *   The dependencies.
   *
   * @return \Drupal\helfi_debug\DebugDataItemInterface
   *   The fake debug data plugin.
   */
  private function getDataItemPlugin(array $dependencies) : DebugDataItemInterface {
    return new class ($dependencies) implements DebugDataItemInterface {

      /**
       * Constructs a new object.
       *
       * @param array $dependencies
       *   The dependencies.
       */
      public function __construct(private array $dependencies) {
      }

      /**
       * {@inheritdoc}
       */
      public function label(): string {
        return 'test';
      }

      /**
       * {@inheritdoc}
       */
      public function collect(): array {
        return [];
      }

      /**
       * {@inheritdoc}
       */
      public function calculateDependencies() {
        return $this->dependencies;
      }

    };
  }

  /**
   * Tests that dependencies are populated.
   */
  public function testDependencies() : void {
    $pluginManager = $this->prophesize(DebugDataItemPluginManager::class);
    $pluginManager->getDefinitions()
      ->willReturn([
        ['id' => 'test'],
        ['id' => 'test2'],
      ]);
    $pluginManager->createInstance('test')
      ->willReturn($this->getDataItemPlugin([
        'config' => ['user.role.anonymous'],
        'theme' => ['seven'],
        'content' => ['node:article:f0a189e6-55fb-47fb-8005-5bef81c44d6d'],
        'modules' => ['node', 'user'],
      ]));
    $pluginManager->createInstance('test2')
      ->willReturn($this->getDataItemPlugin([
        'config' => ['user.role.authenticated'],
        'modules' => ['user', 'helfi_debug'],
      ]));
    $logger = $this->prophesize(LoggerInterface::class);
    $loggerFactory = $this->prophesize(LoggerChannelFactoryInterface::class);
    $loggerFactory->get('rest')
      ->willReturn($logger->reveal());
    $container = new ContainerBuilder();
    $container->setParameter('serializer.formats', ['json']);
    $container->set('logger.factory', $loggerFactory->reveal());
    $container->set('plugin.manager.debug_data_item', $pluginManager->reveal());

    $sut = DebugDataResource::create($container, [], 'plugin', []);
    // Make sure multiple dependencies are merged together.
    $this->assertEquals([
      'config' => ['user.role.anonymous', 'user.role.authenticated'],
      'theme' => ['seven'],
      'content' => ['node:article:f0a189e6-55fb-47fb-8005-5bef81c44d6d'],
      'modules' => ['node', 'user', 'helfi_debug'],
    ], $sut->calculateDependencies());
  }

}
