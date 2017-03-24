# CHANGELOG

## v2.0.0 (2017-03-23)
- Added support for Elasticsearch 5.0
- Added a cache for repetitive search formations. #168
- Introduced event dispatching. #169
- Added `operator` and `fuzzyness` to `MatchFilter`. #171
- Added nested implementation to `MatchFilter`. #184
- Added `DynamicAggregateFilter`. #188
- Added `MultiDynamicAggregate`. #189
- Added `AggregateViewData` container.
- Added `OptionsAwareTrait`.
- Added `DocumentFieldAwareTrait` and `DocumentFieldAwareInterface`
- Added `RequestFieldAwareTrait` and `RequestFieldAwareInterface`
- `options` node was added to all filter configurations.
- Implemented showing zero choices in choice filters. #190

### Breaking changes
- Drop PHP 5.5 support. Now only PHP >=7.0 are supported.
- Drop Symfony 2.8 support. Now only Symfony >=3.0 are supported.
- Drop Elasticsearch 2.x support. Now only Elasticsearch >=5.0 are supported.
- Introduced `JMSSerializerBundle` to handle serialization, the bundle needs to be added to `AppKernel`.
- Removed all the filter factories.
- Changed the formation of filters in configuration. Read the docs for more information.
- `field` node in filter configurations was changed to `document_field`.
- Added required `type` node to filter configuration.
- Changed the filter formation process to make all custom filters stateless. Now instead of creating actual filters,
 custom filters act like filter types, just like standard ones, and must be described in bundle configuration.
- Removed `FuzzyFilter`, use `MatchFilter` instead. #214
- Added `isRelated` method to `FilterInterface`. #186

## v1.0.2 (2016-05-28)
- Fixed first-page pagination when page parameter is forced in controller

## v1.0.1 (2016-05-03)
- Fixed wrong namespace paths in DocumentField and FieldValue factories

## v1.0.0 (2016-03-24)
- Initial release