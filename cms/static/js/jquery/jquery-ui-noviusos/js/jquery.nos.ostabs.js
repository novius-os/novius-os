/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define([
	'jquery-nos',
	'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.loadspinner'
], function( $ ) {
	(function(undefined ) {
		$.widget( "nos.ostabs", {
			options: {
				initTabs: [], // e.g. [{"url":"http://www.google.com","iconUrl":"img/google-32.png","label":"Google","iconSize":32,"labelDisplay":false,"pined":true},{"url":"http://www.twitter.com","iconClasses":"ui-icon ui-icon-signal-diag","label":"Twitter","pined":true}]
				newTab: 'appsTab', // e.g. {"url":"applications.htm","label":"Open a new tab","ajax":true} or "appsTab" or false
				trayTabs: [], // e.g. [{"url":"account.php","iconClasses":"ui-icon ui-icon-contact","label":"Account"},{"url":"customize.htm","iconClasses":"ui-icon ui-icon-gear","label":"Customization"}]
				appsTab: {}, // e.g. {"panelId":"ospanel","url":"applications.htm","iconUrl":"os.png","label":"My OS","ajax":true} or false
				fx: null, // e.g. { height: 'toggle', opacity: 'toggle', duration: 200 }
				labelMinWidth: 100,
				labelMaxWidth: 200,

				texts : {
					scrollLeft : 'Scroll left',
					scrollRight : 'Scroll right',
					newTab: 'New tab',
					removeTab: 'Remove tab',
					closeTab: 'Close tab',
					pinTab: 'Pin tab',
					unpinTab: 'Unpin tab',
					reloadTab: 'Reload tab',
					spinner: 'Loading...'
				},

				// callbacks
				add: null,
				pin: null,
				unpin: null,
				remove: null,
				select: null,
				show: null
			},

			tabId : 0,
			pined: [],
			stackOpening: [],

			_create: function() {
				this._tabify( true );
			},

			// TODO : revoir ?
			_setOption: function( key, value ) {
				if ( key == "selected" ) {
					this.select( value );
				} else {
					this.options[ key ] = value;
					this._tabify();
				}
			},

			_getNextTabId: function() {
				return ++this.tabId;
			},

			_tabId: function( a ) {
				return $( a ).data( "panelid.tabs" ) || "nos-ostabs-" + this._getNextTabId();
			},

			_sanitizeSelector: function( hash ) {
				// we need this because an id may contain a ":"
				return hash.replace( /:/g, "\\:" );
			},

			_ui: function( li, panel ) {
				var self = this,
					index = this.lis.index( li );
				if ( panel === undefined ) {
					panel = self.element.find( self._sanitizeSelector( self.anchors[ index ].hash ) )[ 0 ];
				}

				return {
					tab: li,
					panel: panel,
					index: index
				};
			},

			_cleanup: function() {
				// restore all former loading tabs labels
				this.lis.filter( ".ui-state-processing" )
					.removeClass( "ui-state-processing" )
					.find( "span.nos-ostabs-icon" )
					.removeClass( 'nos-ostabs-loader' )
					.each(function() {
						if ( $.isFunction($.fn.loadspinner) ) {
							$(this).loadspinner( 'destroy' );
						}
					})
					.html( ' ' )
					.parent()
					.find( "span.nos-ostabs-label" )
					.each(function() {
						var el = $( this ),
							html = el.data( "label.tabs" );

						if ( html ) {
							el.html( html ).removeData( "label.tabs" );
						}
					});
			},

			_tabify: function( init ) {
				var self = this,
					o = this.options,
					fragmentId = /^#.+/; // Safari 2 reports '#' for an empty hash

				// initialization from scratch
				if ( init ) {
					this.element.addClass( "nos-ostabs ui-widget ui-widget-content" );

					this.uiOstabsHeader = $( '<div></div>' )
						.addClass( 'ui-widget-header' )
						.appendTo( this.element );

					this.uiOstabsTabsContainer = $( '<div></div>' )
						.addClass('nos-ostabs-tabs')
						.appendTo( this.uiOstabsHeader );

					this.uiOstabsScrollLeft = $( '<a href="#"></a>' )
						.addClass( 'nos-ostabs-scroll-left' );
					$('<span></span>').addClass( 'ui-icon ui-icon-triangle-1-w' )
						.text(this.options.texts.scrollLeft)
						.appendTo(this.uiOstabsScrollLeft);

					this.uiOstabsScrollRight = $('<a href="#"></a>')
						.addClass( 'nos-ostabs-scroll-right' );
					$('<span></span>').addClass( 'ui-icon ui-icon-triangle-1-e' )
						.text(this.options.texts.scrollRight)
						.appendTo(this.uiOstabsScrollRight);

					this.uiOstabsScrollRight.add( this.uiOstabsScrollLeft )
						.addClass( 'ui-state-default ui-corner-all' )
						.attr( 'role', 'button' )
						.mouseenter(function() {
							var scroll = this;
							$( scroll ).data( 'nos-ostabs-hover', true );
							var inter = setInterval(function() {
								if ( self.sorting && $( scroll ).data( 'nos-ostabs-hover' ) ) {
									self._scroll( scroll !== self.uiOstabsScrollRight.get(0) );
								} else {
									clearInterval( inter );
								}
							}, 200);
							self.uiOstabsScrollRight.addClass( 'ui-state-hover' );
						}).mouseleave(function() {
							$( this ).data( 'nos-ostabs-hover', false );
							self.uiOstabsScrollRight.removeClass( 'ui-state-hover' );
						}).focus(function() {
							self.uiOstabsScrollRight.addClass( 'ui-state-focus' );
						}).blur(function() {
							self.uiOstabsScrollRight.removeClass( 'ui-state-focus' );
						}).click(function() {
							self._scroll(this !== self.uiOstabsScrollRight.get(0));
							return false;
						}).appendTo( this.uiOstabsTabsContainer );

					this.uiOstabsTabsWrap = $( '<div></div>' ).appendTo( this.uiOstabsTabsContainer );
					this.uiOstabsTabs = $( '<ul></ul>' ).appendTo( this.uiOstabsTabsWrap );

					if ( $.isArray( this.options.initTabs ) ) {
						$.each( this.options.initTabs, function(i, el) {
							self._add( el );
						} );
					}

					newTab = this.options.newTab;
					if ( $.isPlainObject(newTab) ) {
						newTab = $.extend({
							label: this.options.texts.newTab,
							iconClasses: 'ui-icon ui-icon-circle-plus'
						}, newTab, {
							pined: true
						} );
					} else if ( newTab && $.isPlainObject(this.options.appsTab) ) {
						newTab = $.extend( {}, this.options.appsTab, {
							label: this.options.texts.newTab,
							iconClasses: 'ui-icon ui-icon-circle-plus',
							iconUrl: '',
							iconSize: 16,
							pined: true
						});
					} else {
						newTab = false;
					}

					if ( newTab ) {
						this.uiOstabsNewTab = self._add( newTab ).addClass( 'nos-ostabs-newtab' );
					} else {
						this.uiOstabsNewTab = $ ( '<li>/<li>' );
					}

					this.uiOstabsTabs.sortable({
						items: 'li:not(.nos-ostabs-newtab)',
						appendTo: this.uiOstabsTabsContainer,
						containment: this.uiOstabsTabsContainer,
						cursor: 'move',
						delay: 250,
						scroll: false,
						helper: 'clone',
						tolerance: 'pointer',
						axis: 'x',
						zIndex : 100000,
						placeholder: "ui-state-highlight",
						forcePlaceholderSize: true,
						start: function() {
							self.sorting = true;
						},
						stop: function() {
							self.sorting = false;
						},
						update: function() {
							self.lis = self.uiOstabsAppsTab
								.add( self.uiOstabsTray )
								.add( self.uiOstabsTabs )
								.find( "li:has(a[href])" );
							self.anchors = self.lis.map(function(i) {
								var anchor = $( "a", this )[ 0 ];
								self.element.find( self._sanitizeSelector( anchor.hash ) )
									.find( 'iframe.nos-ostabs-panel-content' )
									.data( 'nos-ostabs-index', i );
								return anchor;
							});

							self._tabsWidth();
						}
					});

					this.uiOstabsTray = $( '<ul></ul>' )
						.addClass( 'nos-ostabs-tray nos-ostabs-nav' );
					if ( $.isArray( this.options.trayTabs ) ) {
						$.each( this.options.trayTabs, function(i, el) {
							if ( $.isPlainObject(el) ) {
								$.extend( el, {pined : true} );
								self._add( el, self.uiOstabsTray )
									.addClass( 'nos-ostabs-tray' );
							}
						} );
					}
					this.uiOstabsTray.prependTo(this.uiOstabsHeader);

					if ( $.isPlainObject(this.options.appsTab) ) {
						this.uiOstabsAppsTab = $( '<ul></ul>' )
							.addClass( 'nos-ostabs-appstab  nos-ostabs-nav' )
							.prependTo( this.uiOstabsHeader );
						self._add( this.options.appsTab, this.uiOstabsAppsTab )
						.addClass( 'nos-ostabs-appstab' )
						.removeClass( 'ui-state-default' );
					} else {
						this.uiOstabsAppsTab = $( '<ul></ul>' );
					}

					this.uiOstabsTabsContainer.css( 'left', this.uiOstabsAppsTab.outerWidth( true ) );
					this.uiOstabsTabsContainer.css( 'right', this.uiOstabsTray.outerWidth( true ) );

					this.tabsWidth = this.uiOstabsTabsContainer.width();
					this.labelWidth = this.options.labelMaxWidth;
					this.uiOstabsTabs.width( this.tabsWidth );

					$('<style type="text/css" class="statepined">.nos-ostabs .ui-widget-header li.ui-state-pined {color : ' + this.uiOstabsHeader.css('color') + ';background:transparent;}</style>').appendTo('head');
				}

				this.lis = this.uiOstabsAppsTab.add(this.uiOstabsTray).add(this.uiOstabsTabs).find("li:has(a[href])");
				this.anchors = this.lis.map(function() {
					return $( "a", this )[ 0 ];
				});
				this.panels = $( [] );

				this.anchors.each(function( i, a ) {
					var href = $( a ).attr( "href" );
					// For dynamically created HTML that contains a hash as href IE < 8 expands
					// such href to the full page url with hash and then misinterprets tab as ajax.
					// Same consideration applies for an added tab with a fragment identifier
					// since a[href=#fragment-identifier] does unexpectedly not match.
					// Thus normalize href attribute...
					var hrefBase = href.split( "#" )[ 0 ],
						baseEl;
					if ( hrefBase && ( hrefBase === location.toString().split( "#" )[ 0 ] ||
							( baseEl = $( "base" )[ 0 ]) && hrefBase === baseEl.href ) ) {
						href = a.hash;
						a.href = href;
					}

					// inline tab
					if ( fragmentId.test( href ) ) {
						self.panels = self.panels.add( self.element.find( self._sanitizeSelector( href ) ) );
					// remote tab
					// prevent loading the page itself if href is just "#"
					} else if ( href && href !== "#" ) {
						// required for restore on destroy
						$.data( a, "href.tabs", href );

						// TODO until #3808 is fixed strip fragment identifier from url
						// (IE fails to load from such url)
						$.data( a, "load.tabs", href.replace( /#.*$/, "" ) );

						var id = self._tabId( a );
						a.href = "#" + id;
						var $panel = self.element.find( "#" + id );
						if ( !$panel.length ) {
							$li = self.lis.eq(i);
							$panel = $( '<div></div>' )
								.attr( "id", id )
								.addClass( "nos-ostabs-panel ui-widget-content ui-corner-bottom nos-ostabs-hide" + ($li.hasClass('ui-state-pined') ? ' ui-state-pined' : ''))
								.appendTo( self.element );

							self._actions($panel, i);
						}
						self.panels = self.panels.add( $panel );
					}
				});

				// initialization from scratch
				if ( init ) {
					// attach necessary classes for styling
					this.uiOstabsTabs.add( this.uiOstabsTray )
						.add( this.uiOstabsAppsTab )
						.addClass( "nos-ostabs-nav ui-helper-reset ui-helper-clearfix" );
					this.lis.addClass( "ui-corner-top" );
					this.panels.addClass( "nos-ostabs-panel ui-widget-content" );

					// Selected tab
					// use "selected" option or try to retrieve:
					// 1. from fragment identifier in url
					// 2. from selected class attribute on <li>
					if ( o.selected === undefined ) {
						if ( location.hash ) {
							this.anchors.each(function( i, a ) {
								if ( a.hash == location.hash ) {
									o.selected = i;
									return false;
								}
							});
						}
						if ( typeof o.selected !== "number" && this.lis.filter( ".nos-ostabs-selected" ).length ) {
							o.selected = this.lis.index( this.lis.filter( ".nos-ostabs-selected" ) );
						}
						o.selected = o.selected || ( this.lis.length ? 0 : -1 );
					}

					// sanity check - default to first tab...
					o.selected = ( ( o.selected >= 0 && this.anchors[ o.selected ] ) || o.selected < 0 )
						? o.selected
						: 0;

					self.pined = $.map( this.lis.filter( ".ui-state-pined" ), function( n, i ) {
							return self.lis.index( n );
						}).sort();

					// highlight selected tab
					this.panels.addClass( "nos-ostabs-hide" );
					this.lis.removeClass( "nos-ostabs-selected ui-state-active" );
					// check for length avoids error when initializing empty list
					if ( o.selected >= 0 && this.anchors.length ) {
						self.element.find( self._sanitizeSelector( self.anchors[ o.selected ].hash ) ).removeClass( "nos-ostabs-hide" );
						this.lis.eq( o.selected ).addClass( "nos-ostabs-selected ui-state-active" );

						// seems to be expected behavior that the show callback is fired
						self.element.queue( "tabs", function() {
							self._trigger( "show", null, self._ui( self.lis[ o.selected ] ) );
						});

						this.title(o.selected, this.title(o.selected));

						this._load( o.selected );
					}

					// clean up to avoid memory leaks in certain versions of IE 6
					// TODO: namespace this event
					$( window ).bind( "unload", function() {
						self.lis.add( self.anchors ).unbind( ".tabs" );
						self.lis = self.anchors = self.panels = null;
					});
				// update selected after add/remove
				} else {
					o.selected = this.lis.index( this.lis.filter( ".nos-ostabs-selected" ) );
				}

				// reset cache if switching from cached to not cached
				this.anchors.removeData( "cache.tabs" );

				// remove all handlers before, tabify may run on existing tabs after add or option change
				this.lis.add( this.anchors ).unbind( ".tabs" );

				var addState = function( state, el ) {
					el.removeClass( "ui-state-pined").addClass( "ui-state-" + state );
				};
				var removeState = function( state, el ) {
					el.removeClass( "ui-state-" + state );
					if ($.inArray( self.lis.index(el), self.pined ) !== -1 && !el.is('.ui-state-active') ) {
						el.addClass( "ui-state-pined");
					}
				};
				this.lis.bind( "mouseover.tabs" , function() {
					addState( "hover", $( this ) );
				});
				this.lis.bind( "mouseout.tabs", function() {
					removeState( "hover", $( this ) );
				});
				this.anchors.bind( "focus.tabs", function() {
					addState( "focus", $( this ).closest( "li" ) );
				});
				this.anchors.bind( "blur.tabs", function() {
					removeState( "focus", $( this ).closest( "li" ) );
				});

				// set up animations
				var hideFx, showFx;
				if ( o.fx ) {
					if ( $.isArray( o.fx ) ) {
						hideFx = o.fx[ 0 ];
						showFx = o.fx[ 1 ];
					} else {
						hideFx = showFx = o.fx;
					}
				}

				// Reset certain styles left over from animation
				// and prevent IE's ClearType bug...
				function resetStyle( $el, fx ) {
					$el.css( "display", "" );
					if ( !$.support.opacity && fx.opacity ) {
						$el[ 0 ].style.removeAttribute( "filter" );
					}
				}

				// Show a tab...
				var showTab = showFx
					? function( clicked, $show ) {
						var $li = $( clicked ).closest( "li" ).addClass( "nos-ostabs-selected ui-state-active" ).removeClass( 'ui-state-pined' );
						$show.hide().removeClass( "nos-ostabs-hide" ) // avoid flicker that way
							.animate( showFx, showFx.duration || "normal", function() {
								if ( $li.hasClass( 'nos-ostabs-newtab' ) ) {
									self._tabsWidth();
								}
								self._scrollTo( $li );
								// TODO ?
								// Gilles : Bug avec effet la class hide réapparait, sans doute à cause de la double création de panel au add
								//$( this ).removeClass( "nos-ostabs-hide" );
								resetStyle( $show, showFx );
							});
					}
					: function( clicked, $show ) {
						var $li = $( clicked ).closest( "li" );
						if ( $li.hasClass( 'nos-ostabs-newtab' ) ) {
							self._tabsWidth();
						}
						self._scrollTo( $li );
						$li.addClass( "nos-ostabs-selected ui-state-active" ).removeClass( 'ui-state-pined' );
						$show.removeClass( "nos-ostabs-hide" );
					};

				// Hide a tab, $show is optional...
				var hideTab = hideFx
					? function( clicked, $hide ) {
						$hide.animate( hideFx, hideFx.duration || "normal", function() {
							if ( self.uiOstabsNewTab.hasClass( 'ui-state-active' ) ) {
								self._tabsWidth();
							}
							self.lis.removeClass( "nos-ostabs-selected ui-state-active" );
							$hide.addClass( "nos-ostabs-hide" );
							resetStyle( $hide, hideFx );
							self.element.dequeue( "tabs" );
						});
					}
					: function( clicked, $hide ) {
						if ( self.uiOstabsNewTab.hasClass( 'ui-state-active' ) ) {
							self._tabsWidth();
						}
						self.lis.removeClass( "nos-ostabs-selected ui-state-active" );
						$hide.addClass( "nos-ostabs-hide" );
						self.element.dequeue( "tabs" );
					};

				// attach tab event handler, unbind to avoid duplicates from former tabifying...
				this.anchors.bind( "click.tabs", function() {
					var el = this,
						$li = $(el).closest( "li" ),
						$hide = self.panels.filter( ":not(.nos-ostabs-hide)" ),
						$show = self.element.find( self._sanitizeSelector( el.hash ) );

					$li.addClass( "ui-state-open" ).removeClass('ui-state-pined');
					self.uiOstabsNewTab.removeClass('ui-state-open ui-state-default');

					// If tab selected or
					// or is already loading or click callback returns false stop here.
					// Check if click handler returns false last so that it is not executed
					// for a pined or loading tab!
					if ($li.hasClass( "nos-ostabs-selected" ) ||
						$li.hasClass( "ui-state-processing" ) ||
						self.panels.filter( ":animated" ).length ||
						self._trigger( "select", null, self._ui( $li[ 0 ], $show[ 0 ] ) ) === false ) {
						this.blur();
						return false;
					}

					o.selected = self.anchors.index( this );

					self._abort();

					// show new tab
					if ( $show.length ) {
						self._trigger( "show", null, self._ui( $li[ 0 ], $show[ 0 ] ) );
						
						if ( $hide.length ) {
							self.element.queue( "tabs", function() {
								hideTab( el, $hide );
							});
						}
						self.element.queue( "tabs", function() {
							self.stackOpening.push(el);
							showTab( el, $show );
						});

						$( 'title' ).text( $li.find( '.nos-ostabs-label' ).text() );

						self._load( self.anchors.index( this ) );
					} else {
						throw "jQuery UI Tabs: Mismatching fragment identifier.";
					}

					this.blur();
				});

				// disable click in any case
				this.anchors.bind( "click.tabs", function(){
					return false;
				});

				this._tabsWidth();
			},

			_getIndex: function( index ) {
				// meta-function to give users option to provide a href string instead of a numerical index.
				// also sanitizes numerical indexes to valid values.
				if ( typeof index == "string" ) {
					index = this.anchors.index( this.anchors.filter( "[href$=" + index + "]" ) );
				}

				return index;
			},

			_actions: function( $panel, index ) {
				var self = this,
					o = this.options;

				var li = self.lis.eq(index),
					a =  self.anchors.eq(index);

				var actions = $( '<div></div>' )
					.addClass( 'nos-ostabs-actions ui-state-active' )
					.prependTo( $panel );
					
				var links = $( '<div></div>' )
					.addClass( 'nos-ostabs-actions-links' )
					.prependTo( actions );

				var removable = li.not( '.nos-ostabs-tray' ).not( '.nos-ostabs-appstab' ).not( '.nos-ostabs-newtab' ).length;
				var closable = li.not( '.nos-ostabs-appstab' ).length;
				var reloadable = !a.data( "ajax.tabs" );

				if ( closable ) {
					var close = $( '<a href="#"></a>' )
						.addClass( 'nos-ostabs-close' )
						.click(function() {
							self.remove( self.lis.index(li) ); // On recalcule l'index au cas où l'onglet est été déplacé
							return false;
						})
						.appendTo( links );
					$( '<span></span>' ).addClass( 'ui-icon ui-icon-closethick' )
						.text( removable && !$.inArray( index, self.pined ) ? o.texts.removeTab : o.texts.closeTab )
						.appendTo( close );
					$( '<span></span>' ).text( removable && !$.inArray( index, self.pined ) ? o.texts.removeTab : o.texts.closeTab )
						.appendTo( close );
				}

				if ( removable ) {
					var pin = $( '<a href="#"></a>' )
						.addClass( 'nos-ostabs-pin' )
						.click(function() {
							self.pin( self.lis.index(li) );
							return false;
						})
						.text( o.texts.pinTab )
						.appendTo( links );
					$( '<span></span>' ).addClass( 'ui-icon ui-icon-pin-s' )
						.text( o.texts.pinTab )
						.appendTo( pin );

					var unpin = $( '<a href="#"></a>' )
						.addClass( 'nos-ostabs-unpin' )
						.click(function() {
							self.unpin( self.lis.index(li) );
							return false;
						})
						.text( o.texts.unpinTab )
						.appendTo( links );
					$( '<span></span>' ).addClass( 'ui-icon ui-icon-pin-w' )
						.text( o.texts.unpinTab )
						.appendTo( unpin );
				}

				if ( reloadable ) {
					var reload = $( '<a href="#"></a>' )
						.addClass( 'nos-ostabs-reload' )
						.click(function() {
							var fr = $panel.find( 'iframe.nos-ostabs-panel-content' );
							if (fr !== undefined) {
								fr.attr("src", fr.attr("src"));
							}
							return false;
						})
						.text( o.texts.reloadTab )
						.appendTo( links );
					$( '<span></span>' ).addClass( 'ui-icon ui-icon-refresh' )
						.text( o.texts.pinTab )
						.appendTo( reload );
				}

			},

			_tabsWidth: function() {
				var width = 0;
				this.uiOstabsTabs.width( 10000000 );
				this.uiOstabsTabs.find( 'li' )
					.each(function() {
						width += $( this ).outerWidth( true );
					});
				this.uiOstabsTabs.width( width );

				var nbLabel = this.uiOstabsTabs.find( '.nos-ostabs-label:visible' ).length,
					add;
				if ( this.tabsWidth < this.uiOstabsTabs.width() ) {
					while ( this.tabsWidth < this.uiOstabsTabs.width() && this.labelWidth > this.options.labelMinWidth ) {
						add = this.labelWidth - this.options.labelMinWidth;
						add = add > 10 ? 10 : add;
						width = width - nbLabel * add;
						this.uiOstabsTabs.width( width );
						this.labelWidth = this.labelWidth - add;
						$( 'head .tabswidth' ).remove();
						$( '<style type="text/css" class="tabswidth">.nos-ostabs .ui-widget-header .nos-ostabs-label {width : ' + this.labelWidth + 'px !important;}</style>' ).appendTo( 'head' );
					}
				} else {
					do {
						add = this.options.labelMaxWidth - this.labelWidth;
						add = add > 10 ? 10 : add;
						if ( this.tabsWidth > (width + nbLabel * 10) ) {
							width = width + nbLabel * add;
							this.uiOstabsTabs.width( width );
							this.labelWidth = this.labelWidth + add;
							$( 'head .tabswidth' ).remove();
							$( '<style type="text/css" class="tabswidth">.nos-ostabs .ui-widget-header .nos-ostabs-label {width : ' + this.labelWidth + 'px !important;}</style>' ).appendTo( 'head' );
						}
					} while ( this.tabsWidth > (width + nbLabel * 10) && this.labelWidth < this.options.labelMaxWidth );
				}
				this._scrollState();
			},

			_scroll: function( back ) {
				var self = this,
					left = parseInt( self.uiOstabsTabs.css( 'left' ).replace( 'px', '' ) );

				if ( (back && left >= 0) || (!back && (left + self.uiOstabsTabs.width()) <= self.uiOstabsTabsWrap.width()) ) {
					return false;
				}

				var lis = this.uiOstabsTabs.find( 'li' );
				lis.each(function( i, el ) {
					var p = $( this ).position();
					if ( (left + p.left) >= 0 ) {
						if ( (!back && i < (lis.length - 1)) || (back && i > 0) ) {
							p = lis.eq( back ? i -1 : i + 1 ).position();
							left = p.left * -1;
							self.uiOstabsTabs.animate( {left : left + 'px'}, 200, function() {
								self._scrollState();
							});
						}
						return false;
					}
				});
			},

			_scrollTo: function( li ) {
				var self = this;
				var pos = li.position();
				var left = parseInt( self.uiOstabsTabs.css( 'left' ).replace( 'px', '' ) );
				if ( (pos.left + li.outerWidth(true) - left) < self.uiOstabsTabsWrap.width() ) {
					return true;
				}
				var lis = this.uiOstabsTabs.find( 'li' );
				lis.each(function() {
					var p = $( this ).position();
					if ( (pos.left + li.outerWidth( true ) - p.left) < self.uiOstabsTabsWrap.width() ) {
						left = p.left * -1;
						self.uiOstabsTabs.animate( {left : left + 'px'}, 200, function() {
							self._scrollState();
						});
						return false;
					}
				});
			},

			_scrollState: function() {
				if ( this.tabsWidth < this.uiOstabsTabs.width() ) {
					this.uiOstabsScrollRight.add( this.uiOstabsScrollLeft )
						.removeClass( 'ui-state-disabled' )
						.show();
					var left = parseInt( this.uiOstabsTabs.css('left').replace('px', '') );
					if ( left >= 0 ) {
						this.uiOstabsScrollLeft.addClass( 'ui-state-disabled' );
					}
					if ( left + this.uiOstabsTabs.width() < this.tabsWidth ) {
						this.uiOstabsScrollRight.addClass( 'ui-state-disabled' );
					}
				} else {
					this.uiOstabsScrollRight.add( this.uiOstabsScrollLeft )
						.removeClass( 'ui-state-disabled' )
						.hide();
				}
			},

			add: function( tab, index ) {
				if ( !$.isPlainObject(tab) || tab.url === undefined ) {
					return false;
				}

				if ( index === undefined ) {
					index = this.anchors.length - 1;
				}

				var self = this,
					$li = self._add(tab);

				if ( index < this.lis.length ) {
					$li.insertBefore( this.lis[ index ] );
				} else {
					index = this.lis.eq($li);
				}
				this.uiOstabsTabs.sortable( 'refresh' );

				self.pined = $.map( self.pined, function( n, i ) {
					return n >= index ? ++n : n;
				});

				this._tabify();

				if ( this.anchors.length == 1 ) {
					self.select(0);
				}

				this._trigger( "add", null, this._ui( $li[ 0 ] ) );
				return index;
			},

			_add: function(tab, target) {
				if ( !$.isPlainObject(tab) || tab.url === undefined ) {
					return false;
				}

				target = target || this.uiOstabsTabs;

				tab = $.extend({
					url: '',
					ajax: false,
					label: '',
					labelDisplay: true,
					iconClasses: 'ui-icon ui-icon-document',
					iconUrl: '',
					iconSize: 16,
					pined: false,
					panelId: false
				}, tab);


				var a = $( '<a href="' + tab.url + '"></a>' );
				if (tab.ajax) {
					a.data( "ajax.tabs", true );
				}
				if (tab.panelId) {
					a.data( "panelid.tabs", tab.panelId );
				}

				var icon = this._icon( tab ).appendTo( a );

				var label = $( '<span></span>' ).addClass( 'nos-ostabs-label' )
					.text( tab.label )
					.appendTo( a );
				if ( !tab.labelDisplay ) {
					label.hide();
				}

				var li = $( '<li></li>' ).append( a )
					.addClass( 'ui-corner-top ui-state-default' + (tab.pined ? ' ui-state-pined' : '') ).data( 'ui-ostab', tab )
					.appendTo( target );

				if ( !isNaN( tab.iconSize ) && tab.iconSize !== 16 && target !== this.uiOstabsTabs) {
					li.css({
						height: ( tab.iconSize + 4 ) + 'px',
						bottom: ( tab.iconSize - 35 ) + 'px'
					});
					icon.css( 'top', '2px' );
				}

				return li;
			},

			remove: function( index ) {
				index = this._getIndex( index );
				var self = this,
					o = this.options,
					$li = this.lis.eq( index ),
					$a = this.anchors.eq( index ),
					$panel = self.element.find( self._sanitizeSelector( self.anchors[ index ].hash ) );

				if ( index == 0 && !$li.hasClass( "nos-ostabs-selected" ) ) {
					var linewtab = this.lis.filter( '.nos-ostabs-newtab' );
					if ( linewtab.hasClass( "nos-ostabs-selected" ) ) {
						$li = linewtab;
					}
				}

				if ( $.inArray( index, this.pined ) === -1 && $li.not( '.nos-ostabs-tray' ).not( '.nos-ostabs-appstab' ).not( '.nos-ostabs-newtab' ).length ) {
					$li.remove();
					$panel.remove();
				}

				$li.removeClass( "ui-state-active ui-state-open" );
				
				// Remove tab from stack opening
				for (var i = 0; i < self.stackOpening.length; i++) {
					if (self.stackOpening[i] === $a.get(0)) {
						self.stackOpening.splice(i, 1);
					}
				}
				
				// Open the last tab in stack opening or the 0 index
				if (self.stackOpening.length) {
					this.select( this.anchors.index( self.stackOpening[self.stackOpening.length - 1] ) );
				} else {
					this.select( 0 );
				}

				if ( $li.not( '.nos-ostabs-appstab' ).not( '.nos-ostabs-newtab' ).length ) {
					$( '> *', $panel ).not( '.nos-ostabs-actions' ).remove();
				}
				$panel.addClass( "nos-ostabs-hide" );

				$li.removeClass( "nos-ostabs-selected" );
				if ( $.inArray( index, this.pined ) ) {
					$li.addClass( "ui-state-pined" );
				}

				if ( $.inArray( index, this.pined ) === -1 && $li.not( '.nos-ostabs-tray' ).not( '.nos-ostabs-appstab' ).not( '.nos-ostabs-newtab' ).length ) {
					self.pined = $.map( self.pined, function( n, i ) {
						return n >= index ? --n : n;
					});
				}

				this._tabify();

				this._trigger( "remove", null, this._ui( $li[ 0 ], $panel[ 0 ] ) );
				return this;
			},

			open: function( index ) {
				index = this._getIndex( index );
				var self = this, o = this.options;

				this.lis.eq( index ).addClass( "ui-state-open" ).removeClass( 'ui-state-pined' );

				this._trigger( "open", null, this._ui( this.lis[ index ] ) );

				return self;
			},

			title: function( index, title ) {
				index = this._getIndex( index );
				var self = this, o = this.options;

				var $li = this.lis.eq( index );
				if ( title === undefined ) {
					return $li.find( '.nos-ostabs-label' ).text();
				} else {
					$li.find( '.nos-ostabs-label' ).text( title );

					if ( this.options.selected == index ) {
						$( 'title' ).text( title );
					}

					this._trigger( "title", null, this._ui( $li[ 0 ] ) );

					return self;
				}
			},

			icon: function( index, icon ) {
				index = this._getIndex( index );

				var $li = this.lis.eq( index );
				if ( icon === undefined ) {
					return {
						icon : $.trim( $li.find( '.nos-ostabs-icon' ).attr( 'class' ).replace( 'nos-ostabs-icon', '' ).replace( /ui-icon-\d\d/, '' ) ),
						app : $li.find( '.nos-ostabs-icon' ).hasClass( 'ui-icon-32' ) ? true : false
					};
				} else {
					if ( !$.isPlainObject( icon ) ) {
						icon = {iconUrl : icon};
					}

					var tab = $li.data('ui-ostab');
					$.extend(tab, {
						iconClasses: '',
						iconUrl: ''
					}, icon);
					$li.data('ui-ostab', tab);

					$li.find( '.nos-ostabs-icon' ).replaceWith( this._icon( tab ) );

					this._trigger( "icon", null, this._ui( $li[ 0 ] ) );

					return this;
				}
			},

			update: function(index, tab) {
				index = this._getIndex( index );

				if ( !$.isPlainObject(tab) ) {
					return false;
				}

				var $li = this.lis.eq( index ),
					$a = $li.find( 'a' );

				tab = $.extend( {}, $li.data( 'ui-ostab' ), tab );
				$li.data( 'ui-ostab', tab );

				$a.find( 'span' ).remove();

				var icon = this._icon( tab ).appendTo( $a );

				var label = $( '<span></span>' ).addClass( 'nos-ostabs-label' )
					.text( tab.label )
					.appendTo( $a );

				if ( this.options.selected == index ) {
					$( 'title' ).text( tab.label );
				}

				if ( !tab.labelDisplay ) {
					label.hide();
				}

				if ( !isNaN( tab.iconSize ) && tab.iconSize !== 16) {
					$li.css({
						height: ( tab.iconSize + 4 ) + 'px',
						bottom: ( tab.iconSize - 35 ) + 'px'
					});
					icon.css( 'top', '2px' );
				}

				return $li;
			},

			_icon: function( tab ) {
				var icon = $( '<span></span>' ).addClass( 'nos-ostabs-icon' );
				if ( tab.iconUrl ) {
					icon.css( 'background-image', 'url("' + tab.iconUrl + '")' );
				} else {
					icon.addClass( tab.iconClasses );
				}
				if ( !isNaN(tab.iconSize) && tab.iconSize !== 16 ) {
					icon.css({
						width: tab.iconSize + 'px',
						height: tab.iconSize + 'px',
						lineHeight: tab.iconSize + 'px',
						top: ( tab.iconSize > 22 ? 22 - tab.iconSize : 10 - tab.iconSize / 2) + 'px'
					});
				}
				return icon;
			},

			unpin: function( index ) {
				var self = this,
					o = this.options;
				index = self._getIndex( index );
				if ( $.inArray( index, self.pined ) == -1 ) {
					return;
				}

				self.element.find( self._sanitizeSelector( self.anchors[ index ].hash ) )
					.find( '.nos-ostabs-actions .nos-ostabs-close span' )
					.text( o.texts.removeTab );

				self.lis.eq( index )
					.add( self.element.find( self._sanitizeSelector( self.anchors[ index ].hash ) ) )
					.removeClass( "ui-state-pined" );
				self.pined = $.grep( self.pined, function( n, i ) {
					return n != index;
				});

				self._trigger( "unpin", null, self._ui( self.lis[ index ] ) );
				return this;
			},

			pin: function( index ) {
				var self = this,
					o = this.options;

				index = this._getIndex( index );
				// cannot pin already pin tab
				if ( $.inArray( index, self.pined ) == -1 ) {
					var $panel = self.element.find( self._sanitizeSelector( self.anchors[ index ].hash ) );
					$panel.find( '.nos-ostabs-actions .nos-ostabs-close span' )
						.text( o.texts.closeTab );

					var $li = this.lis.eq( index );
					$li.add($panel)
						.addClass( "ui-state-pined" );
					if ( $li.hasClass( 'nos-ostabs-selected' ) ) {
						$li.removeClass( "ui-state-pined" );
					}

					self.pined.push( index );
					self.pined.sort();

					this._trigger( "pin", null, this._ui( this.lis[ index ] ) );
				}

				return this;
			},

			select: function( index ) {
				index = this._getIndex( index );
				if ( index == -1 ) {
					return this;
				}
				this.anchors.eq( index ).trigger( "click.tabs" );
				return this;
			},

			_load: function( index ) {
				index = this._getIndex( index );
				var self = this,
					o = this.options,
					a = this.anchors.eq( index )[ 0 ],
					url = $.data( a, "load.tabs" ),
					ajax = $.data( a, "ajax.tabs" );

				this._abort();

				// not remote or from cache
				if ( (!url && !ajax) || this.element.queue( "tabs" ).length !== 0 && $.data( a, "cache.tabs" ) ) {
					this.element.dequeue( "tabs" );
					return;
				}

				var panel = self.element.find( self._sanitizeSelector( a.hash )),
					content = $( '> *', panel ).not( '.nos-ostabs-actions' ).length;

				if (content !== 0) {
					this.element.dequeue( "tabs" );
					return;
				}

				// load remote from here on
				this.lis.eq( index ).addClass( "ui-state-processing" );

				$( "span.nos-ostabs-label", a ).each(function() {
					$( this ).data( "label.tabs", $( this ).html() )
						.html( $(this).data("label.tabs") ? o.texts.spinner : '' );
				});

				if ( $.isFunction($.fn.loadspinner) ) {
					$( "span.nos-ostabs-icon", a ).each(function() {
						$( this ).addClass( 'ui-state-processing' )
							.loadspinner({
								diameter : $( this ).width(),
								scaling : true
							});
					});
				}

				if (ajax) {
					this.xhr = $.ajax({
						url: url,
						success: function( r ) {
							$( '<div></div>' ).addClass( 'nos-ostabs-panel-content' )
								.prependTo( panel )
								.html( r );

							$.data( a, "cache.tabs", true );
						},
						complete: function() {
							// take care of tab labels
							self._cleanup();

							self._trigger( "load", null, self._ui( self.lis[index] ) );
						}
					});
				} else {
					$( '<iframe ' + ($.browser.msie ? 'allowTransparency="true" ' : '') + 'src="' + url + '" frameborder="0"></iframe>' )
						.data( 'nos-ostabs-index', index )
						.addClass( 'nos-ostabs-panel-content' )
						.bind( 'load', function() {
							self._cleanup();
							self._trigger( "load", null, self._ui( self.lis[index] ) );
						})
						.prependTo( self.element.find( self._sanitizeSelector( a.hash ) ) );

					$.data( a, "cache.tabs", true );
				}

				// last, so that load event is fired before show...
				self.element.dequeue( "tabs" );

				return this;
			},

			_abort: function() {
				// stop possibly running animations
				this.element.queue( [] );
				this.panels.stop( false, true );

				// "tabs" queue must not contain more than two elements,
				// which are the callbacks for the latest clicked tab...
				this.element.queue( "tabs", this.element.queue( "tabs" ).splice( -2, 2 ) );

				// take care of tab labels
				this._cleanup();
				return this;
			},

			tabs: function() {
				var tabs = [];
				this.uiOstabsTabs.find( 'li:not(.nos-ostabs-newtab)' )
					.each(function() {
						tabs.push( $(this).data('ui-ostab') );
					});
				return tabs;
			}
		});
	})();
	return $;
});
