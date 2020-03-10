<?php

namespace reiseschein\Contexts;

use Ceres\Contexts\CategoryContext;
use Ceres\Helper\SearchOptions;
use IO\Helper\ContextInterface;
use IO\Services\ItemSearch\SearchPresets\Facets;

use Plenty\Plugin\Log\Loggable;
use reiseschein\Contexts\ItemListContext;
use reiseschein\Services\CategoryItems;

class CategoryItemContext extends CategoryContext implements ContextInterface
{
    use ItemListContext;
    use Loggable;

    public function init($params)
    {
        parent::init($params);

        $itemListOptions1 = [
            'page'          => 1,
            'itemsPerPage'  => 9999,
            'sorting'       => $this->ceresConfig->sorting->defaultSorting,
            'facets'        => $this->getParam( 'facets' ),
            'categoryId'    => $this->category->id,
            'priceMin'      => 0,
            'priceMax'      => 0
        ];
        $this->getPriceRange(
            [
                'itemList' => CategoryItems::getCustomSearchFactory( $itemListOptions1 ),
                'facets'   => Facets::getSearchFactory( $itemListOptions1 ) 
            ],
            $itemListOptions1,
            SearchOptions::SCOPE_CATEGORY
        );    
        $this->getLogger(__METHOD__)->error('reiseschein::getPriceRange', [
            'bigPrice'   => $this->bigPrice,
            'smallPrice' => $this->smallPrice
        ]);

        $itemListOptions = [
            'page'          => $this->getParam( 'page', 1 ),
            'itemsPerPage'  => $this->getParam( 'itemsPerPage', $this->ceresConfig->pagination->rowsPerPage[0] * $this->ceresConfig->pagination->columnsPerPage ),
            'sorting'       => $this->getParam( 'sorting', $this->ceresConfig->sorting->defaultSorting ),
            'facets'        => $this->getParam( 'facets' ),
            'categoryId'    => $this->category->id,
            'priceMin'      => $this->request->get('priceMin', 0),
            'priceMax'      => $this->request->get('priceMax', 0)
        ];
        $this->initItemList(
            [
                'itemList' => CategoryItems::getSearchFactory( $itemListOptions ),
                'facets'   => Facets::getSearchFactory( $itemListOptions )
            ],
            $itemListOptions,
            SearchOptions::SCOPE_CATEGORY
        );
    }
}