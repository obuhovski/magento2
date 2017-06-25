<?php
namespace Ewave\BannerStaging\Api;
use Ewave\BannerStaging\Api\Data\BannerInterface;

/**
 * Interface PageStagingInterface
 * @api
 */
interface BannerStagingInterface
{
    /**
     * @param BannerInterface $page
     * @param string $version
     * @param array $arguments
     * @return bool
     */
    public function schedule(BannerInterface $page, $version, $arguments = []);

    /**
     * @param BannerInterface $page
     * @param string $version
     * @return bool
     */
    public function unschedule(BannerInterface $page, $version);
}
