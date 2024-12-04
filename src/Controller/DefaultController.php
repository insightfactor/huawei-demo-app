<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_default')]
class DefaultController extends AbstractController
{
    public function __construct(
        protected ParameterBagInterface  $parameterBag,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            [
                'name' => 'Demo App',
                'appEnv' => $this->parameterBag->get('app_env'),
                'databaseConnected' => $this->entityManager->getConnection()->connect(),
                'apcuCacheActivated' => $this->checkApcuCache()
            ]
        );
    }

    private function checkApcuCache(): bool
    {
        $apcuAdapter = new ApcuAdapter();
        $cacheKey = 'index';
        $cacheItem = $apcuAdapter->getItem($cacheKey);
        $cacheItem->set(['test']);

        $apcuAdapter->save($cacheItem);

        return $apcuAdapter->hasItem($cacheKey);
    }
}
