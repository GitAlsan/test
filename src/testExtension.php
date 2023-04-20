<?php declare(strict_types = 1);

namespace GitAlsan\Test;

use Nette\Application\UI\Presenter;
use Nette\DI\CompilerExtension;
use Nette\Schema\Schema;
use Nette\Schema\Expect;
use Nette\Security\User;
use Nette\Security\UserStorage;

class TestExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'postsPerPage' => Expect::int(),
			'allowComments' => Expect::bool()->default(true),
		]);
	}

	public function loadConfiguration()
	{
		$num = $this->config->postsPerPage;
	}

	public function checkUserRole(User $user, string $role, Presenter $presenter): void
	{
		if (!$user->isLoggedIn()) {
			if ($user->logoutReason === UserStorage::LOGOUT_INACTIVITY) {
				$presenter->flashMessage('Byl jste odhlášen kvůli nečinnosti. Přihlaste se znovu.', 'danger');
			}
			//$presenter->redirect('Sign:in', ['backlink' => $presenter->storeRequest()]);
		}
		elseif (!$user->isAllowed($role, 'view')) {
			$presenter->flashMessage('Nedostatečná práva.', 'success');
			//$presenter->redirect('Sign:wrongRights');
		}
	}
}