<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Juego</title>
</head>
<body>
	<h1>Welcome to the Game</h1>
    <script>
        fetch('api.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text().then(text => text ? JSON.parse(text) : {});
            })
            .then(data => {
                console.log(data);
                const message = document.createElement('p');
                message.textContent = `API Response: ${JSON.stringify(data)}`;
                document.body.appendChild(message);
            })
            .catch(error => console.error('Error fetching API:', error));
    </script>
</body>
</html>
