<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;

use MelisCore\Service\MelisServiceManager;
use MelisEngine\Model\MelisPage;
use Laminas\Filter\HtmlEntities;

/**
 * This class updates the Meta datas description and title
 * by replacing the one existing (or not) by the one define in Melis SEO Page system
 *
 */
class MelisFrontHeadService extends MelisServiceManager implements MelisFrontHeadServiceInterface
{
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
		$melisPage = $this->getServiceManager()->get('MelisEnginePage');
		$datasPage = $melisPage->getDatasPage($idPage, 'published');
		if (!empty($datasPage))
		{
			$pageTree = $datasPage->getMelisPageTree();
			if (!empty($pageTree))
			{
				/**
				 * Get SEO for this page
				 */
				$pageSeoSrv = $this->getServiceManager()->get('MelisEngineSEOService');
				$datasPageSeo = $pageSeoSrv->getSEOById($idPage);
				/**
				 * Description tag
				 */
				if (!empty($datasPageSeo = $datasPageSeo->current()))
				{
					if (!empty($datasPageSeo)) {
						$descriptionPage = addslashes($datasPageSeo->pseo_meta_description);
						$descriptionPage = str_replace("\'", "'", $descriptionPage);

						$titlePage = addslashes($datasPageSeo->pseo_meta_title);
						$titlePage = str_replace("\'", "'", $titlePage);

						if ($descriptionPage != '') {
							$descriptionTag = "\n\t<meta name=\"description\" content=\"$descriptionPage\" />\n";
							$titleTag = "<title>$titlePage</title>";
							$descriptionRegex = '/(<meta[^>]*name=[\"\']description[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*>)/i';
							$titleReg = '/\<title\>+(.*?)+\<\/title>/';

							preg_match($descriptionRegex, $contentGenerated, $descriptions);
							preg_match($titleReg, $contentGenerated, $titles);
							if (!empty($descriptions)) {
								// Replace existing title in source with the page name
								$newContent = preg_replace($descriptionRegex, $descriptionTag, $contentGenerated,1);
							} else {
								// Title doesn't exist, look for head tag to add
								// if no head tag, then nothing will happen
								$headRegex = '/(<head[^>]*>)/im';
								$newContent = preg_replace($headRegex, "$1$descriptionTag", $contentGenerated,1);
							}

							if (!empty($titles)) {
								// Replace existing title in source with the page name
								$newContent = preg_replace($titleReg, $titleTag, $newContent);
							} else {
								// Title doesn't exist, look for head tag to add
								// if no head tag, then nothing will happen
								$headRegex = '/(<head[^>]*>)/im';
								$newContent = preg_replace($headRegex, "$1$titleTag", $newContent,1);
							}

							$contentGenerated = $newContent;

						}
					}
					/**
					 * Canonical Tag
					 */
					$canonicalUrl = addslashes($datasPageSeo->pseo_canonical);
					$canonicalUrl = str_replace("\'", "'", $canonicalUrl);
					if ($canonicalUrl != '') {
						$canonicalUrlTag = "\n\t<link rel=\"canonical\" href=\"$canonicalUrl\" />\n";
						$canonicalRegex = '/(<link[^>]*rel=[\"\']canonical[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*>)/i';
						preg_match($canonicalRegex, $contentGenerated, $canonicalFound);
						if(!empty($canonicalFound)){
							$newContent = preg_replace($canonicalRegex, $canonicalUrlTag, $contentGenerated,1);
						}else {
							$headRegex = '/(<head[^>]*>)/im';
							$newContent = preg_replace($headRegex, "$1$canonicalUrlTag", $contentGenerated,1);
						}
						$contentGenerated = $newContent;
					}else{
						/**
						 * @var MelisTreeService
						 */
						$pageService = $this->getServiceManager()->get('MelisEngineTree');
						$pageUrl = $pageService->getPageLink($idPage);
						$canonicalUrlTag = "\n\t<link rel=\"canonical\" href=\"$pageUrl\" />\n";
						$canonicalRegex = '/(<link[^>]*rel=[\"\']canonical[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*>)/i';
						preg_match($canonicalRegex, $contentGenerated, $canonicalFound);
						if(!empty($canonicalFound)){
							$newContent = preg_replace($canonicalRegex, $canonicalUrlTag, $contentGenerated ,1);
						}else {
							$headRegex = '/(<head[^>]*>)/im';
							$newContent = preg_replace($headRegex, "$1$canonicalUrlTag", $contentGenerated,1);
						}
						$contentGenerated = $newContent;
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
					// $newContent = preg_replace($titleRegex, "$1$titlePage$3", $contentGenerated,1);
					// Page title starts with digit issue fixed
					$newContent = preg_replace($titleRegex, "<title>$titlePage</title>", $contentGenerated,1);
				}
				else
				{
					// Title doesn't exist, look for head tag to add
					// if no head tag, then nothing will happen
					$headRegex = '/(<head[^>]*>)/im';
					$titleTag = "\n<title>$titlePage</title>\n";
					$newContent = preg_replace($headRegex, "$1$titleTag", $contentGenerated, 1);
				}
				
			}
		}

		//add generator meta tag
        $generatorTag = "\n\t<meta name=\"generator\" content=\"Melis Platform\">\n";
        $headRegex = '/(<\/title>)/im';
        $newContent = preg_replace($headRegex, "$1$generatorTag", $newContent);

		return $newContent;
	}
	
	public function updatePluginsRessources($content)
	{
		$newContent = $content;
		
		// Auto adding plugins CSS and JS files to layout
		if ($this->getServiceManager()->get('templating_plugins')->hasItem('plugins_front'))
		{
			$files = $this->getServiceManager()->get('templating_plugins')->getItem('plugins_front');
			
			$cssHtmlToAdd = "\n";
			foreach ($files['css'] as $css)
				$cssHtmlToAdd .= '<link href="' . $css . '" media="screen" rel="stylesheet" type="text/css">' . "\n";

			$headRegex = '/(<\/head[^>]*>)/im';
			$newContent = preg_replace($headRegex, "$cssHtmlToAdd$1", $newContent);
			
			$jsHtmlToAdd = "\n";
			foreach ($files['js'] as $js)
				$jsHtmlToAdd .= '<script type="text/javascript" src="' . $js . '"></script>' . "\n";

			$bodyRegex = '/(<\/body[^>]*>)/im';
			$newContent = preg_replace($bodyRegex, "$jsHtmlToAdd$1", $newContent);
		}
		
		return $newContent;
	}
}