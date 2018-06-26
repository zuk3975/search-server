<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Plugin\Multilanguage\Domain\Middleware;

use Apisearch\Config\ImmutableConfig;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\ConfigureEnvironment;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
use Apisearch\Server\Elastica\ElasticaLanguages;
use Apisearch\Server\Elastica\Repository\ItemElasticaWrapper;
use Elastica\Client;
use Elastica\Type\Mapping;

/**
 * Class ConfigureEnvironmentMiddleware.
 */
class ConfigureEnvironmentMiddleware implements PluginMiddleware
{
    /**
     * @var Client
     *
     * Elastica client
     */
    private $client;

    /**
     * @var ItemElasticaWrapper
     *
     * Item elastica wrapper
     */
    private $itemElasticaWrapper;

    /**
     * @var array
     *
     * Configuration
     */
    private $configuration;

    /**
     * Construct.
     *
     * @param Client              $client
     * @param ItemElasticaWrapper $itemElasticaWrapper
     * @param array               $configuration
     */
    public function __construct(
        Client $client,
        ItemElasticaWrapper $itemElasticaWrapper,
        array $configuration
    ) {
        $this->client = $client;
        $this->itemElasticaWrapper = $itemElasticaWrapper;
        $this->configuration = $configuration;
    }

    /**
     * Execute middleware.
     *
     * @param mixed    $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute(
        $command,
        $next
    ) {
        foreach (ElasticaLanguages::getLanguages() as $language) {
            $templateName = 'apisearch_template_plugin_language_'.$language;

            $this
                ->client
                ->request(
                    '_template/'.$templateName,
                    'PUT',
                    $this->createBody($language)
                );
        }
    }

    /**
     * Create body.
     *
     * @param string $language
     *
     * @return array
     */
    private function createBody(string $language): array
    {
        $immutableConfig = ImmutableConfig::createFromArray([
            'language' => $language,
        ]);
        $configuration = $this
            ->itemElasticaWrapper
            ->getIndexConfiguration(
                $immutableConfig,
                $this->configuration['shards'],
                $this->configuration['replicas']
            );

        $mapping = new Mapping();
        $type = $this
            ->itemElasticaWrapper
            ->getType(
                RepositoryReference::create('~~~', '~~~'),
                $this->itemElasticaWrapper->getItemType()
            );

        $mapping->setType($type);
        $this
            ->itemElasticaWrapper
            ->buildIndexMapping(
                $mapping,
                ImmutableConfig::createFromArray([
                    'language' => $language,
                ])
            );

        return [
            'index_patterns' => [
                "apisearch_item_*_*_plugin_language_$language",
            ],
            'settings' => $configuration,
            'mappings' => $mapping->toArray(),
        ];
    }

    /**
     * Events subscribed namespace.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [ConfigureEnvironment::class];
    }
}
