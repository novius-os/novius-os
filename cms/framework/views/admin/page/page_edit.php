<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

?>
<script type="text/javascript">
require(['jquery-nos'], function ($) {
	$(function () {
		$.nos.tabs.update({
			label : <?= json_encode($page->page_title) ?>,
			iconUrl : 'static/cms/img/16/page.png'
		});
	});
});
</script>

<?php
/*
$short_url = $page->page_id; // Needs an application page
$short_link = \View::forge('form/short_link', array(
    'short_url' => $short_url,
));
$qrcode = \View::forge('form/qrcode', array(
    'url' => $short_url,
));
*/

$uniqids = array();
?>

<div class="page line ui-widget" id="<?= $uniqid = uniqid('id_'); ?>">
    <div id="<?= $uniqid_tabs = uniqid('tabs_') ?>" style="width: 92.4%; clear:both; margin:0 auto 1em;">
        <ul style="width: 5%;">
            <?php
            $possible = $page->get_possible_lang();
            $locales = Config::get('locales', array());
            foreach ($possible as $lang) {
                $uniqids[$lang] = uniqid($lang.'_');
                echo '<li style="text-align: center;"><a href="#'.$uniqids[$lang].'">'.Cms\Helper::flag($lang)/*.' '.\Arr::get($locales, $lang, $lang)*/.'</a></li>';
            }
            ?>
        </ul>
        <?php
        $labels     = array();
        $pages_lang = array();
        $common_id = $page->find_main_lang()->page_id;
        foreach ($possible as $lang) {
            $pages_lang[$lang] = $page->find_lang($lang);
            if (!empty($pages_lang[$lang])) {
                $labels[$pages_lang[$lang]->page_id] = Arr::get($locales, $lang, $lang);
            }
        }
        foreach ($possible as $lang) {
            $page_lang = $pages_lang[$lang];
            ?>
            <div id="<?= $uniqids[$lang] ?>" class="page_lang">
                <?php
                if (empty($page_lang)) {
                    echo View::forge('cms::admin/page/page_form_blank_slate', array(
                        'lang'      => $lang,
                        'common_id' => $common_id,
                        'possible'  => $labels,
                    ), false);
                } else {
                    $fieldset->populate_with_instance($page_lang);
                    echo View::forge('cms::admin/page/page_form', array(
                        'uniqid' => $uniqids[$lang],
                        'fieldset' => $fieldset,
                        'page' => $page_lang,
                    ), false);
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<script type="text/javascript">
require([
    'jquery-nos',
	'static/cms/js/admin/page/form.js',
], function($, callback_fn) {
	$(function() {
        var $tabs = $('#<?= $uniqid_tabs ?>');
        $tabs.wijtabs({
            alignment: 'left',
            show: function(e, ui) {
                $(ui.panel).bind('blank_slate', callback_fn).trigger('blank_slate');
            }
        });
        $tabs.find('> ul').css({
            width : '5%'
        });
        $tabs.find('> div').css({
            width : '94%'
        });
        $tabs.wijtabs('select', '#<?= $uniqids[$page->get_lang()] ?>');
	});
});</script>