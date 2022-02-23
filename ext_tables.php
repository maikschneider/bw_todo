<?php
defined('TYPO3_MODE') || die();

call_user_func(
    static function () {

        /**
         * Register icons
         */
        $iconsToRegistser = [
            'apps-pagetree-folder-todo',
            'profile',
            'task',
        ];
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        foreach ($iconsToRegistser as $iconName) {
            $iconRegistry->registerIcon(
                $iconName,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:bw_todo/Resources/Public/Images/Icons/' . $iconName . '.svg']
            );
        }
    }
);
