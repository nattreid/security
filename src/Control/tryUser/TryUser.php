<?php

namespace NAttreid\Security\Control;

use NAttreid\Security\Model\Orm;
use NAttreid\Security\User;
use NAttreid\Utils\Hasher;
use Nette\Application\UI\Control;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\Identity;
use Nextras\Orm\Model\Model;

/**
 * Prihlaseni za jineho uzivatele
 *
 * @author Attreid <attreid@gmail.com>
 */
class TryUser extends Control
{

	/** @var int @persistent */
	public $id;

	/** @var boolean */
	private $enable = false;

	/** @var Identity */
	private $originalIdentity;

	/** @var Orm */
	private $orm;

	/** @var Hasher */
	private $hasher;

	/** @var User */
	private $user;

	/** @var Session */
	private $session;

	/** @var string */
	private $redirect;

	public function __construct($redirect, Model $orm, Hasher $hasher, User $user, Session $session)
	{
		parent::__construct();
		$this->orm = $orm;
		$this->hasher = $hasher;
		$this->user = $user;
		$this->session = $session;
		$this->redirect = $redirect;
	}

	public function __destruct()
	{
		if (!empty($this->originalIdentity)) {
			$this->user->setIdentity($this->originalIdentity);
		}
	}

	/**
	 * @return bool
	 */
	public function isEnable()
	{
		return $this->enable;
	}

	/**
	 * Vrati Session
	 * @return SessionSection
	 */
	private function getSession()
	{
		return $this->session->getSection('security/tryingUserParams');
	}

	public function init()
	{
		if (!empty($this->id) && $this->isAllowed()) {
			$session = $this->getSession();
			$session->setExpiration('20 minutes');
			$hash = $session[$this->id];
			$user = $this->orm->users->getByHashId($hash);
			if ($user) {
				$this->originalIdentity = clone $this->user->getIdentity();
				$this->user->setIdentity($user);
				$this->enable = true;
			}
		}
	}

	/**
	 * Nastavi testovaci ucet a presmeruje
	 * @param int $id
	 * @return boolean pokud uzivatel nema prava k teto metode, vrati false
	 */
	public function set($id)
	{
		if (!$this->isAllowed()) {
			return false;
		}
		$hash = $this->hasher->hash($id);
		$uniqid = uniqid();
		$session = $this->getSession();
		$session->setExpiration('5 minutes');
		$session->$uniqid = $hash;
		$this->id = $uniqid;
		$this->presenter->redirect($this->redirect);
		return true;
	}

	/**
	 * Ma prava pro testovani uzivatelu
	 * @return boolean
	 */
	public function isAllowed()
	{
		return $this->user->isAllowed('security.tryUser', 'view');
	}

	/**
	 * Odhlaseni role
	 */
	public function handleLogoutTryRole()
	{
		$session = $this->getSession();
		if (!empty($this->id)) {
			unset($session[$this->id]);
			$this->id = null;
		}
		$this->presenter->redirect('this');
	}

	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/default.latte');

		if ($this->enable) {
			$template->render();
		}
	}

}

interface ITryUserFactory
{

	/**
	 * @param $redirect
	 * @return TryUser
	 */
	public function create($redirect);
}
