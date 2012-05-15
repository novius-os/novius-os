
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


--
-- Structure de la table `nos_blog`
--

CREATE TABLE IF NOT EXISTS `nos_blog` (
  `blog_id` int(10) unsigned NOT NULL auto_increment,
  `blog_lang` varchar(5) NOT NULL,
  `blog_lang_common_id` int(11) NOT NULL,
  `blog_lang_single_id` int(11) default NULL,
  `blog_title` varchar(255) NOT NULL default '',
  `blog_author` varchar(255) default NULL,
  `blog_author_id` int(10) unsigned default NULL,
  `blog_summary` text,
  `blog_created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `blog_publication_start` datetime default NULL,
  `blog_publication_end` datetime default NULL,
  `blog_published` tinyint(1) NOT NULL default '1',
  `blog_read` int(10) unsigned NOT NULL default '0',
  `blog_virtual_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`blog_id`),
  KEY `blog_author_id` (`blog_author_id`),
  KEY `blog_lang` (`blog_lang`,`blog_lang_common_id`,`blog_lang_single_id`),
  KEY `blog_virtual_name` (`blog_virtual_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_blog_category`
--

CREATE TABLE IF NOT EXISTS `nos_blog_category` (
  `blgc_id` int(11) NOT NULL auto_increment,
  `blgc_parent_id` int(11) default NULL,
  `blgc_title` varchar(255) NOT NULL default '',
  `blgc_level` tinyint(4) NOT NULL default '0',
  `blgc_path_to_category` varchar(255) default NULL,
  `blgc_sort` float default NULL,
  PRIMARY KEY  (`blgc_id`),
  KEY `blgc_parent_id` (`blgc_parent_id`),
  KEY `blgc_path_to_category` (`blgc_path_to_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_blog_category_link`
--

CREATE TABLE IF NOT EXISTS `nos_blog_category_link` (
  `blog_id` int(11) NOT NULL default '0',
  `blgc_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`blog_id`,`blgc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_blog_tag`
--

CREATE TABLE IF NOT EXISTS `nos_blog_tag` (
  `blgt_blog_id` int(11) NOT NULL,
  `blgt_tag_id` int(11) NOT NULL,
  UNIQUE KEY `blgt_blog_id` (`blgt_blog_id`,`blgt_tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_comment`
--

CREATE TABLE IF NOT EXISTS `nos_comment` (
  `comm_id` int(10) unsigned NOT NULL auto_increment,
  `comm_type` varchar(50) NOT NULL default '',
  `comm_parent_id` int(10) unsigned NOT NULL default '0',
  `comm_parent_title` varchar(255) default NULL,
  `comm_parent_url` text,
  `comm_email` varchar(100) NOT NULL default '',
  `comm_author` varchar(100) NOT NULL default '',
  `comm_content` text NOT NULL,
  `comm_created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `comm_ip` varchar(15) NOT NULL default '',
  `comm_state` enum('published','pending','refused') NOT NULL default 'pending',
  `comm_blacklist` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`comm_id`),
  KEY `comm_type` (`comm_type`,`comm_parent_id`),
  KEY `comm_etat` (`comm_state`,`comm_blacklist`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_media`
--

CREATE TABLE IF NOT EXISTS `nos_media` (
  `media_id` int(10) unsigned NOT NULL auto_increment,
  `media_folder_id` int(10) unsigned NOT NULL,
  `media_path` varchar(255) NOT NULL,
  `media_file` varchar(100) NOT NULL,
  `media_ext` varchar(4) NOT NULL,
  `media_title` varchar(50) NOT NULL,
  `media_application` varchar(30) default NULL,
  `media_protected` tinyint(1) NOT NULL default '0',
  `media_width` smallint(5) unsigned default NULL,
  `media_height` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`media_id`),
  KEY `media_path_id` (`media_folder_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_media_folder`
--

CREATE TABLE IF NOT EXISTS `nos_media_folder` (
  `medif_id` int(10) unsigned NOT NULL auto_increment,
  `medif_parent_id` int(10) unsigned default NULL,
  `medif_path` varchar(255) NOT NULL,
  `medif_title` varchar(50) NOT NULL,
  PRIMARY KEY  (`medif_id`),
  KEY `medip_parent_id` (`medif_parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
REPLACE INTO `os_media_folder` (`medif_id`, `medif_parent_id`, `medif_path`, `medif_title`) VALUES
(1, NULL, '', 'Media centre');


-- --------------------------------------------------------

--
-- Structure de la table `nos_media_link`
--

CREATE TABLE IF NOT EXISTS `nos_media_link` (
  `medil_id` int(10) unsigned NOT NULL auto_increment,
  `medil_from_table` varchar(255) NOT NULL,
  `medil_foreign_id` int(10) unsigned NOT NULL,
  `medil_key` varchar(30) NOT NULL,
  `medil_media_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`medil_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_page`
--

CREATE TABLE IF NOT EXISTS `nos_page` (
  `page_id` int(11) NOT NULL auto_increment,
  `page_root_id` char(2) NOT NULL default '',
  `page_parent_id` int(11) default NULL,
  `page_template` varchar(255) default NULL,
  `page_level` tinyint(4) NOT NULL default '0',
  `page_title` varchar(255) NOT NULL default '',
  `page_lang` varchar(5) NOT NULL,
  `page_lang_common_id` int(11) NOT NULL,
  `page_lang_single_id` int(11) default NULL,
  `page_menu_title` varchar(255) default NULL,
  `page_meta_title` varchar(255) default NULL,
  `page_search_words` text,
  `page_raw_html` tinyint(4) default '0',
  `page_sort` float default NULL,
  `page_menu` tinyint(4) NOT NULL default '0',
  `page_type` tinyint(4) NOT NULL default '0',
  `page_published` tinyint(4) NOT NULL default '0',
  `page_publication_start` datetime default NULL,
  `page_meta_noindex` tinyint(1) unsigned NOT NULL default '0',
  `page_requested_by_user_id` int(11) default NULL,
  `page_lock` tinyint(4) NOT NULL default '0',
  `page_entrance` tinyint(4) NOT NULL default '0',
  `page_home` tinyint(4) NOT NULL default '0',
  `page_cache_duration` int(11) default NULL,
  `page_virtual_name` varchar(50) default NULL,
  `page_virtual_url` varchar(255) default NULL,
  `page_external_link` varchar(255) default NULL,
  `page_external_link_type` tinyint(4) default NULL,
  `page_created_at` int(11) NOT NULL default '0',
  `page_updated_at` int(11) NOT NULL default '0',
  `page_meta_description` text,
  `page_meta_keywords` text,
  `page_head_additional` text,
  `page__couleur` varchar(255) NOT NULL default '#EE7C1C',
  `page__lien_footer_cloud` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`page_id`),
  UNIQUE KEY `page_url_virtuel` (`page_virtual_url`),
  KEY `page_root_id` (`page_root_id`),
  KEY `page_parent_id` (`page_parent_id`),
  KEY `page_requested_by_user_id` (`page_requested_by_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_page_root`
--

CREATE TABLE IF NOT EXISTS `nos_page_root` (
  `root_id` char(2) NOT NULL default '',
  `root_title` varchar(30) NOT NULL default '',
  `root_default_template` varchar(255) NOT NULL,
  `root_sort` tinyint(4) default NULL,
  PRIMARY KEY  (`root_id`),
  KEY `root_default_template` (`root_default_template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_role`
--

CREATE TABLE IF NOT EXISTS `nos_role` (
  `role_id` int(10) unsigned NOT NULL auto_increment,
  `role_name` varchar(50) NOT NULL,
  `role_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_role_permission`
--

CREATE TABLE IF NOT EXISTS `nos_role_permission` (
  `perm_role_id` int(10) unsigned NOT NULL,
  `perm_application` varchar(30) NOT NULL,
  `perm_identifier` varchar(30) NOT NULL,
  `perm_key` varchar(30) NOT NULL,
  UNIQUE KEY `perm_group_id` (`perm_role_id`,`perm_application`,`perm_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_tag`
--

CREATE TABLE IF NOT EXISTS `nos_tag` (
  `tag_id` int(11) NOT NULL auto_increment,
  `tag_label` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag_id`),
  UNIQUE KEY `tag_label` (`tag_label`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_user`
--

CREATE TABLE IF NOT EXISTS `nos_user` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_name` varchar(100) NOT NULL,
  `user_firstname` varchar(100) default NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(64) NOT NULL,
  `user_last_connection` datetime default NULL,
  `user_configuration` text,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_user_role`
--

CREATE TABLE IF NOT EXISTS `nos_user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nos_wysiwyg`
--

CREATE TABLE IF NOT EXISTS `nos_wysiwyg` (
  `wysiwyg_id` int(10) unsigned NOT NULL auto_increment,
  `wysiwyg_text` text NOT NULL,
  `wysiwyg_join_table` varchar(255) NOT NULL,
  `wysiwyg_key` varchar(30) NOT NULL,
  `wysiwyg_foreign_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`wysiwyg_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
