<?php

namespace Kanboard\Plugin\ChangeColumnFromLinkedTaskStatus;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\ChangeColumnFromLinkedTaskStatus\Action\ConditionalMoveLinkedTaskStatusChange;
use Kanboard\Plugin\ChangeColumnFromLinkedTaskStatus\Action\ConditionalMoveTaskLinkChange;
use Kanboard\Plugin\ChangeColumnFromLinkedTaskStatus\Action\ConditionalMoveTaskLinkDelete;

class Plugin extends Base
{
    public function initialize()
    {
        //Actions
        $this->actionManager->register(new ConditionalMoveLinkedTaskStatusChange($this->container));
        $this->actionManager->register(new ConditionalMoveTaskLinkChange($this->container));
        $this->actionManager->register(new ConditionalMoveTaskLinkDelete($this->container));
    }
    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__ . '/Locale');
    }
    public function getPluginName()
    {
        return 'ChangeColumnFromLinkedTaskStatus';
    }
    public function getPluginDescription()
    {
        return t('Automated action to move tasks between columns when the status of linked tasks changes.');
    }
    public function getPluginAuthor()
    {
        return 'Gryphon Myers';
    }
    public function getPluginVersion()
    {
        return '1.0.0';
    }
    public function getPluginHomepage()
    {
        return 'https://github.com/BlueTeck/kanboard_plugin_reorderaction';
    }
    public function getCompatibleVersion()
    {
        return '>=1.2.13';
    }
}
