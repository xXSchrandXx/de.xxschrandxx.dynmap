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

#[GetRequest('/xxschrandxx/dynmap/{server:\d+}/update/{world}/{timestamp:\d+}')]
class GetUpdate implements IController
{
    #[Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $minecraft = new Minecraft($variables['server']);

        if (!$minecraft->minecraftID) {
            throw new \InvalidArgumentException('server');
        }

        $server = new Server($minecraft);

        if (!$server->checkSchemaVersion()) {
            throw new SystemException('Unsupported SchameVersion');
        }

        if (!$server->hasAccesToServer($variables['server'])) {
            throw new PermissionDeniedException();
        }

        if (!isset($variables['world'])) {
            throw new \InvalidArgumentException('Invalid argument: world');
        }

        if (strpos($variables['world'], '/') || strpos($variables['world'], '\\')) {
            throw new \InvalidArgumentException('Invalid world name: ' . $variables['world']);
        }

        if (!$server->hasAccesToWorld($variables['world'])) {
            return new JsonResponse(['error' => 'access-denied']);
        }

        $json = $server->getWorldData($variables['world']);
        $json['players'] = $server->getVisablePlayers($variables['world']);

        return new JsonResponse($json);
    }
}

/** @internal */
final class UpdateParameters
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $world
    ) {
    }
}
