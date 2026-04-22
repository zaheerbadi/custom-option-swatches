<?php
namespace Bodylanguage\CustomOptionSwatches\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class PatternMappings extends AbstractFieldArray
{
    /**
     * @var PatternOptions|null
     */
    private $patternRenderer;

    /**
     * @inheritDoc
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'option_label',
            [
                'label' => __('Option Label'),
                'class' => 'required-entry',
            ]
        );
        $this->addColumn(
            'pattern',
            [
                'label' => __('Pattern'),
                'renderer' => $this->getPatternRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Pattern Mapping');
    }

    /**
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $pattern = $row->getData('pattern');

        if ($pattern) {
            $options['option_' . $this->getPatternRenderer()->calcOptionHash($pattern)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return PatternOptions
     */
    private function getPatternRenderer()
    {
        if ($this->patternRenderer === null) {
            $this->patternRenderer = $this->getLayout()->createBlock(
                PatternOptions::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->patternRenderer;
    }
}
