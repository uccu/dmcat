let primus = Primus.connect('http://127.0.0.1:8080/', {
    reconnect: {
        max: Infinity, min: 500, retries: 10
    }
})

primus.on('open', function open() {
    console.log('Connection is alive and kicking');
})

primus.on('data', function message(data) {
    console.warn('Received a new message from the server:');
    console.log(data);
})

primus.on('error', function error(err) {
    console.error('Something horrible has happened:', err.stack);
})