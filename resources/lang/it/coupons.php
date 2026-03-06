<?php

return [
    'title' => 'Coupon',
    'subtitle' => 'Gestisci coupon e codici sconto',
    'add_coupon' => 'Aggiungi Coupon',
    'edit_coupon' => 'Modifica Coupon',
    'create_coupon' => 'Crea Coupon',
    'update_coupon' => 'Aggiorna Coupon',
    'coupon_created' => 'Coupon creato con successo.',
    'coupon_updated' => 'Coupon aggiornato con successo.',
    'coupon_deleted' => 'Coupon eliminato con successo.',
    'no_coupons_found' => 'Nessun coupon trovato',
    'delete_confirmation_title' => 'Elimina Coupon',
    'delete_confirmation_text' => 'Sei sicuro di voler eliminare questo coupon?',

    'table' => [
        'code' => 'Codice',
        'discount' => 'Sconto',
        'type' => 'Tipo',
        'active' => 'Attivo',
        'expires_at' => 'Scade il',
        'usage' => 'Utilizzi',
    ],

    'form' => [
        'code' => 'Codice',
        'code_placeholder' => 'Inserisci codice coupon',
        'discount' => 'Sconto',
        'discount_placeholder' => 'Inserisci valore sconto',
        'type' => 'Tipo Sconto',
        'type_placeholder' => 'Seleziona tipo sconto',
        'type_percentage' => 'Percentuale',
        'type_fixed' => 'Importo Fisso',
        'active' => 'Attivo',
        'expires_at' => 'Data Scadenza',
        'max_uses' => 'Utilizzi Massimi',
        'max_uses_placeholder' => 'Inserisci utilizzi massimi (opzionale)',
        'min_order_amount' => 'Importo Minimo Ordine',
        'min_order_amount_placeholder' => 'Inserisci importo minimo (opzionale)',
    ],

    'messages' => [
        'edit_subtitle' => 'Aggiorna i dettagli del coupon',
        'create_subtitle' => 'Aggiungi un nuovo coupon',
    ],
];
