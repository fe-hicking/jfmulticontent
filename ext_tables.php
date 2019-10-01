<?php
defined('TYPO3_MODE') || die('Access denied.');

// get extension configuration
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);

if (
    TYPO3_MODE == 'BE'
) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        JFMULTICONTENT_EXT,
        'Configuration/TypoScript/PluginSetup/',
        'Multi content'
    );
}

