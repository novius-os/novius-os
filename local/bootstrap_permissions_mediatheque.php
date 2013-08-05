<?php

// Migration SQL
// ALTER TABLE `nos_media` ADD `media__user_id` INT NULL DEFAULT NULL;

// Préfixe de l'application dans laquelle ajouter les permissions de la médiathèque
define('PORTAIL_MALIN_PERMISSION_MEDIATHEQUE', 'noviusos_media');

// Configuration de la liste des permissions
Event::register_function('config|'.PORTAIL_MALIN_PERMISSION_MEDIATHEQUE.'::permissions', function(&$config) {
    $config = array(
        'all' => array(
            'view' => 'nos::form/accordion',
            'params' => array(
                'accordions' => array(
                    'folders' => array(
                        'title' => __('Folders permissions'),
                        'view' => 'local::permissions/media_folders',
                        'params' => array(
                            'permission_app' => PORTAIL_MALIN_PERMISSION_MEDIATHEQUE,
                        ),
                    ),
                ),
            ),
        ),
    );
});

// Ajout du champ dans le modèle
Event::register_function('config|noviusos_media::model/media', function(&$config) {
    $config['properties']['media__user_id'] = array(
        'default' => null,
        'data_type' => 'int',
        'null' => true,
    );
});

// Functions pour vérifier si un fichier média est 'désactivé' (= non autorisé)
function noviusos_media_check_disabled_media($item)
{
    $folder = $item->folder;
    do {
        if (\Nos\User\Permission::check(PORTAIL_MALIN_PERMISSION_MEDIATHEQUE.'::folders', $folder->medif_id)) {
            // Autorisation exceptionnelle d'éditer tous les fichiers (même ceux uploadés par les autres)
            if (\Nos\User\Permission::check(PORTAIL_MALIN_PERMISSION_MEDIATHEQUE.'::edit_everyone')) {
                return false;
            }
            // Autorisé car l'utilisateur est celui qui l'a uploadé
            if (\Session::user()->id == $item->media__user_id) {
                return false;
            }
            return 'Vous pouvez seulement modifier les médias qui vous appartiennent.';
        }
        $folder = $folder->get_parent();
    } while (!empty($folder));

    // Pas d'accès au dossier, ni à un dossier parent
    return 'Vous n\'avez pas accès à ce dossier.';
}

// Functions pour vérifier si un dossier est 'désactivé' (= non autorisé)
function noviusos_media_check_disabled_folder($folder)
{
    do {
        if (\Nos\User\Permission::check(PORTAIL_MALIN_PERMISSION_MEDIATHEQUE.'::folders', $folder->medif_id)) {
            return false;
        }
        $folder = $folder->get_parent();
    } while (!empty($folder));

    // Pas d'accès au dossier, ni à un dossier parent
    return 'Vous n\'avez pas accès à ce dossier.';
}

// Désactivation des actions sur les fichiers médias
Event::register_function('config|noviusos_media::common/media', function(&$config) {
    foreach (array('Nos\Media\Model_Media.edit', 'Nos\Media\Model_Media.delete') as $key) {
        $config['actions'][$key]['disabled'][] = 'noviusos_media_check_disabled_media';
    }
    $config['actions']['Nos\Media\Model_Media.add']['visible'][] = function() {
        return \Nos\User\Permission::check(PORTAIL_MALIN_PERMISSION_MEDIATHEQUE.'::folders', 1);
    };
});

// Désactivation des actions sur les dossiers
Event::register_function('config|noviusos_media::common/folder', function(&$config) {
    foreach ($config['actions'] as &$action) {
        $action['disabled'][] = 'noviusos_media_check_disabled_folder';
    }
    $config['actions']['Nos\Media\Model_Folder.add']['visible'][] = function() {
        return \Nos\User\Permission::check(PORTAIL_MALIN_PERMISSION_MEDIATHEQUE.'::folders', 1);
    };
});

// Modification du CRUD des fichiers médias
Event::register_function('config|noviusos_media::controller/admin/media', function(&$config) {

    //Ajout du champ avec l'ID de l'user ayant uploadé le média
    $config['fields']['media__user_id'] = array(
        'form' => array(
            'type' => 'hidden',
            'value' => \Session::user()->user_id,
        ),
    );

    // Pas accès à la racine = pas le choix du dossier (champ caché)
    if (!Nos\User\Permission::check(PORTAIL_MALIN_PERMISSION_MEDIATHEQUE.'::folders', 1)) {
        unset($config['fields']['media_folder_id']['renderer']);
        $config['fields']['media_folder_id']['label'] = '';
    }
});

// Modification du CRUD des dossiers
Event::register_function('config|noviusos_media::controller/admin/folder', function(&$config) {

    // Pas accès à la racine = pas le choix du dossier (champ caché)
    if (!Nos\User\Permission::check(PORTAIL_MALIN_PERMISSION_MEDIATHEQUE.'::folders', 1)) {
        unset($config['fields']['medif_parent_id']['renderer']);
        $config['fields']['medif_parent_id']['label'] = '';
    }
});

