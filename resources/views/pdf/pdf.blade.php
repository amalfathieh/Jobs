<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>CV</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link
      href="https://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css"
      rel="stylesheet"
      id="bootstrap-css"
    />
    {{-- <link href="{{ asset('/css/style.css') }}" rel="stylesheet" /> --}}
    <style>
        /* heading */

h1 {
    font: bold 100% sans-serif;
    letter-spacing: 0.5em;
    text-align: center;
    text-transform: uppercase;
}

/* table */

table {
    font-size: 75%;
    table-layout: fixed;
    width: 100%;
}
table {
    border-collapse: separate;
    border-spacing: 2px;
}
th,
td {
    border-width: 1px;
    padding: 0.5em;
    position: relative;
    text-align: left;
}
th,
td {
    border-radius: 0.25em;
    border-style: solid;
}
th {
    background: #eee;
    border-color: #bbb;
}
td {
    border-color: #ddd;
}

body {
    box-sizing: border-box;
    height: 11in;
    margin: 0 auto;
    overflow: hidden;
    padding: 0.5in;
    width: 7.5in;
}
body {
    background: #fff;
    border-radius: 1px;
    box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
}

/* header */

header {
    margin: 0 0 3em;
}
header:after {
    clear: both;
    content: "";
    display: table;
}

header h1 {
    background: #e40101;
    border-radius: 0.25em;
    color: #fff;
    margin: 0 0 1em;
    padding: 0.5em 0;
}
header address {
    float: left;
    font-size: 95%;
    font-style: normal;
    line-height: 1.25;
    margin: 0 1em 1em 0;
}
article address.norm h4 {
    font-size: 125%;
    font-weight: bold;
}
article address.norm {
    float: left;
    font-size: 95%;
    font-style: normal;
    font-weight: normal;
    line-height: 1.25;
    margin: 0 1em 1em 0;
}
header address p {
    margin: 0 0 0.25em;
}
header span,
header img {
    display: block;
    float: right;
}
header span {
    margin: 0 0 1em 1em;
    max-height: 25%;
    max-width: 60%;
    position: relative;
}
header img {
    max-height: 100%;
    max-width: 100%;
}
header input {
    cursor: pointer;
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
    height: 100%;
    left: 0;
    opacity: 0;
    position: absolute;
    top: 0;
    width: 100%;
}

/* article */

article,
article address,
table.meta,
table.inventory {
    margin: 0 0 3em;
}
article:after {
    clear: both;
    content: "";
    display: table;
}
article h1 {
    clip: rect(0 0 0 0);
    position: absolute;
}

article address {
    float: left;
    font-size: 125%;
    font-weight: bold;
}

/* table meta & balance */

table.meta,
table.balance {
    float: right;
    width: 36%;
}
table.meta:after,
table.balance:after {
    clear: both;
    content: "";
    display: table;
}

/* table meta */

table.meta th {
    width: 40%;
}
table.meta td {
    width: 60%;
}

/* table items */

table.inventory {
    clear: both;
    width: 100%;
}
table.inventory th:first-child {
    width: 50px;
}
table.inventory th:nth-child(2) {
    width: 300px;
}
table.inventory th {
    font-weight: bold;
    text-align: center;
}

table.inventory td:nth-child(1) {
    width: 26%;
}
table.inventory td:nth-child(2) {
    width: 38%;
}
table.inventory td:nth-child(3) {
    text-align: right;
    width: 12%;
}
table.inventory td:nth-child(4) {
    text-align: right;
    width: 12%;
}
table.inventory td:nth-child(5) {
    text-align: right;
    width: 12%;
}

/* table balance */

table.balance th,
table.balance td {
    width: 50%;
}
table.balance td {
    text-align: right;
}

/* aside */

aside h1 {
    border: none;
    border-width: 0 0 1px;
    margin: 0 0 1em;
}
aside h1 {
    border-color: #999;
    border-bottom-style: solid;
}

table.sign {
    float: left;
    width: 220px;
}
table.sign img {
    width: 100%;
}
table.sign tr td {
    border-color: transparent;
}
@media print {
    * {
        -webkit-print-color-adjust: exact;
    }
    html {
        background: none;
        padding: 0;
    }
    body {
        box-shadow: none;
        margin: 0;
    }
    span:empty {
        display: none;
    }
    .add,
    .cut {
        display: none;
    }
}

@page {
    margin: 0;
}

    </style>
</head>
  <body>
    <div id="invoice">
      <header>
        <h1>CV</h1>
        <address>
          <h2>{{$data['full_name']}}</h2>
          <h2>{{$data['birth_day']}}</h2>
          @foreach ($data['contacts'] as $item)
              <p>{{$item}}</p>
          @endforeach
          <p>{{$data['about']}}</p>
        </address>
        <span><img alt="it" src="{{asset('/images/job_seeker/profilePhoto/1716418250.jpg')}}" width="150" /></span>
      </header>
      <article>
        <!-- <address class="norm"></address> -->
        <address class="norm">
          <h4>Skills</h4>
          <ul>
            @foreach ($data['skills'] as $item)
            <li>{{$item}}</li>
            @endforeach
          </ul>
        </address>

        <address class="norm">
          <h4>Certificates</h4>
          <ul>
            @foreach ($data['certificates'] as $item)
            <li>{{$item}}</li>
            @endforeach
          </ul>
        </address>
          <address class="norm">
            <h4>Languages</h4>
            <ul>
                @foreach ($data['languages'] as $item)
                <li>{{$item}}</li>
                @endforeach
              </ul>
          </address>

          <address class="norm">
            <h4>Projects</h4>
            <ul>
                @foreach ($data['projects'] as $item)
                <li>{{$item}}</li>
                @endforeach
              </ul>
            </address>
        <address class="norm">
          <h4>Experiences</h4>

          <ul>
            @foreach ($data['experiences'] as $item)
            <li>{{$item}}</li>
            @endforeach
          </ul>
        </address>
        {{-- <address class="inventory"></address> --}}
      </article>
      <aside>
        <h1><span>Additional Notes</span></h1>
        <div>
          <p>Created by Jobs and FreeLancing Application.</p>
        </div>
      </aside>
    </div>

    <a href="javascript:void(0)" class="btn-download">Download PDF </a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
    {{-- <script src="{{ asset('/js/jspdf.debug.js') }}"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.min.js" integrity="sha512-AxayzhdQnYdvCNE7c2/X50r/ELviR9bmCutz2i0Yagdo97qf5D1t2w0ep2+ILUqVw/JUuxuRm5bjBqaEnVnwQg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
      const options = {
        margin: 0.5,
        filename: "cv.pdf",
        image: {
          type: "jpeg",
          quality: 500,
        },
        html2canvas: {
          scale: 1,
        },
        jsPDF: {
          unit: "in",
          format: "letter",
          orientation: "portrait",
        },
      };

      $(".btn-download").click(function (e) {
        e.preventDefault();
        const element = document.getElementById("invoice");
        html2pdf().from(element).set(options).save();
      });

      function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
      }
    </script>
  </body>
</html>
