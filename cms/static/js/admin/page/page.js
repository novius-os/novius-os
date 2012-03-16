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
                label : mp3Grid.i18n('Pages'),
                iconUrl : 'static/cms/img/32/page.png'
            },
            actions : {
                edit : {
                    label : mp3Grid.i18n('Edit'),
                    name : 'edit',
                    primary : true,
                    icon : 'pencil',
                    action : function(item) {
                        $.nos.tabs.add({
                            url : 'admin/cms/page/form/edit/' + item.id,
                            label : item.title
                        });
                    }
                },
                'delete' : {
                    label : mp3Grid.i18n('Delete'),
                    name : 'delete',
                    primary : true,
                    icon : 'trash',
                    action : function(item) {
                        $.nos.dialog({
                            contentUrl: 'admin/cms/page/form/delete_page/' + item.id,
                            ajax : true,
                            title: mp3Grid.i18n('Delete a page')._(),
                            width: 500,
                            height: 250
                        });
                    }
                },
                'visualise' : {
                    label : mp3Grid.i18n('Visualise'),
                    name : 'visualise',
                    primary : true,
                    iconClasses : 'nos-icon16 nos-icon16-eye',
                    action : function(item) {
                        window.open(item.visualise);
                    }
                }
            },
            reload : 'cms_page',
            mp3grid : {
                adds : {
                    page : {
                        label : mp3Grid.i18n('Add a page'),
                        url : 'admin/cms/page/page/add'
                    }/*,
                    root : {
                        label : mp3Grid.i18n('Add a root'),
                        url : 'admin/cms/page/root/add'
                    }*/
                },
                grid : {
                    proxyUrl : 'admin/cms/page/list/json',
                    columns : {
                        title : {
                            headerText : mp3Grid.i18n('Title'),
                            dataKey : 'title',
                            sortDirection : 'ascending'
                        },
                        lang : {
                            lang : true
                        },
                        url : {
                            headerText : mp3Grid.i18n('Virtual url'),
                            visible : false,
                            dataKey : 'url'
                        },
                        published : {
                            headerText : mp3Grid.i18n('Status'),
                            dataKey : 'publication_status'
                        },
                        actions : {
                            actions : ['edit', 'delete', 'visualise']
                        }
                    }
                },
                treeGrid : {
                    proxyUrl : 'admin/cms/page/list/tree_json'
                },
                defaultView : 'treeGrid',
                inspectors : {
                    roots : {
                        widget_id : 'cms_page_roots',
                        vertical : true,
                        hide : true,
                        label : mp3Grid.i18n('Roots'),
                        url : 'admin/cms/page/inspector/root/list',
                        inputName : 'rac_id',
                        grid : {
                            urlJson : 'admin/cms/page/inspector/root/json',
                            columns : {
                                title : {
                                    headerText : mp3Grid.i18n('Root'),
                                    dataKey : 'title'
                                }
                            }
                        }
                    }
                }
            }
        }
    }
});
