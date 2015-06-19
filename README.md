open-orchestra-model-bundle
===========================

The Open Orchestra model bundle

| Service       | Badge         |
| ------------- |:-------------:|
| Travis | [![Build Status](https://travis-ci.org/open-orchestra/open-orchestra-model-bundle.svg)](https://travis-ci.org/open-orchestra/open-orchestra-model-bundle) |
| CodeClimate (quality) | [![Code Climate](https://codeclimate.com/github/open-orchestra/open-orchestra-model-bundle/badges/gpa.svg)](https://codeclimate.com/github/open-orchestra/open-orchestra-model-bundle) |
| CodeClimate (coverage) | [![Test Coverage](https://codeclimate.com/github/open-orchestra/open-orchestra-model-bundle/badges/coverage.svg)](https://codeclimate.com/github/open-orchestra/open-orchestra-model-bundle/coverage) |
| Sensio insight | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/e6c86919-8c4a-4b5a-9619-7b671e4a4ae1/big.png)](https://insight.sensiolabs.com/projects/e6c86919-8c4a-4b5a-9619-7b671e4a4ae1) |
| VersionEye | [![Dependency Status](https://www.versioneye.com/user/projects/551e8799971f781c4800017c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/551e8799971f781c4800017c) |

Usage
-----

Once you have installed the bundle, you should activate the aggregator query bundle also in the AppKernel::registerBundles() :

    new OpenOrchestra\ModelBundle\OpenOrchestraModelBundle(),
    new Solution\MongoAggregationBundle\SolutionMongoAggregationBundle(),

This will allow you to create aggregation query in your repositories by using the method :

    AbstractRepository::createAggregationQuery()

With the aggregation, mongo does not return the original document, but by using the `$$ROOT` parameter, you can add all 
the fields of the document to the response.

To hydrate all the documents using the database result, use the method :

    AbstractRepository::hydrateAggregateQuery()
