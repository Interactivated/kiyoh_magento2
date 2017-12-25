<?php

namespace Interactivated\Customerreview\Model;

use Magento\Framework\Model\AbstractModel;

class Customerreview extends AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customerreview/customerreview');
    }
}
