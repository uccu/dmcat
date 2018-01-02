if
ps -ef | grep app.js | grep -v grep
then
ps -ef | grep app.js | grep -v grep | cut -c 9-15 | xargs kill -9
cd /home/app/code/dmcat/ws
nohup node app.js >> work.out 2>&1 &
else
cd /home/app/code/dmcat/ws
nohup node app.js >> work.out 2>&1 &
fi