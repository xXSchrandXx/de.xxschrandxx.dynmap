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

#[GetRequest('/xxschrandxx/dynmap/{server:\d+}/update/{world}/{timestamp:\d+}')]
class GetUpdate implements IController
{
    #[Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        if (!isset($variables['server'])) {
            throw new \InvalidArgumentException('Missing required parameters: server');
        }

        if (!isset($variables['world'])) {
            throw new \InvalidArgumentException('Invalid argument: world');
        }

        if (strpos($variables['world'], '/') || strpos($variables['world'], '\\')) {
            throw new \InvalidArgumentException('Invalid world name: ' . $variables['world']);
        }

        if (!DynmapUtil::hasAccesToWorld($variables['server'], $variables['world'])) {
            return new JsonResponse(['error' => 'access-denied']);
        }

        $standaloneFileList = new StandaloneFileList();
        $standaloneFileList->getConditionBuilder()->add('ServerID = ? AND FileName = ?', [
            $variables['server'],
            'dynmap_' . $variables['world'] . '.json'
        ]);
        $standaloneFileList->readObjects();
        $json = $standaloneFileList->getSingleObject()->getContent();

        $json['players'] = DynmapUtil::removeHiddenPlayers($json['players']);

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
