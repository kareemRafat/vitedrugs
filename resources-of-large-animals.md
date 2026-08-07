# Admin Resources vs Public Pages

**Columns:** (1) Sidebar item in admin — (2) The data entered in that page — (3) The admin sub-menu   / submenu it belongs to — (4) The public page that displays it.

---

## Large Animals section

| Sidebar item | Data entered in that page | Admin sub-menu   / submenu | Public page that shows it |
|---|---|---|---|
| **Landing** | — (landing page, no data) | — (standalone, no submenu) | `/large-animals` (Landing) |
| **Clinical Sign** | sign name (canonical/display/Arabic), stage, semantic slug + **embedded picks:** Finding, Modifier, Anatomical Structure | **Diagnosis** sub-menu   — top tabs: Clinical Signs, Findings, Modifiers, Anatomical Structures, Differential Syndromes | **Diagnosis** page |
| **Finding** | finding name, display name, category, ontology type, noisy/general-sign flags, slug | **Diagnosis** sub-menu   (tab: Findings) | Built into Clinical Signs → **Diagnosis** |
| **Modifier** | modifier name, display name, type, modifier group, noisy flag | **Diagnosis** sub-menu   (tab: Modifiers) | Built into Clinical Signs → **Diagnosis** |
| **Body System** | body system name (canonical/display/Arabic) | **Filter** sub-menu   — top tabs: Body Systems, Host Species | `Filter` (body system filter) |
| **Anatomical Structure** | body system pick, parent pick, names, type | **Diagnosis** sub-menu   (tab: Anatomical Structures) | Built into Clinical Signs → **Diagnosis** |
| **HostSpecies** | species name, display name, taxonomy group, domestic/wildlife/human flags | **Filter** sub-menu   (tab: Host Species) | **Species filter** in `Filter` + **Specializations** pages |
| **DiseaseClassification** | infectiousness, transmissibility, etiology type, occurrence patterns, disease courses, notifiable, zoonotic, OIE category | Large Animals (not sub-menu  ed yet — candidate for an Etiology sub-menu   with Microorganisms) | **Classification** section on disease page `diseases/{slug}` |
| **Disease** (shared) | disease data (name, description, slug) + **knowledge_payload** + **embedded sections filled here:** Clinical Signs, Host Species, Microorganisms | `Catalog` group (shared, no submenu) | Disease page `diseases/{slug}` + **Comparison** |
| **Microorganism** | taxonomy, microorganism type, pathogenic/topic flags, searchable text, tags, slug + **embedded sections filled here:** Diseases (role), Active Ingredients (sensitivity) | Large Animals (not sub-menu  ed yet — candidate for an Etiology sub-menu   with Disease Classifications) | **Microorganisms** pages `microorganisms/{slug}` |
| **MedicalArticle** | article title, slug, summary, content (+ Arabic), species, article type, published flag | Large Animals (not sub-menu  ed yet — candidate for a Publications sub-menu   with Veterinary Projects) | **Articles** page `articles` |
| **VeterinaryProject** | project title, slug, summary, content, project type, sector, featured/published flags | Large Animals (not sub-menu  ed yet — candidate for a Publications sub-menu   with Medical Articles) | **Projects** page `projects/{slug}` |
| **DifferentialSyndrome** | syndrome name (en/ar), description + **embedded sections filled here:** Clinical Signs, Diseases (boost score) | **Diagnosis** sub-menu   (tab: Differential Syndromes) | **Diagnosis** / **Comparison** support |
| **Synonym** | synonym term, normalized term, source type, linked entity | **Search** sub-menu   — top tabs: Synonyms, Abbreviations | **Search** support |
| **Abbreviation** | abbreviation, full term, description, category | **Search** sub-menu   (tab: Abbreviations) | **Search** support |
| **Import Disease Knowledge** | upload JSON onto a Disease's knowledge_payload | **Knowledge** sub-menu   — top tabs: Import Disease Knowledge, Knowledge Overview | Admin-only (no public page) |
| **Knowledge Overview** | coverage counters (stats) | **Knowledge** sub-menu   (tab: Knowledge Overview) | Admin-only |

---

## /drugs section

| Sidebar item | Data entered in that page | Admin sub-menu   / submenu | Public page that shows it |
|---|---|---|---|
| **Product** (drug) | product data (trade name, dosage form, type, description, package size, storage, status) + **embedded sections filled here:** Indication, Contraindication, Precaution, SideEffect, Dosages, Withdrawal Periods, Images, Documents, Alternatives + links (active ingredients, companies, diseases) | `Catalog` group (no submenu) | `products` list + drug page `products/{slug}` (shows side effects, precautions, contraindications, indications, etc.) |
| **Company** | company data (name, type, parent company, contact info, logo, coverage) + its role on products | `Catalog` group (no submenu) | `companies` list + `companies/{slug}` |
| **ActiveIngredient** | ingredient data (name, description, indications, contraindications, precautions, side effects) + **embedded sections filled here:** Drug Interaction, Drug Class + link to products/microorganisms | `Catalog` group (no submenu) | `active-ingredients` list + `active-ingredients/{slug}` |
| **DosageForm** | dosage form name | `Catalog` group (no submenu) | shown on product page `products/{slug}` |
| **Species** | species name, slug | `Catalog` group (no submenu) | target species on product/ingredient pages |
| **ContactSubmission** | product submission request + admin notes | `System` group (no submenu) | `products/create-submission` (public submission form) |
| **Disease** (shared) | name + links to products | `Catalog` group (no submenu) | links drugs to diseases + `products/compare` |
| **Blog** / **BlogCategory** | articles + their categories | `Content` group (no submenu) | blog content on the site |

---

## sub-menu   reference (Large Animals admin)

| sub-menu   | Submenu tabs (top navigation) | Purpose |
|---|---|---|
| **Diagnosis** | Clinical Signs · Findings · Modifiers · Anatomical Structures · Differential Syndromes | Everything that builds the public **Diagnosis** flow |
| **Filter** | Body Systems · Host Species | Data powering the **Filter** flow (body system + species filters) |
| **Search** | Synonyms · Abbreviations | Data powering **Search** (aliases + shorthand) |
| **Knowledge** | Import Disease Knowledge · Knowledge Overview | Admin-only tools for the disease knowledge base |

Remaining un-sub-menu  ed (directly under the **Large Animals** group): DiseaseClassification, Microorganism, MedicalArticle, VeterinaryProject. Suggested future sub-menu  s: **Etiology** (DiseaseClassification + Microorganism) and **Publications** (MedicalArticle + VeterinaryProject).
