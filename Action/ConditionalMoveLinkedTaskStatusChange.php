<?php

namespace Kanboard\Plugin\ChangeColumnFromLinkedTaskStatus\Action;

use Kanboard\Action\Base;
use Kanboard\Model\TaskModel;

class ConditionalMoveLinkedTaskStatusChange extends Base
{
    /**
     * Get automatic action description.
     *
     * @return string
     */
    public function getDescription()
    {
        return t('When a task\'s status is changed, move all linked tasks whose links of a specific type meet status requirements from a source column to a destination column.');
    }


    /**
     * Get the list of compatible events.
     *
     * @return array
     */
    public function getCompatibleEvents()
    {
        return [
            TaskModel::EVENT_CLOSE,
            TaskModel::EVENT_OPEN
        ];
    }

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
        return array(
            'task_id',
            'task' => [
                'is_active',
                'project_id',
                'position',
                'swimlane_id',
                'column_id'
            ]
        );
    }

    /**
     * Execute the action (assign the given user).
     *
     * @param array $data Event data dictionary
     *
     * @return bool True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $criteria = $this->getParam('criteria');

        $opposite_link_type = $this->linkModel->getById($this->linkModel->getOppositeLinkId($this->getParam('link_id')));
        $link_type = $this->linkModel->getById($this->getParam('link_id'));
        $opposite_links = $this->taskLinkModel->getAllGroupedByLabel($data['task_id']);
        $links_of_opposite_type = $opposite_links[$opposite_link_type['label']];

        $src_column_id = $this->getParam('src_column_id');
        $dest_column_id = $this->getParam('dest_column_id');

        $num_links_moved = 0;

        if (!empty($links_of_opposite_type))  {

            foreach ($links_of_opposite_type as $opposite_link) { 
                if ($opposite_link['project_id'] != $data['task']['project_id']) {
                    continue;
                }           
                $task = $this->taskFinderModel->getById($opposite_link['task_id']);

                $internal_links = $this->taskLinkModel->getAllGroupedByLabel($task['id']);

                $links_of_type = $internal_links[$link_type['label']];

                $failed_test = false;

                if (!empty($links_of_type))  {
                    foreach ($links_of_type as $link) {
                        if ($link['is_active'] == $criteria) {
                            if ($task['column_id'] == $dest_column_id) {
                                $result = $this->taskPositionModel->movePosition(
                                    $task['project_id'],
                                    $task['id'],
                                    $src_column_id,
                                    $task['position'],
                                    $task['swimlane_id'],
                                    false
                                );

                                if ($result) {
                                    $num_links_moved++;
                                }
                            }
                            $failed_test = true;
                        }
                    }
                }

                if (!$failed_test) {
                    $result = $this->taskPositionModel->movePosition(
                        $task['project_id'],
                        $task['id'],
                        $dest_column_id,
                        $task['position'],
                        $task['swimlane_id'],
                        false
                    );
                    if ($result) {
                        $num_links_moved++;
                    }
                }
            }
        }


        return $num_links_moved > 0;
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
        return true;
    }
}
