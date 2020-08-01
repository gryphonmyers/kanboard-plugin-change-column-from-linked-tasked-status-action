# Kanboard Plugin ChangeColumnFromLinkedTaskStatus

Kanboard Plugin to move tasks between two columns based on the status of that task's linked tasks. This is useful for one very common use case: getting tasks marked as "blocked" to automatically move themselves out of a blocked column and into an active column when all the tasks it has a blocked link with are closed.

Actions:

- Conditionally move task to one column or another based on the status of all its links of a particular type.

Events:

- Task status changed
- Task link changed
- Task link deleted

_At the moment there is no useable event to combine the triggers, therefore the action should be configured multiple times._

Parameter:

- Source Column
- Destination Column
- Criteria: No linked tasks open, no linked tasks closed. 
- Link ID

Plugin for <https://github.com/kanboard/kanboard>

## Author

- [Gryphon Myers](https://github.com/gryphonmyers)
- License MIT

## Installation

- Decompress the archive in the `plugins` folder

or

- Create a folder **plugins/ChangeColumnFromLinkedTaskStatus**
- Copy all files under this directory
