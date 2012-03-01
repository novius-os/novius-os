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

        // Remove all primary actions
        $.each(mp3Grid.actions, function() {
            this.primary = false;
        });

        // Add "pick" as unique primary action
        mp3Grid.mp3grid.grid.columns.actions.actions.unshift('pick');
        mp3Grid.mp3grid.thumbnails.actions.unshift('pick');

        return {
            actions : {
                pick : {
                    primary : true,
                    label : mp3Grid.i18n('Pick'),
                    icon : 'check',
                    action : function(item) {
                        $.nos.listener.fire("tinymce.image_select", true, [item]);
                    }
                }
            },
            mp3grid : {
                grid : {
                    id : 'cms_media_grid_tinymce'
                },
                values: {
                    media_extension: ['image']
                },
                inspectors : {
                    extensions : {
                        hide : true
                    }
                }
                // Another solution is to remove the extensions inspector in the "Order" property
                //inspectorsOrder : 'folders,preview'
            }
        }
    }
});