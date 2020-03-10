<?php

namespace reiseschein\Contexts;

use Ceres\Helper\SearchOptions;
use Ceres\Contexts\GlobalContext;
use IO\Helper\ContextInterface;
use IO\Services\ItemSearch\SearchPresets\Facets;
use IO\Services\ItemSearch\SearchPresets\SearchItems;
use IO\Services\ItemSearch\Services\ItemSearchService;
use reiseschein\Contexts\ItemListContext;
use Plenty\Plugin\Log\Loggable;

class ItemSearchContext extends GlobalContext implements ContextInterface
{
    use ItemListContext;
    use Loggable;

    public $isSearch;
    public $searchString;

    public function init($params)
    {
        parent::init($params);

        $itemListOptions1 = [
            'page'          => 1,
            'itemsPerPage'  => 9999,
            'sorting'       => $this->ceresConfig->sorting->defaultSortingSearch,
            'facets'        => '',
            'query'         => $this->getParam( 'query', '' ),
            'priceMin'      => 0,
            'priceMax'      => 0
        ];

        $this->getPriceRange(
            [
                'itemList' => SearchItems::getSearchFactory( $itemListOptions1 ),
                'facets'   => Facets::getSearchFactory( $itemListOptions1 )
            ],
            $itemListOptions1,
            SearchOptions::SCOPE_SEARCH
        );
        $this->getLogger(__METHOD__)->error('reiseschein::ItemSearchContext', [
            'bigPrice'   => $this->bigPrice,
            'smallPrice' => $this->smallPrice
        ]);

        $itemListOptions = [
            'page'          => $this->getParam( 'page', 1 ),
            'itemsPerPage'  => $this->getParam( 'itemsPerPage', $this->ceresConfig->pagination->rowsPerPage[0] * $this->ceresConfig->pagination->columnsPerPage ),
            'sorting'       => $this->getParam( 'sorting', $this->ceresConfig->sorting->defaultSortingSearch ),
            'facets'        => $this->getParam( 'facets', '' ),
            'query'         => $this->getParam( 'query', '' ),
            'priceMin'      => $this->request->get('priceMin', 0),
            'priceMax'      => $this->request->get('priceMax', 0)
        ];

        $this->initItemList(
            [
                'itemList' => SearchItems::getSearchFactory( $itemListOptions ),
                'facets'   => Facets::getSearchFactory( $itemListOptions )
            ],
            $itemListOptions,
            SearchOptions::SCOPE_SEARCH
        );

        $this->isSearch = true;
        $this->searchString = $itemListOptions['query'];
    }
}