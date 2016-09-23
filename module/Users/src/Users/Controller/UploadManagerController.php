<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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

                $sharedUploads = $uploadTable->getSharedUploadsForUserId($user->id);
                $sharedUploadsList = array();
                foreach($sharedUploads as $sharedUpload) {
                    $uploadOwner = $userTable->getUser($sharedUpload->user_id);
                    $sharedUploadInfo = array();
                    $sharedUploadInfo['label'] = $sharedUpload->label;
                    $sharedUploadInfo['owner'] = $uploadOwner->name;
                    $sharedUploadsList[$sharedUpload->id] = $sharedUploadInfo;
                }

                return new ViewModel(
                    array(
                        'myUploads' => $uploadTable->getUploadsByUserId($user->id),
                        'sharedUploadsList' => $sharedUploadsList,
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

    public function editAction()
    {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $userTable = $this->getServiceLocator()->get('UserTable');

        // Upload Edit Form
        $upload = $uploadTable->getUpload($uploadId);
        $form = $this->getServiceLocator()->get('UploadEditForm');
        $form->bind($upload);

        // Shared Users List
        $sharedUsers = array();
        $sharedUsersResult = $uploadTable->getSharedUsers($uploadId);
        foreach($sharedUsersResult as $sharedUserRow ) {
            $user = $userTable->getUser($sharedUserRow->user_id);
            $sharedUsers[$sharedUserRow->id] =  $user->name;
        }

        // Add Additional Sharing
        $uploadShareForm = $this->getServiceLocator()->get('UploadShareForm');
        $allUsers = $userTable->fetchAll();
        $usersList = array();
        foreach($allUsers as $user) {
            $usersList[$user->id] = $user->name;
        }

        $uploadShareForm->get('upload_id')->setValue($uploadId);
        $uploadShareForm->get('user_id')->setValueOptions($usersList);

        return new ViewModel(array(
                'form' => $form,
                'upload_id' => $uploadId,
                'sharedUsers' => $sharedUsers,
                'uploadShareForm' => $uploadShareForm,
            )
        );
    }


}

