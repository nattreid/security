<?php

namespace NAttreid\Security\Control;

use Nextras\Orm\Model\Model,
    NAttreid\Security\Model\Orm,
    NAttreid\Utils\Hasher,
    NAttreid\Security\User,
    Nette\Http\Session,
    Nette\Http\SessionSection;

/**
 * Prihlaseni za jineho uzivatele
 *
 * @author Attreid <attreid@gmail.com>
 */
class TryUser extends \Nette\Application\UI\Control {

    /** @var int @persistent */
    public $id;

    /** @var boolean */
    private $view = TRUE;

    /** @var \Nette\Security\Identity */
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

    public function __construct($redirect, Model $orm, Hasher $hasher, User $user, Session $session) {
        $this->orm = $orm;
        $this->hasher = $hasher;
        $this->user = $user;
        $this->session = $session;
        $this->setView(FALSE);
        $this->redirect = $redirect;
    }

    public function __destruct() {
        if (!empty($this->originalIdentity)) {
            $this->user->setIdentity($this->originalIdentity);
        }
    }

    /**
     * Vrati Session
     * @return SessionSection
     */
    private function getSession() {
        return $this->session->getSection('security/tryingUserParams');
    }

    public function init() {
        if (!empty($this->id) && $this->isAllowed()) {
            $session = $this->getSession();
            $session->setExpiration('20 minutes');
            $hash = $session[$this->id];
            $user = $this->orm->users->getByHashId($hash);
            if ($user) {
                $this->originalIdentity = clone $this->user->getIdentity();
                $this->user->setIdentity($user);
                $this->setView();
            }
        }
    }

    /**
     * Nastavi zobrazeni
     * @param boolean $view
     */
    public function setView($view = TRUE) {
        $this->view = $view;
    }

    /**
     * Nastavi testovaci ucet a presmeruje
     * @param int $id
     * @return boolean pokud uzivatel name prava k teto metode, vrati false
     */
    public function set($id) {
        if (!$this->isAllowed()) {
            return FALSE;
        }
        $hash = $this->hasher->hash($id);
        $uniqid = uniqid();
        $session = $this->getSession();
        $session->setExpiration('5 minutes');
        $session->$uniqid = $hash;
        $this->id = $uniqid;
        $this->presenter->redirect($this->redirect);
    }

    /**
     * Ma prava pro testovani uzivatelu
     * @return boolean
     */
    public function isAllowed() {
        return $this->user->isAllowed('security.tryUser', 'view');
    }

    /**
     * Odhlaseni role
     */
    public function handleLogoutTryRole() {
        $session = $this->getSession();
        if (!empty($this->id)) {
            unset($session[$this->id]);
            $this->id = NULL;
        }
        $this->presenter->redirect('this');
    }

    public function render($args = NULL) {
        $template = $this->template;
        $template->setFile(__DIR__ . '/../templates/tryUser.latte');

        $template->view = $this->view;
        $template->args = $args;

        $template->render();
    }

}

interface ITryUserFactory {

    /** @return TryUser */
    public function create($redirect);
}
