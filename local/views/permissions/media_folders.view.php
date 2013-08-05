<p>Note : lorsqu'un dossier est coché, tous ses sous-dossiers sont également autorisés.</p>
<?php

$selected = array();
$listPermissionCategories = $role->listPermissionCategories('noviusos_media::folders');

if (!empty($listPermissionCategories)) {
    foreach ($listPermissionCategories as $folder_id) {
        $selected['Nos\media\Model_Folder|'.$folder_id] = array(
            'id' => $folder_id,
            'model' => 'Nos\media\Model_Folder',
        );
    }
}

echo (string) \Request::forge('admin/noviusos_media/inspector/folder/list')->execute(
    array(
        'inspector/modeltree_checkbox',
        array(
            'params' => array(
                'urlJson' => 'admin/noviusos_media/inspector/folder/json',
                'reloadEvent' => 'Nos\\Media\\Model_Folder',
                'input_name' => 'perm['.$permission_app.'::folders][]',
                'selected' => $selected,
                'columns' => array(
                    array(
                        'dataKey' => 'title',
                    )
                ),
                'treeOptions' => array(
                    'context' => null
                ),
                'height' => '150px',
                'width' => null,
            ),
        ),
    )
);


echo (string) \View::forge('nos::admin/permissions/standalone', $view_params + array(
    'list' => array(
        $permission_app.'::edit_everyone' => array(
            'label' => 'Peut modifier les médias de tout le monde',
            //'checked' => false,
        ),
    ),
), false);
