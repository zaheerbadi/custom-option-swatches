define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        var $container = $(element),
            optionId = $container.data('optionId'),
            $field = $container.closest('.field'),
            $select = $('#select_' + optionId);

        if (!$select.length) {
            $select = $field.find('.custom-option-swatch-select select');
        }

        if (!$container.length || !$select.length) {
            return;
        }

        function setActiveState(valueId) {
            var selector = '.color-swatch[data-value-id="' + valueId + '"]',
                $activeSwatch = valueId ? $container.find(selector) : $();

            $container.find('.color-swatch')
                .removeClass('active')
                .attr('aria-pressed', 'false');

            if ($activeSwatch.length) {
                $activeSwatch.addClass('active').attr('aria-pressed', 'true');
            }
        }

        $container.on('click', '.color-swatch', function (event) {
            var valueId = String($(this).data('valueId'));

            event.preventDefault();
            setActiveState(valueId);
            $select.val(valueId);
            $select.find('option').prop('selected', false);
            $select.find('option[value="' + valueId + '"]').prop('selected', true);
            $select.trigger('change');
        });

        $select.on('change', function () {
            var selectedValue = $(this).val();
            setActiveState(selectedValue ? String(selectedValue) : '');
        }).trigger('change');
    };
});
