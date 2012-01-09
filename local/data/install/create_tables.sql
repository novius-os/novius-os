SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------
--
-- Structure de la table `cms_blog_categorie`
--

CREATE TABLE IF NOT EXISTS `cms_blog_categorie` (
  `blgc_id` int(11) NOT NULL auto_increment,
  `blgc_parent_id` int(11) default NULL,
  `blgc_titre` varchar(255) collate latin1_general_ci NOT NULL default '',
  `blgc_niveau` tinyint(4) NOT NULL default '0',
  `blgc_rail` varchar(255) collate latin1_general_ci default NULL,
  `blgc_rang` float default NULL,
  PRIMARY KEY  (`blgc_id`),
  KEY `blgc_parent_id` (`blgc_parent_id`),
  KEY `blgc_rail` (`blgc_rail`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
--
-- Structure de la table `cms_blog_lien_categorie`
--

CREATE TABLE IF NOT EXISTS `cms_blog_lien_categorie` (
  `blog_id` int(11) NOT NULL default '0',
  `blgc_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`blog_id`,`blgc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


-- --------------------------------------------------------
--
-- Structure de la table `cms_blog_tag`
--

CREATE TABLE IF NOT EXISTS `cms_blog_tag` (
  `blgt_blog_id` int(10) unsigned NOT NULL default '0',
  `blgt_tag_id` int(11) NOT NULL,
  PRIMARY KEY  (`blgt_blog_id`,`blgt_tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


-- --------------------------------------------------------
--
-- Structure de la table `cms_commentaire`
--

CREATE TABLE IF NOT EXISTS `cms_commentaire` (
  `comm_id` int(10) unsigned NOT NULL auto_increment,
  `comm_type` varchar(50) collate latin1_general_ci NOT NULL default '',
  `comm_parent_id` int(10) unsigned NOT NULL default '0',
  `comm_parent_title` varchar(255) collate latin1_general_ci default NULL,
  `comm_parent_url` text collate latin1_general_ci,
  `comm_email` varchar(100) collate latin1_general_ci NOT NULL default '',
  `comm_auteur` varchar(100) collate latin1_general_ci NOT NULL default '',
  `comm_contenu` text collate latin1_general_ci NOT NULL,
  `comm_date_creation` datetime NOT NULL default '0000-00-00 00:00:00',
  `comm_ip` varchar(15) collate latin1_general_ci NOT NULL default '',
  `comm_etat` enum('publier','en attente','refuser') collate latin1_general_ci NOT NULL default 'en attente',
  `comm_blacklist` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`comm_id`),
  KEY `comm_type` (`comm_type`,`comm_parent_id`),
  KEY `comm_etat` (`comm_etat`,`comm_blacklist`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Structure de la table `cms_media`
--

CREATE TABLE IF NOT EXISTS `cms_media` (
  `media_id` int(10) unsigned NOT NULL auto_increment,
  `media_path_id` int(10) unsigned NOT NULL,
  `media_path` varchar(255) NOT NULL,
  `media_file` varchar(100) NOT NULL,
  `media_ext` varchar(4) NOT NULL,
  `media_title` varchar(50) NOT NULL,
  `media_module` varchar(30) default NULL,
  `media_protected` tinyint(1) NOT NULL default '0',
  `media_width` smallint(5) unsigned default NULL,
  `media_height` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`media_id`),
  KEY `media_path_id` (`media_path_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Structure de la table `cms_media_folder`
--

CREATE TABLE IF NOT EXISTS `cms_media_folder` (
  `medif_id` int(10) unsigned NOT NULL auto_increment,
  `medif_parent_id` int(10) unsigned default NULL,
  `medif_path` varchar(255) NOT NULL,
  `medif_title` varchar(50) NOT NULL,
  PRIMARY KEY  (`medif_id`),
  KEY `medip_parent_id` (`medif_parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------
--
-- Structure de la table `cms_page`
--

CREATE TABLE IF NOT EXISTS `cms_page` (
  `page_id` int(11) NOT NULL auto_increment,
  `page_rac_id` char(2) collate latin1_general_ci NOT NULL default '',
  `page_pere_id` int(11) default NULL,
  `page_gab_id` int(11) NOT NULL default '0',
  `page_niveau` tinyint(4) NOT NULL default '0',
  `page_titre` varchar(255) collate latin1_general_ci NOT NULL default '',
  `page_titre_menu` varchar(255) collate latin1_general_ci default NULL,
  `page_titre_reference` varchar(255) collate latin1_general_ci default NULL,
  `page_contenu` text collate latin1_general_ci,
  `page_contenu_text` text collate latin1_general_ci,
  `page_travail_contenu` text collate latin1_general_ci,
  `page_html_brut` tinyint(4) default '0',
  `page_rang` float default NULL,
  `page_menu` tinyint(4) NOT NULL default '0',
  `page_type` tinyint(4) NOT NULL default '0',
  `page_publier` tinyint(4) NOT NULL default '0',
  `page_a_publier` datetime default NULL,
  `page_noindex` tinyint(1) unsigned NOT NULL default '0',
  `page_demandeur_id` int(11) default NULL,
  `page_verrou` tinyint(4) NOT NULL default '0',
  `page_home` tinyint(4) NOT NULL default '0',
  `page_carrefour` tinyint(4) NOT NULL default '0',
  `page_duree_vie` int(11) default NULL,
  `page_nom_virtuel` varchar(50) collate latin1_general_ci default NULL,
  `page_url_virtuel` varchar(255) collate latin1_general_ci default NULL,
  `page_lien_externe` varchar(255) collate latin1_general_ci default NULL,
  `page_lien_externe_type` tinyint(4) default NULL,
  `page_date_creation` int(11) NOT NULL default '0',
  `page_date_modif` int(11) NOT NULL default '0',
  `page_description` text collate latin1_general_ci,
  `page_keywords` text collate latin1_general_ci,
  `page_addheader` text collate latin1_general_ci,
  PRIMARY KEY  (`page_id`),
  UNIQUE KEY `page_url_virtuel` (`page_url_virtuel`),
  KEY `page_rac_id` (`page_rac_id`),
  KEY `page_pere_id` (`page_pere_id`),
  KEY `page_gab_id` (`page_gab_id`),
  KEY `page_demandeur_id` (`page_demandeur_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci PACK_KEYS=0 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Structure de la table `os_blog`
--

CREATE TABLE IF NOT EXISTS `os_blog` (
  `blog_id` int(10) unsigned NOT NULL auto_increment,
  `blog_lang` varchar(5) collate latin1_general_ci NOT NULL,
  `blog_lang_common_id` int(11) NOT NULL,
  `blog_lang_single_id` int(11) default NULL,
  `blog_titre` varchar(255) collate latin1_general_ci NOT NULL default '',
  `blog_auteur` varchar(255) collate latin1_general_ci default NULL,
  `blog_auteur_id` int(10) unsigned default NULL,
  `blog_resume` text collate latin1_general_ci,
  `blog_contenu_wysiwyg_id` int(10) unsigned default NULL,
  `blog_contenu_text` text collate latin1_general_ci,
  `blog_date_creation` datetime NOT NULL default '0000-00-00 00:00:00',
  `blog_date_debut_publication` datetime default NULL,
  `blog_date_fin_publication` datetime default NULL,
  `blog_lu` int(10) unsigned NOT NULL default '0',
  `blog__source_id` int(10) unsigned default NULL,
  `blog__titre_initial` varchar(255) collate latin1_general_ci default NULL,
  `blog__source_txt` varchar(255) collate latin1_general_ci default NULL,
  `blog__source_url` varchar(255) collate latin1_general_ci default NULL,
  `blog__importance` enum('important','interessant','strategique') collate latin1_general_ci default NULL,
  `blog__date_info` date default NULL,
  `blog__media_thumbnail` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`blog_id`),
  KEY `blog_auteur_id` (`blog_auteur_id`),
  KEY `blog_lang` (`blog_lang`,`blog_lang_common_id`,`blog_lang_single_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Structure de la table `os_group`
--

CREATE TABLE IF NOT EXISTS `os_group` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `group_name` varchar(50) NOT NULL,
  `group_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Structure de la table `os_group_permission`
--

CREATE TABLE IF NOT EXISTS `os_group_permission` (
  `perm_group_id` int(10) unsigned NOT NULL,
  `perm_module` varchar(30) NOT NULL,
  `perm_identifier` varchar(30) NOT NULL,
  `perm_key` varchar(30) NOT NULL,
  UNIQUE KEY `perm_group_id` (`perm_group_id`,`perm_module`,`perm_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Structure de la table `os_user`
--

CREATE TABLE IF NOT EXISTS `os_user` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_fullname` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(64) NOT NULL,
  `user_last_connection` datetime NOT NULL,
  `user_configuration` text,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Structure de la table `os_user_group`
--

CREATE TABLE IF NOT EXISTS `os_user_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Structure de la table `os_wysiwyg`
--

CREATE TABLE IF NOT EXISTS `os_wysiwyg` (
  `wysiwyg_id` int(10) unsigned NOT NULL auto_increment,
  `wysiwyg_text` text NOT NULL,
  `wysiwyg_join_table` varchar(255) NOT NULL,
  `wysiwyg_key` varchar(30) NOT NULL,
  `wysiwyg_foreign_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`wysiwyg_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Contenu de la table `cms_media_folder`
--

REPLACE INTO `cms_media_folder` (`medif_id`, `medif_parent_id`, `medif_path`, `medif_title`) VALUES
(1, NULL, '', 'Media library');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;