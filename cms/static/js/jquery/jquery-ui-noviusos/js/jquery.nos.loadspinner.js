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
], function( $ ) {
	$.widget ('nos.loadspinner', {
		options : {
			diameter : 40, // The diameter of the loader.
			color : null, // The color of the loader shapes in HEX.
			density : 40, // The number of shapes drawn on the loader canvas.
			range : 1.3, // Sets the amount of the modified shapes in percent.
			scaling : false, // The scaling of the loader shapes.
			fading : true, // The fading of the loader shapes.
			shape : 'circle', // The type of the loader shapes.
			speed : 2, // The speed of the loader animation.
			fps : 24 // The FPS of the loader animation rendering.
		},

		shapes : ["circle", "square", "rectangle", "roundedRectangle"],
		colorReg : /^\#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/,
		colorRgb : /^rgb\((\d+)[ \,]+(\d+)[ \,]+(\d+)\)$/,
		UISpinnerCanvas : null, // The div we draw the shapes into.
		UISpinnerContext : null, // The canvas context.
		UISpinnerCacheCanvas : null, // The canvas we use for caching.
		UISpinnerCacheContext : null, // The context of the cache canvas.
		running : false, // Tell if the loader rendering is running.
		ready : false, // Tell if the canvas and its context is ready.
		timer : null, // Add a timer for the rendering.
		activeId : 0, // The active shape id for rendering.
		colorRGB : null, // The color of the loader shapes in RGB.

		_create : function() {
			// Create the canvas element
			this.UISpinnerCanvas = $('<canvas></canvas>').attr('id', "CanvasLoader").get(0);
			this.UISpinnerContext = this.UISpinnerCanvas.getContext("2d");
			this.element.append(this.UISpinnerCanvas);
			this.UISpinnerCanvas.width = this.UISpinnerCanvas.height = this.options.diameter;

			// Create the cache canvas element
			this.UISpinnerCacheCanvas = $('<canvas></canvas>').get(0);
			$('body').append(this.UISpinnerCacheCanvas);
			this.UISpinnerCacheContext = this.UISpinnerCacheCanvas.getContext("2d");
			this.UISpinnerCacheCanvas.width = this.UISpinnerCacheCanvas.height = this.options.diameter;
			$(this.UISpinnerCacheCanvas).hide();
		},

		_init : function () {
			if (!this.options.color) {
				this.options.color = this.element.css('color');
			}

			this._setOption('color', this.options.color);

			// Set the instance ready
			this.ready = true;

			// Draw the shapes on the canvas
			this._draw();

			// Start rendering the preloader
			this.start();
		},

		_setOption: function(key, value){
			var self = this;

			switch (key) {
				case 'diameter' :
					if (isNaN(value)) {
						return;
					}
					value = Math.round(Math.abs(value));
					break;

				case 'color' :
					this.colorRGB = this._RGB(value);
					break;

				case 'shape' :
					if ($.inArray(value, this.shapes) == -1) {
						return;
					}
					break;

				case 'density' :
					if (isNaN(value)) {
						return;
					}
					value = Math.round(Math.abs(value));
					break;

				case 'range' :
					if (isNaN(value)) {
						return;
					}
					value = Math.abs(value);
					break;

				case 'speed' :
					if (isNaN(value) && Math.abs(value) > 0) {
						return;
					}
					value = Math.round(Math.abs(value));
					break;

				case 'fps' :
					if (isNaN(value)) {
						return;
					}
					value = Math.round(Math.abs(value));
					break;
			}

			// On appelle la méthode originale du framework qui modifie le tableau d'options
			$.Widget.prototype._setOption.apply(self, arguments);

			if ($.inArray(key, ['diameter', 'color', 'shape', 'density', 'range', 'scaling', 'fading']) != -1) {
				this._redraw();
			} else if ($.inArray(key, ['speed', 'fps']) != -1) {
				this._reset();
			}
		},

		/**
		* Return the RGB values of the passed color.
		*/
		_RGB : function (color) {
			var hexObject = {};

			if (this.colorReg.test(color)) {
				color = color.charAt(0) === "#" ? color.substring(1, 7) : color;
				if (color.length == 3) {
					color = color + color;
				}
				hexObject.r = parseInt(color.substring(0, 2), 16);
				hexObject.g = parseInt(color.substring(2, 4), 16);
				hexObject.b = parseInt(color.substring(4, 6), 16);
			} else if (this.colorRgb.exec(color)) {
				hexObject.r = parseInt(RegExp.$1);
				hexObject.g = parseInt(RegExp.$2);
				hexObject.b = parseInt(RegExp.$3);
			} else {
				hexObject.r = 0;
				hexObject.g = 0;
				hexObject.b = 0;
			}

			return hexObject;
		},

		/**
		* Draw the shapes on the canvas
		*/
		_draw : function () {
			var i = 0,
				size = this.options.diameter * 0.07,
				radians, radius, w, h, x, y, angle,
				minBitMod = 0.1,
				animBits = Math.round(this.options.density * this.options.range),
				bitMod;

			// Clean the cache canvas
			this.UISpinnerCacheContext.clearRect(0, 0, this.UISpinnerCacheCanvas.width, this.UISpinnerCacheCanvas.height);
			this.UISpinnerCanvas.width = this.UISpinnerCanvas.height = this.UISpinnerCacheCanvas.width = this.UISpinnerCacheCanvas.height = this.options.diameter;

			// Draw the shapes
			switch (this.options.shape) {
				case this.shapes[0]:
					while (i < this.options.density) {
						if (i <= animBits) { bitMod = 1 - ((1 - minBitMod) / animBits * i); } else { bitMod = minBitMod; }
						radians = (this.options.density - i) * ((Math.PI * 2) / this.options.density);
						x = this.UISpinnerCanvas.width * 0.5 + Math.cos(radians) * (this.options.diameter * 0.45 - size) - this.UISpinnerCanvas.width * 0.5;
						y = this.UISpinnerCanvas.height * 0.5 + Math.sin(radians) * (this.options.diameter * 0.45 - size) - this.UISpinnerCanvas.height * 0.5;
						this.UISpinnerCacheContext.beginPath();
						if (this.options.fading) {
							this.UISpinnerCacheContext.fillStyle = "rgba(" + this.colorRGB.r + "," + this.colorRGB.g + "," + this.colorRGB.b + "," + bitMod.toString() + ")";
						} else {
							this.UISpinnerCacheContext.fillStyle = "rgba(" + this.colorRGB.r + "," + this.colorRGB.g + "," + this.colorRGB.b + ",1)";
						}
						if (this.options.scaling) {
							this.UISpinnerCacheContext.arc(this.options.diameter * 0.5 + x, this.options.diameter * 0.5 + y, size * bitMod, 0, Math.PI * 2, false);
						} else {
							this.UISpinnerCacheContext.arc(this.options.diameter * 0.5 + x, this.options.diameter * 0.5 + y, size, 0, Math.PI * 2, false);
						}
						this.UISpinnerCacheContext.closePath();
						this.UISpinnerCacheContext.fill();
						i += 1;
					}
					break;

				case this.shapes[1]:
					size = this.UISpinnerCanvas.width * 0.12;
					while (i < this.options.density) {
						if (i <= animBits) { bitMod = 1 - ((1 - minBitMod) / animBits * i); } else { bitMod = minBitMod; }
						angle = 360 - 360 / this.options.density * i;
						radians = angle / 180 * Math.PI;
						x = Math.cos(radians) * size * 3 + this.UISpinnerCacheCanvas.width * 0.5;
						y = Math.sin(radians) * size * 3 + this.UISpinnerCacheCanvas.height * 0.5;
						this.UISpinnerCacheContext.save();
						this.UISpinnerCacheContext.translate(x, y);
						this.UISpinnerCacheContext.rotate(radians);
						this.UISpinnerCacheContext.translate(-x, -y);
						this.UISpinnerCacheContext.beginPath();
						if (this.options.fading) { this.UISpinnerCacheContext.fillStyle = "rgba(" + this.colorRGB.r + "," + this.colorRGB.g + "," + this.colorRGB.b + "," + bitMod.toString() + ")"; } else { this.UISpinnerCacheContext.fillStyle = "rgba(" + this.colorRGB.r + "," + this.colorRGB.g + "," + this.colorRGB.b + ",1)"; }
						this.UISpinnerCacheContext.fillRect(x, y - size * 0.5, size, size);
						this.UISpinnerCacheContext.closePath();
						this.UISpinnerCacheContext.fill();
						this.UISpinnerCacheContext.restore();
						i += 1;
					}
					break;

				case this.shapes[2]:
					w = this.UISpinnerCacheCanvas.width * 0.24;
					h = w * 0.35;
					while (i < this.options.density) {
						if (i <= animBits) { bitMod = 1 - ((1 - minBitMod) / animBits * i); } else { bitMod = minBitMod; }
						angle = 360 - 360 / this.options.density * i;
						radians = angle / 180 * Math.PI;
						x = Math.cos(radians) * (h + (this.UISpinnerCacheCanvas.height - h) * 0.13) + this.UISpinnerCacheCanvas.width * 0.5;
						y = Math.sin(radians) * (h + (this.UISpinnerCacheCanvas.height - h) * 0.13) + this.UISpinnerCacheCanvas.height * 0.5;
						this.UISpinnerCacheContext.save();
						this.UISpinnerCacheContext.translate(x, y);
						this.UISpinnerCacheContext.rotate(radians);
						this.UISpinnerCacheContext.translate(-x, -y);
						this.UISpinnerCacheContext.beginPath();
						if (this.options.fading) { this.UISpinnerCacheContext.fillStyle = "rgba(" + this.colorRGB.r + "," + this.colorRGB.g + "," + this.colorRGB.b + "," + bitMod.toString() + ")"; } else { this.UISpinnerCacheContext.fillStyle = "rgba(" + this.colorRGB.r + "," + this.colorRGB.g + "," + this.colorRGB.b + ",1)"; }
						this.UISpinnerCacheContext.fillRect(x, y - h * 0.5, w, h);
						this.UISpinnerCacheContext.closePath();
						this.UISpinnerCacheContext.fill();
						this.UISpinnerCacheContext.restore();
						i += 1;
					}
					break;

				case this.shapes[3]:
					w = this.UISpinnerCacheCanvas.width * 0.24;
					h = w * 0.35;
					radius = h * 0.65;
					while (i < this.options.density) {
						if (i <= animBits) {
							bitMod = 1 - ((1 - minBitMod) / animBits * i);
						} else {
							bitMod = minBitMod;
						}
						angle = 360 - 360 / this.options.density * i;
						radians = angle / 180 * Math.PI;
						x = Math.cos(radians) * (h + (this.UISpinnerCacheCanvas.height - h) * 0.13) + this.UISpinnerCacheCanvas.width * 0.5;
						y = Math.sin(radians) * (h + (this.UISpinnerCacheCanvas.height - h) * 0.13) + this.UISpinnerCacheCanvas.height * 0.5;
						this.UISpinnerCacheContext.save();
						this.UISpinnerCacheContext.translate(x, y);
						this.UISpinnerCacheContext.rotate(radians);
						this.UISpinnerCacheContext.translate(-x, -y);
						if (this.options.fading) {
							this.UISpinnerCacheContext.fillStyle = "rgba(" + this.colorRGB.r + "," + this.colorRGB.g + "," + this.colorRGB.b + "," + bitMod.toString() + ")";
						} else {
							this.UISpinnerCacheContext.fillStyle = "rgba(" + this.colorRGB.r + "," + this.colorRGB.g + "," + this.colorRGB.b + ",1)";
						}
						this.UISpinnerCacheContext.beginPath();
						this.UISpinnerCacheContext.moveTo(x + radius, y - h * 0.5);
						this.UISpinnerCacheContext.lineTo(x + w - radius, y - h * 0.5);
						this.UISpinnerCacheContext.quadraticCurveTo(x + w, y - h * 0.5, x + w, y - h * 0.5 + radius);
						this.UISpinnerCacheContext.lineTo(x + w, y - h * 0.5 + h - radius);
						this.UISpinnerCacheContext.quadraticCurveTo(x + w, y - h * 0.5 + h, x + w - radius, y - h * 0.5 + h);
						this.UISpinnerCacheContext.lineTo(x + radius, y - h * 0.5 + h);
						this.UISpinnerCacheContext.quadraticCurveTo(x, y - h * 0.5 + h, x, y - h * 0.5 + h - radius);
						this.UISpinnerCacheContext.lineTo(x, y - h * 0.5 + radius);
						this.UISpinnerCacheContext.quadraticCurveTo(x, y - h * 0.5, x + radius, y - h * 0.5);
						this.UISpinnerCacheContext.closePath();
						this.UISpinnerCacheContext.fill();
						this.UISpinnerCacheContext.restore();
						i += 1;
					}
					break;
			}

			// Render the changes on the canvas
			this._tick(true);

			return this;
		},

		/**
		* Cleans the canvas.
		*/
		_clean : function () {
			this.UISpinnerContext.clearRect(0, 0, this.UISpinnerCanvas.width, this.UISpinnerCanvas.height);

			return this;
		},

		/**
		* Redraws the canvas.
		*/
		_redraw : function () {
			if (this.ready) {
				this._clean();
				this._draw();
			}

			return this;
		},

		/**
		* Resets the timer.
		*/
		_reset : function () {
			if (this.running) {
				this.stop();
				this.start();
			}

			return this;
		},

		/**
		* Renders the loader animation.
		*/
		_tick : function (initialize) {
			var rotUnit = this.options.density > 360 ? this.options.density / 360 : 360 / this.options.density;
			rotUnit *= this.options.speed;
			if (!initialize) { this.activeId += rotUnit; }
			if (this.activeId > 360) { this.activeId -= 360; }

			this.UISpinnerContext.clearRect(0, 0, this.options.diameter, this.options.diameter);
			this.UISpinnerContext.save();
			this.UISpinnerContext.translate(this.options.diameter * 0.5, this.options.diameter * 0.5);
			this.UISpinnerContext.rotate(Math.PI / 180 * this.activeId);
			this.UISpinnerContext.translate(-this.options.diameter * 0.5, -this.options.diameter * 0.5);
			this.UISpinnerContext.drawImage(this.UISpinnerCacheCanvas, 0, 0, this.options.diameter, this.options.diameter);
			this.UISpinnerContext.restore();

			return this;
		},

		/**
		* Start the rendering of the loader animation.
		*/
		start : function () {
			if (!this.running) {
				this.running = true;
				var t = this;
				this.timer = self.setInterval(function () {
					t._tick();
				}, Math.round(1000 / this.options.fps));
			}

			return this;
		},

		/**
		* Stop the rendering of the loader animation.
		*/
		stop : function () {
			if (this.running) {
				this.running = false;
				clearInterval(this.timer);
				this.timer = null;
				delete this.timer;
			}

			return this;
		},

		/**
		* Remove the CanvasLoader instance.
		*/
		destroy : function () {
			if (this.running) { this.stop(); }
			$(this.UISpinnerCanvas).remove();
			$(this.UISpinnerCacheCanvas).remove();

			$.Widget.prototype.destroy.apply(this);

			return this;
		}
	});
	return $;
});