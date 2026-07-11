@extends('layouts.master')

@section('title', 'About VetPedia')

@section('meta_description')
VetPedia is a veterinary medical knowledge platform providing veterinary diseases, drugs, active ingredients, companies, and clinical decision support.
@endsection

@section('content')

<div class="container py-4">

    <div class="card mb-4">

        <div class="card-body">

            <h1 class="mb-3">
                About VetPedia
            </h1>

            <p class="lead">

                VetPedia is a veterinary medical knowledge platform designed to provide veterinarians, researchers, students, and animal health professionals with structured, searchable, and interconnected veterinary information.

            </p>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            Our Mission
        </div>

        <div class="card-body">

            <p>

                Our mission is to build a comprehensive veterinary knowledge ecosystem that connects diseases, drugs, active ingredients, pharmaceutical companies, and clinical information in a single intelligent platform.

            </p>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            What VetPedia Provides
        </div>

        <div class="card-body">

            <ul>

                <li>Veterinary disease knowledge base.</li>

                <li>Veterinary pharmaceutical product database.</li>

                <li>Active ingredient reference library.</li>

                <li>Veterinary company directory.</li>

                <li>Disease-to-product relationships.</li>

                <li>Ingredient-to-product relationships.</li>

                <li>Clinical decision support tools.</li>

                <li>Advanced veterinary search and navigation.</li>

            </ul>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            Data Sources
        </div>

        <div class="card-body">

            <p>

                Information available on VetPedia is collected from veterinary pharmaceutical monographs, product inserts, veterinary references, scientific publications, and other professional veterinary resources.

            </p>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            Disclaimer
        </div>

        <div class="card-body">

            <p>

                VetPedia is intended for educational and professional reference purposes only. Clinical decisions should always be made by qualified veterinary professionals using their professional judgment and local regulatory requirements.

            </p>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            Contact
        </div>

        <div class="card-body">

            <p>

                For questions, feedback, partnership opportunities, or data contributions, please contact the VetPedia team through the contact page.

            </p>

            <a
                href="{{ route('contact') }}"
                class="btn btn-primary"
            >
                Contact Us
            </a>

        </div>

    </div>

</div>

@endsection