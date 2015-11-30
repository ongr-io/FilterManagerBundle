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
 * Unit test for configuration tree.
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns sample minimal configuration for filter manager.
     *
     * @return array
     */
    protected function getBaseConfiguration()
    {
        return [
            'managers' => [
                'foo_filters' => [
                    'filters' => ['bar'],
                    'repository' => 'es.manager.default.baz',
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
                            'reset' => [
                                'include' => [],
                                'exclude' => ['size'],
                            ],
                        ],
                        'tags' => [],
                    ],
                ],
                'sort' => [
                    'sorting' => [
                        'request_field' => 'sort',
                        'choices' => [],
                        'tags' => [],
                    ],
                ],
                'pager' => [
                    'paging' => ['request_field' => 'page', 'tags' => []],
                ],
                'range' => [
                    'range' => ['request_field' => 'range', 'tags' => [], 'inclusive' => false],
                ],
                'date_range' => [
                    'date' => ['request_field' => 'date_range', 'field' => 'date', 'tags' => [], 'inclusive' => false],
                ],
                'choice' => [
                    'single_choice' => ['request_field' => 'choice', 'tags' => ['badged']],
                ],
                'variant' => [
                    'variant' => ['request_field' => null, 'tags' => []],
                ],
            ],
        ];
    }

    /**
     * Data provider for testConfiguration().
     *
     * @return array
     */
    public function getTestConfigurationData()
    {
        $cases = [];

        $baseConfig = $this->getBaseConfiguration();
        $expectedBaseConfig = $baseConfig;
        $expectedBaseConfig['filters']['pager']['paging']['count_per_page'] = 10;
        $expectedBaseConfig['filters']['pager']['paging']['max_pages'] = 8;
        $expectedBaseConfig['filters']['document_field'] = [];
        $expectedBaseConfig['filters']['choice'] = [
            'single_choice' => ['request_field' => 'choice', 'tags' => ['badged']],
        ];
        $expectedBaseConfig['filters']['multi_choice'] = [];
        $expectedBaseConfig['filters']['fuzzy'] = [];

        // Base configuration with default values.
        $cases['base_config_default_values'] = [
            $baseConfig,
            $expectedBaseConfig,
        ];

        // Normalize relations string to array.
        $customConfig = $baseConfig;
        $customConfig['filters']['match']['phrase']['relations']['search']['include'] = 'color';
        $customConfig['filters']['match']['phrase']['relations']['reset']['exclude'] = 'size';
        $cases['normalize_relations_field'] = [
            $customConfig,
            $expectedBaseConfig,
        ];

        // Set choice field name as label if label is missing.
        $customConfig = $expectedBaseConfig;
        $customConfig['filters']['sort']['sorting']['choices'][0] = ['field' => 'test'];
        $expectedConfig = $customConfig;
        $expectedConfig['filters']['choice'] = ['single_choice' => ['request_field' => 'choice', 'tags' => ['badged']]];
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['label'] = 'test';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['default'] = false;
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['order'] = 'asc';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['mode'] = null;
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][0]['field'] = 'test';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][0]['order'] = 'asc';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][0]['mode'] = null;
        unset($customConfig['filters']['document_field']);
        unset($customConfig['filters']['multi_choice']);
        unset($customConfig['filters']['fuzzy']);
        $cases['choice_name_as_label'] = [
            $customConfig,
            $expectedConfig,
        ];

        // Sorting by multiple fields.
        $customConfig = $expectedBaseConfig;
        $customConfig['filters']['sort']['sorting']['choices'][0]['fields'][0]['field'] = 'price';
        $customConfig['filters']['sort']['sorting']['choices'][0]['fields'][1]['field'] = 'date';
        $customConfig['filters']['sort']['sorting']['choices'][0]['fields'][1]['order'] = 'desc';
        $expectedConfig = $customConfig;
        $expectedConfig['filters']['choice'] = ['single_choice' => ['request_field' => 'choice', 'tags' => ['badged']]];
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['label'] = 'price';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['default'] = false;
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['order'] = 'asc';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['mode'] = null;
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][0]['field'] = 'price';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][0]['order'] = 'asc';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][0]['mode'] = null;
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][1]['field'] = 'date';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][1]['order'] = 'desc';
        $expectedConfig['filters']['sort']['sorting']['choices'][0]['fields'][1]['mode'] = null;
        unset($customConfig['filters']['document_field']);
        unset($customConfig['filters']['multi_choice']);
        unset($customConfig['filters']['fuzzy']);
        $cases['sorting_multiple_fields'] = [
            $customConfig,
            $expectedConfig,
        ];

        // Allow to skip "managers" config.
        $customConfig = $expectedBaseConfig;
        unset($customConfig['managers']);
        $expectedConfig = $customConfig;
        unset($customConfig['filters']['document_field']);
        unset($customConfig['filters']['multi_choice']);
        unset($customConfig['filters']['fuzzy']);
        $expectedConfig['managers'] = [];

        $cases['allow_to_skip_managers_config'] = [
            $customConfig,
            $expectedConfig,
        ];

        return $cases;
    }

    /**
     * Tests if expected default values are added.
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
     * Data provider for testConfigurationException().
     *
     * @return array
     */
    public function getTestConfigurationExceptionData()
    {
        $cases = [];

        // Incomplete manager config, missing "repository".
        $config = $this->getBaseConfiguration();
        unset($config['managers']['foo_filters']['repository']);
        $cases['require_manager_repository'] = [
            $config,
            'The child node "repository" at path "ongr_filter_manager.managers.foo_filters" must be configured.',
        ];

        // Empty "filters" config.
        $config = $this->getBaseConfiguration();
        $config['filters'] = null;
        $cases['empty_filters_config'] = [
            $config,
            'Invalid configuration for path "ongr_filter_manager.filters": At least single filter must be configured.',
        ];

        // Incomplete "filters" config, no filters specified under filter type.
        $config = $this->getBaseConfiguration();
        $config['filters']['match'] = null;
        $cases['incomplete_filters_config'] = [
            $config,
            'The path "ongr_filter_manager.filters.match" should have at least 1 element(s) defined.',
        ];

        // Incomplete "filters" config, "request_field" missing.
        $config = $this->getBaseConfiguration();
        unset($config['filters']['match']['phrase']['request_field']);
        $cases['request_field_missing'] = [
            $config,
            'The child node "request_field" at path "ongr_filter_manager.filters.match.phrase" must be configured.',
        ];

        // Incomplete relations config.
        $config = $this->getBaseConfiguration();
        $config['filters']['match']['phrase']['relations']['search']['include'] = [];
        $cases['incomplete_relations_config'] = [
            $config,
            'Invalid configuration for path "ongr_filter_manager.filters.match.phrase.relations.search": ' .
            'Relation must have "include" or "exclude" fields specified.',
        ];

        // Incorrect relations specified.
        $config = $this->getBaseConfiguration();
        $config['filters']['match']['phrase']['relations']['search']['exclude'] = ['foo'];
        $cases['incorrect_relations_config'] = [
            $config,
            'Invalid configuration for path "ongr_filter_manager.filters.match.phrase.relations.search": ' .
            'Relation must have only "include" or "exclude" fields specified.',
        ];

        // Incorrect type of sorting specified.
        $config = $this->getBaseConfiguration();
        $config['filters']['choice']['single_choice']['sort']['type'] = 'test';
        $cases['incorrect_sorting_type'] = [
            $config,
            'The value "test" is not allowed for path "ongr_filter_manager.filters.choice.single_choice.sort.type"' .
            '. Permissible values: "_term", "_count"',
        ];

        // Sorting fields are not set.
        $config = $this->getBaseConfiguration();
        $config['filters']['sort']['sorting']['choices'][0]['label'] = 'test';
        $cases['sorting_fields_not_set'] = [
            $config,
            'The child node "fields" at path "ongr_filter_manager.filters.sort.sorting.choices.0" must be configured.',
        ];

        return $cases;
    }

    /**
     * Tests if expected default values are added.
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
