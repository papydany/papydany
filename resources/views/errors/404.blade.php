<!DOCTYPE html>
<html>
    <head>
        <title>Page Not found</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }
        </style>
    </head>
    <body>
<!--     RewriteCond %{HTTP_HOST} ^(www\.)?database\.unicalexams\.edu.ng$ [NC]
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://database.unicalexams\.edu.ng%{REQUEST_URI} [R=301,L,NE]

RewriteCond %{HTTP_HOST} ^(www\.)?result\.unicalexams\.edu.ng$ [NC]
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://result.unicalexams\.edu.ng%{REQUEST_URI} [R=301,L,NE]


 RewriteCond %{HTTP_HOST} ^(www\.)?unicalexams\.edu.ng$ [NC]
RewriteCond %{HTTPS} !=on 
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
-->
    
    
    
  

        <div class="container">
            <div class="content">
          <h2>  <a href="{{url('login')}}" class="btn btn-success">Back to home page</a></h2>
                <div class="title"><h3>Opps Page Not Found</h3></div>
            </div>
        </div>
    </body>
</html>
