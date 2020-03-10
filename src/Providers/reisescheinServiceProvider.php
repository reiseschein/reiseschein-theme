<?php

namespace reiseschein\Providers;

use IO\Extensions\Functions\Partial;
use IO\Helper\CategoryKey;
use IO\Helper\CategoryMap;
use IO\Helper\TemplateContainer;
use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Templates\Twig;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ConfigRepository;
use IO\Helper\ComponentContainer;
use IO\Helper\ResourceContainer;
use IO\Services\ContentCaching\Services\Container;
use reiseschein\Contexts\reisescheinSingleItemContext;
use reiseschein\Contexts\resiechenListViewContext;
use reiseschein\Contexts\ItemSearchContext;
use IO\Services\ItemSearch\Helper\ResultFieldTemplate;
use reiseschein\Extensions\ReisescheinExtension;
use reiseschein\Providers\reisescheinRouteServiceProvider;


class reisescheinServiceProvider extends ServiceProvider
{
 const EVENT_LISTENER_PRIORITY = 99;


    /**
     * Register the service provider.
     */

    public function register() {
        $this->getApplication()->register(reisescheinRouteServiceProvider::class);
    }

    public function boot(Twig $twig, Dispatcher $eventDispatcher, ConfigRepository $config)
    {
    $twig->addExtension(ReisescheinExtension::class);

    $eventDispatcher->listen('IO.Resources.Import',
        function (ResourceContainer $container) {
            $container->addScriptTemplate('reiseschein::Homepage.FsHomeKonfi');
        }
    );

    $eventDispatcher->listen('IO.ctx.item', function (TemplateContainer $container, $templateData = []){
         $container->setContext( reisescheinSingleItemContext::class);
         return false;
    });
    $eventDispatcher->listen('IO.ctx.category.item', function (TemplateContainer $container, $templateData = []){
         $container->setContext( resiechenListViewContext::class);
         return false;
    });
    $eventDispatcher->listen('IO.ctx.search', function (TemplateContainer $container, $templateData = []){
         $container->setContext( ItemSearchContext::class);
         return false;
    });

        $eventDispatcher->listen('IO.tpl.home', function(TemplateContainer $container, $templateData)
        {
            $container->setTemplate("reiseschein::Homepage.Homepage");
            return false;
        });
        $eventDispatcher->listen('IO.tpl.item', function(TemplateContainer $container, $templateData) {
            $container->setTemplate("reiseschein::Item.SingleItemWrapper");
            return false;
        });
        $eventDispatcher->listen('IO.tpl.search', function(TemplateContainer $container, $templateData) {
            $container->setTemplate("reiseschein::Category.Item.CategoryItem");
            return false;
        });

        $eventDispatcher->listen('IO.tpl.category.item', function(TemplateContainer $container, $templateData) {
            $container->setTemplate("reiseschein::Category.Item.CategoryItem");
            return false;
        });
        $eventDispatcher->listen('IO.tpl.wish-list', function(TemplateContainer $container, $templateData) {
            $container->setTemplate("reiseschein::WishList.WishListView");
            return false;
        });
        $eventDispatcher->listen('IO.tpl.checkout', function(TemplateContainer $container, $templateData) {
            $container->setTemplate("reiseschein::Checkout.CheckoutView");
            return false;
        });
        $eventDispatcher->listen( 'IO.ResultFields.*', function(ResultFieldTemplate $container, $templateData) {
            $container->setTemplates([
                ResultFieldTemplate::TEMPLATE_LIST_ITEM     => 'reiseschein::ResultFields.ListItem',
                ResultFieldTemplate::TEMPLATE_SINGLE_ITEM   => 'reiseschein::ResultFields.SingleItem',
                ResultFieldTemplate::TEMPLATE_BASKET_ITEM   => 'Ceres::ResultFields.BasketItem',
                ResultFieldTemplate::TEMPLATE_AUTOCOMPLETE_ITEM_LIST => 'Ceres::ResultFields.AutoCompleteListItem',
                ResultFieldTemplate::TEMPLATE_CATEGORY_TREE => 'Ceres::ResultFields.CategoryTree'
            ]);
            return false;
        }, self::EVENT_LISTENER_PRIORITY);

        $eventDispatcher->listen('IO.init.templates', function(Partial $partial)
        {
            $partial->set('page-design', 'reiseschein::PageDesign.PageDesign');
            $partial->set('footer','reiseschein::PageDesign.Partials.Footer');
            $partial->set('header', 'reiseschein::PageDesign.Partials.Header.Header');
            $partial->set('page-metadata', 'reiseschein::PageDesign.Partials.PageMetadata');
        }, self::EVENT_LISTENER_PRIORITY);

        $eventDispatcher->listen('IO.tpl.category.content', function(TemplateContainer $container, $templateData) {
                    $container->setTemplate("reiseschein::Category.Content.CategoryContent");
                    return false;
                });

        $eventDispatcher->listen('IO.Component.Import', function (ComponentContainer $componentContainer)
        {
           if($componentContainer->getOriginComponentTemplate() == 'Ceres::ItemList.Components.CategoryItem')
            {
                $componentContainer->setNewComponentTemplate('reiseschein::ItemList.Components.CategoryItem');
            }
            if($componentContainer->getOriginComponentTemplate() == 'Ceres::Item.Components.SingleItem')
            {
                $componentContainer->setNewComponentTemplate('reiseschein::Item.Components.SingleItem');
            }
            if($componentContainer->getOriginComponentTemplate() == 'Ceres::Item.Components.AddToWishList')
            {
                $componentContainer->setNewComponentTemplate('reiseschein::Item.Components.AddToWishList');
            }
            if($componentContainer->getOriginComponentTemplate() == 'Ceres::ItemList.Components.Filter.ItemFilter')
            {
                $componentContainer->setNewComponentTemplate('reiseschein::ItemList.Components.Filter.ItemFilter');
            }
            if($componentContainer->getOriginComponentTemplate() == 'Ceres::ItemList.Components.Filter.ItemFilterPrice')
            {
                $componentContainer->setNewComponentTemplate('reiseschein::ItemList.Components.Filter.ItemFilterPrice');
            }
            if($componentContainer->getOriginComponentTemplate() == 'Ceres::Item.Components.ItemImageCarousel')
            {
                $componentContainer->setNewComponentTemplate('reiseschein::Item.Components.ItemImageCarousel');
            }
        }, self::EVENT_LISTENER_PRIORITY);

  }
}
