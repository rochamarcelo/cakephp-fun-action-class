<?php

namespace App\Controller\Action\Users;

use App\Controller\Action;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

class MeAction extends Action
{
    public function execute(): Response
    {
        $this->set('user', [
            'name' => 'John Done',
            'notes' => 'The best Cake',
            'birth_date' => new FrozenDate('1980-01-01')
        ]);
        return $this->render();
    }

}
