@extends('app.layouts.master')

@section('title', 'Terms of Service | VetPedia')

@section('meta_description')
Terms of Service governing the use of the VetPedia veterinary medical knowledge platform.
@endsection

@section('content')

<div class="container py-4">

```
<div class="card">

    <div class="card-body">

        <h1 class="mb-4">
            Terms of Service
        </h1>

        <p>
            Last Updated: {{ now()->format('F d, Y') }}
        </p>

        <hr>

        <h4>Acceptance of Terms</h4>

        <p>
            By accessing or using VetPedia, you agree to be bound by these
            Terms of Service. If you do not agree with any part of these terms,
            you should discontinue use of the platform.
        </p>

        <h4>Purpose of the Platform</h4>

        <p>
            VetPedia provides veterinary medical, pharmaceutical, and
            educational information intended for reference and professional
            learning purposes.
        </p>

        <h4>No Veterinary Advice</h4>

        <p>
            Information provided on VetPedia does not constitute veterinary,
            medical, legal, or regulatory advice. Users should exercise
            professional judgment and consult qualified professionals when
            making clinical or business decisions.
        </p>

        <h4>Accuracy of Information</h4>

        <p>
            While reasonable efforts are made to maintain accurate and
            up-to-date information, VetPedia does not guarantee the
            completeness, accuracy, or suitability of any content.
        </p>

        <h4>Intellectual Property</h4>

        <p>
            Unless otherwise stated, all platform content, design,
            organization, and original materials are the property of
            VetPedia and may not be reproduced or redistributed without
            permission.
        </p>

        <h4>User Conduct</h4>

        <p>
            Users agree not to misuse the platform, interfere with its
            operation, attempt unauthorized access, or engage in activities
            that could damage the platform or other users.
        </p>

        <h4>Third-Party Content and Links</h4>

        <p>
            VetPedia may reference third-party organizations, products,
            publications, or websites. VetPedia is not responsible for the
            content or practices of external resources.
        </p>

        <h4>Limitation of Liability</h4>

        <p>
            To the fullest extent permitted by applicable law, VetPedia and
            its operators shall not be liable for any direct, indirect,
            incidental, consequential, or special damages arising from the
            use of the platform.
        </p>

        <h4>Changes to These Terms</h4>

        <p>
            VetPedia reserves the right to modify these Terms of Service at
            any time. Updated terms will be published on this page.
        </p>

        <h4>Contact</h4>

        <p>
            Questions regarding these Terms of Service may be directed
            through the Contact page.
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
