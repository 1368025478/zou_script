<?php

/*
 * var $app  \Inhere\Console\Application
 * can config multiple configure
 */

//测试脚本
$console->registerCommands('App\\Commands\\Test', __DIR__ . '/../Console/Commands/Test');
//推送脚本
$console->registerCommands('App\\Commands\\Push', __DIR__ . '/../Console/Commands/Push');
//微信机器人检验脚本
$console->registerCommands('App\\Commands\\Robot', __DIR__ . '/../Console/Commands/Robot');
//新浪微博登陆脚本
$console->registerCommands('App\\Commands\\Sina', __DIR__ . '/../Console/Commands/Sina');






























