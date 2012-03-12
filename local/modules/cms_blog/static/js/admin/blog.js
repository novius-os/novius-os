/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define([
    'jquery-nos'
], function($) {
    return function(mp3Grid) {
        return {
            tab : {
                label : mp3Grid.i18n('Blog'),
                iconUrl : 'static/modules/cms_blog/img/32/blog.png'
            },
            actions : {
                update : {
                    action : function(item) {
                        $.nos.tabs.add({
                            url     : "admin/cms_blog/form/edit/" + item.id,
                            label   : mp3Grid.i18n('Edit')._()
                        });
                    },
                    label : mp3Grid.i18n('Edit')
                },
                'delete' : {
                    action : function(item) {
                        $.nos.ajax.request({
                            url: "admin/cms_blog/list/delete/" + item.id,
                            data: {},
                            success: function(response) {
                                if (response.success) {
                                    $.nos.notify("Suppression réalisée !");
                                    $.nos.fireEvent({
                                        event : 'reload',
                                        target : 'cms_blog'
                                    })
                                } else {
                                    $.nos.notify("Erreur lors de la suppression !", "error");
                                }
                            }
                        });
                    },
                    label : mp3Grid.i18n('Delete')
                }
            },
            mp3grid : {
                adds : {
                    post : {
                        label : mp3Grid.i18n('Add a post'),
                        action : function() {
                            $.nos.tabs.add({
                                url     : 'admin/cms_blog/form/edit',
                                label   : mp3Grid.i18n('Edit')._()
                            });
                        }
                    },
                    category : {
                        label : mp3Grid.i18n('Add a category'),
                        url : 'admin/cms_blog/categoryform'
                    }
                },
                splittersVertical :  250,
                grid : {
                    proxyUrl : 'admin/cms_blog/list/json',
                    columns : {
                        title : {
                            headerText : mp3Grid.i18n('Title'),
                            dataKey : 'title',
                            sortDirection : 'ascending'
                        },
                        lang : {
                            lang : true
                        },
                        author : {
                            headerText : mp3Grid.i18n('Author'),
                            dataKey : 'author'
                        },
                        data : {
                            headerText : mp3Grid.i18n('Date'),
                            dataKey : 'date',
                            dataFormatString  : 'MM/dd/yyyy HH:mm:ss',
                            showFilter : false
                        },
                        actions : {
                            actions : ['update', 'delete']
                        }
                    }
                },
                inspectors : {
                    categories : {
                        widget_id : 'cms_blog_categories',
                        label : mp3Grid.i18n('Categories'),
                        vertical : true,
                        url : 'admin/cms_blog/inspector/category/list',
                        treeGrid : {
                            columns : {
                                title : {
                                    headerText : mp3Grid.i18n('Category'),
                                    dataKey : 'title'
                                },
                                actions : {
                                    showOnlyArrow : true,
                                    actions : [
                                        {
                                            action : function(item) {
                                                $.nos.tabs.add({
                                                    iframe : true,
                                                    url     : "admin/cms_blog/form?id=" + item.id,
                                                    label   : mp3Grid.i18n('Update')._()
                                                });
                                            },
                                            label : mp3Grid.i18n('Update')
                                        },
                                        {
                                            action : function(item) {
                                                $.nos.ajax.request({
                                                    url: "admin/cms_blog/inspector/category/delete/" + item.id,
                                                    data: {},
                                                    success: function(response) {
                                                        if (response.success) {
                                                            $.nos.notify("Suppression réalisée !");
                                                            $.nos.fireEvent({
                                                                event : 'reload',
                                                                target : 'cms_blog'
                                                            })
                                                        } else {
                                                            $.nos.notify("Erreur lors de la suppression !", "error");
                                                        }
                                                    }
                                                });
                                            },
                                            label : mp3Grid.i18n('Delete')
                                        }
                                    ]
                                }
                            },
                            treeUrl : 'admin/cms_blog/inspector/category/json'
                        },
                        inputName : 'blgc_id[]'
                    },
                    tags : {
                        widget_id : 'cms_blog_tags',
                        hide : true,
                        label : mp3Grid.i18n('Tags'),
                        url : 'admin/cms_blog/inspector/tag/list',
                        grid : {
                            urlJson : 'admin/cms_blog/inspector/tag/json',
                            columns : {
                                title : {
                                    headerText : mp3Grid.i18n('Tag'),
                                    dataKey : 'title'
                                },
                                actions : {
                                    showOnlyArrow : true,
                                    actions : [
                                        {
                                            action : function(item) {
                                                $.nos.tabs.add({
                                                    iframe : true,
                                                    url     : "admin/cms_blog/form?id=" + item.id,
                                                    label   : mp3Grid.i18n('Edit')
                                                });
                                            },
                                            label : mp3Grid.i18n('Edit')
                                        },
                                        {
                                            action : function(item) {
                                                $.nos.ajax.request({
                                                    url: "admin/cms_blog/inspector/category/delete/" + item.id,
                                                    data: {},
                                                    success: function(response) {
                                                        if (response.success) {
                                                            $.nos.notify("Suppression réalisée !");
                                                            $.nos.fireEvent({
                                                                event : 'reload',
                                                                target : 'cms_blog'
                                                            })
                                                        } else {
                                                            $.nos.notify("Erreur lors de la suppression !", "error");
                                                        }
                                                    }
                                                });
                                            },
                                            label : mp3Grid.i18n('Delete')
                                        }
                                    ]
                                }
                            }
                        },
                        inputName : 'tag_id[]'
                    },
                    authors : {
                        widget_id : 'cms_blog_authors',
                        label : mp3Grid.i18n('Authors'),
                        url : 'admin/cms_blog/inspector/author/list',
                        grid : {
                            columns : {
                                title : {
                                    headerText : mp3Grid.i18n('Author'),
                                    dataKey : 'title'
                                },
                                actions : {
                                    showOnlyArrow : true,
                                    actions : [
                                        {
                                            action : function(item) {
                                                $.nos.tabs.add({
                                                    iframe : true,
                                                    url     : "admin/cms_blog/form?id=" + item.id,
                                                    label   : "Update"
                                                });
                                            },
                                            label : mp3Grid.i18n('Update')
                                        }
                                    ]
                                }
                            },
                            urlJson : 'admin/cms_blog/inspector/author/json'
                        },
                        inputName : 'blog_author_id[]'
                    },
                    publishDate : {
                        widget_id : 'cms_blog_publishDate',
                        vertical : true,
                        label : mp3Grid.i18n('Publish date'),
                        url : 'admin/cms_blog/inspector/date/list',
                        inputName : 'blog_created_at'
                    }
                }
            }
        }
    }
});
