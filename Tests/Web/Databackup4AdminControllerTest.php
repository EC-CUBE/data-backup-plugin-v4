<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Databackup42\Tests\Web;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class Databackup4AdminControllerTest.
 */
class Databackup4AdminControllerTest extends AbstractAdminWebTestCase
{
    /**
     * Setup method.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * testIndex
     */
    public function testIndex()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('databackup42_admin_config'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertContains('データ移行のためのバックアップ', $crawler->html());
    }

    /**
     * testExecute
     */
    public function testExecute()
    {
        $this->client->request('POST',
            $this->generateUrl('databackup42_admin_config'),
            [
                'form' => ['_token' => 'dummy'],
            ],
        );

        $response = $this->client->getResponse();
        self::assertTrue($response->isSuccessful());
        self::assertInstanceOf(BinaryFileResponse::class, $response);

        $tarGz = new \PharData($response->getFile());
        foreach ($tarGz as $f) {
            // csvファイルが格納されている
            self::assertContains('.csv', $f->getFileName());
        }
    }
}
