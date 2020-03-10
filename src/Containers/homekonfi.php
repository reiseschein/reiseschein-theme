<?php

namespace reiseschein\Containers;

use Plenty\Plugin\Templates\Twig;

class homekonfi
{
    public function call(Twig $twig):string
    {
        return $twig->render('reiseschein::Homepage.Konfi');
    }
}
