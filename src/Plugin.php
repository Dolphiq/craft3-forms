<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 19-05-17
 * Time: 15:23
 */


namespace plugins\dolphiq\form;

use Craft;
use plugins\dolphiq\form\twigextensions\YiiTwigExtension;

class Plugin extends \craft\base\Plugin
{
  public function init()
  {
    parent::init();

    // Custom initialization code goes here...
    Craft::$app->view->twig->addExtension(new YiiTwigExtension());

  }
}

?>