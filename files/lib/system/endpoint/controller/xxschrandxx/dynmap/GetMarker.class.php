<?php

namespace wcf\system\endpoint\controller\xxschrandxx\dynmap;

use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Laminas\Diactoros\Response\RedirectResponse;
use Negotiation\Exception\InvalidArgument;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\dynmap\faces\FaceList;
use wcf\data\dynmap\markerfiles\MarkerFileList;
use wcf\data\dynmap\markericons\MarkerIconList;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\GetRequest;
use wcf\system\request\RouteHandler;

#[GetRequest('/xxschrandxx/dynmap/{server:\d+}/marker')]
class GetMarker implements IController
{
    #[Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        if (!isset($variables['server'])) {
            throw new \InvalidArgumentException('Missing required parameters: server');
        }

        $parameters = Helper::mapApiParameters($request, MarkerParameters::class);

        if (!isset($parameters->marker)) {
            throw new \InvalidArgumentException('Invalid argument: marker');
        }

        $path = htmlspecialchars($_REQUEST['marker']);

        if ((!isset($path)) || strstr($path, "..")) {
            throw new \InvalidArgumentException('Invalid path');
        }

        $parts = explode("/", $path);

        if (($parts[0] != "faces") && ($parts[0] != "_markers_")) {
            throw new InvalidArgument('Bad marker');
        }

        if ($parts[0] == "faces") {
            if (count($parts) != 3) {
                throw new InvalidArgument('Bad face');
            }
            $ft = 0;
            if ($parts[1] == "8x8") {
                $ft = 0;
            } elseif ($parts[1] == '16x16') {
                $ft = 1;
            } elseif ($parts[1] == '32x32') {
                $ft = 2;
            } elseif ($parts[1] == 'body') {
                $ft = 3;
            }
            $pn = explode(".", $parts[2]);
            $faceList = new FaceList();
            $faceList->getConditionBuilder()->add('PlayerName = ? AND TypeID = ?', [$pn[0], $ft]);
            $faceList->readObjects();
            $face = $faceList->getSingleObject();

            if (isset($face)) {
                return new Response(
                    200,
                    ['Content-Type' => 'image/png'],
                    $face->Image
                );
            } else {
                return new RedirectResponse(RouteHandler::getHost() . '/js/dynmap/images/blank.png');
            }
        } else { // _markers_
            $in = explode(".", $parts[1]);
            $name = implode(".", array_slice($in, 0, count($in) - 1));
            $ext = $in[count($in) - 1];
            if (($ext == "json") && (strpos($name, "marker_") == 0)) {
                $world = substr($name, 7);
                $markerFileList = new MarkerFileList();
                $markerFileList->getConditionBuilder()->add('FileName = ?', [$world]);
                $markerFileList->readObjects();
                $markerFile = $markerFileList->getSingleObject();
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    $markerFile ? $markerFile->Content : '{}'
                );
            } else {
                $markerIconList = new MarkerIconList();
                $markerIconList->getConditionBuilder()->add('IconName = ?', [$name]);
                $markerIconList->readObjects();
                $markerIcon = $markerIconList->getSingleObject();

                if (isset($markerIcon)) {
                    return new Response(
                        200,
                        ['Content-Type' => 'image/png'],
                        $markerIcon->Image
                    );
                } else {
                    return new RedirectResponse(RouteHandler::getHost() . '/js/dynmap/images/blank.png');
                }
            }
        }
    }
}

/** @internal */
final class MarkerParameters
{
    public function __construct(
        /**
         * faces/{size}x{size}/{player}.png
         * _markers_/{marker}.{_marker.json/png}
         * @var non-empty-string
         */
        public readonly string $marker
    ) {
    }
}

