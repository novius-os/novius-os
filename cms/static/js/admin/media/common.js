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
            actions : {
                edit : {
                    name : 'edit',
                    primary : true,
                    icon : 'pencil',
                    label : mp3Grid.i18n('Edit'),
                    action : function(item) {
                        $.nos.notify('Not implemented (yet).');
                    }
                },
                'delete' : {
                    name : 'delete',
                    primary : true,
                    icon : 'trash',
                    label : mp3Grid.i18n('Delete'),
                    action : function(item) {
                        if (confirm("Are you sure?")) {
                            $.nos.ajax.request({
                                url : 'admin/admin/media/actions/delete_media',
                                method : 'POST',
                                data : {
                                    id : item.id
                                }
                            });
                            log('ok ?');
                        }
                    }
                },
                visualize : {
                    name : 'visualize',
                    //primary : true,
                    icon : 'search',
                    label : mp3Grid.i18n('Visualize'),
                    action : function(item) {
                        window.open(item.path);
                    }
                }
            },
            tab : {
                label : mp3Grid.i18n('Media center'),
                iconUrl : 'static/cms/img/32/media.png'
            },
            refresh : 'cms_media_media',
            mp3grid : {
                adds : {
                    media : {
                        label : mp3Grid.i18n('Add a media'),
                        action : function() {
                            $.nos.dialog({
                                contentUrl: 'admin/admin/media/upload/form/',
                                ajax : true,
                                title: mp3Grid.i18n('Add a media')._(),
                                width: 850,
                                height: 400
                            });
                        }
                    },
                    folder : {
                        label : mp3Grid.i18n('Add a folder'),
                        action : function() {
                            $.nos.dialog({
                                contentUrl: 'admin/admin/media/folder/form',
                                ajax : true,
                                title: 'Add a folder',
                                width: 600,
                                height: 400
                            });
                        }
                    }
                },
                grid : {
                    id : 'cms_media_grid',
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
                            dataKey : 'title',
                            sortDirection : 'ascending'
                        },
                        actions : {
                            actions : ['edit', 'delete', 'visualize']
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
                    actions : ['edit', 'delete', 'visualize']
                },
                defaultView : 'thumbnails',
                inspectorsOrder : 'preview,folders,extensions',
                inspectors : {
                    folders : {
                        vertical : true,
                        widget_id : 'cms_media_folders',
                        label : mp3Grid.i18n('Folders'),
                        url : 'admin/admin/media/inspector/folder/list',
                        inputName : 'folder_id',
                        treeGrid : {
                            treeUrl : 'admin/admin/media/inspector/folder/json',
                            columns : {
                                title : {
                                    headerText : mp3Grid.i18n('Folder'),
                                    dataKey : 'title'
                                },
                                actions : {
                                    showOnlyArrow : true,
                                    actions : [
                                        {
                                            name : 'add_media',
                                            label : mp3Grid.i18n('Add a media in this folder'),
                                            icon : 'plus',
                                            action : function(item) {
                                                $.nos.dialog({
                                                    contentUrl: 'admin/admin/media/upload/form/' + item.id,
                                                    ajax : true,
                                                    title: 'Add a media in the "' + item.title + '" folder',
                                                    width: 650,
                                                    height: 240
                                                });
                                            }
                                        },
                                        {
                                            name : 'add_folder',
                                            label : mp3Grid.i18n('Add a sub-folder to this folder'),
                                            icon : 'folder-open',
                                            action : function(item) {
                                                $.nos.dialog({
                                                    contentUrl: 'admin/admin/media/folder/form/' + item.id,
                                                    ajax : true,
                                                    title: 'Add a sub-folder in "' + item.title + '"',
                                                    width: 600,
                                                    height: 250
                                                });
                                            }
                                        },
                                        {
                                            name : 'edit',
                                            label : mp3Grid.i18n('Edit this folder'),
                                            icon : 'pencil',
                                            action : function(item) {
                                                $.nos.notify('Not implemented (yet).');
                                            }
                                        },
                                        {
                                            name : 'delete',
                                            label : mp3Grid.i18n('Delete this folder'),
                                            icon : 'trash',
                                            action : function(item) {
                                                $.nos.dialog({
                                                    contentUrl: 'admin/admin/media/actions/delete_folder/' + item.id,
                                                    ajax : true,
                                                    title: 'Delete the "' + item.title + '" folder',
                                                    width: 400,
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
                        vertical : true,
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
                        vertical : true,
                        widget_id : 'cms_media_preview',
                        label : mp3Grid.i18n('Preview'),
                        preview : true,
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
                            actions : ['edit', 'delete', 'visualize'],
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
        }
    }
});
