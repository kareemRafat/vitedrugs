Overview

Implement a Smart Veterinary Diagnosis Tool that guides veterinarians through a multi-step differential diagnosis process instead of showing results immediately. The goal is to progressively narrow down the most likely diseases by collecting additional clinical signs.

Step 1 – Initial Clinical Signs

Create a diagnosis page where the veterinarian provides the following information:

Animal Type (Required)

Display a dropdown containing:

Cattle
Buffalo
Sheep
Goats
Camels
Horses
Poultry
Rabbits
(Any other supported species)
Clinical Signs (Required)

The veterinarian must enter at least three clinical signs.

Important UI Requirements

Each clinical sign should have its own input field.
Do NOT allow multiple symptoms inside one input.
Start with exactly 3 input fields.
Show an "Add Another Symptom" button below the fields.
Clicking the button adds another symptom field.
Users may add unlimited symptoms.
Validation should prevent continuing unless at least 3 symptoms are entered.

Example:

Clinical Sign 1
[_______________]

Clinical Sign 2
[_______________]

Clinical Sign 3
[_______________]

Add Another Symptom
Symptom Search

Each symptom input should be an autocomplete search field.

Behavior:

As the user types any English letters, immediately search the symptoms database.
Display matching symptom suggestions.
The user selects one suggestion.
Free text should not be allowed if the symptom database is being used.

Example:

User types:

cou

Suggestions:

Cough
Continuous Cough
Cough with Nasal Discharge

The veterinarian selects one.

Search Button

Button text:

Search Diseases

Clicking this button should NOT immediately show the final diagnosis.

Instead, proceed to Step 2.

Step 2 – Additional Clinical Signs Confirmation

Purpose:

After receiving the initial symptoms, the system should identify the Top 3 Most Probable Diseases internally.

The user does not see the diseases yet.

Instead, extract the most important distinguishing clinical signs from those diseases that the veterinarian has not already selected.

Display them under the heading:

What other clinical signs do you observe?

These signs help narrow the diagnosis.

UI

Display each symptom with a checkbox.

Example:

☐ Fever

☐ Dyspnea

☐ Nasal Discharge

☐ Depression

☐ Lacrimation

☐ Weight Loss

☐ Bloody Diarrhea

Only display symptoms that:

belong to the top probable diseases
were NOT selected in Step 1

The veterinarian selects every additional symptom that exists in the animal.

Then clicks:

Continue Diagnosis

Proceed to Step 3.

Step 3 – Differential Diagnosis Results

Now calculate the final differential diagnosis using:

Animal species
Initial symptoms
Additional confirmed symptoms

Split the diseases into three probability groups.

Primary Diagnosis

Highest confidence diseases.

Probability:

50% – 100%

Display up to 5 diseases.

Secondary Diagnosis

Medium confidence diseases.

Probability:

30% – 50%

Display up to 5 diseases.

Low Probability

Lower confidence diseases.

Probability:

Below 30%

Display up to 5 diseases.

Results Table

Each diagnosis section should display a table.

Columns:

Column Description
Disease Disease name
Probability Final disease probability (%)
Match Score Percentage showing how well the selected symptoms match this disease
Matching Clinical Signs Clinical signs selected by the veterinarian that exist in this disease
Important Missing Clinical Signs Highly characteristic signs of the disease that the veterinarian did not select

Example

Disease Probability Match Matching Clinical Signs Important Missing Clinical Signs
Foot and Mouth Disease 92% 90% Fever, Salivation, Oral Lesions Lameness
Bovine Respiratory Disease 71% 76% Cough, Fever Nasal Discharge
Hemorrhagic Septicemia 58% 63% Fever Neck Swelling
Match Score Calculation

The Match Score should represent how closely the veterinarian's selected symptoms match the disease profile.

Example:

Disease contains:

Fever
Cough
Nasal Discharge
Depression
Dyspnea

Veterinarian selected:

Fever
Cough
Dyspnea

Match Score:

3 / 5 = 60%

More advanced weighted scoring may be used if symptom importance is available.

Important Missing Clinical Signs

This is one of the most valuable features.

For every disease, display the important characteristic signs that were NOT selected by the veterinarian.

Example:

Disease:

Foot and Mouth Disease

Veterinarian selected:

Fever
Salivation

Missing characteristic signs:

Oral Vesicles
Lameness
Tongue Ulcers

This helps the veterinarian re-examine the animal and confirm or rule out the diagnosis.

User Flow

Step 1

Select animal type.
Enter at least 3 symptoms.
Click Search Diseases.

↓

Step 2

System internally finds the top 3 probable diseases.
Show only additional distinguishing symptoms.
Veterinarian checks any symptoms that are also present.
Click Continue Diagnosis.

↓

Step 3

Calculate the final differential diagnosis.
Group diseases into:
Primary
Secondary
Low Probability
Display detailed comparison tables including:
Disease name
Probability
Match Score
Matching Clinical Signs
Important Missing Clinical Signs
UX Goals
Make the diagnosis process feel intelligent and interactive.
Reduce false positives by collecting additional distinguishing symptoms before showing results.
Help veterinarians remember important clinical signs they may have overlooked.
Provide transparent reasoning behind every suggested disease by showing both matched and missing clinical signs.
Keep the interface clean, modern, and easy to use on both desktop and mobile devices.
