<?php

declare(strict_types=1);

namespace NAttreid\Security;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use NAttreid\Security\Authenticator\Authenticator;
use NAttreid\Security\Model\AclResources\AclResource;
use NAttreid\Security\Model\AclRoles\AclRolesMapper;
use NAttreid\Security\Model\Orm;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\Session;
use Nette\InvalidStateException;
use Nette\Security\AuthenticationException;
use Nette\Security\Authorizator;
use Nette\Security\SimpleIdentity;
use Nette\Security\User as NUser;
use Nette\Security\UserStorage;
use Nette\Utils\Random;
use Nextras\Dbal\Drivers\Exception\UniqueConstraintViolationException;
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

	public function __construct(UserStorage $storage, Model $orm, Session $session, Request $request, Response $response, AuthorizatorFactory $authorizatorFactory, Authenticator $authenticator = null, Authorizator $authorizator = null)
	{
		parent::__construct(null, $authenticator, $authorizator, $storage);
		$this->authenticator = $authenticator;
		$this->orm = $orm;
		$this->session = $session;
		$this->request = $request;
		$this->response = $response;
		$this->authorizatorFactory = $authorizatorFactory;

		$this->authenticatedRole = AclRolesMapper::USER;

		$this->initSession();
//		$this->initIdentity();
	}

	private function initSession(): void
	{
		$session = $this->session->getSection('user');
		$debug = !Debugger::$productionMode;

		// antiBot
		if (!isset($session->isBot) || $debug) {
			$CrawlerDetect = new CrawlerDetect;
			$session->isBot = $CrawlerDetect->isCrawler();
		}

		// mobileDetect
		if (!isset($session->isMobile) || $debug) {
			$detect = new \Mobile_Detect();
			$session->isMobile = $detect->isMobile();
			$session->isTablet = $detect->isTablet();
		}
	}

	private function initIdentity(): void
	{
		if ($this->isLoggedIn() && $this->authenticator !== null) {
			try {
				$identity = $this->authenticator->getIdentity($this->getId());
				if ($identity) {
					$this->setIdentity($identity);
				}
			} catch (AuthenticationException $ex) {
				$this->logout();
			}
		}
	}

	/**
	 * Nastavei identitu
	 * @param SimpleIdentity $identity
	 */
	public function setIdentity(SimpleIdentity $identity): void
	{
		$this->getStorage()->setIdentity($identity);
	}

	/**
	 * Nastavi namespace pro autentizaci
	 * @param string $namespace
	 */
	public function setNamespace(string $namespace): void
	{
		$storage = $this->getStorage();
		if ($storage instanceof UserStorage) {
			$storage->setNamespace($namespace);
		}
//		$this->initIdentity();
	}

	/**
	 * @param string $resource
	 * @param string $privilege
	 * @param string $name
	 * @return bool
	 */
	public function isAllowed($resource = Authorizator::ALL, $privilege = Authorizator::ALL, string $name = null): bool
	{
		$this->getAuthorizator();
		try {
			return parent::isAllowed($resource, $privilege);
		} catch (InvalidStateException $ex) {
			$aclResource = new AclResource;
			$aclResource->resource = $resource;
			$aclResource->name = $name;

			try {
				$this->orm->persistAndFlush($aclResource);
			} catch (UniqueConstraintViolationException $ex) {

			}
			$this->refreshPermissions();
			return parent::isAllowed($resource, $privilege);
		}
	}

	/**
	 * Znovunacte opravneni uzivatele
	 */
	public function refreshPermissions(): void
	{
		$this->authorizator = $this->authorizatorFactory->create();
	}

	/**
	 * Vrati zda je client robot
	 * @return bool
	 */
	public function isBot(): bool
	{
		$session = $this->session->getSection('user');
		return $session->isBot;
	}

	/**
	 * Je klientsky prohlizec mobilni verze?
	 * @param bool $tablet patri do skupiny i tablety
	 * @return bool
	 */
	public function isMobile(bool $tablet = true): bool
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
	public function getUid(): string
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
