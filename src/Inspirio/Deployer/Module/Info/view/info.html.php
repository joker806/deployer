<?php
	if (isset($data['svnInfo'])) {
		echo $data['action']->renderTemplate('svnInfo', $data['svnInfo']);
	}
?>
