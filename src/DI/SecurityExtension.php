<?php

namespace NAttreid\Security\DI;

use NAttreid\Security\Authenticator;
use NAttreid\Security\AuthorizatorFactory;
use NAttreid\Security\Control\ITryUserFactory;
use NAttreid\Security\Control\TryUser;
use NAttreid\Security\User;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;

/**
 * Rozsireni prihlasovaci logiky
 *
 * @author Attreid <attreid@gmail.com>
 */
class SecurityExtension extends CompilerExtension
{

	private $defaults = [
		'authenticator' => []
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$authenticator = $builder->addDefinition($this->prefix('authenticator'))
			->setClass(Authenticator::class);

		foreach ($config['authenticator'] as $name => $class) {
			$auth = $builder->addDefinition($this->prefix('authenticators.' . $name))
				->setClass($this->getClass($class))
				->setAutowired(false);

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

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('security.user')
			->setFactory(User::class)
			->setClass(User::class);
	}

	/**
	 * @param mixed $class
	 * @return string
	 */
	private function getClass($class)
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
