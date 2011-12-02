<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

    error_reporting(E_ALL & ~E_NOTICE);


    /************************** PARAMETRE DU SITE ************************************/
    if (Fuel::$env == Fuel::PRODUCTION) {
        define('DUREE_VIE_PAGE', 24 * 60 * 60);

        define('DOSSIER_MENU_IMAGES_1', 16); // ID Du dossier v�rouill� contenant les pages du menu images 1
        define('DOSSIER_MENU_IMAGES_2', 17); // ID Du dossier v�rouill� contenant les pages du menu images 2
    } else if (Fuel::$env == Fuel::STAGE) {
        define('DUREE_VIE_PAGE', 60 * 10);
    } else if (Fuel::$env == Fuel::DEVELOPMENT) {
        define('DUREE_VIE_PAGE', 60 * 10);
    } else {
        exit('environnement non reconnu.');
    }

    define('NOM_SITE', 'Novius Labs');
    define('PICTO_MENU', false);                      //false ou true, Utilisation de pictogramme dans les menus
    define('LOGO_TITRE', false);                      //false ou true, Utilisation d'une image pour les titres de pages
    define('NIVEAU_MENU', 2);                    //chiffre, Niveau maximum des menus
    define('POPUP', false);                               //false ou true, Site utilisant des gabarit pour popup
    define('TITRE_MENU', true);                      //false ou true, Possibilit� de saisir un titre de page sp�cifique � l'utilisation dans le menu
    define('TITRE_REFERENCE', true);                            //false ou true, Possibilit� de saisir un titre pour le r�f�rencement de la page
    define('META_KEYWORDS', true);                              //false ou true, Utilisation du champ keywords pour les META
    define('MULTILANGUE', false);                   //false ou true, Site multilangue
    define('LANGUE_DEFAUT', 'fr');              //code en 2 caract�res, Code de la langue par d�faut du site
    define('REGENARATION_PAGE', false);        //false ou true, Possibilit� ou non pour le webmaster de g�rer le cache
    define('PAGE_ADDHEADER', true);                   //false ou true, Utilisation du champ d'ajout de header dans les pages
    define('CONTENU_TEXT', false);                  //false ou true, Gestion du contenu des pages en mode Text pour le moteur de recherche
    define('GESTION_USER_FRONT', false);       //false ou true, Possibilit� de g�rer des utilisateurs uniquement Front Office
    define('GOOGLE_ANALYTICS_AUTO_TRACKING', true);             //ajouter la possibilit� d'utiliser le tracking auto
    define('HTML_BRUT', true);
    define('EMAIL_NOVIUS', 'felix@novius.fr');

    $wysiwyg_settings = array(
        'content_css'       => 'body {font-size:10px;font-family:Verdana, Arial, Helvetica, sans-serif;color:#000000;text-decoration:none;background-color:#FFFFFF;background-image:none;}',
        'content_css_url'   => 'static/css/blog_content.css', //Url relative de la feuille de style du contenu
        'width'             => '100%', //Largeur du conetnu du wysiwyg
        'colors'            => array('000000', 'FFFFFF'), // Tableau des codes couleurs du sites
        'enable'            => array( // D�commenter une ligne pour activer la fonction du wysiwyg correspondant
            //'fontsizeselect', //selection de la taille du texte
            //'formatselect', //Selection du block format, devenu inutile avec le style_formats
            //'cite', // tag citation
            //'abbr', // tag abbr�viation
            //'attribs', // Configuration des atributs d'un tag
            //'acronym', // tag accronyme
            //'insertlayercontrols', // insertion d'�lement en position absolute
            //'styleprops', // Configuration du style d'un tag
        ),
        'style_formats'     => array(
            array('title' => 'Normal', 'block' => 'p'),
            array('title' => 'Titre 1', 'block' => 'h1'),
            array('title' => 'Titre 2', 'block' => 'h2'),
            array('title' => 'Titre 3', 'block' => 'h3'),
        ),
    );

    //Ajout de Menu et Sous-Menu supplémentaire
    $tableau_menu_admin = array(
        array(
            'droit'        => 'blog',
            'label'        => 'Blog',
            'sous_menu'    => array(
                array('droit' => 'blog', 'label' => 'Billets', 'icone' => URL_ADMIN.'images/icone_menu/blog.gif', 'href' => URL_ADMIN.'blog/billet/index.htm'),
                array('droit' => 'blog', 'label' => 'Commentaires', 'icone' => URL_ADMIN.'images/icone_menu/commentaire.gif', 'href' => URL_ADMIN.'blog/commentaire/index.htm'),
                array('droit' => 'blog', 'label' => 'Catégories', 'icone' => URL_ADMIN.'images/icone_menu/arbo_catalogue.gif', 'href' => URL_ADMIN.'blog/categorie/index.htm'),
                array('droit' => 'blog', 'label' => 'Tags', 'icone' => URL_ADMIN.'images/icone_menu/tag.gif', 'href' => URL_ADMIN.'blog/tag/index.htm'),
            ),
        ),
        'modules' => array(
            'sousmenus' => array(
                'formulaire'  => 'default',
                'commentaire' => 'default',
            ),
        ),
    );

    // D�finition des fonctions et modules sp�cifiques
    $tableau_fonction_specifique = array(
        array(
            'nom'       => 'formulaire',
            'titre'     => 'Formulaire',
            'droit'     => 'formulaire',
            'inc'       => 'formulaire.inc',
            'icone'     => URL_ADMIN.'images/icone_menu/formulaire.gif',
            'headers'   => true,
        ),
        array(
            'nom'       => 'commentaire',
            'titre'     => 'Commentaires',
            'droit'     => 'commentaire',
            'inc'       => 'commentaire.inc',
            'icone'     => URL_ADMIN.'images/icone_menu/commentaire.gif',
            'headers'   => true,
        ),
        array(
            'nom'       => 'blog',
            'droit'     => 'blog',
            'inc'       => 'blog.inc',
            'headers'   => true,
            'icone'     => URL_ADMIN.'images/icone_fonction/blog.gif',
        ),
        array(
            'nom'     => 'codesource',
            'titre'   => 'Code source',
            'droit'   => 'webmaster',
            'inc'     => 'codesource.inc',
            'headers' => true
        ),
        array(
            'nom'     => 'codehighlight',
            'titre'   => 'Code à afficher',
            'droit'   => 'webmaster',
            'inc'     => 'codehighlight.inc',
            'headers' => true
        ),
    );

    define('CLASSE_PUBLI_BLOG', 'ClientBlog');
    define('CLASSE_PUBLI_BLOG_CATEGORIE', 'ClientBlogCategorie');
    define('CLASSE_PUBLI_BLOG_COMMENTAIRE', 'ClientBlogCommentaire');

    $tableau_multilangue     = array('fr');
    $tableau_cms_multilangue = array('fr', 'uk');

    define('PAGE_RACINE', 'fr');
    define('PAGE_CONTRIBUTIONS', 27); // ID Du dossier v�rouill� contenant le menu de la sidebar
    define('PAGE_MENU_SIDEBAR', 4); // ID Du dossier v�rouill� contenant le menu de la sidebar
    define('PAGE_MENU_FOOTER', 3); // ID Du dossier v�rouill� contenant le menu footer
    define('DOSSIER_MENU_HEADER', 5); // ID Du dossier v�rouill� contenant les pages du menu header
    define('DOSSIER_MENU_IMAGES_1', 14); // ID Du dossier v�rouill� contenant les pages du menu images 1
    define('DOSSIER_MENU_IMAGES_2', 15); // ID Du dossier v�rouill� contenant les pages du menu images 2
    define('PAGE_INSCRIPTION_NEWSLETTER', 9);
    define('URL_TWITTER', 'http://twitter.com/noviuslabs');


    define('CLASSE_SITEMAP', 'ClientSiteMap'); //Constante contenant le nom de la classe du sitemap, false si non g�r�e
    define('CLASSE_PUBLI_PAGE', 'ClientPage');

    // Constantes et variables � activer au cas par cas et � supprimer autrement
    define('MENU_TRADUCTION', true); // Constante indiquant que le menu traduction apparait en back si pas multilangue

