<?php

namespace Messere\PromoAudioScrapper\WebApi;

use DI\Bridge\Slim\App;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Messere\PromoAudioScrapper\Audio\FfmpegAudioExtractor;
use Messere\PromoAudioScrapper\Audio\IAudioExtractor;
use Messere\PromoAudioScrapper\FileStorage\IFileStorage;
use Messere\PromoAudioScrapper\FileStorage\LocalFileStorage;
use Messere\PromoAudioScrapper\Search\IPromoSearch;
use Messere\PromoAudioScrapper\Search\RestApiPromoSearch;
use Messere\PromoAudioScrapper\ShellCommand\ExecCommandExecutor;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\UuidFactory;

/**
 * @codeCoverageIgnore
 */
class SlimApiApplication extends App
{
    public function __construct()
    {
        parent::__construct();

        $this->installErrorHandler();
        $this->configureRoutes();
    }

    protected function configureContainer(ContainerBuilder $builder) : void
    {
        parent::configureContainer($builder);

        // Slim configuration
        $builder->addDefinitions([
            'settings.displayErrorDetails' => true,
        ]);

        // DI definitions
        $builder->addDefinitions([
            IAudioExtractor::class => function () {
                return new FfmpegAudioExtractor(
                    new ExecCommandExecutor()
                );
            },
            IFileStorage::class => function (ContainerInterface $container) {
                return new LocalFileStorage(
                    new UuidFactory(),
                    $container->get('request')
                );
            },
            IPromoSearch::class => function () {
                return new RestApiPromoSearch(
                    new Client()
                );
            },
        ]);
    }

    private function configureRoutes() : void
    {
        $this->get(
            '/api/promo2mp3',
            Promo2Mp3ActionHandler::class
        )->setName('promo2mp3');
    }

    private function installErrorHandler(): void
    {
        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }
            throw new ApiException($message, 0, $severity, $file, $line);
        });
    }
}
