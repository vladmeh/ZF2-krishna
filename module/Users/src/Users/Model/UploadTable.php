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
use Zend\Db\TableGateway\TableGateway;

class UploadTable
{
    protected $_tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->_tableGateway = $tableGateway;
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

}