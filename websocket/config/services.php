<?php

use OnceTwiceSold\Message\MessageFactory;
use OnceTwiceSold\MessageHandler\MessageHandlerRegistry;
use OnceTwiceSold\Persistence\AuctionRepository;
use OnceTwiceSold\Persistence\ParticipantRepository;
use OnceTwiceSold\WebSocketServer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();
    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('OnceTwiceSold\\MessageHandler\\Handler\\', __DIR__.'/../src/MessageHandler/Handler')
        ->tag('app.message_handler');

    $services
        ->set(MessageHandlerRegistry::class)
        ->arg('$handlers', tagged_iterator('app.message_handler'));

    $services->set(MessageFactory::class);
    $services->set(AuctionRepository::class);
    $services->set(ParticipantRepository::class);

    $services
        ->set(WebSocketServer::class)
        ->arg('$listenHost', $_ENV['WEBSOCKET_HOST'])
        ->arg('$listenPort', (int)$_ENV['WEBSOCKET_PORT'])
        ->public();
};
