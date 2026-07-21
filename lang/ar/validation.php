<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح.',
    'max' => [
        'string' => 'يجب ألا يزيد :attribute عن :max حرفًا.',
    ],
    'min' => [
        'string' => 'يجب أن يكون :attribute على الأقل :min أحرف.',
    ],
    'unique' => ':attribute مسجل بالفعل.',
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'url' => 'يجب أن يكون :attribute رابطًا صحيحًا.',
    'integer' => 'يجب أن يكون :attribute رقمًا صحيحًا.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'in' => ':attribute المحدد غير صحيح.',
    'required_with' => 'حقل :attribute مطلوب.',

    'login' => [
        'admin_restricted' => 'يجب على المسؤولين استخدام لوحة التحكم على /admin.',
        'credentials_mismatch' => 'بيانات الاعتماد التي أدخلتها لا تتطابق مع سجلاتنا.',
    ],

    'register' => [
        'name_required' => 'يرجى إدخال اسمك.',
        'name_max' => 'يجب ألا يتجاوز الاسم 255 حرفًا.',
        'email_required' => 'يرجى إدخال بريدك الإلكتروني.',
        'email_valid' => 'يرجى إدخال بريد إلكتروني صحيح.',
        'email_max' => 'يجب ألا يتجاوز البريد الإلكتروني 255 حرفًا.',
        'email_unique' => 'هذا البريد الإلكتروني مسجل بالفعل.',
        'password_required' => 'يرجى إدخال كلمة المرور.',
        'password_min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',
        'password_confirmed' => 'تأكيد كلمة المرور غير متطابق.',
    ],

    'profile' => [
        'wrong_password' => 'كلمة المرور الحالية التي أدخلتها غير صحيحة.',
    ],

    'contact' => [
        'name_required' => 'الرجاء إدخال اسمك.',
        'name_max' => 'يجب ألا يتجاوز الاسم 255 حرفًا.',
        'email_required' => 'الرجاء إدخال بريدك الإلكتروني.',
        'email_email' => 'الرجاء إدخال بريد إلكتروني صحيح.',
        'email_max' => 'يجب ألا يتجاوز البريد الإلكتروني 255 حرفًا.',
        'subject_required' => 'الرجاء إدخال الموضوع.',
        'subject_max' => 'يجب ألا يتجاوز الموضوع 255 حرفًا.',
        'message_required' => 'الرجاء كتابة رسالتك.',
        'message_max' => 'يجب ألا تتجاوز الرسالة 5000 حرف.',
    ],

    'submission' => [
        'name_required' => 'يرجى إدخال اسمك.',
        'email_required' => 'يرجى إدخال بريدك الإلكتروني.',
        'email_invalid' => 'يرجى إدخال بريد إلكتروني صحيح.',
        'trade_name_required' => 'يرجى إدخال الاسم التجاري.',
        'product_type_required' => 'يرجى اختيار نوع المنتج.',
        'product_type_invalid' => 'يرجى اختيار نوع منتج صحيح.',
        'company_required' => 'يرجى إدخال اسم الشركة المصنعة.',
        'dosage_form_required' => 'يرجى إدخال الشكل الصيدلاني.',
        'active_ingredients_required' => 'يرجى إضافة مادة فعالة واحدة على الأقل.',
        'ingredient_name_required' => 'يرجى إدخال اسم المادة الفعالة لكل مكون.',
        'dosages_required' => 'يرجى إضافة إدخال جرعة واحد على الأقل.',
        'dosage_species_required' => 'يرجى إدخال النوع لكل إدخال جرعة.',
        'withdrawal_required' => 'يرجى إضافة إدخال فترة سحب واحد على الأقل.',
        'withdrawal_species_required' => 'يرجى إدخال النوع لكل إدخال فترة سحب.',
        'document_title_required' => 'يرجى إدخال عنوان المستند.',
        'document_url_required' => 'يرجى إدخال رابط المستند.',
        'document_url_invalid' => 'يرجى إدخال رابط صحيح للمستند.',
        'image_url_invalid' => 'يرجى إدخال رابط صحيح للصورة.',
    ],

    'submission_attributes' => [
        'submitted_by_name' => 'الاسم',
        'submitted_by_email' => 'البريد الإلكتروني',
        'submitted_by_phone' => 'رقم الهاتف',
        'trade_name' => 'الاسم التجاري',
        'trade_name_ar' => 'الاسم التجاري (عربي)',
        'product_type' => 'نوع المنتج',
        'company' => 'الشركة',
        'dosage_form' => 'الشكل الصيدلاني',
        'active_ingredients.*.name' => 'اسم المادة الفعالة',
    ],

    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
    ],
];
