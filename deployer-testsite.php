<?php
echo "Fetching master branch changes" ;
exec("git fetch origin master");

echo "Resetting latest master changes"; 
exec("git reset --hard origin/master");
echo "Pulled successfully from master";
