<?php

namespace Efrag\Bundle\PaginatorBundle\Tests\DependencyInjection;

use Efrag\Bundle\PaginatorBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigurationTest
 * @package Efrag\Bundle\PaginatorBundle\Tests\DependencyInjection
 */
class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function validConfigurations()
    {
        $configs = [];

        $configs[] = array(array());
        $configs[] = array(array('perPage' => 20));

        return $configs;
    }

    /**
     * @dataProvider validConfigurations
     * @param $config
     */
    public function testValidConfigurations($config)
    {
        $this->assertConfigurationIsValid(array($config));
    }

    public function invalidConfigurations()
    {
        $configs = [];

        $configs[] = array(array('per' => 20));
        $configs[] = array(array('perPage' => '20'));
        $configs[] = array(array('perPage' => 'config'));

        return $configs;
    }

    /**
     * @dataProvider invalidConfigurations
     */
    public function testInvalidConfigurations($config)
    {
        $this->assertConfigurationIsInvalid(array($config));
    }
}