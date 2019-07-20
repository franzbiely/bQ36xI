echo "Fetching master branch changes" 
git fetch origin master

echo "Resetting latest master changes" 
git reset --hard origin/master
echo "Pulled successfully from master"
