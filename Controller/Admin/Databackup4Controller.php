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

namespace Plugin\Databackup4\Controller\Admin;

use Eccube\Controller\AbstractController;
use Plugin\Databackup4\Service\Databackup4Service;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Annotation\Route;

class Databackup4Controller extends AbstractController
{
    /**
     * @var Databackup4Service
     */
    protected $databackup4Service;

    /**
     * Databackup4Controller constructor.
     *
     * @param Databackup4Service $databackup4Service
     */
    public function __construct(
        Databackup4Service $databackup4Service
    ) {
        $this->databackup4Service = $databackup4Service;
    }

    /**
     * @Route("/%eccube_admin_route%/databackup4/config", name="databackup4_admin_config")
     * @Template("@Databackup4/admin/index.twig")
     */
    public function index(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $backupBaseDir = $this->getParameter('plugin_data_realdir').'/Databackup4';
            $backupDir = $backupBaseDir.'/'.date('YmdHis');

            $fs = new Filesystem();
            $fs->mkdir($backupDir);

            $tables = $this->databackup4Service->listTableNames();
            foreach ($tables as $table) {
                $this->databackup4Service->dumpCSV($table, $backupDir);
            }

            $tarFile = $backupDir.'.tar';

            // tar.gzファイルに圧縮する.
            $phar = new \PharData($tarFile);
            $phar->buildFromDirectory($backupDir);
            $phar->compress(\Phar::GZ);

            // 終了時に一時ディレクトリを削除.
            $eventDispatcher->addListener(KernelEvents::TERMINATE, function (PostResponseEvent $event) use ($backupBaseDir, $fs) {
                // UnitTest実行時はterminateイベント実行後にファイル出力が行われるため、ここでは削除しない
                if (env('APP_ENV') === 'test') {
                    return;
                }
                $fs->remove($backupBaseDir);
            });

            return (new BinaryFileResponse($tarFile.'.gz'))->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
