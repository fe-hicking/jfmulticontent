<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Juergen Furrer <juergen.furrer@gmail.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * 'tx_jfmulticontent_ttnews_extend' for the 'jfmulticontent' extension.
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_jfmulticontent
 */
class tx_jfmulticontent_ttnews_extend
{
	var $conf = array();
	var $cObj = NULL;
	var $extKey = 'jfmulticontent';
	var $jsFiles = array();
	var $js = array();
	var $cssFiles = array();
	var $css = array();
	var $piFlexForm = array();

	public function extraCodesProcessor($newsObject)
	{
		$content = NULL;
		$this->cObj = $newsObject->cObj;
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jfmulticontent_pi1.'];
		switch ($newsObject->theCode) {
			case 'LIST_ACCORDION': {
				$content .= $newsObject->displayList();
				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], TRUE);
					$this->addJsFile($this->conf['jQueryEasing']);
					$this->addJsFile($this->conf['jQueryUI']);
				}
				$this->addCssFile($this->conf['jQueryUIstyle']);
				$this->addResources();
				break;
			}
			case 'LIST_SLIDER': {
				$content .= $newsObject->displayList();
				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], TRUE);
					$this->addJsFile($this->conf['jQueryEasing']);
				}
				$this->addJsFile($this->conf['sliderJS']);
				$this->addCssFile($this->conf['sliderCSS']);
				$this->addResources();
				break;
			}
			case 'LIST_SLIDEDECK': {
				$content .= $newsObject->displayList();
				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], TRUE);
					$this->addJsFile($this->conf['jQueryEasing']);
				}
				$this->addJsFile($this->conf['slidedeckJS']);
				$this->addCssFile($this->conf['slidedeckCSS']);
				$this->addJsFile($this->conf['jQueryMouseWheel']);
				$this->addResources();
				break;
			}
			case 'LIST_EASYACCORDION': {
				$content .= $newsObject->displayList();
				// Add all CSS and JS files
				if (T3JQUERY === TRUE) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], TRUE);
				}
				$this->addJsFile($this->conf['easyaccordionJS']);
				$this->addCssFile($this->conf['easyaccordionCSS']);
				$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
				$this->addCssFile($confArr['easyAccordionSkinFolder'] . $this->conf['config.']['easyaccordionSkin'] . "/style.css");
				$this->addResources();
				break;
			}
		}
		return $content;
	}

	/**
	 * Return additional markers for tt_news
	 * @param $markerArray
	 * @param $row
	 * @param $conf
	 * @param $pObj
	 * @return array
	 */
	public function extraGlobalMarkerProcessor(&$pObj, $markerArray)
	{
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jfmulticontent_pi1.'];
		$markerArray['###EASY_ACCORDION_SKIN###'] = $conf['config.']['easyaccordionSkin'];

		return $markerArray;
	}

	/**
	 * Include all defined resources (JS / CSS)
	 *
	 * @return void
	 */
	public function addResources()
	{
        $pagerender = GeneralUtility::makeInstance(PageRenderer::class) ;
		// Fix moveJsFromHeaderToFooter (add all scripts to the footer)
		if ($GLOBALS['TSFE']->config['config']['moveJsFromHeaderToFooter']) {
			$allJsInFooter = TRUE;
		} else {
			$allJsInFooter = FALSE;
		}
		// add all defined JS files
		if (count($this->jsFiles) > 0) {
			foreach ($this->jsFiles as $jsToLoad) {
				if (T3JQUERY === TRUE) {
					$conf = array(
						'jsfile' => $jsToLoad,
						'tofooter' => ($this->conf['jsInFooter'] || $allJsInFooter),
						'jsminify' => $this->conf['jsMinify'],
					);
					tx_t3jquery::addJS('', $conf);
				} else {
					$file = $this->getPath($jsToLoad);
					if ($file) {
                        if ($this->conf['jsInFooter'] || $allJsInFooter) {
                            $pagerender->addJsFooterFile($file, 'text/javascript', $this->conf['jsMinify']);
                        } else {
                            $pagerender->addJsFile($file, 'text/javascript', $this->conf['jsMinify']);
                        }
					} else {
						GeneralUtility::devLog("'{$jsToLoad}' does not exist!", $this->extKey, 2);
					}
				}
			}
		}
		// add all defined JS script
		if (count($this->js) > 0) {
			foreach ($this->js as $jsToPut) {
				$temp_js .= $jsToPut;
			}
			$conf = array();
			$conf['jsdata'] = $temp_js;
			if (T3JQUERY === TRUE && class_exists(\TYPO3\CMS\Core\Utility\VersionNumberUtility) && \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger($this->getExtensionVersion('t3jquery')) >= 1002000) {
				$conf['tofooter'] = ($this->conf['jsInFooter'] || $allJsInFooter);
				$conf['jsminify'] = $this->conf['jsMinify'];
				$conf['jsinline'] = $this->conf['jsInline'];
				tx_t3jquery::addJS('', $conf);
			} else {
				// Add script only once
				$hash = md5($temp_js);
				if ($this->conf['jsInline']) {
					$GLOBALS['TSFE']->inlineJS[$hash] = $temp_js;
				} else {
					if ($this->conf['jsInFooter'] || $allJsInFooter) {
						$pagerender->addJsFooterInlineCode($hash, $temp_js, $this->conf['jsMinify']);
					} else {
						$pagerender->addJsInlineCode($hash, $temp_js, $this->conf['jsMinify']);
					}
				}
			}
		}
		// add all defined CSS files
		if (count($this->cssFiles) > 0) {
			foreach ($this->cssFiles as $cssToLoad) {
				// Add script only once
				$file = $this->getPath($cssToLoad);
				if ($file) {
                    $pagerender->addCssFile($file, 'stylesheet', 'all', '', $this->conf['cssMinify']);
				} else {
					GeneralUtility::devLog("'{$cssToLoad}' does not exist!", $this->extKey, 2);
				}
			}
		}
		// add all defined CSS Script
		if (count($this->css) > 0) {
			foreach ($this->css as $cssToPut) {
				$temp_css .= $cssToPut;
			}
			$hash = md5($temp_css);
            $pagerender->addCssInlineBlock($hash, $temp_css, $this->conf['cssMinify']);
		}
	}

	/**
	 * Return the webbased path
	 * 
	 * @param string $path
	 * return string
	 */
	public function getPath($path="")
	{
		return $GLOBALS['TSFE']->tmpl->getFileName($path);
	}

	/**
	 * Add additional JS file
	 * 
	 * @param string $script
	 * @param boolean $first
	 * @return void
	 */
	public function addJsFile($script="", $first=FALSE)
	{
		if ($this->getPath($script) && ! in_array($script, $this->jsFiles)) {
			if ($first === TRUE) {
				$this->jsFiles = array_merge(array($script), $this->jsFiles);
			} else {
				$this->jsFiles[] = $script;
			}
		}
	}

	/**
	 * Add JS to header
	 * 
	 * @param string $script
	 * @return void
	 */
	public function addJS($script="")
	{
		if (! in_array($script, $this->js)) {
			$this->js[] = $script;
		}
	}

	/**
	 * Add additional CSS file
	 * 
	 * @param string $script
	 * @return void
	 */
	public function addCssFile($script="")
	{
		if ($this->getPath($script) && ! in_array($script, $this->cssFiles)) {
			$this->cssFiles[] = $script;
		}
	}

	/**
	 * Add CSS to header
	 * 
	 * @param string $script
	 * @return void
	 */
	public function addCSS($script="")
	{
		if (! in_array($script, $this->css)) {
			$this->css[] = $script;
		}
	}

	/**
	 * Returns the version of an extension (in 4.4 its possible to this with \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getExtensionVersion)
	 * @param string $key
	 * @return string
	 */
	public function getExtensionVersion($key)
	{
		if (! \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($key)) {
			return '';
		}
		$_EXTKEY = $key;
		include(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($key) . 'ext_emconf.php');
		return $EM_CONF[$key]['version'];
	}

	/**
	* Set the piFlexform data
	*
	* @return void
	*/
	protected function setFlexFormData()
	{
		if (! count($this->piFlexForm)) {
			$this->pi_initPIflexForm();
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
		}
	}

	/**
	 * Extract the requested information from flexform
	 * @param string $sheet
	 * @param string $name
	 * @param boolean $devlog
	 * @return string
	 */
	protected function getFlexformData($sheet='', $name='', $devlog=TRUE)
	{
		$this->setFlexFormData();
		if (! isset($this->piFlexForm['data'])) {
			if ($devlog === TRUE) {
				GeneralUtility::devLog("Flexform Data not set", $this->extKey, 1);
			}
			return NULL;
		}
		if (! isset($this->piFlexForm['data'][$sheet])) {
			if ($devlog === TRUE) {
				GeneralUtility::devLog("Flexform sheet '{$sheet}' not defined", $this->extKey, 1);
			}
			return NULL;
		}
		if (! isset($this->piFlexForm['data'][$sheet]['lDEF'][$name])) {
			if ($devlog === TRUE) {
				GeneralUtility::devLog("Flexform Data [{$sheet}][{$name}] does not exist", $this->extKey, 1);
			}
			return NULL;
		}
		if (isset($this->piFlexForm['data'][$sheet]['lDEF'][$name]['vDEF'])) {
			return $this->pi_getFFvalue($this->piFlexForm, $name, $sheet);
		} else {
			return $this->piFlexForm['data'][$sheet]['lDEF'][$name];
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_ttnews_extend.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_ttnews_extend.php']);
}
?>
