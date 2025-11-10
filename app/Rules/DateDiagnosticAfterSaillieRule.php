<?php

namespace App\Rules;

use App\Models\CycleReproduction;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Règle de validation pour la date de diagnostic de gestation
 *
 * Vérifie que :
 * 1. Au moins une saillie existe pour le cycle
 * 2. La date du diagnostic est postérieure à la date de la première saillie
 */
class DateDiagnosticAfterSaillieRule implements ValidationRule
{
    public function __construct(
        protected mixed $get,
        protected ?CycleReproduction $record = null
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value) {
            return; // Si pas de date, on ne valide pas (champ optionnel)
        }

        // Récupérer les dates de saillies (formulaire ou base de données)
        $datesSaillies = $this->getDatesSaillies();

        // RÈGLE 1 : Au moins une saillie doit exister
        if (empty($datesSaillies)) {
            $fail('Vous devez enregistrer au moins une saillie avant de définir une date de diagnostic.');

            return;
        }

        // RÈGLE 2 : La date du diagnostic doit être postérieure à la première saillie
        $premiereSaillie = min($datesSaillies);
        $datePremiereSaillie = Carbon::parse($premiereSaillie);
        $dateDiagnostic = Carbon::parse($value);

        if ($dateDiagnostic->lessThanOrEqualTo($datePremiereSaillie)) {
            $fail("La date du diagnostic ({$dateDiagnostic->format('d/m/Y')}) doit être postérieure à la date de la première saillie ({$datePremiereSaillie->format('d/m/Y H:i')}).");
        }
    }

    /**
     * Récupère les dates de saillies depuis le formulaire ou la base de données
     */
    protected function getDatesSaillies(): array
    {
        // Récupérer les saillies depuis le formulaire
        $sailliesForm = ($this->get)('../../saillies') ?? [];

        // Filtrer les saillies du formulaire qui ont une date
        $datesSailliesForm = array_filter(array_column($sailliesForm, 'date_heure'));

        // Si pas de saillies dans le formulaire, vérifier en base de données
        if (empty($datesSailliesForm) && $this->record instanceof CycleReproduction) {
            $sailliesDb = $this->record->saillies()->pluck('date_heure')->toArray();
            $datesSailliesForm = array_filter($sailliesDb);
        }

        return $datesSailliesForm;
    }
}
