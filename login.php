<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body><div>

<form method="post" action="index.php" class="login">
    <div class="my-2">
        <label for="username" class="form-label">Username</label>
        <input id="username" name="username" type="text" placeholder="username" class="form-control"/>
    </div>
    <div class="my-2">
        <label for="password" class="form-label">Password</label>
        <input id="password" name="password" type="password" placeholder="password" class="form-control"/>
    </div>
    <div class="my-2">
        <input type="submit" name="login" value="LOGIN" class="btn btn-primary"/>
    </div>
</form>

</div>
</body>
</html>