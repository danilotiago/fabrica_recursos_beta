<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>
<body>
<h2>Bem vindo {{$user->name}}!</h2>
<br/>
Seu e-mal cadastrado foi: {{$user->email}} , Por favor, clique no link abaixo para verificar sua conta.
<br/>
    <a href="{{route('verify.user', ['token' => $user->verifyUser->token])}}">Verificar conta</a>
</body>
</html>