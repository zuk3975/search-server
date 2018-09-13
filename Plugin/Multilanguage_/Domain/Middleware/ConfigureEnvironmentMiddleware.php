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

use Apisearch\Config\Config;
use Apisearch\Plugin\Elastica\Domain\ElasticaLanguages;
use Apisearch\Plugin\Elastica\Domain\ItemElasticaWrapper;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\ConfigureEnvironment;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
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
        $this
            ->client
            ->request(
                '_template/apisearch_template_plugin_language_xx',
                'PUT',
                $this->createBody('xx')
            );

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
        $config = Config::createFromArray([
            'language' => 'xx' != $language
                ? $language
                : null,
        ]);
        $configuration = $this
            ->itemElasticaWrapper
            ->getIndexConfiguration(
                $config,
                $this->configuration['shards'],
                $this->configuration['replicas']
            );

        $mapping = new Mapping();
        $type = $this
            ->itemElasticaWrapper
            ->getType(
                RepositoryReference::create(),
                $this->itemElasticaWrapper->getItemType()
            );

        $mapping->setType($type);
        $this
            ->itemElasticaWrapper
            ->buildIndexMapping(
                $mapping,
                $config
            );

        return [
            'index_patterns' => [
                "apisearch_item_*_*-plugin-language-$language",
            ],
            'settings' => $configuration,
            'mappings' => $mapping->toArray(),
        ];
    }

    /**
     * Events subscribed namespace. Can refer to specific class namespace, any
     * parent class or any interface.
     *
     * By returning an empty array, means coupled to all.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [ConfigureEnvironment::class];
    }
}
