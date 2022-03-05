<div class="row">
    <div class="column">
        <div class="message default text-center">
            <small>Template for action class \App\Controller\Action\Users\MeAction</small>
        </div>
        <div>
            <dl>
                <dt><?= __('Name')?></dt>
                <dd><?= h($user['name'])?></dd>

                <dt><?= __('Birth Date')?></dt>
                <dd><?= h($user['birth_date'])?></dd>

                <dt><?= __('Notes')?></dt>
                <dd><?= h($user['notes'])?></dd>
            </dl>
        </div>
    </div>
</div>
