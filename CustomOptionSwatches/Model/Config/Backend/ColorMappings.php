<?php
namespace Bodylanguage\CustomOptionSwatches\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\Exception\LocalizedException;

class ColorMappings extends ArraySerialized
{
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
            $color = strtoupper(isset($row['color']) ? trim((string) $row['color']) : '');

            if ($label === '' || $color === '') {
                continue;
            }

            if (!preg_match('/^#[0-9A-F]{6}$/', $color)) {
                throw new LocalizedException(
                    __('Each swatch color must be a valid 6-digit hex code like #FF00AA.')
                );
            }

            $prepared[] = [
                'option_label' => $label,
                'color' => $color,
            ];
        }

        $this->setValue($prepared);

        return parent::beforeSave();
    }
}
