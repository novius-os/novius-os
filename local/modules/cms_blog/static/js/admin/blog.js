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
            label : 'Blog',
            iconUrl : 'static/modules/cms_blog/img/32/blog.png'
        },
        proxyUrl : 'admin/cms_blog/list/json',
        adds : {
            post : {
                label : 'Add a post',
                url : 'admin/cms_blog/form/edit'
            },
            category : {
                label : 'Add a category',
                url : 'admin/cms_blog/categoryform'
            }
        },
        columns : {
            title : {
                headerText : 'Title',
                cellFormatter : function(args) {
                    if ($.isPlainObject(args.row.data)) {
                        args.$container.closest("td").attr("title", args.row.data.title);

                        $("<a href=\"admin/cms_blog/form?id=" + args.row.data.id + "\"></a>")
                            .text(args.row.data.title)
                            .appendTo(args.$container);

                        return true;
                    }
                },
                dataKey : 'title'
            },
            lang : true,
            author : {
                headerText : 'Author',
                dataKey : 'author'
            },
            data : {
                headerText : 'Date',
                dataKey : 'date',
                dataFormatString  : 'MM/dd/yyyy HH:mm:ss',
                showFilter : false
            },
            actions : true
        },
        actions : {
            update : {
                action : function(args) {
                    $.nos.tabs.openInNewTab({
                        url     : "admin/cms_blog/form/edit/" + args.row.data.id,
                        label   : "Update"
                    });
                },
                label : 'Update'
            },
            delete : {
                action : function(args) {
                    $.nos.ajax.request({
                        url: "admin/cms_blog/list/delete/" + args.row.data.id,
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
                label : 'Delete'
            }
        },
        inspectors : {
            categories : {
                widget_id : 'inspector-category',
                label : 'Categories',
                vertical : true,
                url : 'admin/cms_blog/inspector/category/list'
            },
            tags : {
                widget_id : 'inspector-tag',
                hide : true,
                label : 'Tags',
                url : 'admin/cms_blog/inspector/tag/list'
            },
            authors : {
                widget_id : 'inspector-author',
                label : 'Authors',
                url : 'admin/cms_blog/inspector/author/list'
            },
            publishDate : {
                widget_id : 'inspector-publishdate',
                vertical : true,
                label : 'Publish date',
                url : 'admin/cms_blog/inspector/date/list'
            },
            language : {
                widget_id : 'inspector-lang',
                vertical : true,
                label : 'Language',
                url : 'admin/cms_blog/inspector/lang/list',
                languages : {
                    fr : 'Français',
                    en : 'Anglais'
                }
            }
        },
        splittersVertical :  250
    });
    return mp3Grid;
});
