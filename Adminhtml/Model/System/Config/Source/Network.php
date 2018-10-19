<?php
/**
 * My own options
 *
 */
namespace Interactivated\Customerreview\Adminhtml\Model\System\Config\Source;

class Network
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'kiyoh', 'label'=>__('Kiyoh')],
            ['value' => 'klantenvertellen', 'label'=>__('Klantenvertellen')],
        ];
    }
}
