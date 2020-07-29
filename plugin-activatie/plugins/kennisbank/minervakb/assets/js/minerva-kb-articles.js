/**
 * Project: Minerva KB
 * Copyright: 2015-2016 @KonstruktStudio
 */
(function($) {
    'use strict';

    var GLOBAL_DATA = window.MinervaKB;
    var ARTICLE_DATA = GLOBAL_DATA.articleEdit;
    var ui = window.MinervaUI;
    var settings = GLOBAL_DATA.settings;
    var i18n = GLOBAL_DATA.i18n;

    function setupRelatedArticles($container) {
        var $addBtn = $container.find('#mkb_add_related_article');
        var $relatedContainer = $container.find('.js-mkb-related-articles');

        $addBtn.on('click', function(e) {
            e.preventDefault();

            var $related = $('<div class="mkb-related-articles__item state--edit js-mkb-related-articles-item"></div>');
            var $current = $(
                '<a class="mkb-related-current js-mkb-related-current" href="#" target="_blank" title="Open in new tab">' +
                    '<span class="js-mkb-related-current-title">Select article</span><i class="fa fa-external-link mkb-related-current-link-icon"></i>' +
                '</a>'
            );
            var $form = $(
                '<div class="mkb-related-article-search">' +
                    '<input type="text" class="js-mkb-related-article-search-input mkb-related-article-search-input" name="mkb_related_article_search" placeholder="Type to search for articles">' +
                    '<a href="#" class="mkb-related-edit-cancel js-mkb-related-edit-cancel">Cancel</a>' +
                    '<ul class="mkb-related-article-search-results js-mkb-related-article-search-results"></ul>' +
                '</div>'
            );
            var $edit = $('<a href="#" class="mkb-related-edit js-mkb-related-edit">Edit</a>');
            var $store = $('<input type="hidden" class="js-mkb-related-article-id mkb-related-articles__store" name="mkb_related_articles[]" value="" />');
            var $remove = $(
                '<a class="mkb-related-articles__item-remove js-mkb-related-remove mkb-unstyled-link" href="#">' +
                    '<i class="fa fa-close"></i>' +
                '</a>'
            );
            var $noRelatedMessage = $container.find('.js-mkb-no-related-message');

            $noRelatedMessage.length && $noRelatedMessage.remove();

            $related.append($current);
            $related.append($form);
            $related.append($edit);
            $related.append($store);
            $related.append($remove);

            $relatedContainer.append($related);

            $related.find('.js-mkb-related-edit').trigger('click');
        });

        $relatedContainer.sortable({
            'items': '.mkb-related-articles__item',
            'axis': 'y'
        });

        $relatedContainer.on('click', '.js-mkb-related-remove', function(e) {
            e.preventDefault();

            var $link = $(e.currentTarget);

            $link.parents('.mkb-related-articles__item').remove();

            if ($relatedContainer.find('.mkb-related-articles__item').length === 0) {
                $relatedContainer.append(
                    $('<div class="js-mkb-no-related-message mkb-no-related-message">' +
                        '<p>' + i18n['no-related'] + '</p>' +
                    '</div>'
                ));
            }
        });

        $relatedContainer.on('click', '.js-mkb-related-edit', function(e) {
            e.preventDefault();

            var $parent = $(e.currentTarget).parents('.js-mkb-related-articles-item');

            $parent.addClass('state--edit');

            $parent.find('.js-mkb-related-article-search-input').focus();
        });

        $relatedContainer.on('click', '.js-mkb-related-edit-cancel', function(e) {
            e.preventDefault();

            var $parent = $(e.currentTarget).parents('.js-mkb-related-articles-item');

            $parent.removeClass('state--edit');

            var $store = $parent.find('.js-mkb-related-article-id');

            if (!$store.val()) {
                $parent.remove();

                if ($relatedContainer.find('.mkb-related-articles__item').length === 0) {
                    $relatedContainer.append(
                        $(
                            '<div class="js-mkb-no-related-message mkb-no-related-message">' +
                                '<p>' + i18n['no-related'] + '</p>' +
                            '</div>'
                        )
                    );
                }
            }
        });

        $relatedContainer.on('input', '.js-mkb-related-article-search-input', function(e) {
            e.preventDefault();

            var $el = $(e.currentTarget);
            var needle =  e.currentTarget.value.trim();
            var $parent = $el.parents('.js-mkb-related-articles-item');
            var $results = $parent.find('.js-mkb-related-article-search-results');

            if (needle.length < 3) {
                $results.html('');
                return;
            }

            ui.fetch({
                action: 'mkb_kb_search',
                search: needle
            }).then(function(response) {
                var found = response.result;

                if (!found.length) {
                    return $results.html('<li><span class="mkb-related-not-found">Nothing found</span></li>');
                }

                $results.html(found.reduce(function(html, result) {
                    return html +
                        '<li>' +
                            '<a class="js-mkb-related-found" data-id="' + result.id + '" data-link="' + result.link+ '">' +
                                result.title +
                            '</a>' +
                        '</li>';
                }, ''));
            });
        });

        $relatedContainer.on('click', '.js-mkb-related-article-search-results a', function(e) {
            e.preventDefault();

            var selected = e.currentTarget;
            var id = selected.dataset.id;
            var title = selected.innerText;
            var link = selected.dataset.link;
            var $parent = $(selected).parents('.js-mkb-related-articles-item');
            var $current = $parent.find('.js-mkb-related-current');
            var $store = $parent.find('.js-mkb-related-article-id');
            var $results = $parent.find('.js-mkb-related-article-search-results');

            $current.attr('href', link).find('.js-mkb-related-current-title').html(title);
            $parent.find('.js-mkb-related-article-search-input').val('');
            $results.html('');
            $store.val(id);
            $parent.removeClass('state--edit');
        });
    }

    function initFeedback() {
        $('#poststuff').on('click', '.fn-remove-feedback', function(e) {
            e.preventDefault();

            var $link = $(e.currentTarget);
            var $row = $link.parents('.mkb-article-feedback-item');

            $row.addClass('mkb-article-feedback-item--removing');

            ui.fetch({
                action: 'mkb_remove_feedback',
                feedback_id: parseInt($link.data('id'))
            }).then(function() {
                $row.slideUp('fast', function() {
                    $row.remove();
                });
            });
        });
    }

    function initReset() {
        $('.fn-mkb-article-reset-stats-btn').on('click', function(e) {
            e.preventDefault();

            var resetConfig = ui.getFormData($('.fn-mkb-article-reset-form'));

            if(!Object.keys(resetConfig).filter(function(key) {
                    return resetConfig[key] === true;
                }).length) {
                return;
            }

            if (!confirm('Confirm data reset')) {
                return;
            }

            ui.fetch({
                action: 'mkb_reset_stats',
                articleId: e.currentTarget.dataset.id,
                resetConfig: resetConfig
            }).then(function(response) {
                if (response.status == 0) {
                    toastr.success('Data was reset successfully.');
                } else {
                    toastr.error('Could not reset data, try to refresh the page');
                }
            });
        });
    }

    /***************
     * Attachments *
     ***************/

    function setupArticleAttachments() {
        var $attachmentsContainer = $('.js-mkb-attachments');
        var $addBtn = $('.js-mkb-add-attachment');
        var $addBtnExternal = $('.js-mkb-add-attachment-external');
        var frame;
        var attachmentsIconMap = ARTICLE_DATA.attachmentsIconMap;
        var attachmentsTracking = ARTICLE_DATA.attachmentsTracking;
        var attachmentsIconDefault = ARTICLE_DATA.attachmentsIconDefault;
        var $editedAttachment = null;

        attachmentsTracking = Array.isArray(attachmentsTracking) ? {} : attachmentsTracking;

        function getDownloads(id) {
            return attachmentsTracking[id] && attachmentsTracking[id]['downloads'] || 0;
        }

        var attachmentItemTmpl = wp.template('mkb-attachment-item');
        var noAttachmentsTmpl = wp.template('mkb-no-attachments');
        var externalPopupTmpl = wp.template('mkb-external-attachment');
        var externalPopupAddTmpl = wp.template('mkb-external-attachment-add');
        var externalPopupUpdateTmpl = wp.template('mkb-external-attachment-update');

        if (!$attachmentsContainer.length) {
            return;
        }

        // remove
        $attachmentsContainer.on('click', '.js-mkb-attachment-remove', function(e) {
            e.preventDefault();

            var $link = $(e.currentTarget);

            if (!confirm('Are you sure you want to remove attachment?')) {
                return;
            }

            $link.parents('.js-mkb-attachment-item').remove();

            if ($attachmentsContainer.find('.js-mkb-attachment-item').length === 0) {
                $attachmentsContainer.html(noAttachmentsTmpl());
            }
        });

        // edit
        $attachmentsContainer.on('click', '.js-mkb-attachment-edit', function(e) {
            e.preventDefault();

            var $link = $(e.currentTarget);
            var $item = $editedAttachment = $link.parents('.js-mkb-attachment-item');
            var $store = $item.find('.js-mkb-attachment-value');
            var value = $store.val();
            var isExternal = /^EXTERNAL*/.test(value);

            if (isExternal) {
                var url = value.replace(/^EXTERNAL{{/, '').replace(/}}$/, '');
                var customLabel = '';
                var customSize = '';

                var sizeMatch = url.match(/SIZE{{(.*)}}/);

                if (sizeMatch != null) {
                    customSize = sizeMatch[1];
                    url = url.replace(/SIZE{{(.*)}}/, '');
                }

                var labelMatch = url.match(/LABEL{{(.*)}}/);

                if (labelMatch != null) {
                    customLabel = labelMatch[1];
                    url = url.replace(/LABEL{{(.*)}}/, '');
                }

                openExternalLinkPopup(url, customLabel, customSize);
            } else {
                openMedia({
                    onSelect: function(attachments) {
                        appendAttachment(attachments[0], $item);
                        $item.remove();
                    },
                    selectedId: parseInt($store.val())
                });
            }
        });

        function openMedia(options) {
            frame = wp.media({
                title: 'Select or Upload Media',
                button: {
                    text: 'Use this media'
                },
                multiple: Boolean(options.multiple)
            });

            frame.on('select', function() {
                var attachments = frame.state().get('selection').map(function(attachment) {
                    return attachment.toJSON();
                });

                options.onSelect(attachments);
            });

            // add preselected attachment
            if (options.selectedId) {
                frame.on('open',function() {
                    var selection = frame.state().get('selection');
                    var attachment = wp.media.attachment(options.selectedId);
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                });
            }

            frame.open();
        }

        function matchByMimeType(attachment, config) {
            return config.mime.length && (
                config.mimeBase ?
                    config.mime.map(function(type) { return config.mimeBase + '/' + type; }).includes(attachment.mime) :
                    config.mime.includes(attachment.subtype)
            );
        }

        function getExtension(filename) {
            var extension = filename.split('.').pop();

            return extension.length <= 5 ? extension : '';
        }

        function matchByExtension(attachment, config) {
            return config.extension.includes(getExtension(attachment.filename));
        }

        function getAttachmentIconConfig(attachment) {
            return _.find(attachmentsIconMap, function(configEntry) {
                return matchByMimeType(attachment, configEntry) || matchByExtension(attachment, configEntry);
            }) || attachmentsIconDefault;
        }

        function appendAttachment(attachment, $after) {
            var iconConfig = getAttachmentIconConfig(attachment);
            var itemHTML = attachmentItemTmpl(
                _.extend({}, attachment,
                    {
                        color: iconConfig.color,
                        icon: iconConfig.icon,
                        description: iconConfig.description,
                        extension: getExtension(attachment.filename),
                        downloads: getDownloads(attachment.id)
                    })
            );
            var $html = $(itemHTML);

            if ($after) {
                $after.after($html);
            } else {
                $attachmentsContainer.append($html);
            }

            ui.setupTooltips($html);
        }

        $addBtn.on('click', function(e) {
            e.preventDefault();

            openMedia({
                onSelect: function(attachments) {
                    $attachmentsContainer.find('.js-mkb-no-attachments').remove();

                    attachments.forEach(function(attachment) {
                        appendAttachment(attachment);
                    });
                },
                multiple: true
            });
        });

        // create popup instance
        var externalLinkPopup = new ui.Popup();

        externalLinkPopup.bindEvents({
            'click .fn-mkb-popup-close': externalLinkPopup.close.bind(externalLinkPopup),
            'click .fn-mkb-external-link-insert': handleExternalLinkInsert,
            'click .fn-mkb-external-link-update': handleExternalLinkUpdate
        });

        function parseExternalFileInfoFromURL(url, label, size) {
            if (!url) {
                return null;
            }

            label = label || '';
            size = size || '';

            var splitURL = url.split('.');
            var extension = splitURL.pop();
            var name = splitURL.pop();
            var hasExtension = Boolean(extension && extension.length <= 5);

            if (hasExtension) {
                name = name.split('/');
                name = name.pop();
            } else {
                extension = '';
                name = url;
            }

            return {
                id: 'EXTERNAL{{' + url +
                        (label ? 'LABEL{{' + label + '}}' : '') +
                        (size ? 'SIZE{{' + size + '}}' : '') +
                    '}}',
                url: url,
                title: label ? label : url,
                filename: name + (hasExtension ? '.' + extension : ''),
                filesizeHumanReadable: size ? size : "External link",
                isExternal: true
            };
        }

        function handleExternalLinkInsert(e) {
            var $url = externalLinkPopup.$el.find('.js-mkb-external-file-url');
            var $caption = externalLinkPopup.$el.find('.js-mkb-external-file-caption');
            var $size = externalLinkPopup.$el.find('.js-mkb-external-file-size');
            var url = $url.val().trim();
            var caption = $caption.val().trim();
            var size = $size.val().trim();

            e.preventDefault();

            if (!url) {
                alert('URL must not be empty');

                return;
            }

            // TODO: size
            appendAttachment(parseExternalFileInfoFromURL(url, caption, size));

            externalLinkPopup.close();
        }

        function handleExternalLinkUpdate(e) {
            var $url = externalLinkPopup.$el.find('.js-mkb-external-file-url');
            var $caption = externalLinkPopup.$el.find('.js-mkb-external-file-caption');
            var $size = externalLinkPopup.$el.find('.js-mkb-external-file-size');
            var url = $url.val().trim();
            var caption = $caption.val().trim();
            var size = $size.val().trim();

            e.preventDefault();

            if (!url) {
                alert('URL must not be empty');

                return;
            }

            appendAttachment(parseExternalFileInfoFromURL(url, caption, size), $editedAttachment);
            $editedAttachment.remove();

            externalLinkPopup.close();
        }

        function openExternalLinkPopup(url, title, size) {
            url = url || '';
            title = title || '';
            size = size || '';

            externalLinkPopup.render({
                title: 'Add external file',
                content: externalPopupTmpl({ url: url, title: title, size: size }),
                footerControlsRight: [
                    url ? externalPopupUpdateTmpl({}) : externalPopupAddTmpl({})
                ],
                autoHeight: true
            });
        }

        $addBtnExternal.on('click', function(e) {
            e.preventDefault();

            openExternalLinkPopup();
        });

        if (ARTICLE_DATA.attachments.length) {
            $attachmentsContainer.html('');
            ARTICLE_DATA.attachments.forEach(function(attachment) {
                try {
                    appendAttachment(attachment);
                } catch (e) {
                    console.log('Attachment parse failed', e);
                }
            });
        } else {
            $attachmentsContainer.html(noAttachmentsTmpl());
        }

        $attachmentsContainer.sortable({
            'items': '.mkb-article-attachments__item',
            'axis': 'y'
        });
    }

    function init() {
        var $restrictContainer = $('#mkb-article-meta-restrict-id');
        var $relatedContainer = $('#mkb-article-meta-related-id');

        setupRelatedArticles($relatedContainer);
        setupArticleAttachments();
        initFeedback();
        initReset();

        ui.setupRolesSelector($restrictContainer);
    }

    $(document).ready(init);
})(jQuery);