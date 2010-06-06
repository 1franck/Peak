<h3>Tests Unit:</h3>
<ul>
<?php

try {
    $it = new DirectoryIterator(dirname(__FILE__));

    while($it->valid()) {
        if((!in_array($it->getFilename(),array('.','..','index.php','.htaccess','simpletest', 'temps','tests_helpers','classes')))) {
            //patch for wyn app: rewrite url
            $modified_url = (defined('ROOT_URL')) ? ROOT_URL.'/tests/'.$it->getFilename() : $it->getFilename();
            echo '<li><a href="'.$modified_url.'">'.$it->getFilename().'</a></li>';
            
        }
        $it->next();
    }
}
catch(Exception $e) { echo $e->getMessage(); exit(); }
?>
</ul>
        