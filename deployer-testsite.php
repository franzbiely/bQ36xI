<?php
echo "Fetching testsite branch changes" ;
exec("git fetch origin testsite");

echo "Resetting latest testsite changes"; 
exec("git reset --hard origin/testsite");
echo "Pulled successfully from testsite";
