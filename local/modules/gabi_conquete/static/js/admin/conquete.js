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
                        url     : "admin/gabi_conquete/form/edit/" + item.id,
                        label   : mp3Grid.i18n('Edit')._()
                    });
                },
                label : mp3Grid.i18n('Update')
            },
            'delete' : {
                action : function(item) {
                    $.nos.ajax.request({
                        url: "admin/gabi_conquete/list/delete/" + item.id,
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
            iconUrl : 'static/modules/gabi_conquete/img/32/blog.png'
        },
        mp3grid : {
            adds : {
                post : {
                    label : mp3Grid.i18n('Add a post'),
                    action : function() {
                        $.nos.tabs.add({
                            url     : 'admin/gabi_conquete/form/edit',
                            label   : mp3Grid.i18n('Edit')._()
                        });
                    }
                },
                category : {
                    label : mp3Grid.i18n('Add a category'),
                    url : 'admin/gabi_conquete/categoryform'
                }
            },
            splittersVertical :  250,
            grid : {
                proxyUrl : 'admin/gabi_conquete/list/json',
                columns : {
                    id : {
                        headerText : mp3Grid.i18n('Id'),
                        dataKey : 'id',
                        sortDirection : 'ascending'
                    },
                    prenom : {
                        headerText : mp3Grid.i18n('Prénom'),
                        dataKey : 'prenom',
                        sortDirection : 'ascending'
                    },
                    nom : {
                        headerText : mp3Grid.i18n('Nom'),
                        dataKey : 'nom',
                        sortDirection : 'ascending'
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
                prenom : {
                    widget_id : 'gabi_conquete_prenom',
                    label : mp3Grid.i18n('Prénom'),
                    url : 'admin/gabi_conquete/inspector/prenom/list',
                    grid : {
                        columns : {
                            title : {
                                headerText : mp3Grid.i18n('Prénom'),
                                dataKey : 'title'
                            }
                        },
                        urlJson : 'admin/gabi_conquete/inspector/prenom/json'
                    },
                    inputName : 'prenoms[]'
                }
                /*categories : {
                    widget_id : 'gabi_conquete_categories',
                    label : mp3Grid.i18n('Categories'),
                    vertical : true,
                    url : 'admin/gabi_conquete/inspector/category/list',
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
                                                url     : "admin/gabi_conquete/form?id=" + item.id,
                                                label   : mp3Grid.i18n('Update')._()
                                            });
                                        },
                                        label : mp3Grid.i18n('Update')
                                    },
                                    {
                                        action : function(item) {
                                            $.nos.ajax.request({
                                                url: "admin/gabi_conquete/inspector/category/delete/" + item.id,
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
                        urlJson : 'admin/gabi_conquete/inspector/category/json'
                    },
                    inputName : 'blgc_id[]'
                },
                tags : {
                    widget_id : 'gabi_conquete_tags',
                    hide : true,
                    label : mp3Grid.i18n('Tags'),
                    url : 'admin/gabi_conquete/inspector/tag/list',
                    grid : {
                        urlJson : 'admin/gabi_conquete/inspector/tag/json',
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
                                                url     : "admin/gabi_conquete/form?id=" + item.id,
                                                label   : mp3Grid.i18n('Edit')
                                            });
                                        },
                                        label : mp3Grid.i18n('Edit')
                                    },
                                    {
                                        action : function(item) {
                                            $.nos.ajax.request({
                                                url: "admin/gabi_conquete/inspector/category/delete/" + item.id,
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
                    widget_id : 'gabi_conquete_authors',
                    label : mp3Grid.i18n('Authors'),
                    url : 'admin/gabi_conquete/inspector/author/list',
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
                                                url     : "admin/gabi_conquete/form?id=" + item.id,
                                                label   : "Update"
                                            });
                                        },
                                        label : mp3Grid.i18n('Update')
                                    }
                                ]
                            }
                        },
                        urlJson : 'admin/gabi_conquete/inspector/author/json'
                    },
                    inputName : 'blog_author_id[]'
                },
                publishDate : {
                    widget_id : 'gabi_conquete_publishDate',
                    vertical : true,
                    label : mp3Grid.i18n('Publish date'),
                    url : 'admin/gabi_conquete/inspector/date/list',
                    inputName : 'blog_created_at'
                },
                language : {
                    widget_id : 'gabi_conquete_language',
                    vertical : true,
                    label : mp3Grid.i18n('Language'),
                    url : 'admin/gabi_conquete/inspector/lang/list',
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
                }*/
            }
        }
    });
    return mp3Grid;
});
