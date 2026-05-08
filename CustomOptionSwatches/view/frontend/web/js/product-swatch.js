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

        function resetSwatches() {
            $container.find('.color-swatch, .size-swatch')
                .removeClass('active')
                .attr('aria-pressed', 'false');

            $container.find('[data-role="swatch-box"]').each(function () {
                $(this).css({
                    borderColor: '#d1d5db',
                    boxShadow: '0 6px 16px rgba(15, 23, 42, 0.08)',
                    transform: 'translateY(0)'
                });
            });

            $container.find('[data-role="swatch-label"]').each(function () {
                $(this).css({
                    color: '#111827',
                    fontWeight: '600'
                });
            });

            $container.find('.size-swatch').each(function () {
                $(this).css({
                    borderColor: '#d1d5db',
                    backgroundColor: '#fff',
                    color: '#374151'
                });
            });
        }

        function highlightSwatch($swatch) {
            if (!$swatch.length) {
                return;
            }

            $swatch.addClass('active').attr('aria-pressed', 'true');

            if ($swatch.hasClass('color-swatch')) {
                $swatch.find('[data-role="swatch-box"]').css({
                    borderColor: '#60a5fa',
                    boxShadow: '0 0 0 4px rgba(96, 165, 250, 0.35), 0 10px 22px rgba(15, 23, 42, 0.18)',
                    transform: 'translateY(-1px)'
                });
                $swatch.find('[data-role="swatch-label"]').css({
                    color: '#2563eb',
                    fontWeight: '700'
                });
            } else if ($swatch.hasClass('size-swatch')) {
                $swatch.css({
                    borderColor: '#60a5fa',
                    backgroundColor: '#60a5fa',
                    color: '#fff'
                });
            }
        }

        function applyActiveStates(selectedValues) {
            resetSwatches();
            if (!selectedValues) {
                return;
            }
            if (!$.isArray(selectedValues)) {
                selectedValues = [selectedValues];
            }
            $.each(selectedValues, function (i, valueId) {
                var $active = $container.find('[data-value-id="' + valueId + '"]');
                highlightSwatch($active);
            });
        }

        function setActiveState(valueId) {
            applyActiveStates(valueId ? [valueId] : []);
        }

        $container.on('click', '.color-swatch, .size-swatch', function (event) {
            var valueId = String($(this).data('valueId'));
            var isMultiple = $select.prop('multiple');

            event.preventDefault();

            if (isMultiple) {
                var $option = $select.find('option[value="' + valueId + '"]');
                $option.prop('selected', !$option.prop('selected'));
            } else {
                $select.val(valueId);
                $select.find('option').prop('selected', false);
                $select.find('option[value="' + valueId + '"]').prop('selected', true);
            }

            $select.trigger('change');
        });

        $select.on('change', function () {
            var selectedValues = $(this).val();
            applyActiveStates(selectedValues);
        }).trigger('change');
    };
});
