<?php

declare(strict_types=1);

namespace NAttreid\Security\DI;

use NAttreid\Security\Authenticator\Authenticator;
use NAttreid\Security\Authenticator\UserAuthenticator;
use NAttreid\Security\AuthorizatorFactory;
use NAttreid\Security\Control\ITryUserFactory;
use NAttreid\Security\Control\TryUser;
use NAttreid\Security\Translator;
use NAttreid\Security\User;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\DI\Statement;

/**
 * Rozsireni prihlasovaci logiky
 *
 * @author Attreid <attreid@gmail.com>
 */
class SecurityExtension extends CompilerExtension
{

	private $defaults = [
		'authenticator' => [],
		'langDir' => '%appDir%/lang'
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$config['langDir'] = Helpers::expand($config['langDir'], $builder->parameters);

		$authenticator = $builder->addDefinition($this->prefix('authenticator'))
			->setType(Authenticator::class);

		$authenticators = $config['authenticator'];
		$authenticators[''] = UserAuthenticator::class;
		foreach ($authenticators as $name => $class) {
			$auth = $builder->addDefinition($this->prefix('authenticators' . $name))
				->setType($this->getClass($class))
				->setAutowired(false);

			$authenticator->addSetup('add', [$name, $auth]);
		}

		$builder->addDefinition($this->prefix('authorizatorFactory'))
			->setType(AuthorizatorFactory::class);

		$builder->addDefinition($this->prefix('authorizator'))
			->setFactory('@' . $this->prefix('authorizatorFactory') . '::create');

		$builder->addFactoryDefinition($this->prefix('tryUser'))
			->setImplement(ITryUserFactory::class)
			->getResultDefinition()
			->setFactory(TryUser::class);

		$builder->addDefinition($this->prefix('translator'))
			->setType(Translator::class)
			->setArguments([$config['langDir']]);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('security.user')
			->setFactory(User::class)
			->setType(User::class);
	}

	/**
	 * @param mixed $class
	 * @return string
	 */
	private function getClass($class): string
	{
		if ($class instanceof Statement) {
			return $class->getEntity();
		} elseif (is_object($class)) {
			return get_class($class);
		} else {
			return $class;
		}
	}

}
