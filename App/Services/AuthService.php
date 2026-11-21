<?php
declare(strict_types=1);

namespace Atlas\Services;

use Atlas\Core\Session;
use Atlas\Models\User;
use Atlas\Repositories\UserRepository;
use DomainException;

final class AuthService
{
    private UserRepository $users;
    private ?User $cachedUser = null;
    private bool $userLoaded = false;

    public function __construct(?UserRepository $users = null)
    {
        $this->users = $users ?? new UserRepository();
    }

    public function register(string $fullName, string $email, ?string $phone, string $password): User
    {
        if ($this->users->findByEmail($email) !== null) {
            throw new DomainException('EMAIL_TAKEN');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->users->create($fullName, $email, $phone, $hash, 'customer');

        $user = $this->users->findById($userId);
        if ($user === null) {
            throw new \RuntimeException('Failed to load newly created user.');
        }

        Session::regenerate();
        Session::set('user_id', $user->id);
        Session::set('role', $user->role);
        Session::set('last_activity', time());
        $this->users->touchLastLogin($user->id);

        $this->cachedUser = $user;
        $this->userLoaded = true;

        return $user;
    }

    public function attemptLogin(string $email, string $password, string $requiredRole): ?User
    {
        $user = $this->users->findByEmail($email);
        if ($user === null || !$user->isActive() || $user->role !== $requiredRole) {
            return null;
        }

        $hash = $this->users->getPasswordHash($user->id);
        if ($hash === '' || !password_verify($password, $hash)) {
            return null;
        }

        if (password_needs_rehash($hash, PASSWORD_BCRYPT)) {
            $this->users->updatePassword($user->id, password_hash($password, PASSWORD_BCRYPT));
        }

        Session::regenerate();
        Session::set('user_id', $user->id);
        Session::set('role', $user->role);
        Session::set('last_activity', time());
        $this->users->touchLastLogin($user->id);

        $this->cachedUser = $user;
        $this->userLoaded = true;

        return $user;
    }

    public function logout(): void
    {
        $this->cachedUser = null;
        $this->userLoaded = false;
        Session::destroy();
    }

    public function currentUser(): ?User
    {
        if ($this->userLoaded) {
            return $this->cachedUser;
        }

        $this->userLoaded = true;
        $userId = Session::get('user_id');
        if (!is_int($userId) && !ctype_digit((string) $userId)) {
            return null;
        }

        $user = $this->users->findById((int) $userId);
        if ($user === null || !$user->isActive()) {
            $this->logout();
            return null;
        }

        $this->cachedUser = $user;
        return $this->cachedUser;
    }

    public function check(?string $role = null): bool
    {
        $user = $this->currentUser();
        if ($user === null) {
            return false;
        }

        return $role === null || $user->role === $role;
    }

    public function touchActivity(): void
    {
        Session::set('last_activity', time());
    }

    public function isTimedOut(int $idleSeconds): bool
    {
        $last = Session::get('last_activity');
        if (!is_int($last)) {
            return true;
        }

        return (time() - $last) > $idleSeconds;
    }

    public function changePassword(int $userId, string $current, string $new): bool
    {
        $hash = $this->users->getPasswordHash($userId);
        if ($hash === '' || !password_verify($current, $hash)) {
            return false;
        }

        $newHash = password_hash($new, PASSWORD_BCRYPT);
        $this->users->updatePassword($userId, $newHash);
        Session::regenerate();

        return true;
    }
}
