<?php

namespace reiseschein\Containers;

use Plenty\Plugin\Templates\Twig;

class reisescheinScripts
{
    public function call(Twig $twig):string
    {
        return $twig->render('reiseschein::reisescheinScripts');
    }
}
