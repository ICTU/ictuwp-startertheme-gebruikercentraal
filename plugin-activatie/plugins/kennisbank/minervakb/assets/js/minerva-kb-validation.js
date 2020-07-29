/**
 * Project: Minerva KB
 * Copyright: 2015-2016 @KonstruktStudio
 */
(function($) {

    var GLOBAL_DATA = window.MinervaKB;
    var ui = window.MinervaUI;
    var i18n = GLOBAL_DATA.i18n;

    var OPTION_PREFIX = GLOBAL_DATA.optionPrefix;

    var composeValidation = function() {
        var fns = Array.prototype.slice.call(arguments);

        return function(value) {
            return fns.map(function(fn) { return fn(value); }).filter(Boolean)[0];
        }
    };

    /**
     * Generic validation
     */
    var REGEXP = function(re, message) {
        return function(value) {
            if (!re.test(value)) {
                return message;
            }

            return false;
        }
    };

    var RANGE = function(from, to) {
        return function(value) {
            var parsedValue = parseFloat(value);

            if (parsedValue < from || parsedValue > to) {
                return 'Please enter value between ' + from + ' and ' + to;
            }
        }
    };

    var MIN = function(from) {
        return function(value) {
            var parsedValue = parseFloat(value);

            if (parsedValue < from) {
                return 'Please enter value not less than ' + from;
            }
        }
    };

    var MAX = function(to) {
        return function(value) {
            var parsedValue = parseFloat(value);

            if (parsedValue > to) {
                return 'Please enter value not more than ' + to;
            }
        }
    };

    var INTEGER_REGEXP = /^[0-9]*$/;
    var INTEGER_ERROR_MESSAGE = 'Please, use only positive numbers';
    var INTEGER = REGEXP(INTEGER_REGEXP, INTEGER_ERROR_MESSAGE);

    var SIGNED_INTEGER_REGEXP = /^\-?[0-9]*$/;
    var SIGNED_INTEGER_ERROR_MESSAGE = 'Please, use only numbers';
    var SIGNED_INTEGER = REGEXP(SIGNED_INTEGER_REGEXP, SIGNED_INTEGER_ERROR_MESSAGE);

    var FLOAT_REGEXP = /^[0-9]*\.?[0-9]+$/;
    var FLOAT_ERROR_MESSAGE = 'Please, use only floating point numbers';
    var FLOAT = REGEXP(FLOAT_REGEXP, FLOAT_ERROR_MESSAGE);

    var PERCENT = composeValidation(
        INTEGER,
        RANGE(0, 100)
    );

    var WP_POST_LIMIT = composeValidation(
        SIGNED_INTEGER,
        MIN(-1)
    );

    var SLUG_REGEXP = /^[a-zA-Z0-9_\-]{1,}$/;
    var SLUG_ERROR_MESSAGE = 'Only latin characters, dashes, underscore and numbers are allowed';
    var WP_SLUG = REGEXP(SLUG_REGEXP, SLUG_ERROR_MESSAGE);

    var CSS_OPACITY = composeValidation(
        FLOAT,
        RANGE(0, 1)
    );

    var ID_LIST = REGEXP(/^[0-9,\s]*$/, 'Please, use comma separated list of IDs, for ex. 12,20,125');

    /**
     * All settings inputs validation
     */
    var validationRules = {
        /**
         * Home: Topics
         */
        home_topics_limit: WP_POST_LIMIT,
        home_topics_articles_limit: WP_POST_LIMIT,
        /**
         * Home: Search
         */
        search_container_gradient_opacity: CSS_OPACITY,
        search_container_image_pattern_opacity: CSS_OPACITY,
        /**
         * General
         */
        content_width: PERCENT,
        /**
         * FAQ
         */
        faq_slug: WP_SLUG,
        faq_scroll_offset: SIGNED_INTEGER,
        /**
         * Glossary
         */
        glossary_term_bg_opacity: CSS_OPACITY,
        /**
         * Search
         */
        search_delay: INTEGER,
        search_needle_length: INTEGER,
        live_search_excerpt_length: INTEGER,
        search_results_per_page: WP_POST_LIMIT,
        /**
         * Фкешсду мersions
         */
        versions_slug: WP_SLUG,
        /**
         * Article search
         */
        article_search_container_gradient_opacity: CSS_OPACITY,
        article_search_container_image_pattern_opacity: CSS_OPACITY,
        /**
         * Topics
         */
        search_results_excerpt_length: INTEGER,
        topic_articles_per_page: WP_POST_LIMIT,
        /**
         * Topic search
         */
        topic_search_container_gradient_opacity: CSS_OPACITY,
        topic_search_container_image_pattern_opacity: CSS_OPACITY,
        /**
         * Tags
         */
        tag_articles_per_page: WP_POST_LIMIT,
        /**
         * Ratings
         */
        rating_prevent_multiple_interval: INTEGER,
        /**
         * Floating helper
         */
        fh_show_delay: INTEGER,
        fh_hide_on_pages_ids: ID_LIST,
        fh_show_on_pages_ids: ID_LIST,
        /**
         * CPT
         */
        article_slug: WP_SLUG,
        category_slug: WP_SLUG,
        tag_slug: WP_SLUG
    };

    function setupValidation() {
        for(var name in validationRules) {
            (function(name){
                var $control = $('.fn-control[name=' + OPTION_PREFIX + name + ']');

                if (!$control.length) {
                    return;
                }

                $control.on('change', function(e) {
                    var el = e.currentTarget;
                    var $el = $(el);
                    var $wrap = $el.parent();
                    var value = el.value;
                    var error = validationRules[name](value);

                    $wrap.find('.js-mkb-validation-error').remove();

                    if (error) {
                        $el.addClass('mkb-input-invalid');
                        $wrap.append('<div class="mkb-validation-error js-mkb-validation-error">' + error + '</div>');
                    } else {
                        $el.removeClass('mkb-input-invalid');
                    }
                });
            }(name));
        }
    }

    /**
     * Init
     */
    function init() {
        setupValidation();
    }

    $(document).ready(init);
})(jQuery);