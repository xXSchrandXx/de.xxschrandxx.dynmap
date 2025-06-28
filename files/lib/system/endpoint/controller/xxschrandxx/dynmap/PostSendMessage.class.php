<?php

namespace wcf\system\endpoint\controller\xxschrandxx\dynmap;

use Laminas\Diactoros\Response\JsonResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\dynmap\standalonefiles\StandaloneFileEditor;
use wcf\data\dynmap\standalonefiles\StandaloneFileList;
use wcf\http\Helper;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\util\JSON;
use wcf\util\UserUtil;

#[PostRequest('/xxschrandxx/dynmap/{server:\d+}/sendmessage')]
class PostSendMessage implements IController
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
        $config = $standaloneFileList->getSingleObject()->getContent();

        if (!isset($config['allowwebchat']) && !$config['allowwebchat']) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $msginterval = $config['webchat-interval'] ?? 2000;

        if (isset($_SESSION['lastchat'])) {
            $lastchat = $_SESSION['lastchat'];
        } else {
            $lastchat = 0;
        }

        if ($lastchat >= time()) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }
        

        $micro = microtime(true);
        $timestamp = round($micro * 1000.0);

        $parameters = Helper::mapApiParameters($request, SendMessageParameters::class);
        $data = [
            'message' => $parameters->message,
            'name' => $parameters->name
        ];


        $data['timestamp'] = $timestamp;
        $data['ip'] = UserUtil::getIpAddress();

        // TODO event to set name via Minecraft-Linker

        $standaloneFileList = new StandaloneFileList();
        $standaloneFileList->getConditionBuilder()->add('ServerID = ? AND FileName = ?', [$variables['server'], 'dynmap_webchat.json']);
        $standaloneFileList->readObjects();
        $webchat = $standaloneFileList->getSingleObject();

        $gotold = false;
        if (isset($webchat)) {
            $old_messages = $webchat->getContent();
            $gotold = true;
        }

        if (!empty($old_messages)) {
            foreach ($old_messages as $message) {
                if (($timestamp - $config['updaterate'] - 10000) < $message['timestamp']) {
                    $new_messages[] = $message;
                }
            }
        }
        $new_messages[] = $data;

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('ServerID = ? AND FileName = ?', [$variables['server'], 'dynmap_webchat.json']);
        if ($gotold) {
            $editor = new StandaloneFileEditor($webchat);
            $editor->update([
                'Content' => JSON::encode($new_messages)
            ], $conditionBuilder);
        } else {
            wcfDebug(
            StandaloneFileEditor::create([
                'FileName' => 'dynmap_webchat.json',
                'ServerID' => $variables['server'],
                'Content' => JSON::encode($new_messages)
            ]));
        }

        $_SESSION['lastchat'] = time() + $msginterval;

        return new JsonResponse(['error' => 'none']);
    }
}

/** @internal */
final class SendMessageParameters
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $message,
        /** @var string */
        public readonly string $name = ''
    ) {
    }
}
