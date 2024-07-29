<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time User Data Updates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 50px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Real-Time User Data Updates</h1>
    <div class="card">
        <div class="card-body" id="data-container">
            <p>No data yet...</p>
        </div>
    </div>
</div>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    // Enable Pusher logging - remove this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('local', {
        cluster: 'mt1',
        wsHost: '127.0.0.1',
        wsPort: 6001,
        forceTLS: false,
        encrypted: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'] // Only use WebSockets
    });

    var channel = pusher.subscribe('dataset');
    channel.bind('App\\Events\\UpdateDataset', function(data) {
        console.log("Dataset Updated", data.data);
        const users = data.data; // Assuming 'data' is the key that holds user information
        const container = document.getElementById('data-container');
        container.innerHTML = ''; // Clear existing data
        users.forEach(user => {
            const userElement = document.createElement('div');
            userElement.className = 'list-group-item';
            userElement.innerHTML = `
                    <h5 class="mb-1">${user.name}</h5>
                    <p class="mb-1"><strong>Email:</strong> ${user.email}</p>
                    <small>Joined on: ${new Date(user.created_at).toLocaleDateString('en-US', {
                year: 'numeric', month: 'long', day: 'numeric'
            })}</small>
                `;
            container.appendChild(userElement);
        });
    });

    // Handle errors
    pusher.connection.bind('error', function(err) {
        console.log('Error with Pusher connection', err);
    });

    // Check for successful connection
    pusher.connection.bind('connected', function() {
        console.log('Successfully connected!');
    });
</script>
</body>
</html>
