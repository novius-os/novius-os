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
            $.nos.tabs.updateTab({
                label : '<?= $object->blog_titre ?>',
                iconUrl : 'static/modules/cms_page/img/16/page.png'
            });
        });
    });
</script>


<style type="text/css">
.wijmo-checkbox {
	display: inline-block;
	width: inherit;
	vertical-align: middle;
}
.wijmo-checkbox label {
	width: inherit;
}
.ui-helper-clearfix:after {
	content: '';
}
.mceExternalToolbar {
	z-index:100;
}

.wijmo-wijaccordion p {
	margin: 0.5em 0;
}

/* ? */
.ui-accordion-content-active {
	overflow: visible !important;
}
</style>

<?php

if (true) {
?>

<div class="page myPage myBody">
<?php
$fieldset->form()->set_config('field_template',  "\t\t<tr><th class=\"{error_class}\">{label}{required}</th><td class=\"{error_class}\">{field} {error_msg}</td></tr>\n");

foreach ($fieldset->field() as $field) {
	if ($field->type == 'checkbox') {
		$field->set_template('{field} {label}');
	}
}

$fieldset->field('blog_lu')->set_template('{label} {field} times');
?>

<?= $fieldset->open('admin/cms_blog/form/edit/'.$object->blog_id); ?>
<?= View::forge('form/layout_standard', array(
	'fieldset' => $fieldset,
	'medias' => array('media->thumbnail->medil_media_id'),
	'title' => 'blog_titre',
	'id' => 'blog_id',

	'published' => 'blog_date_debut_publication',
	'save' => 'save',

	'subtitle' => array(),

	'content' => \View::forge('form/expander', array(
		'title'   => 'Content',
		'content' => $fieldset->field('wysiwyg->content->wysiwyg_text'),
	), false),

	'menu' => array(
		'Meta' => array('author->user_fullname', 'blog_auteur', 'blog_date_creation', 'blog_lu'),
		'Categories' => array(),
		'Tags' => array(),
	),
), false); ?>
<?= $fieldset->close(); ?>
</div>

<?php
}

if (false) {
?>

<script type="text/javascript">
    require(['jquery-nos'], function($) {
        $(function() {
            $(":input[type='text'],:input[type='password'],textarea").wijtextbox();
            $(":input[type='submit'],button").button();
            $("select").wijdropdown();
            $(":input[type=checkbox]").wijcheckbox();
            $('.expander').wijexpander({expanded: true });
            $(".accordion").wijaccordion({
                header: "h3"
            });
        });
    });
</script>
<?php

    $globals = array(
        'blog_titre' => array(
            'type' => 'field',
            'name' => 'blog_titre',
            'template' => '{field}',
            'attributes' => array(
                'class' => 'title c4',
            ),
        ),
        'blog_auteur' => array(
            'type' => 'field',
            'name' => 'blog_auteur',
            'template' => '{field}',
            'attributes' => array(
                'style' => 'width: 150px;'
            )
        ),
        'blog_date_creation' => array(
            'type' => 'field',
            'name' => 'blog_date_creation',
            'template' => '{field}',
            'attributes' => array(
                'style' => 'width: 10px;',
            ),
        ),
    );

    $tab = array(
        'type' => 'standard_layout',
        'content' => array(
            array(
                'type' => 'line',
                'layout' => '{blog_titre}',
                'attributes' => array('style' => 'margin-bottom: 1em;'),
            ),
            array(
                'type' => 'line',
                'items' => array(
                    array(
                        'type' => 'expander',
                        'title' => 'Contenu',
                        'layout' => '{wysiwyg->content->wysiwyg_text}',
                    )
                )
            )
        ),
        'menu' => array(
            array(
                'title' => 'Propriétés',
                'content' => array(
                    'type' => 'node',
                    'layout' => '<table>
                                 <tr><td>ID</td><td style="padding: 0px 10px">:</td><td>{blog_id}</td></tr>
                                 <tr><td>Auteur</td><td style="padding: 0px 10px">:</td><td>{author->user_fullname}</td></tr>
                                 <tr><td>Alias</td><td style="padding: 0px 10px">:</td><td>{blog_auteur}</td></tr>
                                 <tr><td>Date création</td><td style="padding: 0px 10px">:</td><td>{blog_date_creation}</td></tr>
                                 <tr><td>Lu</td><td style="padding: 0px 10px">:</td><td>{blog_lu}</td></tr>
                                 </table>',
                )
            ),
            array(
                'title' => 'Catégories',
                'content' => array(
                    'type' => 'node',
                    'partial' => 'cms_blog::form/category/list',
                )
            ),
            array(
                'title' => 'Tags',
                'content' => array(
                    'type' => 'node',
                    'layout' => 'A venir !',
                )
            ),
        )
    );


	echo \Cms\Layout::forge($tab, $object, $fieldset, $globals);
}

if (false) {

?>



<div class="page myPage myBody">
    <?= $fieldset->open('admin/cms_blog/form/edit/'.$object->blog_id); ?>
    <div class="line">
        <div class="unit col c1"></div>
        <div class="unit col c7" id="line_first" style="position:relative;z-index:99;">
            <div class="line" style="margin-bottom:1em;">
                <?= $fieldset->field('blog_titre')
                ->set_template('{field}')
                ->set_attribute('class', 'title c4');
                ?>
                <?= $fieldset->field('blog_id')->set_template('{label} {field}')->build(); ?>

                <?php
                $fieldset->form()->set_config('field_template',  "<p class=\"{error_class}\">{label}{required} {field} {error_msg}</p>");
                ?>
            </div>
        </div>
        <div class="unit col c3" style="position:relative;z-index:98;text-align:center;">
            <p><?= $fieldset->field('save')->set_template('{field}')->build(); ?> &nbsp; or &nbsp; <a href="#" onclick="javascript:$.nos.tabs.close();return false;">Cancel</a></p>
        </div>
    </div>
    <?php
    $fieldset->form()->set_config('field_template',  "\t\t<tr><th class=\"{error_class}\">{label}{required}</th><td class=\"{error_class}\">{field} {error_msg}</td></tr>\n");
    ?>
    <div class="line">
        <div class="unit col c1"></div>
        <div class="unit col c7" id="line_second" style="position:relative;margin-bottom:1em;">
            <div class="expander fieldset">
                <h3>Content</h3>
                <div style="overflow:visible">
                    <div id="internal" style="display:none;">
                        <p style="padding:1em;">We're sorry, internal links are not supported yet. We need a nice page selector before that.</p>
                    </div>
                    <div id="wysiwyg">
                        <?= $fieldset->field('wysiwyg->wysiwyg_text')->build(); ?>
                    </div>
                </div>

            </div>
        </div>
        <?php
        $fieldset->form()->set_config('field_template',  "\t\t<span class=\"{error_class}\">{label}{required}</span>\n\t\t<br />\n\t\t<span class=\"{error_class}\">{field} {error_msg}</span>\n");
        ?>
        <div class="unit col c3" style="position:relative;z-index:98;margin-bottom:1em;">
            <div class="accordion">
                <div>
                    <h3>
                        <a href="#">Menu</a></h3>
                    <div>
                    </div>
                </div>
                <div>
                    <h3>
                        <a href="#">SEO</a></h3>
                    <div>
                    </div>
                </div>
                <div>
                    <h3>
                        <a href="#">Admin</a></h3>
                    <div style="overflow:visible;">
                    </div>
                </div>
            </div>
        </div>
        <div class="unit lastUnit"></div>
    </div>
    <?= $fieldset->close(); ?>
</div>
<?php
}
?>