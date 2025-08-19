<!-- resources/views/emails/contact.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .field {
            margin-bottom: 15px;
        }
        .field label {
            font-weight: bold;
            color: #495057;
            display: block;
            margin-bottom: 5px;
        }
        .field-value {
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        .message-content {
            white-space: pre-wrap;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; color: #007bff;">Nouveau message de contact</h1>
        <p style="margin: 10px 0 0 0; color: #6c757d;">
            Reçu le {{ $contact->created_at->format('d/m/Y à H:i') }}
        </p>
    </div>

    <div class="content">
        <div class="field">
            <label>Nom complet :</label>
            <div class="field-value">{{ $contact->name }}</div>
        </div>

        <div class="field">
            <label>Adresse email :</label>
            <div class="field-value">
                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
            </div>
        </div>

        <div class="field">
            <label>Sujet :</label>
            <div class="field-value">{{ $contact->subject }}</div>
        </div>

        <div class="field">
            <label>Message :</label>
            <div class="message-content">{{ $contact->message }}</div>
        </div>
    </div>

    <div class="footer">
        <p>
            Ce message a été envoyé depuis le formulaire de contact de votre site web.<br>
            Vous pouvez répondre directement à cet email pour contacter {{ $contact->name }}.
        </p>
    </div>
</body>
</html>
