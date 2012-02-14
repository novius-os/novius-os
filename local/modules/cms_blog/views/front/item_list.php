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
    <div id="encart_gauche_blog" style="margin-bottom:10px;margin-top:8px;">
      <?= $thumbnail ?>
      <br />
      <?= $date ?>
      <br />
      <div class="auteur"><?= $author ?></div>
      <span class="categories"><?= $categories ?></span>
    </div>
    <div id="encart_droite_blog" style="margin-top:5px;margin-left:220px;margin-bottom:10px;">
      <?= $title ?>
      <br />
      <div class="resume" style="text-align:justify"><?= $summary ?></div>
      <a href="<?= $link_to_item ?>" class="read_more">Lire la suite</a>
      <br />
      <div style="clear:left;"></div>
    </div>
    <div class="nb_commentaire">
      <?= $stats ?>
    </div>
</div>