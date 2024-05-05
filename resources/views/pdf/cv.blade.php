<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My CV</title>
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
</head>
<body>

    <div class="container">
        <h1>{{$data['full_name']}}</h1>
        <p>{{$data['about']}}</p>
        <ul>
            @foreach ($data['contacts'] as $item)
            <li>{{$item}}</li>
            @endforeach
        </ul>
        <h2>Skills</h2>
        <ul>
            @foreach ($data['skills'] as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
        <h2>Experience</h2>
        <p>Frontend Developer at XYZ Company (2018 - Present)</p>
        <!-- Add more experience details here -->
    </div>
</body>
</html>
