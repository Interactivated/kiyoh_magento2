<?php
/**
 * My own options
 *
 */
namespace Interactivated\Customerreview\Adminhtml\Model\System\Config\Source;

class Reviewevents
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label'=>__('')],
            ['value' => 'Shipping', 'label'=>__('Shipping')],
            ['value' => 'Purchase', 'label'=>__('Purchase')],
            ['value' => 'Orderstatus', 'label'=>__('Order status change')],
        ];
    }
}
