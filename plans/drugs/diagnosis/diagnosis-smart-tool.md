# Smart Veterinary Diagnosis Tool UI Plan

## Goal

Update the public `/drugs/diagnosis` flow into a guided three-step differential diagnosis tool. Veterinarians select a species and at least three database-backed clinical signs, confirm additional distinguishing signs, and receive transparent ranked results.

The first release remains anonymous and request/session-scoped. It does not create patient records or save diagnosis history.

## Milestone 1: Step 1 - Clinical-sign input experience

[x] Replace the current full clinical-sign checklist with exactly three clinical-sign input rows on first load.

[x] Make animal type mandatory and populate it from the existing supported host-species records.

[x] Make each clinical-sign row an autocomplete field that searches the existing clinical-sign database while the user types.

[x] Require users to select a suggestion from the database; do not accept free-text symptom values.

[x] Add an "Add Another Symptom" action that appends one new autocomplete row without limiting the total number of signs.

[x] Allow users to remove added rows while retaining the initial three required rows.

[x] Validate that the selected species is valid and that at least three unique, valid clinical-sign IDs were submitted.

[] Add translated validation, empty-state, loading, and no-suggestion messages for English and Arabic.

[x] Build the responsive Blade/Alpine UI with the project's Flowbite v4 semantic tokens, Lucide icons, and existing layout conventions.

## Milestone 2: Diagnosis query and refinement logic

[x] Add a lightweight endpoint that returns matching clinical-sign suggestions by localized display name and canonical name.

[x] Extract the current diagnosis ranking logic into a reusable application service or action so Step 1 and Step 3 use one scoring source.

[x] Filter candidate diseases to the chosen host species using the existing disease-host-species relationship and eligibility metadata.

[x] Eager-load clinical signs and host relationships required by the scorer to avoid N+1 queries.

[x] Preserve the existing clinical-sign evidence metadata: pivot weight, specific flag, required flag, and pathognomonic flag.

[x] Internally rank the Step 1 candidates and retain only the top three diseases to build the refinement-question pool.

[x] Do not display candidate disease names during the refinement step.

[x] Build the refinement pool from unselected signs belonging to the top three candidates.

[x] Treat pathognomonic, required, and specific high-weight signs as distinguishing signs; sort them by diagnostic importance and then weight.

[x] Remove signs already selected in Step 1 and de-duplicate refinement signs across diseases.

[x] Allow the veterinarian to confirm zero or more refinement signs before continuing.

## Milestone 3: Step 2 - Additional-sign confirmation UI

[] Update the refinement page heading to "What other clinical signs do you observe?" with translated equivalents. English is complete; Arabic wording remains pending.

[x] Display each distinguishing sign as a checkbox with its localized name and, where useful, its diagnostic-significance indicator.

[x] Provide a clear message and a continue action when no additional distinguishing signs are available.

[x] Preserve the selected species and initial clinical-sign IDs securely through the refinement submission.

[x] Add back and restart actions that keep navigation predictable without exposing preliminary diseases.

## Milestone 4: Step 3 - Differential diagnosis results

[x] Recalculate rankings using the species, initial signs, and confirmed refinement signs.

[x] Calculate final probability as each eligible disease's weighted evidence score normalized against the highest-scoring eligible disease, producing a value from 0% to 100%.

[x] Calculate Match Score separately as selected clinical-sign weight divided by the disease profile's total clinical-sign weight.

[x] Group results as Primary Diagnosis for probabilities of 50% or above, Secondary Diagnosis for 30% to under 50%, and Low Probability for under 30%.

[x] Sort each group by final probability descending and limit each group to five diseases.

[x] Replace the current qualitative result cards with responsive tables containing disease, probability, Match Score, matching clinical signs, and important missing clinical signs.

[x] Define important missing signs as unselected pathognomonic signs, required signs, or high-weight specific signs; display up to five per disease.

[x] Keep disease names linked to their existing public disease-detail pages.

[x] Add clear empty states when the selected species has no eligible diseases or no disease matches the submitted signs.

[x] Include a concise decision-support disclaimer stating that the tool assists differential diagnosis and does not replace examination or confirmatory testing.

## Milestone 5: Database and data readiness

[x] Do not add a migration for this release; the existing `host_species`, `clinical_signs`, `disease_host_species`, and `disease_clinical_sign` tables contain the required structure.

[] Audit disease-host-species links so supported species have accurate eligibility and susceptibility data.

[] Audit disease-clinical-sign pivot records so important signs have appropriate weights and specific, required, or pathognomonic flags.

[] Treat incomplete disease/sign metadata as a data-quality issue to improve through existing admin workflows, not as a reason to add a new schema.

## Milestone 6: Verification

[] Skipped at user request: PHPUnit feature coverage for required species, three-sign minimum, duplicate signs, invalid sign IDs, and rejected free-text submissions.

[] Skipped at user request: autocomplete search and localized clinical-sign suggestion coverage.

[] Skipped at user request: diagnosis-flow coverage for species filtering, top-three refinement selection, hidden preliminary diseases, and refinement signs excluding Step 1 signs.

[] Skipped at user request: scoring coverage for normalized probability, weighted Match Score, group boundaries, ordering, five-result limits, and important missing signs.

[] Skipped at user request: response coverage for all three pages, no-result states, translations, and the expected table columns.

[x] Laravel Pint completed; feature tests skipped at user request.

## Milestone 7: Shareable localized results

[x] Redirect completed assessments to a GET result URL containing an encrypted, tamper-resistant assessment token.

[x] Expire shared assessment links after 24 hours and redirect invalid or expired links to a new assessment.

[x] Recalculate the results from the decrypted species and clinical-sign IDs on every GET request, so language switching retains the assessment and uses localized names.

## Acceptance criteria

[] A veterinarian cannot proceed without selecting a species and three distinct database-backed signs.

[] The first step starts with three autocomplete inputs and supports adding unlimited additional inputs.

[] Step 2 asks about unselected distinguishing signs from only the internally ranked top three candidates.

[] Step 3 displays up to five diseases in each probability group with transparent matching and missing-sign evidence.

[] No database migration or persisted diagnosis history is introduced.
