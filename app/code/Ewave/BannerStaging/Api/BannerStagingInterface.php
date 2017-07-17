<?php

namespace Ewave\BannerStaging\Api;

use Ewave\BannerStaging\Api\Data\BannerInterface;

/**
 * Interface BannerStagingInterface
 * @api
 */
interface BannerStagingInterface
{
    /**
     * @param BannerInterface $banner
     * @param string $version
     * @param array $arguments
     * @return bool
     */
    public function schedule(BannerInterface $banner, $version, $arguments = []);

    /**
     * @param BannerInterface $banner
     * @param string $version
     * @return bool
     */
    public function unschedule(BannerInterface $banner, $version);
}
