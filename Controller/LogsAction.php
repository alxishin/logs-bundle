<?php

declare(strict_types=1);

namespace LogsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class LogsAction extends AbstractController
{
    #[Route(path: 'logs/{file?}', name: 'admin.logs', methods: ['GET'])]
    public function __invoke(KernelInterface $kernel, ?string $file = null): Response
    {
        $fs = new Filesystem();
        $files = [];
        if ($fs->exists($kernel->getLogDir())) {
            $files = scandir($kernel->getLogDir());
        }

        $files = array_values(array_diff($files, ['.', '..']));

        if (is_null($file)) {
            $file = $files[0];
        }

        if (!in_array($file, $files, true)) {
            throw new NotFoundHttpException();
        }

        $result = [];
        foreach (explode(PHP_EOL, $fs->readFile($kernel->getLogDir() . DIRECTORY_SEPARATOR . $file)) as $line) {
            if ($line === '') {
                continue;
            }
            $result[] = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        }

        return $this->render('@Logs/logs.html.twig', [
            'files' => $files,
            'result' => $result
        ]);
    }
}
