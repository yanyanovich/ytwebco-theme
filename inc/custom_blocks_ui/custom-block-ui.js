(function (wp) {
    const { registerPlugin } = wp.plugins;
    const { createElement: el, render, useMemo, useState } = wp.element;
    const { Button, Modal } = wp.components;
    const { createBlock } = wp.blocks;
    const { select, dispatch } = wp.data;

    const getAllowedBlocks = () => {
        const settings = select('core/block-editor').getSettings();
        const allowedBlockNames = settings.allowedBlockTypes === true
            ? wp.blocks.getBlockTypes().map((block) => block.name)
            : settings.allowedBlockTypes || [];

        return wp.blocks.getBlockTypes().filter((block) => {
            return allowedBlockNames.includes(block.name) && block.name !== 'core/block';
        });
    };

    const getGroupedBlocks = () => {
        return getAllowedBlocks().reduce((groups, block) => {
            const category = block.category || 'uncategorized';

            if (!groups[category]) {
                groups[category] = [];
            }

            groups[category].push(block);

            return groups;
        }, {});
    };

    const getCategoryOrder = () => {
        return window.emptyThemeCustomBlocks && window.emptyThemeCustomBlocks.categoryOrder
            ? window.emptyThemeCustomBlocks.categoryOrder
            : [];
    };

    const sortCategorySlugs = (categorySlugs) => {
        const categoryOrder = getCategoryOrder();

        return categorySlugs.sort((a, b) => {
            const aIndex = categoryOrder.indexOf(a);
            const bIndex = categoryOrder.indexOf(b);

            if (aIndex !== -1 && bIndex !== -1) {
                return aIndex - bIndex;
            }

            if (aIndex !== -1) {
                return -1;
            }

            if (bIndex !== -1) {
                return 1;
            }

            return getCategoryTitle(a).localeCompare(getCategoryTitle(b));
        });
    };

    const getCategoryTitle = (slug) => {
        const category = wp.blocks.getCategories().find((item) => item.slug === slug);

        return category ? category.title : 'Other';
    };

    const getBlockPreviewUrl = (block) => {
        const previewUrls = window.emptyThemeCustomBlocks ? window.emptyThemeCustomBlocks.previewUrls : {};

        if (previewUrls && previewUrls[block.name]) {
            return previewUrls[block.name];
        }

        if (!block.path) {
            return '';
        }

        return block.path.replace(/^.*?wp-content/, '/wp-content') + '/preview.png';
    };

    const insertSelectedBlock = (blockName) => {
        const block = createBlock(blockName);
        const selectedBlock = select('core/block-editor').getSelectedBlock();

        if (selectedBlock) {
            const selectedBlockIndex = select('core/block-editor').getBlockIndex(selectedBlock.clientId);
            dispatch('core/block-editor').insertBlock(block, selectedBlockIndex + 1);
            return;
        }

        dispatch('core/block-editor').insertBlock(block);
    };

    const CustomBlockSelector = () => {
        const [isModalOpen, setModalOpen] = useState(false);
        const [selectedCategory, setSelectedCategory] = useState('all');
        const [searchQuery, setSearchQuery] = useState('');
        const groupedBlocks = useMemo(getGroupedBlocks, [isModalOpen]);
        const normalizedSearchQuery = searchQuery.trim().toLowerCase();
        const filterBlock = (block) => {
            if (!normalizedSearchQuery) {
                return true;
            }

            const title = String(block.title || '').toLowerCase();
            const description = String(block.description || '').toLowerCase();
            const name = String(block.name || '').toLowerCase();

            return title.includes(normalizedSearchQuery) ||
                description.includes(normalizedSearchQuery) ||
                name.includes(normalizedSearchQuery);
        };
        const filteredGroupedBlocks = Object.keys(groupedBlocks).reduce((groups, categorySlug) => {
            const blocks = groupedBlocks[categorySlug].filter(filterBlock);

            if (blocks.length) {
                groups[categorySlug] = blocks;
            }

            return groups;
        }, {});
        const categorySlugs = sortCategorySlugs(Object.keys(filteredGroupedBlocks));
        const visibleCategorySlugs = selectedCategory === 'all' ? categorySlugs : [selectedCategory];
        const hasBlocks = categorySlugs.length > 0;

        const closeModal = () => setModalOpen(false);

        const handleBlockSelect = (blockName) => {
            insertSelectedBlock(blockName);
            closeModal();
        };

        return el(
            wp.element.Fragment,
            null,
            el(CustomBlockToolbarButton, {
                onClick: () => setModalOpen(true),
            }),
            isModalOpen && el(
                Modal,
                {
                    title: 'Custom blocks list',
                    onRequestClose: closeModal,
                    className: 'custom-block-selector-modal',
                },
                el(
                    'div',
                    { className: 'custom-block-selector-modal-container' },
                    el(
                        'aside',
                        { className: 'custom-block-selector-modal-categories' },
                        el('label', {
                            className: 'custom-block-selector-modal-search',
                        },
                        el('span', null, 'Search'),
                        el('input', {
                            type: 'search',
                            value: searchQuery,
                            onChange: (event) => {
                                setSearchQuery(event.target.value);
                                setSelectedCategory('all');
                            },
                        })),
                        el(
                            'button',
                            {
                                type: 'button',
                                className: selectedCategory === 'all' ? 'is-active' : '',
                                onClick: () => setSelectedCategory('all'),
                            },
                            'All blocks'
                        ),
                        categorySlugs.map((categorySlug) => {
                            return el(
                                'button',
                                {
                                    key: categorySlug,
                                    type: 'button',
                                    className: selectedCategory === categorySlug ? 'is-active' : '',
                                    onClick: () => setSelectedCategory(categorySlug),
                                },
                                getCategoryTitle(categorySlug)
                            );
                        })
                    ),
                    el(
                        'div',
                        { className: 'custom-block-selector-modal-blocklist' },
                        hasBlocks
                            ? visibleCategorySlugs.map((categorySlug) => {
                                const blocks = filteredGroupedBlocks[categorySlug] || [];

                                if (!blocks.length) {
                                    return null;
                                }

                                return el(
                                    'section',
                                    {
                                        key: categorySlug,
                                        className: 'custom-block-selector-modal-section',
                                    },
                                    el('h3', null, getCategoryTitle(categorySlug)),
                                    el(
                                        'div',
                                        { className: 'custom-block-selector-modal-grid' },
                                        blocks.map((block) => {
                                            const previewUrl = getBlockPreviewUrl(block);

                                            return el(
                                                'button',
                                                {
                                                    key: block.name,
                                                    type: 'button',
                                                    className: 'custom-block-selector-modal-card',
                                                    onClick: () => handleBlockSelect(block.name),
                                                },
                                                previewUrl && el('img', {
                                                    className: 'custom-block-selector-modal-preview',
                                                    src: previewUrl,
                                                    alt: '',
                                                }),
                                                el('strong', null, block.title),
                                                block.description && el('span', null, block.description)
                                            );
                                        })
                                    )
                                );
                            })
                            : el(
                                'div',
                                { className: 'custom-block-selector-modal-empty' },
                                'No blocks found'
                            )
                    )
                )
            )
        );
    };

    const mountToolbarButton = (onClick) => {
        const toolbar = document.querySelector('.editor-document-tools');

        if (!toolbar || document.querySelector('.custom-block-selector-container')) {
            return Boolean(toolbar);
        }

        const container = document.createElement('div');
        container.className = 'custom-block-selector-container';
        toolbar.parentNode.insertBefore(container, toolbar.nextSibling);

        render(
            el(Button, {
                className: 'custom-block-selector-button',
                icon: 'screenoptions',
                label: 'Add custom block',
                onClick,
            }),
            container
        );

        return true;
    };

    const CustomBlockToolbarButton = ({ onClick }) => {
        wp.element.useEffect(() => {
            if (mountToolbarButton(onClick)) {
                return undefined;
            }

            const observer = new MutationObserver(() => {
                if (mountToolbarButton(onClick)) {
                    observer.disconnect();
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true,
            });

            return () => observer.disconnect();
        }, [onClick]);

        return null;
    };

    registerPlugin('custom-block-selector', {
        render: CustomBlockSelector,
    });
})(window.wp);
