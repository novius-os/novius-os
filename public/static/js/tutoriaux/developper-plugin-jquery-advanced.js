(function($) {
    $.widget("ui.bloc", {
        // options par défaut du widget
        // modifiables à la construction
        // mais aussi après la construction du widget
        options: {
            title: 'Titre',
            togglable: true, // Variable indiquant si le bloc est ouvrable/fermable
            opened : true // Variable indiquant si le bloc est ouvert
        },

        // Variables internes
        uiBlocContainer: null, // contient l'objet jQuery du container du bloc
        uiBlocTitle: null, // contient l'objet jQuery du titre du bloc
        uiBlocTitleToggle: null, // contient l'objet jQuery du picto dans la barre de titre indiquant l'état d'ouverture/fermeture du bloc

        // La fonction _create est appelée à la construction du widget
        // la variable d'instance this.element contient un objet jQuery
        // contenant l'élément sur lequel porte le widget
        _create: function() {
            // On crée un container pour notre nouvel élément d'UI
            // On lui ajoute la classe ui-widget qui doit être ajoutée à tout container de widget
            // On lui ajoute également la classe ui-widget-content qui doit être appliquée  à tout container de contenu de widget
            // La classe ui-corner-all arrondit les 4 angles de notre bloc
            this.uiBlocContainer = $('<div></div>')
                .addClass('ui-bloc ui-widget ui-widget-content ui-corner-all')
                .insertAfter(this.element);

            // On encapsule notre élément initial dans notre nouveau container
            this.element.addClass('ui-bloc-content').appendTo(this.uiBlocContainer);

            // On ajoute la classe ui-widget-header qui doit être ajoutée à tout élément titre de widget
            // La classe ui-corner-top arrondit les 2 angles suppérieurs de notre bloc titre
            // L'élément uiBlocTitle est ajouté au container et non plus à notre élément de base
            this.uiBlocTitle = $('<h5></h5>').addClass('ui-bloc-title ui-widget-header ui-corner-top')
                .prependTo(this.uiBlocContainer);

            // On encapsule un SPAN dans le bloc titre pour y écrire le titre et pouvoir le modifier à posteriori
            $('<span></span>').appendTo(this.uiBlocTitle);

            // On ajoute un SPAN au bloc titre pour le picto indicateur de l'état d'ouverture / fermeture
            // Mais on le cache au cas où la fonctionnalité soit désactivée
            this.uiBlocTitleToggle = $('<span></span>')
                .addClass('ui-bloc-title-toggle ui-icon ui-icon-pin-s')
                .appendTo(this.uiBlocTitle)
                .hide();
        },

        // La fonction _init est appelée à la construction ET à la réinitialisation du widget
        _init : function() {
            var self = this;

            // On renseigne le texte du titre avec la valeur présente dans les options
            self.uiBlocTitle.children('span:first').text(self.options.title);

            // On enlève l'événement click sur le bloc titre
            // En cas de réinitialisation, cela évite d'ajouter un nouvel événement click
            // à notre titre qui en a déjà potentiellement un
            self.uiBlocTitle.unbind('click');

            if (self.options.togglable) {
                // On ajoute l'événement click au bloc titre si le bloc est ouvrable / fermable
                self.uiBlocTitle.click(function(event) {
                    // Si le widget est disabled, il ne se passe rien au click
                    if (!self.options.disabled) {
                        self.toggle(event);
                        return false;
                    }
                }).css('cursor', 'pointer'); // On modifie le curseur au survol du titre

                // On affiche le picto indicateur de l'état d'ouverture / fermeture
                // précédemment crée dans _create
                self.uiBlocTitleToggle.show();

                if (!self.options.opened) {
                    // Si le bloc est initialisé avec le paramètre opened à false, on ferme le bloc
                    self._close();
                } else {
                    // Si le bloc est initialisé avec le paramètre opened à true, on ouvre le bloc
                    self._open();
                }
            } else {
                // Le bloc n'est pas togglable
                // On réinitialise le curseur au survol du titre
                self.uiBlocTitle.css('cursor', 'auto');
                // On cache le picto indicateur de l'état d'ouverture / fermeture
                self.uiBlocTitleToggle.hide();
                // On ouvre le bloc
                self._open();
            }
        },

        // La fonction destroy ramène l'élément du DOM, sur lequel est basé notre widget,
        // dans l'état où il était avant la création du widget.
        // Elle défait ce que _create a fait
        destroy: function() {
            // On réaffiche l'élément éventuellement caché
            // On enlève les classes css propres au widget
            // Et on sort l'élément du container
            this.element.show()
                .removeClass('ui-bloc-content')
                .insertBefore(this.uiBlocContainer);

            // On détruit le container
            // Ce qui détruit par ricochet tous les autres éléments créés par notre widget
            this.uiBlocContainer.remove();

            // On appelle la méthode originale du framework
            // Elle supprime l'instance du widget qui a été stocké en data dans l'élément
            $.Widget.prototype.destroy.apply(this);

            return this;
        },

        // Surcharge de la méthode _setOption qui est appelée par la méthode option
        // qui permet de modifier des options de notre widget
        _setOption: function(key, value){
            var self = this;

            // On appelle la méthode originale du framework qui modifie le tableau d'options
            $.Widget.prototype._setOption.apply(self, arguments);

            if ($.inArray(key, ['title', 'togglable', 'opened']) != -1) {
                // Si l'option modifiée est une des 3 options title, togglable, opened
                // On appelle la méthode d'initialisation
                self._init();
            } else if (key === 'disabled') {
                // L'option disabled a été modifiée
                // On ajoute ou supprime, en fonction du cas, la classe ui-state-disabled au container
                // Dans le framework css de jQuery UI, la classe ui-state-disabled grise un élément
                if (value) {
                    this.uiBlocContainer.addClass('ui-state-disabled');
                } else {
                    this.uiBlocContainer.removeClass('ui-state-disabled');
                }
            }
        },

        toggle : function() {
            var self = this;

            //Si le bloc n'est pas ouvrable/fermable on sort tout de suite
            if (!self.options.togglable) {
                return self;
            }

            if (self.options.opened) {
                // Si l'événement beforeClose retourne false, on arrête la fermeture
                if (false === this._trigger('beforeClose')) {
                    return false;
                }

                self._close();

                // Envoi du signal de fermeture
                // _trigger accepte 3 paramètres, les deux derniers étant optionnels :
                // - le nom de l'événement
                // - l'objet événement
                // - des données additionnelles envoyées aux fonctions interceptant l'événement
                this._trigger('close');
            } else {
                // Si l'événement beforeOpen retourne false, on arrête l'ouverture
                if (false === this._trigger('beforeOpen')) {
                    return false;
                }

                self._open();

                // Envoi du signal d'ouverture
                this._trigger('open');
            }
            // On inverse la valeur de l'option opened
            self.options.opened = !self.options.opened;

            // On retourne l'instance du plugin pour préserver le chaînage des fonctions
            return self;
        },

        _close: function() {
            // On doit cacher tous les enfants du container sauf le titre
            this.uiBlocContainer.children().not(this.uiBlocTitle).hide();
            // L'icône de la barre de titre devient une tête d'épingle orientée vers l'ouest (west, donc vers la gauche)
            this.uiBlocTitleToggle.removeClass('ui-icon-pin-s').addClass('ui-icon-pin-w');
        },

        _open: function() {
            // On doit afficher tous les enfants du container
            this.uiBlocContainer.children().show();
            // L'icône de la barre de titre devient une tête d'épingle orientée vers le sud (vers le bas)
            this.uiBlocTitleToggle.removeClass('ui-icon-pin-w').addClass('ui-icon-pin-s');
        }
    });
})(jQuery);
