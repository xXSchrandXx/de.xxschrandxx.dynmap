<?php

namespace wcf\system\endpoint\controller\xxschrandxx\dynmap;

use Laminas\Diactoros\Response\JsonResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\dynmap\standalonefiles\StandaloneFileList;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\util\DynmapUtil;

#[GetRequest('/xxschrandxx/dynmap/{server:\d+}/configuration')]
class GetConfiguration implements IController
{
    #[Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        if (!isset($variables['server'])) {
            throw new \InvalidArgumentException('Missing required parameters: server');
        }

        $configFileList = new StandaloneFileList();
        $configFileList->getConditionBuilder()->add('ServerID = ? AND FileName = ?', [$variables['server'], 'dynmap_config.json']);
        $configFileList->readObjects();
        $config = $configFileList->getSingleObject();
        $configArray = $config->getContent();

        $configArray['login-enabled'] = false;
        $configArray['loginrequired'] = false;
        /* TODO modify webchat
        $configArray['allowwebchat'] = OPTION;
        $configArray['webchat-requires-login'] = OPTION;
        $configArray['webchat-interval'] = OPTION;
        */

        $configArray['worlds'] = DynmapUtil::removeProtrectedWorlds($configArray['worlds']);

        return new JsonResponse($configArray);
    }
}
