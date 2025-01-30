<?php
if (isset($_GET['data'])) {
    $base64Data = $_GET['data'];
    $decodedData = base64_decode($base64Data);
    list($name, $email, $phone) = explode(':', $decodedData);
} else {
    $name = $email = $phone = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_dob'])) {
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];

        // Prepare the data to be encrypted (phone number)
        $requestData = [
            '_mobileNumber' => $phone
        ];

        $jsonData = json_encode($requestData);

        // AES encryption setup
        $key = "ABC0DEF1GHI2JL3MNO4PQR5STU6VWX7Y"; // Replace with your actual key
        $iv = "A9B8G7H6E1T0I2Q1"; // Replace with your actual IV

        $encryptedData = openssl_encrypt($jsonData, 'AES-256-CBC', $key, 0, $iv);

        // Prepare the request body with the encrypted data
        $requestBody = [
            'RequestBody' => $encryptedData,
            'API_Version' => 'V1',
            'Format' => 'Json',
            'Secured' => 'Yes',
            'Engine' => 'JosAlukkas Professional Security',
            'ECode' => 'JADG@2022',
            'Mode' => 'Strict-mode',
            'ServerTime' => gmdate('Y-m-d\TH:i:s\Z')
        ];

        // Send the POST request to the OTP generation API
        $ch = curl_init('https://www.josalukkasdigigold.com/backend/api/SMS/Server/SendVerificationCode/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: www.josalukkasdigigold.com',
            'Content-Length: ' . strlen(json_encode($requestBody)),
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        // Handle the response from the OTP generation API
        $responseData = json_decode($response, true);

        if ($responseData['status'] == 'success') {
            $otpMessage = "OTP has been sent to your phone number.";
        } else {
            $errorMessage = "Failed to send OTP. Please try again.";
        }
    }

    if (isset($_POST['submit_otp'])) {
        $phone = $_POST['phone'];
        $otp = $_POST['otp'];

        $data = [
            'customerRefNo' => $phone,
            'Password' => 'QWE@123',
            'ConfirmPassword' => 'QWE@123',
            'OTP' => $otp
        ];

        $jsonData = json_encode($data);

        // AES encryption setup for password reset API
        $key = "ABC0DEF1GHI2JL3MNO4PQR5STU6VWX7Y"; // Replace with your actual key
        $iv = "A9B8G7H6E1T0I2Q1"; // Replace with your actual IV

        $encryptedData = openssl_encrypt($jsonData, 'AES-256-CBC', $key, 0, $iv);

        $requestBody = [
            'RequestBody' => $encryptedData,
            'API_Version' => 'V1',
            'Format' => 'Json',
            'Secured' => 'Yes',
            'Engine' => 'JosAlukkas Professional Security',
            'ECode' => 'JADG@2022',
            'Mode' => 'Strict-mode',
            'ServerTime' => gmdate('Y-m-d\TH:i:s\Z')
        ];

        // Send the POST request to the OTP verification API
        $ch = curl_init('https://www.josalukkasdigigold.com/backend/api/User/Customer/ResetPassword/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($responseData['status'] == 'success') {
            $successMessage = "Profile updated successfully!";
        } else {
            $errorMessage = "Incorrect OTP. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #ffe590;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .header {
            background-color: #571613;
            color: white;
            width: 100%;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            height: 50px;
        }

        .container {
            background-color: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="date"], input[type="text"] {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #571613;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .success-message {
            color: green;
            margin-top: 10px;
            display: <?php echo isset($otpMessage) ? 'block' : 'none'; ?>;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            display: <?php echo isset($errorMessage) ? 'block' : 'none'; ?>;
        }

        .footer {
            background-color: #571613;
            color: white;
            width: 100%;
            padding: 20px;
            text-align: center;
            margin-top: auto;
        }

        .otp-section {
            display: <?php echo isset($phone) && isset($dob) ? 'block' : 'none'; ?>;
        }
    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="https://www.josalukkasdigigold.com/assets/images/header/jos-digi-logo.svg" alt="Jos Alukkas Digi Gold Logo">
        <img class="logo" src="https://www.josalukkasdigigold.com/assets/images/header/mmtc-logo.svg" alt="MMTC Logo">
    </div>

    <div class="container">
        <h2>Update Your Profile</h2>
        <p>Please update your profile details.</p>

        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" readonly>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>

            <button type="submit" name="submit_dob">Submit</button>
        </form>

        <div class="otp-section">
            <form method="POST" action="">
                <label for="otp">Enter OTP:</label>
                <input type="text" id="otp" name="otp" required>
                <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                <button type="submit" name="submit_otp">Update Profile</button>
            </form>
        </div>

        <div class="success-message">
            <?php echo $otpMessage ?? ''; ?>
            <?php echo $successMessage ?? ''; ?>
        </div>

        <div class="error-message">
            <?php echo $errorMessage ?? ''; ?>
        </div>
    </div>

    <div class="footer">
        2025 Jos Alukkas Group. All rights reserved. The product/service names listed in this document are marks and/or registered marks of their respective owners and used under license. Unauthorized use strictly prohibited.
    </div>
</body>
</html>
