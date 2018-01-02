if
ps -ef | grep app.js | grep -v grep
then
ps -ef | grep app.js | grep -v grep | cut -c 9-15 | xargs kill -9
cd /home/app/code/dmcat/ws
nohup node app.js &
else
cd /home/app/code/dmcat/ws
nohup node app.js &
fi