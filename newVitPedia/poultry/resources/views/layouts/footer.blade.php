<!-- Footer -->
<footer class="main-footer border-top mt-5">

    <div class="container-fluid">

        <div class="row py-5">

            <!-- About -->

            <div class="col-xl-5 col-lg-5 col-md-12 mb-4">

                <div class="mb-3">

                    <h3 class="text-primary font-weight-bold mb-3">

                        VetPedia Poultry

                    </h3>

                    <p class="text-muted mb-4">

                        Intelligent Poultry Clinical Decision Support System designed
                        to support veterinarians, researchers, students, and poultry
                        professionals through advanced diagnostic intelligence,
                        knowledge exploration, and evidence-based decision support.

                    </p>

                </div>

                <div class="d-flex flex-wrap">

                    <span class="badge badge-primary mr-2 mb-2 px-3 py-2">

                        Clinical Decision Support

                    </span>

                    <span class="badge badge-info mr-2 mb-2 px-3 py-2">

                        Knowledge Graph

                    </span>

                    <span class="badge badge-success mr-2 mb-2 px-3 py-2">

                        Differential Diagnosis

                    </span>

                </div>

            </div>


            <!-- Platform Modules -->

            <div class="col-xl-3 col-lg-3 col-md-6 mb-4">

                <h5 class="font-weight-bold mb-4">

                    Platform Modules

                </h5>

                <ul class="list-unstyled footer-links">

                    <li class="mb-3">

                        <a href="{{ url('/diagnostic') }}">

                            <i class="fa fa-stethoscope mr-2"></i>

                            Intelligent Diagnosis

                        </a>

                    </li>

                    <li class="mb-3">

                        <a href="{{ route('clinical.search') }}">

                            <i class="fa fa-search mr-2"></i>

                            Clinical Search

                        </a>

                    </li>

                    <li class="mb-3">

                        <a href="{{ route('anatomy.index') }}">

                            <i class="fa fa-heartbeat mr-2"></i>

                            Anatomical Navigator

                        </a>

                    </li>

                    <li class="mb-3">

                        <a href="{{ route('cases.index') }}">

                            <i class="fa fa-folder-open mr-2"></i>

                            Clinical Case Library

                        </a>

                    </li>

                </ul>

            </div>


            <!-- Knowledge Base -->

            <div class="col-xl-4 col-lg-4 col-md-6 mb-4">

                <h5 class="font-weight-bold mb-4">

                    Knowledge Base

                </h5>

                <div class="row">

                    <div class="col-6">

                        <ul class="list-unstyled footer-links">

                            <li class="mb-3">

                                <i class="fa fa-virus text-primary mr-2"></i>

                                Diseases

                            </li>

                            <li class="mb-3">

                                <i class="fa fa-notes-medical text-primary mr-2"></i>

                                Clinical Findings

                            </li>

                        </ul>

                    </div>

                    <div class="col-6">

                        <ul class="list-unstyled footer-links">

                            <li class="mb-3">

                                <i class="fa fa-microscope text-primary mr-2"></i>

                                Necropsy Lesions

                            </li>

                            <li class="mb-3">

                                <i class="fa fa-vial text-primary mr-2"></i>

                                Diagnostic Tests

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>


        <!-- Bottom Footer -->

        <div class="row border-top pt-4">

            <div class="col-md-6 text-center text-md-left">

                <p class="text-muted mb-2 mb-md-0">

                    © {{ date('Y') }}

                    <strong>

                        VetPedia Poultry

                    </strong>

                    . All rights reserved.

                </p>

            </div>

            <div class="col-md-6 text-center text-md-right">

                <small class="text-muted">

                    Version 1.0 • Intelligent Veterinary Knowledge Platform

                </small>

            </div>

        </div>

    </div>

</footer>


<style>

.main-footer {

    background: #ffffff;

    box-shadow: 0 -2px 10px rgba(0,0,0,.03);
    margin-left: 80px;
}

.main-footer h3,
.main-footer h5 {

    color: #1d2746;
}

.footer-links li {

    transition: all .2s ease;
}

.footer-links li:hover {

    transform: translateX(5px);
}

.footer-links a {

    color: #6c757d;

    text-decoration: none;

    transition: all .2s ease;
}

.footer-links a:hover {

    color: #0162e8;

    text-decoration: none;
}

@media (max-width: 768px) {

    .main-footer {

        text-align: center;
    }

    .footer-links li:hover {

        transform: none;
    }
}

</style>

