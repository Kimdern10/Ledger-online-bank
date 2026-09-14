<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => "Nivel 1",
    'tier_2' => "Nivel 2",
    'tier_3' => "Nivel 3",
    'tier_1_desc' => "Inicial — verifica tu identidad para aumentar tu límite",
    'tier_2_desc' => "Identidad verificada — verifica tu dirección para aumentarlo aún más",
    'tier_3_desc' => "Totalmente verificado",

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => "Aprobado",
    'status_rejected' => "Rechazado",
    'status_pending_review' => "Revisión pendiente",

    // Settings hub rows
    'identity_verification' => "Verificación de identidad",
    'address_verification' => "Verificación de domicilio",
    'badge_verified' => "Verificado",
    'badge_pending' => "Pendiente",
    'badge_needs_resubmit' => "Requiere reenvío",
    'badge_not_started' => "No iniciado",

    // Shared buttons
    'back_to_dashboard' => "Volver al panel",
    'back_to_settings' => "Volver a Configuración",
    'message_support' => "Enviar mensaje a soporte",
    'submit_for_review' => "Enviar para revisión",
    'verify_identity' => "Verifica tu identidad",
    'verify_address' => "Verifica tu domicilio",
    'last_submission_rejected' => "Tu último envío no fue aprobado.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => "Enviar dinero se desbloquea cuando se revise tu identificación",
    'kyc_gate_pending_body' => "Ya tenemos tu identificación oficial y tu foto. Nuestro equipo las está revisando, normalmente en un día. Todo lo demás en tu cuenta funciona con normalidad mientras tanto.",
    'kyc_gate_rejected_title' => "Enviar dinero necesita una nueva verificación de identidad",
    'kyc_gate_rejected_body' => "Tu último envío no fue aprobado. Revísalo de nuevo y vuelve a enviarlo. Solo toma un minuto.",
    'kyc_gate_not_started_title' => "Verifica tu identidad para enviar dinero",
    'kyc_gate_not_started_body' => "Sube una identificación oficial y una selfie rápida. Es el último paso para desbloquear tu cuenta por completo. Todo lo demás ya funciona.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "Estás verificado",
    'kyc_approved_body' => "Tu identificación oficial y tu foto fueron revisadas y aprobadas. Enviar dinero está completamente desbloqueado.",
    'kyc_approved_body_dated' => "Tu identificación oficial y tu foto fueron revisadas y aprobadas el :date. Enviar dinero está completamente desbloqueado.",
    'kyc_pending_title' => "Tu identificación está en revisión",
    'kyc_pending_body' => "Enviaste un :type y una foto :time. Nuestro equipo revisa esto manualmente, normalmente en un día. Te avisaremos en cuanto se decida. Todo lo demás en tu cuenta funciona con normalidad mientras tanto; solo Enviar dinero permanece bloqueado hasta entonces.",
    'kyc_resubmit_notice' => "Vuelve a intentarlo abajo con una foto clara y sin editar de tu identificación y una selfie bien iluminada.",
    'kyc_form_title' => "Un último paso",
    'kyc_form_body' => "Sube una foto de una identificación oficial vigente y una selfie tuya. Así confirmamos que realmente eres tú antes de desbloquear tu cuenta por completo. Ningún tercero las ve jamás; solo nuestro propio equipo las revisa manualmente. Puedes seguir usando tu cuenta con normalidad mientras se revisa; solo Enviar dinero espera hasta que se apruebe.",
    'id_type_label' => "Tipo de identificación",
    'select_id_type' => "Selecciona el tipo de identificación",
    'id_photo_label' => "Foto de tu identificación (frente)",
    'selfie_label' => "Una selfie tuya",
    'selfie_hint' => "(bien iluminada, con el rostro claramente visible)",

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "Estás completamente verificado",
    'address_approved_body' => "Tu comprobante de domicilio fue revisado y aprobado. Ahora eres Nivel 3 — tu límite diario es :limit.",
    'address_approved_body_dated' => "Tu comprobante de domicilio fue revisado y aprobado el :date. Ahora eres Nivel 3 — tu límite diario es :limit.",
    'address_pending_title' => "Tu documento está en revisión",
    'address_pending_body' => "Enviaste un :type :time. Nuestro equipo revisa esto manualmente, normalmente en un día. Tu límite diario actual se mantiene en :limit hasta entonces.",
    'address_resubmit_notice' => "Vuelve a intentarlo abajo con un documento claro y reciente.",
    'address_form_title' => "Aumenta tu límite diario",
    'address_form_body' => "Sube un documento reciente que muestre tu nombre y domicilio: una factura de servicios, un estado de cuenta bancario o un contrato de alquiler funcionan. Este es el último paso de verificación: aumenta tu límite diario de Enviar dinero y Retirar de :from a :to. Solo nuestro propio equipo lo revisa, normalmente en un día.",
    'document_type_label' => "Tipo de documento",
    'select_document_type' => "Selecciona el tipo de documento",
    'document_label' => "Documento",
    'document_hint' => "(JPG, PNG o PDF, con fecha de los últimos 3 meses)",

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Licencia de conducir",
    'doc_state_id' => "Identificación estatal",
    'doc_passport' => "Pasaporte estadounidense",
    'doc_other_id' => "Otra identificación oficial",
    'doc_utility_bill' => "Factura de servicios",
    'doc_bank_statement' => "Estado de cuenta bancario",
    'doc_tenancy_agreement' => "Contrato de alquiler",
    'doc_other_address' => "Otro comprobante de domicilio",

    // Controller flash messages
    'kyc_submitted_status' => "Gracias, recibimos tu identificación. Tu cuenta está lista para usarse mientras nuestro equipo la revisa, normalmente en un día. Te avisaremos en cuanto se decida.",
    'address_submitted_status' => "Gracias, recibimos tu documento. Te avisaremos en cuanto se decida, normalmente en un día.",
    'address_verify_identity_first' => "Verifica tu identidad primero: la verificación de domicilio es el siguiente paso después de eso.",
];
