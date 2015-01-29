<?php

class agora_script_base{

	public $title = 'No title';
	public $info = "";
	public $cron = false;
	public $cli = false;
	public $api = true;
	protected $test = true;

	public function params() {
		$params = array();
		return $params;
	}

	function execute_web($action) {
		global $OUTPUT;
		echo $OUTPUT->heading($this->title, 3);
		echo $OUTPUT->box($this->info);


		$params = $this->params();
		$this->form($params);
		if ($action == 'test') {
			echo $OUTPUT->notification('Testing...', 'notifymessage');
			\core\session\manager::write_close();
			try {
				return $this->_execute($params, false);
			} catch (Exception $e) {
				echo $e->getMessage();
				return false;
			}
		} else if ($action == 'execute' && $this->can_be_executed($params)) {
			echo $OUTPUT->notification('Executing!!', 'notifymessage');
			\core\session\manager::write_close();
			try {
				return $this->_execute($params);
			} catch (Exception $e) {
				echo $e->getMessage();
				return false;
			}
		}
		return false;
	}

	function execute_cli() {
		if (!$this->cli) {
			mtrace('Script not enabled for cli execution');
			return false;
		}

		$starttime = microtime();

		mtrace($this->title);

		list($params, $unrecognized) = cli_get_params($this->params());
		try {
			$return  = $this->_execute($params);
		} catch (Exception $e) {
			echo $e->getMessage();
			$return = false;
		}


		$difftime = microtime_diff($starttime, microtime());
		mtrace("\n"."Execution took ".$difftime." seconds");
		return $return;
	}

	function execute($params) {
		if ($this->can_be_executed($params)) {
			try {
				return $this->_execute($params);
			} catch (Exception $e) {
				echo $e->getMessage();
				return false;
			}
		}
		return false;
	}

	protected function _execute($params = array(), $execute = true) {
		return false;
	}

	function form($params) {
		echo '<form class="mform" method="post" action="?script='.get_class($this).'">';
		$more_params = $this->_form($params);
		foreach ($more_params as $paramname => $value) {
			echo '<div class="fitem fitem_ftext ">';
			echo '<div class="fitemtitle"><label for="'.$paramname.'">'.$paramname.'</label></div>';
			echo '<div class="felement ftext"><input name="'.$paramname.'" id="'.$paramname.'" value="'.$value.'"/></div>';
			echo '</div>';
		}
		echo '<div id="fgroup_id_buttonar" class="fitem fitem_actionbuttons fitem_fgroup"><div class="felement fgroup">';
		echo '<input type="submit" name="action" value="execute" id="id_submitbutton">';
		if ($this->test) {
			echo '<input type="submit" name="action" value="test">';
		}
		echo '</div></div>';
		echo '</form>';
	}

	protected function _form($params) {
		return $params;
	}

	/**
	 * Function to be executed when cron runs
	 */
	function cron() {
		if ($this->cron && $this->can_be_executed()) {
			mtrace('Script: '.$this->title);

			$success = $this->_cron();

			if ($success) {
				mtrace('Done');
			} else {
				mtrace('Failed');
			}

			return $success;
		}
		return true;
	}

	protected function _cron() {
		return true;
	}

	protected function can_be_executed($params = array()) {
		return true;
	}

	function is_visible() {
		global $USER;
		if (is_agora() && !is_xtecadmin()) {
			return false;
        }
        if (!is_siteadmin()) {
            return false;
        }
        return true;
	}

}