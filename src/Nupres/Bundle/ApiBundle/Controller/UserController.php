<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function addAction()
    {
        return $this->render('NupresApiBundle:User:add.html.twig', array(
            // ...
        ));
    }
}
