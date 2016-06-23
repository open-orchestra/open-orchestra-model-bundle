open-orchestra-model-bundle
===========================

The Open Orchestra model bundle

| Service       | Badge         |
| ------------- |:-------------:|
| Travis | [![Build Status](https://travis-ci.org/open-orchestra/open-orchestra-model-bundle.svg?branch=1.1)](https://travis-ci.org/open-orchestra/open-orchestra-model-bundle) |
| Sensio insight | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/22e9c4d6-0a06-4570-9053-38236f69cdee/big.png)](https://insight.sensiolabs.com/projects/22e9c4d6-0a06-4570-9053-38236f69cdee) |
| Code Climate (Quality) | [![Code Climate](https://codeclimate.com/github/open-orchestra/open-orchestra-model-bundle/badges/gpa.svg)](https://codeclimate.com/github/open-orchestra/open-orchestra-model-bundle) |
| Code Climate (Code coverage) | [![Test Coverage](https://codeclimate.com/github/open-orchestra/open-orchestra-model-bundle/badges/coverage.svg)](https://codeclimate.com/github/open-orchestra/open-orchestra-model-bundle/coverage) |
| Latest Stable Version | [![Latest Stable Version](https://poser.pugx.org/open-orchestra/open-orchestra-model-bundle/v/stable)](https://packagist.org/packages/open-orchestra/open-orchestra-model-bundle) |
| Total Downloads | [![Total Downloads](https://poser.pugx.org/open-orchestra/open-orchestra-model-bundle/downloads)](https://packagist.org/packages/open-orchestra/open-orchestra-model-bundle) |

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
