<!-- Sidebar-right-->
<div class="sidebar sidebar-left sidebar-animate">

    <div class="panel panel-primary card mb-0 box-shadow h-100">

        <!-- Header -->

        <div class="tab-menu-heading border-0 px-4 py-3 d-flex align-items-center justify-content-between">

            <div>

                <h5 class="mb-0 font-weight-bold">

                    VetPedia Workspace

                </h5>

                <small class="text-muted">

                    Intelligent Clinical Decision Support

                </small>

            </div>

            <div class="card-options">

                <a href="#" class="sidebar-remove text-muted">

                    <i class="fe fe-x"></i>

                </a>

            </div>

        </div>

        <div class="panel-body p-3">

            <!-- Recently Viewed -->

            <div class="card shadow-sm border-0 mb-3">

                <div class="card-header bg-transparent">

                    <h6 class="mb-0 font-weight-bold">

                        <i class="fa fa-history text-primary mr-2"></i>

                        Recently Viewed

                    </h6>

                </div>

                <div class="card-body p-3">

                    <div id="recently-viewed-widget">

                        <div class="text-center text-muted py-2">

                            <i class="fa fa-clock-o mb-2 d-block"></i>

                            <small>

                                No recent items

                            </small>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Quick Actions -->

            <div class="card shadow-sm border-0 mb-3">

                <div class="card-header bg-transparent">

                    <h6 class="mb-0 font-weight-bold">

                        <i class="fa fa-bolt text-warning mr-2"></i>

                        Quick Actions

                    </h6>

                </div>

                <div class="list-group list-group-flush">

                    <a href="{{ url('/diagnostic') }}"
                        class="list-group-item list-group-item-action border-0">

                        <i class="fa fa-stethoscope text-primary mr-2"></i>

                        Start Diagnosis

                    </a>

                    <a href="{{ route('clinical.search') }}"
                        class="list-group-item list-group-item-action border-0">

                        <i class="fa fa-search text-success mr-2"></i>

                        Clinical Search

                    </a>

                    <a href="{{ route('disease.comparison') }}"
                        class="list-group-item list-group-item-action border-0">

                        <i class="fa fa-balance-scale text-info mr-2"></i>

                        Disease Comparison

                    </a>

                    <a href="{{ route('anatomy.index') }}"
                        class="list-group-item list-group-item-action border-0">

                        <i class="fa fa-heartbeat text-danger mr-2"></i>

                        Anatomical Navigator

                    </a>

                    @if(Route::has('body.map'))
                        <a href="{{ route('body.map') }}"
                            class="list-group-item list-group-item-action border-0">

                            <i class="fa fa-map-marked-alt text-secondary mr-2"></i>

                            Poultry Body Map

                        </a>
                    @endif

                    @if(Route::has('lesion.atlas'))
                        <a href="{{ route('lesion.atlas') }}"
                            class="list-group-item list-group-item-action border-0">

                            <i class="fa fa-microscope text-warning mr-2"></i>

                            Lesion Atlas

                        </a>
                    @endif

                    @if(Route::has('cases.index'))
                        <a href="{{ route('cases.index') }}"
                            class="list-group-item list-group-item-action border-0">

                            <i class="fa fa-folder-open text-purple mr-2"></i>

                            Clinical Cases

                        </a>
                    @endif

                </div>

            </div>


            <!-- Knowledge Base Statistics -->

            <div class="card shadow-sm border-0 mb-3">

                <div class="card-header bg-transparent">

                    <h6 class="mb-0 font-weight-bold">

                        <i class="fa fa-database text-success mr-2"></i>

                        Knowledge Base

                    </h6>

                </div>

                <div class="card-body">

                    <div class="row text-center">

                        <div class="col-6 mb-4">

                            <div class="border rounded p-3">

                                <h3 class="text-primary font-weight-bold mb-1">

                                    {{ number_format(\App\Models\Disease::count()) }}

                                </h3>

                                <small class="text-muted">

                                    Diseases

                                </small>

                            </div>

                        </div>

                        <div class="col-6 mb-4">

                            <div class="border rounded p-3">

                                <h3 class="text-success font-weight-bold mb-1">

                                    {{ number_format(\App\Models\Finding::count()) }}

                                </h3>

                                <small class="text-muted">

                                    Findings

                                </small>

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="border rounded p-3">

                                <h3 class="text-danger font-weight-bold mb-1">

                                    {{ number_format(\App\Models\Lesion::count()) }}

                                </h3>

                                <small class="text-muted">

                                    Lesions

                                </small>

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="border rounded p-3">

                                <h3 class="text-warning font-weight-bold mb-1">

                                    {{ class_exists(\App\Models\ClinicalCase::class)
                                        ? number_format(\App\Models\ClinicalCase::count())
                                        : 0 }}

                                </h3>

                                <small class="text-muted">

                                    Cases

                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Platform Modules -->

            <div class="card shadow-sm border-0">

                <div class="card-header bg-transparent">

                    <h6 class="mb-0 font-weight-bold">

                        <i class="fa fa-cubes text-info mr-2"></i>

                        Platform Modules

                    </h6>

                </div>

                <div class="list-group list-group-flush">

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Diagnostic Engine

                    </div>

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Clinical Search

                    </div>

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Disease Explorer

                    </div>

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Knowledge Graph

                    </div>

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Anatomical Navigator

                    </div>

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Body Map

                    </div>

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Lesion Atlas

                    </div>

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Case Library

                    </div>

                    <div class="list-group-item border-0">

                        <i class="fa fa-check-circle text-success mr-2"></i>

                        Universal Search

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<!--/Sidebar-right-->