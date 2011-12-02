(function($) {
    // Pour créer mon plugin il suffit d'appeller la méthode $.widget
    // Le 1er paramètre est le nom de mon plugin préfixé par ui. (le namespace de jQuery UI)
    // Le 2eme paramètres est un objet json de paramétrage du plugin
    $.widget("ui.bloc", {
        // options par défaut du widget
        // modifiables à la construction
        // mais aussi après la construction du widget
        options: {
            title: 'Titre'
        },

        // Une variable interne
        // contient l'objet jQuery du titre du bloc
         uiBlocTitle: null,

        // La fonction _create est appelée à la construction du widget
        // la variable d'instance this.element contient un objet jQuery
        // contenant l'élément sur lequel porte le widget
        _create: function() {
            this.element.addClass('uiBloc');
            this._title() ;
        },

        // Toutes les fonctions commençant par un underscore
        // sont des fonctions internes
         _title: function() {
            this.uiBlocTitle = $('<h5></h5>').text(this.options.title).prependTo(this.element);
         },

        // Les fonctions ne commençant pas par un underscore
        // sont des fonctions pouvant être appelées de l'extérieur
        title: function(text) {
            if (typeof(text)!= 'undefined') {
                // la variable text a été passée en paramètre
                // Modification du texte
                // et ne pas oublier de retourner l'élément (this.element)
                // pour rendre possible le chainage de fonction
                return this.uiBlocTitle.text(text);
            } else {
                // la variable text n'a pas été passée en paramètre
                // On retourne le texte actuellement contenu dans l'élément
                return this.uiBlocTitle.text();
            }
        }
    });
})(jQuery);