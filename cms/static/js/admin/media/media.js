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
            edit : {
                label : mp3Grid.i18n('Edit'),
                action : function(item) {
                    $.nos.tabs.add({
                        iframe : true,
                        url : "admin/admin/media/form?id=" + item.id,
                        label : item.title
                    });
                }
            },
            delete : {
                label : mp3Grid.i18n('Delete'),
                action : function(item) {
                    if (confirm("Are you sure ?")) {
                        $.nos.tabs.add({
                            iframe : true,
                            url : "admin/admin/media/form?id=" + item.id,
                            label : item.title
                        });
                    }
                }
            },
            visualize : {
                label : mp3Grid.i18n('Visualize'),
                action : function(item) {
                    window.open(item.image);
                }
            }
        };

    return $.extend(true, mp3Grid, {
        actions : actions,
        tab : {
            label : mp3Grid.i18n('Media center'),
            iconUrl : 'static/cms/img/32/media.png'
        },
        mp3grid : {
            adds : {
                media : {
                    label : mp3Grid.i18n('Add a media'),
                    url : 'admin/admin/media/add'
                },
                folder : {
                    label : mp3Grid.i18n('Add a folder'),
                    url : 'admin/admin/media/folder/add'
                }
            },
            grid : {
                proxyUrl : 'admin/admin/media/list/json',
                columns : {
                    extension : {
                        headerText : mp3Grid.i18n('Ext.'),
                        dataKey : 'extension',
                        width : 1,
                        allowSizing : false
                    },
                    title : {
                        headerText : mp3Grid.i18n('Title'),
                        dataKey : 'title'
                    },
                    actions : {
                        actions : [
                            actions.edit,
                            actions.delete,
                            actions.visualize
                        ]
                    }
                }
            },
            thumbnails : {
                dataParser : function(size, item) {
                    var data = {
                        title : item.title,
                        thumbnail : (item.image ? item.thumbnail : item.thumbnailAlternate).replace(/64/g, size),
                        thumbnailAlternate : (item.image ? item.thumbnailAlternate : '').replace(/64/g, size),
                        actions : []
                    };
                    return data;
                },
                actions : [
                    actions.edit,
                    actions.delete,
                    actions.visualize
                ]
            },
            defaultView : 'thumbnails',
            inspectors : {
                folders : {
                    vertical : true,
                    label : mp3Grid.i18n('Folders'),
                    url : 'admin/admin/media/inspector/folder/list',
                    widget_id : 'cms_media_folders',
                    inputName : 'folder_id',
                    grid : {
                        urlJson : 'admin/admin/media/inspector/folder/json',
                        columns : {
                            title : {
                                headerText : mp3Grid.i18n('Folder name'),
                                dataKey : 'title'
                            },
                            actions : {
                                actions : [
                                    {
                                        label : mp3Grid.i18n('Upload a new file'),
                                        action : function(item) {
                                            $.nos.dialog({
                                                contentUrl: 'admin/admin/media/upload/form/' + item.id,
                                                title: 'Upload a new file in the "' + item.title + '" folder',
                                                width: 400,
                                                height: 200
                                            });
                                        }
                                    },
                                    {
                                        label : mp3Grid.i18n('Create a sub-folder'),
                                        action : function(item) {
                                            $.nos.dialog({
                                                contentUrl: 'admin/admin/media/folder/form/' + item.id,
                                                title: 'Create a sub-folder in "' + item.title + '"',
                                                width: 550,
                                                height: 200
                                            });
                                        }
                                    }
                                ]
                            }
                        }
                    }
                },
                extensions : {
                    widget_id : 'cms_media_extensions',
                    label : mp3Grid.i18n('Type of file'),
                    url : 'admin/admin/media/inspector/extension/list',
                    inputName : 'media_extension[]',
                    grid : {
                        columns : {
                            title : {
                                headerText : mp3Grid.i18n('Type of file'),
                                dataKey : 'title',
                                cellFormatter : function(args) {
                                    if ($.isPlainObject(args.row.data)) {
                                        var text = "";
                                        if (args.row.data.icon) {
                                            text += "<img style=\"vertical-align:middle\" src=\"static/cms/img/16/" + args.row.data.icon + "\"> ";
                                        }
                                        text += args.row.data.title;

                                        args.$container.html(text);

                                        return true;
                                    }
                                }
                            },
                            hide : {
                                visible : false
                            },
                            hide2 : {
                                visible : false
                            }
                        }
                    }
                },
                preview : {
                    preview : true,
                    hide : false,
                    vertical : true,
                    label : mp3Grid.i18n('Preview'),
                    widget_id : 'cms_media_preview',
                    options : {
                        meta : {
                            id : {
                                label : mp3Grid.i18n('Id')
                            },
                            extensions : {
                                label : mp3Grid.i18n('Extension')
                            },
                            fileName : {
                                label : mp3Grid.i18n('File name')
                            },
                            path : {
                                label : mp3Grid.i18n('Path')
                            }
                        },
                        actions : [
                            actions.edit,
                            $.extend(actions.visualize, {
                                button : true
                            }),
                            actions.delete
                        ],
                        dataParser : function(item) {
                            var data = {
                                title : item.title,
                                thumbnail : (item.image ? item.thumbnail.replace(/64/g, 256) : item.thumbnailAlternate),
                                thumbnailAlternate : (item.image ? item.thumbnailAlternate : ''),
                                meta : {
                                    id : item.id,
                                    extension : item.extension,
                                    fileName : item.file_name,
                                    path : item.path
                                }
                            };
                            return data;
                        }
                    }
                }
            }
        }
    });
});
