<?php

namespace Drupal\product_qr\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\File\FileSystemInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides an apppurchaselinkqrblock block.
 *
 * @Block(
 *   id = "app_purchase_link_qr",
 *   admin_label = @Translation("App Purchase Link QR"),
 *   category = @Translation("Custom")
 * )
 */
class AppPurchaseLinkQRBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * Constructs a new AppPurchaseLinkQRBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Symfony\Component\HttpFoundation\Request $current_request
   *   The current request.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, FileSystemInterface $file_system, Request $current_request) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->fileSystem = $file_system;
    $this->currentRequest = $current_request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('file_system'),
      $container->get('request_stack')->getCurrentRequest(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->routeMatch->getParameter('node');
    $build = [];

    if ($node && $node->getType() === 'product') {
      $prodTitle = $node->getTitle();
      $prodNid = $node->id();
      $purchaseLink = $node->get('field_purchase_link')->value;
      $baseUrl = $this->currentRequest->getSchemeAndHttpHost();

      $endroidQR = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($purchaseLink)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(350)
        ->margin(5)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->labelText('App Purchase Link')
        ->labelFont(new NotoSans(12))
        ->labelAlignment(new LabelAlignmentCenter())
        ->validateResult(FALSE)
        ->build();

      $qrCodeDirectory = $this->fileSystem->realpath('public://') . "/qr_code/";
      $fileName = $prodNid . "_app_purchase_link_qr_code.png";
      $qrPath = $qrCodeDirectory . $fileName;
      $endroidQR->saveToFile($qrPath);
      $qrImgUrl = $baseUrl . "/sites/default/files/qr_code/" . $fileName;

      $build = [
        'content' => [
          '#markup' => '<img src="' . $qrImgUrl . '" alt="' . $prodTitle . '"> </img>',
        ],
        '#cache' => [
          // Invalidate block cache when node changes.
          // Hence, setting block cache tags as node.
          'tags' => $node->getCacheTags(),
        ],
      ];
    }
    return $build;
  }

}
