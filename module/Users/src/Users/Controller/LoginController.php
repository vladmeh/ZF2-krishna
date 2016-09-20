<?php

namespace Users\Controller;

use Users\Form\LoginFilter;
use Users\Form\LoginForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController
{

    public function indexAction()
    {
        $form = new LoginForm();
        return new ViewModel(
            array('form' => $form)
        );
    }

    public function confirmAction()
    {
        return new ViewModel();
    }

    public function processAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute(NULL , array(
                'controller' => 'register',
                'action' =>  'index'
            ));
        }

        $post = $this->request->getPost();

        $form = new LoginForm();
        $inputFilter = new LoginFilter();
        $form->setInputFilter($inputFilter);

        $form->setData($post);
        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => true,
                'form'  => $form,
            ));
            $model->setTemplate('users/login/index');
            return $model;
        }

        return $this->redirect()->toRoute(NULL , array(
            'controller' => 'login',
            'action' =>  'confirm'
        ));
    }
}

