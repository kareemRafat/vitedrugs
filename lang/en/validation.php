<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute must be a valid email address.',
    'max' => [
        'string' => 'The :attribute must not be greater than :max characters.',
    ],
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'unique' => 'The :attribute has already been taken.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'string' => 'The :attribute must be a string.',
    'url' => 'The :attribute must be a valid URL.',
    'integer' => 'The :attribute must be an integer.',
    'array' => 'The :attribute must be an array.',
    'in' => 'The selected :attribute is invalid.',
    'required_with' => 'The :attribute field is required.',

    'login' => [
        'admin_restricted' => 'Admins must use the admin panel at /admin.',
        'credentials_mismatch' => 'The provided credentials do not match our records.',
    ],

    'register' => [
        'name_required' => 'Please enter your name.',
        'name_max' => 'Name must not exceed 255 characters.',
        'email_required' => 'Please enter your email address.',
        'email_valid' => 'Please enter a valid email address.',
        'email_max' => 'Email must not exceed 255 characters.',
        'email_unique' => 'This email is already registered.',
        'password_required' => 'Please enter a password.',
        'password_min' => 'Password must be at least 8 characters.',
        'password_confirmed' => 'Password confirmation does not match.',
    ],

    'profile' => [
        'wrong_password' => 'The current password you entered is incorrect.',
    ],

    'contact' => [
        'name_required' => 'Please enter your name.',
        'name_max' => 'Name must not exceed 255 characters.',
        'email_required' => 'Please enter your email address.',
        'email_email' => 'Please enter a valid email address.',
        'email_max' => 'Email must not exceed 255 characters.',
        'subject_required' => 'Please enter a subject.',
        'subject_max' => 'Subject must not exceed 255 characters.',
        'message_required' => 'Please write your message.',
        'message_max' => 'Message must not exceed 5000 characters.',
    ],

    'submission' => [
        'name_required' => 'Please enter your name.',
        'email_required' => 'Please enter your email address.',
        'email_invalid' => 'Please enter a valid email address.',
        'trade_name_required' => 'Please enter the trade name.',
        'product_type_required' => 'Please select the product type.',
        'product_type_invalid' => 'Please select a valid product type.',
        'company_required' => 'Please enter the manufacturer or company name.',
        'dosage_form_required' => 'Please enter the dosage form.',
        'active_ingredients_required' => 'Please add at least one active ingredient.',
        'ingredient_name_required' => 'Please enter the ingredient name for each active ingredient.',
        'dosages_required' => 'Please add at least one dosage entry.',
        'dosage_species_required' => 'Please enter the species for each dosage entry.',
        'withdrawal_required' => 'Please add at least one withdrawal period entry.',
        'withdrawal_species_required' => 'Please enter the species for each withdrawal period entry.',
        'document_title_required' => 'Please enter the document title.',
        'document_url_required' => 'Please enter the document URL.',
        'document_url_invalid' => 'Please enter a valid URL for the document.',
        'image_url_invalid' => 'Please enter a valid image URL.',
    ],

    'submission_attributes' => [
        'submitted_by_name' => 'name',
        'submitted_by_email' => 'email',
        'submitted_by_phone' => 'phone',
        'trade_name' => 'trade name',
        'trade_name_ar' => 'trade name (Arabic)',
        'product_type' => 'product type',
        'company' => 'company',
        'dosage_form' => 'dosage form',
        'active_ingredients.*.name' => 'ingredient name',
    ],

    'attributes' => [
        'name' => 'name',
        'email' => 'email address',
        'password' => 'password',
        'current_password' => 'current password',
        'subject' => 'subject',
        'message' => 'message',
    ],
];
