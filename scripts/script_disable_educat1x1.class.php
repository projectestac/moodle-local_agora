<?php

require_once('agora_script_base.class.php');

class script_disable_educat1x1 extends agora_script_base{

    public $title = 'Disable educat1x1 domain';
    public $info = "Replaces text in the DB to disable educat1x1 domain";
    public $cron = false;
    public $cli = true;
    protected $test = false;

    protected function _execute($params = array(), $execute = true) {
        global $agora;
        global $CFG, $OUTPUT;

        $params = array();
        $params['origintext'] = $agora['server']['marsupial'];
        $params['targettext'] = $agora['server']['server'];

        $success = $this->execute_suboperation('replace_database_text', $params);

        if ($success) {

            $params = array();
            $params['origintext'] = str_replace( 'http://', 'https://', $agora['server']['marsupial']);
            $params['targettext'] = str_replace( 'http://', 'https://', $agora['server']['server']);

            $success = $this->execute_suboperation('replace_database_text', $params);
        }

        return $success;
    }

}
