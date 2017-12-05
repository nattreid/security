<?php

declare(strict_types=1);

namespace NAttreid\Security\Control;

use NAttreid\Security\Model\Orm;
use NAttreid\Security\User;
use NAttreid\Utils\Hasher;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\Identity;
use Nextras\Orm\Model\Model;

/**
 * Prihlaseni za jineho uzivatele
 *
 * @property-write string $permission
 *
 * @author Attreid <attreid@gmail.com>
 */
class TryUser extends Control
{

	/** @var int @persistent */
	public $id;

	/** @var bool */
	private $enable = false;

	/** @var Identity */
	private $originalIdentity;

	/** @var Orm */
	private $orm;

	/** @var Hasher */
	private $hasher;

	/** @var User */
	private $user;

	/** @var SessionSection */
	private $session;

	/** @var string */
	private $redirect;

	/** @var string */
	private $permission = 'security.tryUser';

	public function __construct(string $redirect, Model $orm, Hasher $hasher, User $user, Session $session)
	{
		parent::__construct();
		$this->orm = $orm;
		$this->hasher = $hasher;
		$this->user = $user;
		$this->session = $session->getSection('security/tryingUserParams');
		$this->redirect = $redirect;
	}

	public function __destruct()
	{
		if (!empty($this->originalIdentity)) {
			$this->user->setIdentity($this->originalIdentity);
		}
	}

	protected function setPermission(string $value): void
	{
		$this->permission = $value;
	}

	/**
	 * @return bool
	 */
	public function isEnable(): bool
	{
		return $this->enable;
	}

	public function init(): void
	{
		if (!empty($this->id) && $this->isAllowed()) {
			$this->session->setExpiration('20 minutes');
			$hash = $this->session[$this->id];
			$user = $this->orm->users->getByHashId($hash);
			if ($user) {
				$this->originalIdentity = clone $this->user->getIdentity();
				$this->user->setIdentity($user->getIdentity());
				$this->enable = true;
			}
		}
	}

	/**
	 * Nastavi testovaci ucet a presmeruje
	 * @param int $id
	 * @return bool pokud uzivatel nema prava k teto metode, vrati false
	 * @throws AbortException
	 */
	public function set(int $id): bool
	{
		if (!$this->isAllowed()) {
			return false;
		}
		$hash = $this->hasher->hash($id);
		$uniqid = uniqid();
		$this->session->setExpiration('5 minutes');
		$this->session->$uniqid = $hash;
		$this->id = $uniqid;
		$this->presenter->redirect($this->redirect);
		return true;
	}

	/**
	 * Ma prava pro testovani uzivatelu
	 * @return bool
	 */
	public function isAllowed(): bool
	{
		return $this->user->isAllowed($this->permission, 'view');
	}

	/**
	 * Odhlaseni role
	 * @throws AbortException
	 */
	public function handleLogoutTryRole(): void
	{
		if (!empty($this->id)) {
			unset($this->session[$this->id]);
			$this->id = null;
		}
		$this->presenter->redirect('this');
	}

	public function render(): void
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

	public function create(string $redirect): TryUser;
}
