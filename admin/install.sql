CREATE TABLE IF NOT EXISTS `#__quipforum_boards` (
  `tag` varchar(25) NOT NULL,
  `topic` text NOT NULL,
  `access` tinyint(1) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  `ordering` float NOT NULL default '0',
  `read_access` smallint(1) NOT NULL default '0',
  `description` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `thread_count` int(11) NOT NULL,
  `published` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `#__quipforum_boards_read` (
  `board_id` int(11) NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__quipforum_board_access` (
  `viewlevel_id` int(11) NOT NULL,
  `access_level` tinyint(1) NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  `board_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__quipforum_flagged_posts` (
  `id` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  `type` varchar(23) NOT NULL,
  `note` text NOT NULL,
  `votes` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__quipforum_flagged_posts` (
  `id` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  `type` varchar(23) NOT NULL,
  `note` text NOT NULL,
  `votes` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__quipforum_posts` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `thread_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `thread_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_alt_name` tinytext NOT NULL,
  `no_text` tinyint(1) NOT NULL default '0',
  `wysiwyg` tinyint(1) NOT NULL,
  `ip_address` varchar(25) NOT NULL,
  `converted` tinyint(1) NOT NULL default '0',
  `subject` tinytext NOT NULL,
  `body` text NOT NULL,
  `bodyblob` blob NOT NULL,
  `compressed` tinyint(1) NOT NULL default '0',
  `links` tinyint(1) NOT NULL default '0',
  `reference_key_id` int(11) NOT NULL,
  `trashed` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `post_date` (`post_date`),
  KEY `thread_id` (`thread_id`),
  KEY `parent_id` (`parent_id`,`post_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `#__quipforum_posts_read` (
  `user_id` int(11) NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__quipforum_post_logs` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `log` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__quipforum_post_references` (
  `id` int(11) NOT NULL default '0',
  `board_id` int(11) NOT NULL default '0',
  `key_id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`key_id`),
  KEY `id` (`id`),
  KEY `board_id` (`board_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `#__quipforum_threads` (
  `id` int(11) NOT NULL auto_increment,
  `thread_cache` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `#__quipforum_user_settings` (
  `user_id` int(11) NOT NULL,
  `post_prefix` text NOT NULL,
  `post_sig` text NOT NULL,
  `post_template` text NOT NULL,
  `colours` tinytext NOT NULL,
  `ignore_list` text NOT NULL,
  `icon` varchar(255) NOT NULL,
  `flags` text NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
