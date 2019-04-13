<?php

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Efrag\Bundle\PaginatorBundle\DependencyInjection\EfragPaginatorExtension;

class EfragPaginatorExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions() : array
    {
        return [
            new EfragPaginatorExtension()
        ];
    }

    public function testPerPageParameterIsSet()
    {
        $this->load(['perPage' => 15]);

        $this->assertContainerBuilderHasService('efrag_paginator');
        $this->assertContainerBuilderHasParameter('efrag_paginator.perPage', 15);
    }
}