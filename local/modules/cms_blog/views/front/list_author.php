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
<div class="billets">
    <h1><a href="<?= $link_to_author($author); ?>"><?= $author->fullname() ?></a></h1>
    <?= $list ?>
    <?= $pagination ?>
</div>