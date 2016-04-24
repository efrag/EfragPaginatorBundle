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

    public function invalidIntegerSettings()
    {
        $cases = [];

        $cases[] = [null];
        $cases[] = [1.1];
        $cases[] = [false];
        $cases[] = ['1.0'];

        return $cases;
    }

    /**
     * @dataProvider invalidIntegerSettings
     * @expectedException \InvalidArgumentException
     *
     * @param $perPage
     */
    public function testInvalidPerPageSettingsThrowException($perPage)
    {
        $this->paginator->setPerPage($perPage);
    }

    /**
     * @dataProvider invalidIntegerSettings
     * @expectedException \InvalidArgumentException
     *
     * @param $total
     */
    public function testInvalidTotalSettingsThrowException($total)
    {
        $this->paginator->setTotal($total);
    }

    /**
     * @dataProvider invalidIntegerSettings
     * @expectedException \InvalidArgumentException
     *
     * @param $page
     */
    public function testInvalidPageRequestThrowsException($page)
    {
        $links = $this->paginator->setRoutePath('app_search')->setPerPage(10)->setTotal(20)->getLinks($page);
    }

    public function previousLinkCases()
    {
        $cases = [];

        $cases[] = ['app_search', [], 10, 10, 1, '/object/search'];
        $cases[] = ['app_search', [], 10, 20, 1, '/object/search?page=1&pp=10'];
        $cases[] = ['app_search', [], 10, 20, 2, '/object/search?page=1&pp=10'];
        $cases[] = ['app_search', [], 10, 30, 3, '/object/search?page=2&pp=10'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 10, 1, '/object/foo/search'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 20, 1, '/object/foo/search?page=1&pp=10'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 20, 2, '/object/foo/search?page=1&pp=10'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 30, 3, '/object/foo/search?page=2&pp=10'];

        return $cases;
    }

    /**
     * @dataProvider previousLinkCases
     *
     * @param string $route The name of the route we use to generate the search URIs
     * @param array $parameters The array of parameters required to generate the search URIs
     * @param integer $perPage The maximum number of search results per page
     * @param integer $total The total number of results returned
     * @param integer $curPage The current page number
     * @param string $outcome The expected URI
     */
    public function testPreviousLink($route, array $parameters, $perPage, $total, $curPage, $outcome)
    {
        $links = $this->paginator
            ->setRoutePath($route)
            ->setRoutePathParameters($parameters)
            ->setPerPage($perPage)
            ->setTotal($total)
            ->getLinks($curPage);

        $this->assertArrayHasKey('prev', $links, 'Missing "Previous" link');
        $this->assertEquals($outcome, $links['prev']['location'], 'The "Previous" link is not set correctly');
    }

    public function nextLinkCases()
    {
        $cases = [];

        $cases[] = ['app_search', [], 10, 10, 1, '/object/search'];
        $cases[] = ['app_search', [], 10, 20, 1, '/object/search?page=2&pp=10'];
        $cases[] = ['app_search', [], 10, 20, 2, '/object/search?page=2&pp=10'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 10, 1, '/object/foo/search'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 20, 1, '/object/foo/search?page=2&pp=10'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 20, 2, '/object/foo/search?page=2&pp=10'];

        return $cases;
    }

    /**
     * @dataProvider nextLinkCases
     *
     * @param string $route The name of the route we use to generate the search URIs
     * @param array $parameters The array of parameters required to generate the search URIs
     * @param integer $perPage The maximum number of search results per page
     * @param integer $total The total number of results returned
     * @param integer $curPage The current page number
     * @param string $outcome The expected URI
     */
    public function testNextLink($route, $parameters, $perPage, $total, $curPage, $outcome)
    {
        $links = $this->paginator
            ->setRoutePath($route)
            ->setRoutePathParameters($parameters)
            ->setPerPage($perPage)
            ->setTotal($total)
            ->getLinks($curPage);

        $this->assertArrayHasKey('next', $links, 'Missing "Next" link');
        $this->assertEquals($outcome, $links['next']['location'], 'The "Next" link is not set correctly');
    }

    public function activeLinkCases()
    {
        $cases = [];

        $cases[] = ['app_search', [], 10, 10, 1, 3, 'l0'];
        $cases[] = ['app_search', [], 10, 20, 2, 4, 'l1'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 10, 1, 3, 'l0'];
        $cases[] = ['app_search_params', ['type' => 'foo'], 10, 20, 2, 4, 'l1'];

        return $cases;
    }

    /**
     * @dataProvider activeLinkCases
     *
     * @param string $route The name of the route we use to generate the search URIs
     * @param array $parameters The array of parameters required to generate the search URIs
     * @param integer $perPage The maximum number of search results per page
     * @param integer $total The total number of results returned
     * @param integer $curPage The current page number
     * @param integer $numElements The number of elements the outcome $links array is expected to have
     * @param string $activeKey The key of the $links array that contains the link that is currently active
     */
    public function testActiveLink($route, $parameters, $perPage, $total, $curPage, $numElements, $activeKey)
    {
        $links = $this->paginator
            ->setRoutePath($route)
            ->setRoutePathParameters($parameters)
            ->setPerPage($perPage)
            ->setTotal($total)
            ->getLinks($curPage);

        $active = [];
        for ($i = 0; $i < (count($links) - 2); $i++) {
            $key = 'l' . $i;

            if ($links['l' . $i]['active'] === 1) {
                $active[] = $key;
            }
        }

        $this->assertCount($numElements, $links, 'The $links array does not match the expected count');
        $this->assertCount(1, $active, 'There were more than one active links detected');
        $this->assertEquals($activeKey, array_pop($active), 'Unexpected active link');
    }

    public function testExactCountOfVisiblePages()
    {
        $links = $this->paginator->setRoutePath('app_search')->setPerPage(10)->setTotal(90)->getLinks(5);

        $this->assertCount(11, $links, 'We should have exactly 11 pages showing up in this case');
    }

    public function testBothSidesOfVisiblePages()
    {
        $links = $this->paginator->setRoutePath('app_search')->setPerPage(10)->setTotal(210)->getLinks(11);

        $this->assertCount(15, $links, 'We should have exactly 15 pages showing up in this case');
        $this->assertEquals('1', $links['l0']['text'], 'First page is visible');
        $this->assertEquals('...', $links['l1']['text'], 'The second element is the dots');
        $this->assertEquals('...', $links['l11']['text'], 'The second to last element is the dots');
        $this->assertEquals('21', $links['l12']['text'], 'The last page is visible');

        $pageText = 7;
        for ($pageIndex = 2; $pageIndex <= 10; $pageIndex++) {
            $this->assertEquals($pageText, $links['l' . $pageIndex]['text']);
            $pageText += 1;
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testRoutePathIsInitialized()
    {
        $links = $this->paginator->setTotal(90)->getLinks(1);
    }

    /**
     * @expectedException \Exception
     */
    public function testTotalIsInitialized()
    {
        $links = $this->paginator->setRoutePath('app_search')->getLinks(1);
    }

    public function validSortCases()
    {
        $cases = [];

        $base = '/object/search?page=1&pp=10&sort=';

        $cases[] = ['app_search', ['id' => 'asc', 'name' => 'desc'], $base . urlencode('id:asc,name:desc')];
        $cases[] = ['app_search', ['id' => 'Asc', 'name' => 'DESC'], $base . urlencode('id:asc,name:desc')];

        return $cases;
    }

    public function invalidSortCases()
    {
        $cases = [];

        $base = '/object/search?page=1&pp=10&sort=';

        $cases[] = ['app_search', ['id' => '', 'name' => ''], $base];
        $cases[] = ['app_search', ['id' => 'test1', 'name' => 'test2'], $base];
        $cases[] = ['app_search', ['id' => 'test1', 'name' => 'asc'], $base . urlencode('name:asc')];

        return $cases;
    }

    /**
     * @dataProvider validSortCases
     *
     * @param string $route The named route to be used for generating the links
     * @param array $sort The array with the sorting parameters for this search
     * @param string $expectedUri The expected uri for the first page of results
     *
     * @throws \Exception
     */
    public function testValidSortingParameter($route, $sort, $expectedUri)
    {
        $links = $this->paginator->setRoutePath($route)->setTotal(100)->setPerPage(10)->setSort($sort)->getLinks();

        $this->assertEquals($expectedUri, $links['l0']['location'], 'The sorting has not been added correctly');
    }

    /**
     * @dataProvider validSortCases
     *
     * @param string $route The named route to be used for generating the links
     * @param array $sort The array with the sorting parameters for this search
     * @param string $expectedUri The expected uri for the first page of results
     *
     * @throws \Exception
     */
    public function testInvalidSortingParameter($route, $sort, $expectedUri)
    {
        $links = $this->paginator->setRoutePath($route)->setTotal(100)->setPerPage(10)->setSort($sort)->getLinks();

        $this->assertEquals($expectedUri, $links['l0']['location'], 'The sorting has not been added correctly');
    }
}
