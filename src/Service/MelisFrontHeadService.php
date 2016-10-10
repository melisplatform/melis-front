<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MelisEngine\Model\MelisPage;
use Zend\Filter\HtmlEntities;

/**
 * This class updates the Meta datas description and title
 * by replacing the one existing (or not) by the one define in Melis SEO Page system
 *
 */
class MelisFrontHeadService implements MelisFrontHeadServiceInterface, ServiceLocatorAwareInterface
{
	protected $serviceLocator;
	
	public function setServiceLocator(ServiceLocatorInterface $sl)
	{
		$this->serviceLocator = $sl;
		return $this;
	}
	
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}	
	
	/**
	 * Updates the title and description
	 * 
	 * @param int $idPage Id of page asked
	 * @param strig $contentGenerated Content to be changed
	 * 
	 */
	public function updateTitleAndDescription($idPage, $contentGenerated)
	{
		$newContent = $contentGenerated;
		
		// Get the page title
		$melisPage = $this->serviceLocator->get('MelisEnginePage');
		$datasPage = $melisPage->getDatasPage($idPage, 'published');
		if (!empty($datasPage))
		{
			$pageTree = $datasPage->getMelisPageTree();
			if (!empty($pageTree))
			{
				/**
				 * Get SEO for this page
				 */
				$melisTablePageSeo = $this->serviceLocator->get('MelisEngineTablePageSeo');
				$datasPageSeo = $melisTablePageSeo->getEntryById($idPage);
				
				/**
				 * Description tag
				 */
				if (!empty($datasPageSeo))
				{
					$datasPageSeo = $datasPageSeo->current();
					if (!empty($datasPageSeo))
					{
						$descriptionPage = addslashes($datasPageSeo->pseo_meta_description);
						
						if ($descriptionPage != '')
						{
							$descriptionTag = "\n<meta name='description' content='$descriptionPage' />\n";
							$descriptionRegex = '/(<meta[^>]*name=[\"\']description[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*>)/i';
							preg_match($descriptionRegex, $contentGenerated, $descriptions);
							
							if (!empty($descriptions))
							{
								// Replace existing title in source with the page name
								$newContent = preg_replace($descriptionRegex, $descriptionTag, $contentGenerated);
							}
							else
							{
								// Title doesn't exist, look for head tag to add
								// if no head tag, then nothing will happen
								$headRegex = '/(<head[^>]*>)/im';
								$newContent = preg_replace($headRegex, "$1$descriptionTag", $contentGenerated);
							}
							
							$contentGenerated = $newContent;
						}
					}
				}


				/**
				 * Title tag
				 */
				$titlePage = $pageTree->page_name;
				if (!empty($datasPageSeo) && !empty($datasPageSeo->pseo_meta_title))
					$titlePage = $datasPageSeo->pseo_meta_title;
				
				$titleRegex = '/(<title[^>]*>)([^<]+)(<\/title>)/im';
				preg_match($titleRegex, $contentGenerated, $titles);
				if (!empty($titles))
				{
					// Replace existing title in source with the page name
					$newContent = preg_replace($titleRegex, "$1$titlePage$3", $contentGenerated);
				}
				else
				{
					// Title doesn't exist, look for head tag to add
					// if no head tag, then nothing will happen
					$headRegex = '/(<head[^>]*>)/im';
					$titleTag = "\n<title>$titlePage</title>\n";
					$newContent = preg_replace($headRegex, "$1$titleTag", $contentGenerated);
				}
				
			}
		}
		
		return $newContent;
	}
}