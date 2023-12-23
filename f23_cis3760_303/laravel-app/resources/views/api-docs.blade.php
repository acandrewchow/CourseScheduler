<!DOCTYPE html>
<html lang="en">
<!-- Head Section -->

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>API Documentation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="manifest" href="{{asset('manifest.json')}}">
    <script src="{{asset('js/swagger-ui.js')}}"></script>
    <!-- <script src="{{asset('js/course-finder.js')}}"></script>
    <script src="{{asset('js/scripts.js')}}"></script> -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3/swagger-ui.css" />
    <link rel="icon" href="{{asset('img/favicon.webp')}}" type="image/webp" />
</head>

<!-- Nav Bar -->
@include('components.navbar')

<!-- API Documentation -->
<body>
    <div id="swagger-ui"></div>
</body>

<!-- Footer Section -->
@include('components/footer')

</html>
