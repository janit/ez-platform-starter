<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MenuController extends Controller
{

    public function mainMenuAction($currentLocationId)
    {

        $configResolver = $this->get('ezpublish.config.resolver');

        $rootLocationId = $configResolver->getParameter('content.tree_root.location_id');

        $queryType = $this->get('ezpublish.query_type.registry')->getQueryType('MainMenu');

        $query = $queryType->getQuery(['parentLocationId' => $rootLocationId]);
        $menuItems = $this->get('ezpublish.api.service.search')->findLocations($query);

        return $this->render('menu/main.html.twig', [
            'root_location_id' => $rootLocationId,
            'current_location_id' => $currentLocationId,
            'menu_items' => $menuItems->searchHits,
        ]);
    }
}
