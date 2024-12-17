<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI API Request</title>
</head>
<body>
    <h1>Send AI API Request</h1>
    <form id="aiForm">
        <label for="prompt">Enter Prompt:</label>
        <input type="text" id="prompt" name="prompt" placeholder="Explain how AI works" required>
        <button type="submit">Send</button>
    </form>

    <div id="response">
        <h2>Response:</h2>
        <pre id="responseText"></pre>
    </div>

    <script>
        document.getElementById('aiForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const prompt = document.getElementById('prompt').value;

            const apiKey = "AIzaSyDfOJetSni2WRzl1UHv9S0f1zZPoJxJrqk";
            const endpoint = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=${apiKey}`;

            const body = {
                contents: [
                    {
                        parts: [
                            { text: prompt }
                        ]
                    }
                ]
            };

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(body),
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                document.getElementById('responseText').textContent = JSON.stringify(data, null, 2);
            } catch (error) {
                document.getElementById('responseText').textContent = `Error: ${error.message}`;
            }
        });
    </script>
</body>
</html>
