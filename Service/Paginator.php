<?php

namespace Efrag\Bundle\PaginatorBundle\Service;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class Paginator
 * @package Efrag\Bundle\PaginatorBundle\Service
 */
class Paginator
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var integer
     */
    protected $perPage;

    /**
     * @var string
     */
    protected $routePath;

    /**
     * @var array
     */
    protected $routePathParameters = [];

    /**
     * @var integer
     */
    protected $total;

    public function __construct(RouterInterface $router, $perPage)
    {
        $this->router = $router;
        $this->perPage = $perPage;
    }

    /**
     * Sets the named route as defined in the routing configuration file
     *
     * @param string $routePath
     *
     * @return Paginator
     */
    public function setRoutePath($routePath)
    {
        $this->routePath = $routePath;

        return $this;
    }

    /**
     * Sets an array of route parameters in key=>value pairs as expected in the routing configuration file
     *
     * @param array $parameters
     *
     * @return Paginator
     */
    public function setRoutePathParameters(array $parameters)
    {
        $this->routePathParameters = $parameters;

        return $this;
    }

    /**
     * Sets the total number of results that the particular search has generated
     *
     * @param integer $total
     *
     * @return Paginator
     */
    public function setTotal($total)
    {
        if (!is_integer($total)) {
            throw new \InvalidArgumentException('The total number of results should be an integer value');
        }

        $this->total = abs((int) $total);

        return $this;
    }

    /**
     * Sets the desired number of results per page. This will override th default that is set in the settings
     *
     * @param integer $perPage
     *
     * @return Paginator
     */
    public function setPerPage($perPage)
    {
        if (!is_integer($perPage)) {
            throw new \InvalidArgumentException('The total number of results per page should be an integer value');
        }

        $this->perPage = $perPage;

        return $this;
    }

    /**
     * The main method of this class. It returns an array of the links for the current result set
     *
     * @param int $page
     *
     * @return array
     */
    public function getLinks($page = 1)
    {
        if (!is_integer($page)) {
            throw new \InvalidArgumentException('The current page needs to be an integer value');
        }

        $page = abs((int) $page);

        $totalPages = (int) ceil($this->total / $this->perPage);

        if ($totalPages > 1) {
            $links = $this->multiPageLinks($totalPages, $page);
        } else {
            $links = $this->onePageLinks();
        }

        return $links;
    }

    /**
     * @return array
     */
    protected function onePageLinks()
    {
        $links = array();

        $route = $this->router->generate($this->routePath, $this->routePathParameters, false);

        $links['prev'] = array(
            'location' => $route, 'text' => 'Previous', 'active' => 0
        );

        $links['l0'] = array(
            'location' => $route, 'text' => '1', 'active' => 1
        );

        $links['next'] = array(
            'location' => $route, 'text' => 'Next', 'active' => 0
        );

        return $links;
    }

    /**
     * @param $totalPages
     * @param $current
     * @param null $type
     * @return array
     */
    protected function setLinkParameters($totalPages, $current, $type = null)
    {
        $parameters = $this->routePathParameters;

        switch($type) {
            case 'previous':
                $parameters['page'] = ($current == 1) ? 1: $current - 1;
                break;
            case 'next':
                $parameters['page'] = ($current === $totalPages) ? $totalPages : $current + 1;
                break;
            default:
                $parameters['page'] = null;
                break;
        }

        $parameters['pp'] = $this->perPage;

        return $parameters;
    }

    /**
     * @param $totalPages
     * @param $current
     * @return array
     */
    protected function findVisiblePages($totalPages, $current)
    {
        $currentVisible = array();

        if ($totalPages > 8) {
            $currentVisible = array($current);

            // Find the pages to add links to from the beginning of our page numbers
            for ($i = 1; $i <= 4; $i++) {
                $previousPage = $current - $i;

                if ($current - $i > 0) {
                    array_unshift($currentVisible, $previousPage);
                }
            }

            if ($currentVisible[0] > 2) {
                array_unshift($currentVisible, '...');
                array_unshift($currentVisible, 1);
            } elseif ($currentVisible[0] == 2) {
                array_unshift($currentVisible, 1);
            }

            // Find the page links we need to add from the end of our page numbers
            for ($i = 1; $i <= 4; $i++) {
                $nextPage = $current + $i;

                if ($current + $i <= $totalPages) {
                    $currentVisible[] = $nextPage;
                }
            }

            if (end($currentVisible) < $totalPages - 2) {
                $currentVisible[] = '...';
                $currentVisible[] = $totalPages;
            } elseif (end($currentVisible) == 2) {
                $currentVisible[] = $totalPages;
            }
        } else {
            for ($i = 1; $i <= $totalPages; $i++) {
                $currentVisible[] = $i;
            }
        }

        return $currentVisible;
    }

    protected function multiPageLinks($totalPages, $current)
    {
        $links = array();

        $previousParameters = $this->setLinkParameters($totalPages, $current, 'previous');
        $links['prev'] = array(
            'location' => $this->router->generate($this->routePath, $previousParameters, false),
            'text' => 'Previous',
            'active' => 0
        );

        $visible = $this->findVisiblePages($totalPages, $current);

        for ($i = 0; $i < count($visible); $i++) {
            if ($visible[$i] == '...') {
                $links['l' . $i] = array('location' => '#', 'text' => $visible[$i], 'active' => 0);
            } else {
                $currentParameters = $this->setLinkParameters($totalPages, $current);
                $currentParameters['page'] = $visible[$i];

                $links['l'. $i] = array(
                    'location' => $this->router->generate($this->routePath, $currentParameters, false),
                    'text' => $visible[$i],
                    'active' => ($visible[$i] === $current) ? 1 : 0
                );
            }
        }

        // Get the routePathParameters for the previous link
        $nextParameters = $this->setLinkParameters($totalPages, $current, 'next');

        $links['next'] = array(
            'location' => $this->router->generate($this->routePath, $nextParameters, false),
            'text' => 'Next',
            'active' => 0
        );

        return $links;
    }
}