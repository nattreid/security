<?php

namespace NAttreid\Security\DI;

use NAttreid\AppManager\AppManager;
use NAttreid\Security\Authenticator;
use NAttreid\Security\AuthorizatorFactory;
use NAttreid\Security\Control\ITryUserFactory;
use NAttreid\Security\Control\TryUser;
use NAttreid\Security\Translator;
use NAttreid\Security\User;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\DI\ServiceCreationException;
use Nette\DI\Statement;
use Nextras\Orm\Model\Model;

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

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$config['langDir'] = Helpers::expand($config['langDir'], $builder->parameters);

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

		$builder->addDefinition($this->prefix('translator'))
			->setClass(Translator::class)
			->setArguments([$config['langDir']]);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('security.user')
			->setFactory(User::class)
			->setClass(User::class);

		try {
			$app = $builder->getByType(AppManager::class);
			$builder->getDefinition($app)
				->addSetup(new Statement('$app->onInvalidateCache[] = function() {?->aclResources->cleanCache();}', ['@' . Model::class]));
		} catch (ServiceCreationException $ex) {

		}
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
