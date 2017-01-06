<?php

namespace EasyAdminTranslationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{

    /**
     * Get dynamic css which hide easyadmin menu when there is a hideMenu request param
     *
     * @Route("/eatb/api/hide-easyadmin-menu-in-iframe.css")
     * @Method("GET")
     * @return Response
     */
    public function hideEasyadminMenuAction(Request $request)
    {
        // Verify if the request came from an hideMenu page
        $hideMenu = preg_match('/.*\?.*hideMenu=true/', $request->headers->get('referer'), $matches);

        // Build css rules
        $cssResponse = $this->render(
            'EasyAdminTranslationsBundle::hide-easyadmin-menu-in-iframe.css.twig',
            ['hideMenu' => $hideMenu]
        );

        // Set header to be handle as css by browser
        $cssResponse->headers->set('Content-Type', 'text/css');

        // Return css to browser
        return $cssResponse;
    }

}
