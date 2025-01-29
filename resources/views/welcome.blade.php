<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prayer App</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg,rgb(12, 147, 136),rgb(14, 188, 176));
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .navbar-custom {
            background-color: rgba(0, 0, 0, 0.7);
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.3);
        }
        .btn-custom {
            border-radius: 30px;
            background: #fff;
            color:rgb(168, 137, 142);
            padding: 10px 20px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background:rgb(12, 118, 147);
            color: #fff;
        }
        .hero-text {
            text-align: center;
            margin-top: 50px;
        }
        .hero-text h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .hero-text p {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand text-white" href="{{ url('/') }}">
            <strong>Prayer</strong>
        </a>
        <div class="d-flex">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-custom me-2">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-custom me-2">
                        Log In
                    </a>
                   
                @endauth
            @endif
        </div>
    </div>
</nav>

<div class="hero-text">
    <h1>Welcome to Prayer App</h1>
    <p>Your journey with Prayer App .</p>
    <div>
        <a href="{{ route('login') }}" class="btn btn-custom me-2">Get Started</a>
       
    </div>
</div>

<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
