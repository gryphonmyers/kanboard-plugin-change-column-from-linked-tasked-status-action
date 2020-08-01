<?php

namespace Kanboard\Plugin\ChangeColumnFromLinkedTaskStatus\Action;

use Kanboard\Action\Base;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Plugin\ChangeColumnFromLinkedTaskStatus\Action\ConditionalMoveBaseAction;

class ConditionalMoveTaskLinkDelete extends ConditionalMoveBaseAction
{
    /**
     * Get automatic action description.
     *
     * @return string
     */
    public function getDescription()
    {
        return t('When removing task links, move task from a source column to a destination column if all linked tasks of a specific type meet status requirements');
    }

    /**
     * Get the list of compatible events.
     *
     * @return array
     */
    public function getCompatibleEvents()
    {
        return [
            TaskLinkModel::EVENT_DELETE
        ];
    }

    /**
     * Execute the action (assign the given user).
     *
     * @param array $data Event data dictionary
     * @param string $eventName The name of the event being executed
     *
     * @return bool True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $criteria = $this->getParam('criteria');

        $internal_links = $this->taskLinkModel->getAllGroupedByLabel($data['task_link']['task_id']);
        $links_of_type = $internal_links[$data['task_link']['label']];

        $src_column_id = $this->getParam('src_column_id');
        $dest_column_id = $this->getParam('dest_column_id');

        if (!empty($links_of_type))  {
            foreach ($links_of_type as $link) {
                if ($link['id'] == $data['task_link']['id']) {
                    continue;
                }
                if ($link['is_active'] == $criteria) {
                    if ($data['task']['column_id'] == $dest_column_id) {
                        return $this->taskPositionModel->movePosition(
                            $data['task']['project_id'],
                            $data['task_link']['task_id'],
                            $src_column_id,
                            $data['task']['position'],
                            $data['task']['swimlane_id'],
                            false
                        );
                    }
                    return false;
                }
            } 
        }

        return $this->taskPositionModel->movePosition(
            $data['task']['project_id'],
            $data['task_link']['task_id'],
            $dest_column_id,
            $data['task']['position'],
            $data['task']['swimlane_id'],
            false
        );
    }
}
