<?php
/**
 * Rubedo
 *
 * LICENSE
 *
 * yet to be written
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2012 WebTales (http://www.webtales.fr)
 * @license    yet to be written
 * @version    $Id:
 */

Use Rubedo\Services\Manager;

require_once ('AbstractController.php');
/**
 *
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
class Blocks_ContentListController extends Blocks_AbstractController
{

    /**
     * Default Action, return the Ext/Js HTML loader
     */
    public function indexAction() {
        $this->_dataReader = Manager::getService('Contents');
        $this->_typeReader = Manager::getService('ContentTypes');
        $blockConfig = $this->getRequest()->getParam('block-config');

        $taxanomyTerm = array_pop($blockConfig['taxonomy']);

        $output = array();

        //$filterArray[] = array('property' => 'typeId', 'value' => '999999999999999999999999');
        //$filterArray[] = array('property' => 'typeId', 'value' => '507fea58add92a5108000000');
        $filterArray[] = array('property' => 'status', 'value' => 'published');

        $filterArray[] = array('property' => 'taxonomy.50c0cabc9a199dcc0f000002', 'value' => $taxanomyTerm);

        $sort = array();
        $sort[] = array('property' => 'text', 'direction' => 'asc');

        $pageData['limit'] = isset($blockConfig['pageSize']) ? $blockConfig['pageSize'] : 6;
        $pageData['currentPage'] = $this->getRequest()->getParam("page", 1);

        $contentArray = $this->_dataReader->getList($filterArray, $sort, (($pageData['currentPage'] - 1) * $pageData['limit']), $pageData['limit']);

        $nbItems = $contentArray["count"];
        if ($nbItems > 0) {
            $pageData['nbPages'] = (int)ceil(($nbItems) / $pageData['limit']);
            $pageData['limitPage'] = min(array($pageData['nbPages'],3));

            $typeArray = $this->_typeReader->getList();
            $contentTypeArray = array();
            foreach ($typeArray['data'] as $dataType) {
                $contentTypeArray[(string)$dataType['id']] = "root/blocks/shortsingle/" . preg_replace('#[^a-zA-Z]#', '', $dataType['type']) . ".html.twig";
            }

            foreach ($contentArray['data'] as $vignette) {
                $fields = $vignette['fields'];
                $fields['title'] = $fields['text'];
                unset($fields['text']);
                $fields['id'] = (string)$vignette['id'];
                $fields['type'] = $contentTypeArray[(string)$vignette['typeId']];
                $data[] = $fields;
            }

            $output["data"] = $data;
            $output["page"] = $pageData;
        }

        if (isset($blockConfig['displayType'])) {
            $template = Manager::getService('FrontOfficeTemplates')->getFileThemePath("blocks/" . $blockConfig['displayType'] . ".html.twig");
        } else {
            $template = Manager::getService('FrontOfficeTemplates')->getFileThemePath("blocks/contentlist.html.twig");

        }

        $css = array();
        $js = array();
        $this->_sendResponse($output, $template, $css, $js);
    }

}