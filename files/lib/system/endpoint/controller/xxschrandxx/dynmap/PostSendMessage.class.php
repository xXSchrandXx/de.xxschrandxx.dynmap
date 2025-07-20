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
use wcf\system\flood\FloodControl;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\UserUtil;

#[PostRequest('/xxschrandxx/dynmap/{server:\d+}/sendmessage')]
class PostSendMessage implements IController
{
    public $floodgate = 'de.xxschrarndxx.wsc.dynmap.floodgate';

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

        if (!WCF::getSession()->getPermission('user.minecraft.dynmap.canUseWebchat')) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $config = $server->getConfig();

        if (!isset($config['allowwebchat']) && !$config['allowwebchat']) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $msginterval = $config['webchat-interval'];
        $user = WCF::getUser();
        if ($user->userID) {
            $lastchat = FloodControl::getInstance()->getUserLastTime($this->floodgate, $user->userID) ?? 0;
            FloodControl::getInstance()->registerUserContent($this->floodgate, $user->userID);
        } else {
            $ip = UserUtil::getIpAddress();
            $lastchat = FloodControl::getInstance()->getGuestLastTime($this->floodgate, $ip) ?? 0;
            FloodControl::getInstance()->registerGuestContent($this->floodgate, $ip);
            unset($ip);
        }

        if ($lastchat + $msginterval >= TIME_NOW) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }
        
        $parameters = Helper::mapApiParameters($request, SendMessageParameters::class);
        $data = [
            'message' => $parameters->message,
            'name' => $parameters->name
        ];
        $micro = microtime(true);
        $timestamp = round($micro * 1000.0);
        $data['timestamp'] = $timestamp;

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
                $data['name'] = $data['userid'] = $name;
            }
        }
        if (!isset($data['name']) || empty($data['name'])) {
            $data['userid'] = $data['name'] = WCF::getLanguage()->get('wcf.user.guest');
        }

        $standaloneFileList = $server->getStandaloneFileList();
        $standaloneFileList->getConditionBuilder()->add('FileName = ?', ['dynmap_webchat.json']);
        $standaloneFileList->readObjects();
        $webchat = $standaloneFileList->getSingleObject();

        $gotold = false;
        if (isset($webchat)) {
            try {
                $old_messages = $webchat->getContent();
                if (isset($old_messages) && !empty($old_messages)) {
                    foreach ($old_messages as $message) {
                        if (($timestamp - $config['updaterate'] - 10000) < $message['timestamp']) {
                            $new_messages[] = $message;
                        }
                    }
                    $new_messages[] = $data;
                }
            $gotold = true;
            } catch (\Exception $e) {
                // Do nothing
            }
        }

        if ($gotold) {
            $conditionBuilder = new PreparedStatementConditionBuilder();
            $conditionBuilder->add('FileName = ?', ['dynmap_webchat.json']);
            $editor = new StandaloneFileEditor($webchat, $server);
            $editor->update([
                'Content' => JSON::encode($new_messages)
            ], $conditionBuilder);
        } else {
            StandaloneFileEditor::create([
                'server' => $server,
                'FileName' => 'dynmap_webchat.json',
                'ServerID' => 0,
                'Content' => JSON::encode([$data])
            ]);
        }

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
