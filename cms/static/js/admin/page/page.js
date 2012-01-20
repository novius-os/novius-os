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
    return $.nos.mp3GridSetup({
        tab : {
            label : 'Pages',
            iconUrl : 'static/cms/img/32/page.png'
        },
        adds : {
            page : {
                label : 'Add a Page',
                url : 'admin/admin/page/page/add'
            },
            root : {
                label : 'Add a root',
                iconClasses : 'nos-icon16 nos-icon16-root',
                url : 'admin/admin/page/root/add'
            }
        },
        proxyUrl : 'admin/admin/page/list/json',
        columns : {
            title : {
                headerText : 'Title',
                dataKey : 'title',
                cellFormatter : function(args) {
                    if ($.isPlainObject(args.row.data)) {
                        args.$container.closest('td').attr('title', args.row.data.title);

                        $('<a href="admin/admin/page/form/edit/' + args.row.data.id + '"></a>')
                            .text(args.row.data.title)
                            .appendTo(args.$container)
                            .click(function(e) {
                                $.nos.tabs.add({
                                    iframe : true,
                                    url : this.href
                                });
                                e.preventDefault();
                            });

                        return true;
                    }
                }
            },
            url : {
                headerText : 'Virtual url',
                dataKey : 'url'
            }
        },
        inspectors : {
            roots : {
                widget_id : 'inspector-root',
                vertical : true,
                label : 'Roots',
                iconClasses : 'nos-icon16 nos-icon16-root',
                url : 'admin/admin/page/inspector/root/list'
            },
            directories : {
                widget_id : 'inspector-tree',
                vertical : true,
                label : 'Directories',
                iconClasses : 'nos-icon16 nos-icon16-root',
                url : 'admin/admin/page/inspector/tree/list'
            }
        }
    });
});
