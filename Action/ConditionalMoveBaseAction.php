<?php

namespace Kanboard\Plugin\ChangeColumnFromLinkedTaskStatus\Action;

use Kanboard\Action\Base;
use Kanboard\Model\TaskLinkModel;

abstract class ConditionalMoveBaseAction extends Base
{

    /**
     * Get the required parameter for the action (defined by the user).
     *
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return [
            'src_column_id' => t('Source column'),
            'dest_column_id' => t('Destination column'),
            'link_id' => t('Link type'),
            'criteria' => [
                'No linked tasks closed',
                'No linked tasks open'
            ]
        ];
    }

    /**
     * Get the required parameter for the event.
     *
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return [
            'task_link' => [
                'task_id',
                'link_id'
            ],
            'task' => [
                'project_id',
                'position',
                'swimlane_id',
                'column_id'
            ]
        ];
    }

    /**
     * Check if the event data meet the action condition.
     *
     * @param array $data Event data dictionary
     *
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
        return ($data['task_link']['link_id'] == $this->getParam('link_id')) && 
            (
                $data['task']['column_id'] == $this->getParam('src_column_id') ||
                $data['task']['column_id'] == $this->getParam('dest_column_id')
            );
    }
}
