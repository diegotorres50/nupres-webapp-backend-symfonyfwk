<?php

namespace Nupres\Bundle\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nupres_api');
        // TODO: Falta definir la configuracion aqui por ambiente

        // Aqui definimos el arbol de configuracion del bundle
        $rootNode->children()
            ->arrayNode('nupres')
                ->prototype('scalar')->end()
                ->defaultValue(array(
                    'articles_api_request' => array(
                        'url'           => 'https://information-syndication.api.bbc.com',
                        'api_key'       => 'LA9GnXWtAfVgAFnUhW3NjKS5zbp5SFii',
                        'resource'      => '/articles',
                        'get_params'    => array(
                            'mixins'        => 'summary,body,body_images,thumbnail_images,hero_images',
                            'feed'          => 'mundo-all'
                        ),
                        'headers'       => array(
                            'Accept'        => 'application/rss+xml'
                        ),
                        'timeout'       => 10.0,
                        'debug'         => false,
                        'ttl'           => 600,
                        'expire'        => 86400,
                        'expire_error'  => 14400
                    ),
                    'feed_prefixes' => array(
                        'img_id'                => 'IMG-BBC-GUID',
                        'media_content_id'      => 'IMG-BBC-MEDIA-CONTENT',
                        'media_thumbnail_id'    => 'IMG-BBC-MEDIA-THUMBNAIL',
                        'source_id'             => 'BBC-News',
                        'tags'                  => 'BBC',
                        'guid'                  => 'BBC',
                        'img_title'             => 'BBC Mundo: ',
                    ),
                    'feed_content' => array(
                        'related_class'     => 'page-item-link btn_Texto24 enlace page-link',
                        'aditional_tags'    => 'BBC-News',
                        'default_author'    => 'bbcnews',
                        'title_maxlength'   => 200
                    ),
                    'feed_media' => array(
                        'default_alias_feed'        => 'GALERIAS',
                        'default_section'           => 'INTERNACIONAL'
                    )
                ))
            ->end()

            ->arrayNode('nosql_redis')
                ->prototype('scalar')->end()
                ->defaultValue(array(
                    'arguments'         => array(
                        'scheme'            => 'tcp',
                        'host'              => '127.0.0.1',
                        'port'              => 6379,
                        'database'          => 0,
                        'password'          => null,
                        'timeout'           => 5.0,
                        'alias'             => null,
                        'throw_errors'      => true
                    ),
                    'options'           => array(
                        'prefix'  => 'etce:bbcnews:'
                    )
                ))
            ->end()

        ->end();

        return $treeBuilder;
    }
}
