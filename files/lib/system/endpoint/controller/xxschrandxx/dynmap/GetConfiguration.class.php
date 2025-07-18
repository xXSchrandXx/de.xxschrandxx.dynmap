<?php

namespace wcf\system\endpoint\controller\xxschrandxx\dynmap;

use Laminas\Diactoros\Response\JsonResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\dynmap\Server;
use wcf\data\minecraft\Minecraft;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;

#[GetRequest('/xxschrandxx/dynmap/{server:\d+}/configuration')]
class GetConfiguration implements IController
{
    #[Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        if (!isset($variables['server'])) {
            throw new \InvalidArgumentException('server');
        }

        $minecraft = new Minecraft($variables['server']);

        if (!$minecraft->minecraftID) {
            throw new \InvalidArgumentException('server');
        }

        $server = new Server($minecraft);

        if (!$server->checkSchemaVersion()) {
            throw new SystemException('Unsupported SchameVersion');
        }

        if (!$server->hasAccesToServer()) {
            throw new PermissionDeniedException();
        }

        $configArray = $server->getConfig();
        $configArray['allowwebchat'] = $server->canUseWebchat();
        $configArray['worlds'] = $server->getAccessableWorlds();

        return new JsonResponse($configArray);
    }
}
