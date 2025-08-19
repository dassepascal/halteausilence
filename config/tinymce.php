<?php

return [
	'config' => [
		'language'       => env('APP_TINYMCE_LOCALE', 'en_US'),
		'plugins'        => 'fullscreen',
		// Ajout de "bold" et "formatselect" pour les titres
		'toolbar'        => 'forecolor backcolor | undo redo | bold | formatselect | fontfamily fontsize | alignleft aligncenter alignright alignjustify | bullist numlist | copy cut paste pastetext | hr | link image quicktable | fullscreen',
		'toolbar_sticky' => true,
		'min_height'     => 50,
		'license_key'    => 'gpl',
		'valid_elements' => '*[*]',
		// Optionnel : définir les formats disponibles pour formatselect
		'block_formats'  => 'Titre 1=h1;Titre 2=h2;Titre 3=h3;Paragraphe=p',
	],
];
