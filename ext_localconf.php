<?php

defined('TYPO3_MODE') || die('Access denied.');

(static function () {

    // add TypoScript setup and constants
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'bw_todo',
        'setup',
        "@import 'EXT:bw_todo/Configuration/TypoScript/setup.typoscript'"
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'bw_todo',
        'constants',
        "@import 'EXT:bw_todo/Configuration/TypoScript/constants.typoscript'"
    );

    // add PageTS
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:bw_todo/Configuration/TSconfig/Page.tsconfig'"
    );

    // register FE-Plugins
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BwTodo',
        'Api',
        [
            \Blueways\BwTodo\Controller\ProfileController::class => 'index,detail',
            \Blueways\BwTodo\Controller\TaskController::class => 'create,delete'
        ],
        [
            \Blueways\BwTodo\Controller\ProfileController::class => '',
            \Blueways\BwTodo\Controller\TaskController::class => ''
        ]
    );
})();
