<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContactCategory: string implements HasLabel
{
    case FormularyIntegration = 'formulary_integration';
    case MonographCorrection = 'monograph_correction';
    case InstitutionalLicensing = 'institutional_licensing';
    case Pharmacovigilance = 'pharmacovigilance';
    case GeneralFeedback = 'general_feedback';

    /**
     * Resolved through the translator, so the label follows the active locale.
     */
    public function getLabel(): string
    {
        return __('messages.contact_categories.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::FormularyIntegration => 'info',
            self::MonographCorrection => 'warning',
            self::InstitutionalLicensing => 'violet',
            self::Pharmacovigilance => 'danger',
            self::GeneralFeedback => 'gray',
        };
    }
}
