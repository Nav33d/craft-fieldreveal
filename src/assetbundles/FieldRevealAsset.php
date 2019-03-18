<?php

/**
 * Field Reveal plugin for Craft CMS 3
 *
 * View where your custom fields are being used.
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\fieldreveal\assetbundles;

use Craft;
use craft\web\View;
use craft\helpers\Json;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class FieldRevealAsset extends AssetBundle
{

  // Public Methods
  // =========================================================================

  /**
   * Initializes the bundle.
   */
  public function init()
  {
      // define the path that your publishable resources live
    $this->sourcePath = "@nav33d/fieldreveal/assetbundles/dist";

    // define the dependencies
    $this->depends = [
      CpAsset::class,
    ];

    // define the relative path to CSS/JS files that should be registered with the page
    // when this asset bundle is registered
    $this->js = [
      'js/fieldreveal.js',
    ];

    $this->css = [
      'css/fieldreveal.css',
    ];

    parent::init();
  }

}
