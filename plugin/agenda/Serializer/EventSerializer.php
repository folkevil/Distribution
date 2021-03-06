<?php

namespace Claroline\AgendaBundle\Serializer;

use Claroline\AgendaBundle\Entity\Event;
use Claroline\AppBundle\API\Options;
use Claroline\AppBundle\API\Serializer\SerializerTrait;
use Claroline\AppBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\API\Serializer\File\PublicFileSerializer;
use Claroline\CoreBundle\API\Serializer\User\UserSerializer;
use Claroline\CoreBundle\API\Serializer\Workspace\WorkspaceSerializer;
use Claroline\CoreBundle\Entity\File\PublicFile;
use Claroline\CoreBundle\Entity\Workspace\Workspace;
use Claroline\CoreBundle\Library\Normalizer\DateNormalizer;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class EventSerializer
{
    use SerializerTrait;

    /** @var AuthorizationCheckerInterface */
    private $authorization;

    /** @var ObjectManager */
    private $om;

    /** @var PublicFileSerializer */
    private $fileSerializer;

    /** @var WorkspaceSerializer */
    private $workspaceSerializer;

    /** @var UserSerializer */
    private $userSerializer;

    /**
     * RoleSerializer constructor.
     *
     * @param AuthorizationCheckerInterface $authorization
     * @param ObjectManager                 $om
     * @param PublicFileSerializer          $fileSerializer
     * @param WorkspaceSerializer           $workspaceSerializer
     * @param UserSerializer                $userSerializer
     */
    public function __construct(
        AuthorizationCheckerInterface $authorization,
        ObjectManager $om,
        PublicFileSerializer $fileSerializer,
        WorkspaceSerializer $workspaceSerializer,
        UserSerializer $userSerializer
    ) {
        $this->authorization = $authorization;
        $this->om = $om;
        $this->fileSerializer = $fileSerializer;
        $this->workspaceSerializer = $workspaceSerializer;
        $this->userSerializer = $userSerializer;
    }

    public function getName()
    {
        return 'event';
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    public function serialize(Event $event)
    {
        $editable = $event->getWorkspace() ?
            $this->authorization->isGranted('EDIT', $event) :
            false !== $event->isEditable();

        return [
            'id' => $event->getUuid(),
            'title' => $event->getTitle(),
            'start' => $event->getStart() ? DateNormalizer::normalize($event->getStart()) : null,
            'end' => $event->getEnd() ? DateNormalizer::normalize($event->getEnd()) : null,
            'allDay' => $event->isAllDay(),
            'thumbnail' => $this->serializeThumbnail($event),
            'description' => $event->getDescription(),
            'workspace' => $event->getWorkspace() ? $this->workspaceSerializer->serialize($event->getWorkspace(), [Options::SERIALIZE_MINIMAL]) : null,
            'meta' => $this->serializeMeta($event),
            'display' => [
                'color' => $event->getPriority(),
            ],
            'permissions' => [
                'edit' => $editable,
            ],
        ];
    }

    public function serializeMeta(Event $event)
    {
        return [
            'type' => $event->isTask() ? 'task' : 'event',
            'creator' => $this->userSerializer->serialize($event->getUser()),
            'done' => $event->isTaskDone(),
        ];
    }

    /**
     * Serialize the event thumbnail.
     *
     * @param Event $event
     *
     * @return array|null
     */
    private function serializeThumbnail(Event $event)
    {
        if (!empty($event->getThumbnail())) {
            /** @var PublicFile $file */
            $file = $this->om
                ->getRepository(PublicFile::class)
                ->findOneBy(['url' => $event->getThumbnail()]);

            if ($file) {
                return $this->fileSerializer->serialize($file);
            }
        }

        return null;
    }

    /**
     * @param array      $data
     * @param Event|null $event
     *
     * @return Event
     */
    public function deserialize(array $data, Event $event = null)
    {
        $this->sipe('id', 'setUuid', $data, $event);
        $this->sipe('title', 'setTitle', $data, $event);
        $this->sipe('display.color', 'setPriority', $data, $event);
        $this->sipe('allDay', 'setAllDay', $data, $event);
        $this->sipe('description', 'setDescription', $data, $event);
        $this->sipe('meta.done', 'setIsTaskDone', $data, $event);
        $this->sipe('isEditable', 'setIsEditable', $data, $event);

        if (isset($data['meta'])) {
            if (isset($data['meta']['type'])) {
                $event->setIsTask('task' === $data['meta']['type']);
            }
        }

        if (isset($data['thumbnail']) && isset($data['thumbnail']['url'])) {
            $event->setThumbnail($data['thumbnail']['url']);
        }

        if (isset($data['workspace'])) {
            /** @var Workspace $workspace */
            $workspace = $this->om->getObject($data['workspace'], Workspace::class);
            if ($workspace->getId()) {
                $event->setWorkspace($workspace);
            }
        }
        //owner set in crud create

        if (isset($data['start'])) {
            $event->setStart(DateNormalizer::denormalize($data['start']));
        }

        if (isset($data['end'])) {
            $event->setEnd(DateNormalizer::denormalize($data['end']));
        }

        return $event;
    }

    public function getClass()
    {
        return Event::class;
    }
}
