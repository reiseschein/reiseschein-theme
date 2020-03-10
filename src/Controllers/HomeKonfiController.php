<?php

namespace reiseschein\Controllers;

// use IO\Api\ResponseCode;
use IO\Controllers\LayoutController;
// use IO\Services\TemplateService;
use Plenty\Plugin\Templates\Twig;
//use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Log\Loggable;
// use Plenty\Plugin\Http\Response;
use reiseschein\Services\HomeKonfiService;
// use ThemeFH\Contexts\ManufacturerSearchContext;

/**
 * Class BrandController
 * @package ThemeFH\Controllers
 */
class HomeKonfiController extends LayoutController {

	use Loggable;

	public function getJson(Twig $twig):string 
	{
		$service = pluginApp(HomeKonfiService::class);
		$json	 = $service->getJson("config");

		return $twig->renderString(json_encode($json));
	}
}
