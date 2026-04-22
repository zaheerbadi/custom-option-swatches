<?php
namespace Bodylanguage\CustomOptionSwatches\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PatternOptions implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'pattern-zebra', 'label' => __('Zebra')],
            ['value' => 'pattern-leopard', 'label' => __('Leopard')],
            ['value' => 'pattern-camo', 'label' => __('Camouflage')],
        ];
    }
}
