<?php

/**
 * INFO DEVELPER
 */
define("NAME_DEVELOPER", "Rodolfo J.Silva");

/**
 * DATABASE
 */
define("CONF_DB_HOST", "localhost");
define("CONF_DB_USER", "root");
define("CONF_DB_PASS", "1234");
define("CONF_DB_NAME", "upinside");

define("CONF_DB_HOST_TEST", "localhost");
define("CONF_DB_USER_TEST", "root");
define("CONF_DB_PASS_TEST", "1234");
define("CONF_DB_NAME_TEST", "upinside");

/**
 * PROJECT URLs
 */
define("CONF_URL_BASE", "https://www.localhost/quantoPaguei");
define("CONF_URL_BASE_TEST", "http://localhost/quantoPaguei");
define("CONF_URL_ADMIN", CONF_URL_BASE . "/admin");

/**
 * SITE
 */
define("CONF_SITE_NAME", "UpInside");
define("CONF_SITE_LANG", "pt_BR");
define("CONF_SITE_DOMAIN", "upinside.com.br");
define("CONF_SITE_DESC", "Gerencie suas contas com o melhor café");
define("CONF_SITE_ADDR_STREET", "SC 406 - Rod. Drº Antônio Luiz Moura Gonzaga");
define("CONF_SITE_ADDR_NUMBER", "3339");
define("CONF_SITE_ADDR_COMPLEMENT", "Bloco A, Sala 208");
define("CONF_SITE_ADDR_CITY", "Florianópolis");
define("CONF_SITE_ADDR_STATE", "SC");
define("CONF_SITE_ADDR_ZIPCODE", "88048-301");

/**
 * SOCIAL
 */
define("CONF_SOCIAL_TWITTER_CREATOR", "@robsonvleite");
define("CONF_SOCIAL_TWITTER_PUBLISHER", "@robsonvleite");
define("CONF_SOCIAL_FACEBOOK_APP", "626590460695980");
define("CONF_SOCIAL_FACEBOOK_PAGE", "upinside");
define("CONF_SOCIAL_FACEBOOK_AUTHOR", "robsonvleiteoficial");
define("CONF_SOCIAL_GOOGLE_PAGE", "107305124528362639842");
define("CONF_SOCIAL_GOOGLE_AUTHOR", "103958419096641225872");

/**
 * DATES
 */
define("CONF_DATE_BR", "d/m/Y H:i:s");
define("CONF_DATE_APP", "Y-m-d H:i:s");

/**
 * PASSWORD
 */
define("CONF_PASSWD_MIN_LEN", 8);
define("CONF_PASSWD_MAX_LEN", 40);
define("CONF_PASSWD_ALGO", PASSWORD_DEFAULT);
define("CONF_PASSWD_OPTION", ["cost" => 10]);

/**
 * VIEW
 */
define("CONF_VIEW_PATH", __DIR__ . "/../../shared/views");
define("CONF_VIEW_EXT", "php");
define("CONF_VIEW_THEME", "backlog");
define("CONF_VIEW_APP", "app");

/**
 * UPLOAD
 */
define("CONF_UPLOAD_DIR", "../storage");
define("CONF_UPLOAD_IMAGE_DIR", "images");
define("CONF_UPLOAD_FILE_DIR", "files");
define("CONF_UPLOAD_MEDIA_DIR", "medias");

/**
 * IMAGES
 */
define("CONF_IMAGE_CACHE", CONF_UPLOAD_DIR . "/" . CONF_UPLOAD_IMAGE_DIR . "/cache");
define("CONF_IMAGE_SIZE", 2000);
define("CONF_IMAGE_QUALITY", ["jpg" => 75, "png" => 5]);

/**
 * MAIL
 */
//CHAVE EMAIL1 SENBLUE
define("CONF_MAIL_API_KEY", "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");
define("CONF_MAIL_HOST", "smtp-relay.sendinblue.com");
define("CONF_MAIL_PORT", "587");
define("CONF_MAIL_USER", "rodolfo0ti@gmail.com");
define("CONF_MAIL_PASS", "OQT7dLs6YvVBJnyX");
define("CONF_MAIL_SENDER", ["name" => "TI NOS NEGOCIOS", "address" => "financas@tinosnegocios.com.br"]);
define("CONF_MAIL_SUPPORT", "suporte_financas@tinosnegocios.com.br");
define("CONF_MAIL_OPTION_LANG", "br");
define("CONF_MAIL_OPTION_HTML", true);
define("CONF_MAIL_OPTION_AUTH", true);
define("CONF_MAIL_OPTION_SECURE", "tls");
define("CONF_MAIL_OPTION_CHARSET", "utf-8");