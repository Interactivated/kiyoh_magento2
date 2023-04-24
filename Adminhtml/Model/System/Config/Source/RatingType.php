<?php

/**
 * @category   Kiyoh
 * @package    interactivated_customerreview
 * @author     ModuleCreator
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Interactivated\Customerreview\Adminhtml\Model\System\Config\Source;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\Order\StatusFactory;

class RatingType
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label'=>__('Default')],
            ['value' => '12months', 'label'=>__('The average rating of the last 12 months')],
        ];
    }
}
