<?php
// index.php
require __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to BitKode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background: #fff;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #3a7bd5;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 1.1rem;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to BitKode</h1>
        <p>Amazing things are coming - keep in touch on https://bitkode.com</p>
       
    </div>
</body>
</html>
