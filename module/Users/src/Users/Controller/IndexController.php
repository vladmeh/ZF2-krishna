<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function registerAction()
    {
        return new ViewModel();
    }

    public function loginAction()
    {
        return new ViewModel();
    }


}

