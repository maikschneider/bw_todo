<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:profile',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'hideAtCopy' => true,
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'name',
        'iconfile' => 'EXT:bw_todo/Resources/Public/Images/Icons/profile.svg',
    ],
    'types' => [
        0 => [
            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                                --palette--;;general,,
                            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                                --palette--;;hidden'
        ],
    ],
    'palettes' => [
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden
            ',
        ],
        'general' => [
            'showitem' => 'name,--linebreak--,tasks'
        ]
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:profile.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:profile.name',
            'config' => [
                'type' => 'input',
                'eval' => 'unique,trim,required',
                'size' => 50,
                'max' => 255,
            ]
        ],
        'tasks' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:profile.tasks',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_bwtodo_domain_model_task',
                'foreign_field' => 'profile',
                'foreign_label' => 'title',
                'appearance' => [
                    'enabledControls' => [
                        'info' => false,
                        'hide' => false
                    ]
                ]
            ]
        ]
    ]
];
