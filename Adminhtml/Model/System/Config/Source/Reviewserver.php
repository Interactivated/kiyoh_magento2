<?php
/**
 * My own options
 *
 */
namespace Interactivated\Customerreview\Adminhtml\Model\System\Config\Source;

class Reviewserver
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'kiyoh.nl', 'label'=>__('Kiyoh Netherlands')],
            ['value' => 'kiyoh.com', 'label'=>__('Kiyoh International')],
        ];
    }
}
