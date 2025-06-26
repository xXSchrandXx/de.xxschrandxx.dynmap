<?php

namespace wcf\system\endpoint\controller\xxschrandxx\dynmap;

use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Laminas\Diactoros\Response\RedirectResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\dynmap\tiles\Tile;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;

#[GetRequest('/xxschrandxx/dynmap/{server:\d+}/tile')]
class GetTile implements IController
{
    #[Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        if (!isset($variables['server'])) {
            throw new \InvalidArgumentException('Missing required parameters: server');
        }

        $parameters = Helper::mapApiParameters($request, TileParameters::class);

        if (!isset($parameters->tile)) {
            throw new \InvalidArgumentException('Invalid argument: tile');
        }

        $path = htmlspecialchars($parameters->tile);

        if ((!isset($path)) || strstr($path, "..")) {
            throw new \InvalidArgumentException('Invalid path');
        }

        $parts = explode("/", $path);

        if (count($parts) != 4) {
            return new RedirectResponse(WCF_DIR . 'js/3rdParty/dynmap/images/blank.png');
        }

        $world = $parts[0];

        // TODO check proteced Worlds

        $variant = 'STANDARD';

        $prefix = $parts[1];
        $plen = strlen($prefix);
        if (($plen > 4) && (substr($prefix, $plen - 4) === "_day")) {
            $prefix = substr($prefix, 0, $plen - 4);
            $variant = 'DAY';
        }

        // TODO check map access

        $fparts = explode("_", $parts[3]);

        if (count($fparts) == 3) { // zoom_x_y
            $zoom = strlen($fparts[0]);
            $x = intval($fparts[1]);
            $y = intval($fparts[2]);
        } elseif (count($fparts) == 2) { // x_y
            $zoom = 0;
            $x = intval($fparts[0]);
            $y = intval($fparts[1]);
        } else {
            return new RedirectResponse(WCF_DIR . 'js/3rdParty/dynmap/images/blank.png');
        }

        $tile = Tile::getTile($world, $prefix, $variant, $x, $y, $zoom);
        if (!$tile->HashCode) {
            throw new InvalidArgumentException('Invalid tile: ' . $path);
        }
        if ($tile->format === 0) {
            $contentType = 'image/png';
        } elseif ($tile->format === 2) {
            $contentType = 'image/webp';
        } else {
            $contentType = 'image/jpeg';
        }
        return new Response(
            200,
            [
                'Content-Type' => $contentType,
                'ETag' => $tile->HashCode,
                'Last-Modified' => gmdate('D, d M Y H:i:s', (int) ($tile->LastUpdate / 1000)) . ' GMT'
            ],
            isset($tile->NewImage) ? $tile->NewImage : $tile->Image
        );
    }
}

/** @internal */
final class TileParameters
{
    public function __construct(
        /** 
         * {prefix}{nightday}/{scaledx}_{scaledy}/{zoom}{x}_{y}.{fmt}
         * @var non-empty-string
         * */
        public readonly string $tile
    ) {
    }
}

