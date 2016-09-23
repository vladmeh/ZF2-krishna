<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2016, Alpha-Hydro
 *
 */

namespace Users\Form;

use Zend\Form\Form;


class UploadShareForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('UploadShare');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');

        $this->add(array(
            'name' => 'upload_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
            'options' => array(
                'label' => 'Upload',
            ),
        ));


        $this->add(array(
            'name' => 'user_id',
            'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
            ),
            'options' => array(
                'label' => 'User',
            ),
        ));


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Add Share'
            ),
        ));
    }
}