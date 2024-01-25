<?php
return array(
    'plugins' => array(
        'melisfront' => array(
            'datas' => [
                'default' => [
                    'errors' => array(
                        'error_reporting' => E_ALL & ~E_USER_DEPRECATED,
                        'display_errors' => 1,
                    ),
                ],
                'gdpr_auto_anonymized_time_format' => 'd'
            ], 
            'resources' => array(
                'js' => array(),
                'css' => array(),
            )
        )
    )
);
