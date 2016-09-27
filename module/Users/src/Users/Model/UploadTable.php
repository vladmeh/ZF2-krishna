<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2016, Alpha-Hydro
 *
 */

namespace Users\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class UploadTable
{
    protected $_tableGateway;
    protected $_uploadSharingTableGateway;

    public function __construct(TableGateway $tableGateway, TableGateway $uploadSharingTableGateway)
    {
        $this->_tableGateway = $tableGateway;
        $this->_uploadSharingTableGateway = $uploadSharingTableGateway;
    }

    /**
     * @return ResultSet
     */
    public function fetchAll()
    {
        return $this->_tableGateway->select();
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getUpload($id)
    {
        $id  = (int) $id;
        $rowset = $this->_tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param Upload $upload
     * @throws \Exception
     */
    public function saveUpload(Upload $upload)
    {
        $data = array(
            'filename' => $upload->filename,
            'label' => $upload->label,
            'user_id' => $upload->user_id,
        );

        $id = (int)$upload->id;
        if($id == 0){
            $this->_tableGateway->insert($data);
        }
        else{
            if($this->getUpload($id)){
                $this->_tableGateway->update($data, array('id' => $id));
            }
            else{
                throw new \Exception('Upload ID does not exist');
            }
        }
    }

    /**
     * @param $id
     * @return $this
     */
    public function deleteUpload($id)
    {
        $this->_tableGateway->delete(array('id' => $id));
        return $this;
    }

    /**
     * @param $userId
     * @return ResultSet
     */
    public function getUploadsByUserId($userId)
    {
        $userId = (int)$userId;
        return $this->_tableGateway->select(
            array('user_id' => $userId)
        );
    }

    public function getSharing($id)
    {
        $id = (int)$id;
        $rowset = $this->_uploadSharingTableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function addSharing($uploadId, $userId)
    {
        $data = array(
            'upload_id' => (int)$uploadId,
            'user_id'  => (int)$userId,
        );

        try {
            $this->_uploadSharingTableGateway->insert($data);
        } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
            // Do nothing
        }
    }

    public function removeSharing($uploadId, $userId)
    {
        $data = array(
            'upload_id' => (int)$uploadId,
            'user_id'  => (int)$userId,
        );

        $this->_uploadSharingTableGateway->delete($data);
    }

    public function deleteSharing($shareId)
    {
        $this->_uploadSharingTableGateway->delete(array('id' => $shareId));
        return $this;
    }

    public function getSharedUsers($uploadId)
    {
        $uploadId  = (int) $uploadId;

        $rowset = $this->_uploadSharingTableGateway->select(array('upload_id' => $uploadId));

        return $rowset;
    }

    public function getSharedUploadsForUserId($userId)
    {
        $userId  = (int) $userId;

        $rowset = $this->_uploadSharingTableGateway->select(function (Select $select) use ($userId){
            $select->columns(array()) // no columns from main table
                ->where(array('uploads_sharing.user_id'=>$userId))
                ->join('uploads', 'uploads_sharing.upload_id = uploads.id');
        });

        return $rowset;
    }
}