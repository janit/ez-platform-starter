<?php

namespace AppBundle\QueryType;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\QueryType\QueryType;

class MainMenuQueryType implements QueryType {

    /**
     * @param array $parameters
     * @return mixed
     */
    public function getQuery(array $parameters = [])
    {

        $parentLocationId = $parameters['parentLocationId'];

        $filter = new Query\Criterion\LogicalAnd([
            new Query\Criterion\ContentTypeIdentifier('blog'),
            new Query\Criterion\ParentLocationId($parentLocationId)
        ]);

        return new LocationQuery([
            'filter' => $filter,
        ]);

    }

    /**
     * @return mixed
     */
    public function getSupportedParameters()
    {
        return['parentLocationId'];
    }

    /**
     * @return mixed
     */
    public static function getName()
    {
        return 'MainMenu';
    }
}