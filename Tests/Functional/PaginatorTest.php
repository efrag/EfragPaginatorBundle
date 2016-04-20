<?php

namespace Efrag\Bundle\PaginatorBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PaginatorTest
 * @package Efrag\Bundle\PaginatorBundle\Tests\Functional
 */
class PaginatorTest extends WebTestCase
{
    /**
     * @var \Efrag\Bundle\PaginatorBundle\Service\Paginator
     */
    protected $paginator;

    public function setUp()
    {
        $client = $this->createClient();
        $this->paginator = $client->getContainer()->get('efrag_paginator');
    }

    public function testSinglePagePaginator()
    {
        $links = $this->paginator
            ->setRoutePath('app_search')
            ->setTotal(11)
            ->getLinks(1);

        $this->assertArrayHasKey('prev', $links, 'Missing the "Previous" link');
        $this->assertArrayHasKey('l0', $links, 'Missing the "1" link');
        $this->assertEquals($links['l0']['active'], 1, 'Page number 1 is not active');
        $this->assertArrayHasKey('next', $links, 'Missing the "Next" link');
        $this->assertCount(3, $links, '3 elements were expected in the list of links');
    }

    public function testMultiplePagePaginator()
    {
        $links = $this->paginator
            ->setRoutePath('app_search')
            ->setTotal(30)
            ->getLinks(1);

        $this->assertArrayHasKey('prev', $links, 'Missing the "Previous" link');
        $this->assertArrayHasKey('l0', $links, 'Missing the "1" link');
        $this->assertArrayHasKey('l1', $links, 'Missing the "2 link');
        $this->assertEquals($links['l0']['active'], 1, 'Page number 1 is not active');
        $this->assertArrayHasKey('next', $links, 'Missing the "Next" link');
        $this->assertCount(4, $links, '4 elements were expected in the list of links');
    }
}
