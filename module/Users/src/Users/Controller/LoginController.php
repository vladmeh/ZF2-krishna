<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController
{
    protected $_authservice;
    protected $_storage;

    public function getAuthService()
    {
        if (! $this->_authservice) {

            $this->_authservice = $this->getServiceLocator()->get('AuthService');
        }

        return $this->_authservice;
    }

    public function logoutAction()
    {
        $this->getAuthService()->clearIdentity();

        return $this->redirect()->toRoute('users/login');
    }

    public function indexAction()
    {
        $form = $this->getServiceLocator()->get('LoginForm');
        return new ViewModel(
            array('form' => $form)
        );
    }

    public function confirmAction()
    {
        return new ViewModel(array(
            'user_email' => $this->getAuthService()->getStorage()->read()
        ));
    }

    public function processAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('users/login');
        }

        $post = $this->request->getPost();

        $form = $this->getServiceLocator()->get('LoginForm');

        $form->setData($post);
        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => true,
                'form'  => $form,
            ));
            $model->setTemplate('users/login/index');
            return $model;
        }
        else {
            $this->getAuthService()->getAdapter()
                ->setIdentity($this->request->getPost('email'))
                ->setCredential($this->request->getPost('password'));
            $result = $this->getAuthService()->authenticate();

            if ($result->isValid()) {
                $this->getAuthService()->getStorage()->write($this->request->getPost('email'));
                return $this->redirect()->toRoute('users/login' , array(
                    'action' =>  'confirm'
                ));
            }
            else {
                $model = new ViewModel(array(
                    'error' => true,
                    'form'  => $form,
                ));
                $model->setTemplate('users/login/index');
                return $model;
            }
        }
    }
}

