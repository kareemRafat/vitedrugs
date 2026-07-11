@extends('layouts.master')

@section('title', 'Privacy Policy | VetPedia')

@section('meta_description')
Privacy Policy for VetPedia veterinary medical knowledge platform.
@endsection

@section('content')

<div class="container py-4">

```
<div class="card">

    <div class="card-body">

        <h1 class="mb-4">
            Privacy Policy
        </h1>

        <p>
            Last Updated: {{ now()->format('F d, Y') }}
        </p>

        <hr>

        <h4>Introduction</h4>

        <p>
            VetPedia is committed to protecting the privacy of its users.
            This Privacy Policy explains how information is collected,
            used, and protected when using the VetPedia platform.
        </p>

        <h4>Information We Collect</h4>

        <p>
            We may collect information voluntarily provided by users,
            including contact information submitted through forms,
            support requests, or partnership inquiries.
        </p>

        <h4>How We Use Information</h4>

        <ul>

            <li>To improve the VetPedia platform.</li>

            <li>To respond to inquiries and support requests.</li>

            <li>To maintain platform security and performance.</li>

            <li>To communicate updates and important notices.</li>

        </ul>

        <h4>Cookies</h4>

        <p>
            VetPedia may use cookies and similar technologies to improve
            user experience and analyze platform usage.
        </p>

        <h4>Third-Party Services</h4>

        <p>
            VetPedia may utilize third-party services for hosting,
            analytics, security, and performance monitoring.
        </p>

        <h4>Data Security</h4>

        <p>
            Reasonable administrative and technical measures are used to
            protect information against unauthorized access, disclosure,
            alteration, or destruction.
        </p>

        <h4>Data Accuracy</h4>

        <p>
            While VetPedia strives to provide accurate veterinary and
            pharmaceutical information, users should independently verify
            critical information before making professional decisions.
        </p>

        <h4>Changes to This Policy</h4>

        <p>
            VetPedia reserves the right to update this Privacy Policy at
            any time. Updates will be reflected on this page.
        </p>

        <h4>Contact</h4>

        <p>
            For questions regarding this Privacy Policy, please visit the
            Contact page.
        </p>

        <a
            href="{{ route('contact') }}"
            class="btn btn-primary"
        >
            Contact Us
        </a>

    </div>

</div>
```

</div>

@endsection
