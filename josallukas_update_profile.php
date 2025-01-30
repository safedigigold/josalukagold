<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_dob'])) {
        // Save DOB and redirect with data and dob as GET parameters
        $data = $_POST['data'];
        $dob = $_POST['dob'];
        header("Location: ?data=" . urlencode($data) . "&dob=" . urlencode($dob));
        exit();
    } elseif (isset($_POST['submit_otp'])) {
        // Handle OTP submission
        $data = base64_decode($_POST['data']);
        list($name, $email, $phone) = explode(':', $data);
        $otp = $_POST['otp'];

        // Prepare the data for API request
        $dataArray = [
            'customerRefNo' => $phone,
            'Password' => 'QWE@123',
            'ConfirmPassword' => 'QWE@123',
            'OTP' => $otp
        ];

        $jsonData = json_encode($dataArray);

        $key = "ABC0DEF1GHI2JL3MNO4PQR5STU6VWX7Y";
        $iv = "A9B8G7H6E1T0I2Q1";

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

        $ch = curl_init('https://www.josalukkasdigigold.com/backend/api/User/Customer/ResetPassword/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

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

// Decode data and dob for display
$data = $_GET['data'] ?? '';
$dob = $_GET['dob'] ?? '';
if ($data) {
    $decodedData = base64_decode($data);
    list($name, $email, $phone) = explode(':', $decodedData);
} else {
    $name = $email = $phone = '';
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
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px;
            margin-top: 50px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        input[type="password"] {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
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
            font-size: 16px;
        }

        button:hover {
            background-color: #892023;
        }

        .success-message {
            color: green;
            margin-top: 10px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Your Profile</h2>

        <?php if (empty($dob)): ?>
        <!-- DOB Form -->
        <form method="POST" action="">
            <input type="hidden" name="data" value="<?php echo htmlspecialchars($data); ?>">
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
            <button type="submit" name="submit_dob">Submit</button>
        </form>
        <?php else: ?>
        <!-- OTP Form -->
        <form method="POST" action="">
            <input type="hidden" name="data" value="<?php echo htmlspecialchars($data); ?>">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" readonly>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly>

            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <button type="submit" name="submit_otp">Update Profile</button>
        </form>
        <?php endif; ?>

        <!-- Messages -->
        <?php if (!empty($successMessage)): ?>
        <div class="success-message">
            <?php echo $successMessage; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
        <div class="error-message">
            <?php echo $errorMessage; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
