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
                label : mp3Grid.i18n('Users'),
                iconUrl : 'static/cms/img/32/user.png'
            },
            actions : {
                edit : {
                    label : mp3Grid.i18n('Edit'),
                    icon : 'pencil',
                    primary : true,
                    action : function(item, ui) {
                        $.nos.tabs.add({
                            url : 'admin/cms/user/form/edit/' + item.id,
                            label : item.title
                        });
                    }
                },
                'delete' : {
                    label : mp3Grid.i18n('Delete'),
                    icon : 'trash',
                    primary : true,
                    action : function(item, ui) {
                        $.nos.dialog({
                            contentUrl: 'admin/cms/user/user/delete_user/' + item.id,
                            ajax : true,
                            title: mp3Grid.i18n('Delete a user')._(),
                            width: 400,
                            height: 150
                        });
                    }
                }
            },
            mp3grid : {
                adds : {
                    user : {
                        label : mp3Grid.i18n('Add a user'),
                        action : function() {
                            $.nos.tabs.add({
                                url : 'admin/cms/user/form/add',
                                label : mp3Grid.i18n('Add a user')._()
                            });
                        }
                    }
                },
                grid : {
                    proxyUrl : 'admin/cms/user/list/json',
                    columns : {
                        user : {
                            headerText : mp3Grid.i18n('Name'),
                            dataKey : 'fullname',
                            sortDirection : 'ascending'
                        },
                        email : {
                            headerText : mp3Grid.i18n('Email'),
                            dataKey : 'email'
                        },
                        actions : {
                            actions : ['edit', 'delete']
                        }
                    }
                }
            }
        }
    }
});
