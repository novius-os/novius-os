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
    mp3Grid = $.nos.mp3GridSetup({
        tab : {
            label : 'Media center',
            iconUrl : 'static/cms/img/32/media.png'
        },
        proxyUrl : 'admin/admin/media/list/json',
        adds : {
            media : {
                label : 'Add a media',
                url : 'admin/admin/media/add'
            },
            folder : {
                label : 'Add a folder',
                iconClasses : 'nos-icon16 nos-icon16-folder',
                url : 'admin/admin/media/folder/add'
            }
        },
        columns : {
            extension : {
                headerText : 'Ext.',
                dataKey : 'extension',
                width : 1,
                allowSizing : false
            },
            title : {
                headerText : 'Title',
                dataKey : 'title'
            },
            actions : true
        },
        actions : {
            edit : {
                label : 'Edit',
                action : function(item) {
                    $.nos.tabs.add({
                        iframe : true,
                        url : "admin/admin/media/form?id=" + item.id,
                        label : item.title
                    });
                }
            },
            delete : {
                label : 'Delete',
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
                label : 'Visualize',
                action : function(item) {
                    window.open(item.image);
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
            actions : true
        },
        defaultView : 'thumbnails',
        preview : {
            hide : false,
            vertical : true,
            options : {
                meta : {
                    id : {
                        label : 'Id'
                    },
                    extensions : {
                        label : 'Extension'
                    },
                    fileName : {
                        label : 'File name'
                    },
                    path : {
                        label : 'Path'
                    }
                },
                actions : {
                    edit : true,
                    visualize : {
                        button : true
                    },
                    delete : true
                },
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
        },
        inspectors : {
            folders : {
                vertical : true,
                label : 'Folders',
                url : 'admin/admin/media/inspector/folder/list',
                widget_id : 'inspector-folder'
            },
            extensions : {
                widget_id : 'inspector-extension',
                label : 'Type of file',
                url : 'admin/admin/media/inspector/extension/list'
            }
        }
    });
    return mp3Grid;
});
