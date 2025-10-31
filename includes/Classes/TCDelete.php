<?php

class TCDelete
{
    public $actions = [], $callbacks = [];
    public $success, $error, $complete;
    public $db;
    public function __construct($data = [])
    {
        global $db;
        $this->db = $db;
        $fn = function () {
            return true;
        };
        $this->success = arr_val($data, "success", $fn);
        $this->error = arr_val($data, "error", $fn);
    }
    // return msg
    private function msg($type, $msgData = [])
    {
        $data = [
            'data' => $msgData
        ];
        $data['success'] = $type === "success" ? true : false;
        $data['error'] = $type === "error" ? true : false;
        return $data;
    }
    // return Error
    public function stop()
    {
        return false;
    }
    // Return susccess
    public function next()
    {
        return true;
    }
    // on action
    public function on($action, $callback)
    {
        $this->callbacks[$action] = $callback;
    }
    // set data
    public function set($action, $table_name = "")
    {
        if (gettype($action) === "array") {
            foreach ($action as $key => $table_name) {
                $this->actions[$key] = $table_name;
            }
            return true;
        }
        $this->actions[$action] = $table_name;
    }
    // Callback
    private function callback($type)
    {
        if ($type === "success") {
            $success = $this->success;
            $success = $success();
            if ($success !== false) echo success("Data Deleted Successfully!");
            return true;
        }
        if ($type === "error") {
            $error = $this->error;
            $error = $error();
            if ($error) echo error();
            return true;
        }
    }
    // Validate delete action
    public function validate($action, $data_id)
    {
        if (isset($this->callbacks[$action])) {
            $data = [
                'action' => $action,
                'id' => $data_id
            ];
            $res = $this->callbacks[$action]($this, $data);
            return $res;
        } else
            return true;
    }
    public function init()
    {
        if (!isset($_POST['deleteData']) || !isset($_POST['action']) || !isset($_POST['target'])) return false;
        $action = _POST('action');
        $data_id = _POST('target');

        if (isset($this->actions[$action])) {
            $table_name = $this->actions[$action];
            $this->delete($data_id, $table_name, $action);
        }
    }
    // Delete Data
    public function delete($data_id, $table_name, $action)
    {
        $res = $this->validate($action, $data_id);
        if ($res) {
            $delete = $this->db->delete($table_name, ['id' => $data_id]);
            if ($delete) return $this->callback('success');
            return $this->callback('error');
        } else {
            $this->callback("error");
        }
    }
}
$_delete = new TCDelete();
