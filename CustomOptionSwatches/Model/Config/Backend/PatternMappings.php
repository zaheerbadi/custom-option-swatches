<?php
namespace Bodylanguage\CustomOptionSwatches\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\Exception\LocalizedException;

class PatternMappings extends ArraySerialized
{
    /**
     * @var string[]
     */
    private $allowedPatterns = [
        'pattern-zebra',
        'pattern-leopard',
        'pattern-camo',
    ];

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (!is_array($value)) {
            return parent::beforeSave();
        }

        $prepared = [];
        foreach ($value as $row) {
            if (!is_array($row)) {
                continue;
            }

            $label = isset($row['option_label']) ? trim((string) $row['option_label']) : '';
            $pattern = isset($row['pattern']) ? trim((string) $row['pattern']) : '';

            if ($label === '' || $pattern === '') {
                continue;
            }

            if (!in_array($pattern, $this->allowedPatterns, true)) {
                throw new LocalizedException(__('Please select a valid swatch pattern.'));
            }

            $prepared[] = [
                'option_label' => $label,
                'pattern' => $pattern,
            ];
        }

        $this->setValue($prepared);

        return parent::beforeSave();
    }
}
