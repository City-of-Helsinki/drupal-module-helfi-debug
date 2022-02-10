# Debug module

![CI](https://github.com/City-of-Helsinki/drupal-module-helfi-debug/workflows/CI/badge.svg)

Provides a module to gather all kind of debug information from current Drupal instance.

## Usage

By default this module provides the following debug plugins:
- `composer` (shows installed `drupal/helfi_*` and `drupal/hdbt*` packages and their versions)

Navigate to `/admin/debug` to see available debug data, or`/api/v1/debug` for JSON endpoint.

## Creating your own debug data provider plugin

See [src/Plugin/DebugDataItem/Composer.php](src/Plugin/DebugDataItem/Composer.php) for an example plugin implementation.

At mimimum you need: 
- A plugin class that implements `\Drupal\helfi_debug\DebugDataItemInterface`
- Override the default template with your plugin specific template (`debug-item--{plugin_id}.html.twig`). See [templates/debug-item.html.twig](templates/debug-item.html.twig) for more information. 

## Requirements

- PHP 8.0 or higher.

## Contact

Slack: #helfi-drupal (http://helsinkicity.slack.com/)

Mail: helfi-drupal-aaaactuootjhcono73gc34rj2u@druid.slack.com
