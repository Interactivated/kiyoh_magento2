<?php
/**
 * My own options
 *
 */
namespace Interactivated\Customerreview\Adminhtml\Model\System\Config\Source;

class Reviewservernew
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'klantenvertellen.nl', 'label'=>__('Klantenvertellen.nl')],
            ['value' => 'newkiyoh.com', 'label'=>__('Kiyoh.com (International)')],
        ];
    }
}
