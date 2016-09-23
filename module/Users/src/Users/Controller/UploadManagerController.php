<?php

namespace Users\Controller;

use Users\Model\Upload;
use Zend\File\Transfer\Adapter\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;

class UploadManagerController extends AbstractActionController
{

    protected $_storage = null;

    protected $_authservice = null;

    /**
     * @return array|object
     */
    public function getAuthService()
    {
        if(!$this->_authservice){
            $this->_authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->_authservice;
    }

    public function getFileUploadLocation()
    {
        // Fetch Configuration from Module Config
        $config  = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }

    public function indexAction()
    {
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $userTable = $this->getServiceLocator()->get('UserTable');

        $userEmail = $this->getAuthService()->getStorage()->read();
        $user = $userTable->getUserByEmail($userEmail);

        return new ViewModel(
            array(
                'myUploads' => $uploadTable->getUploadsByUserId($user->id),
            )
        );
    }

    public function uploadAction()
    {
        return new ViewModel(array(
            'form' => $this->getServiceLocator()->get('UploadForm')
        ));
    }

    public function processUploadAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user_email = $this->getAuthService()->getStorage()->read();
        $user = $userTable->getUserByEmail($user_email);
        $form = $this->getServiceLocator()->get('UploadForm');
        $request = $this->getRequest();

        if($request->getPost()){
            $upload = new Upload();
            $uploadFile = $this->params()->fromFiles('fileupload');
            $form->setData($request->getPost());

            if($form->isValid()){
                $uploadPath = $this->getFileUploadLocation();
                //\Zend\Debug\Debug::dump($uploadPath);die();

                $adapter = new Http();
                $adapter->setDestination($uploadPath);

                if ($adapter->receive($uploadFile['name'])) {

                    $exchange_data = array();
                    $exchange_data['label'] = $request->getPost()->get('label');
                    $exchange_data['filename'] = $uploadFile['name'];
                    $exchange_data['user_id'] = $user->id;

                    $upload->exchangeArray($exchange_data);

                    $uploadTable = $this->getServiceLocator()->get('UploadTable');
                    $uploadTable->saveUpload($upload);

                    return $this->redirect()->toRoute('users/upload-manager' , array(
                        'action' =>  'index'
                    ));
                }
            }


        }
    }

    public function deleteAction()
    {
        $this->layout('layout/myaccount');
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()
            ->get('UploadTable');
        $upload = $uploadTable->getUpload($uploadId);
        $uploadPath    = $this->getFileUploadLocation();
        // Remove File
        unlink($uploadPath ."/" . $upload->filename);
        // Delete Records
        $uploadTable->deleteUpload($uploadId);

        return $this->redirect()->toRoute('users/upload-manager');
    }


}

