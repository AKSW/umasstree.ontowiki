<?php
/**
 * This file is part of the {@link http://ontowiki.net OntoWiki} project.
 *
 * @copyright Copyright (c) 2013, {@link http://aksw.org AKSW}
 * @license   http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * Controller for OntoWiki Basicimporter Extension
 *
 * @category OntoWiki
 * @package  OntoWiki_Extensions_Umasstree
 * @author   Sebastian Tramp <mail@sebastian.tramp.name>
 */
class UmasstreeController extends OntoWiki_Controller_Component
{
    private $_model;
    private $_treeData = array();

    public function dataAction()
    {
        // m is automatically used and selected
        if ((!isset($this->_request->m)) && (!$this->_owApp->selectedModel)) {
            throw new OntoWiki_Exception(
                'No model pre-selected model and missing parameter m (model)!'
            );
        } else {
            $this->_model = $this->_owApp->selectedModel;
        }

        // disable auto-rendering
        $this->_helper->viewRenderer->setNoRender();

        // disable layout for Ajax requests
        $this->_helper->layout()->disableLayout();

        // provide the tree data
        $this->_treeData = $this->_getTreeData();

        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody(json_encode($this->_treeData));
    }

    private function _getTreeData() {
        $subclasses = array();
        $types = array();
        $labels = array();

        $titleHelper = new OntoWiki_Model_TitleHelper($this->_model);

        // fetch all rdfs:subClassOf relations
        // please adjust this query if you want other results here
        $subclassQueryResult = $this->_model->sparqlQuery(
            'SELECT DISTINCT ?class ?subclass {
                ?subclass <' . EF_RDFS_SUBCLASSOF . '> ?class
            } LIMIT 200 '
        );
        foreach ($subclassQueryResult as $resultItem) {
            $subclasses[$resultItem['class']][] = $resultItem['subclass'];
            // fill titlehelper and label array
            $titleHelper->addResource($resultItem['subclass']);
            $titleHelper->addResource($resultItem['class']);
            $labels[$resultItem['subclass']] = '';
            $labels[$resultItem['class']] = '';
        }

        // fetch all instance types
        $typeQueryResult = $this->_model->sparqlQuery(
            'SELECT DISTINCT ?instance ?class {
                ?instance <' . EF_RDF_TYPE . '> ?class
            } LIMIT 200 '
        );
        foreach ($typeQueryResult as $resultItem) {
            $types[$resultItem['class']][] = $resultItem['instance'];
            // fill titlehelper and label array
            $titleHelper->addResource($resultItem['class']);
            $titleHelper->addResource($resultItem['instance']);
            $labels[$resultItem['class']] = '';
            $labels[$resultItem['instance']] = '';
        }

        // fetch all labels
        foreach ($labels as $uri => $label) {
            $labels[$uri] = $titleHelper->getTitle($uri);
        }

        // prepare overall array
        return $this->_treeData = array(
            'subclasses' => $subclasses,
            'types' => $types,
            'labels' => $labels
        );
    }
}
