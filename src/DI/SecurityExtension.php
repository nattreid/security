<?php

namespace NAttreid\Security\DI;

use NAttreid\Security\Authenticator,
    NAttreid\Security\AuthorizatorFactory,
    Nette\DI\Statement,
    NAttreid\Security\Control\ITryUserFactory,
    NAttreid\Security\Control\TryUser,
    NAttreid\Security\User,
    NAttreid\Security\Authenticator\MainAuthenticator;

/**
 * Rozsireni prihlasovaci logiky
 *
 * @author Attreid <attreid@gmail.com>
 */
class SecurityExtension extends \Nette\DI\CompilerExtension {

    private $defaults = [
        'defaultNamespace' => NULL,
        'authenticator' => []
    ];

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults, $this->getConfig());

        if (!isset($config['defaultNamespace'])) {
            throw new \Nette\InvalidArgumentException("Missing value 'namespace' for security");
        }

        $authenticator = $builder->addDefinition($this->prefix('authenticator'))
                ->setClass(Authenticator::class);

        $config['authenticator'][$config['defaultNamespace']] = MainAuthenticator::class;
        foreach ($config['authenticator'] as $name => $class) {
            $auth = $builder->addDefinition($this->prefix('authenticators.' . $name))
                    ->setClass($this->getClass($class))
                    ->setAutowired(FALSE);

            $authenticator->addSetup('add', [$name, $auth]);
        }

        $builder->addDefinition($this->prefix('authorizatorFactory'))
                ->setClass(AuthorizatorFactory::class);

        $builder->addDefinition($this->prefix('authorizator'))
                ->setFactory('@' . $this->prefix('authorizatorFactory') . '::create');

        $builder->addDefinition($this->prefix('tryUser'))
                ->setImplement(ITryUserFactory::class)
                ->setFactory(TryUser::class);
    }

    public function beforeCompile() {
        $builder = $this->getContainerBuilder();

        $builder->getDefinition('security.user')
                ->setFactory(User::class)
                ->setClass(User::class);
    }

    /**
     * @param mixed $class
     * @return string
     */
    private function getClass($class) {
        if ($class instanceof Statement) {
            return $class->getEntity();
        } elseif (is_object($class)) {
            return get_class($class);
        } else {
            return $class;
        }
    }

}
