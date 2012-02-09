<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?= htmlspecialchars($GLOBALS['page_titre_reference'].' - '.NOM_SITE) ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="description" content="<?= $GLOBALS['head_description'] ?>">
<meta name="keywords" content="<?= $GLOBALS['head_keywords'] ?>">
<base href="<?= PHP_BEGIN ?> echo  BASE_URL <?= PHP_END ?>">
<link href="<?= PHP_BEGIN ?> echo  BASE_URL <?= PHP_END ?>favicon.ico" rel="shortcut icon" type="image/x-icon">
<!--[if!lte IE 9]><!-->
<style type="text/css">
    @font-face {
        font-family: "league gothic";
        src: url('static/css/font/league_gothic.otf') format("truetype");
    }
</style>
<!--><![endif]-->
<!--[if lte IE 9]>
<style type="text/css">
    @font-face {
        font-family: "league gothic";
        src: url('static/css/font/league_gothic.eot');
    }
</style>
<![endif]-->
<link rel="stylesheet" type="text/css" href="static/css/site.css">
<link rel="alternate" type="application/rss+xml" title="Novius OS Blog" href="http://feeds.feedburner.com/NoviusOS">

<script src="cms/fonctions.js" type="text/javascript"></script>
<script type="text/javascript" src="static/js/jquery.js"></script>
<?php
    require_once(DIR_PARAM.'include/reso/css_js_files.inc');

    if (!EN_PROD) {


        foreach ($js_files as $js_file) {
            echo '<script type="text/javascript" src="'.$js_file.'?'.$last_js_css_update.'" ></script>';
        }
    } else {
        echo '<script type="text/javascript" src="static/reso/js/bundle.js?'.$last_js_css_update.'" ></script>';
    }
    foreach ($css_files as $css_file) {
        echo '<link href="'.$css_file.'" rel="stylesheet" type="text/css" />';
    }
?>
</head>

<body<?= (GABARIT == 'home')? ' id="body_home"':''?>>
<div id="container">
    <div id="wip"><span>Work In Progress</span>Novius OS est loin d'être stable. Il va considérablement évoluer dans les semaines à venir. Restez informé(e) via <a href="http://twitter.com/NoviusOS">Twitter</a> ou <a href="http://feeds.feedburner.com/NoviusOS">RSS</a>.
    </div>
  <div id="header<?= (GABARIT == 'home')? '_home':''?>">
      <div id="header_background">
      <a href="<?= PubliPage::getURL(array('carrefour' => $GLOBALS['page_rac_id'])) ?>" id="lien_logo"><img src="static/images/logo_novius_os.png" alt="Logo <?= NOM_SITE ?>" width="96" height="77" border="0"></a>
    </div>
      <div id="header_menu">
<?  Gabarit::afficheMenu(); ?>
    </div>
     <div id="header_language">
<?  /*afficheLinkLangMirror(); par Antoine L en attendant la version anglaise */ ?>
            <a href="<?= PubliPage::getURL(49) ?>"><img alt="English version" src="static/images/menu_flag_uk.gif"> English</a>
      </div>
    <br class="clearfloat" />
    <?if(GABARIT == 'home') {?>
        <!-- Zone de texte bandeau accueil -->
        <div id="zone_texte_accueil">
            <span class="g_open"></span>
            <span class="g_open"></span>
            <br class="clearfloat" />
            <H1><?= ClientTraduction::getTraduction('Novius OS, CMS open-source nouvelle génération, transforme votre back-office en OS web',array('langue'=>LANGUE_DEFAUT));?></H1>
            <span class="g_close"></span>
            <span class="g_close"></span>
            <br class="clearfloat" />
        </div>
        <!-- Image computer -->
        <div id="home_image_computer">
            <? Gabarit::afficheIMGComputer();?>
        </div>
        <!-- Bonttons télécharger accueil -->
        <div id="zone_boutton_accueil">
            <a class="boutton_accueil" href="<?=PubliPage::getUrl(array('id'=>'17','LANGUE'=>GLOBAL_LANGUE))?>"><?= ClientTraduction::getTraduction('Testez (démo)',array('langue'=>LANGUE_DEFAUT));?></a>
            <a class="boutton_accueil" href="<?=PubliPage::getUrl(array('id'=>'37','LANGUE'=>GLOBAL_LANGUE))?>"><?= ClientTraduction::getTraduction('Téléchargez',array('langue'=>LANGUE_DEFAUT));?></a>
        </div>
        <br class="clearfloat" />
    <?}?>
  </div>

      <?= $wysiwyg_content ?>
</div>
<div id="footer">
      <div id="footer_menu">
<?  Gabarit::afficheMenuFooter(); ?>
    <div id="host_novius">
        <?=ClientTraduction::getTraduction('Hébergement Novius OS sur : ',array('langue'=>LANGUE_DEFAUT));?>
        <? Gabarit::afficheFooterIMG();?>

    </div>
     <br class="clearfloat" />
    </div>
 </div>
<script type="text/javascript" src="static/js/superfish.js"></script>
<script type="text/javascript" src="static/js/site.js"></script>
</body>
</html>