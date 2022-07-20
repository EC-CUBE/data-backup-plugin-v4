<?php

namespace Plugin\Databackup4;

use Eccube\Common\EccubeNav;

class Databackup4Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'setting' => [
                'children' => [
                    'system' => [
                        'children' => [
                            'databackup4' => [
                                'name' => 'databackup4.admin.title',
                                'url' => 'databackup4_admin_index',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
