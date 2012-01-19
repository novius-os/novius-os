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
    return new $.nos.Mp3Grid({
        tab : {
            label : 'Users',
            iconUrl : 'static/cms/img/32/user.png'
        },
        adds : {
            user : {
                label : 'Add a user',
                url : 'admin/admin/user/form/add'
            }
        },
        proxyUrl : 'admin/admin/user/list/json',
        columns : {
            user : {
                headerText : 'User',
                dataKey : 'fullname',
                cellFormatter : function(args) {
                    if ($.isPlainObject(args.row.data)) {
                        args.$container.closest("td").attr("title", args.row.data.fullname);

                        $("<a href=\"admin/admin/user/form/edit/" + args.row.data.id + "\"></a>")
                            .text(args.row.data.fullname)
                            .appendTo(args.$container)
                            .click(function(e) {
                                $.nos.tabs.openInNewTab({
                                    url : this.href
                                });
                                e.preventDefault();
                            });

                        return true;
                    }
                }
            },
            email : {
                headerText : 'Email',
                dataKey : 'email'
            },
            permissions : {
                headerText : 'Permissions',
                allowSizing : false,
                width : 1,
                showFilter : false,
                cellFormatter : function(args) {
                    if ($.isPlainObject(args.row.data)) {
                        args.$container.css("text-align", "center");

                        $("<a href=\"admin/admin/user/group/permission/edit/" + args.row.data.id_permission + "\"></a>")
                            .addClass("ui-state-default")
                            .append("<img src=\"static/cms/img/icons/tick.png\" />")
                            .appendTo(args.$container)
                            .click(function() {
                                $.nos.tabs.openInNewTab({
                                    url: this.href
                                });
                                return false;
                            });

                        return true;
                    }
                }
            }
        }
    });
});
