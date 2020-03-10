<?php

namespace reiseschein\Containers;

use Plenty\Plugin\Templates\Twig;

class reiseschein
{
    public function call(Twig $twig):string
    {
        return $twig->render('reiseschein::reiseschein');
    }
}
