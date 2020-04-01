<?php

############################
# Check if file was included
############################

if (!isset($is_included)) require_once "../init.php";

############################
# Get TODOList
############################

// Init managers
$TodoListsManager = new TodoLists($db);
$TasksManager = new Tasks($db);

// Get Todo lists and tasks
$todoLists = $TodoListsManager->getAll($_SESSION["user_id"]);
$tasksList = $TasksManager->getAll($_SESSION["user_id"]);

// Start output buffering
ob_start();

foreach ($todoLists as $todoList) {

    // Get formatted todo list properties
    $list_id = $todoList["list_id"];
    $list_name = htmlspecialchars($todoList["name"]);
    $list_color = strlen($todoList["color"]) != 0 ? htmlspecialchars($todoList["color"]) : "grey";
    $list_tasks = array_keys(array_column($tasksList, 'list_id'), $list_id);

    // Process HTML code of the todo list
    ?>
        <div class="todoList <?= $list_color ?>">
            <div class="tlDeleteContainer" data-list-id="<?= $list_id ?>">
                <i class="fas fa-trash"></i>
            </div>
            <div class="tlHead">
                <h2><?= $list_name ?></h2>
            </div>
            <div class="tlBody">
                <ul>
                    <?php
                        $tasks_limit = 4;
                        $tasks_count = 0;

                        // Add tasks
                        foreach ($list_tasks as $task) {

                            // Prevent to display too many tasks
                            if ($tasks_count == $tasks_limit) break;

                            // Get formatted task properties
                            $task = $tasksList[$task];
                            $task_id = $task["task_id"];
                            $task_name = htmlspecialchars($task["name"]);
                            $task_status = $task["status"] == "1" ? "checked" : "";
                            $HTMLid = "l" . $list_id . "t" . $task_id;

                            echo '<li><input type="checkbox" id="'.$HTMLid.'" '.$task_status.'/><label for="'.$HTMLid.'">'.$task_name.'</label></li>';

                            $tasks_count++;
                        }

                        // "See more" button
                        $see_more_id = "t".$list_id."SeeMore";
                        echo '<a href="./view_todo_list.php?list_id='.$list_id.'" class="seeMore"><li><input type="checkbox" id="'.$see_more_id.'"/><label for="'.$see_more_id.'"><i class="fas fa-search"></i> Voir plus</label></li></a>';
                    ?>
                </ul>
            </div>
        </div>
    <?php
}

?>
    <div id="addTodoList">
        <div>
            <p><i class="fas fa-plus-square"></i> Ajouter une liste</p>
        </div>
    </div>
<?php

// Get the content of output buffering and stop it.
$todoListsHTML = ob_get_contents();
ob_end_clean();

if (!isset($is_included)) echo $todoListsHTML;