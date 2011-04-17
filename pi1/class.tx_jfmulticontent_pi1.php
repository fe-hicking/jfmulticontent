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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

if (t3lib_extMgm::isLoaded('t3jquery')) {
	require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
}


/**
 * Plugin 'Multiple Content' for the 'jfmulticontent' extension.
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_jfmulticontent
 */
class tx_jfmulticontent_pi1 extends tslib_pibase
{
	public $prefixId      = 'tx_jfmulticontent_pi1';
	public $scriptRelPath = 'pi1/class.tx_jfmulticontent_pi1.php';
	public $extKey        = 'jfmulticontent';
	public $pi_checkCHash = true;
	public $conf = array();
	private $lConf = array();
	private $confArr = array();
	private $templateFile = null;
	private $templateFileJS = null;
	private $templatePart = null;
	private $additionalMarker = array();
	private $contentKey = null;
	private $contentCount = null;
	private $contentClass = array();
	private $classes = array();
	private $contentWrap = array();
	private $jsFiles = array();
	private $js = array();
	private $cssFiles = array();
	private $css = array();
	private $titles = array();
	private $attributes = array();
	private $cElements = array();
	private $content_id = array();

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf)
	{
		$this->content = $content;
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		// get the config from EXT
		$this->confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
		// Plugin or template?
		if ($this->cObj->data['list_type'] == $this->extKey.'_pi1') {
			// It's a content, all data from flexform
			// Set the Flexform information
			$this->pi_initPIflexForm();
			$piFlexForm = $this->cObj->data['pi_flexform'];
			foreach ($piFlexForm['data'] as $sheet => $data) {
				foreach ($data as $lang => $value) {
					foreach ($value as $key => $val) {
						if (! isset($this->lConf[$key])) {
							$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
						}
					}
				}
			}

			// Override the config with flexform data
			$this->conf['config.']['style'] = $this->lConf['style'];
			if ($this->cObj->data['tx_jfmulticontent_view']) {
				$this->conf['config.']['view'] = $this->cObj->data['tx_jfmulticontent_view'];
			} else {
				$this->conf['config.']['view'] = 'content';
			}
			// columns
			$this->conf['config.']['column1']     = $this->lConf['column1'];
			$this->conf['config.']['column2']     = $this->lConf['column2'];
			$this->conf['config.']['column3']     = $this->lConf['column3'];
			$this->conf['config.']['column4']     = $this->lConf['column4'];
			$this->conf['config.']['column5']     = $this->lConf['column5'];
			$this->conf['config.']['columnOrder'] = $this->lConf['columnOrder'];
			if ($this->lConf['equalize'] < 2) {
				$this->conf['config.']['equalize'] = $this->lConf['equalize'];
			}
			// tab
			if ($this->lConf['tabCollapsible'] < 2) {
				$this->conf['config.']['tabCollapsible'] = $this->lConf['tabCollapsible'];
			}
			if ($this->lConf['tabOpen'] > 0) {
				$this->conf['config.']['tabOpen'] = $this->lConf['tabOpen'];
			}
			if ($this->lConf['tabRandomContent'] < 2) {
				$this->conf['config.']['tabRandomContent'] = $this->lConf['tabRandomContent'];
			}
			if (strlen($this->lConf['tabCookieExpires']) > 0) {
				$this->conf['config.']['tabCookieExpires'] = $this->lConf['tabCookieExpires'];
			}
			if ($this->lConf['tabFxHeight'] < 2) {
				$this->conf['config.']['tabFxHeight'] = $this->lConf['tabFxHeight'];
			}
			if ($this->lConf['tabFxOpacity'] < 2) {
				$this->conf['config.']['tabFxOpacity'] = $this->lConf['tabFxOpacity'];
			}
			if ($this->lConf['tabFxDuration'] > 0) {
				$this->conf['config.']['tabFxDuration'] = $this->lConf['tabFxDuration'];
			}
			// accordion
			if ($this->lConf['accordionAutoHeight'] < 2) {
				$this->conf['config.']['accordionAutoHeight'] = $this->lConf['accordionAutoHeight'];
			}
			if ($this->lConf['accordionCollapsible'] < 2) {
				$this->conf['config.']['accordionCollapsible'] = $this->lConf['accordionCollapsible'];
			}
			if ($this->lConf['accordionClosed'] < 2) {
				$this->conf['config.']['accordionClosed'] = $this->lConf['accordionClosed'];
			}
			if (is_numeric($this->lConf['accordionOpen'])) {
				$this->conf['config.']['accordionOpen'] = $this->lConf['accordionOpen'];
			}
			if ($this->lConf['accordionRandomContent'] < 2) {
				$this->conf['config.']['accordionRandomContent'] = $this->lConf['accordionRandomContent'];
			}
			if ($this->lConf['accordionEvent']) {
				$this->conf['config.']['accordionEvent'] = $this->lConf['accordionEvent'];
			}
			if ($this->lConf['accordionAnimated']) {
				$this->conf['config.']['accordionAnimated'] = $this->lConf['accordionAnimated'];
			}
			if ($this->lConf['accordionTransition']) {
				$this->conf['config.']['accordionTransition'] = $this->lConf['accordionTransition'];
			}
			if ($this->lConf['accordionTransitiondir']) {
				$this->conf['config.']['accordionTransitiondir'] = $this->lConf['accordionTransitiondir'];
			}
			if ($this->lConf['accordionTransitionduration'] > 0) {
				$this->conf['config.']['accordionTransitionduration'] = $this->lConf['accordionTransitionduration'];
			}
			// slider
			if ($this->lConf['sliderWidth']) {
				$this->conf['config.']['sliderWidth'] = $this->lConf['sliderWidth'];
			}
			if ($this->lConf['sliderHeight']) {
				$this->conf['config.']['sliderHeight'] = $this->lConf['sliderHeight'];
			}
			if ($this->lConf['sliderResizeContents'] < 2) {
				$this->conf['config.']['sliderResizeContents'] = $this->lConf['sliderResizeContents'];
			}
			if ($this->lConf['sliderTheme']) {
				$this->conf['config.']['sliderTheme'] = $this->lConf['sliderTheme'];
			}
			if ($this->lConf['sliderOpen'] > 0) {
				$this->conf['config.']['sliderOpen'] = $this->lConf['sliderOpen'];
			}
			if ($this->lConf['sliderRandomContent'] < 2) {
				$this->conf['config.']['sliderRandomContent'] = $this->lConf['sliderRandomContent'];
			}
			if ($this->lConf['sliderHashTags'] < 2) {
				$this->conf['config.']['sliderHashTags'] = $this->lConf['sliderHashTags'];
			}
			if ($this->lConf['sliderBuildArrows'] < 2) {
				$this->conf['config.']['sliderBuildArrows'] = $this->lConf['sliderBuildArrows'];
			}
			if ($this->lConf['sliderToggleArrows'] < 2) {
				$this->conf['config.']['sliderToggleArrows'] = $this->lConf['sliderToggleArrows'];
			}
			if ($this->lConf['sliderNavigation'] < 2) {
				$this->conf['config.']['sliderNavigation'] = $this->lConf['sliderNavigation'];
			}
			if ($this->lConf['sliderPanelFromHeader'] < 2) {
				$this->conf['config.']['sliderPanelFromHeader'] = $this->lConf['sliderPanelFromHeader'];
			}
			if ($this->lConf['sliderToggleControls'] < 2) {
				$this->conf['config.']['sliderToggleControls'] = $this->lConf['sliderToggleControls'];
			}
			if ($this->lConf['sliderAutoStart'] < 2) {
				$this->conf['config.']['sliderAutoStart'] = $this->lConf['sliderAutoStart'];
			}
			if ($this->lConf['sliderPauseOnHover'] < 2) {
				$this->conf['config.']['sliderPauseOnHover'] = $this->lConf['sliderPauseOnHover'];
			}
			if ($this->lConf['sliderResumeOnVideoEnd'] < 2) {
				$this->conf['config.']['sliderResumeOnVideoEnd'] = $this->lConf['sliderResumeOnVideoEnd'];
			}
			if ($this->lConf['sliderStopAtEnd'] < 2) {
				$this->conf['config.']['sliderStopAtEnd'] = $this->lConf['sliderStopAtEnd'];
			}
			if ($this->lConf['sliderPlayRtl'] < 2) {
				$this->conf['config.']['sliderPlayRtl'] = $this->lConf['sliderPlayRtl'];
			}
			if ($this->lConf['sliderTransition']) {
				$this->conf['config.']['sliderTransition'] = $this->lConf['sliderTransition'];
			}
			if ($this->lConf['sliderTransitiondir']) {
				$this->conf['config.']['sliderTransitiondir'] = $this->lConf['sliderTransitiondir'];
			}
			if ($this->lConf['sliderTransitionduration'] > 0) {
				$this->conf['config.']['sliderTransitionduration'] = $this->lConf['sliderTransitionduration'];
			}
			// slidedeck
			if ($this->lConf['slidedeckHeight'] > 0) {
				$this->conf['config.']['slidedeckHeight'] = $this->lConf['slidedeckHeight'];
			}
			if ($this->lConf['slidedeckTransition']) {
				$this->conf['config.']['slidedeckTransition'] = $this->lConf['slidedeckTransition'];
			}
			if ($this->lConf['slidedeckTransitiondir']) {
				$this->conf['config.']['slidedeckTransitiondir'] = $this->lConf['slidedeckTransitiondir'];
			}
			if ($this->lConf['slidedeckTransitionduration'] > 0) {
				$this->conf['config.']['slidedeckTransitionduration'] = $this->lConf['slidedeckTransitionduration'];
			}
			if ($this->lConf['slidedeckStart'] > 0) {
				$this->conf['config.']['slidedeckStart'] = $this->lConf['slidedeckStart'];
			}
			if ($this->lConf['slidedeckActivecorner'] < 2) {
				$this->conf['config.']['slidedeckActivecorner'] = $this->lConf['slidedeckActivecorner'];
			}
			if ($this->lConf['slidedeckIndex'] < 2) {
				$this->conf['config.']['slidedeckIndex'] = $this->lConf['slidedeckIndex'];
			}
			if ($this->lConf['slidedeckScroll'] < 2) {
				$this->conf['config.']['slidedeckScroll'] = $this->lConf['slidedeckScroll'];
			}
			if ($this->lConf['slidedeckKeys'] < 2) {
				$this->conf['config.']['slidedeckKeys'] = $this->lConf['slidedeckKeys'];
			}
			if ($this->lConf['slidedeckHidespines'] < 2) {
				$this->conf['config.']['slidedeckHidespines'] = $this->lConf['slidedeckHidespines'];
			}
			// easyAccordion
			if ($this->lConf['easyaccordionSkin']) {
				$this->conf['config.']['easyaccordionSkin'] = $this->lConf['easyaccordionSkin'];
			}
			if ($this->lConf['easyaccordionOpen'] > 0) {
				$this->conf['config.']['easyaccordionOpen'] = $this->lConf['easyaccordionOpen'];
			}
			if ($this->lConf['easyaccordionWidth'] > 0) {
				$this->conf['config.']['easyaccordionWidth'] = $this->lConf['easyaccordionWidth'];
			}
			if ($this->lConf['easyaccordionSlideNum'] < 2) {
				$this->conf['config.']['easyaccordionSlideNum'] = $this->lConf['easyaccordionSlideNum'];
			}
			// TODO: Add the opened content...
			// booklet
			if ($this->lConf['bookletWidth'] > 0) {
				$this->conf['config.']['bookletWidth'] = $this->lConf['bookletWidth'];
			}
			if ($this->lConf['bookletHeight'] > 0) {
				$this->conf['config.']['bookletHeight'] = $this->lConf['bookletHeight'];
			}
			if ($this->lConf['bookletSpeed'] > 0) {
				$this->conf['config.']['bookletSpeed'] = $this->lConf['bookletSpeed'];
			}
			if ($this->lConf['bookletStartingPage'] > 0) {
				$this->conf['config.']['bookletStartingPage'] = $this->lConf['bookletStartingPage'];
			}
			if ($this->lConf['bookletRTL'] < 2) {
				$this->conf['config.']['bookletRTL'] = $this->lConf['bookletRTL'];
			}
			if ($this->lConf['bookletTransition']) {
				$this->conf['config.']['bookletTransition']    = $this->lConf['bookletTransition'];
			}
			if ($this->lConf['bookletTransitiondir']) {
				$this->conf['config.']['bookletTransitiondir'] = $this->lConf['bookletTransitiondir'];
			}
			if ($this->lConf['bookletPagePadding'] != '') {
				$this->conf['config.']['bookletPagePadding'] = $this->lConf['bookletPagePadding'];
			}
			if ($this->lConf['bookletPageNumbers'] < 2) {
				$this->conf['config.']['bookletPageNumbers'] = $this->lConf['bookletPageNumbers'];
			}
			if ($this->lConf['bookletShadows'] < 2) {
				$this->conf['config.']['bookletShadows'] = $this->lConf['bookletShadows'];
			}
			if ($this->lConf['bookletClosed'] < 2) {
				$this->conf['config.']['bookletClosed'] = $this->lConf['bookletClosed'];
			}
			if ($this->lConf['bookletCovers'] < 2) {
				$this->conf['config.']['bookletCovers'] = $this->lConf['bookletCovers'];
			}
			if ($this->lConf['bookletHash'] < 2) {
				$this->conf['config.']['bookletHash'] = $this->lConf['bookletHash'];
			}
			if ($this->lConf['bookletKeyboard'] < 2) {
				$this->conf['config.']['bookletKeyboard'] = $this->lConf['bookletKeyboard'];
			}
			// TODO: This option dos not work correct!
			// if ($this->lConf['bookletKeyboard'] < 2) {
			// 	$this->conf['config.']['bookletOverlays']    = $this->lConf['bookletOverlays'];
			// }
			if ($this->lConf['bookletArrows'] < 2) {
				$this->conf['config.']['bookletArrows'] = $this->lConf['bookletArrows'];
			}
			if ($this->lConf['bookletHovers'] < 2) {
				$this->conf['config.']['bookletHovers'] = $this->lConf['bookletHovers'];
			}
			// autoplay
			if ($this->lConf['delayDuration'] > 0) {
				$this->conf['config.']['delayDuration'] = $this->lConf['delayDuration'];
			}
			if ($this->lConf['autoplayContinuing'] < 2) {
				$this->conf['config.']['autoplayContinuing'] = $this->lConf['autoplayContinuing'];
			}
			if ($this->lConf['autoplayCycle'] < 2) {
				$this->conf['config.']['autoplayCycle'] = $this->lConf['autoplayCycle'];
			}
			// define the titles to overwrite
			if (trim($this->lConf['titles'])) {
				$this->titles = t3lib_div::trimExplode(chr(10), $this->lConf['titles']);
			}
			// define the attributes
			if (trim($this->lConf['attributes'])) {
				$this->attributes = t3lib_div::trimExplode(chr(10), $this->lConf['attributes']);
			}
			// options
			$this->conf['config.']['options']         = $this->lConf['options'];
			$this->conf['config.']['optionsOverride'] = $this->lConf['optionsOverride'];

			$view = $this->conf['views.'][$this->conf['config.']['view'].'.'];

			if ($this->conf['config.']['view'] == 'page') {
				// get the page ID's
				$page_ids = t3lib_div::trimExplode(",", $this->cObj->data['tx_jfmulticontent_pages']);
				// get the informations for every page
				for ($a=0; $a < count($page_ids); $a++) {
					$row = null;
					if ($GLOBALS['TSFE']->sys_language_content) {
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages_language_overlay', 'pid='.intval($page_ids[$a]).' AND sys_language_uid='.$GLOBALS['TSFE']->sys_language_content, '', '', 1);
						$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}
					if (! is_array($row)) {
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', 'uid='.intval($page_ids[$a]), '', '', 1);
						$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}
					if (is_array($row)) {
						foreach ($row as $key => $val) {
							$GLOBALS['TSFE']->register['page_'.$key] = $val;
						}
					}
					$GLOBALS['TSFE']->register['pid'] = $page_ids[$a];
					if ($this->titles[$a] == '' || !isset($this->titles[$a])) {
						$this->titles[$a] = $this->cObj->cObjGetSingle($view['title'], $view['title.']);
					}
					$this->cElements[] = $this->cObj->cObjGetSingle($view['content'], $view['content.']);
					$this->content_id[$a] = $page_ids[$a];
				}
			} else if ($this->conf['config.']['view'] == 'content') {
				// get the content ID's
				$content_ids = t3lib_div::trimExplode(",", $this->cObj->data['tx_jfmulticontent_contents']);
				// get the informations for every content
				for ($a=0; $a < count($content_ids); $a++) {
					// Select the content
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_content', 'uid='.intval($content_ids[$a]), '', '', 1);
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					if ($GLOBALS['TSFE']->sys_language_content) {
						$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tt_content', $row, $GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL);
					}
					$GLOBALS['TSFE']->register['uid'] = ($row['_LOCALIZED_UID'] ? $row['_LOCALIZED_UID'] : $row['uid']);
					$GLOBALS['TSFE']->register['title'] = (strlen(trim($this->titles[$a])) > 0 ? $this->titles[$a] : $row['header']);
					if ($this->titles[$a] == '' || !isset($this->titles[$a])) {
						$this->titles[$a] = $this->cObj->cObjGetSingle($view['title'], $view['title.']);
						$GLOBALS['TSFE']->register['title'] = $this->titles[$a];
					}
					$this->cElements[] = $this->cObj->cObjGetSingle($view['content'], $view['content.']);
					$this->content_id[$a] = $content_ids[$a];
				}
			}
			// HOOK for additional views
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['jfmulticontent']['getViews'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['jfmulticontent']['getViews'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					if ($this->conf['config.']['view'] == $_procObj->getIdentifier()) {
						$_procObj->main($this->content, $this->conf, $this);
						$this->titles = $_procObj->getTitles();
						$this->cElements = $_procObj->getElements();
						$this->content_id = $_procObj->getIds();
					}
				}
			}
			// define the key of the element
			$this->setContentKey('jfmulticontent_c' . $this->cObj->data['uid']);
		} else {
			// TS config will be used
			// define the key of the element
			if ($this->conf['config.']['contentKey']) {
				$this->setContentKey($this->conf['config.']['contentKey']);
			} else {
				$this->setContentKey('jfmulticontent_ts1');
			}
			// Render the contents
			if (count($this->conf['contents.']) > 0) {
				foreach ($this->conf['contents.'] as $key => $contents) {
					$title = trim($this->cObj->cObjGetSingle($contents['title'], $contents['title.']));
					$content = trim($this->cObj->cObjGetSingle($contents['content'], $contents['content.']));
					if ($content) {
						$this->titles[] = $title;
						$this->cElements[] = $content;
						$this->content_id[] = $this->cObj->stdWrap($contents['id'], $contents['id.']);
					}
				}
			}
		}
		$this->contentCount = count($this->cElements);
		// return false, if there is no element
		if ($this->contentCount == 0) {
			return false;
		}

		// The template
		if (! $this->templateFile = $this->cObj->fileResource($this->conf['templateFile'])) {
			$this->templateFile = $this->cObj->fileResource("EXT:jfmulticontent/res/tx_jfmulticontent_pi1.tmpl");
		}
		// The template for JS
		if (! $this->templateFileJS = $this->cObj->fileResource($this->conf['templateFileJS'])) {
			$this->templateFileJS = $this->cObj->fileResource("EXT:jfmulticontent/res/tx_jfmulticontent_pi1.js");
		}


		// add the CSS file
		$this->addCssFile($this->conf['cssFile']);

		// define the jQuery mode and function
		if ($this->conf['jQueryNoConflict']) {
			$jQueryNoConflict = "jQuery.noConflict();";
		} else {
			$jQueryNoConflict = "";
		}

		// style
		switch ($this->conf['config.']['style']) {
			case "2column" : {
				$this->templatePart = "TEMPLATE_COLUMNS";
				$this->contentCount = 2;
				$this->classes = array(
					$this->conf['config.']["column1"],
					$this->conf['config.']["column2"],
				);
				$this->contentClass = t3lib_div::trimExplode("|*|", $this->conf['2columnClasses']);
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['columnWrap.']['wrap']);
				break;
			}
			case "3column" : {
				$this->templatePart = "TEMPLATE_COLUMNS";
				$this->contentCount = 3;
				$this->classes = array(
					$this->conf['config.']["column1"],
					$this->conf['config.']["column2"],
					$this->conf['config.']["column3"],
				);
				$this->contentClass = t3lib_div::trimExplode("|*|", $this->conf['3columnClasses']);
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['columnWrap.']['wrap']);
				break;
			}
			case "4column" : {
				$this->templatePart = "TEMPLATE_COLUMNS";
				$this->contentCount = 4;
				$this->classes = array(
					$this->conf['config.']["column1"],
					$this->conf['config.']["column2"],
					$this->conf['config.']["column3"],
					$this->conf['config.']["column4"],
				);
				$this->contentClass = t3lib_div::trimExplode("|*|", $this->conf['4columnClasses']);
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['columnWrap.']['wrap']);
				break;
			}
			case "5column" : {
				$this->templatePart = "TEMPLATE_COLUMNS";
				$this->contentCount = 5;
				$this->classes = array(
					$this->conf['config.']["column1"],
					$this->conf['config.']["column2"],
					$this->conf['config.']["column3"],
					$this->conf['config.']["column4"],
					$this->conf['config.']["column5"],
				);
				$this->contentClass = t3lib_div::trimExplode("|*|", $this->conf['5columnClasses']);
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['columnWrap.']['wrap']);
				break;
			}
			case "tab" : {
				// jQuery Tabs
				$this->templatePart = "TEMPLATE_TAB";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['tabWrap.']['wrap']);
				// the id attribute is not permitted in tabs-style
				if (count($this->attributes) > 0) {
					foreach ($this->attributes as $key => $attribute) {
						if (preg_match("/id=[\"|\'](.*?)[\"|\']/i", $attribute, $preg)) {
							$this->attributes[$key] = trim(str_replace($preg[0], "", $attribute));
						}
					}
				}
				$this->addJS($jQueryNoConflict);
				$fx = array();
				if ($this->conf['config.']['tabFxHeight']) {
					$fx[] = "height: 'toggle'";
				}
				if ($this->conf['config.']['tabFxOpacity']) {
					$fx[] = "opacity: 'toggle'";
				}
				if ($this->conf['config.']['tabFxDuration']) {
					$fx[] = "duration: '{$this->conf['config.']['tabFxDuration']}'";
				}
				if ($this->conf['config.']['delayDuration'] > 0) {
					$rotate = ".tabs('rotate' , {$this->conf['config.']['delayDuration']}, ".($this->conf['config.']['autoplayContinuing'] ? 'true' : 'false').")";
				}
				$options = array();
				if (count($fx) > 0) {
					$options['fx'] = "fx:{".implode(",", $fx)."}";
				}
				if ($this->conf['config.']['tabCollapsible']) {
					$options['collapsible'] = "collapsible: true";
				}
				if ($this->conf['config.']['tabRandomContent']) {
					$options['selected'] = "selected:Math.floor(Math.random()*{$this->contentCount})";
				} elseif (is_numeric($this->conf['config.']['tabOpen'])) {
					$options['selected'] = "selected: ".($this->conf['config.']['tabOpen'] - 1);
				}
				// overwrite all options if set
				if (trim($this->conf['config.']['options'])) {
					if ($this->conf['config.']['optionsOverride']) {
						$options = array($this->conf['config.']['options']);
					} else {
						$options['options'] = $this->conf['config.']['options'];
					}
				}
				// Add Cookies script, if cookie is active
				if ($this->conf['config.']['tabCookieExpires'] > 0) {
					$this->addJsFile($this->conf['jQueryCookies']);
					unset($options['selected']);
					$options['cookie'] = "cookie: { expires: ".$this->conf['config.']['tabCookieExpires']." }";
				}
				// get the Template of the Javascript
				$markerArray = array();
				// get the template
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_TAB_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_TAB_JS is missing", true);
				}
				// Fix the href problem (config.prefixLocalAnchors = all)
				if ($GLOBALS['TSFE']->config['config']['prefixLocalAnchors']) {
					$fixTabHref = trim($this->cObj->getSubpart($templateCode, "###FIX_HREF###"));
				} else {
					$fixTabHref = null;
				}
				$templateCode = trim($this->cObj->substituteSubpart($templateCode, '###FIX_HREF###', $fixTabHref, 0));
				// Replace default values
				$markerArray["KEY"] = $this->getContentKey();
				$markerArray["PREG_QUOTE_KEY"] = preg_quote($this->getContentKey(), "/");
				$markerArray["OPTIONS"] = implode(", ", $options);
				$markerArray["ROTATE"] = $rotate;
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);
				// Add all CSS and JS files
				if (T3JQUERY === true) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], true);
					$this->addJsFile($this->conf['jQueryUI']);
				}
				$this->addCssFile($this->conf['jQueryUIstyle']);
				$this->addJS($templateCode);
				break;
			}
			case "accordion" : {
				// jQuery Accordion
				$this->templatePart = "TEMPLATE_ACCORDION";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['accordionWrap.']['wrap']);
				$this->addJS($jQueryNoConflict);
				$options = array();
				if ($this->conf['config.']['accordionAutoHeight'] < 1) {
					$options['autoHeight'] = "autoHeight:false";
				}
				if ($this->conf['config.']['accordionCollapsible']) {
					$options['collapsible'] = "collapsible:true";
				}
				if ($this->conf['config.']['accordionClosed']) {
					$options['active'] = "active:false";
					$options['collapsible'] = "collapsible:true";
				} elseif ($this->conf['config.']['accordionRandomContent']) {
					$options['active'] = "active:Math.floor(Math.random()*{$this->contentCount})";
				} elseif (is_numeric($this->conf['config.']['accordionOpen'])) {
					$options['active'] = "active:".($this->conf['config.']['accordionOpen'] - 1);
				}
				if ($this->conf['config.']['accordionEvent']) {
					$options['event'] = "event:'{$this->conf['config.']['accordionEvent']}'";
				}
				// get the Template of the Javascript
				$markerArray = array();
				$markerArray["KEY"]            = $this->getContentKey();
				$markerArray["CONTENT_COUNT"]  = $this->contentCount;
				$markerArray["EASING"]         = (in_array($this->conf['config.']['accordionTransition'], array("swing", "linear")) ? "" : "ease".$this->conf['config.']['accordionTransitiondir'].$this->conf['config.']['accordionTransition']);
				$markerArray["TRANS_DURATION"] = (is_numeric($this->conf['config.']['accordionTransitionduration']) ? $this->conf['config.']['accordionTransitionduration'] : 1000);
				$markerArray["DELAY_DURATION"] = (is_numeric($this->conf['config.']['delayDuration']) ? $this->conf['config.']['delayDuration'] : '0');
				// get the template for the Javascript
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_ACCORDION_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_ACCORDION_JS is missing", true);
				}
				$easingAnimation = null;
				if ($this->conf['config.']['accordionTransition']) {
					$options['animated'] = "animated:'{$this->getContentKey()}'";
					$easingAnimation = trim($this->cObj->getSubpart($templateCode, "###EASING_ANIMATION###"));
				} else if ($this->conf['config.']['accordionAnimated']) {
					$options['animated'] = "animated:'{$this->conf['config.']['accordionAnimated']}'";
				}
				// set the easing animation script
				$templateCode = $this->cObj->substituteSubpart($templateCode, '###EASING_ANIMATION###', $easingAnimation, 0);
				$continuing = null;
				$autoPlay = null;
				$settimeout = null;
				if ($this->conf['config.']['delayDuration'] > 0) {
					// does not work if (! $this->conf['config.']['autoplayContinuing']) {}
					$continuing = trim($this->cObj->getSubpart($templateCode, "###CONTINUING###"));
					$autoPlay   = trim($this->cObj->getSubpart($templateCode, "###AUTO_PLAY###"));
					$settimeout = trim($this->cObj->getSubpart($templateCode, "###SETTIMEOUT###"));
					$settimeout = $this->cObj->substituteMarkerArray($settimeout, $markerArray, '###|###', 0);
					$options['change'] = "change:function(event,ui){{$settimeout}}";
				}
				$templateCode = $this->cObj->substituteSubpart($templateCode, '###CONTINUING###', $continuing, 0);
				$templateCode = $this->cObj->substituteSubpart($templateCode, '###AUTO_PLAY###',  $autoPlay, 0);
				$templateCode = $this->cObj->substituteSubpart($templateCode, '###SETTIMEOUT###', $settimeout, 0);
				// overwrite all options if set
				if (trim($this->conf['config.']['options'])) {
					if ($this->conf['config.']['optionsOverride']) {
						$options = array($this->conf['config.']['options']);
					} else {
						$options['flexform'] = $this->conf['config.']['options'];
					}
				}

				// Replace default values
				$markerArray["OPTIONS"] = implode(", ", $options);
				// Replace all markers
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);

				// Add all CSS and JS files
				if (T3JQUERY === true) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], true);
					$this->addJsFile($this->conf['jQueryEasing']);
					$this->addJsFile($this->conf['jQueryUI']);
				}
				$this->addCssFile($this->conf['jQueryUIstyle']);
				$this->addJS(trim($templateCode));
				break;
			}
			case "slider" : {
				// anythingslider
				$this->templatePart = "TEMPLATE_SLIDER";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['sliderWrap.']['wrap']);
				$this->addJS($jQueryNoConflict);
				// 
				if ($this->conf['config.']['sliderTransition']) {
					$options[] = "easing: '".(in_array($this->conf['config.']['sliderTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['sliderTransitiondir']}")."{$this->conf['config.']['sliderTransition']}'";
				}
				if ($this->conf['config.']['sliderTransitionduration'] > 0) {
					$options[] = "animationTime: {$this->conf['config.']['sliderTransitionduration']}";
				}
				if ($this->conf['config.']['delayDuration'] > 0) {
					$options[] = "autoPlay: true";
					$options[] = "delay: {$this->conf['config.']['delayDuration']}";
					$options[] = "startStopped: ".($this->conf['config.']['sliderAutoStart'] ? 'false' : 'true');
					$options[] = "stopAtEnd: ".($this->conf['config.']['sliderStopAtEnd'] ? 'true' : 'false');
				} else {
					$options[] = "autoPlay: false";
					// Toggle only if not autoplay
					$options[] = "toggleArrows: ".($this->conf['config.']['sliderToggleArrows'] ? 'true' : 'false');
					$options[] = "toggleControls: ".($this->conf['config.']['sliderToggleControls'] ? 'true' : 'false');
				}
				if ($this->conf['config.']['sliderWidth']) {
					$options[] = "width: '".t3lib_div::slashJS($this->conf['config.']['sliderWidth'])."'";
				}
				if ($this->conf['config.']['sliderHeight']) {
					$options[] = "height: '".t3lib_div::slashJS($this->conf['config.']['sliderHeight'])."'";
				}
				if ($this->conf['config.']['sliderResizeContents']) {
					$options[] = "resizeContents: true";
				}
				if ($this->conf['config.']['sliderTheme']) {
					$options[] = "theme: '".t3lib_div::slashJS($this->conf['config.']['sliderTheme'])."'";
					if (substr($this->confArr['anythingSliderThemeFolder'], 0, 4) === 'EXT:') {
						list($extKey, $local) = explode('/', substr($this->confArr['anythingSliderThemeFolder'], 4), 2);
						$anythingSliderThemeFolder = t3lib_extMgm::siteRelPath($extKey) . $local;
					} else {
						$anythingSliderThemeFolder = $this->confArr['anythingSliderThemeFolder'];
					}
					$options[] = "themeDirectory: '".t3lib_div::slashJS($anythingSliderThemeFolder)."{themeName}/style.css'";
				}
				$options[] = "buildArrows: ".($this->conf['config.']['sliderBuildArrows'] ? 'true' : 'false');
				$options[] = "resumeOnVideoEnd: ".($this->conf['config.']['sliderResumeOnVideoEnd'] ? 'true' : 'false');
				$options[] = "playRtl: ".($this->conf['config.']['sliderPlayRtl'] ? 'true' : 'false');
				$options[] = "hashTags: ".($this->conf['config.']['sliderHashTags'] ? 'true' : 'false');
				$options[] = "pauseOnHover: ".($this->conf['config.']['sliderPauseOnHover'] ? 'true' : 'false');
				$options[] = "buildNavigation: ".($this->conf['config.']['sliderNavigation'] ? 'true' : 'false');
				$options[] = "startText: '".t3lib_div::slashJS($this->pi_getLL('slider_start'))."'";
				$options[] = "stopText: '".t3lib_div::slashJS($this->pi_getLL('slider_stop'))."'";
				if ($this->pi_getLL('slider_forward')) {
					$options[] = "forwardText: '".t3lib_div::slashJS($this->pi_getLL('slider_forward'))."'";
				}
				if ($this->pi_getLL('slider_back')) {
					$options[] = "backText: '".t3lib_div::slashJS($this->pi_getLL('slider_back'))."'";
				}
				// define the paneltext
				if ($this->conf['config.']['sliderPanelFromHeader']) {
					$tab = array();
					for ($a=0; $a < $this->contentCount; $a++) {
						$tab[] = "if(i==".($a+1).") return ".t3lib_div::quoteJSvalue($this->titles[$a]).";";
					}
					$options[] = "navigationFormatter: function(i,p){\n			".implode("\n			", $tab)."\n		}";
				} elseif (trim($this->pi_getLL('slider_panel'))) {
					$options[] = "navigationFormatter: function(i,p){ var str = '".(t3lib_div::slashJS($this->pi_getLL('slider_panel')))."'; return str.replace('%i%',i); }";
				}
				if ($this->conf['config.']['sliderRandomContent']) {
					$options[] = "startPanel: Math.floor(Math.random()*".($this->contentCount + 1).")";
				} elseif ($this->conf['config.']['sliderOpen'] > 1) {
					$options[] = "startPanel: ".($this->conf['config.']['sliderOpen'] < $this->contentCount ? $this->conf['config.']['sliderOpen'] : $this->contentCount);
				}
				// overwrite all options if set
				if (trim($this->conf['config.']['options'])) {
					if ($this->conf['config.']['optionsOverride']) {
						$options = array($this->conf['config.']['options']);
					} else {
						$options[] = $this->conf['config.']['options'];
					}
				}
				// get the Template of the Javascript
				$markerArray = array();
				// get the template
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_SLIDER_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_SLIDER_JS is missing", true);
				}
				// Replace default values
				$markerArray["KEY"] = $this->getContentKey();
				$markerArray["OPTIONS"] = implode(", ", $options);
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);
				// Fix the href problem (config.prefixLocalAnchors = all)
				if ($GLOBALS['TSFE']->config['config']['prefixLocalAnchors']) {
					$fixTabHref = trim($this->cObj->getSubpart($templateCode, "###FIX_HREF###"));
				} else {
					$fixTabHref = null;
				}
				$templateCode = trim($this->cObj->substituteSubpart($templateCode, '###FIX_HREF###', $fixTabHref, 0));
				// Add all CSS and JS files
				if (T3JQUERY === true) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], true);
					$this->addJsFile($this->conf['jQueryEasing']);
				}
				$this->addJsFile($this->conf['sliderJS']);
				$this->addCssFile($this->conf['sliderCSS']);
				$this->addJS($templateCode);
				break;
			}
			case "slidedeck" : {
				// SlideDeck
				$this->templatePart = "TEMPLATE_SLIDEDECK";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['slidedeckWrap.']['wrap']);
				$this->addJS($jQueryNoConflict);
				$options = array();
				if ($this->conf['config.']['slidedeckTransitionduration']) {
					$options['speed'] = "speed: {$this->conf['config.']['slidedeckTransitionduration']}";
				}
				if ($this->conf['config.']['slidedeckTransition']) {
					$options['transition'] = "transition: '".(in_array($this->conf['config.']['slidedeckTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['slidedeckTransitiondir']}")."{$this->conf['config.']['slidedeckTransition']}'";
				}
				if ($this->conf['config.']['slidedeckStart']) {
					$options['start'] = "start: {$this->conf['config.']['slidedeckStart']}";
				}
				$options['activeCorner'] = "activeCorner: ".($this->conf['config.']['slidedeckActivecorner'] ? 'true' : 'false');
				$options['index']        = "index: ".($this->conf['config.']['slidedeckIndex'] ? 'true' : 'false');
				$options['scroll']       = "scroll: ".($this->conf['config.']['slidedeckScroll'] ? 'true' : 'false');
				$options['keys']         = "keys: ".($this->conf['config.']['slidedeckKeys'] ? 'true' : 'false');
				$options['hideSpines']   = "hideSpines: ".($this->conf['config.']['slidedeckHidespines'] ? 'true' : 'false');
				if ($this->conf['config.']['delayDuration'] > 0) {
					$options['autoPlay']         = "autoPlay: true";
					$options['autoPlayInterval'] = "autoPlayInterval: {$this->conf['config.']['delayDuration']}";
					$options['cycle']            = "cycle: ".($this->conf['config.']['autoplayCycle'] ? 'true' : 'false');
				}
				// overwrite all options if set
				if (trim($this->conf['config.']['options'])) {
					if ($this->conf['config.']['optionsOverride']) {
						$options = array($this->conf['config.']['options']);
					} else {
						$options['flexform'] = $this->conf['config.']['options'];
					}
				}

				// get the template for the Javascript
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_SLIDEDECK_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_SLIDEDECK_JS is missing", true);
				}
				// Replace default values
				$markerArray = array();
				$markerArray["KEY"]     = $this->getContentKey();
				$markerArray["HEIGHT"]  = ($this->conf['config.']['slidedeckHeight'] > 0 ? $this->conf['config.']['slidedeckHeight'] : 300);
				$markerArray["OPTIONS"] = implode(", ", $options);
				// Replace all markers
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);

				// Add all CSS and JS files
				if (T3JQUERY === true) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], true);
					$this->addJsFile($this->conf['jQueryEasing']);
				}
				$this->addJsFile($this->conf['slidedeckJS']);
				$this->addCssFile($this->conf['slidedeckCSS']);
				if ($this->conf['config.']['slidedeckScroll']) {
					$this->addJsFile($this->conf['jQueryMouseWheel']);
				}
				$this->addJS(trim($templateCode));
				break;

			}
			case "easyaccordion" : {
				// easyaccordion
				$this->templatePart = "TEMPLATE_EASYACCORDION";
				$this->additionalMarker["SKIN"] = $this->conf['config.']['easyaccordionSkin'];
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['easyaccordionWrap.']['wrap']);
				$this->addJS($jQueryNoConflict);
				$options = array();
				if ($this->conf['config.']['delayDuration'] > 0) {
					$options['autoStart']     = "autoStart: true";
					$options['slideInterval'] = "slideInterval: {$this->conf['config.']['delayDuration']}";
				}
				$options['slideNum'] = "slideNum: ".($this->conf['config.']['easyaccordionSlideNum'] ? 'true' : 'false');
				// overwrite all options if set
				if (trim($this->conf['config.']['options'])) {
					if ($this->conf['config.']['optionsOverride']) {
						$options = array($this->conf['config.']['options']);
					} else {
						$options['flexform'] = $this->conf['config.']['options'];
					}
				}

				// get the template for the Javascript
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_EASYACCORDION_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_EASYACCORDION_JS is missing", true);
				}
				// Replace default values
				$markerArray = array();
				$markerArray["KEY"]     = $this->getContentKey();
				$markerArray["WIDTH"]   = ($this->conf['config.']['easyaccordionWidth'] > 0  ? $this->conf['config.']['easyaccordionWidth']  : 600);
				$markerArray["OPTIONS"] = implode(", ", $options);
				// Replace all markers
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);
				
				// Add all CSS and JS files
				if (T3JQUERY === true) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], true);
				}
				$this->addJsFile($this->conf['easyaccordionJS']);
				$this->addCssFile($this->conf['easyaccordionCSS']);
				$this->addCssFile($this->confArr['easyAccordionSkinFolder'] . $this->conf['config.']['easyaccordionSkin'] . "/style.css");
				$this->addJS(trim($templateCode));
				break;
			}
			case "booklet" : {
				// easyaccordion
				$this->templatePart = "TEMPLATE_BOOKLET";
				$this->contentWrap = t3lib_div::trimExplode("|*|", $this->conf['bookletWrap.']['wrap']);
				$this->addJS($jQueryNoConflict);
				$options = array();
				if (is_numeric($this->conf['config.']['bookletWidth'])) {
					$options['width'] = "width: ".$this->conf['config.']['bookletWidth'];
				}
				if (is_numeric($this->conf['config.']['bookletHeight'])) {
					$options['height'] = "height: ".$this->conf['config.']['bookletHeight'];
				}
				if (is_numeric($this->conf['config.']['bookletSpeed'])) {
					$options['speed'] = "speed: ".$this->conf['config.']['bookletSpeed'];
				}
				if (is_numeric($this->conf['config.']['bookletStartingPage'])) {
					$options['startingPage'] = "startingPage: ".$this->conf['config.']['bookletStartingPage'];
				}
				if ($this->conf['config.']['bookletRTL']) {
					$options['direction'] = "direction: 'RTL'";
				}
				if ($this->conf['config.']['bookletTransition']) {
					$options['transition'] = "easing: '".(in_array($this->conf['config.']['bookletTransition'], array("swing", "linear")) ? "" : "ease{$this->conf['config.']['bookletTransitiondir']}")."{$this->conf['config.']['bookletTransition']}'";
				}
				if (is_numeric($this->conf['config.']['bookletPagePadding'])) {
					$options['pagePadding'] = "pagePadding: ".$this->conf['config.']['bookletPagePadding'];
				}
				$options['pageNumbers'] = "pageNumbers: ".($this->conf['config.']['bookletPageNumbers'] ? 'true' : 'false');
				$options['shadows']     = "shadows: ".($this->conf['config.']['bookletShadows'] ? 'true' : 'false');
				$options['closed']      = "closed: ".($this->conf['config.']['bookletClosed'] ? 'true' : 'false');
				$options['covers']      = "covers: ".($this->conf['config.']['bookletCovers'] ? 'true' : 'false');
				$options['hash']        = "hash: ".($this->conf['config.']['bookletHash'] ? 'true' : 'false');
				$options['keyboard']    = "keyboard: ".($this->conf['config.']['bookletKeyboard'] ? 'true' : 'false');
				// $options['overlays']    = "overlays: ".($this->conf['config.']['bookletOverlays'] ? 'true' : 'false');
				$options['arrows']      = "arrows: ".($this->conf['config.']['bookletArrows'] ? 'true' : 'false');
				$options['hovers']      = "hovers: ".($this->conf['config.']['bookletHovers'] ? 'true' : 'false');
				// overwrite all options if set
				if (trim($this->conf['config.']['options'])) {
					if ($this->conf['config.']['optionsOverride']) {
						$options = array($this->conf['config.']['options']);
					} else {
						$options['flexform'] = $this->conf['config.']['options'];
					}
				}
				// get the template for the Javascript
				if (! $templateCode = trim($this->cObj->getSubpart($this->templateFileJS, "###TEMPLATE_BOOKLET_JS###"))) {
					$templateCode = $this->outputError("Template TEMPLATE_BOOKLET_JS is missing", true);
				}
				// Replace default values
				$markerArray = array();
				$markerArray["KEY"]     = $this->getContentKey();
				$markerArray["OPTIONS"] = implode(", ", $options);
				// Replace all markers
				$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);
				
				// Add all CSS and JS files
				if (T3JQUERY === true) {
					tx_t3jquery::addJqJS();
				} else {
					$this->addJsFile($this->conf['jQueryLibrary'], true);
					$this->addJsFile($this->conf['jQueryEasing']);
				}
				$this->addJsFile($this->conf['bookletJS']);
				$this->addCssFile($this->conf['bookletCSS']);
				$this->addJS(trim($templateCode));
				break;
			}
			default: {
				return $this->outputError("NO VALID TEMPLATE SELECTED", false);
			}
		}

		// Add the ressources
		$this->addResources();

		// Render the Template
		$content = $this->renderTemplate();

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Set the contentKey
	 * @param string $contentKey
	 */
	public function setContentKey($contentKey=null)
	{
		$this->contentKey = ($contentKey == null ? $this->extKey : $contentKey);
	}

	/**
	 * Get the contentKey
	 * @return string
	 */
	public function getContentKey()
	{
		return $this->contentKey;
	}

	/**
	 * Render the template with the defined contents
	 * 
	 * @return string
	 */
	public function renderTemplate()
	{
		$markerArray = $this->additionalMarker;
		// get the template
		if (! $templateCode = $this->cObj->getSubpart($this->templateFile, "###{$this->templatePart}###")) {
			$templateCode = $this->outputError("Template {$this->templatePart} is missing", false);
		}
		// Replace default values
		$markerArray["KEY"] = $this->getContentKey();
		// replace equalizeClass
		if ($this->conf['config.']['equalize']) {
			$markerArray["EQUALIZE_CLASS"] = ' '.$this->cObj->stdWrap($this->conf['equalizeClass'], $this->conf['equalizeClass.']);
		} else {
			$markerArray["EQUALIZE_CLASS"] = '';
		}
		$templateCode = $this->cObj->substituteMarkerArray($templateCode, $markerArray, '###|###', 0);
		// Get the title template
		$titleCode = $this->cObj->getSubpart($templateCode, "###TITLES###");
		// Get the column template
		$columnCode = $this->cObj->getSubpart($templateCode, "###COLUMNS###");
		// Define the contentWrap
		switch (count($this->contentWrap)) {
			case 1 : {
				$contentWrap_array = array(
					$this->contentWrap[0],
					$this->contentWrap[0],
					$this->contentWrap[0],
				);
				break;
			}
			case 2 : {
				$contentWrap_array = array(
					$this->contentWrap[0],
					$this->contentWrap[0],
					$this->contentWrap[1],
				);
				break;
			}
			case 3 : {
				$contentWrap_array = $this->contentWrap;
				break;
			}
			default: {
				$contentWrap_array = array(
					null,
					null,
					null
				);
				break;
			}
		}
		if ($this->conf['config.']['easyaccordionOpen'] > $this->contentCount) {
			$this->conf['config.']['easyaccordionOpen'] = $this->contentCount;
		}
		// fetch all contents
		for ($a=0; $a < $this->contentCount; $a++) {
			$markerArray = array();
			// get the attribute if exist
			$markerArray["ATTRIBUTE"] = "";
			if ($this->attributes[$a] != '') {
				$markerArray["ATTRIBUTE"] .= ' ' . $this->attributes[$a];
			}
			// if the attribute does not have a class entry, the class will be wraped for yaml (c33l, c33l, c33r)
			if ($this->classes[$a] && isset($this->contentClass[$a]) && ! preg_match("/class\=/i", $markerArray["ATTRIBUTE"])) {
				// wrap the class
				$markerArray["ATTRIBUTE"] .= $this->cObj->stdWrap($this->classes[$a], array("wrap" => ' class="'.$this->contentClass[$a].'"', "required" => 1));
			}
			// Set the active class for the active slide
			if (($a+1) ==  $this->conf['config.']['easyaccordionOpen']) {
				$markerArray["EASYACCORDION_ACTIVE"] = 'class="active"';
			} else {
				$markerArray["EASYACCORDION_ACTIVE"] = '';
			}
			
			// render the content
			$markerArray["CONTENT_ID"] = $this->content_id[$a];
			$markerArray["ID"]         = $a+1;
			$markerArray["TITLE"]      = null;
			// Title will be selected if not COLUMNS (TAB, ACCORDION and SLIDER)
			if ($this->templatePart != "TEMPLATE_COLUMNS") {
				// overwrite the title if set in $this->titles
				$markerArray["TITLE"] = $this->titles[$a];
			}
			// define the used wrap
			if ($a == 0) {
				$wrap = $contentWrap_array[0];
			} elseif (($a+1) == $this->contentCount) {
				$wrap = $contentWrap_array[2];
			} else {
				$wrap = $contentWrap_array[1];
			}
			// override the CONTENT
			if ($this->templatePart == "TEMPLATE_COLUMNS" && $this->conf['config.']['columnOrder']) {
				switch ($this->conf['config.']['columnOrder']) {
					case 1 : {
						// left to right, top to down
						foreach ($this->cElements as $key => $cElements) {
							$test = ($key - $a) / $this->contentCount;
							if (intval($test) == $test) {
								$markerArray["CONTENT"] .= $this->cObj->stdWrap($this->cElements[$key], array('wrap' => $wrap));
							}
						}
						break;
					}
					case 2 : {
						// right to left, top to down
						foreach ($this->cElements as $key => $cElements) {
							$test = ($key - ($this->contentCount - ($a + 1))) / $this->contentCount;
							if (intval($test) == $test) {
								$markerArray["CONTENT"] .= $this->cObj->stdWrap($this->cElements[$key], array('wrap' => $wrap));
							}
						}
						break;
					}
					case 3 : {
						// top to down, left to right
						
						break;
					}
					case 4 : {
						// top to down, right to left
						
						break;
					}
				}
			} else {
				// wrap the content
				$markerArray["CONTENT"] = $this->cObj->stdWrap($this->cElements[$a], array('wrap' => $wrap));
			}
			if ($markerArray["CONTENT"]) {
				// add content to COLUMNS
				$columns .= $this->cObj->substituteMarkerArray($columnCode, $markerArray, '###|###', 0);
				// add content to TITLE
				$titles .= $this->cObj->substituteMarkerArray($titleCode, $markerArray, '###|###', 0);
			}
		}
		$return_string = $templateCode;
		$return_string = $this->cObj->substituteSubpart($return_string, '###TITLES###', $titles, 0);
		$return_string = $this->cObj->substituteSubpart($return_string, '###COLUMNS###', $columns, 0);

		if (isset($this->conf['additionalMarkers'])) {
			$additonalMarkerArray = array();
			// get additional markers
			$additionalMarkers = t3lib_div::trimExplode(',', $this->conf['additionalMarkers']);
			// get additional marker configuration
			if(count($additionalMarkers) > 0) {
				foreach($additionalMarkers as $additonalMarker) {
					$additonalMarkerArray[strtoupper($additonalMarker)] = $this->cObj->cObjGetSingle($this->conf['additionalMarkerConf.'][$additonalMarker], $this->conf['additionalMarkerConf.'][$additonalMarker.'.']);
				}
			}
			// add addtional marker content to template
			$return_string = $this->cObj->substituteMarkerArray($return_string, $additonalMarkerArray, '###|###', 0);
		}

		return $return_string;
	}

	/**
	 * Include all defined resources (JS / CSS)
	 *
	 * @return void
	 */
	public function addResources()
	{
		if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
			$pagerender = $GLOBALS['TSFE']->getPageRenderer();
		}
		// Fix moveJsFromHeaderToFooter (add all scripts to the footer)
		if ($GLOBALS['TSFE']->config['config']['moveJsFromHeaderToFooter']) {
			$allJsInFooter = true;
		} else {
			$allJsInFooter = false;
		}
		// add all defined JS files
		if (count($this->jsFiles) > 0) {
			foreach ($this->jsFiles as $jsToLoad) {
				if (T3JQUERY === true) {
					$conf = array(
						'jsfile' => $jsToLoad,
						'tofooter' => ($this->conf['jsInFooter'] || $allJsInFooter),
						'jsminify' => $this->conf['jsMinify'],
					);
					tx_t3jquery::addJS('', $conf);
				} else {
					$file = $this->getPath($jsToLoad);
					if ($file) {
						if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
							if ($allJsInFooter) {
								$pagerender->addJsFooterFile($file, 'text/javascript', $this->conf['jsMinify']);
							} else {
								$pagerender->addJsFile($file, 'text/javascript', $this->conf['jsMinify']);
							}
						} else {
							$temp_file = '<script type="text/javascript" src="'.$file.'"></script>';
							if ($allJsInFooter) {
								$GLOBALS['TSFE']->additionalFooterData['jsFile_'.$this->extKey.'_'.$file] = $temp_file;
							} else {
								$GLOBALS['TSFE']->additionalHeaderData['jsFile_'.$this->extKey.'_'.$file] = $temp_file;
							}
						}
					} else {
						t3lib_div::devLog("'{$jsToLoad}' does not exists!", $this->extKey, 2);
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
			if (T3JQUERY === true && t3lib_div::int_from_ver($this->getExtensionVersion('t3jquery')) >= 1002000) {
				$conf['tofooter'] = ($this->conf['jsInFooter'] || $allJsInFooter);
				$conf['jsminify'] = $this->conf['jsMinify'];
				$conf['jsinline'] = $this->conf['jsInline'];
				tx_t3jquery::addJS('', $conf);
			} else {
				// Add script only once
				$hash = md5($temp_js);
				if ($this->conf['jsInline']) {
					$GLOBALS['TSFE']->inlineJS[$hash] = $temp_css;
				} elseif (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
					if ($this->conf['jsInFooter'] || $allJsInFooter) {
						$pagerender->addJsFooterInlineCode($hash, $temp_js, $this->conf['jsMinify']);
					} else {
						$pagerender->addJsInlineCode($hash, $temp_js, $this->conf['jsMinify']);
					}
				} else {
					if ($this->conf['jsMinify']) {
						$temp_js = t3lib_div::minifyJavaScript($temp_js);
					}
					if ($this->conf['jsInFooter'] || $allJsInFooter) {
						$GLOBALS['TSFE']->additionalFooterData['js_'.$this->extKey.'_'.$hash] = t3lib_div::wrapJS($temp_js, true);
					} else {
						$GLOBALS['TSFE']->additionalHeaderData['js_'.$this->extKey.'_'.$hash] = t3lib_div::wrapJS($temp_js, true);
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
					if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
						$pagerender->addCssFile($file, 'stylesheet', 'all', '', $this->conf['cssMinify']);
					} else {
						$GLOBALS['TSFE']->additionalHeaderData['cssFile_'.$this->extKey.'_'.$file] = '<link rel="stylesheet" type="text/css" href="'.$file.'" media="all" />'.chr(10);
					}
				} else {
					t3lib_div::devLog("'{$cssToLoad}' does not exists!", $this->extKey, 2);
				}
			}
		}
		// add all defined CSS Script
		if (count($this->css) > 0) {
			foreach ($this->css as $cssToPut) {
				$temp_css .= $cssToPut;
			}
			$hash = md5($temp_css);
			if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
				$pagerender->addCssInlineBlock($hash, $temp_css, $this->conf['cssMinify']);
			} else {
				// addCssInlineBlock
				$GLOBALS['TSFE']->additionalCSS['css_'.$this->extKey.'_'.$hash] .= $temp_css;
			}
		}
	}

	/**
	 * Return the webbased path
	 * 
	 * @param string $path
	 * return string
	 */
	private function getPath($path="")
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
	private function addJsFile($script="", $first=false)
	{
		if ($this->getPath($script) && ! in_array($script, $this->jsFiles)) {
			if ($first === true) {
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
	private function addJS($script="")
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
	private function addCssFile($script="")
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
	private function addCSS($script="")
	{
		if (! in_array($script, $this->css)) {
			$this->css[] = $script;
		}
	}

	/**
	 * Returns the version of an extension (in 4.4 its possible to this with t3lib_extMgm::getExtensionVersion)
	 * @param string $key
	 * @return string
	 */
	private function getExtensionVersion($key)
	{
		if (! t3lib_extMgm::isLoaded($key)) {
			return '';
		}
		$_EXTKEY = $key;
		include(t3lib_extMgm::extPath($key) . 'ext_emconf.php');
		return $EM_CONF[$key]['version'];
	}

	/**
	 * Return a errormessage if needed
	 * @param string $msg
	 * @param boolean $js
	 * @return string
	 */
	private function outputError($msg='', $js=false)
	{
		t3lib_div::devLog($msg, $this->extKey, 3);
		if ($this->confArr['frontendErrorMsg'] || ! isset($this->confArr['frontendErrorMsg'])) {
			return ($js ? "alert(".t3lib_div::quoteJSvalue($msg).")" : "<p>{$msg}</p>");
			
		} else {
			return null;
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/pi1/class.tx_jfmulticontent_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/pi1/class.tx_jfmulticontent_pi1.php']);
}
?>