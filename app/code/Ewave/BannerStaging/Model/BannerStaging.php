<?php
namespace Ewave\BannerStaging\Model;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Ewave\BannerStaging\Api\BannerStagingInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\ValidatorException;
use Magento\Staging\Model\ResourceModel\Db\CampaignValidator;

/**
 * Class BannerStaging
 */
class BannerStaging implements BannerStagingInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var CampaignValidator
     */
    protected $campaignValidator;

    /**
     * BannerStaging constructor.
     *
     * @param EntityManager $entityManager
     * @param CampaignValidator $campaignValidator
     */
    public function __construct(
        EntityManager $entityManager,
        CampaignValidator $campaignValidator
    ) {
        $this->entityManager = $entityManager;
        $this->campaignValidator = $campaignValidator;
    }

    /**
     * @param BannerInterface $banner
     * @param string $version
     * @param array $arguments
     * @return bool
     * @throws \Exception
     */
    public function schedule(BannerInterface $banner, $version, $arguments = [])
    {
        $previous = isset($arguments['origin_in']) ? $arguments['origin_in'] : null;
        if (!$this->campaignValidator->canBeScheduled($banner, $version, $previous)) {
            throw new ValidatorException(
                __(
                    'Future Update in this time range already exists. '
                    . 'Select a different range to add a new Future Update.'
                )
            );
        }
        $arguments['created_in'] = $version;
        return (bool)$this->entityManager->save($banner, $arguments);
    }

    /**
     * @param BannerInterface $banner
     * @param string $version
     * @return bool
     */
    public function unschedule(BannerInterface $banner, $version)
    {
        return (bool)$this->entityManager->delete(
            $banner,
            [
                'created_in' => $version
            ]
        );
    }
}
