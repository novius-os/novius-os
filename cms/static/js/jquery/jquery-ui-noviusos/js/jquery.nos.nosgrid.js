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
], function( $, undefined ) {
	$.widget( "nos.nosgrid", $.wijmo.wijgrid, {
		options: {
            texts : {
                loading : 'Loading...'
            }
		},

		_create: function() {
			var self = this;

            $.wijmo.wijgrid.prototype._create.call(self);
		},

		_init: function() {
			var self = this,
				o = self.options;

			if ($.isFunction(o.dataLoading)) {
                var old_dataLoading = o.dataLoading;
            }
            o.dataLoading = function() {
                self._overlayShow();
                if ($.isFunction(old_dataLoading)) {
                    old_dataLoading.apply(this, arguments);
                }
            };
            if ($.isFunction(o.dataLoaded)) {
                var old_dataLoaded = o.dataLoaded;
            }
            o.dataLoaded = function() {
                self._overlayHide();
                if ($.isFunction(old_dataLoaded)) {
                    old_dataLoaded.apply(this, arguments);
                }
            };

            $.wijmo.wijgrid.prototype._init.call(self);
		},

		_overlayShow : function() {
			var self = this,
                o = self.options;

            self.uiOverlay = $('<div></div>')
                .addClass('wijmo-wijgrid-overlay ui-widget-overlay')
                .appendTo(self.outerDiv);
            self.uiOverlayText = $('<span><span></span>' + o.texts.loading + '</span>')
                .addClass('wijmo-wijgrid-loadingtext ui-widget-content ui-corner-all')
                .find('span')
                .addClass('ui-icon ui-icon-clock')
                .end()
                .appendTo(self.outerDiv);

            self.uiOverlayText.css({
                marginLeft : (self.uiOverlayText.width() * -1 / 2) + 'px',
                marginTop : (self.uiOverlayText.height() * -1 / 2) + 'px'
            });

			return self;
		},

        _overlayHide : function() {
            var self = this;

            self.uiOverlay.remove();
            self.uiOverlayText.remove();

            return self;
        }
    });
	return $;
});
