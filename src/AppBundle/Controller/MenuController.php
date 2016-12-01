<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MenuController extends Controller
{

    /**
     * @Route("/main_menu/{rootLocationId}")
     * @Template()
     */

    public function mainMenuAction($rootLocationId)
    {

        $queryType = $this->get('ezpublish.query_type.registry')->getQueryType('MainMenu');

        $query = $queryType->getQuery(['parentLocationId' => $rootLocationId]);
        $menuItems = $this->get('ezpublish.api.service.search')->findLocations($query);

        return $this->render('menu/main.html.twig', [
            'location_id' => $rootLocationId,
            'menuItems' => $menuItems->searchHits
        ]);
    }
}
