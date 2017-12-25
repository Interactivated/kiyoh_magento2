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

class Orderstatus
{
    /**
     * @var ProductMetadataInterface
     */
    protected $appProductMetadataInterface;

    /**
     * @var StatusFactory
     */
    protected $orderStatusFactory;

    /**
     * @var Config
     */
    protected $orderConfig;

    public function __construct(
        ProductMetadataInterface $appProductMetadataInterface,
        StatusFactory $orderStatusFactory,
        Config $orderConfig
    ) {
        $this->appProductMetadataInterface = $appProductMetadataInterface;
        $this->orderStatusFactory = $orderStatusFactory;
        $this->orderConfig = $orderConfig;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $magentoVersion = $this->appProductMetadataInterface->getVersion();
        if (version_compare($magentoVersion, '1.5', '>=')) {
            //version is 1.5 or greater
            $data = $this->orderStatusFactory->create()->getResourceCollection()->getData();
            foreach ($data as $key => $item) {
                $data[$key]['value'] = $item['status'];
            }
            return $data;
        } else {
            //version is below 1.5
            $data = $this->orderConfig->getStatuses();
            $dataArray = [];
            $i = 0;
            foreach ($data as $key => $item) {
                $dataArray[$i]['value'] = $key;
                $dataArray[$i]['label'] = $item;
                $i++;
            }
            return $dataArray;
        }
    }
}
