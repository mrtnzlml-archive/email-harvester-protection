<?php

namespace App\Presenters;

use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter {

	protected function createTemplate($class = NULL) {
		$template = parent::createTemplate($class);
		$template->registerHelper('email', 'Minetro\Latte\Helpers\EmailHelper::mailto');
		return $template;
	}

	public function handleGetEmail() {
		if ($this->isAjax()) {
			$hidden = 'protected@email.net';
			$el = Nette\Utils\Html::el('a')->href('mailto:' . $hidden)->setText($hidden);
			$this->payload->emailLink = (string)$el;
			$this->sendPayload();
		}
		$this->redirect('this');
	}

}
