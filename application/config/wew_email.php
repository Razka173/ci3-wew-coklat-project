<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['smtp_host']	= "ssl://smtp.gmail.com"; //pengaturan smtp
$config['smtp_user']	= "wewcoklat.noreply@gmail.com"; // isi dengan email kamu
$config['smtp_pass']	= "coklatenak"; // isi dengan password kamu
$config['charset'] 		= 'utf-8';
$config['useragent'] 	= 'Codeigniter';
$config['protocol']		= "smtp";
$config['mailtype']		= "html";
$config['smtp_port']	= "465";
$config['smtp_timeout']	= "400";
$config['crlf']			= "\r\n"; 
$config['newline']		= "\r\n"; 
$config['wordwrap'] 	= TRUE;