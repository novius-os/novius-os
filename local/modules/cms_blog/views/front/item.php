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
<div class="billet">
      <?= $title ?>
      <?= $date ?> • <?= $author ?>
      <br class="clearfloat"/>

      <div class="resume" style="text-align:justify"><?= $summary ?></div>
      <div class="post"><?= $wysiwyg ?></div>
      <div style="clear:both;"></div>

      <div class="categories"><?= $categories ?></div>
      <div class="tags"><?= $tags ?></div>
      <div class="nb_commentaire"><?= $stats ?></div>

      <?= $comments ?>
</div>