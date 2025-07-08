<?php

namespace wcf\system\endpoint\controller\xxschrandxx\dynmap;

use Laminas\Diactoros\Response\JsonResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\dynmap\standalonefiles\StandaloneFileList;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\PermissionDeniedException;
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

        if (!DynmapUtil::hasAccesToServer($variables['server'])) {
            throw new PermissionDeniedException();
        }

        $configFileList = new StandaloneFileList();
        $configFileList->getConditionBuilder()->add('ServerID = ? AND FileName = ?', [$variables['server'], 'dynmap_config.json']);
        $configFileList->readObjects();
        $config = $configFileList->getSingleObject();
        $configArray = $config->getContent();

        $configArray['login-enabled'] = false;
        $configArray['loginrequired'] = false;
        $configArray['allowwebchat'] = DynmapUtil::canUseWebchat();
        $configArray['webchat-requires-login'] = false;
        $configArray['webchat-interval'] = DYNMAP_GENERAL_WEBCHAT_INTERVAL;

        $configArray['worlds'] = DynmapUtil::removeProtrectedWorlds($variables['server'], $configArray['worlds']);

        return new JsonResponse($configArray);
    }
}
