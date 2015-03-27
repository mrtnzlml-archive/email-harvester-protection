<?php

namespace App\Presenters;

use Nette;

class HomepagePresenter extends BasePresenter {

	public function renderDefault() {
		$this->template->mail1 = 'my1@email.net';
		$this->template->mail2 = 'my2@email.net';
		$this->template->mail3 = 'my3@email.net';
		$this->template->mail4 = 'my4@email.net';
		$this->template->mail5 = 'my5@email.net';
		$this->template->mail6 = 'my6@email.net';
		$this->template->mail7 = 'my7@email.net';
	}

}
