<?php

namespace wcf\system\endpoint\controller\xxschrandxx\dynmap;

use Laminas\Diactoros\Response\JsonResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\dynmap\standalonefiles\StandaloneFileList;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;

#[GetRequest('/xxschrandxx/dynmap/{server:\d+}/configuration')]
class GetConfiguration implements IController
{
    #[Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        if (!isset($variables['server'])) {
            throw new \InvalidArgumentException('Missing required parameters: server');
        }

        $standaloneFileList = new StandaloneFileList();
        $standaloneFileList->getConditionBuilder()->add('ServerID = ? AND FileName = ?', [$variables['server'], 'dynmap_config.json']);
        $standaloneFileList->readObjects();
        $json = $standaloneFileList->getSingleObject()->getContent();

        // TODO remove proteced Worlds
        // TODO set login options to false
        // TODO modify webchat

        return new JsonResponse($json);
    }
}
