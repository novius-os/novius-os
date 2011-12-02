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
            this._title();

            if (this.options.togglable && !this.options.opened) {
                // Si le bloc est initialisé avec le paramètre opened à false, on ferme le bloc
                this._close();
            }
        },

        // Toutes les fonctions commençant par un underscore
        // sont des fonctions interne
        _title: function() {
            var self = this;

            // On ajoute la classe ui-widget-header qui doit être ajoutée à tout élément titre de widget
            // La classe ui-corner-top arrondit les 2 angles suppérieurs de notre bloc titre
            // L'élément uiBlocTitle est ajouté au container et non plus à notre élément de base
            self.uiBlocTitle = $('<h5></h5>').addClass('ui-bloc-title ui-widget-header ui-corner-top')
                .css('cursor', 'pointer') // On modifie le curseur au survol du titre
                .prependTo(this.uiBlocContainer);

            // On encapsule le texte du titre dans un span pour pouvoir le modifier
            $('<span></span>').text(self.options.title)
                .appendTo(self.uiBlocTitle);

            if (self.options.togglable) {
                // On ajoute l'événement clic à notre titre si le bloc est ouvrable/fermable
                self.uiBlocTitle.click(function(event) {
                    self.toggle(event);
                    return false;
                });

                // On ajoute un span à notre titre
                // la classe ui-bloc-title-toggle va nous servir à placer le span à droite dans la barre de titre
                // la classe ui-icon associée à la classe ui-icon-pin-s
                // va transformer notre span en une icône de tête d'épingle orientée sud (vers le bas)
                self.uiBlocTitleToggle = $('<span></span>')
                    .addClass('ui-bloc-title-toggle ui-icon ui-icon-pin-s')
                    .appendTo(self.uiBlocTitle);
            }
        },

        // Les fonctions ne commençant pas par un underscore
        // sont des fonctions pouvant être appelée de l'extérieur
        title: function(text) {
            if (typeof(text)!= 'undefined') {
                // la variable text a été passée en paramètre
                // Modification du texte
                // et ne pas oublié de retourné l'élément (this.element)
                // pour rendre possible le chainage de fonction
                return this.uiBlocTitle.children('span:first').text(text);
            } else {
                // la variable text n'a pas été passée en paramètre
                // On retourne le texte actuellement contenu dans l'élément
                return this.uiBlocTitle.children('span:first').text();
            }
        },

        toggle : function() {
            var self = this;

            //Si le bloc n'est pas ouvrable/fermable on sort tout de suite
            if (!self.options.togglable) {
                return self;
            }

            if (self.options.opened) {
                self._close();
            } else {
                self._open();
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