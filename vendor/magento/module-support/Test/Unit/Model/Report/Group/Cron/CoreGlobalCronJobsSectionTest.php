<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Cron;

use Magento\Support\Model\Report\Group\Cron\CronJobs;

class CoreGlobalCronJobsSectionTest extends AbstractCronJobSectionTest
{
    /**
     * @return void
     */
    public function testGenerate()
    {
        $this->report = $this->objectManagerHelper->getObject(
            'Magento\Support\Model\Report\Group\Cron\CoreGlobalCronJobsSection',
            ['cronJobs' => $this->cronJobsMock]
        );
        $this->cronJobsMock->expects($this->once())
            ->method('getAllCronJobs')
            ->willReturn($this->cronJobs);
        $this->cronJobsMock->expects($this->once())
            ->method('getCronJobsByType')
            ->with($this->cronJobs, CronJobs::TYPE_CORE)
            ->willReturn($this->cronJobs);

        $data = [$this->cronJobs['clear_cache']];
        $expectedResult = $this->getExpectedResult('Core Global Cron Jobs', $data);
        $this->assertEquals($expectedResult, $this->report->generate());
    }
}
