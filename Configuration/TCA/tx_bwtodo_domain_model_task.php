<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:task',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'hideAtCopy' => true,
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'title,description',
        'iconfile' => 'EXT:bw_todo/Resources/Public/Images/Icons/task.svg',
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
            'showitem' => 'title, --linebreak--, profile, due_date, --linebreak--, description, --linebreak--'
        ]
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:task.hidden',
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
        'profile' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:task.profile',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_bwtodo_domain_model_profile',
                'items' => [
                    ['-', 0]
                ],
                'eval' => ''
            ]
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:task.title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'size' => 50,
                'max' => 255,
            ]
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:task.description',
            'config' => [
                'type' => 'text',
                'cols' => 50,
                'rows' => 10
            ]
        ],
        'due_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_todo/Resources/Private/Language/locallang.xlf:task.due_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0
            ]
        ],
    ]
];
