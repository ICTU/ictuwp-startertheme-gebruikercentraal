/**
 * Project: Minerva KB
 * Copyright: 2015-2016 @KonstruktStudio
 */
(function($) {
    'use strict';

    function waitForElement(selector, callback) {
        var intervalHandle = setInterval(function() {
            if ($(selector.length)) {
                callback();
                return clearInterval(intervalHandle);
            }
        }, 1000);
    }

    /**
     * Toggles MinervaKB page settings
     */
    function setupTemplateSettings() {
        var updateCheckboxVisibility = function () {
            var $templateSettingsContainer = $('#mkb-page-template-meta-box-id');
            var $builderSettingsContainer = $('#mkb-page-meta-box-id');
            var $templateSelect = $('.editor-page-attributes__template select');

            $templateSettingsContainer.toggleClass('mkb-invisible', $templateSelect.val() !== 'minervakb-page-template');
            $builderSettingsContainer.toggleClass('mkb-invisible', $templateSelect.val() === 'minervakb-page-template');
        };

        $('body').on('change', '.editor-page-attributes__template select', updateCheckboxVisibility);

        updateCheckboxVisibility();
    }

    function init() {
        waitForElement('.editor-page-attributes__template select', setupTemplateSettings);
    }

    $(document).ready(init);
})(jQuery);