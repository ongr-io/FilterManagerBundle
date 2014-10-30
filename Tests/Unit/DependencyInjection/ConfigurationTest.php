<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\DependencyInjection;

use ONGR\FilterManagerBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Unit test for configuration tree
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns sample minimal configuration for filter manager
     *
     * @return array
     */
    protected function getBaseConfiguration()
    {
        return [
            'managers' => [
                'foo_filters' => [
                    'filters' => ['bar'],
                    'repository' => 'baz',
                ],
            ],
            'filters' => [
                'match' => [
                    'phrase' => [
                        'request_field' => 'q',
                        'relations' => [
                            'search' => [
                                'include' => ['color'],
                                'exclude' => [],
                            ],
                        ],
                    ],
                ],
                'sort' => [
                    'sorting' => [
                        'request_field' => 'sort',
                        'choices' => [],
                    ],
                ],
                'pager' => [
                    'paging' => [
                        'request_field' => 'page'
                    ]
                ],
                'range' => [
                    'range' => [
                        'request_field' => 'range'
                    ]
                ]
            ],
        ];
    }

    /**
     * Data provider for testConfiguration()
     *
     * @return array
     */
    public function getTestConfigurationData()
    {
        $cases = [];

        $baseConfig = $this->getBaseConfiguration();
        $expectedBaseConfig = $baseConfig;
        $expectedBaseConfig['filters']['pager']['paging']['count_per_page'] = 10;
        $expectedBaseConfig['filters']['document_field'] = [];
        $expectedBaseConfig['filters']['choice'] = [];
        $expectedBaseConfig['es_manager'] = 'default';

        // Case #0 Base configuration with default values.
        $cases[] = [$baseConfig, $expectedBaseConfig];

        // Case #1 Normalize relations string to array.
        $customConfig = $baseConfig;
        $customConfig['filters']['match']['phrase']['relations']['search']['include'] = 'color';
        $cases[] = [$customConfig, $expectedBaseConfig];

        // Case #2 Set choice field name as label if label is missing.
        $customConfig = $expectedBaseConfig;
        $customConfig['filters']['sort']['sorting']['choices'][0] = [
            'field' => 'test',
        ];
        $expectedConfig = $customConfig;
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['label'] = 'test';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['default'] = false;
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['order'] = 'asc';
        unset($customConfig['filters']['document_field']);
        unset($customConfig['filters']['choice']);
        $cases[] = [$customConfig, $expectedConfig];

        return $cases;
    }

    /**
     * Tests if expected default values are added
     *
     * @param array $config
     * @param array $expected
     *
     * @dataProvider getTestConfigurationData()
     */
    public function testConfiguration($config, $expected)
    {
        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [$config]);
        $this->assertEquals($expected, $processedConfig);
    }

    /**
     * Data provider for testConfigurationException()
     *
     * @return array
     */
    public function getTestConfigurationExceptionData()
    {
        $cases = [];

        // #0 Empty configuration
        $config = $this->getBaseConfiguration();
        unset($config['managers']);
        $cases[] = [
            $config,
            'The child node "managers" at path "ongr_filter_manager" must be configured.',
        ];

        // #1 Missing "managers" config
        $config = $this->getBaseConfiguration();
        $config['managers'] = null;
        $cases[] = [
            $config,
            'The path "ongr_filter_manager.managers" should have at least 1 element(s) defined.',
        ];

        // #2 Incomplete manager config, missing "filters"
        $config = $this->getBaseConfiguration();
        unset($config['managers']['foo_filters']['filters']);
        $cases[] = [
            $config,
            'The child node "filters" at path "ongr_filter_manager.managers.foo_filters" must be configured.',
        ];

        // #3 Incomplete manager config, "filters" is empty
        $config = $this->getBaseConfiguration();
        $config['managers']['foo_filters']['filters'] = null;
        $cases[] = [
            $config,
            'The path "ongr_filter_manager.managers.foo_filters.filters" should have at least 1 element(s) defined.',
        ];

        // #4 Incomplete manager config, missing "repository"
        $config = $this->getBaseConfiguration();
        unset($config['managers']['foo_filters']['repository']);
        $cases[] = [
            $config,
            'The child node "repository" at path "ongr_filter_manager.managers.foo_filters" must be configured.',
        ];

        // #5 Missing "filters" config
        $config = $this->getBaseConfiguration();
        unset($config['filters']);
        $cases[] = [
            $config,
            'The child node "filters" at path "ongr_filter_manager" must be configured.',
        ];

        // #6 Empty "filters" config
        $config = $this->getBaseConfiguration();
        $config['filters'] = null;
        $cases[] = [
            $config,
            'Invalid configuration for path "ongr_filter_manager.filters": At least single filter must be configured.',
        ];

        // #7 Incomplete "filters" config, no filters specified under filter type
        $config = $this->getBaseConfiguration();
        $config['filters']['match'] = null;
        $cases[] = [
            $config,
            'The path "ongr_filter_manager.filters.match" should have at least 1 element(s) defined.',
        ];

        // #8 Incomplete "filters" config, "request_field" missing
        $config = $this->getBaseConfiguration();
        unset($config['filters']['match']['phrase']['request_field']);
        $cases[] = [
            $config,
            'The child node "request_field" at path "ongr_filter_manager.filters.match.phrase" must be configured.',
        ];

        // #9 Incomplete relations config
        $config = $this->getBaseConfiguration();
        $config['filters']['match']['phrase']['relations']['search']['include'] = [];
        $cases[] = [
            $config,
            'Invalid configuration for path "ongr_filter_manager.filters.match.phrase.relations.search": ' .
            'Relation must have "include" or "exclude" fields specified.',
        ];

        // #10 Incorrect relations specified
        $config = $this->getBaseConfiguration();
        $config['filters']['match']['phrase']['relations']['search']['exclude'] = ['foo'];
        $cases[] = [
            $config,
            'Invalid configuration for path "ongr_filter_manager.filters.match.phrase.relations.search": ' .
            'Relation must have only "include" or "exclude" fields specified.',
        ];

        return $cases;
    }

    /**
     * Tests if expected default values are added
     *
     * @param array  $config
     * @param string $exceptionMessage
     *
     * @dataProvider getTestConfigurationExceptionData()
     */
    public function testConfigurationException($config, $exceptionMessage)
    {
        $this->setExpectedException(
            '\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            $exceptionMessage
        );

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [$config]);
    }
}
