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
                pick : {
                    label : mp3Grid.i18n('Pick'),
                    action : function(item) {
                        $.nos.listener.fire("tinymce.image_select", true, [item]);
                    }
                }
            },
            mp3grid : {
                grid : {
                    id : 'cms_media_grid_tinymce',
                    columns : {
                        actions : {
                            actions : ['pick', 'edit', 'delete', 'visualize']
                        }
                    }
                },
                thumbnails : {
                    actions : ['pick', 'edit', 'delete', 'visualize']
                },
                values: {
                    media_extension: ['image']
                },
                inspectors : {
                    extensions : {
                        hide : true
                    }
                },
                // Another solution is to remove the extensions inspector in the "Order" property
                inspectorsOrder : 'folders,preview'
            }
        }
    }
});