<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2016, Alpha-Hydro
 *
 */

namespace Users\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class UserTable
{
    /**
     * @var TableGateway
     */
    protected $_tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->_tableGateway = $tableGateway;
    }

    public function saveUser(User $user)
    {
        $data = array(
            'email' => $user->email,
            'name'  => $user->name,
            'password'  => $user->password,
        );

        $id = (int)$user->id;
        if ($id == 0) {
            $this->_tableGateway->insert($data);
        } else {
            if ($this->getUser($id)) {
                $this->_tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('User ID does not exist');
            }
        }
    }


    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getUser($id)
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
     * @return ResultSet
     */
    public function fetchAll()
    {
        return $this->_tableGateway->select();
    }

    /**
     * @param $userEmail
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getUserByEmail($userEmail)
    {
        $rowset = $this->_tableGateway->select(array('email' => $userEmail));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $userEmail");
        }
        return $row;
    }

    public function deleteUser($id)
    {
        $this->_tableGateway->delete(array('id' => $id));
        return $this;
    }
}