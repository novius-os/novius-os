<?php
    //if (in_array(PAGE_CONTRIBUTIONS, (array) $GLOBALS['page_rail'])) {
    //    $GLOBALS['langue'] = 'uk';
    //}
    //require_once DIR_PARAM.'fonction/blog.inc';
    define('CONTENU_EDITORIAL', false);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <base href="<?= Uri::base(false); ?>">
  <link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
  <link rel="stylesheet" type="text/css" href="static/css/blog.css">
  <link rel="stylesheet" href="static/js/orangebox/css/orangebox.css" type="text/css" />
  <script type="text/javascript" src="static/js/jquery.js"></script>
  <script type="text/javascript" src="static/js/orangebox/js/orangebox.min.js"></script>
  <script type="text/javascript" src="static/js/site.js"></script>
</head>

<body>
<div id="site_container">
<div id="site">
  <div id="header">
    <form name="search" id="search" method="get" action="<? //URL_BLOG ?>" onsubmit="return Trim(this.search.value);">
      <input type="hidden" name="todo" value="search" />
      <input type="text" class="champ" value="<?= __('Recherche') ?>" name="search" onfocus="if(this.value==this.defaultValue){this.value='';}" onblur="if(Trim(this.value)==''){this.value=this.defaultValue;}"/>
      <input type="image" src="static/images/blog/search.gif"/>
    </form>
    <a href="<? //PubliPage::getUrl(array('carrefour' => $GLOBALS['page_rac_id'])) ?>" id="logo" title="Novius Labs"></a>
  </div>
  <div id="header_menu">
    <?php echo Cms::hmvc('cms_blog/front/menu'); ?>
    <div id="header_menu_left"></div>
    <div id="header_menu_right"></div>
  </div>

  <div id="main">
    <div id="sidebar">
      <?php // echo Cms::hmvc('cms_blog/front/links'); ?>
      <?php echo $wysiwyg_right ?>
      <?php echo Cms::hmvc('cms_blog/front/insert_tags'); ?>
    </div>
    <div id="content<?= CONTENU_EDITORIAL ? '_editorial':'' ?>">
      <?= $wysiwyg_content ?>
    </div>
    <br class="clearfloat"/>
  </div>

  <div id="copyright">
    <b><?= NOM_SITE ?></b>&nbsp;&copy; &nbsp;|&nbsp;
    <?= __('Réalisation :') ?> <a href="http://www.novius.com" target="_blank"><img src="static/images/novius.png" width="21" height="17" border="0" alt="Novius"> Novius</a> &copy; -
    <?= __('Motorisé par :') ?> <a href="http://www.publinova.fr" target="_blank"><img src="static/images/publinova.png" width="16" height="16" border="0" alt="Publi-Nova"> Publi-Nova</a>
  </div>
  <div style="height:350px;">&nbsp;</div>
</div>
</div>
</body>
</html>

<?php
    Cms::main_controller()->page_title = Cms::main_controller()->page->page_meta_title.' - Laboratoire technologique Internet : CMS, mobile, emailing, réseaux sociaux - '.NOM_SITE;
?>
