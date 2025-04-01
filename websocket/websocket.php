<?php

require_once './vendor/autoload.php';

use Dotenv\Dotenv;
use OnceTwiceSold\WebSocketServer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

// Load the .env file variables
Dotenv::createImmutable(__DIR__)->load();

// Set up the DI container using ./config/services.php
$container = new ContainerBuilder();
$loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/config'));
$loader->load('services.php');
$container->compile();


// Get the WebSockets service and start
$webSocketServer = $container->get(WebSocketServer::class);
$webSocketServer->start();
