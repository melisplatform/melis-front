<?php

namespace MelisFront\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;

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
	
	private function getDatasPage($idPage)
	{
		$melisPage = $this->serviceManager->get('MelisEnginePage');
		
		if (empty($this->datasPages[$idPage]))
		{
			$datasPage = $melisPage->getDatasPage($idPage, 'published');
			$this->datasPages[$idPage] = $datasPage->getMelisPageTree();
		}
		if (empty($this->datasPagesSaved[$idPage]) && $this->renderMode == 'melis')
		{
			$datasPageSaved = $melisPage->getDatasPage($idPage, 'saved');
			$this->datasPagesSaved[$idPage] = $datasPageSaved->getMelisPageTree();
		}
	}
	
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
		
		$idversion = $routeMatch->getParam('idversion');
		
		$value = '';
		if ($this->renderMode == 'front' || !empty($idversion))
		{
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
		
		if ($value == '' && $this->renderMode == 'melis')
			$value = $defaultValue;
		
		$finalValue = $value;
		
		if ($this->renderMode == 'front' )
			$finalValue = $value;
		else
		{
			if (!$this->preview)
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