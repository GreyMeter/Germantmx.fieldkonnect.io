<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            background: linear-gradient(1deg, #1d6dc3, transparent);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .coming-soon-container {
            background: rgb(98 154 213);
            padding: 40px;
            text-align: center;
            color: white;
            border-radius: 10px;
            font-weight: 900;
            box-shadow: inset 0px 0px 15px 3px #91b8e2;
        }

        .btn-back {
            margin-top: 20px;
            color: #fff;
            background-color: #629ad5;
            border-color: #629ad5;
            box-shadow: inset 0px 0px 15px #bad2ed;
        }
        .btn-back:hover{
            color: #fff;
            font-weight: 700;
            text-shadow: 0px 3px 7px #000;
        }
    </style>
</head>

<body>

    <div class="coming-soon-container">
        <h1 style="text-shadow: 0px 4px 5px #000;">Coming Soon</h1>
        <p>This module is under construction. We'll be here soon with our new awesome module.</p>
        <a href="{{ url()->previous() }}" class="btn btn-back"> << Go Back</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>