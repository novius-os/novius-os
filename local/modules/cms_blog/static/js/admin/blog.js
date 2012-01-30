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
    var mp3Grid = $.nos.mp3GridSetup(),
        actions = {
            update : {
                action : function(item) {
                    $.nos.tabs.add({
                        url     : "admin/cms_blog/form/edit/" + item.id,
                        label   : mp3Grid.i18n('Edit')
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
                                $("#mp3grid").mp3grid("gridRefresh");
                            } else {
                                $.nos.notify("Erreur lors de la suppression !", "error");
                            }
                        }
                    });
                },
                label : mp3Grid.i18n('Delete')
            }
        };
    return $.extend(true, mp3Grid, {
        tab : {
            label : mp3Grid.i18n('Blog'),
            iconUrl : 'static/modules/cms_blog/img/32/blog.png'
        },
        mp3grid : {
            adds : {
                post : {
                    label : mp3Grid.i18n('Add a post'),
                    url : 'admin/cms_blog/form/edit'
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
                        actions : [
                            actions.update,
                            actions['delete']
                        ]
                    }
                }
            },
            inspectors : {
                categories : {
                    widget_id : 'cms_blog_categories',
                    label : mp3Grid.i18n('Categories'),
                    vertical : true,
                    url : 'admin/cms_blog/inspector/category/list',
                    grid : {
                        columns : {
                            title : {
                                headerText : mp3Grid.i18n('Category'),
                                dataKey : 'title'
                            },
                            actions : {
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
                                    },
                                    {
                                        action : function(item) {
                                            $.nos.ajax.request({
                                                url: "admin/cms_blog/inspector/category/delete/" + item.id,
                                                data: {},
                                                success: function(response) {
                                                    if (response.success) {
                                                        $.nos.notify("Suppression réalisée !");
                                                        $("#mp3grid").mp3grid("gridRefreshAll");
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
                        urlJson : 'admin/cms_blog/inspector/category/json'
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
                                                        $("#mp3grid").mp3grid("gridRefreshAll");
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
                    inputName : 'blog_auteur_id[]'
                },
                publishDate : {
                    widget_id : 'cms_blog_publishDate',
                    vertical : true,
                    label : mp3Grid.i18n('Publish date'),
                    url : 'admin/cms_blog/inspector/date/list',
                    inputName : 'blog_date_creation'
                },
                language : {
                    widget_id : 'cms_blog_language',
                    vertical : true,
                    label : mp3Grid.i18n('Language'),
                    url : 'admin/cms_blog/inspector/lang/list',
                    grid : {
                        columns : {
                            title : {
                                headerText  : mp3Grid.i18n('Language'),
                                dataKey : 'title'
                            },
                            hide : {
                                visible : false
                            }
                        }
                    },
                    languages : {
                        fr : mp3Grid.i18n('Français'),
                        en : mp3Grid.i18n('Anglais')
                    }
                }
            }
        }
    });
    return mp3Grid;
});
