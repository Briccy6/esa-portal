<!DOCTYPE html>
<html>
<head>
    <title>Test Verification</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Test Email Verification</h1>
    
    <div>
        <label>Registration Number:</label>
        <input type="text" id="regNumber" value="ESATOR0012025-2026">
        <button onclick="sendCode()">Send Code</button>
    </div>
    
    <div id="result"></div>
    
    <script>
        async function sendCode() {
            const regNumber = document.getElementById('regNumber').value;
            const resultDiv = document.getElementById('result');
            
            if (!regNumber) {
                resultDiv.innerHTML = 'Please enter a registration number';
                return;
            }
            
            try {
                const response = await fetch('/students/send-verification-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ reg_number: regNumber })
                });
                
                const data = await response.json();
                resultDiv.innerHTML = 'Success: ' + data.message;
            } catch (error) {
                resultDiv.innerHTML = 'Error: ' + error.message;
                console.error('Full error:', error);
            }
        }
    </script>
</body>
</html>
