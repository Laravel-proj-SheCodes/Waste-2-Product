<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nouvelle Offre de Troc</title>
</head>
<body>
    <h2>Bonjour {{ $postOwner->name }},</h2>

    <p>Une nouvelle offre de troc a été créée sur votre post <strong>"{{ $offre->postDechet->titre }}"</strong>.</p>

    <p><strong>Détails de l'offre :</strong></p>
    <ul>
        <li><strong>Catégorie :</strong> {{ $offre->categorie }}</li>
        <li><strong>Quantité :</strong> {{ $offre->quantite }} {{ $offre->unite_mesure }}</li>
        <li><strong>État :</strong> {{ ucfirst($offre->etat) }}</li>
        <li><strong>Description :</strong> {{ $offre->description }}</li>
        <li><strong>Créée par :</strong> {{ $offre->user->name }} ({{ $offre->user->email }})</li>
    </ul>

    <p>
        Vous pouvez consulter le post ici :
        <a href="{{ url('/waste-posts/' . $offre->post_dechet_id) }}">
            Voir le post
        </a>
    </p>

    <p>Merci,<br>L’équipe TrocPlatform 🌱</p>
</body>
</html>
