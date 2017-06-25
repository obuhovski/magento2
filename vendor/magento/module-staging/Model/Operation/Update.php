<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Operation;

use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\Operation\UpdateInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Staging\Model\Operation\Update\CampaignIntegrity;
use Magento\Staging\Model\ResourceModel\Db\ReadEntityVersion;
use Magento\Staging\Model\Entity\Action\UpdateVersion;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EventManager;
use Magento\Staging\Model\Operation\Update\CreateEntityVersion;
use Magento\Staging\Model\Operation\Update\UpdateEntityVersion;
use Magento\Framework\EntityManager\TypeResolver;
use Magento\Staging\Api\UpdateRepositoryInterface;
use Magento\Staging\Model\Operation\Update\RescheduleUpdate;
use Magento\Staging\Model\VersionInfoProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Update implements UpdateInterface
{
    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ReadEntityVersion
     */
    private $entityVersion;

    /**
     * @var UpdateVersion
     */
    private $updateVersion;

    /**
     * @var Update\CreateEntityVersion
     */
    private $createEntityVersion;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var UpdateRepositoryInterface
     */
    private $updateRepository;

    /**
     * @var UpdateEntityVersion
     */
    private $updateEntityVersion;

    /**
     * @var RescheduleUpdate
     */
    private $rescheduleUpdate;

    /**
     * @var CampaignIntegrity
     */
    private $campaignIntegrity;

    /**
     * @var VersionInfoProvider
     */
    private $versionInfoProvider;

    /**
     * @param TypeResolver $typeResolver
     * @param ReadEntityVersion $entityVersion
     * @param MetadataPool $metadataPool
     * @param UpdateVersion $updateVersion
     * @param CreateEntityVersion $createEntityVersion
     * @param UpdateEntityVersion $updateEntityVersion
     * @param ResourceConnection $resourceConnection
     * @param EventManager $eventManager
     * @param UpdateRepositoryInterface $updateRepository
     * @param RescheduleUpdate $rescheduleUpdate
     * @param CampaignIntegrity $campaignIntegrity
     * @internal param PermanentUpdateProcessorPool $permanentUpdateProcessorPool
     * @internal param TemporaryUpdateProcessorPool $temporaryUpdateProcessorPool
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        TypeResolver $typeResolver,
        ReadEntityVersion $entityVersion,
        MetadataPool $metadataPool,
        UpdateVersion $updateVersion,
        CreateEntityVersion $createEntityVersion,
        UpdateEntityVersion $updateEntityVersion,
        ResourceConnection $resourceConnection,
        EventManager $eventManager,
        UpdateRepositoryInterface $updateRepository,
        RescheduleUpdate $rescheduleUpdate,
        CampaignIntegrity $campaignIntegrity,
        VersionInfoProvider $versionInfoProvider
    ) {
        $this->typeResolver = $typeResolver;
        $this->entityVersion = $entityVersion;
        $this->metadataPool = $metadataPool;
        $this->updateVersion = $updateVersion;
        $this->createEntityVersion = $createEntityVersion;
        $this->resourceConnection = $resourceConnection;
        $this->eventManager = $eventManager;
        $this->updateRepository = $updateRepository;
        $this->updateEntityVersion = $updateEntityVersion;
        $this->rescheduleUpdate = $rescheduleUpdate;
        $this->campaignIntegrity = $campaignIntegrity;
        $this->versionInfoProvider = $versionInfoProvider;
    }

    /**
     * @param array $data
     * @return string|null
     */
    private function resolveVersion($data)
    {
        return isset($data['created_in']) ? $data['created_in'] : null;
    }

    /**
     * @param EntityMetadataInterface $metadata
     * @param string $entityType
     * @param object $entity
     * @param array $arguments
     * @throws \Exception
     */
    private function processUpdate(
        EntityMetadataInterface $metadata,
        $entityType,
        $entity,
        $arguments
    ) {
        $needReschedule = (isset($arguments['origin_in']) && $arguments['created_in'] != $arguments['origin_in']);
        if ($needReschedule) {
            $this->rescheduleUpdate->reschedule($arguments['origin_in'], $arguments['created_in'], $entity);
        }
        $hydrator = $this->metadataPool->getHydrator($entityType);
        $entityData = $hydrator->extract($entity);
        $identifier = $entityData[$metadata->getIdentifierField()];
        $version = $this->versionInfoProvider->getVersionInfo(
            $entity,
            $this->resolveVersion(array_merge($entityData, $arguments))
        );
        if (!isset($arguments['created_in'])) {
            $createdIn = array_key_exists('created_in', $entityData) ? $entityData['created_in'] : 1;
            $arguments['created_in'] = $createdIn;
        }
        $update = $this->updateRepository->get($arguments['created_in']);
        if ($version->getRowId() != null) {
            if ($update->getRollbackId()) {
                $arguments['updated_in'] = $update->getRollbackId();
            } else {
                $arguments['updated_in'] = $version->getUpdatedIn();
            }
            $arguments[$metadata->getLinkField()] = $version->getRowId();
            $this->updateEntityVersion->execute($entity, $arguments);
            if ($needReschedule) {
                $this->campaignIntegrity->createRollbackPoint($update, $entity);
            }
        } else {
            $this->updateVersion->execute($entityType, $identifier);
            $arguments['updated_in'] = ($update->getRollbackId()) ?:
                $this->entityVersion->getNextVersionId($entityType, $update->getId(), $identifier);
            $this->createEntityVersion->execute($entity, $arguments);
            $this->campaignIntegrity->createRollbackPoint($update, $entity);
        }
        $this->campaignIntegrity->synchronizeAffectedCampaigns($update, $entity);
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityType = $this->typeResolver->resolve($entity);
        $metadata = $this->metadataPool->getMetadata($entityType);

        $connection = $this->resourceConnection->getConnectionByName($metadata->getEntityConnectionName());
        $connection->beginTransaction();
        try {
            $this->eventManager->dispatch('entity_save_before', ['entity_type' => $entityType, 'entity' => $entity]);
            $this->eventManager->dispatchEntityEvent($entityType, 'save_before', ['entity' => $entity]);
            $this->processUpdate($metadata, $entityType, $entity, $arguments);
            $this->eventManager->dispatchEntityEvent($entityType, 'save_after', ['entity' => $entity]);
            $this->eventManager->dispatch(
                'entity_manager_save_after',
                [
                    'entity_type' => $entityType,
                    'entity' => $entity
                ]
            );
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
        return $entity;
    }
}
