<?php

namespace Drupal\drupal_utility\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "date_time_block",
 *   admin_label = @Translation("Date Time Block"),
 *   category = @Translation("Custom")
 * )
 */
class DateTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\drupal_utility\TimezoneService definition.
   *
   * @var \Drupal\drupal_utility\TimezoneService
   */
  protected $timezoneService;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->timezoneService = $container->get('drupal_utility.timezone_service');
    $instance->configFactory = $container->get('config.factory');
    $instance->renderer = $container->get('renderer');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $dateTimeConfig = $this->configFactory->get('drupal_utility.date_time_config');
    $currentDateTime = $this->timezoneService->getCurrentDateTime($dateTimeConfig->get('timezone'));

    $currentDateTimeObj = \DateTime::createFromFormat('jS M Y - h:i A', $currentDateTime);
    $currentDateTimestamp = $currentDateTimeObj->getTimestamp();
    $country = $dateTimeConfig->get('country');
    $city = $dateTimeConfig->get('city');

    $build = [
      '#theme' => 'current_date_time_block',
      '#data' => [
        'custom_date' => date('l, d F Y', $currentDateTimestamp),
        'time' => date('H:i a', $currentDateTimestamp),
        'custom_text' => "Time in $city, $country",
      ],
      '#attached' => [
        'library' => [
          'drupal_utility/date_time_display',
        ],
      ],
      '#cache' => [
        // Caching block for 60 seconds
        // by this we can get updated time in every minute.
        'max-age' => 60,
      ],
    ];

    // Adding cacheablity dependency as upon changing admin config form
    // time, date, country should also upddated on block.
    $this->renderer->addCacheableDependency($build, $dateTimeConfig);
    return $build;
  }

}
