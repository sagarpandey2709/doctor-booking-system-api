<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Appointment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h3 {
            color: #333333;
        }
        p {
            color: #555555;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            margin-bottom: 10px;
            color: #555555;
        }
        ul li strong {
            color: #333333;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Hello {{ $appointment->patient->name }},</h3>

        <p>This is a reminder that you have an appointment with Dr. {{ $appointment->doctor->name }} in 30 minutes.</p>

        <p><strong>Appointment Details:</strong></p>
        <ul>
            <li><strong>Date & Time:</strong> {{ $appointment->appointment_time }}</li>
            <li><strong>Doctor:</strong> {{ $appointment->doctor->name }}</li>
        </ul>

        <p>Please be prepared for your appointment.</p>

        <p>Thank you!</p>

        <div class="footer">
            <p>&copy; 2024 Your Clinic Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
