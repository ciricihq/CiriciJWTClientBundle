<?php

namespace Cirici\JWTClientBundle\Tests\Repository\Api;

use Cirici\JWTClientBundle\Tests\Repository\Api\BaseTestSuite;

class BaseRepositoryTest extends BaseTestSuite
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRepositoryServiceInstance()
    {
        $baseRepo = $this->getContainer()->get('project.repository.api');
        $this->assertInstanceOf('Cirici\JWTClientBundle\Repository\Api\BaseRepository', $baseRepo);
    }
}
