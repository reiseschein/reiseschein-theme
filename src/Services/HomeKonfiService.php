<?php

namespace reiseschein\Services;

use IO\Services\CategoryService;
use IO\Services\SessionStorageService;
use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Log\Loggable;
/**
 * Class HomeKonfiService
 * @package reiseschein\Services
 */
class HomeKonfiService {
    
    use Loggable;

    private $queryFieldLabel;
    private $queryFieldValues;
    private $facettFields;

    private $configRepo;
    private $catService;
    private $sessionService;

    private $cat;
    private $catId;
    private $catUrl;
    private $json = null;
    private $lang;

    private function addCategoryNames($id, $values){
        $myCat = $this->catService->get($id);
        $name  = $myCat->details[0]->name;
        if (($values!=null)&&
            ($values!="")&&
            ($name!=null)){
            if(strpos($values, $name)===false){$values .= ",," . $name;}
        }elseif($name!=null){
            $values = $name;
        }
        $catChildren = $this->catService->getChildren($id);
        if($catChildren){
            foreach ($catChildren as $child) {
                $values = $this->addCategoryNames($child->id, $values);
            }
        }
        // $this->getLogger(__METHOD__)->error('HomeKonfiService::addCategoryNames', [
        //     'values1'=> $values,
        //     'id'    => $id,
        //     'name'  => $name,
        //     'myCat' => $myCat
        // ]);
        return $values;
    }

    private function processIDs($json){
        $tmp = null;
        // $this->getLogger(__METHOD__)->error('HomeKonfiService::processIDs', [
        //     'json'=> $json["config"]
        // ]);
        foreach ($json["config"] as &$node) {
            $this->getLogger(__METHOD__)->error('HomeKonfiService::processIDs', [
                'type'=> $node["type"]
            ]);
            if ($node['type']=='query'){
                $values = $node["values"];
                $ids    = explode(",,",$node["id"]);

                foreach ($ids as $id) {
                    $values = $this->addCategoryNames($id, $values);
                }
                $node["values"] = $values;
            }
        }
        return json_encode($json);
    }

    private function setJson($branch=null){
        $json = json_decode(json_decode( json_encode($this->cat->details[0]->description2), true), true);
        if(!$branch!=null) { $json = $json[$branch]; }
        $this->json = $this->processIDs($json);
    }

    public function __construct( CategoryService $CategoryService,
                                ConfigRepository $ConfigRepository,
                                SessionStorageService $SessionStorageService) {
        $this->catService       = $CategoryService;
        $this->configRepo       = $ConfigRepository;
        $this->sessionService   = $SessionStorageService;

        $this->lang     = $this->sessionService->getLang();
        $this->catId    = $this->configRepo->get('reiseschein.homepage.konfiCategoryId');
        $this->cat      = $this->catService->get($this->catId, $this->lang);
    }


    public function getCategory(){
        return $this->cat;
    }
    public function getLang(){
        return $this->lang;
    }
    public function getJson($branch = null){ 
        if ($this->json == null ){ $this->setJson($branch); }

        if ($branch == null){
            return $this->json;
        }else{

            return json_decode( $this->json, true)[$branch];
        }
    }
    public function getDate(){
        return $this->cat->updatedAt;
    }
}