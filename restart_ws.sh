if
ps -ef | grep app.js | grep -v grep
then
ps -ef | grep app.js | grep -v grep | cut -c 9-15 | xargs kill -9
/home/app/code/dmcat/ws
nohup node app.js &
else
/home/app/code/dmcat/ws
nohup node app.js &
fi