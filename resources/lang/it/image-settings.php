<?php

return [
    'title' => 'Impostazioni Immagini',
    'subtitle' => 'Definisci le dimensioni delle miniature per prodotti, categorie e marchi.',

    'add_image_type' => 'Aggiungi Tipo Immagine',
    'edit_image_type' => 'Modifica Tipo Immagine',
    'create_image_type' => 'Crea Tipo Immagine',
    'update_image_type' => 'Aggiorna Tipo Immagine',

    'no_image_types' => 'Nessun tipo immagine definito',
    'no_image_types_text' => 'Aggiungi tipi immagine per definire le dimensioni delle miniature, o carica quelli predefiniti.',

    'seed_defaults' => 'Carica Predefiniti',
    'seed_defaults_confirm' => 'Verranno aggiunti i tipi immagine predefiniti (cart, small, medium, large, home, category). I tipi esistenti non verranno sovrascritti. Continuare?',
    'defaults_seeded' => 'Tipi immagine predefiniti caricati con successo.',

    'image_type_created' => 'Tipo immagine creato con successo.',
    'image_type_updated' => 'Tipo immagine aggiornato con successo.',
    'image_type_deleted' => 'Tipo immagine eliminato con successo.',

    'delete_confirmation_text' => 'Sei sicuro di voler eliminare questo tipo immagine? Le miniature esistenti di questo tipo non saranno più accessibili.',

    'regenerate_info' => 'Dopo aver aggiunto o modificato le impostazioni immagine, clicca "Rigenera Miniature" per aggiornare le immagini esistenti con le nuove dimensioni.',

    'regenerate_thumbnails' => 'Rigenera Miniature',
    'regenerate_confirm' => 'Verranno rigenerate tutte le miniature per tutte le immagini esistenti. Questa operazione potrebbe richiedere tempo. Continuare?',
    'thumbnails_regenerated' => 'Tutte le miniature sono state rigenerate con successo.',

    'config_saved' => 'Impostazioni immagine salvate con successo.',
    'config_reset' => 'Le impostazioni immagine sono state ripristinate ai valori predefiniti.',

    'table' => [
        'name' => 'Nome',
        'dimensions' => 'Dimensioni',
        'products' => 'Prodotti',
        'categories' => 'Categorie',
        'brands' => 'Marchi',
    ],

    'form' => [
        'name' => 'Nome',
        'name_placeholder' => 'es. cart_default, product_list, email_share',
        'name_description' => 'Usa solo lettere minuscole, numeri e underscore. Viene usato come identificatore di conversione.',
        'name_regex_error' => 'Il nome deve contenere solo lettere minuscole, numeri e underscore.',
        'dimensions' => 'Dimensioni',
        'width' => 'Larghezza',
        'height' => 'Altezza',
        'apply_to' => 'Applica A',
        'apply_to_description' => 'Seleziona per quali entità generare le miniature a queste dimensioni.',
        'products' => 'Prodotti',
        'products_description' => 'Genera per le immagini dei prodotti',
        'categories' => 'Categorie',
        'categories_description' => 'Genera per le immagini delle categorie',
        'brands' => 'Marchi',
        'brands_description' => 'Genera per le immagini dei marchi',
    ],

    'messages' => [
        'create_subtitle' => 'Definisci una nuova dimensione miniatura per le immagini',
        'edit_subtitle' => 'Aggiorna la configurazione delle dimensioni miniatura',
    ],
];
