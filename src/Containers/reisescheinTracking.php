<?php

namespace reiseschein\Containers;

use Plenty\Plugin\Templates\Twig;

class reisescheinTracking
{
    public function call(Twig $twig):string
    {
        return $twig->render('reiseschein::reisescheinTracking');
    }
}
