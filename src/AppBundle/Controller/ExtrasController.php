<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ExtrasController extends Controller
{

    public function listBlogArticlesAction($locationId)
    {

        $queryType = $this->get('ezpublish.query_type.registry')->getQueryType('BlogPosts');

        $query = $queryType->getQuery(['parentLocationId' => $locationId]);
        $menuItems = $this->get('ezpublish.api.service.search')->findLocations($query);

        return $this->render('sidebar/blogListing.html.twig', [
            'root_location_id' => $locationId,
            'blog_posts' => $menuItems->searchHits,
        ]);
    }
}
