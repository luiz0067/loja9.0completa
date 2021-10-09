-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 27, 2007 at 04:00 PM
-- Server version: 5.0.32
-- PHP Version: 4.4.4-8+etch4
--
-- Database: `storesuite`
--

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%accountingref`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%accountingref` (
  `accountingrefid` int(10) unsigned NOT NULL auto_increment,
  `accountingrefmoduleid` varchar(100) NOT NULL default '',
  `accountingrefexternalid` varchar(100) NOT NULL DEFAULT '',
  `accountingrefnodeid` int(10) unsigned NOT NULL DEFAULT 0,
  `accountingreftype` varchar(20) NOT NULL DEFAULT '',
  `accountingrefvalue` TEXT,
  PRIMARY KEY  (`accountingrefid`),
  KEY `i_accountingref_accountingrefmoduleid` (`accountingrefmoduleid`),
  KEY `i_accountingref_accountingrefexternalid` (`accountingrefexternalid`),
  KEY `i_accountingref_accountingrefnodeid` (`accountingrefnodeid`),
  KEY `i_accountingref_accountingreftype` (`accountingreftype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%administrator_log`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%administrator_log` (
  `logid` int(11) NOT NULL auto_increment,
  `loguserid` int(11) NOT NULL default '0',
  `logip` varchar(30) NOT NULL default '',
  `logdate` int(11) NOT NULL default '0',
  `logtodo` varchar(100) NOT NULL default '',
  `logdata` text,
  PRIMARY KEY  (`logid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%banners`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%banners` (
  `bannerid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `content` longtext,
  `page` enum('home_page','category_page','brand_page','search_page') NOT NULL default 'home_page',
  `catorbrandid` int(11) NOT NULL default '0',
  `location` enum('top','bottom','direito','esquerdo') NOT NULL default 'top',
  `datecreated` int(11) NOT NULL default '0',
  `datetype` enum('always','custom') NOT NULL default 'always',
  `datefrom` int(11) NOT NULL default '0',
  `dateto` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`bannerid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%brands`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%brands` (
  `brandid` int(11) NOT NULL auto_increment,
  `brandname` varchar(255) NOT NULL default '',
  `brandpagetitle` varchar(250) NOT NULL default '',
  `brandmetakeywords` text,
  `brandmetadesc` text,
  `brandimagefile` varchar(255) NOT NULL default '',
  `brandsearchkeywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`brandid`),
  UNIQUE KEY `u_brands_brandname` (`brandname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%brand_search`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%brand_search` (
  `brandsearchid` int(11) NOT NULL auto_increment,
  `brandid` int(11) NOT NULL default '0',
  `brandname` varchar(250) NOT NULL default '',
  `brandpagetitle` varchar(250) NOT NULL default '',
  `brandsearchkeywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`brandsearchid`),
  KEY `i_brand_search_brandid` (`brandid`),
  FULLTEXT KEY `brandname` (`brandname`,`brandpagetitle`,`brandsearchkeywords`),
  FULLTEXT KEY `brandname2` (`brandname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%brand_words`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%brand_words` (
  `wordid` int(11) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL default '',
  `brandid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wordid`),
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%categories`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%categories` (
  `categoryid` int(11) NOT NULL auto_increment,
  `catparentid` int(11) default '0',
  `catname` varchar(50) NOT NULL default '',
  `catdesc` text NOT NULL,
  `catviews` int(11) NOT NULL default	 '0',
  `catsort` int(11) NOT NULL default '0',
  `catpagetitle` varchar(250) NOT NULL default '',
  `catmetakeywords` text,
  `catmetadesc` text,
  `catlayoutfile` varchar(50) NOT NULL default '',
  `catparentlist` text,
  `catimagefile` varchar(255) NOT NULL default '',
  `catvisible` TINYINT NOT NULL DEFAULT 1,
  `catsearchkeywords` varchar(255) NOT NULL default '',
  `cat_enable_optimizer` TINYINT( 1 ) NOT NULL DEFAULT '0',
  `catnsetleft` int(11) unsigned NOT NULL default '0',
  `catnsetright` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryid`),
  KEY `i_categoryid_catnsetleft_catnsetright` (`categoryid`,`catnsetleft`,`catnsetright`),
  KEY `i_catnsetleft` (`catnsetleft`),
  KEY `i_catparentid_catsort_catname` (`catparentid`,`catsort`,`catname`),
  KEY `i_catvisible_catsort_catname` (`catvisible`,`catsort`,`catname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%category_search`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%category_search` (
  `categorysearchid` int(11) NOT NULL auto_increment,
  `categoryid` int(11) NOT NULL default '0',
  `catname` varchar(250) NOT NULL default '',
  `catdesc` text NOT NULL,
  `catsearchkeywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`categorysearchid`),
  KEY `i_category_search_categoryid` (`categoryid`),
  FULLTEXT KEY `catname` (`catname`,`catdesc`,`catsearchkeywords`),
  FULLTEXT KEY `catname2` (`catname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%category_words`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%category_words` (
  `wordid` int(11) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL default '',
  `categoryid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wordid`),
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%categoryassociations`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%categoryassociations` (
  `associationid` int(11) NOT NULL auto_increment,
  `productid` int(11) default '0',
  `categoryid` int(11) default '0',
  PRIMARY KEY  (`associationid`),
  KEY `i_categoryassociations_prodcat` (`productid`, `categoryid`),
  KEY `i_categoryassociations_catprod` (`categoryid`, `productid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%config`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%config` (
  `database_version` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%country_regions`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%country_regions` (
  `couregid` int(11) NOT NULL auto_increment,
  `couregname` varchar(255) NOT NULL default '',
  `couregiso2` char(2) NOT NULL default '',
  `couregiso3` char(3) NOT NULL default '',
  PRIMARY KEY  (`couregid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Data for table `%%PREFIX%%countries`
--

TRUNCATE `%%PREFIX%%country_regions`;

INSERT INTO `%%PREFIX%%country_regions` (`couregid`, `couregname`, `couregiso2`, `couregiso3`) VALUES (1, 'Europa', 'EU', 'EUR');

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%countries`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%countries` (
  `countryid` int(11) NOT NULL auto_increment,
  `countrycouregid` int(11),
  `countryname` varchar(255) NOT NULL default '',
  `countryiso2` char(2) NOT NULL default '',
  `countryiso3` char(3) NOT NULL default '',
  PRIMARY KEY  (`countryid`),
  KEY `i_regions_countrycouregid` (`countrycouregid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Data for table `%%PREFIX%%countries`
--

TRUNCATE `%%PREFIX%%countries`;

INSERT INTO `%%PREFIX%%countries` (`countryid`, `countrycouregid`, `countryname`, `countryiso2`, `countryiso3`) VALUES (30, NULL, 'Brasil', 'BR', 'BRA');

--
-- Table structure for table `%%PREFIX%%country_states`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%country_states` (
  `stateid` int(11) NOT NULL auto_increment,
  `statename` varchar(255) NOT NULL default '',
  `statecountry` int(11) NOT NULL default '0',
  `stateabbrv` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`stateid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

--
-- Data for table `%%PREFIX%%country_states`
--

TRUNCATE `%%PREFIX%%country_states`;

INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Piaui', 30, 'PI');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Sao Paulo', 30, 'SP');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Santa Catarina', 30, 'SA');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Espirito Santo', 30, 'ES');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Bahia', 30, 'BA');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Ceara', 30, 'CE');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Maranhao', 30, 'MA');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Pernambuco', 30, 'PE');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Roraima', 30, 'RR');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Rondonia', 30, 'RO');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Sergipe', 30, 'SE');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Rio Grande do Norte', 30, 'RN');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Rio Grande do Sul', 30, 'RS');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Mato Grosso', 30, 'MT');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Rio de Janeiro', 30, 'RJ');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Mato Grosso do Sul', 30, 'MS');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Acre', 30, 'AC');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Amapa', 30, 'AP');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Goias', 30, 'GO');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Tocantins', 30, 'TO');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Distrito Federal', 30, 'DF');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Parana', 30, 'PR');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Para', 30, 'PA');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Amazonas', 30, 'AM');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Paraiba', 30, 'PB');
INSERT INTO `%%PREFIX%%country_states` (`statename`, `statecountry`, `stateabbrv`) VALUES ('Minas Gerais', 30, 'MG');


-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%coupons`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%coupons` (
  `couponid` int(11) NOT NULL auto_increment,
  `couponname` varchar(100) NOT NULL default '',
  `coupontype` tinyint(4) NOT NULL default '0',
  `couponamount` decimal(20,4) NOT NULL default '0',
  `couponminpurchase` int(11) NOT NULL default '0',
  `couponexpires` int(11) NOT NULL default '0',
  `couponenabled` tinyint(4) NOT NULL default '0',
  `couponcode` varchar(50) NOT NULL default '',
  `couponappliesto` enum('categories','products') NOT NULL default 'products',
  `couponnumuses` int(11) NOT NULL default '0',
  `couponmaxuses` int(11) NOT NULL default '0',
  PRIMARY KEY  (`couponid`),
  UNIQUE KEY `u_coupons_couponcode` (`couponcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%coupon_values`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%coupon_values` (
  `couponid` int(11) NOT NULL,
  `valueid` int(11) NOT NULL,
  PRIMARY KEY  (`couponid`,`valueid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%currencies`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%currencies` (
 `currencyid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `currencycountryid` INT(11) UNSIGNED DEFAULT NULL,
 `currencycouregid` INT(11) UNSIGNED DEFAULT NULL,
 `currencycode` CHAR(3) NOT NULL DEFAULT '',
 `currencyconvertercode` VARCHAR(255) DEFAULT NULL,
 `currencyname` varchar(255) NOT NULL DEFAULT '',
 `currencyexchangerate` DECIMAL(20,10) NOT NULL DEFAULT 0,
 `currencystring` VARCHAR(20) NOT NULL DEFAULT '',
 `currencystringposition` CHAR(5) NOT NULL DEFAULT '',
 `currencydecimalstring` CHAR(1) NOT NULL DEFAULT '',
 `currencythousandstring` CHAR(1) NOT NULL DEFAULT '',
 `currencydecimalplace` SMALLINT UNSIGNED NOT NULL DEFAULT 2,
 `currencylastupdated` INT(11) NOT NULL DEFAULT 0,
 `currencyisdefault` SMALLINT(1) NOT NULL DEFAULT 0,
 `currencystatus` SMALLINT(1) NOT NULL DEFAULT 0,
 PRIMARY KEY (`currencyid`),
 UNIQUE KEY `u_currencies_currencycode_currencycountryid_currencycouregid` (`currencycode`,`currencycountryid`, `currencycouregid`),
 KEY `i_countries_currencycountryid`(`currencycountryid`),
 KEY `i_countries_currencycouregid`(`currencycouregid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%custom_searches`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%custom_searches` (
  `searchid` int(11) NOT NULL auto_increment,
  `searchtype` enum('orders','products','customers', 'returns', 'giftcertificates', 'shipments') NOT NULL default 'orders',
  `searchname` varchar(255) NOT NULL default '',
  `searchvars` longtext,
  PRIMARY KEY  (`searchid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

INSERT INTO `%%PREFIX%%custom_searches` (`searchtype`, `searchname`, `searchvars`) VALUES ('orders', 'Pedidos Incompletos', 'viewName=Incomplete+Orders&orderStatus=0');


-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%customer_credits`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%customer_credits` (
  `custcreditid` int(10) NOT NULL auto_increment,
  `customerid` int(10) NOT NULL default '0',
  `creditamount` decimal(20,4) NOT NULL default '0',
  `credittype` enum('return','gift','adjustment') NOT NULL,
  `creditdate` int(10) NOT NULL default '0',
  `creditrefid` int(10) NOT NULL default '0',
  `credituserid` int(10) NOT NULL default '0',
  `creditreason` varchar(200) NOT NULL default '0',
  PRIMARY KEY  (`custcreditid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%customers`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%customers` (
  `customerid` int(11) NOT NULL auto_increment,
  `custpassword` varchar(50) NOT NULL default '',
  `custconcompany` varchar(255) NOT NULL default '',
  `custconfirstname` varchar(100) NOT NULL default '',
  `custconlastname` varchar(100) NOT NULL default '',
  `custconemail` varchar(250) NOT NULL default '',
  `custconphone` varchar(50) NOT NULL default '',
  `customertoken` varchar(250) NOT NULL default '',
  `customerpasswordresettoken` varchar(32) NOT NULL default '',
  `customerpasswordresetemail` varchar(255) NOT NULL default '',
  `custdatejoined` int(11) NOT NULL default '0',
  `custlastmodified` int(11) NOT NULL default '0',
  `custimportpassword` varchar(100) NOT NULL default '',
  `custstorecredit` decimal(20,4) NOT NULL default '0',
  `custregipaddress` varchar(30) NOT NULL default '',
  `custgroupid` int(11) NOT NULL default '0',
  `custnotes` TEXT,
  `custformsessionid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`customerid`),
  KEY `i_customers_customertoken` (`customertoken`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%forms`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%forms` (
  `formid` int(10) unsigned NOT NULL auto_increment,
  `formname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`formid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


INSERT INTO `%%PREFIX%%forms` VALUES (1,'Dados de Conta');
INSERT INTO `%%PREFIX%%forms` VALUES (2,'Dados de Cobranca');
INSERT INTO `%%PREFIX%%forms` VALUES (3,'Dados de Envio');

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%formfields`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%formfields` (
  `formfieldid` int(10) unsigned NOT NULL auto_increment,
  `formfieldformid` int(10) unsigned NOT NULL default '0',
  `formfieldtype` varchar(50) NOT NULL default '',
  `formfieldlabel` varchar(255) NOT NULL default '',
  `formfielddefaultval` varchar(255) NOT NULL default '',
  `formfieldextrainfo` text,
  `formfieldisrequired` tinyint(1) NOT NULL default '0',
  `formfieldisimmutable` tinyint(1) default '0',
  `formfieldprivateid` varchar(255) NOT NULL default '',
  `formfieldlastmodified` int (10) unsigned NOT NULL default '0',
  `formfieldsort` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`formfieldid`),
  KEY `i_formfields_formfieldformid` (`formfieldformid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;



CREATE TABLE IF NOT EXISTS `%%PREFIX%%formsessions` (
  `formsessionid` int(10) unsigned NOT NULL auto_increment,
  `formsessiondate` int (10) unsigned NOT NULL default '0',
  `formsessionformidx` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`formsessionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%formfieldsessions`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%formfieldsessions` (
  `formfieldsessioniformsessionid` int(10) unsigned NOT NULL default '0',
  `formfieldfieldid` int(10) unsigned NOT NULL default '0',
  `formfieldformid` int(10) unsigned NOT NULL default '0',
  `formfieldfieldtype` varchar(50) NOT NULL default '',
  `formfieldfieldlabel` varchar(255) NOT NULL default '',
  `formfieldfieldvalue` TEXT,
  PRIMARY KEY  (`formfieldsessioniformsessionid`, `formfieldfieldid`),
  KEY `i_formfieldsessions_formfieldsessioniformsessionid` (`formfieldsessioniformsessionid`),
  KEY `i_formfieldsessions_formfieldfieldid` (`formfieldfieldid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%gift_certificate_history`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%gift_certificate_history` (
  `historyid` int(10) NOT NULL auto_increment,
  `histgiftcertid` int(10) NOT NULL default '0',
  `historderid` int(10) NOT NULL default '0',
  `histcustomerid` int(10) NOT NULL default '0',
  `histbalanceused` decimal(20,4) NOT NULL default '0.0000',
  `histbalanceremaining` decimal(20,4) NOT NULL default '0.0000',
  `historddate` int(10) NOT NULL default '0',
  PRIMARY KEY  (`historyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%gift_certificates`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%gift_certificates` (
  `giftcertid` int(10) NOT NULL auto_increment,
  `giftcertcode` varchar(20) NOT NULL default '',
  `giftcertto` varchar(100) NOT NULL default '',
  `giftcerttoemail` varchar(100) NOT NULL default '',
  `giftcertfrom` varchar(100) NOT NULL default '',
  `giftcertfromemail` varchar(100) NOT NULL default '',
  `giftcertcustid` int(10) NOT NULL default '0',
  `giftcertamount` decimal(20,4) NOT NULL default '0',
  `giftcertbalance` decimal(20,4) NOT NULL default '0',
  `giftcertstatus` int(1) NOT NULL default '0',
  `giftcerttemplate` varchar(50) NOT NULL default '',
  `giftcertmessage` varchar(250) NOT NULL default '',
  `giftcertpurchasedate` int(10) NOT NULL default '0',
  `giftcertexpirydate` int(10) NOT NULL default '0',
  `giftcertorderid` int(10) NOT NULL default '0',
  PRIMARY KEY  (`giftcertid`),
  UNIQUE KEY `u_gift_certificates` (`giftcertcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%module_vars`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%module_vars` (
  `variableid` int(11) NOT NULL auto_increment,
  `modulename` varchar(100) NOT NULL default '',
  `variablename` varchar(100) NOT NULL default '',
  `variableval` text,
  PRIMARY KEY  (`variableid`),
  KEY `modulename` (`modulename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%news`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%news` (
  `newsid` int(11) NOT NULL auto_increment,
  `newstitle` varchar(250) NOT NULL default '',
  `newscontent` longtext,
  `newsdate` int(11) NOT NULL default '0',
  `newsvisible` tinyint(4) NOT NULL default '0',
  `newssearchkeywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`newsid`),
  KEY `i_news_date_vis` (`newsdate`, `newsvisible`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%news_search`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%news_search` (
  `newssearchid` int(11) NOT NULL auto_increment,
  `newsid` int(11) NOT NULL default '0',
  `newstitle` varchar(255) NOT NULL default '',
  `newscontent` longtext NOT NULL,
  `newssearchkeywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`newssearchid`),
  KEY `i_news_search_newsid` (`newsid`),
  FULLTEXT KEY `newstitle` (`newstitle`,`newscontent`,`newssearchkeywords`),
  FULLTEXT KEY `newstitle2` (`newstitle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%news_words`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%news_words` (
  `wordid` int(11) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL default '',
  `newsid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wordid`),
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%order_coupons`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%order_coupons` (
  `ordcoupid` int(11) NOT NULL auto_increment,
  `ordcouporderid` int(11) NOT NULL default '0',
  `ordcoupprodid` int(11) NOT NULL default '0',
  `ordcouponid` int(11) NOT NULL default '0',
  `ordcouponcode` varchar(50) NOT NULL default '',
  `ordcouponamount` varchar(50) NOT NULL default '',
  `ordcoupontype` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`ordcoupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%order_downloads`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%order_downloads` (
  `orddownid` int(11) NOT NULL auto_increment,
  `orderid` int(11) NOT NULL default '0',
  `downloadid` int(11) NOT NULL default '0',
  `numdownloads` int(11) NOT NULL default '0',
  `downloadexpires` int unsigned NOT NULL default '0',
  `maxdownloads` int unsigned NOT NULL default '0',
  PRIMARY KEY  (`orddownid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%order_messages`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%order_messages` (
  `messageid` int(11) NOT NULL auto_increment,
  `messagefrom` enum('customer','admin') NOT NULL default 'customer',
  `subject` varchar(255) NOT NULL default '',
  `message` longtext,
  `datestamp` int(11) NOT NULL default '0',
  `messageorderid` int(11) NOT NULL default '0',
  `messagestatus` enum('read','unread') NOT NULL default 'read',
  `staffuserid` int(11) NOT NULL default '0',
  `isflagged` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`messageid`),
  KEY `i_order_mesages_messageorderid` (`messageorderid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%order_products`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%order_products` (
  `orderprodid` int(11) NOT NULL auto_increment,
  `ordprodsku` varchar(250) NOT NULL default '',
  `ordprodname` varchar(250) NOT NULL default '',
  `ordprodtype` enum('physical','digital','giftcertificate') NOT NULL default 'physical',
  `ordprodcost` decimal(20,4) NOT NULL default '0',
  `ordprodoriginalcost` decimal(20, 4) NOT NULL default '0',
  `ordprodweight` double NOT NULL default '0',
  `ordprodqty` smallint(6) NOT NULL default '0',
  `orderorderid` int(11) NOT NULL default '0',
  `ordprodid` int(11) NOT NULL default '0',
  `ordprodcostprice` decimal(20,4) NOT NULL default '0',
  `ordoriginalprice` decimal(20,4) NOT NULL default '0',
  `ordprodrefunded` int(10) NOT NULL default '0',
  `ordprodrefundamount` decimal(20,4) NOT NULL default '0',
  `ordprodreturnid` int(10) NOT NULL default '0',
  `ordprodoptions` text,
  `ordprodvariationid` int(11) NOT NULL default '0',
  `ordprodwrapid` int unsigned NOT NULL default '0',
  `ordprodwrapname` varchar(100) NOT NULL default '',
  `ordprodwrapcost` decimal(20, 4) NOT NULL default '0.00',
  `ordprodwrapmessage` text NULL,
  `ordprodqtyshipped` int unsigned NOT NULL default '0',
  `ordprodeventname` VARCHAR(255),
  `ordprodeventdate` INT(9),
  `ordprodistaxable` tinyint(1) NOT NULL default '1',
  `ordprodfixedshippingcost` decimal(20,4) NOT NULL default '0',
  PRIMARY KEY  (`orderprodid`),
  KEY `i_order_products_orderid_prodid` (`orderorderid`, `ordprodid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%order_status`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%order_status` (
  `statusid` int(11) NOT NULL auto_increment,
  `statusdesc` varchar(100) NOT NULL default '',
  `statusorder` int(11) NOT NULL default 0,
  PRIMARY KEY  (`statusid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


--
-- Data for table `%%PREFIX%%order_status`
--

TRUNCATE `%%PREFIX%%order_status`;
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (1, 'Pendente', 1);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (2, 'Enviado', 8);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (3, 'Env. Parcialmente', 6);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (4, 'Reembolsado', 11);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (5, 'Cancelado', 9);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (6, 'Recusado', 10);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (7, 'Aguar. Pagamento', 2);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (8, 'Aguar. Retirada', 5);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (9, 'Aguar. Envio', 4);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (10, 'Completo', 7);
INSERT INTO `%%PREFIX%%order_status` (`statusid`, `statusdesc`, `statusorder`) VALUES (11, 'Aguar. Cumprimento', 3);

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%orders`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%orders` (
  `orderid` int(11) NOT NULL auto_increment,
  `ordtoken` varchar(32) NOT NULL default '0',
  `ordcustid` int(11) NOT NULL default '0',
  `orddate` int(11) NOT NULL default '0',
  `ordlastmodified` int(11) NOT NULL default '0',
  `ordsubtotal` decimal(20,4) NOT NULL default '0',
  `ordtaxtotal` decimal(20,4) NOT NULL default '0',
  `ordtaxrate` decimal(10,4) NOT NULL default '0',
  `ordtaxname` varchar(100) NOT NULL default '',
  `ordtotalincludestax` int(1) NOT NULL default '0',
  `ordshipcost` decimal(20,4) NOT NULL default '0',
  `ordshipmethod` varchar(250) NOT NULL default '0',
  `ordershipmodule` varchar(100) NOT NULL default '',
  `ordhandlingcost` decimal(20,4) NOT NULL default '0',
  `ordtotalamount` decimal(20,4) NOT NULL default '0',
  `ordstatus` smallint(6) NOT NULL default '0',
  `ordtotalqty` int unsigned NOT NULL default '0',
  `ordtotalshipped` int unsigned NOT NULL default '0',
  `orderpaymentmethod` varchar(100) NOT NULL default '',
  `orderpaymentmodule` varchar(100) NOT NULL default '',
  `ordpayproviderid` varchar(255) DEFAULT NULL,
  `ordpaymentstatus` varchar(100) NOT NULL DEFAULT '',
  `ordrefundedamount` decimal(20, 4) NOT NULL DEFAULT 0,
  `ordbillfirstname` varchar(255) NOT NULL default '',
  `ordbilllastname` varchar(255) NOT NULL default '',
  `ordbillcompany` varchar(100) NOT NULL default '',
  `ordbillstreet1` varchar(255) NOT NULL default '',
  `ordbillstreet2` varchar(255) NOT NULL default '',
  `ordbillsuburb` varchar(100) NOT NULL default '',
  `ordbillstate` varchar(50) NOT NULL default '',
  `ordbillzip` varchar(20) NOT NULL default '',
  `ordbillcountry` varchar(50) NOT NULL default '',
  `ordbillcountrycode` varchar(2) NOT NULL default '',
  `ordbillcountryid` int(11) NOT NULL default '0',
  `ordbillstateid` int(11) NOT NULL default '0',
  `ordbillphone` varchar(50) NOT NULL default '',
  `ordbillemail` varchar(250) NOT NULL default '',
  `ordshipfirstname` varchar(100) NOT NULL default '',
  `ordshiplastname` varchar(100) NOT NULL default '',
  `ordshipcompany` varchar(100) NOT NULL default '',
  `ordshipstreet1` varchar(255) NOT NULL default '',
  `ordshipstreet2` varchar(255) NOT NULL default '',
  `ordshipsuburb` varchar(100) NOT NULL default '',
  `ordshipstate` varchar(50) NOT NULL default '',
  `ordshipzip` varchar(20) NOT NULL default '',
  `ordshipcountry` varchar(50) NOT NULL default '',
  `ordshipcountrycode` varchar(2) NOT NULL default '',
  `ordshipcountryid` int(11) NOT NULL default '0',
  `ordshipstateid` int(11) NOT NULL default '0',
  `ordshipphone` varchar(50) NOT NULL default '',
  `ordshipemail` varchar(250) NOT NULL default '',
  `ordisdigital` tinyint(4) NOT NULL default '0',
  `ordtrackingno` varchar(100) NOT NULL default '',
  `orddateshipped` int(11) NOT NULL default '0',
  `ordgatewayamount` decimal(20,4) NOT NULL default '0',
  `ordstorecreditamount` decimal(20,4) NOT NULL default '0',
  `ordgiftcertificateamount` decimal(20,4) NOT NULL default '0',
  `ordinventoryupdated` int(1) NOT NULL default '0',
  `ordonlygiftcerts` tinyint(4) NOT NULL default '0',
  `extrainfo` text,
  `ordipaddress` varchar(30) NOT NULL default '',
  `ordgeoipcountry` varchar(50) NOT NULL default '',
  `ordgeoipcountrycode` varchar(2) NOT NULL default '',
  `ordcurrencyid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `orddefaultcurrencyid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `ordcurrencyexchangerate` DECIMAL(20,10) NOT NULL DEFAULT '0',
  `ordshippingzoneid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `ordshippingzone` VARCHAR(200) NOT NULL DEFAULT '',
  `ordnotes` TEXT,
  `ordcustmessage` TEXT,
  `ordvendorid` int unsigned NOT NULL default '0',
  `ordformsessionid` int(11) NOT NULL default '0',
  `orddiscountamount` decimal(20, 4) NOT NULL default '0',
  PRIMARY KEY  (`orderid`),
  KEY `i_orders_ordcustid` (`ordcustid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%pages`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%pages` (
  `pageid` int(11) NOT NULL auto_increment,
  `pagetitle` varchar(250) NOT NULL default '',
  `pagemetatitle` varchar(250) NOT NULL default '',
  `pagelink` varchar(250) NOT NULL default '',
  `pagefeed` varchar(250) NOT NULL default '',
  `pageemail` varchar(250) NOT NULL default '',
  `pagecontent` longtext,
  `pagestatus` tinyint(4) NOT NULL default '0',
  `pageparentid` int(11) NOT NULL default '0',
  `pagesort` int(11) NOT NULL default '0',
  `pagekeywords` text,
  `pagedesc` text,
  `pagetype` tinyint(4) NOT NULL default '0',
  `pagecontactfields` varchar(100) NOT NULL,
  `pagemetakeywords` varchar(250) NOT NULL default '',
  `pagemetadesc` varchar(250) NOT NULL default '',
  `pageishomepage` tinyint(4) NOT NULL default '0',
  `pagelayoutfile` varchar(50) NOT NULL default '',
  `pageparentlist` text,
  `pagecustomersonly` tinyint(1) NOT NULL default '0',
  `pagevendorid` int unsigned NOT NULL default '0',
  `page_enable_optimizer` TINYINT( 1 ) NOT NULL DEFAULT '0',
  `pagensetleft` int(11) unsigned NOT NULL default '0',
  `pagensetright` int(11) unsigned NOT NULL default '0',
  `pagesearchkeywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`pageid`),
  KEY `i_pageid_pagensetleft_pagensetright` (`pageid`,`pagensetleft`,`pagensetright`),
  KEY `i_pagensetleft` (`pagensetleft`),
  KEY `i_pageparentid_pagesort_pagetitle` (`pageparentid`,`pagesort`,`pagetitle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%page_search`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%page_search` (
  `pagesearchid` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `pagetitle` varchar(255) NOT NULL default '',
  `pagecontent` longtext NOT NULL,
  `pagedesc` text NOT NULL,
  `pagesearchkeywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`pagesearchid`),
  KEY `i_page_search_pageid` (`pageid`),
  FULLTEXT KEY `pagetitle` (`pagetitle`,`pagecontent`,`pagedesc`,`pagesearchkeywords`),
  FULLTEXT KEY `pagetitle2` (`pagetitle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%page_words`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%page_words` (
  `wordid` int(11) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL default '',
  `pageid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wordid`),
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%permissions`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%permissions` (
  `pk_permid` int(11) NOT NULL auto_increment,
  `permuserid` int(11) NOT NULL default '0',
  `permpermissionid` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`pk_permid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_customfields`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_customfields` (
  `fieldid` int(11) NOT NULL auto_increment,
  `fieldprodid` int(11) NOT NULL default '0',
  `fieldname` varchar(250) NOT NULL default '',
  `fieldvalue` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_downloads`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_downloads` (
  `downloadid` int(11) NOT NULL auto_increment,
  `prodhash` varchar(32) NOT NULL default '',
  `productid` int(11) NOT NULL default '0',
  `downfile` varchar(200) NOT NULL default '',
  `downdateadded` int(11) NOT NULL default '0',
  `downmaxdownloads` int(11) NOT NULL default '0',
  `downexpiresafter` int(11) NOT NULL default '0',
  `downfilesize` int(11) NOT NULL default '0',
  `downname` varchar(200) NOT NULL default '',
  `downdescription` text,
  PRIMARY KEY  (`downloadid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_images`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_images` (
  `imageid` int(11) NOT NULL auto_increment,
  `imageprodid` int(11) NOT NULL default '0',
  `imageprodhash` varchar(32) NOT NULL default '',
  `imagefile` varchar(255) NOT NULL default '',
  `imageisthumb` tinyint(4) NOT NULL default '0',
  `imagesort` int(11) NOT NULL default '0',
  `imagefiletiny` varchar(255) default '',
  `imagefilethumb` varchar(255) default '',
  `imagefilestd` varchar(255) default '',
  `imagefilezoom` varchar(255) default '',
  `imagedesc` longtext,
  `imagedateadded` int(11) default '0',
  `imagefiletinysize` varchar(11) default '',
  `imagefilethumbsize` varchar(11) default '',
  `imagefilestdsize` varchar(11) default '',
  `imagefilezoomsize` varchar(11) default '',
  PRIMARY KEY  (`imageid`),
  KEY `i_product_images_imageprodid` (`imageprodid`, `imageisthumb`),
  KEY `i_product_images_imageprodid_imagesort_imageprodhash` (`imageprodid`,`imagesort`,`imageprodhash`),
  KEY `i_product_images_imageid_imageprodid_imageprodhash` (`imageid`,`imageprodid`,`imageprodhash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_search`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_search` (
  `productsearchid` int(11) NOT NULL auto_increment,
  `productid` int(11) NOT NULL default '0',
  `prodname` varchar(250) NOT NULL default '',
  `prodcode` varchar(250) NOT NULL default '',
  `proddesc` longtext,
  `prodsearchkeywords` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`productsearchid`),
  KEY `i_product_search_productid` (`productid`),
  KEY `i_product_search_prodcode` (`prodcode`),
  FULLTEXT KEY `prodname` (`prodname`,`prodcode`,`proddesc`,`prodsearchkeywords`),
  FULLTEXT KEY `prodname2` (`prodname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_variation_combinations`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_variation_combinations` (
  `combinationid` int(11) NOT NULL auto_increment,
  `vcproductid` int(11) NOT NULL default '0',
  `vcproducthash` varchar(32) NOT NULL default '',
  `vcvariationid` int(11) NOT NULL default '0',
  `vcenabled` tinyint(4) NOT NULL default '1',
  `vcoptionids` varchar(100) NOT NULL default '',
  `vcsku` varchar(50) NOT NULL default '',
  `vcpricediff` enum('','add','subtract','fixed') NOT NULL default '',
  `vcprice` decimal(20,4) NOT NULL default '0',
  `vcweightdiff` enum('','add','subtract','fixed') NOT NULL default '',
  `vcweight` decimal(20,4) NOT NULL default '0',
  `vcimage` varchar(100) NOT NULL default '',
  `vcimagezoom` varchar(100) NOT NULL default '',
  `vcimagestd` varchar(100) NOT NULL default '',
  `vcimagethumb` varchar(100) NOT NULL default '',
  `vcstock` int(11) NOT NULL default '0',
  `vclowstock` int(11) NOT NULL default '0',
  `vclastmodified` int(10) NOT NULL default '0',
  PRIMARY KEY  (`combinationid`),
  KEY `i_product_variation_combinations_vcvariationid` (`vcvariationid`),
  KEY `i_product_variation_combinations_vcproductid` (`vcproductid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_variation_options`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_variation_options` (
  `voptionid` int(11) NOT NULL auto_increment,
  `vovariationid` int(11) NOT NULL default '0',
  `voname` varchar(255) NOT NULL default '',
  `vovalue` text,
  `vooptionsort` int(11) NOT NULL default '0',
  `vovaluesort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`voptionid`),
  KEY `i_product_variation_options_vovariationid` (`vovariationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_variations`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_variations` (
  `variationid` int(11) NOT NULL auto_increment,
  `vname` varchar(100) NOT NULL default '',
  `vnumoptions` int(11) NOT NULL default '0',
  `vvendorid` int unsigned NOT NULL default '0',
  PRIMARY KEY  (`variationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_words`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_words` (
  `wordid` int(11) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL default '',
  `productid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wordid`),
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%products`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%products` (
  `productid` int(11) NOT NULL auto_increment,
  `prodname` varchar(250) NOT NULL default '',
  `prodtype` smallint(6) NOT NULL default '0',
  `prodcode` varchar(250) NOT NULL default '',
  `prodfile` varchar(250) NOT NULL default '',
  `proddesc` longtext,
  `prodsearchkeywords` varchar(250) NOT NULL default '',
  `prodavailability` varchar(250) NOT NULL default '',
  `prodprice` decimal(20,4) NOT NULL default '0',
  `prodcostprice` decimal(20,4) NOT NULL default '0',
  `prodretailprice` decimal(20,4) NOT NULL default '0',
  `prodsaleprice` decimal(20,4) NOT NULL default '0',
  `prodcalculatedprice` decimal(20,4) NOT NULL default '0',
  `prodistaxable` tinyint(1) NOT NULL default '1',
  `prodsortorder` int(11) NOT NULL default '0',
  `prodvisible` tinyint(4) NOT NULL default '0',
  `prodfeatured` tinyint(4) NOT NULL default '0',
  `prodvendorfeatured` tinyint(1) NOT NULL default '0',
  `prodrelatedproducts` varchar(250) NOT NULL default '',
  `prodcurrentinv` int(11) NOT NULL default '0',
  `prodlowinv` int(11) NOT NULL default '0',
  `prodoptionsrequired` tinyint(4) NOT NULL default '0',
  `prodwarranty` text,
  `prodweight` decimal(20,4) NOT NULL default '0',
  `prodwidth` decimal(20,4) NOT NULL default '0',
  `prodheight` decimal(20,4) NOT NULL default '0',
  `proddepth` decimal(20,4) NOT NULL default '0',
  `prodfixedshippingcost` decimal(20,4) NOT NULL default '0',
  `prodfreeshipping` tinyint(4) NOT NULL default '0',
  `prodinvtrack` tinyint(4) NOT NULL default '0',
  `prodratingtotal` int(11) NOT NULL default '0',
  `prodnumratings` int(11) NOT NULL default '0',
  `prodnumsold` int(11) NOT NULL default '0',
  `proddateadded` int(11) NOT NULL default '0',
  `prodbrandid` int(11) NOT NULL default '0',
  `prodnumviews` int(11) NOT NULL default '0',
  `prodpagetitle` varchar(250) NOT NULL default '',
  `prodmetakeywords` text,
  `prodmetadesc` text,
  `prodlayoutfile` varchar(50) NOT NULL default '',
  `prodvariationid` int(11) NOT NULL default '0',
  `prodallowpurchases` int(1) NOT NULL default '1',
  `prodhideprice` int(1) NOT NULL default '0',
  `prodcallforpricinglabel` varchar(200) NOT NULL default '',
  `prodcatids` text NOT NULL,
  `prodlastmodified` int unsigned NOT NULL default '0',
  `prodvendorid` int unsigned NOT NULL default '0',
  `prodhastags` int(1) NOT NULL default '0',
  `prodwrapoptions` text NULL,
  `prodconfigfields` varchar(255) NOT NULL default '',
  `prodeventdaterequired` tinyint(4),
  `prodeventdatefieldname` varchar(255),
  `prodeventdatelimited` tinyint(4),
  `prodeventdatelimitedtype` tinyint(4),
  `prodeventdatelimitedstartdate` int(9),
  `prodeventdatelimitedenddate` int(9),
  `prodmyobasset` VARCHAR(20) NOT NULL default '',
  `prodmyobincome` VARCHAR(20) NOT NULL default '',
  `prodmyobexpense` VARCHAR(20) NOT NULL default '',
  `prodpeachtreegl` VARCHAR(20) NOT NULL default '',
  `prodcondition` enum('New','Used','Refurbished') NOT NULL default 'New',
  `prodshowcondition` tinyint(1) unsigned NOT NULL default '0',
  `product_enable_optimizer` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (`productid`),
  KEY `i_products_brand_vis` (`prodbrandid`, `prodvisible`),
  UNIQUE KEY `u_products_prodname` (`prodname`),
  KEY `i_products_prodnumsold` (`prodnumsold`),
  KEY `i_products_feature_vis` (`prodfeatured`, `prodvisible`),
  KEY `i_products_rating_vis` (`prodvisible`, `prodratingtotal`),
  KEY `i_products_added_vis` (`prodvisible`, `proddateadded`),
  KEY `i_products_hideprice_vis` (`prodhideprice`, `prodvisible`),
  KEY `i_products_sortorder_vis` (`prodvisible`, `prodsortorder`, `prodname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%returns`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%returns` (
  `returnid` int(10) NOT NULL auto_increment,
  `retorderid` int(10) NOT NULL default '0',
  `retcustomerid` int(10) NOT NULL default '0',
  `retprodid` int(10) NOT NULL default '0',
  `retprodvariationid` INT( 11 ) NOT NULL default '0',
  `retprodoptions` text,
  `retprodname` varchar(200) NOT NULL default '',
  `retprodcost` decimal(20,4) NOT NULL default '0',
  `retprodqty` int(1) NOT NULL default '1',
  `retstatus` int(1) NOT NULL default '0',
  `retreason` varchar(200) NOT NULL default '',
  `retaction` varchar(200) NOT NULL default '',
  `retdaterequested` int(10) NOT NULL default '0',
  `retcomment` text,
  `retuserid` int(10) NOT NULL default '0',
  `retreceivedcredit` int(1) NOT NULL default '0',
  `retordprodid` int(10) NOT NULL default '0',
  `retstaffnotes` text,
  `retvendorid` int unsigned NOT NULL default '0',
  PRIMARY KEY  (`returnid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%reviews`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%reviews` (
  `reviewid` int(11) NOT NULL auto_increment,
  `revproductid` int(11) NOT NULL default '0',
  `revfromname` varchar(100) NOT NULL default '',
  `revdate` int(11) NOT NULL default '0',
  `revrating` smallint(6) NOT NULL default '0',
  `revtext` text,
  `revtitle` varchar(250) NOT NULL default '',
  `revstatus` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`reviewid`),
  KEY `i_reviews_revproductid` (`revproductid`),
  FULLTEXT KEY `ft_reviews_text_title_from` (`revtext`,`revtitle`,`revfromname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%search_corrections`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%search_corrections` (
  `correctionid` int(11) NOT NULL auto_increment,
  `correctiontype` enum('correction','recommendation') NOT NULL default 'correction',
  `correction` varchar(250) NOT NULL default '',
  `numresults` int(11) NOT NULL default '0',
  `oldsearchtext` varchar(250) NOT NULL default '',
  `oldnumresults` int(11) NOT NULL default '0',
  `correctdate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`correctionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%searches`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%searches` (
  `searchid` int(11) NOT NULL auto_increment,
  `searchtext` text,
  `numsearches` int(11) NOT NULL default '0',
  PRIMARY KEY  (`searchid`),
  FULLTEXT KEY `searchtext` (`searchtext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%searches_extended`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%searches_extended` (
  `searchid` int(11) NOT NULL auto_increment,
  `searchtext` text,
  `numresults` int(11) NOT NULL default '0',
  `searchdate` int(11) NOT NULL default '0',
  `clickthru` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`searchid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%sessions`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%sessions` (
  `sessionid` int(10) unsigned NOT NULL auto_increment,
  `sessionhash` varchar(32) NOT NULL default '',
  `sessdata` longtext,
  `sesslastupdated` int(10) NOT NULL default '0',
  PRIMARY KEY  (`sessionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%shipping_addresses`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%shipping_addresses` (
  `shipid` int(11) NOT NULL auto_increment,
  `shipcustomerid` int(11) NOT NULL default '0',
  `shipfirstname` varchar(255) NOT NULL default '',
  `shiplastname` varchar(255) NOT NULL default '',
  `shipcompany` varchar(255) NOT NULL default '',
  `shipaddress1` text,
  `shipaddress2` text,
  `shipcity` varchar(100) NOT NULL default '',
  `shipstate` varchar(100) NOT NULL default '',
  `shipzip` varchar(30) NOT NULL default '',
  `shipcountry` varchar(100) NOT NULL default '',
  `shipphone` varchar(50) NOT NULL default '',
  `shipstateid` int(11) NOT NULL default '0',
  `shipcountryid` int(11) NOT NULL default '0',
  `shipdestination` enum('residential','commercial') NOT NULL default 'residential',
  `shiplastused` int(11) NOT NULL default '0',
  `shipformsessionid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`shipid`),
  KEY `i_shipping_addresses_shipcustomerid` (`shipcustomerid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%subscribers`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%subscribers` (
  `subscriberid` int(11) NOT NULL auto_increment,
  `subemail` varchar(250) NOT NULL default '',
  `subfirstname` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`subscriberid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%system_log`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%system_log` (
  `logid` int(11) NOT NULL auto_increment,
  `logtype` enum('general','php','sql','shipping','payment','notification','ssnx') default NULL,
  `logmodule` varchar(100) NOT NULL default '',
  `logseverity` int(1) NOT NULL default '4',
  `logsummary` varchar(250) NOT NULL  default '',
  `logmsg` longtext,
  `logdate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`logid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%tax_rates`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%tax_rates` (
  `taxrateid` int(11) NOT NULL auto_increment,
  `taxratename` varchar(50) NOT NULL default '',
  `taxratepercent` decimal(20,4) NOT NULL default '0',
  `taxratecountry` text,
  `taxratestates` text,
  `taxratebasedon` enum('subtotal','subtotal_and_shipping') NOT NULL default 'subtotal',
  `taxaddress` enum('billing','shipping') NOT NULL default 'billing',
  `taxratestatus` tinyint(4) NOT NULL,
  `taxshippingfortaxableorder` tinyint(1) NOT NULL,
  PRIMARY KEY  (`taxrateid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

--
-- Data for table `%%PREFIX%%tax_rates`
--


-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%unique_visitors`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%unique_visitors` (
  `uniqueid` int(11) NOT NULL auto_increment,
  `datestamp` int(11) NOT NULL default '0',
  `numuniques` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uniqueid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%users`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%users` (
  `pk_userid` int(11) NOT NULL auto_increment,
  `username` varchar(50) NOT NULL default '',
  `userpass` varchar(50) NOT NULL default '',
  `userfirstname` varchar(50) NOT NULL default '',
  `userlastname` varchar(50) NOT NULL default '',
  `userstatus` tinyint(1) NOT NULL default '0',
  `useremail` varchar(250) NOT NULL default '',
  `userimportpass` varchar(100) NOT NULL default '',
  `token` varchar(50) NOT NULL default '',
  `usertoken` varchar(50) NOT NULL default '',
  `userapi` tinyint(4) NOT NULL default '0',
  `uservendorid` int unsigned NOT NULL default '0',
  `userrole` varchar(20) NOT NULL default 'custom',
  PRIMARY KEY  (`pk_userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%wishlists`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%wishlists` (
 `wishlistid` int(11) NOT NULL auto_increment,
  `customerid` int(11) NOT NULL,
  `wishlistname` varchar(255) NOT NULL,
  `ispublic` tinyint(4) NOT NULL,
  `wishlisttoken` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`wishlistid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


--
-- Table structure for table `isc_wishlist_items`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%wishlist_items` (
  `wishlistitemid` int(11) NOT NULL auto_increment,
  `wishlistid` int(11) NOT NULL,
  `productid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wishlistitemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


DELETE FROM `%%PREFIX%%users` WHERE username='admin';
INSERT INTO `%%PREFIX%%users`(pk_userid, username, userpass, userstatus, token, usertoken, userapi, useremail,userrole) values(0, 'admin', '%%PASS%%', '1', '%%TOKEN%%', '', '0', '%%EMAIL%%','admin');

INSERT INTO `%%PREFIX%%pages` (`pageid`, `pagetitle`, `pagelink`, `pagefeed`, `pageemail`, `pagecontent`, `pagestatus`, `pageparentid`, `pagesort`, `pagekeywords`, `pagedesc`, `pagetype`, `pagecontactfields`, `pagemetakeywords`, `pagemetadesc`, `pageishomepage`, `pagelayoutfile`, `pageparentlist`) VALUES (0, 'Pagina de Teste', 'http://', 'http://', '', 'Conteudo da Pagina de Teste', 1, 0, 2, '', '', 0, '', '', '', 0, 'page.html', '0');

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%users`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%customer_groups` (
  `customergroupid` int(11) NOT NULL auto_increment,
  `groupname` varchar(255) NOT NULL,
  `discount` decimal(10,4) NOT NULL,
  `discountmethod` VARCHAR( 100 ) NOT NULL,
  `isdefault` tinyint(4) NOT NULL,
  `categoryaccesstype` enum('none','all','specific') NOT NULL,
  PRIMARY KEY  (`customergroupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `%%PREFIX%%customer_group_categories` (
	`customergroupid` int(11) NOT NULL,
	`categoryid` int(11) NOT NULL,
	PRIMARY KEY  (`customergroupid`,`categoryid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS %%PREFIX%%transactions (
	id int unsigned not null auto_increment PRIMARY KEY,
	orderid int unsigned default NULL,
	transactionid varchar(160) default NULL,
	providerid varchar(160),
	amount DECIMAL(20, 4) NOT NULL,
	message text not null,
	status int unsigned default 0,
	transactiondate int not null,
	extrainfo text,
	KEY `i_order_transation` (orderid, transactionid),
	KEY `i_transaction_provider` (transactionid, providerid)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


--
-- Table structure for table `%%PREFIX%%customer_group_discounts`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%customer_group_discounts` (
  `groupdiscountid` INT NOT NULL AUTO_INCREMENT ,
  `customergroupid` INT NOT NULL ,
  `discounttype` ENUM( 'CATEGORY', 'PRODUCT' ) NOT NULL ,
  `catorprodid` INT NOT NULL ,
  `discountpercent` DECIMAL( 10, 4 ) NOT NULL ,
  `appliesto` ENUM( 'CATEGORY_ONLY', 'CATEGORY_AND_SUBCATS', 'NOT_APPLICABLE' ) NOT NULL ,
  `discountmethod` VARCHAR(100) NOT NULL ,
PRIMARY KEY ( `groupdiscountid` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%shipping_methods` (
  `methodid` int(10) unsigned NOT NULL auto_increment,
  `zoneid` int(10) unsigned NOT NULL default '0',
  `methodname` varchar(150) NOT NULL default '',
  `methodmodule` varchar(100) NOT NULL default '',
  `methodhandlingfee` decimal(20,4) NOT NULL default '0.0000',
  `methodenabled` int(1) NOT NULL default '1',
  `methodvendorid` int unsigned NOT NULL default '0',
  PRIMARY KEY  (`methodid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%shipping_vars` (
  `variableid` int(11) NOT NULL auto_increment,
  `methodid` int(10) unsigned NOT NULL default '0',
  `zoneid` int(10) unsigned NOT NULL default '0',
  `modulename` varchar(100) NOT NULL default '',
  `variablename` varchar(100) NOT NULL default '',
  `variableval` text,
  `varvendorid` int unsigned NOT NULL default '0',
  PRIMARY KEY  (`variableid`),
  KEY `modulename` (`modulename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%shipping_zones` (
  `zoneid` int(10) unsigned NOT NULL auto_increment,
  `zonename` varchar(100) NOT NULL default '',
  `zonetype` enum('country','state','zip') default 'country',
  `zonefreeshipping` int(1) NOT NULL default '0',
  `zonefreeshippingtotal` decimal(20,4) NOT NULL default '0.0000',
  `zonehandlingtype` enum('none','global','module') default 'none',
  `zonehandlingfee` decimal(20,4) NOT NULL default '0.0000',
  `zonehandlingseparate` int(1) NOT NULL default '1',
  `zoneenabled` int(1) NOT NULL default '1',
  `zonevendorid` int unsigned NOT NULL default '0',
  `zonedefault` int(1) NOT NULL default '0',
  PRIMARY KEY  (`zoneid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%shipping_zone_locations` (
  `locationid` int(10) unsigned NOT NULL auto_increment,
  `zoneid` int(10) unsigned NOT NULL default '0',
  `locationtype` enum('country','state','zip') default 'country',
  `locationvalueid` int(10) unsigned NOT NULL default '0',
  `locationvalue` varchar(100) NOT NULL default '0',
  `locationcountryid` int(10) unsigned NOT NULL default '0',
  `locationvendorid` int unsigned NOT NULL default '0',
  PRIMARY KEY  (`locationid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `%%PREFIX%%vendors` (
	`vendorid` int unsigned NOT NULL auto_increment,
	`vendorname` varchar(200) NOT NULL default '',
	`vendorfriendlyname` varchar(100) NOT NULL default '',
	`vendorphone` varchar(50) NOT NULL default '',
	`vendorbio` text NOT NULL,
	`vendoraddress` varchar(200) NOT NULL default '',
	`vendorcity` varchar(100) NOT NULL default '',
	`vendorcountry` varchar(100) NOT NULL default '',
	`vendorstate` varchar(100) NOT NULL default '',
	`vendorzip` varchar(20) NOT NULL default '',
	`vendornumsales` int unsigned NOT NULL default '0',
	`vendororderemail` varchar(200) NOT NULL default '',
	`vendorshipping` int(1) NOT NULL default '0',
	`vendoremail` varchar(200) NOT NULL default '',
	`vendoraccesscats` text NULL,
	`vendorlogo` varchar(200) NOT NULL default '',
	`vendorphoto` varchar(200) NOT NULL default '',
	`vendorprofitmargin` decimal(20,4) NOT NULL default '0.00',
	PRIMARY KEY(vendorid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS %%PREFIX%%product_tags(
	 tagid INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	 tagname VARCHAR( 100 ) NOT NULL DEFAULT  '',
	 tagfriendlyname VARCHAR( 100 ) NOT NULL DEFAULT  '',
	 tagcount INT UNSIGNED NOT NULL DEFAULT  '0',
	 PRIMARY KEY ( tagid )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS %%PREFIX%%product_tagassociations(
	 tagassocid INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	 tagid INT UNSIGNED NOT NULL DEFAULT  '0',
	 productid INT UNSIGNED NOT NULL DEFAULT  '0',
	 PRIMARY KEY ( tagassocid )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS %%PREFIX%%gift_wrapping (
	wrapid int unsigned NOT NULL auto_increment,
	wrapname varchar(100) NOT NULL default '',
	wrapprice decimal(20, 4) NOT NULL default '0.00',
	wrapvisible int(1) NOT NULL default '0',
	wrapallowcomments int(1) NOT NULL default '0',
	wrappreview varchar(100) NOT NULL default '',
	PRIMARY KEY(wrapid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS [|PREFIX|]shipments (
	shipmentid int unsigned NOT NULL auto_increment,
	shipcustid int unsigned NOT NULL default '0',
	shipvendorid int unsigned NOT NULL default '0',
	shipdate int(11) NOT NULL default '0',
	shiptrackno varchar(50) NOT NULL default '',
	shipmethod varchar(100) NOT NULL default '',
	shiporderid int unsigned NOT NULL default '0',
	shiporderdate int(11) NOT NULL default '0',
	shipcomments TEXT NULL,
	shipbillfirstname varchar(255) NOT NULL default '',
	shipbilllastname varchar(255) NOT NULL default '',
	shipbillcompany varchar(100) NOT NULL default '',
	shipbillstreet1 varchar(255) NOT NULL default '',
	shipbillstreet2 varchar(255) NOT NULL default '',
	shipbillsuburb varchar(100) NOT NULL default '',
	shipbillstate varchar(50) NOT NULL default '',
	shipbillzip varchar(20) NOT NULL default '',
	shipbillcountry varchar(50) NOT NULL default '',
	shipbillcountrycode varchar(2) NOT NULL default '',
	shipbillcountryid int(11) NOT NULL default '0',
	shipbillstateid int(11) NOT NULL default '0',
	shipbillphone varchar(50) NOT NULL default '',
	shipbillemail varchar(250) NOT NULL default '',
	shipshipfirstname varchar(100) NOT NULL default '',
	shipshiplastname varchar(100) NOT NULL default '',
	shipshipcompany varchar(100) NOT NULL default '',
	shipshipstreet1 varchar(255) NOT NULL default '',
	shipshipstreet2 varchar(255) NOT NULL default '',
	shipshipsuburb varchar(100) NOT NULL default '',
	shipshipstate varchar(50) NOT NULL default '',
	shipshipzip varchar(20) NOT NULL default '',
	shipshipcountry varchar(50) NOT NULL default '',
	shipshipcountrycode varchar(2) NOT NULL default '',
	shipshipcountryid int(11) NOT NULL default '0',
	shipshipstateid int(11) NOT NULL default '0',
	shipshipphone varchar(50) NOT NULL default '',
	shipshipemail varchar(250) NOT NULL default '',
	PRIMARY KEY(shipmentid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS [|PREFIX|]shipment_items (
	itemid int unsigned NOT NULL auto_increment,
	shipid int unsigned NOT NULL default '0',
	itemprodid int unsigned NOT NULL default '0',
	itemordprodid int unsigned NOT NULL default '0',
	itemprodsku varchar(250) NOT NULL default '',
	itemprodname varchar(250) NOT NULL default '',
	itemqty int unsigned NOT NULL default '0',
	itemprodoptions text NULL,
	itemprodvariationid int unsigned NOT NULL default '0',
	itemprodeventname VARCHAR(255),
	itemprodeventdate INT(9),
	PRIMARY KEY(itemid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS [|PREFIX|]vendor_payments (
	paymentid int unsigned NOT NULL auto_increment,
	paymentfrom int(11) NOT NULL default '0',
	paymentto int(11) NOT NULL default '0',
	paymentvendorid int unsigned NOT NULL default '0',
	paymentamount decimal(20, 4) NOT NULL default '0.0000',
	paymentforwardbalance decimal(20, 4) NOT NULL default '0.0000',
	paymentdate int(11) NOT NULL default '0',
	paymentdeducted int(1) NOT NULL default '0',
	paymentmethod varchar(100) NOT NULL default '',
	paymentcomments text NULL,
	PRIMARY KEY(paymentid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%product_discounts`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_discounts` (
 `discountid` INT NOT NULL auto_increment,
 `discountprodid` INT NOT NULL default '0',
 `discountquantitymin` INT NOT NULL default '0',
 `discountquantitymax` INT NOT NULL default '0',
 `discounttype` ENUM('price', 'percent', 'fixed') NOT NULL default 'price',
 `discountamount` DECIMAL(20,4) NOT NULL default '0',
 PRIMARY KEY (`discountid`),
 INDEX `i_product_discounts_discountprodid` (`discountprodid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_configurable_fields` (
  `productfieldid` int(11) NOT NULL auto_increment,
  `fieldprodid` int(11) NOT NULL default '0',
  `fieldname` varchar(255) NOT NULL default '',
  `fieldtype` varchar(255) NOT NULL default '',
  `fieldfiletype` varchar(255) NOT NULL default '',
  `fieldfilesize` int(11) NOT NULL default '0',
  `fieldrequired` tinyint(4) NOT NULL default '0',
  `fieldsortorder` int(11) NOT NULL default '1',
  PRIMARY KEY  (`productfieldid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `%%PREFIX%%order_configurable_fields` (
  `orderfieldid` int(11) NOT NULL auto_increment,
  `fieldid` int(11) NOT NULL default '0',
  `orderid` int(11) NOT NULL default '0',
  `ordprodid` int(11) NOT NULL default '0',
  `productid` int(11) NOT NULL default '0',
  `textcontents` text NULL,
  `filename` varchar(255) NOT NULL default '',
  `filetype` varchar(255) NOT NULL default '',
  `originalfilename` varchar(255) NOT NULL default '',
  `fieldname` varchar(255) NOT NULL default '',
  `fieldtype` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`orderfieldid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%export_templates` (
	`exporttemplateid` int(11) unsigned NOT NULL auto_increment,
	`exporttemplatename` varchar(100) NOT NULL,
	`myobassetaccount` varchar(20) NOT NULL,
	`myobincomeaccount` varchar(20) NOT NULL,
	`myobexpenseaccount` varchar(20) NOT NULL,
	`peachtreereceivableaccount` varchar(20) NOT NULL,
	`peachtreeglaccount` varchar(20) NOT NULL,
	`modifyforpeachtree` tinyint(1) unsigned NOT NULL,
	`dateformat` varchar(15) NOT NULL,
	`priceformat` varchar(15) NOT NULL,
	`boolformat` varchar(15) NOT NULL,
	`blankforfalse` tinyint(1) unsigned NOT NULL,
	`vendorid` int(11) unsigned NOT NULL,
	`usedtypes` varchar(63) NOT NULL,
	`builtin` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY  (`exporttemplateid`),
	KEY `exporttemplatename` (`exporttemplatename`),
	KEY `vendorid` (`vendorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%export_template_fields` (
	`exporttemplatefieldid` smallint(5) unsigned NOT NULL auto_increment,
	`exporttemplateid` smallint(5) unsigned NOT NULL,
	`fieldid` varchar(31) NOT NULL,
	`fieldtype` varchar(31) NOT NULL,
	`fieldname` varchar(63) NOT NULL,
	`includeinexport` tinyint(1) unsigned NOT NULL,
	`sortorder` tinyint(3) unsigned NOT NULL,
	PRIMARY KEY  (`exporttemplatefieldid`),
	KEY `exporttemplateid` (`exporttemplateid`,`fieldtype`,`includeinexport`,`sortorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%export_method_settings` (
	`exportmethodid` int(11) unsigned NOT NULL auto_increment,
	`methodname` varchar(15) NOT NULL,
	`exporttemplateid` int(11) unsigned NOT NULL,
	`variablename` varchar(31) NOT NULL,
	`variablevalue` varchar(31) NOT NULL,
	PRIMARY KEY  (`exportmethodid`),
	KEY `methodname` (`methodname`,`exporttemplateid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%discounts` (
	`discountid` int(11) NOT NULL auto_increment,
	`discountname` varchar(100) NOT NULL,
	`discountruletype` varchar(100) NOT NULL,
	`discountmaxuses` int(11) NOT NULL default '0',
	`discountcurrentuses` int(11) NOT NULL default '0',
	`discountexpiry` int(11) NOT NULL default '0',
	`discountenabled` tinyint(4) NOT NULL default '0',
	`sortorder` int(9) NOT NULL,
	`halts` int(1) NOT NULL,
	`configdata` text NOT NULL,
	PRIMARY KEY  (`discountid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%product_videos` (
	`video_id` VARCHAR( 25 ) NOT NULL ,
	`video_product_id` INT( 11 ) UNSIGNED NOT NULL ,
	`video_sort_order` INT( 11 ) UNSIGNED NOT NULL ,
	`video_title` VARCHAR( 255 ) NOT NULL ,
	`video_description` TEXT NOT NULL ,
	`video_length` VARCHAR( 10 ) NOT NULL,
	PRIMARY KEY ( `video_id` , `video_product_id` ),
	KEY ( `video_product_id` , `video_sort_order` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%optimizer_config` (
  `optimizer_id` int(11) NOT NULL auto_increment ,
  `optimizer_type` varchar(255) NOT NULL,
  `optimizer_item_id` int(11) NOT NULL,
  `optimizer_config_date` int(11) NOT NULL,
  `optimizer_conversion_page` varchar(255) NOT NULL,
  `optimizer_conversion_url` varchar(255) NOT NULL,
  `optimizer_control_script` text NOT NULL,
  `optimizer_tracking_script` text NOT NULL,
  `optimizer_conversion_script` text NOT NULL,
  PRIMARY KEY  (`optimizer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

/*instalando alguns modulos e opcoes*/

INSERT INTO `%%PREFIX%%formfields` (`formfieldid`, `formfieldformid`, `formfieldtype`, `formfieldlabel`, `formfielddefaultval`, `formfieldextrainfo`, `formfieldisrequired`, `formfieldisimmutable`, `formfieldprivateid`, `formfieldlastmodified`, `formfieldsort`) VALUES
(1, 1, 'singleline', 'Email', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'EmailAddress', 1271954736, 1),
(2, 1, 'password', 'Senha', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'Password', 1271954736, 2),
(3, 1, 'password', 'Confirmar Senha', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'ConfirmPassword', 1271954736, 3),
(4, 2, 'singleline', 'Nome', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'FirstName', 1271954736, 2),
(5, 2, 'singleline', 'Sobrenome', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'LastName', 1271954736, 3),
(6, 2, 'singleline', 'CPF/CNPJ', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'CompanyName', 1271954736, 4),
(7, 2, 'singleline', 'Telefone', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'Phone', 1271954736, 5),
(8, 2, 'singleline', 'Endereco', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'AddressLine1', 1271954736, 6),
(9, 2, 'singleline', 'Bairro', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 0, 1, 'AddressLine2', 1271954736, 7),
(10, 2, 'singleline', 'Cidade', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'City', 1271954736, 8),
(11, 2, 'singleselect', 'Pais', '', 'a:4:{s:5:"class";s:8:"Field200";s:5:"style";s:0:"";s:12:"chooseprefix";s:24:"--Selecione Pais--";s:7:"options";a:0:{}}', 1, 1, 'Country', 1271954902, 9),
(12, 2, 'selectortext', 'Estado', '', 'a:6:{s:5:"class";s:8:"Field200";s:5:"style";s:0:"";s:12:"chooseprefix";s:21:"--Selecionar Estado--";s:7:"options";a:0:{}s:4:"size";s:0:"";s:9:"maxlength";s:0:"";}', 1, 1, 'State', 1271954923, 10),
(13, 2, 'singleline', 'CEP', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:15:"maxlength";s:0:"";s:9:"class";s:15:"Textbox Field45";s:5:"style";s:11:"width:60px;";}', 1, 1, 'Zip', 1271954736, 1),
(14, 2, 'checkboxselect', 'Salvar Endereco?', '', 'a:3:{s:5:"class";s:0:"";s:5:"style";s:0:"";s:7:"options";a:1:{i:0;s:3:"sim";}}', 0, 1, 'SaveThisAddress', 1271954736, 11),
(15, 2, 'checkboxselect', 'Enviar para o Endereco?', '', 'a:3:{s:5:"class";s:0:"";s:5:"style";s:0:"";s:7:"options";a:1:{i:0;s:3:"sim";}}', 0, 1, 'ShipToAddress', 1271954736, 12),
(16, 3, 'singleline', 'Nome', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'FirstName', 1271954736, 2),
(17, 3, 'singleline', 'Sobrenome', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'LastName', 1271954736, 3),
(18, 3, 'singleline', 'CPF/CNPJ', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'CompanyName', 1271954736, 4),
(19, 3, 'singleline', 'Telefone', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'Phone', 1271954736, 5),
(20, 3, 'singleline', 'Endereco', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'AddressLine1', 1271954736, 6),
(21, 3, 'singleline', 'Bairro', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 0, 1, 'AddressLine2', 1271954736, 7),
(22, 3, 'singleline', 'Cidade', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:9:"maxlength";s:0:"";s:5:"class";s:16:"Textbox Field200";s:5:"style";s:0:"";}', 1, 1, 'City', 1271954736, 8),
(23, 3, 'singleselect', 'Pais', '', 'a:4:{s:5:"class";s:8:"Field200";s:5:"style";s:0:"";s:12:"chooseprefix";s:24:"--Selecionar Pais--";s:7:"options";a:0:{}}', 1, 1, 'Country', 1271954902, 9),
(24, 3, 'selectortext', 'Estado', '', 'a:6:{s:5:"class";s:8:"Field200";s:5:"style";s:0:"";s:12:"chooseprefix";s:21:"--Selecionar Estado--";s:7:"options";a:0:{}s:4:"size";s:0:"";s:9:"maxlength";s:0:"";}', 1, 1, 'State', 1271954923, 10),
(25, 3, 'singleline', 'CEP', '', 'a:5:{s:12:"defaultvalue";s:0:"";s:4:"size";s:0:"";s:15:"maxlength";s:0:"";s:9:"class";s:15:"Textbox Field45";s:5:"style";s:11:"width:60px;";}', 1, 1, 'Zip', 1271954736, 1),
(26, 3, 'checkboxselect', 'Salvar Endereco?', '', 'a:3:{s:5:"class";s:0:"";s:5:"style";s:0:"";s:7:"options";a:1:{i:0;s:3:"sim";}}', 0, 1, 'SaveThisAddress', 1271954736, 11),
(27, 3, 'checkboxselect', 'Enviar para o Endereco?', '', 'a:3:{s:5:"class";s:0:"";s:5:"style";s:0:"";s:7:"options";a:1:{i:0;s:3:"sim";}}', 0, 1, 'ShipToAddress', 1271954736, 12);

INSERT INTO `%%PREFIX%%shipping_methods` (`methodid`, `zoneid`, `methodname`, `methodmodule`, `methodhandlingfee`, `methodenabled`, `methodvendorid`) VALUES
(19, 1, 'Correios', 'shipping_correios', '0.0000', 1, 0);

INSERT INTO `%%PREFIX%%shipping_vars` (`variableid`, `methodid`, `zoneid`, `modulename`, `variablename`, `variableval`, `varvendorid`) VALUES
(213, 19, 1, 'shipping_correios', 'meios', '40010', 0),
(211, 19, 1, 'shipping_correios', 'senha', '', 0),
(212, 19, 1, 'shipping_correios', 'meios', '41106', 0),
(210, 19, 1, 'shipping_correios', 'id', '', 0),
(209, 19, 1, 'shipping_correios', 'displayname', 'Correios', 0),
(208, 19, 1, 'shipping_correios', 'is_setup', '1', 0);

INSERT INTO `%%PREFIX%%module_vars` (`variableid`, `modulename`, `variablename`, `variableval`) VALUES
(NULL, 'addon_parcelas', 'usertwitter', 'google'),
(NULL, 'addon_parcelas', 'twitter', 'nao'),
(NULL, 'addon_parcelas', 'pdf', 'sim'),
(NULL, 'addon_parcelas', 'scrolln', '10'),
(NULL, 'addon_parcelas', 'scroll', 'flash'),
(NULL, 'addon_parcelas', 'descboleto', '0'),
(NULL, 'addon_parcelas', 'rodape1', 'deposito'),
(NULL, 'addon_parcelas', 'tipos', 'deposito'),
(NULL, 'addon_parcelas', 'is_setup', '1'),
(NULL, 'addon_simularfrete', 'tipos', 'sedex'),
(NULL, 'addon_simularfrete', 'tipos', 'pac'),
(NULL, 'addon_simularfrete', 'is_setup', '1'),
(NULL, 'checkout_deposito', 'helptext', 'Banco: Banco do Brasil\r\nAgencia: 64646\r\nNome: John Smith\r\nConta: XXXXXXXXXXXX\r\n\r\nMais Instrucoes e Detalhes.'),
(NULL, 'checkout_dinheiromail', 'is_setup', '1'),
(NULL, 'checkout_deposito', 'desconto', '0'),
(NULL, 'checkout_deposito', 'availablecountries', 'all'),
(NULL, 'checkout_deposito', 'displayname', 'Deposito Bancario'),
(NULL, 'checkout_deposito', 'is_setup', '1');


