echo "Fetching testsite branch changes" 
git fetch origin testsite

echo "Resetting latest testsite changes" 
git reset --hard origin/testsite
echo "Pulled successfully from testsite"
