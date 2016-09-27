<?php

namespace Users\Controller;

use Zend\File\Transfer\Adapter\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Users\Model\Upload;


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

    /**
     * @return mixed
     */
    public function getFileUploadLocation()
    {
        // Fetch Configuration from Module Config
        $config  = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }

    public function indexAction()
    {
        $this->layout('layout/myaccount');

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
        $this->layout('layout/myaccount');

        return new ViewModel(array(
            'form' => $this->getServiceLocator()->get('UploadForm')
        ));
    }

    public function processUploadAction()
    {
        $this->layout('layout/myaccount');

        $userTable = $this->getServiceLocator()->get('UserTable');
        $user_email = $this->getAuthService()->getStorage()->read();
        $user = $userTable->getUserByEmail($user_email);

        $request = $this->getRequest();

        //\Zend\Debug\Debug::dump($request->getPost());die();

        $form = $this->getServiceLocator()->get('UploadForm');

        if ($request->isPost()) {
            $upload = new Upload();
            $uploadFile    = $this->params()->fromFiles('fileupload');
            $form->setData($request->getPost());

            if ($form->isValid()) {
                // Fetch Configuration from Module Config
                $uploadPath    = $this->getFileUploadLocation();
                // Save Uploaded file
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

        return array('form' => $form);
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

    public function deleteShareAction()
    {
        $this->layout('layout/myaccount');
        $shareId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()
            ->get('UploadTable');

        $uploadId = $uploadTable->getSharing($shareId)->upload_id;
        $uploadTable->deleteSharing($shareId);

        return $this->redirect()->toRoute('users/upload-manager',
            array(
                'action' =>  'edit',
                'id' =>  $uploadId
            )
        );
    }

    public function editAction()
    {
        $this->layout('layout/myaccount');

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

    public function processUploadShareAction()
    {
        $this->layout('layout/myaccount');

        $uploadTable = $this->getServiceLocator()->get('UploadTable');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $userId = $request->getPost()->get('user_id');
            $uploadId = $request->getPost()->get('upload_id');
            $uploadTable->addSharing($uploadId, $userId);
            return $this->redirect()->toRoute('users/upload-manager',array(	'action' =>  'edit','id' =>  $uploadId));
        }
    }

    public function fileDownloadAction()
    {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $upload = $uploadTable->getUpload($uploadId);

        // Fetch Configuration from Module Config
        $uploadPath    = $this->getFileUploadLocation();
        $file = file_get_contents($uploadPath ."/" . $upload->filename);

        // Directly return the Response
        $response = $this->getEvent()->getResponse();
        $response->getHeaders()->addHeaders(array(
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment;filename="' .$upload->filename . '"',
        ));
        $response->setContent($file);

        return $response;
    }

    public function processUpdateAction()
    {
        $this->layout('layout/myaccount');

        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('users/upload-manager');
        }

        $post = $this->request->getPost();
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $upload = $uploadTable->getUpload($post->id);

        $form = $this->getServiceLocator()->get('UploadEditForm');
        $form->setData($post);

        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => true,
                'form'  => $form,
            ));
            $model->setTemplate('users/update-manager/edit');
            return $model;
        }

        $upload->label = $post->label;

        $this->getServiceLocator()->get('UploadTable')->saveUpload($upload);

        return $this->redirect()->toRoute('users/upload-manager',
            array(
                'action' =>  'edit',
                'id' =>  $upload->id
            )
        );

    }

}

