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
                        url : 'admin/admin/user/form/edit/' + item.id,
                        label : item.title
                    });
                }
            }
        };

    return $.extend(true, mp3Grid, {
        tab : {
            label : mp3Grid.i18n('Users'),
            iconUrl : 'static/cms/img/32/user.png'
        },
        mp3grid : {
            adds : {
                user : {
                    label : mp3Grid.i18n('Add a user'),
                    url : 'admin/admin/user/form/add'
                }
            },
            grid : {
                proxyUrl : 'admin/admin/user/list/json',
                columns : {
                    user : {
                        headerText : mp3Grid.i18n('User'),
                        dataKey : 'fullname',
                        sortDirection : 'ascending'
                    },
                    email : {
                        headerText : mp3Grid.i18n('Email'),
                        dataKey : 'email'
                    },
                    permissions : {
                        headerText : mp3Grid.i18n('Permissions'),
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
                                        $.nos.tabs.add({
                                            iframe : true,
                                            url: this.href
                                        });
                                        return false;
                                    });

                                return true;
                            }
                        }
                    },
                    actions : {
                        actions : [
                            actions.edit
                        ]
                    }
                }
            }
        }
    });
});
