<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

/**
 * This helper gets the content for a page and show it for front mode, or adds the 
 * tinyMCE edition system for back office
 *
 */
class MelisTagsHelper extends AbstractHelper
{
	public $serviceManager;
//	public $renderMode;
//	public $preview;
//	public $datasPages;
//	public $datasPagesSaved;

	public function __construct(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
	}
	
	/**
	 * Gets the text corresponding to the tag and page
	 * 
	 * @param int $idPage Page id to look in
	 * @param string $tagId Tag id to search in the XML of page
	 * @param string $type Type of conf to load for TinyMCE
	 * @param string $defaultValue If nothing in DB, default text to show for rendering
	 * @return string Text of the tag
	 */
	public function __invoke($idPage, $tagId, $type, $defaultValue = '')
	{
	    $forceRenderFront = false;
	    if ($type == null)
	    {
	        $forceRenderFront = true;
	        $type = 'html';
	    }
	    
	    $classname = 'MelisFrontTag' . ucfirst($type) . 'Plugin';
	    
	    try
	    {
	        
    	    $melisFrontTagPlugin = $this->serviceManager->get('ControllerPluginManager')->get($classname);
    	        
    	    $melisFrontTagPlugin->setPluginFromDragDrop(false);
    	    
    	    $melisFrontTagPluginView = $melisFrontTagPlugin->render(array(
    	        'tagPageId' => $idPage,
    	        'id' => $tagId,
    	        'type' => $type,
    	        'default' => $defaultValue,
    	        'fromDragDropZone' => false
    	    ), false, $forceRenderFront);

    	    $viewRender = $this->serviceManager->get('ViewRenderer');
    	    $tagHtml = $viewRender->render($melisFrontTagPluginView);
    	    
    	    return $tagHtml;
	    }
	    catch (Exception $e)
	    {
	        return 'Tag ' . $type . ' error';
	    }
	}
}