<?php
try {
    echo "Fetching testsite branch changes<br />" ;
    exec("git fetch origin testsite");

    echo "Resetting latest testsite changes<br />"; 
    exec("git reset --hard origin/testsite");
    echo "Pulled successfully from testsite";
}
catch (Exception $e) {
    echo "Deploy Fail. Error : ". $e;
}