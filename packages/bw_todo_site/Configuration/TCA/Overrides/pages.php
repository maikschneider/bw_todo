<?php

defined('TYPO3_MODE') || die();

(static function () {
    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        0 => 'LLL:EXT:bw_todo_site/Resources/Private/Language/locallang.xlf:page.types.td',
        1 => 'td',
        2 => 'apps-pagetree-folder-todo',
    ];
    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-td'] = 'apps-pagetree-folder-todo';
})();
