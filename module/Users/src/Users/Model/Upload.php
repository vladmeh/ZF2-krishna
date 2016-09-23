<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2016, Alpha-Hydro
 *
 */

namespace Users\Model;


class Upload
{
    public $id;
    public $filename;
    public $label;
    public $user_id;

    function exchangeArray($data){
        $this->id		= (isset($data['id'])) ? $data['id'] : null;
        $this->filename		= (isset($data['filename'])) ? $data['filename'] : null;
        $this->label		= (isset($data['label'])) ? $data['label'] : null;
        $this->user_id		= (isset($data['user_id'])) ? $data['user_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}