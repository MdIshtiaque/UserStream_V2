<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time User Data Updates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 50px 0;
            background: #f0f2f5;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        .title {
            text-align: center;
            color: #007bff;
        }
        .user-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .user-card {
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease-in-out;
        }
        .user-card:hover {
            transform: translateY(-5px);
            border-color: #007bff;
        }
        .user-card h5 {
            color: #007bff;
            margin-bottom: 5px;
        }
        .user-card p {
            margin-bottom: 4px;
            font-size: 14px;
            color: #666;
        }
        .user-card small {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="title">Real-Time User Data Updates</h1>
    <div id="data-container" class="user-info">
        <p>No data yet...</p>
    </div>
</div>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;
    var pusher = new Pusher('local', {
        cluster: 'mt1',
        wsHost: '127.0.0.1',
        wsPort: 6001,
        forceTLS: false,
        encrypted: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss']
    });

    var channel = pusher.subscribe('dataset');
    channel.bind('App\\Events\\UpdateDataset', function(data) {
        if (data.type === 'batch') {
            renderUsers(data.data); // Replace existing users with new batch
        } else if (data.type === 'single') {
            prependUser(data.data[0]); // Append new user to existing list
        }
    });

    function renderUsers(users) {
        const container = document.getElementById('data-container');
        container.innerHTML = ''; // Clear existing users
        // Reverse the array to maintain the order consistency
        users.reverse().forEach(user => {
            prependUser(user); // Use prependUser for consistency
        });
    }

    function prependUser(user) {
        const container = document.getElementById('data-container');
        const userElement = document.createElement('div');
        userElement.className = 'user-card';
        userElement.innerHTML = `
        <h5>${user.name}</h5>
        <p><strong>Email:</strong> ${user.email}</p>
        <small>Joined on: ${new Date(user.created_at).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric'
        })}</small>
    `;

        // Check if there are existing user cards, and prepend the new user
        if (container.firstChild) {
            container.insertBefore(userElement, container.firstChild);
        } else {
            container.appendChild(userElement); // If no users are displayed yet
        }
    }
</script>
</body>
</html>
