<?php

namespace AppBundle\Slot;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\SignalSlot\SignalDispatcher;
use eZ\Publish\Core\SignalSlot\Slot as BaseSlot;
use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\UserService;
use EzSystems\Notification\Core\SignalSlot\Signal\NotificationSignal;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class OnUserPublishSlot extends BaseSlot
{
    /** @var  UserService */
    private  $userService;

    /** @var LocationService */
    private $locationService;

    /** @var SignalDispatcher */
    private $signalDispatcher;

    public $logFile = "../app/logs/slot.log";

    public function __construct(
        UserService $userService,
        LocationService $locationService,
        SignalDispatcher $signalDispatcher

    )
    {
        $this->userService = $userService;
        $this->locationService = $locationService;
        $this->signalDispatcher = $signalDispatcher;
    }

    public function receive( Signal $signal )
    {
        $log = new Logger("SlotNotification");
        $log->pushHandler(new StreamHandler($this->logFile, Logger::INFO));
        if ( $signal instanceof Signal\UserService\CreateUserSignal )
        {
            $user = $this->userService->loadUser($signal->userId);
            $data = [
                'user_notification' => 'UserNotification',
                'sender_firstname' => (string)$user->getFieldValue('first_name'),
                'sender_lastname' => (string)$user->getFieldValue('last_name'),
                'message' => 'New User was created:'.(string)$user->getFieldValue('first_name') .' '.(string)$user->getFieldValue('last_name') .'('.$user->versionInfo->initialLanguageCode.') user contentID:'.$signal->userId .', LocationID:'. $user->versionInfo->contentInfo->mainLocationId.', E-Mail:' .$user->getFieldValue('user_account')->email,
                'content_name' => $user->contentInfo->name,

            ];
            $log->addInfo('Signal:' . print_r($data, 1));
            //$log->addInfo('Signal:' . print_r($user, 1));

            //(Step 2)Add Administrator Notification using Notification Signal and FlexWorkflow

            $location = $this->locationService->loadLocation($user->versionInfo->contentInfo->mainLocationId);

            $platformUrl = sprintf(
                '/api/ezp/v2/content/locations%s',
                substr($location->pathString,0,-1)

            );
            $data = [
                'user_notification' => 'UserNotification',
                'sender_firstname' => (string)$user->getFieldValue('first_name'),
                'sender_lastname' => (string)$user->getFieldValue('last_name'),
                'message' => 'New User was created:'.(string)$user->getFieldValue('first_name') .' '.(string)$user->getFieldValue('last_name') .'('.$user->versionInfo->initialLanguageCode.') user contentID:'.$signal->userId .', LocationID:'. $user->versionInfo->contentInfo->mainLocationId.', E-Mail:' .$user->getFieldValue('user_account')->email,
                'content_name' => $user->contentInfo->name,
                'link' => '/ez#/view/'.urlencode($platformUrl).'/'.$user->versionInfo->initialLanguageCode,
            ];

            $this->signalDispatcher->emit(new NotificationSignal([
                'ownerId' => 14,
                'type' => 'FlexWorkflow:Review',
                'data' => $data,
            ]));

            //END Step 2

        }

        $log->addInfo('Signal:' . print_r($signal, 1));

    }
}