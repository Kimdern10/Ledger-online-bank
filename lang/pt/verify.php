<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => "Nível 1",
    'tier_2' => "Nível 2",
    'tier_3' => "Nível 3",
    'tier_1_desc' => "Inicial — verifique sua identidade para aumentar seu limite",
    'tier_2_desc' => "Identidade verificada — verifique seu endereço para aumentá-lo ainda mais",
    'tier_3_desc' => "Totalmente verificado",

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => "Aprovado",
    'status_rejected' => "Rejeitado",
    'status_pending_review' => "Revisão pendente",

    // Settings hub rows
    'identity_verification' => "Verificação de identidade",
    'address_verification' => "Verificação de endereço",
    'badge_verified' => "Verificado",
    'badge_pending' => "Pendente",
    'badge_needs_resubmit' => "Requer novo envio",
    'badge_not_started' => "Não iniciado",

    // Shared buttons
    'back_to_dashboard' => "Voltar ao painel",
    'back_to_settings' => "Voltar às Configurações",
    'message_support' => "Enviar mensagem ao suporte",
    'submit_for_review' => "Enviar para revisão",
    'verify_identity' => "Verifique sua identidade",
    'verify_address' => "Verifique seu endereço",
    'last_submission_rejected' => "Seu último envio não foi aprovado.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => "Enviar dinheiro é liberado assim que seu documento for revisado",
    'kyc_gate_pending_body' => "Já recebemos seu documento oficial com foto e sua selfie. Nossa equipe está revisando agora, normalmente em até um dia. Tudo o mais na sua conta funciona normalmente enquanto isso.",
    'kyc_gate_rejected_title' => "Enviar dinheiro precisa de uma nova verificação de identidade",
    'kyc_gate_rejected_body' => "Seu último envio não foi aprovado. Dê uma nova olhada e reenvie. Leva só um minuto.",
    'kyc_gate_not_started_title' => "Verifique sua identidade para enviar dinheiro",
    'kyc_gate_not_started_body' => "Envie um documento oficial com foto e uma selfie rápida. É o último passo para desbloquear totalmente sua conta. Tudo o mais já funciona.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "Você está verificado",
    'kyc_approved_body' => "Seu documento oficial com foto e sua selfie foram revisados e aprovados. Enviar dinheiro está totalmente liberado.",
    'kyc_approved_body_dated' => "Seu documento oficial com foto e sua selfie foram revisados e aprovados em :date. Enviar dinheiro está totalmente liberado.",
    'kyc_pending_title' => "Seu documento está em análise",
    'kyc_pending_body' => "Você enviou um(a) :type e uma foto :time. Nossa equipe revisa isso manualmente, normalmente em até um dia. Avisaremos assim que houver uma decisão. Tudo o mais na sua conta funciona normalmente enquanto isso; apenas Enviar dinheiro permanece bloqueado até lá.",
    'kyc_resubmit_notice' => "Tente novamente abaixo com uma foto nítida e sem edições do seu documento e uma selfie bem iluminada.",
    'kyc_form_title' => "Um último passo",
    'kyc_form_body' => "Envie uma foto de um documento oficial válido e uma selfie sua. É assim que confirmamos que é realmente você antes de liberar totalmente sua conta. Nenhum terceiro vê essas informações; apenas nossa própria equipe as revisa manualmente. Você pode continuar usando sua conta normalmente enquanto isso é revisado; apenas Enviar dinheiro aguarda a aprovação.",
    'id_type_label' => "Tipo de documento",
    'select_id_type' => "Selecione o tipo de documento",
    'id_photo_label' => "Foto do seu documento (frente)",
    'selfie_label' => "Uma selfie sua",
    'selfie_hint' => "(bem iluminada, com o rosto claramente visível)",

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "Você está totalmente verificado",
    'address_approved_body' => "Seu comprovante de endereço foi revisado e aprovado. Agora você está no Nível 3 — seu limite diário é :limit.",
    'address_approved_body_dated' => "Seu comprovante de endereço foi revisado e aprovado em :date. Agora você está no Nível 3 — seu limite diário é :limit.",
    'address_pending_title' => "Seu documento está em análise",
    'address_pending_body' => "Você enviou um(a) :type :time. Nossa equipe revisa isso manualmente, normalmente em até um dia. Seu limite diário atual permanece em :limit até lá.",
    'address_resubmit_notice' => "Tente novamente abaixo com um documento nítido e recente.",
    'address_form_title' => "Aumente seu limite diário",
    'address_form_body' => "Envie um documento recente que mostre seu nome e endereço residencial — uma conta de consumo, um extrato bancário ou um contrato de aluguel servem. Este é o último passo de verificação: ele aumenta seu limite diário de Enviar dinheiro e Sacar de :from para :to. Somente nossa própria equipe revisa, normalmente em até um dia.",
    'document_type_label' => "Tipo de documento",
    'select_document_type' => "Selecione o tipo de documento",
    'document_label' => "Documento",
    'document_hint' => "(JPG, PNG ou PDF, datado dos últimos 3 meses)",

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Carteira de motorista",
    'doc_state_id' => "Identidade emitida pelo estado",
    'doc_passport' => "Passaporte americano",
    'doc_other_id' => "Outro documento oficial de identidade",
    'doc_utility_bill' => "Conta de consumo",
    'doc_bank_statement' => "Extrato bancário",
    'doc_tenancy_agreement' => "Contrato de aluguel",
    'doc_other_address' => "Outro comprovante de endereço",

    // Controller flash messages
    'kyc_submitted_status' => "Obrigado — recebemos seu documento. Sua conta está pronta para uso enquanto nossa equipe o revisa, normalmente em até um dia. Avisaremos assim que houver uma decisão.",
    'address_submitted_status' => "Obrigado — recebemos seu documento. Avisaremos assim que houver uma decisão, normalmente em até um dia.",
    'address_verify_identity_first' => "Verifique sua identidade primeiro — a verificação de endereço é o próximo passo depois disso.",
];
