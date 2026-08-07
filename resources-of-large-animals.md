# Admin Resources vs Public Pages

**Columns:** (1) Sidebar item in admin — (2) The data entered in that page — (3) The public page that displays it.

---

## Large Animals section

| Sidebar item | Data entered in that page | Public page that shows it |
|---|---|---|
| **Landing** | — (landing page, no data) | `/large-animals` (Landing) |
| **Clinical Sign** | sign name (canonical/display/Arabic), stage, semantic slug + **embedded picks:** Finding, Modifier, Anatomical Structure | **Diagnosis** page |
| **Finding** | finding name, display name, category, ontology type, noisy/general-sign flags, slug | Built into Clinical Signs → **Diagnosis** |
| **Modifier** | modifier name, display name, type, modifier group, noisy flag | Built into Clinical Signs → **Diagnosis** |
| **Body System** | body system name (canonical/display/Arabic) | `Filter` (body system filter) |
| **Anatomical Structure** | body system pick, parent pick, names, type | Built into Clinical Signs → **Diagnosis** |
| **HostSpecies** | species name, display name, taxonomy group, domestic/wildlife/human flags | **Species filter** in `Filter` + **Specializations** pages |
| **DiseaseClassification** | infectiousness, transmissibility, etiology type, occurrence patterns, disease courses, notifiable, zoonotic, OIE category | **Classification** section on disease page `diseases/{slug}` |
| **Disease** (shared) | disease data (name, description, slug) + **knowledge_payload** + **embedded sections filled here:** Clinical Signs, Host Species, Microorganisms | Disease page `diseases/{slug}` + **Comparison** |
| **Microorganism** | taxonomy, microorganism type, pathogenic/topic flags, searchable text, tags, slug + **embedded sections filled here:** Diseases (role), Active Ingredients (sensitivity) | **Microorganisms** pages `microorganisms/{slug}` |
| **MedicalArticle** | article title, slug, summary, content (+ Arabic), species, article type, published flag | **Articles** page `articles` |
| **VeterinaryProject** | project title, slug, summary, content, project type, sector, featured/published flags | **Projects** page `projects/{slug}` |
| **DifferentialSyndrome** | syndrome name (en/ar), description + **embedded sections filled here:** Clinical Signs, Diseases (boost score) | **Diagnosis** / **Comparison** support |
| **Synonym** | synonym term, normalized term, source type, linked entity | **Search** support |
| **Abbreviation** | abbreviation, full term, description, category | **Search** support |
| **Import Disease Knowledge** | upload JSON onto a Disease's knowledge_payload | Admin-only (no public page) |
| **Knowledge Overview** | coverage counters (stats) | Admin-only |

---

## /drugs section

| Sidebar item | Data entered in that page | Public page that shows it |
|---|---|---|
| **Product** (drug) | product data (trade name, dosage form, type, description, package size, storage, status) + **embedded sections filled here:** Indication, Contraindication, Precaution, SideEffect, Dosages, Withdrawal Periods, Images, Documents, Alternatives + links (active ingredients, companies, diseases) | `products` list + drug page `products/{slug}` (shows side effects, precautions, contraindications, indications, etc.) |
| **Company** | company data (name, type, parent company, contact info, logo, coverage) + its role on products | `companies` list + `companies/{slug}` |
| **ActiveIngredient** | ingredient data (name, description, indications, contraindications, precautions, side effects) + **embedded sections filled here:** Drug Interaction, Drug Class + link to products/microorganisms | `active-ingredients` list + `active-ingredients/{slug}` |
| **DosageForm** | dosage form name | shown on product page `products/{slug}` |
| **Species** | species name, slug | target species on product/ingredient pages |
| **ContactSubmission** | product submission request + admin notes | `products/create-submission` (public submission form) |
| **Disease** (shared) | name + links to products | links drugs to diseases + `products/compare` |
| **Blog** / **BlogCategory** | articles + their categories | blog content on the site |

---
