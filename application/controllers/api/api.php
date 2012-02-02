<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
 */
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Api extends REST_Controller {

    function user_get() {
        $this->load->model('UserModel');
        if (!$this->get('username')) {
            $this->response(NULL, 400);
        }
        $user = $this->UserModel->user_get($this->get('username'));
        if ($user) {
            $this->response($user, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }

    function users_get() {
        $this->load->model('UserModel');
        $users = $this->UserModel->users_get();

        if ($users) {
            $this->response($users, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }

    /* TASK API SECTION ********************************************** */

    function task_get() {
        $this->load->model('TaskModel');

        if (!$this->get('id')) {
            $this->response(NULL, 400);
        }
        $task = $this->TaskModel->task_get($this->get('id'));
        if ($task) {
            $this->response($task, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Task could not be found'), 404);
        }
    }

    function task_post() {
        $this->load->model('TaskModel');

        $this->load->library('Task');
        $task = new Task();

        $task->id = $this->post('taskId');
        $task->name = $this->post('name');
        $task->assignedTo = $this->post('username');
        $task->status = $this->post('status');
        $task->notes = $this->post('notes');
        $task->dateDue = $this->post('dateDue');

        if (!$this->post('taskId')) {
            $this->response(array('error' => 'Apparently you didnt put shit in the taskID, justsayin'), 400);
        }

        $updateTask = $this->TaskModel->task_update($task);
        if ($updateTask) {
            $this->response($updateTask, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Task could not be found'), 404);
        }
    }

    function task_put() {
        $this->load->model('TaskModel');
        $this->load->library('Task');
        $task = new Task();
        $task->name = $this->put('name');
        $task->assignedTo = $this->put('assignedTo');
        $task->notes = $this->put('notes');
        $task->dateDue = $this->put('dateDue');
        $taskResponse = $this->TaskModel->task_put($task);
        $this->response($taskResponse, 200);
    }

    function task_delete() {
        $this->load->model('TaskModel');
        $isDeleted = $this->TaskModel->task_delete($this->delete('id'));
        if ($isDeleted == true) {
            $this->response(array('message' => 'Task ' . $this->get('id') . ' successfully deleted'), 200);
        } else {
            $this->response(array('error' => 'Task could not be found'), 404);
        }
    }

    function tasks_get() {
        $this->load->model('TaskModel');

        if (!$this->get('username')) { // get all non-deleted tasks
            $tasks = $this->TaskModel->tasks_get();
        } else { //get tasks for username
            $tasks = $this->TaskModel->userTasks_get($this->get('username'));
        }

        if ($tasks) {
            $this->response($tasks, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'User could not be found or no tasks available for that user.'), 404);
        }
    }

}