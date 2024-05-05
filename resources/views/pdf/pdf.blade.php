<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/styles.css')}}">
    <title>Your CV</title>
</head>
<body>
    <header>
        <h1>{{$data['full_name']}}</h1>
        <h4>{{$data['about']}}</h4>
        <p>Birth-Day: {{$data['birth_day']}}</p>
        <p>Address: {{$data['location']}}</p>
    </header>
    <section class="certificates">
        <h2>Certificates</h2>
        <ul>
            @foreach ($data['certificates'] as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
    </section>
    <section class="skills">
        <h2>Skills</h2>
        <ul>
            @foreach ($data['skills'] as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
    </section>
    <section class="experience">
        <h2>Experience</h2>
        <ul>
            @foreach ($data['experiences'] as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
    </section>
    <section class="languages">
        <h2>Languages</h2>
        <ul>
            @foreach ($data['languages'] as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
    </section>

    <section class="projects">
        <h2>Projects</h2>
        <ul>
            @foreach ($data['projects'] as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
    </section>

    <section class="contacts">
        <h2>Contacts</h2>
        <ul>
            @foreach ($data['contacts'] as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
    </section>
    <section class="profile">
        <h2>Profile</h2>
        <ul>
            @foreach ($data['profile'] as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
    </section>
    <!-- Add more sections for education, skills, etc. -->
</body>
</html>
