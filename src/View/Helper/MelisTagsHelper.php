<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;

/**
 * This helper gets the content for a page and show it for front mode, or adds the 
 * tinyMCE edition system for back office
 *
 */
class MelisTagsHelper extends AbstractHelper
{
	public $serviceManager;
	public $renderMode;
	public $preview;
	public $datasPages;
	public $datasPagesSaved;

	public function __construct($sm, $renderMode, $preview)
	{
		$this->serviceManager = $sm;
		$this->renderMode = $renderMode;
		$this->preview = $preview;
		$this->datasPages = array();
		$this->datasPagesSaved = array();
	}
	
	/**
	 * Gets the datas from a page and "cache" it in an array to avoid
	 * multiple querries when getting content
	 * 
	 * @param int $idPage
	 */
	private function getDatasPage($idPage)
	{
		$melisPage = $this->serviceManager->get('MelisEnginePage');
		
		if (empty($this->datasPages[$idPage]))
		{
			$datasPage = $melisPage->getDatasPage($idPage, 'published');
			$this->datasPages[$idPage] = (!empty($datasPage)) ? $datasPage->getMelisPageTree() : '';
		}
		if (empty($this->datasPagesSaved[$idPage]) && $this->renderMode == 'melis')
		{
			$datasPageSaved = $melisPage->getDatasPage($idPage, 'saved');
			$this->datasPagesSaved[$idPage] = (!empty($datasPageSaved)) ? $datasPageSaved->getMelisPageTree() : '';
		}
	}
	
	/**
	 * Gets a tag in an XML by its id
	 * 
	 * @param string $xmlPage
	 * @param int $tagId
	 * @return string The content of the tag
	 */
	public function getPageTagValue($xmlPage, $tagId)
	{
		$xml = simplexml_load_string($xmlPage);
		if ($xml)
		{
			foreach ($xml->melisTag as $melisTag)
			{
				$id = (string)$melisTag->attributes()->id;
				
				if ($id == $tagId)
					return (string)$melisTag;
			}
		}
		
		return '';
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
		if ($this->renderMode == 'melis' || empty($this->datasPages[$idPage]))
			$this->getDatasPage($idPage, $this->renderMode);
		        
		
		// There's no page in DB
		if (empty($this->datasPages[$idPage]))
			return '';
		
		$router = $this->serviceManager->get('router');
		$request = $this->serviceManager->get('request');
		$routeMatch = $router->match($request);
		
		$idversion = null;
		if (!empty($routeMatch))
		  $idversion = $routeMatch->getParam('idversion');
		
		$value = '';
		if ($this->renderMode == 'front' || !empty($idversion))
		{
		    // Front mode, nothing to do but to send back the text
			$xmlPage = $this->datasPages[$idPage]->page_content;
			$value = $this->getPageTagValue($xmlPage, $tagId);
		}
		else
		{
			// Melis mode
			// Check if there's an already modified content in session
			$container = new Container('meliscms');
			
			if (!empty($container['content-pages'][$idPage][$tagId]))
				$value = $container['content-pages'][$idPage][$tagId];
			else
			{
				// Else, we send back the saved page if exist, the published one otherwise
				if (!empty($this->datasPagesSaved[$idPage]->page_content))
					$xmlPage = $this->datasPagesSaved[$idPage]->page_content;
				else
					$xmlPage = $this->datasPages[$idPage]->page_content;
				
				$value = $this->getPageTagValue($xmlPage, $tagId);
			}
		}
		
		// If BO and value is empty, let's put default one so that the template looks like something
		if ($value == '' && $this->renderMode == 'melis')
			$value = $defaultValue;
		
		$finalValue = $value;

		
		if ($this->renderMode == 'front' )
			$finalValue = $value;
		else
		{
		    // BO, we add the classes and div nessecary to render TInyMCE
		    $routeMatch = $this->serviceManager->get('Application')->getMvcEvent()->getRouteMatch();
		    $idpageLoaded = $routeMatch->getParam('idpage');
		     
			if (!$this->preview && $idpageLoaded == $idPage)
			{
				$data_tag_type = " data-tag-type='$type'";
				$data_tag_id = " data-tag-id='$tagId'";
				$data_id_page = " data-id-page='$idPage'";
			
				$cornerType = '';
				$finalValue = "<div class='$type-editable melis-editable' $data_tag_type $data_tag_id $data_id_page>" . $cornerType . $value . '</div>';
			}
			else
			{
				$finalValue = $value;
			}
			
		}
			
		return $finalValue;
	}
}