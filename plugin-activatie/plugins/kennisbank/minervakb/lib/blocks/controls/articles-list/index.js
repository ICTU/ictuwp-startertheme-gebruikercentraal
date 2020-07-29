/**
 * External dependencies
 */
const _ = window.lodash;
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Spinner } = wp.components;
const { Component } = wp.element;
const { withSelect } = wp.data;

/**
 * Internal dependencies
 */
const GLOBAL_DATA = window.MinervaKB;
const ui = window.MinervaUI;

import { ControlWrap } from "controls/common/control-wrap";
import { getOption } from 'utils';
import { articlesSelect } from 'data/resolvers';
import Sortable from 'controls/common/sortable';

class ArticlesListItem extends Component {

    constructor(props) {
        super(props);

        this.state = {
            isEdit: false,
            searchNeedle: '',
            searchResults: []
        };

        this.onEdit = this.onEdit.bind(this);
        this.onEditCancel = this.onEditCancel.bind(this);
        this.onSearchType = this.onSearchType.bind(this);
        this.onArticleSelect = this.onArticleSelect.bind(this);
    }

    onEdit() {
        this.setState({ isEdit: true });
    }

    onEditCancel() {
        this.setState({ isEdit: false });
    }

    onArticleSelect(articleId) {
        const { selected, index, onChange } = this.props;

        selected[index] = articleId;
        onChange(selected.join(','));

        this.setState({ isEdit: false });
    }

    onSearchType(e) {
        e.preventDefault();

        this.setState({ searchNeedle: e.currentTarget.value });

        const needle = e.currentTarget.value.trim();

        if (needle.length < 3) {
            this.state.searchResults.length && this.setState({ searchResults: [] });
            return;
        }

        ui.fetch({
            action: 'mkb_kb_search',
            search: needle
        }).then((response) => {
            const found = response.result;

            this.setState({ searchResults: found });
        });
    }

    render() {
        const { posts, selected, index, articleId, value, onChange } = this.props;

        const article = _.find(posts, { id: articleId });

        return (
            <div className={classnames('mkb-related-articles__item', { 'state--edit': this.state.isEdit })} key={_.uniqueId()} data-id={articleId}>

                <a className="mkb-related-current" href="#">
                    <span>{article.title.rendered}</span>
                </a>

                {this.state.isEdit && <div className="mkb-related-article-search">
                    <input
                        type="text"
                        className="mkb-related-article-search-input"
                        placeholder="Type to search"
                        onChange={this.onSearchType}
                        autoFocus="true"
                        value={this.state.searchNeedle}
                    />
                    <a href="#" className="mkb-related-edit-cancel" onClick={this.onEditCancel}>Cancel</a>
                    <ul className="mkb-related-article-search-results">
                        {this.state.searchResults.length ?
                            this.state.searchResults.map((searchResult) => (
                                <li>
                                    <a onClick={() => this.onArticleSelect(searchResult.id)}>{searchResult.title}</a>
                                </li>
                                )
                            ) :
                            <li><span className="mkb-related-not-found">Nothing found</span></li>}
                    </ul>
                </div>}

                <a href="#" className="mkb-related-edit" onClick={this.onEdit}>Edit</a>

                <a className="mkb-related-articles__item-remove mkb-unstyled-link" onClick={() => {
                    _.pullAt(selected, [index]);
                    onChange(selected.join(','));
                }}>
                    <i className="fa fa-close"/>
                </a>
            </div>
        );
    }
}

export const ArticlesList = withSelect(articlesSelect)((props) => {
    const { value, posts, onChange } = props;

    const selected = value.split(',').filter(Boolean).map(Number);

    if (!posts) {
        return (
            <p>
                <Spinner />
                {__( 'Loading Articles...', 'minervakb' )}
            </p>
        );
    }

    if (0 === posts.length ) {
        return <p>{__('No articles', 'minerva-kb')}</p>;
    }

    return (
        <ControlWrap {...props}>
            <div className="mkb-related-articles">
                {selected.length ? (
                    <Sortable
                        onChange={(reorderedItems) => onChange(reorderedItems.join(','))}>
                        {selected.map((articleId, index) => (
                            <ArticlesListItem index={index} selected={selected} articleId={articleId} {...props} />
                        ))}
                    </Sortable>
                ) : (
                    <div className="mkb-no-related-message">
                        <p>{__('No related articles selected', 'minerva-kb')}</p>
                    </div>
                )}
            </div>
            <div className="mkb-related-actions">
                <a className="button button-primary button-large"
                   onClick={() => onChange([...selected, posts[0].id].join(','))}
                   title={__('Add article', 'minerva-kb')}>
                    {__('Add article', 'minerva-kb')}
                </a>
            </div>
        </ControlWrap>
    );
});
