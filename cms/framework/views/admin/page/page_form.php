<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

$fieldset->form()->set_config('field_template',  "\t\t<tr><th class=\"{error_class}\">{label}{required}</th><td class=\"{error_class}\">{field} {error_msg}</td></tr>\n");

foreach ($fieldset->field() as $field) {
	if ($field->type == 'checkbox') {
		$field->set_template('{field} {label}');
	}
}

$fieldset->field('page_cache_duration')->set_template('{label} {field} seconds');
$fieldset->field('page_lock')->set_template('{label} {field}');

$checkbox = '<br /><label><input type="checkbox" data-id="same_title">'.strtr(__('Use {field}'), array('{field}' => __('title'))).'</label>';
$fieldset->field('page_menu_title')->set_template("\t\t<span class=\"{error_class}\">{label}{required}</span>\n\t\t<br />\n\t\t<span class=\"{error_class}\">{field} $checkbox {error_msg}</span>\n");

?>

<?= $fieldset->open('admin/cms/page/page/'.($page->is_new() ? 'add' : 'edit/'.$page->page_id)) ?>
<?= View::forge('form/layout_standard', array(
    'fieldset' => $fieldset,
    // Used by the behaviours (publishable, etc.)
    'object' => $page,
    'medias' => array(),
    'title' => 'page_title',
    'id' => 'page_id',

    'large' => true,

    'save' => 'save',

    'subtitle' => array('page_type', 'page_template'),

    'content' => \View::forge('form/expander', array(
        'title'    => 'Content',
        // Wysiwyg are edge-to-edge with the border
        'nomargin' => true,
        'options' => array(
            'allowExpand' => false,
        ),
        'content'  => '
            <div data-id="external">
                <table>
                    '.$fieldset->field('page_external_link')->build().'
                    '.$fieldset->field('page_external_link_type')->build().'
                </table>
            </div>
            <div data-id="internal" style="display:none;">
                <p style="padding:1em;">We\'re sorry, internal links are not supported yet. We need a nice page selector before that.</p>
            </div>
            <div data-id="wysiwyg" style="display:none;"></div>',
    ), false),

    'menu' => array(
        __('Menu') => array('page_menu', 'page_menu_title'),
        __('URL (page address)') => array('page_virtual_name'),
        __('SEO') => array('page_meta_noindex', 'page_meta_title', 'page_meta_description', 'page_meta_keywords'),
        __('Admin') => array('page_cache_duration', 'page_lock'),
    ),
), false) ?>
<?= $fieldset->close() ?>