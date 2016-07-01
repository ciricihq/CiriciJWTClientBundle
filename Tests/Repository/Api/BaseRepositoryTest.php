<?php

namespace Cirici\JWTClientBundle\Tests\Repository\Api;

class BaseRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    protected function setUp()
    {
        require_once __DIR__.'/../../App/AppKernel.php';
        $kernel = new \AppKernel('test', true);
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }

    public function testRepositoryServiceInstance()
    {
        $baseRepo = $this->container->get('project.repository.api');
        $this->assertInstanceOf('Cirici\JWTClientBundle\Repository\Api\BaseRepository', $baseRepo);
    }
}
