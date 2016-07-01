<?php

namespace Cirici\JWTClientBundle\Tests\Repository\Api;

class BaseRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    protected function setUp()
    {
        $file = __DIR__.'/../../App/AppKernel.php';
        if (!file_exists($file))
        {
            $file = __DIR__.'/../../../../../../app/AppKernel.php';
            if (!file_exists($file))
                throw new RuntimeException('No kernel found.');
        }

        require_once $file;

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
