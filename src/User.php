<?php

namespace NAttreid\Security;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use NAttreid\Security\Model\AclResource;
use NAttreid\Security\Model\Orm;
use NAttreid\Security\Model\User as UserEntity;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\Session;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthorizator;
use Nette\Security\Identity;
use Nette\Security\IUserStorage;
use Nette\Security\User as NUser;
use Nette\Utils\Random;
use Nextras\Orm\Model\Model;
use Tracy\Debugger;

class User extends NUser
{

	const
		TRACKING_COOKIE = 'tracker',
		TRACKING_EXPIRE = '10 years';

	/** @var Authenticator */
	private $authenticator;

	/** @var Orm */
	private $orm;

	/** @var Session */
	private $session;

	/** @var Request */
	private $request;

	/** @var Response */
	private $response;

	/** @var AuthorizatorFactory */
	private $authorizatorFactory;

	public function __construct(IUserStorage $storage, Model $orm, Session $session, Request $request, Response $response, AuthorizatorFactory $authorizatorFactory, Authenticator $authenticator = null, IAuthorizator $authorizator = null)
	{
		parent::__construct($storage, $authenticator, $authorizator);
		$this->authenticator = $authenticator;
		$this->orm = $orm;
		$this->session = $session;
		$this->request = $request;
		$this->response = $response;
		$this->authorizatorFactory = $authorizatorFactory;

		// session
		$section = $this->session->getSection('user');
		$debug = !Debugger::$productionMode;
		// antiBot
		if (!isset($section->isBot) || $debug) {
			$CrawlerDetect = new CrawlerDetect;
			$section->isBot = $CrawlerDetect->isCrawler();
		}
		// mobileDetect
		if (!isset($section->isMobile) || $debug) {
			$detect = new \Mobile_Detect();
			$section->isMobile = $detect->isMobile();
			$section->isTablet = $detect->isTablet();
		}

		$this->orm->users->onFlush[] = function ($persisted, $removed) {
			foreach ($persisted as $user) {
				/* @var $user UserEntity */
				$this->orm->users->invalidateIdentity($user->id);
			}
			foreach ($removed as $user) {
				/* @var $user UserEntity */
				$this->orm->users->invalidateIdentity($user->id);
			}
		};

		$this->init();
	}

	private function init()
	{
		if ($this->isLoggedIn()) {
			try {
				$user = $this->orm->users->getRefreshUser($this->getId());
				if ($user) {
					$this->setIdentity($user);
				}
			} catch (AuthenticationException $ex) {
				$this->logout();
			}
		}
	}

	/**
	 * Nastavei identitu
	 * @param UserEntity|Identity $user
	 */
	public function setIdentity($user)
	{
		if ($user instanceof UserEntity) {
			$roles = $user->getRoles();

			$arr = $user->toArray(UserEntity::TO_ARRAY_RELATIONSHIP_AS_ID);
			unset($arr['password']);

			$identity = new Identity($user->id, $roles, $arr);
		} elseif ($user instanceof Identity) {
			$identity = $user;
		} else {
			throw new InvalidArgumentException;
		}
		$this->getStorage()->setIdentity($identity);
	}

	/**
	 * Nastavi namespace pro autentizaci
	 * @param string $namespace
	 */
	public function setNamespace($namespace)
	{
		$this->getStorage()->setNamespace($namespace);
		$this->init();
	}

	/**
	 * Otestuje aktualnost identity, a popripade ji aktualizuje
	 */
	public function invalidateIdentity()
	{
		$this->orm->users->invalidateIdentity($this->getId());
	}

	public function isAllowed($resource = IAuthorizator::ALL, $privilege = IAuthorizator::ALL)
	{
		try {
			return parent::isAllowed($resource, $privilege);
		} catch (InvalidStateException $ex) {
			$aclResource = new AclResource;
			$aclResource->name = $resource;

			$this->orm->persistAndFlush($aclResource);
			$this->refreshPermissions();
			return parent::isAllowed($resource, $privilege);
		}
	}

	/**
	 * Znovunacte opravneni uzivatele
	 */
	public function refreshPermissions()
	{
		$this->authorizator = $this->authorizatorFactory->create();
	}

	/**
	 * Vrati zda je client robot
	 * @return bool
	 */
	public function isBot()
	{
		$session = $this->session->getSection('user');
		return $session->isBot;
	}

	/**
	 * Je klientsky prohlizec mobilni verze?
	 * @param boolean $tablet patri do skupiny i tablety
	 * @return boolean
	 */
	public function isMobile($tablet = true)
	{
		$session = $this->session->getSection('user');

		if ($tablet) {
			return $session->isMobile;
		} else {
			return $session->isMobile && !$session->isTablet;
		}
	}

	/**
	 * Vrati uzivatelske uid
	 * @return string
	 */
	public function getUid()
	{
		$uid = $this->request->getCookie(self::TRACKING_COOKIE);
		if (empty($uid)) {
			$charList = 'a-f0-9';
			$uid = Random::generate(8, $charList);
			$uid .= "-" . Random::generate(4, $charList);
			$uid .= "-5" . Random::generate(3, $charList);
			$uid .= "-" . Random::generate(4, $charList);
			$uid .= "-" . Random::generate(12, $charList);

			$this->response->setCookie(self::TRACKING_COOKIE, $uid, self::TRACKING_EXPIRE);
		}
		return $uid;
	}

}
