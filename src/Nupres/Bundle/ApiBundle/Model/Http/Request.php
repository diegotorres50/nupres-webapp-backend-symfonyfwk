<?php

namespace Nupres\Bundle\ApiBundle\Model\Http;

use Symfony\Component\HttpFoundation\RequestStack;

//https://symfony.com/doc/2.8/service_container/request.html

class Request
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getCurrentRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function getQueryString($param)
    {
        return $this->getCurrentRequest()->query->get($param);
    }
}
