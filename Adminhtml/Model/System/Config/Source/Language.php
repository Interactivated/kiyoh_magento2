<?php

namespace Interactivated\Customerreview\Adminhtml\Model\System\Config\Source;

class Language
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => ''],
            ['value' => '1', 'label' => __('Dutch (BE)')],
            ['value' => '2', 'label' => __('French')],
            ['value' => '3', 'label' => __('German')],
            ['value' => '4', 'label' => __('English')],
            ['value' => '5', 'label' => __('Netherlands')],
            ['value' => '6', 'label' => __('Danish')],
            ['value' => '7', 'label' => __('Hungarian')],
            ['value' => '8', 'label' => __('Bulgarian')],
            ['value' => '9', 'label' => __('Romanian')],
            ['value' => '10', 'label' => __('Croatian')],
            ['value' => '11', 'label' => __('Japanese')],
            ['value' => '12', 'label' => __('Spanish')],
            ['value' => '13', 'label' => __('Italian')],
            ['value' => '14', 'label' => __('Portuguese')],
            ['value' => '15', 'label' => __('Turkish')],
            ['value' => '16', 'label' => __('Norwegian')],
            ['value' => '17', 'label' => __('Swedish')],
            ['value' => '18', 'label' => __('Finnish')],
            ['value' => '20', 'label' => __('Brazilian Portuguese')],
            ['value' => '21', 'label' => __('Polish')],
            ['value' => '22', 'label' => __('Slovenian')],
            ['value' => '23', 'label' => __('Chinese')],
            ['value' => '24', 'label' => __('Russian')],
            ['value' => '25', 'label' => __('Greek')],
            ['value' => '26', 'label' => __('Czech')],
            ['value' => '29', 'label' => __('Estonian')],
            ['value' => '31', 'label' => __('Lithuanian')],
            ['value' => '33', 'label' => __('Latvian')],
            ['value' => '35', 'label' => __('Slovak')]
        ];
    }
}