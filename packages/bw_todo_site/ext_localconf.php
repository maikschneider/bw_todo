<?php

defined('TYPO3_MODE') || die('Access denied.');

(static function () {

    // add TypoScript setup and constants
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'bw_todo_site',
        'setup',
        "@import 'EXT:bw_todo_site/Configuration/TypoScript/setup.typoscript'"
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'bw_todo_site',
        'constants',
        "@import 'EXT:bw_todo_site/Configuration/TypoScript/constants.typoscript'"
    );

    // add PageTS
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:bw_todo_site/Configuration/TSconfig/Page.tsconfig'"
    );

    // register FE-Plugins
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BwTodoSite',
        'Api',
        [
            \Blueways\BwTodoSite\Controller\ProfileController::class => 'index,detail',
            \Blueways\BwTodoSite\Controller\TaskController::class => 'create,delete'
        ],
        [
            \Blueways\BwTodoSite\Controller\ProfileController::class => '',
            \Blueways\BwTodoSite\Controller\TaskController::class => ''
        ]
    );
})();
