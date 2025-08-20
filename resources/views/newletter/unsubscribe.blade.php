<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Désinscription réussie</title>
</head>
<body>
    <div style="max-width: 600px; margin: 50px auto; padding: 20px; text-align: center;">
        <h1>Désinscription réussie</h1>
        <p>{{ $user->name ?? $user->firstname ?? 'Vous' }}, vous avez été désinscrit avec succès de notre newsletter.</p>
        <p>Nous espérons vous revoir bientôt !</p>
    </div>
</body>
</html>
