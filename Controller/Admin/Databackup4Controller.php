<?php

namespace Plugin\Databackup4\Controller\Admin;

use Eccube\Controller\AbstractController;
use Plugin\Databackup4\Service\Databackup4Service;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
     * @Route("/%eccube_admin_route%/databackup4/index", name="databackup4_admin_index")
     * @Template("@Databackup4/admin/index.twig")
     */
    public function index(Request $request)
    {
        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($request->getMethod() === 'POST') {
            // %kernel.project_dir%/var/backup
            $backupDir = $this->container->getParameter('kernel.project_dir').'/var/backup/'.date('YmdHis');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0777, true);
            }

            $tables = $this->databackup4Service->listTableNames();
            foreach ($tables as $table) {
                $this->databackup4Service->dumpCSV($table, $backupDir);
            }

            $tarFile = $backupDir.'.tar';

            // tar.gzファイルに圧縮する.
            $phar = new \PharData($tarFile);
            $phar->buildFromDirectory($backupDir);
            $phar->compress(\Phar::GZ);

            return (new BinaryFileResponse($tarFile.'.gz'))->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
