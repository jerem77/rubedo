<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2013, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license. 
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2013 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */
Use Rubedo\Services\Manager;
Use Alb\OEmbed;

require_once ('AbstractController.php');

/**
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
class Blocks_ContentSingleController extends Blocks_AbstractController
{
	
    /**
     * Default Action, return the Ext/Js HTML loader
     */
    public function indexAction ()
    {
        $this->_dataReader = Manager::getService('Contents');
        $this->_typeReader = Manager::getService('ContentTypes');
        
        $blockConfig = $this->getRequest()->getParam('block-config');
        $output["blockConfig"]=$blockConfig;
        
        $mongoId = $this->getRequest()->getParam('content-id');
        $frontOfficeTemplatesService = Manager::getService('FrontOfficeTemplates');
        
        if (isset($mongoId) && $mongoId != 0) {
            $content = $this->_dataReader->findById($mongoId, true, false);
            $data = $content['fields'];
            $termsArray = array();
            if (isset($content['taxonomy'])) {
                if (is_array($content['taxonomy'])) {
                    foreach ($content['taxonomy'] as $key => $terms) {
                        if($key == 'navigation'){
                            continue;
                        }
                        foreach ($terms as $term) {
                            $termsArray[] = Manager::getService('TaxonomyTerms')->getTerm($term);
                        }
                    }
                }
            }
            $data['terms'] = $termsArray;
            $data["id"] = $mongoId;
            
            $type = $this->_typeReader->findById($content['typeId'], true, false);
            $cTypeArray = array();
            $multiValuedArray=array();
            $CKEConfigArray = array();
            $output = $this->getAllParams();
            foreach ($type["fields"] as $value) {
            	
                $cTypeArray[$value['config']['name']] = $value;

                if($value["cType"] == "CKEField"){
                    $CKEConfigArray[$value['config']['name']] = $value["config"]["CKETBConfig"];
                } else if ($value["cType"] == "externalMediaField"){
                    $mediaConfig = $data[$value["config"]["name"]];
                    
                    if(isset($mediaConfig['url'])) {
                    
                        $oembedParams['url'] = $mediaConfig['url'];
                    
                        $cache = Rubedo\Services\Cache::getCache('oembed');
                    
                        $options = array();
                        	
                        if(isset($mediaConfig['maxWidth']) && is_integer($mediaConfig['minHeight'])){
                            $oembedParams['maxWidth'] = $mediaConfig['maxWidth'];
                            $options['maxWidth'] = $mediaConfig['maxWidth'];
                        } else {
                            $oembedParams['maxWidth'] = 0;
                        }
                        	
                        if(isset($mediaConfig['maxHeight']) && is_integer($mediaConfig['maxHeight'])){
                            $oembedParams['maxHeight'] = $mediaConfig['maxHeight'];
                            $options['maxHeight'] = $mediaConfig['maxHeight'];
                        } else {
                            $oembedParams['maxHeight'] = 0;
                        }
                    
                        $cacheKey = 'oembed_item_'.md5(serialize($oembedParams));
                        	
                        if (!($item = $cache->load($cacheKey))) {
                            $response = OEmbed\Simple::request($oembedParams['url'], $options);

                            $item['width'] = $oembedParams['maxWidth'];
                            $item['height'] = $oembedParams['maxHeight'];
                            if($response){
                                if (!stristr($oembedParams['url'],'www.flickr.com')) {
                                    $item['html'] = $response->getHtml();
                                } else {
                                    $raw= $response->getRaw();
                                    if ($oembedParams['maxWidth'] > 0) {
                                        $width_ratio = $raw->width / $oembedParams['maxWidth'];
                                    } else {
                                        $width_ratio = 1;
                                    }
                                    if ($oembedParams['maxHeight'] > 0) {
                                        $height_ratio = $raw->height / $oembedParams['maxHeight'];
                                    } else {
                                        $height_ratio = 1;
                                    }
                                    	
                                    $size="";
                                    if ($width_ratio>$height_ratio) {
                                        $size = "width='".$oembedParams['maxWidth']."'";
                                    }
                                    if ($width_ratio<$height_ratio) {
                                        $size = "height='".$oembedParams['maxHeight']."'";
                                    }
                                    $item['html'] = "<img src='".$raw->url."' ".$size."' title='".$raw->title."'>";
                                }
                        
                                $cache->save($item, $cacheKey,array('oembed'));
                            } else {
                                $item["html"] = "<div class=\"alert alert-error\">Le média éxterne n'a pas pu être chargé</div>";
                            }
                            	
                        }
                    
                        $output['item'] = $item;
                    }
                }
            }
            
            $templateName = preg_replace('#[^a-zA-Z]#', '', $type["type"]);
            $templateName .= ".html.twig";
            $output["data"] = $data;
            $output['activateDisqus']=isset($type['activateDisqus']) ? $type['activateDisqus'] : false ;
            $output["type"] = $cTypeArray;
            $output["CKEFields"] = $CKEConfigArray;
            
            $js = array('/templates/' . $frontOfficeTemplatesService->getFileThemePath("js/rubedo-map.js"),
                '/templates/' . $frontOfficeTemplatesService->getFileThemePath("js/map.js"),
                '/templates/' . $frontOfficeTemplatesService->getFileThemePath("js/rating.js"),
                '/components/jquery/jqueryui/ui/minified/jquery-ui.min.js',
                '/components/jquery/jqueryui/ui/i18n/jquery.ui.datepicker-fr.js',
                '/components/jquery/timepicker/jquery.ui.timepicker.js',
            );
            
            if (isset($blockConfig['displayType']) && !empty($blockConfig['displayType'])) {
            	$template = $frontOfficeTemplatesService->getFileThemePath(
            			"blocks/" . $blockConfig['displayType'] . ".html.twig");
            } else {
	            $template = $frontOfficeTemplatesService->getFileThemePath("blocks/single/" . $templateName);
	            
	            if (! is_file($frontOfficeTemplatesService->getTemplateDir() . '/' . $template)) {
	            	$template = $frontOfficeTemplatesService->getFileThemePath("blocks/single/default.html.twig");
	            }
            }
        } else {
            $output = array();
            $template = $frontOfficeTemplatesService->getFileThemePath("blocks/single/noContent.html.twig");
            $js = array();
        }
        
        $css = array(   "/components/jquery/timepicker/jquery.ui.timepicker.css",
                        "/components/jquery/jqueryui/themes/base/jquery-ui.css",
        );
        
        $this->_sendResponse($output, $template, $css, $js);
    }

    public function getContentsAction ()
    {
        $this->_dataReader = Manager::getService('Contents');
        $returnArray = array();
        $data = $this->getRequest()->getParams();
        if (isset($data['block']['contentId'])) {
            $content = $this->_dataReader->findById($data['block']['contentId']);
            $returnArray[] = array(
                'text' => $content['text'],
                'id' => $content['id']
            );
            $returnArray['total'] = count($returnArray);
            $returnArray["success"] = true;
        } else {
            $returnArray = array(
                "success" => false,
                "msg" => "No query found"
            );
        }
        $this->getHelper('Layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        $this->getResponse()->setBody(Zend_Json::encode($returnArray), 'data');
    }
}
