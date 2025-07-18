<?php

namespace wcf\system\endpoint\controller\xxschrandxx\dynmap;

use Laminas\Diactoros\Response\JsonResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\dynmap\external\standalonefiles\StandaloneFileEditor;
use wcf\data\dynmap\Server;
use wcf\data\minecraft\Minecraft;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\http\Helper;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\MinecraftLinkerUtil;
use wcf\util\UserUtil;

#[PostRequest('/xxschrandxx/dynmap/{server:\d+}/sendmessage')]
class PostSendMessage implements IController
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

        if (!$server->hasAccesToServer($variables['server'])) {
            throw new PermissionDeniedException();
        }

        $config = $server->getConfig();

        if (!isset($config['allowwebchat']) && !$config['allowwebchat']) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $msginterval = $config['webchat-interval'] ?? 2000;

        if (isset($_SESSION['lastchat_' . $server->minecraftID])) {
            $lastchat = $_SESSION['lastchat_' . $server->minecraftID];
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
        $user = WCF::getUser();
        if ($user->userID) {
            $userToMinecraftUserList = new UserToMinecraftUserList();
            $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [$user->userID]);
            $userToMinecraftUserList->readObjectIDs();
            $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();
            $minecraftUserList = new MinecraftUserList();
            $minecraftUserList->setObjectIDs($userToMinecraftUserIDs);
            $minecraftUserList->readObjects();
            $name = '';
            /**
             * When multiple MinecraftUser are linked, chain with | 
             * @var \wcf\data\user\minecraft\MinecraftUser $minecraftUser
             */
            foreach($minecraftUserList->getObjects() as $minecraftUser) {
                if (empty($name)) {
                    $name = $minecraftUser->getMinecraftName();
                } else {
                    $name .= '|' . $minecraftUser->getMinecraftName();
                }
            }
            if (!empty($name)) {
                $data['name'] = $name;
            }
        }

        $standaloneFileList = $server->getStandaloneFileList();
        $standaloneFileList->getConditionBuilder()->add('FileName = ?', ['dynmap_webchat.json']);
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

        if ($gotold) {
            $conditionBuilder = new PreparedStatementConditionBuilder();
            $conditionBuilder->add('FileName = ?', [0, 'dynmap_webchat.json']);
            $editor = new StandaloneFileEditor($webchat, $server);
            $editor->update([
                'Content' => JSON::encode($new_messages)
            ], $conditionBuilder);
        } else {
            StandaloneFileEditor::create([
                'server' => $server,
                'FileName' => 'dynmap_webchat.json',
                'ServerID' => 0,
                'Content' => JSON::encode($new_messages)
            ]);
        }

        $_SESSION['lastchat_' . $server->minecraftID] = time() + $msginterval;

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
