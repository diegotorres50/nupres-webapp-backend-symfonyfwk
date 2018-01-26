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
            ->arrayNode('mysql')
                ->prototype('scalar')->end()
                ->defaultValue(array(
                    'database' => array(
                        'name'  => 'nupres_dev_01',
                        'host'  => 'localhost',
                        'port'  => 3306,
                        'user'  => 'root',
                        'pass'  => '123456'
                    )
                ))
            ->end()

            ->arrayNode('jwt')
                ->prototype('scalar')->end()
                ->defaultValue(array(
                    'secret_key'    =>  'Sdw1s9x8@',
                    'algorithms'    =>  ['HS256'],
                    'iss'           =>  'nupres.com.co',
                    'aud'           =>  'nupres.com.co',
                    'uid'           =>  'nupres.com.co'
                ))
            ->end()

            ->arrayNode('api_key')
                ->prototype('scalar')->end()
                ->defaultValue(array(
                    'authorization' => array(
                        'user'  => 'nupres',
                        'pass'  => '123456'
                    )
                ))
            ->end()

        ->end();

        return $treeBuilder;
    }
}
