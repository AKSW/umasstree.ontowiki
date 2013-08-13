<?php
/**
 * This file is part of the {@link http://ontowiki.net OntoWiki} project.
 *
 * @copyright Copyright (c) 2013, {@link http://aksw.org AKSW}
 * @license   http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * OntoWiki module – umasstree Tree
 *
 * @category OntoWiki
 * @package  OntoWiki_Extensions_Umasstree
 * @author   Sebastian Tramp <mail@sebastian.tramp.name>
 */
class UmasstreeModule extends OntoWiki_Module
{
    public function getTitle()
    {
        return 'Umass Tree Navigation';
    }

    public function getContents()
    {
        $data = array();
        $content = $this->render('templates/umasstree/tree', $data);
        return $content;
    }
}
