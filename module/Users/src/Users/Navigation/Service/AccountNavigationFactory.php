<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2016, Alpha-Hydro
 *
 */

namespace Users\Navigation\Service;
use Zend\Navigation\Service\DefaultNavigationFactory;

class AccountNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'account';
    }
}