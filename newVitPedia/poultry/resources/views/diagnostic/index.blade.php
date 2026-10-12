@extends('layouts.master')
@section('css')
    <style>
        #suggestions {

            position: absolute;

            width: 100%;

            z-index: 9999;

            background: #fff;

            border-radius: 10px;

            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);

            overflow: hidden;

            max-height: 300px;

            overflow-y: auto;
        }

        #suggestions button {

            text-align: left;

            border: 0;

            padding: 12px 15px;
        }

        #suggestions button:hover {

            background: #f5f7fb;
        }

        .hero-card {

            background: linear-gradient(135deg,
                    #0162e8,
                    #1e88e5);

            color: #fff;

            border: none;
        }

        .hero-card h2,
        .hero-card p {

            color: #fff;
        }

        .section-card {

            border: none;

            border-radius: 12px;

            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
        }

        .section-card .card-header {

            background: #fff;

            border-bottom: 1px solid #eef1f5;

            font-weight: 600;
        }

        .diagnostic-result {

            border-left: 5px solid #0162e8;

            transition: .2s;
        }

        .diagnostic-result:hover {

            transform: translateY(-2px);

            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
        }

        .finding-badge {

            font-size: 13px;

            padding: 8px 12px;

            border-radius: 20px;
        }

        .sticky-sidebar {

            position: sticky;

            top: 20px;
        }

        .quick-tip li {

            margin-bottom: 10px;
        }

        .empty-state {

            padding: 50px 20px;

            text-align: center;

            color: #6c757d;
        }

        @media(max-width:991px) {

            .sticky-sidebar {

                position: relative;
            }

        }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header">

        <div>

            <h2 class="content-title mb-1">

                Poultry Diagnostic Engine

            </h2>

            <p class="text-muted mb-0">

                Intelligent Clinical Decision Support System

            </p>

        </div>

    </div>
@endsection



@section('content')




    <div class="row">

        <!-- LEFT SIDE -->

        <div class="col-xl-4 sticky-sidebar">


            <!-- Patient Information -->

            <div class="card section-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h4 class="card-title mb-0">

                        Case Information

                    </h4>

                    <button class="btn btn-outline-danger btn-sm" onclick="resetDiagnosis()">

                        <i class="fa fa-refresh"></i>

                        New Diagnosis

                    </button>

                </div>


                <div class="card-body">

                    <!-- BASIC -->

                    <div class="form-group">

                        <label>

                            Species <span class="text-danger">*</span>

                        </label>

                        <select id="species" class="form-control">

                            <option value="">
                                Select Species
                            </option>

                            @foreach ($species as $speciesItem)
                                <option value="{{ $speciesItem->id }}">

                                    {{ $speciesItem->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="form-group">

                        <label>

                            Age (Days)

                        </label>

                        <input type="number" id="age_days" class="form-control" placeholder="Bird age in days">

                    </div>

                    <hr>

                    <!-- ADVANCED -->

                    <button class="btn btn-light btn-block" type="button" data-toggle="collapse"
                        data-target="#advancedInvestigation">

                        <i class="fa fa-sliders"></i>

                        Advanced Investigation Data

                    </button>

                    <div class="collapse mt-4" id="advancedInvestigation">

                        <div class="alert alert-light">

                            Optional epidemiological information to improve diagnostic accuracy.

                        </div>

                        <div class="form-group">

                            <label>Country</label>

                            <select id="country" class="form-control">

                                <option value="Egypt">

                                    Egypt

                                </option>

                                <option value="Saudi Arabia">

                                    Saudi Arabia

                                </option>

                                <option value="Sudan">

                                    Sudan

                                </option>

                                <option value="Libya">

                                    Libya

                                </option>

                                <option value="Jordan">

                                    Jordan

                                </option>

                                <option value="Other">

                                    Other

                                </option>

                            </select>

                        </div>

                        <div class="form-group">

                            <label>

                                Governorate / Region

                            </label>

                            <input type="text" id="governorate" class="form-control" placeholder="Dakahlia">

                        </div>

                        <div class="form-group">

                            <label>

                                Production Type

                            </label>

                            <select id="production_type" class="form-control">

                                <option value="">
                                    Select Type
                                </option>

                                <option value="broiler">
                                    Broiler
                                </option>

                                <option value="layer">
                                    Layer
                                </option>

                                <option value="breeder">
                                    Breeder
                                </option>

                                <option value="backyard">
                                    Backyard
                                </option>

                                <option value="commercial">
                                    Commercial
                                </option>

                            </select>

                        </div>

                        <hr>

                        <h6>

                            Outbreak Information

                        </h6>

                        <div class="form-group">

                            <label>

                                Mortality Rate %

                            </label>

                            <input type="number" id="mortality_rate" class="form-control">

                        </div>

                        <div class="form-group">

                            <label>

                                Mortality Pattern

                            </label>

                            <select id="mortality_pattern" class="form-control">

                                <option value="">
                                    Select Pattern
                                </option>

                                <option value="peracute">
                                    Peracute
                                </option>

                                <option value="acute">
                                    Acute
                                </option>

                                <option value="subacute">
                                    Subacute
                                </option>

                                <option value="chronic">
                                    Chronic
                                </option>

                            </select>

                        </div>

                        <div class="form-group">

                            <label>

                                Peak Mortality Day

                            </label>

                            <input type="number" id="peak_day" class="form-control">

                        </div>

                        <div class="custom-control custom-checkbox mb-3">

                            <input type="checkbox" class="custom-control-input" id="sudden_deaths">

                            <label class="custom-control-label" for="sudden_deaths">

                                Sudden Deaths

                            </label>

                        </div>

                        <hr>

                        <h6>

                            Risk Factors

                        </h6>

                        <div class="custom-control custom-checkbox mb-2">

                            <input type="checkbox" class="custom-control-input" id="poor_ventilation">

                            <label class="custom-control-label" for="poor_ventilation">

                                Poor Ventilation

                            </label>

                        </div>

                        <div class="custom-control custom-checkbox mb-2">

                            <input type="checkbox" class="custom-control-input" id="wet_litter">

                            <label class="custom-control-label" for="wet_litter">

                                Wet Litter

                            </label>

                        </div>

                        <div class="custom-control custom-checkbox mb-2">

                            <input type="checkbox" class="custom-control-input" id="mixed_species">

                            <label class="custom-control-label" for="mixed_species">

                                Mixed Species

                            </label>

                        </div>

                        <div class="custom-control custom-checkbox mb-2">

                            <input type="checkbox" class="custom-control-input" id="new_birds_introduced">

                            <label class="custom-control-label" for="new_birds_introduced">

                                New Birds Introduced

                            </label>

                        </div>

                        <div class="custom-control custom-checkbox mb-3">

                            <input type="checkbox" class="custom-control-input" id="no_vaccination">

                            <label class="custom-control-label" for="no_vaccination">

                                No Vaccination Program

                            </label>

                        </div>

                        <div class="form-group">

                            <label>

                                Case Notes

                            </label>

                            <textarea id="case_notes" rows="4" class="form-control" placeholder="Optional additional information"></textarea>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Clinical Findings -->

            <div class="card section-card">

                <div class="card-header d-flex justify-content-between">

                    <h4 class="card-title">

                        Clinical Findings

                    </h4>

                    <span class="badge badge-primary">

                        <span id="finding-count">

                            0

                        </span>

                        Findings

                    </span>

                </div>

                <div class="card-body">

                    <div class="position-relative">

                        <input type="text" id="finding_search" class="form-control" placeholder="Search findings...">

                        <div id="suggestions" class="list-group mt-2">

                        </div>

                    </div>

                    <hr>

                    <h6>

                        Selected Findings

                    </h6>

                    <div id="selected_findings" class="mb-3">

                    </div>

                    <hr>

                    <h6 class="text-muted">

                        Negative Findings

                    </h6>

                    <small class="text-muted d-block mb-2">

                        (Future feature)

                    </small>

                    <div id="negative_findings">

                    </div>

                    <button class="btn btn-primary btn-block mt-4" id="diagnoseBtn">

                        <i class="fa fa-stethoscope"></i>

                        Start Diagnosis

                    </button>

                </div>

            </div>



            <!-- Loading -->

            <div id="loading-box" class="card custom-card" style="display:none;">

                <div class="card-body text-center">

                    <div class="spinner-border text-primary">

                    </div>

                    <p class="mt-3 mb-0">

                        Analyzing clinical findings...

                    </p>

                </div>

            </div>



            <!-- Interactive Interview -->

            <div class="card custom-card" id="interview-card" style="display:none;">

                <div class="card-header">

                    <h4 class="card-title">

                        Interactive Diagnostic Interview

                    </h4>

                    <small id="question-counter" class="text-muted">

                        Question 0

                    </small>

                </div>

                <div class="card-body">

                    <div class="progress mb-4" style="height:30px;">

                        <div id="interview-progress"
                            class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                            style="width:0%;" aria-valuemin="0" aria-valuemax="100">

                            0%

                        </div>

                    </div>

                    <div id="question-box">

                    </div>

                </div>

            </div>



            <!-- Quick Help -->

            <div class="card section-card">

                <div class="card-header">

                    <h4 class="card-title">

                        Diagnostic Tips

                    </h4>

                </div>

                <div class="card-body">

                    <ul class="mb-0 quick-tip">

                        <li>
                            Enter at least 3 findings.
                        </li>

                        <li>
                            Include gross lesions if available.
                        </li>

                        <li>
                            Include flock age for better accuracy.
                        </li>

                        <li>
                            Use the interview questions to refine diagnosis.
                        </li>

                    </ul>

                </div>

            </div>

        </div>



        <!-- RIGHT SIDE -->

        <div class="col-xl-8">


            <!-- Differential Diagnosis -->

            <div class="card custom-card" id="differential-card">

                <div class="card-header">

                    <h4 class="card-title">

                        Differential Diagnosis

                    </h4>

                </div>

                <div class="card-body">

                    <div id="differential-box">

                        <div class="empty-state">

                            <i class="fa fa-random fa-3x text-muted mb-3"></i>

                            <h6>

                                Differential diagnosis will appear here

                            </h6>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Results -->

            <div class="card section-card">

                <div class="card-header">

                    <h4 class="card-title">

                        Diagnostic Results

                    </h4>

                </div>

                <div class="card-body">

                    <div id="results">

                        <div class="empty-state">

                            <i class="fa fa-stethoscope fa-4x mb-3 text-primary"></i>

                            <h5>

                                No Diagnostic Session Started

                            </h5>

                            <p>

                                Add clinical findings and start diagnosis.

                            </p>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Final Report -->

            <div class="card custom-card" id="report-card" style="display:none;">

                <div class="card-header bg-success text-white">

                    <h4 class="card-title text-white">

                        Final Diagnostic Report

                    </h4>

                </div>

                <div class="card-body">

                    <div id="final-report">

                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- Disease Details Modal -->

    <div class="modal fade" id="diseaseModal">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 id="disease-modal-title">

                    </h5>

                    <button type="button" class="close" data-dismiss="modal">

                        &times;

                    </button>

                </div>

                <div class="modal-body" id="disease-modal-body">

                </div>

            </div>

        </div>

    </div>
@endsection












@section('js')
    <script>
        let selectedFindings = [];
        let sessionId = null;
        let currentQuestion = null;
        let questionCount = 0;
        let isProcessing = false;

        const searchInput =
            document.getElementById('finding_search');

        const suggestions =
            document.getElementById('suggestions');

        const selectedContainer =
            document.getElementById('selected_findings');

        const resultsDiv =
            document.getElementById('results');

        const diagnoseBtn =
            document.getElementById('diagnoseBtn');


        // ==========================================================
        // SEARCH FINDINGS
        // ==========================================================

        searchInput.addEventListener(
            'keyup',

            debounce(async function(e) {

                const q = e.target.value.trim();

                if (q.length < 2) {

                    suggestions.innerHTML = '';

                    return;
                }

                try {

                    const response = await fetch(
                        `/api/v1/findings/suggestions?q=${encodeURIComponent(q)}`
                    );

                    const data = await response.json();

                    suggestions.innerHTML = '';

                    data.forEach(item => {

                        const row =
                            document.createElement('button');

                        row.type = 'button';

                        row.className =
                            'list-group-item list-group-item-action';

                        row.innerHTML = `
                        <strong>
                            ${item.display_name}
                        </strong>
                    `;

                        row.onclick =
                            () => addFinding(item);

                        suggestions.appendChild(row);

                    });

                } catch (error) {

                    console.error(error);
                }

            }, 300)
        );


        // ==========================================================
        // FINDINGS MANAGEMENT
        // ==========================================================

        function addFinding(item) {

            if (
                selectedFindings.includes(
                    item.canonical_name
                )
            ) {
                return;
            }

            selectedFindings.push(
                item.canonical_name
            );

            renderSelected();

            suggestions.innerHTML = '';

            searchInput.value = '';

            searchInput.focus();
        }


        function removeFinding(name) {

            selectedFindings =
                selectedFindings.filter(
                    f => f !== name
                );

            renderSelected();
        }


        function renderSelected() {

            selectedContainer.innerHTML = '';

            selectedFindings.forEach(item => {

                selectedContainer.innerHTML += `

                <span class="badge badge-primary finding-badge mr-2 mb-2">

                    ${item}

                    <a href="#"

                        class="text-white ml-2"

                        onclick="
                            event.preventDefault();
                            removeFinding('${item}')
                        ">

                        <i class="fa fa-times"></i>

                    </a>

                </span>

            `;

            });

            document.getElementById(
                    'finding-count'
                ).innerText =
                selectedFindings.length;
        }


        // ==========================================================
        // START DIAGNOSIS
        // ==========================================================

        diagnoseBtn.addEventListener(
            'click',
            startDiagnosis
        );

        async function startDiagnosis() {

            if (isProcessing) return;

            if (selectedFindings.length < 3) {

                alert(
                    'Please enter at least 3 clinical findings.'
                );

                return;
            }

            isProcessing = true;

            diagnoseBtn.disabled = true;

            diagnoseBtn.innerHTML = `
            <i class="fa fa-spinner fa-spin"></i>
            Processing...
        `;

            resetResultPanels();

            showLoading();

            try {

                const response = await fetch(

                    '/api/v1/session/start',

                    {

                        method: 'POST',

                        headers: {

                            'Content-Type': 'application/json',

                            'Accept': 'application/json',

                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },

                        body: JSON.stringify({

                            species_id: document.getElementById('species').value,

                            age_days: document.getElementById('age_days').value,

                            findings: selectedFindings,

                            vaccinated:
                                !document.getElementById('no_vaccination').checked,

                            wet_litter: document.getElementById('wet_litter').checked,

                            poor_ventilation: document.getElementById('poor_ventilation').checked,

                            mixed_species: document.getElementById('mixed_species').checked,

                            new_birds_introduced: document.getElementById('new_birds_introduced')
                                ?.checked ?? false,

                            country: document.getElementById('country').value,

                            governorate: document.getElementById('governorate').value,

                            production_type: document.getElementById('production_type').value,

                            mortality_rate: document.getElementById('mortality_rate').value,

                            mortality_pattern: document.getElementById('mortality_pattern').value,

                            sudden_deaths: document.getElementById('sudden_deaths').checked,

                            peak_day: document.getElementById('peak_day').value,

                            case_notes: document.getElementById('case_notes').value
                        })
                    }

                );

                const json =
                    await response.json();

                if (!response.ok) {

                    throw new Error(
                        json.message ||
                        'Unable to perform diagnosis.'
                    );
                }

                renderResults(
                    json.results || []
                );

                if (json.differential) {

                    renderDifferential(
                        json.differential
                    );
                }

                if (json.next_question) {

                    renderQuestion(
                        json.next_question
                    );
                }

                window.scrollTo({

                    top: 0,

                    behavior: 'smooth'
                });

            } catch (error) {

                alert(error.message);

                console.error(error);

            } finally {

                hideLoading();

                isProcessing = false;

                diagnoseBtn.disabled = false;

                diagnoseBtn.innerHTML = `
                <i class="fa fa-stethoscope"></i>
                Start Diagnosis
            `;
            }
        }


        // ==========================================================
        // QUESTIONS
        // ==========================================================

        function renderQuestion(question) {

            currentQuestion = question;

            questionCount++;

            const progress =
                Math.min(
                    100,
                    questionCount * 10
                );

            document.getElementById(
                'interview-card'
            ).style.display = 'block';

            document.getElementById(
                    'question-counter'
                ).innerHTML =
                `Question ${questionCount}`;

            const progressBar =
                document.getElementById(
                    'interview-progress'
                );

            progressBar.style.width =
                progress + '%';

            progressBar.innerHTML =
                progress + '%';

            progressBar.setAttribute(
                'aria-valuenow',
                progress
            );

            document.getElementById(
                'question-box'
            ).innerHTML = `

            <div class="text-center">

                <h4 class="mb-4">

                    ${question.question}

                </h4>

                <button class="btn btn-success btn-lg mr-2 mb-2"
                    onclick="submitAnswer('yes')">

                    <i class="fa fa-check"></i>

                    Yes

                </button>

                <button class="btn btn-danger btn-lg mr-2 mb-2"
                    onclick="submitAnswer('no')">

                    <i class="fa fa-times"></i>

                    No

                </button>

                <button class="btn btn-secondary btn-lg mb-2"
                    onclick="submitAnswer('unknown')">

                    <i class="fa fa-question"></i>

                    Unknown

                </button>

            </div>
        `;
        }


        // ==========================================================
        // LOADING
        // ==========================================================

        function showLoading() {

            document.getElementById(
                'loading-box'
            ).style.display = 'block';
        }

        function hideLoading() {

            document.getElementById(
                'loading-box'
            ).style.display = 'none';
        }


        // ==========================================================
        // RESULTS
        // ==========================================================

        function renderResults(results) {

            resultsDiv.innerHTML = '';

            if (!results.length) {

                resultsDiv.innerHTML = `

                <div class="alert alert-warning">

                    No matching diseases found.

                </div>

            `;

                return;
            }

            results.slice(0, 10)
                .forEach((item, index) => {

                    const badge =
                        index === 0 ?
                        `
                        <span class="badge badge-success mb-3">
                            Most Likely Diagnosis
                        </span>
                    ` :
                        '';

                    resultsDiv.innerHTML += `

                    <div class="card diagnostic-result mb-4">

                        <div class="card-body">

                            ${badge}

                            <h4>

                                <a href="javascript:void(0)"

                                    onclick="
                                        openDiseaseModal(
                                            '${item.disease.slug}'
                                        )
                                    ">

                                    ${item.disease.name}

                                </a>

                            </h4>

                            <div class="progress mb-3">

                                <div class="progress-bar bg-success"

                                    style="
                                        width:${item.confidence}%
                                    ">

                                    ${item.confidence}%

                                </div>

                            </div>

                            <p>

                                <strong>Matched Findings:</strong>

                                ${(item.matched_findings || []).join(', ')}

                            </p>

                        </div>

                    </div>

                `;
                });
        }


        // ==========================================================
        // RESET
        // ==========================================================

        function resetDiagnosis() {

            if (!confirm(
                    'Start a new diagnostic session?'
                )) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Session Variables
            |--------------------------------------------------------------------------
            */

            selectedFindings = [];

            sessionId = null;

            currentQuestion = null;

            questionCount = 0;

            isProcessing = false;


            /*
            |--------------------------------------------------------------------------
            | Main Inputs
            |--------------------------------------------------------------------------
            */

            document.getElementById(
                'species'
            ).selectedIndex = 0;

            document.getElementById(
                'age_days'
            ).value = '';

            document.getElementById(
                'finding_search'
            ).value = '';


            /*
            |--------------------------------------------------------------------------
            | Advanced Inputs
            |--------------------------------------------------------------------------
            */

            document.getElementById(
                'country'
            ).selectedIndex = 0;

            document.getElementById(
                'governorate'
            ).value = '';

            document.getElementById(
                'production_type'
            ).selectedIndex = 0;

            document.getElementById(
                'mortality_rate'
            ).value = '';

            document.getElementById(
                'mortality_pattern'
            ).selectedIndex = 0;

            document.getElementById(
                'peak_day'
            ).value = '';

            document.getElementById(
                'case_notes'
            ).value = '';


            /*
            |--------------------------------------------------------------------------
            | Checkboxes
            |--------------------------------------------------------------------------
            */

            [
                'sudden_deaths',

                'poor_ventilation',

                'wet_litter',

                'mixed_species',

                'new_birds_introduced',

                'no_vaccination'

            ].forEach(id => {

                const el =
                    document.getElementById(id);

                if (el) {

                    el.checked = false;
                }

            });


            /*
            |--------------------------------------------------------------------------
            | Findings UI
            |--------------------------------------------------------------------------
            */

            suggestions.innerHTML = '';

            renderSelected();

            document.getElementById(
                'finding-count'
            ).innerText = 0;


            /*
            |--------------------------------------------------------------------------
            | Cards
            |--------------------------------------------------------------------------
            */

            document.getElementById(
                'interview-card'
            ).style.display = 'none';

            document.getElementById(
                'report-card'
            ).style.display = 'none';

            hideLoading();

            resetResultPanels();


            /*
            |--------------------------------------------------------------------------
            | Progress
            |--------------------------------------------------------------------------
            */

            document.getElementById(
                'question-counter'
            ).innerHTML = 'Question 0';

            let progressBar =
                document.getElementById(
                    'interview-progress'
                );

            progressBar.style.width = '0%';

            progressBar.innerHTML = '0%';


            /*
            |--------------------------------------------------------------------------
            | Buttons
            |--------------------------------------------------------------------------
            */

            diagnoseBtn.disabled = false;

            diagnoseBtn.innerHTML = `

        <i class="fa fa-stethoscope"></i>

        Start Diagnosis

    `;


            /*
            |--------------------------------------------------------------------------
            | Scroll
            |--------------------------------------------------------------------------
            */

            window.scrollTo({

                top: 0,

                behavior: 'smooth'
            });
        }


        function resetResultPanels() {

            resultsDiv.innerHTML = '';

            document.getElementById(
                'differential-box'
            ).innerHTML = `
            <div class="empty-state">
                Differential diagnosis will appear here
            </div>
        `;
        }


        // ==========================================================
        // DISEASE MODAL
        // ==========================================================

        function openDiseaseModal(slug) {

            $('#diseaseModal').modal('show');

            document.getElementById(
                'disease-modal-title'
            ).innerHTML = 'Loading...';

            document.getElementById(
                'disease-modal-body'
            ).innerHTML = `
            <div class="text-center p-5">
                <div class="spinner-border text-primary"></div>
            </div>
        `;

            fetch(`/api/v1/diseases/${slug}`)

                .then(r => r.json())

                .then(data => {

                    document.getElementById(
                            'disease-modal-title'
                        ).innerHTML =
                        data.name;

                    document.getElementById(
                        'disease-modal-body'
                    ).innerHTML = `

                    <h5>Description</h5>

                    <p>

                        ${data.description || 'No description available.'}

                    </p>

                    <hr>

                    <a href="/diseases/${slug}"

                        class="btn btn-primary">

                        Open Full Disease Page

                    </a>
                `;
                });
        }


        // ==========================================================
        // HELPERS
        // ==========================================================

        function debounce(func, wait) {

            let timeout;

            return function(...args) {

                clearTimeout(timeout);

                timeout =
                    setTimeout(
                        () => func(...args),
                        wait
                    );
            };
        }


        document.addEventListener(
            'click',
            function(e) {

                if (
                    !searchInput.contains(e.target) &&
                    !suggestions.contains(e.target)
                ) {

                    suggestions.innerHTML = '';
                }
            }
        );


        searchInput.addEventListener(
            'keydown',
            function(e) {

                if (e.key === 'Enter') {

                    e.preventDefault();

                    suggestions.querySelector('button')
                        ?.click();
                }
            }
        );
    </script>
@endsection
