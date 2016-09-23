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

class UserEditForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Edit User');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Full Name',
            ),
        ));


        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save'
            ),
        ));
    }
}