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

namespace Plugin\Databackup4\Tests\Web;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use \RecursiveIteratorIterator;

/**
 * Class Databackup4AdminControllerTest.
 */
class Databackup4AdminControllerTest extends AbstractAdminWebTestCase
{
    /**
     * Setup method.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * testIndex
     */
    public function testIndex()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('databackup4_admin_index'));
        $this->assertContains('データ移行のためのバックアップ', $crawler->html());
    }

    /**
     * testExecute
     */
    public function testExecute()
    {
        $backupDir = self::$container->getParameter('kernel.project_dir').'/var/backup/';
        if (!is_dir($backupDir)) {
            // create backup dir
            mkdir($backupDir, 0777, true);
        } else {
            // remove backup dir(recurcive)
            $this->rm($backupDir);
        }

        // execute data backup
        $crawler = $this->client->request('POST', $this->generateUrl('databackup4_admin_index'));

        $this->assertTrue(is_dir($backupDir));

        // get dir list from backup dir
        $targets = glob($backupDir.'/*', GLOB_ONLYDIR);
        $this->assertEquals(1, count($targets));

        $targetDir = $targets[0];

        $this->assertFileExists($targetDir.'/dtb_authority_role.csv');
        $this->assertFileExists($targetDir.'/mtb_work.csv');
        $this->assertFileExists($targetDir.'.tar');
        $this->assertFileExists($targetDir.'.tar.gz');

    }

    private function rm($dir){
        $entries = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($entries as $entry) {
            $func = ($entry->isDir() ? 'rmdir' : 'unlink');
            $func($entry->getRealPath());
        }
        rmdir($dir);
    }
}
