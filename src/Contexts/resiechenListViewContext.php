<?php

namespace reiseschein\Contexts;

use reiseschein\Contexts\CategoryItemContext;
use IO\Helper\ContextInterface;

use IO\Services\CategoryService;

use Plenty\Plugin\Log\Loggable;

use Ceres\Helper\SearchOptions;
use IO\Services\ItemSearch\SearchPresets\CategoryItems;
use IO\Services\ItemSearch\SearchPresets\Facets;

class resiechenListViewContext extends CategoryItemContext implements ContextInterface
{
    use Loggable;

    private function custom_init($params)
    {
        $itemListOptions = [
            'page'          => 1,
            'itemsPerPage'  => 999,
            'sorting'       => "",
            'facets'        => [],
            'categoryId'    => $this->category->id,
            'priceMin'      => 0,
            'priceMax'      => 0
        ];

        $this->initItemList(
            [
                'itemList' => CategoryItems::getSearchFactory( $itemListOptions ),
                'facets'   => Facets::getSearchFactory( $itemListOptions )
            ],
            $itemListOptions,
            SearchOptions::SCOPE_CATEGORY
        );
        $tmpList            = array_map(array($this, 'mapList'), $this->itemList);
        $this->bigPrice     = max($tmpList);
        $this->smallPrice   = min($tmpList);
    }


    public function init($params)
    {
        parent::init($params);

        // foreach ($this->searchResults['itemList']['documents'] as $item) {            
        //     $this->getLogger(__METHOD__)->error('reiseschein::init', [
        //         '$item'   => $item["data"]["prices"]
        //     ]);
        // }
    }
}